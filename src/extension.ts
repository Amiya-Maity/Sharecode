import * as vscode from "vscode";
import * as fs from "fs";
import * as path from "path";
import axios from "axios";

/* -------------------- CONSTANTS -------------------- */

// base URL only
const API_BASE_URL = "https://share.hackend.in/";

/* -------------------- HELPERS -------------------- */

const getWorkspacePath = () => vscode.workspace.rootPath || "";

const decodeText = (encodedText: string): string => decodeURI(encodedText);

const writeFileAndOpen = async (filePath: string, content: string) => {
  fs.writeFileSync(filePath, content, "utf-8");

  const document = await vscode.workspace.openTextDocument(filePath);
  await vscode.window.showTextDocument(document);
};

/* -------------------- API CALLS -------------------- */

async function uploadText(uid: string, text: string) {
  await vscode.window.withProgress(
    {
      location: vscode.ProgressLocation.Notification,
      title: "Uploading text...",
      cancellable: false,
    },
    async () => {
      try {
        const encodedText = encodeURI(text);

        const response = await axios.post(
          `${API_BASE_URL}${uid}`,
          { txt: encodedText },
          { headers: { "Content-Type": "application/json" } },
        );

        if (response.data?.status === "success") {
          vscode.window.showInformationMessage("Text uploaded successfully");
        } else {
          vscode.window.showWarningMessage("Problem occurred while uploading.");
        }
      } catch (error: any) {
        if (error?.response?.status === 414) {
          vscode.window.showWarningMessage("Text too long.");
        } else {
          vscode.window.showWarningMessage("Upload failed: " + error);
        }
      }
    },
  );
}

async function fetchJSONData(id: string, fileName: string) {
  await vscode.window.withProgress(
    {
      location: vscode.ProgressLocation.Notification,
      title: "Downloading data...",
      cancellable: false,
    },
    async () => {
      try {
        const response = await axios.get(`${API_BASE_URL}${id}`);
        const data = response.data;

        if (!data || data.length === 0) {
          return vscode.window.showWarningMessage("No data found");
        }

        const decodedText = decodeText(data[0].text);

        if (fileName === "clipboard") {
          await vscode.env.clipboard.writeText(decodedText);
          vscode.window.showInformationMessage("Copied to clipboard");
          return;
        }

        if (fileName !== "clipboard") {
          const exists = fs.existsSync(fileName);

          if (exists) {
            const result = await showMergeEditor(fileName, decodedText);

            if (result === "cancel") return;

            if (result === "current") {
              vscode.window.showInformationMessage("Kept existing file.");
              return;
            }

            // Accept incoming → overwrite
            await writeFileAndOpen(fileName, decodedText);
            return;
          }

          await writeFileAndOpen(fileName, decodedText);
        }

        vscode.window.showInformationMessage("Data fetched successfully");
      } catch (error) {
        vscode.window.showWarningMessage("Fetch failed: " + error);
      }
    },
  );
}

/* -------------------- INPUT UTILS -------------------- */

const askForId = () =>
  vscode.window.showInputBox({
    prompt: "Enter Id:",
    placeHolder: "Type here...",
  });

async function pickWorkspaceFile(): Promise<string | undefined> {
  if (!vscode.workspace.workspaceFolders) {
    vscode.window.showErrorMessage("No workspace open.");
    return;
  }

  const workspaceRoot = vscode.workspace.workspaceFolders[0].uri.fsPath;

  const files = await vscode.workspace.findFiles(
    "**/*",
    "**/{node_modules,.git,dist,build}/**",
  );

  if (files.length === 0) {
    vscode.window.showInformationMessage("No files found in workspace.");
    return;
  }

  const items = files.map((file) => ({
    label: path.basename(file.fsPath),
    description: vscode.workspace.asRelativePath(file.fsPath),
    fullPath: file.fsPath,
    iconPath: vscode.ThemeIcon.File,
  }));

  const selected = await vscode.window.showQuickPick(items, {
    placeHolder: `Search and select file → ${workspaceRoot}`,
    matchOnDescription: true,
  });

  return selected?.fullPath;
}

async function showMergeEditor(
  filePath: string,
  incomingContent: string,
): Promise<"incoming" | "current" | "cancel"> {
  const currentUri = vscode.Uri.file(filePath);

  previewProvider.setContent(incomingContent);
  const incomingUri = vscode.Uri.parse("sharecoder-preview://incoming");

  // open diff
  await vscode.commands.executeCommand(
    "vscode.diff",
    currentUri,
    incomingUri,
    "Current ↔ Incoming Changes",
  );

  // only 2 buttons (no custom cancel)
  const choice = await vscode.window.showInformationMessage(
    "Resolve changes",
    { modal: true },
    "Accept Incoming",
    "Accept Current",
  );

  // close diff automatically
  await vscode.commands.executeCommand("workbench.action.closeActiveEditor");

  if (choice === "Accept Incoming") return "incoming";
  if (choice === "Accept Current") return "current";

  // user pressed ESC or closed dialog
  return "cancel";
}

/* -------------------- COMMANDS -------------------- */

async function downloadToFile() {
  const id = await askForId();
  if (!id)
    return vscode.window.showInformationMessage("Input canceled by user.");

  const filePath = await pickWorkspaceFile();
  if (!filePath) {
    return vscode.window.showInformationMessage(
      "No file selected. Please create a file first if needed.",
    );
  }
  await vscode.window.withProgress(
    {
      location: vscode.ProgressLocation.Notification,
      title: `Downloading to ${path.basename(filePath)}...`,
    },
    async () => {
      await fetchJSONData(id, filePath);
    },
  );
}

async function downloadToClipboard() {
  const id = await askForId();
  if (!id)
    return vscode.window.showInformationMessage("Input canceled by user.");

  vscode.window.showInformationMessage(`ID entered: ${id}`);
  fetchJSONData(id, "clipboard");
}

async function uploadFromFile() {
  const id = await askForId();
  if (!id) {
    return vscode.window.showInformationMessage("Input canceled.");
  }

  const filePath = await pickWorkspaceFile();
  if (!filePath) return;

  try {
    const fileContent = fs.readFileSync(filePath, "utf-8");
    uploadText(id, fileContent);
  } catch {
    vscode.window.showErrorMessage("Error reading selected file.");
  }
}

async function uploadSelectedText() {
  const editor = vscode.window.activeTextEditor;

  if (!editor) {
    return vscode.window.showInformationMessage("No active editor found.");
  }

  const selectedText = editor.document.getText(editor.selection);

  if (!selectedText) {
    return vscode.window.showInformationMessage("No text selected.");
  }

  const id = await askForId();
  if (!id)
    return vscode.window.showInformationMessage("Input canceled by user.");

  vscode.window.showInformationMessage(`ID entered: ${id}`);
  uploadText(id, selectedText);
}
class PreviewProvider implements vscode.TextDocumentContentProvider {
  private content = "";

  setContent(text: string) {
    this.content = text;
  }

  provideTextDocumentContent(): string {
    return this.content;
  }
}

const previewProvider = new PreviewProvider();

/* -------------------- EXTENSION ENTRY -------------------- */

export function activate(context: vscode.ExtensionContext) {
  context.subscriptions.push(
    vscode.commands.registerCommand("sharecoder.dnldfilecodes", downloadToFile),
    vscode.commands.registerCommand(
      "sharecoder.dnldselectcodes",
      downloadToClipboard,
    ),
    vscode.workspace.registerTextDocumentContentProvider(
      "sharecoder-preview",
      previewProvider,
    ),
    vscode.commands.registerCommand("sharecoder.upldfilecodes", uploadFromFile),
    vscode.commands.registerCommand(
      "sharecoder.upldselectcodes",
      uploadSelectedText,
    ),
  );
}

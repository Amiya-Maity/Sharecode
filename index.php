<?php
include ('config.php');
include('functions.php');

$url = isset($_GET['url']) ? $_GET['url'] : '';

$urlComponents = explode('/', $url);

$url = $urlComponents[0];
if (isset($urlComponents[0])) {

    $text = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['txt'])) {
        $tx = $_POST["txt"];
        $url = $_POST["id"];
        $sql = 'SELECT text FROM sharecode WHERE id = "' . $url . '";';
        $result = $conn->query($sql);
        if ($tx != "" && $result->num_rows == 0)
            $sql = 'INSERT INTO `sharecode`(`id`, `text`, `last_edit`) VALUES("' . $url . '","' . $tx . '","' . time() . '");';
        else if ($tx == "")
            $sql = 'DELETE FROM `sharecode` WHERE id ="' . $url . '";';
        else
            $sql = 'UPDATE sharecode SET text = "' . $tx . '", last_edit = "' . time() . '" WHERE id ="' . $url . '";';
        $conn->query($sql);
    }
    else{
        $sql_update = "UPDATE codes SET count = count + 1 WHERE id = 1";
        $conn->query($sql_update);
        get_client_ip($conn,$url);
    }
    $id = $url;
    $sql = "Select *  from sharecode where id = '$id'";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $text = $row['text'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Index Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        body {
            overflow: hidden;
        }

        textarea {
            line-height: 4ch;
            min-height: 48ch;
            background-size: 100% 4ch;
        }

        textarea::-webkit-scrollbar {
            display: none;
        }

        textarea::-webkit-scrollbar-track {
            display: none;
        }

        textarea::-webkit-scrollbar-thumb {
            display: none;
        }
        
        textarea::-webkit-scrollbar-thumb:hover {
            display: none;
        }

        .wrapper {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #24C6DC;
            background: -webkit-linear-gradient(to bottom, #51479D, #24F6DC);
            background: linear-gradient(to bottom, #514A9D, #24C6DC);
        }

        .box div {
            position: absolute;
            width: 60px;
            height: 60px;
            background-color: transparent;
            border: 6px solid rgba(255, 255, 255, 0.8);
        }

        .box div:nth-child(1) {
            top: 12%;
            left: 42%;
            animation: animate 10s linear infinite;
        }

        .box div:nth-child(2) {
            top: 70%;
            left: 50%;
            animation: animate 7s linear infinite;
        }

        .box div:nth-child(3) {
            top: 17%;
            left: 6%;
            animation: animate 9s linear infinite;
        }

        .box div:nth-child(4) {
            top: 20%;
            left: 60%;
            animation: animate 10s linear infinite;
        }

        .box div:nth-child(5) {
            top: 67%;
            left: 10%;
            animation: animate 6s linear infinite;
        }

        .box div:nth-child(6) {
            top: 80%;
            left: 70%;
            animation: animate 12s linear infinite;
        }

        .box div:nth-child(7) {
            top: 60%;
            left: 80%;
            animation: animate 15s linear infinite;
        }

        .box div:nth-child(8) {
            top: 32%;
            left: 25%;
            animation: animate 16s linear infinite;
        }

        .box div:nth-child(9) {
            top: 90%;
            left: 25%;
            animation: animate 9s linear infinite;
        }

        .box div:nth-child(10) {
            top: 20%;
            left: 80%;
            animation: animate 5s linear infinite;
        }

        @keyframes animate {
            0% {
                transform: scale(0) translateY(-90px) rotate(360deg);
                opacity: 1;
            }

            50% {
                transform: scale(1.3) translateY(-90px) rotate(-180deg);
                border-radius: 50%;
                opacity: 0;
            }

            100% {
                transform: scale(0) translateY(90px) rotate(180deg);
                border-radius: 50%;
                opacity: 1;
            }
        }

        div.navbar {
            background-color: rgb(56, 55, 55);
            display: flex;
            align-content: stretch;
            padding-right: 4px;
        }

        div.navbar>.sitename {
            color: aqua;
            font-size: large;
            margin: auto;
            margin-left: 10px;
        }

        div.navbar>.sitename>.name {
            font-weight: bolder;
        }

        div.navbar a button {
            color: aqua;
            background-color: #6d6a6a;
            border: none;
            margin: 4px;
            padding: 4px;
            float: right;
        }

        .container {
            font-size: 2em;
            Text-align: center;
            font-family: "roboto";
        }.toggle {
          background-color: #ddddde;
          border-radius: 60px;  
          box-shadow: 0 1px 1px 0 rgba(255,255,255,.4), 0 1px 0 0 rgba(0,0,0,0.10) inset;
          cursor: pointer;
          width: 130px;
          height: 50px;
          overflow: hidden;
          position: relative;
          transition: all .25s linear;
        }
        .toggle .slide {
          color: #818283;
          color: rgba(0,0,0,.15);
          background: #efefef;
          border-radius: 50%;
          font-size: 30px;
          line-height: 51px;
          text-align: center;
          text-decoration: none;
          height: 45px;
          width: 45px;
          position: absolute;
          top: 2.5px;
          left: 5px;
          box-shadow: 0 1px 2px 0 rgba(0,0,0,0.15), 0 1px 1px 0 rgba(255,255,255,.8) inset;
          transition: all 0.3s cubic-bezier(0.43, 1.3, 0.86, 1);
        }
        .toggle .slide span{
            text-shadow: 0 1px 1px rgba(255,255,255,.7), 0 0 1px rgba(0,0,0,.3);       
        }
        .toggle .slide:before, 
        .toggle .slide:after {
          color: #FFF;
          content: "\f023";
          font-family: "Font Awesome 5 Free";
          font-size: 34px;
          font-weight: 400;
          text-shadow: 0 -1px 1px rgba(0, 0, 0, 0.25);
          -webkit-font-smoothing: antialiased;
          position: absolute;
        }
        .toggle .slide:before {
          right: -50px;
          color: #2a2b2c;
          opacity: 0.2;
        }
        .toggle .slide:after {
          content: "\f09c";
          left: -50px;
          color: #00ba00;
        }
        .toggle.on {
          background: #00dc00;
        }
        .toggle.on .slide {
          left: 80px;
          color: #00d100;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <span class="sitename"><span class="name"><a href="https://sharecode.currentechz.com/" style="color:aqua;text-decoration:none">Sharecode</a></span> - Share code anonymously</span>
        <a href="https://sharecode.currentechz.com/files">
            <button>Sharefile</button>
        </a>
        <a href="https://sharecode.currentechz.com/<? echo $url;?>">
            <button>Refresh</button>
        </a>
        <div class='toggle'>
           <div class='slide'>
             <span class='fa fa-circle-o'></span>
          </div>
        </div>
    </div>
    <div class="wrapper">
        <div class="box">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div><br><br>
        <div class="row">
            <div class="col right">
                <center>
                    <form action="" method="POST" id="code">
                        <h2 class="glow"><?php echo $url;?></h2>
                        <div>
                            <textarea name="txt" id="txt"
                                style="color: white; background-color: black; border-radius: 0.5rem; width: 90vw;"
                                size="10000" width="90vw" placeholder="Enter your texts..."
                                onchange="rephrase()"></textarea>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $url;?>">
                        <button id="submit" type="submit" hidden></button>
                    </form>
                </center>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
</body>
<script>
    document.getElementById('txt').value = decodeURI(<?php echo '"' . $text . '"' ?>);
</script>

<script>
    function disableTextbox(textboxName) {
      document.querySelector(textboxName).disabled = true;
    }
    
    function enableTextbox(textboxName) {
      document.querySelector(textboxName).disabled = false;
    }
    disableTextbox('textarea#txt');
    document.addEventListener('DOMContentLoaded', function() {
        var toggles = document.querySelectorAll('.toggle');
        toggles.forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                if (this.classList.contains('on')) {
                    this.classList.remove('on');
                    disableTextbox('textarea#txt');
                } else {
                    this.classList.add('on');
                    enableTextbox('textarea#txt');
                }
            });
        });
    });

    function rephrase() {
        var encodedInput = encodeURI(document.getElementById("txt").value);
        document.getElementById("txt").value = encodedInput;
        document.getElementById("submit").click();
        // var form = document.getElementById('code');
        // var formData = new FormData(form);
        // var xhr = new XMLHttpRequest();
        // xhr.open("POST", "index.php", true);
        // console.log("posting");
    }
</script>
</html>
<html>
<head>
	<meta charset="UTF-8"></meta>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <audio id="soundtrack" preload="auto" src="media/sounds/menu.mp3" loop="true" volume=".35" autobuffer>
        Unsupported in Firefox
    </audio>
	<title>sendMessage</title>
    <style>
        body {
            background: #777;
        }

        table {
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
            vertical-align: top;
            text-align: left;
        }

        input#volumeslider {
            width: 70px;
        }

        input[type='range'] {
            -webkit-appearance: none !important;
            margin: 0px;
            padding: 0px;
            background: #000;
            height: 13px;
            border-bottom: #333 1px solid;
        }

        input[type='range']::-ms-fill-lower {
            background: #000;
        }

        input[type='range']::-ms-fill-upper {
            background: #000;
        }

        input[type='range']::-moz-range-track {
            border: none;
            background: #000;
        }

        input[type='range']::-webkit-slider-thumb {
            -webkit-appearance: none !important;
            background: radial-gradient(#FFF, #333);
            height: 11px;
            width: 11px;
            border-radius: 100%;
            cursor: pointer;
        }

        input[type='range']::-moz-range-thumb {
            background: radial-gradient(#FFF, #333);
            height: 11px;
            width: 11px;
            border-radius: 100%;
            cursor: pointer;
        }

        input[type='range']::-ms-thumb {
            -webkit-appearance: none !important;
            background: radial-gradient(#FFF, #333);
            height: 11px;
            width: 11px;
            border-radius: 100%;
            cursor: pointer;
        }
    </style>
</head>
<body>
	<H1>
	<?php
		session_start();
		//$path = 'phpseclib';
		echo "Send a message";
		include_once('class.php');
		
		if(isset($_POST['sendMessage'])){
			$newMessage = new Message();
			$newMessage->sender = $_SESSION['username'];
			$newMessage->receiver = $_POST['receiver'];
			$newMessage->message = $_POST['message'];
			
			$stringData = file_get_contents("../storage/users.txt");
			$myUserArray = json_decode($stringData);
			/*
			for ($i = 0; $i < sizeof($myUserArray); $i++) {
				if($myUserArray[$i]->userName == $newMessage->receiver){
					$publicKey = $myUserArray[$i]->public_Key;
					$privateKey = $myUserArray[$i]->private_Key;
					break;
				}
			}
			*/
			if($newMessage->receiver == "" || $newMessage->message == ""){
				header('Location: sendMessage.php');
			}else{
				$myMessageArray = array();
				
				if(file_exists("../storage/messages.txt")){
					$string_data = file_get_contents("../storage/messages.txt");
					$myMessageArray = json_decode($string_data);
				}else{
					$myfile = fopen("../storage/messages.txt", "w+");
				}	

				// openssl_public_encrypt($newMessage->message, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
				$newMessage->message = base64_encode($newMessage->message);
		
				array_push($myMessageArray, $newMessage);
				$string_data = json_encode($myMessageArray);
				file_put_contents("../storage/messages.txt", $string_data);
		
				header('Location: lobby.php');
			}
		}
		?>
	</H1>
	<img style="cursor: pointer;" id="muteicon" src="media/img/Speaker_Icon.png" height="14px">
    <input style="cursor: pointer;" id="volumeslider" type="range" min="0" max="1" value="" step=".05"></input>

	<form id="sendID" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<fieldset>
		<input type="Button" name="viewPost" id="viewPostbtn" value="view Posts"></input>
			<legend><?php echo $_SESSION['username'];?></legend>
				<p>To:</p>
				<input name="receiver" id="receiver" type="text" placeholder="Enter Reciever"></input>
				<p>Message</p>
				<textarea style="width:100%"  name="message" id ="message" rows ="5" cols ="80" placeholder="Type your message here!"></textarea>
				<br>
				<input type="submit" name="sendMessage" id="sendMessage" value="send Message"></input>
		</fieldset>
	</form>
	<script>
		$(document).ready(function(){
			$("#viewPostbtn").click(function(){
				window.location.href = "lobby.php";
			});
			//Music Stuff Begin
			function setCookie(c_name, value, exdays) {
				var exdate = new Date();
				exdate.setDate(exdate.getDate() + exdays);
				var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
				document.cookie = c_name + "=" + c_value;
			}

			function getCookie(c_name) {
				var i, x, y, ARRcookies = document.cookie.split(";");
				for (i = 0; i < ARRcookies.length; i++) {
					x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
					y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
					x = x.replace(/^\s+|\s+$/g, "");
					if (x == c_name) {
						return unescape(y);
					}
				}
			}

			var song = document.getElementsByTagName('audio')[0];
			var played = false;
			var tillPlayed = getCookie('timePlayed');

			function update() {
				if (!played) {
					if (tillPlayed) {
						song.currentTime = tillPlayed;
						song.play();
						played = true;
					} else {
						song.play();
						played = true;
					}
				} else {
					setCookie('timePlayed', song.currentTime);
				}
			}
			setInterval(update, 1000);

			var muted = false;

			var mutebtn = document.getElementById("#mutebtn");
			var audio = document.getElementById("soundtrack");
			var volumeslider = document.getElementById("volumeslider");
			volumeslider.value = localStorage.getItem("musicVol");
			audio.volume = localStorage.getItem("musicVol");
			var prevousVol = audio.volume;

			volumeslider.addEventListener("mousemove", function () {
				if (!muted) {
					audio.volume = prevousVol = this.value;
				}
			});

			$("#muteicon").click(function () {
				mute(this);
			});

			function mute(img) {
				if (muted) {
					muted = false;
					img.src = "media/img/Speaker_Icon.png";
					audio.volume = prevousVol;
				} else {
					muted = true;
					img.src = "media/img/Mute_Icon.png";
					audio.volume = 0;
				}
			}   // Music Stuff Ends
    	});
	</script>
</body>
</html>
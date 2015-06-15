<?php
error_reporting(E_ALL);
session_start();
require('./ortc.php');
//see if is the same file
 
/* -------------------- */
/* REPLACE THESE VALUES */
/* -------------------- */
$URL = 'http://ortc-developers.realtime.co/server/2.1';
$AK = 'YOUR_APPLICATION_KEY';// your realtime.co application key
$PK = 'YOUR_APPLICATION_PRIVATE_KEY';// your realtime.co private key
$TK = 'YOUR_AUTHENTICATION_TOKEN';// token: could be randomly generated in the session
$CH = 'myChannel'; //channel
$ttl = 180; 
$isAuthRequired = false;
$result = false;
/* -------------------- */
/*        END           */
/* -------------------- */
     
// ORTC auth
// on a live usage we would already have the auth token authorized and stored in a php session
// Since a developer appkey does not require authentication the following code is optional
 
if( ! array_key_exists('ortc_token', $_SESSION) ){    
	$_SESSION['ortc_token'] = $TK;       
}	
 
$rt = new Realtime( $URL, $AK, $PK, $TK );  

if($isAuthRequired){
	$result = $rt->auth(
		array(
			$CH => 'w'
		), 
		$ttl
	);//post authentication permissions. w -> write; r -> read
	echo '<div class="status-error">authentication status '.( $result ? 'success' : 'failed' ).'</div>';
}

if($result || !$isAuthRequired){
	$result = $rt->send($CH, "Sending message from php API", $response);
	
	if($result){
		
		echo '<div class="status-ok"> send status connected</div>';
	}else{
		echo '<div class="status-error"> send status failed</div>';
	
	}
}    

?>


<!doctype html>
<html>
<head>
    <title>Testando Realtime.co</title>
	<style type="text/css">
	body {
	  color:#333;
	  font-family:Arial,sans-serif;
	}
	
	.status-ok{padding:5px; color:#fff; background:green; margin-top:10px; margin-bottom:10px; width:550px;}
	.status-error{padding:5px; color:#fff; background:red; margin-top:10px; margin-bottom:10px; width:550px;}
	#log{border-top:1px solid #ccc; padding:10px; margin-top:10px; width:550px; }
	.msg{display:inline; border:1px solid #ccc; padding:3px; margin-top:3px; float:left; width:500px; height:auto;
		color:#32CD32; background:#000; font-family: 'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace;}
	
	</style>
</head>
<body>
    <input type="text" id="message" />
    <input type="button" onclick="sendMessage('myChannel');" value="Send to myChannel" />
    <div id="log">
    	
    </div>

    <script src="http://code.xrtml.org/xrtml-3.0.0.js"></script>
	<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script>
        var appkey = '<?php echo($AK); ?>',
            url = '<?php echo($URL); ?>',
            token = '<?php echo($TK); ?>';
        xRTML.ready(function(){
            xRTML.Config.debug = true;
            xRTML.ConnectionManager.create(
            {
                id: 'myConn',
                appkey: appkey,
                authToken: token,
                url: url,
                channels: [
                    {name: 'myChannel'}
                ]
            }).bind(
            {
                message: function(e) {
                    var log = $("#log");
                    log.prepend('<span class="msg">Message received: ' + e.message + '</span>');
                }
            });
        });
        function sendMessage(channel){
            var msg = $('#message').val();
            xRTML.ConnectionManager.sendMessage({
                connections: ['myConn'],
                channel: channel,
                content: msg
            });
        }
    </script>

</body>
</html>
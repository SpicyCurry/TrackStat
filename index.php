<?php
require ('steamauth/steamauth.php');
?>

<html>
<head>
	<meta charset="utf-8">
	<title>TrackStat</title>

	<link rel = "stylesheet" type = "text/css" href="style.css"/>
</head>
<body>
<div id="content" align="center">
	<h1>TrackStat</h1>
	<a href="endUserAgreement.html">End User Agreement</a> -
	<a href="privacy.html">Privacy Policy </a>
	<br><br>

	<?php
	if(!isset($_SESSION['steamid']))
	{
		echo "<div style='margin: 30px auto; text-align: center;'>";
		echo steamlogin();
		echo "</div>";
	}
	else
	{
		include ('steamauth/userInfo.php');

		echo "  <br><br>";
		echo "  <h4 style='margin-bottom: 3px; float:left;'>Steam WebAPI-Output:</h4>";
		echo "  <span style='float:right;'><a href='steamauth/logout.php'>Log out</a></span>";
		echo "  <table class='table table-striped'><tr><td><b>Variable name</b></td><td><b>Value</b></td><td><b>Description</b></td></tr>";
		echo "  	<tr><td>\$steamprofile['steamid']</td><td>".$steamprofile['steamid']."</td><td>SteamID64 of the user</td></tr>";
		echo "  	<tr><td>\$steamprofile['communityvisibilitystate']</td><td>".$steamprofile['communityvisibilitystate']."</td><td>1 - Account not visible; 3 - Account is public (Depends on the relationship of your account to the others)</td></tr>";
		echo "  	<tr><td>\$steamprofile['profilestate']</td><td>".$steamprofile['profilestate']."</td><td>1 - The user has a Steam Community profile; 0 - if not</td></tr>";
		echo "  	<tr><td>\$steamprofile['personaname']</td><td>".$steamprofile['personaname']."</td><td>Public name of the user</td></tr>";
		echo "  	<tr><td>\$steamprofile['lastlogoff']</td><td>".$steamprofile['lastlogoff']."</td><td><a href='http://www.unixtimestamp.com/' target='_blank'>Unix timestamp</a> of the user's last logoff</td></tr>";
		echo "  	<tr><td>\$steamprofile['profileurl']</td><td>".$steamprofile['profileurl']."</td><td>Link to the user's profile</td></tr>";
		echo "  	<tr><td>\$steamprofile['personastate']</td><td>".$steamprofile['personastate']."</td><td>0 - Offline, 1 - Online, 2 - Busy, 3 - Away, 4 - Snooze, 5 - looking to trade, 6 - looking to play</td></tr>";
		echo "  	<tr><td>\$steamprofile['realname']</td><td>".$steamprofile['realname']."</td><td>\"Real\" name</td></tr>";
		echo "  	<tr><td>\$steamprofile['primaryclanid']</td><td>".$steamprofile['primaryclanid']."</td><td>The ID of the user's primary group</td></tr>";
		echo "  	<tr><td>\$steamprofile['timecreated']</td><td>".$steamprofile['timecreated']."</td><td><a href='http://www.unixtimestamp.com/' target='_blank'>Unix timestamp</a> for the time the user's account was created</td></tr>";
		echo "  	<tr><td>\$steamprofile['avatar']</td><td><img src='".$steamprofile['avatar']."'><br>".$steamprofile['avatar']."</td><td>Adress of the user's 32x32px avatar</td></tr>";
		echo "  	<tr><td>\$steamprofile['avatarmedium']</td><td style=''><img src='".$steamprofile['avatarmedium']."'><br>".$steamprofile['avatarmedium']."</td><td>Adress of the user's 64x64px avatar</td></tr>";
		echo "  	<tr><td>\$steamprofile['avatarfull']</td><td><img src='".$steamprofile['avatarfull']."'><br>".$steamprofile['avatarfull']."</td><td>Adress of the user's 184x184px avatar</td></tr>";
		echo "  </table>";
	}
	?>
	<div class="pull-right"><i>This page is powered by <a href="http://steampowered.com">Steam</a></i></div>
</div>
</body>
</html>
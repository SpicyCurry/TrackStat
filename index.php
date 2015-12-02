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
		echo "  <a href='steamauth/logout.php'>Log out</a>";
		echo $steamprofile['steamid']." SteamID64 <br>";
		echo $steamprofile['personaname']." Public name <br>";
		echo "<img src='".$steamprofile['avatarmedium']."'>avatar<br>";
	}
	?>
	<div><i>This page is powered by <a href="http://steampowered.com">Steam</a></i></div>
</div>
</body>
</html>
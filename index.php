<?php
require ('steamauth/steamauth.php');
include ('header.html');
?>

<html>

<body>

<header class="jumbotron hero-spacer">

    <div id="content" align="center">
        <h1>TrackStat</h1>

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
            echo "<div style='margin: 30px auto; text-align: center;'>";
            echo $steamprofile['steamid']." SteamID64 <br>";
            echo $steamprofile['personaname']."<br>";
            echo "<img src='".$steamprofile['avatarmedium']."'><br>";
            echo "  <a href='steamauth/logout.php'>Log out</a>";
            echo "</div>";
            echo "<br><br>";
            echo "<a href='database/user.php'> User </a>";
        }
        ?>
        <div><i>This page is powered by <a href="http://steampowered.com">Steam</a></i></div>
    </div>
    </p>
</header>
</body>
</html>
<?php

$ID = $_SESSION["steam_steamid"];
try
{
	$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");

	$matchesStmt =  $dbh->prepare("SELECT * FROM team_has_user WHERE SteamID64=:ID
									INNER JOIN match
									ON team_has_user.TeamID=match.MatchID");
	$matchesStmt->bindParam(":ID", $ID);
	$matchesStmt->execute();


} catch (PDOException $e)
{
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}
$dbh = null;
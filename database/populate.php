<?php

$ID = "76561197961713788";

$insert = [];
$key = 1234;
$providerID = rand();
$providerName = "Populate";
$team1ID = rand();
$team2ID = $team1ID+1;
$matchID = $team1ID+2;
$placeholderUserID = 1;
$time = time();
$map = "de_dust";
$winningside = "ct";
$weapon = "ak-47";
try
{
	$dbh = new PDO('mysql:host=localhost;dbname=trackstatdb', "root", "vikhram");
	$ProviderStmt = $dbh->prepare("INSERT INTO provider (ProviderID, `key`, ProviderName) VALUES (:providerID, :key, :providerName)");
	$ProviderStmt->bindParam(":providerID", $providerID);
	$ProviderStmt->bindParam(":key", $key);
	$ProviderStmt->bindParam(":providerName", $providerName);
	var_dump($ProviderStmt->execute());

	$UserProviderStmt = $dbh->prepare("INSERT INTO user_has_provider (user_SteamID64, Provider_ProviderID) VALUES (:ID, :providerID)");
	$UserProviderStmt->bindParam(":ID", $ID);
	$UserProviderStmt->bindParam(":providerID", $providerID);
	var_dump($UserProviderStmt->execute());

	$TeamStmt = $dbh->prepare("INSERT INTO team (TeamID, Provider_key) VALUES (:teamID,:key)");
	$TeamStmt->bindParam(":teamID", $team1ID);
	$TeamStmt->bindParam(":key", $key);
	var_dump($TeamStmt->execute());
	$TeamStmt->bindParam(":teamID", $team2ID);
	var_dump($TeamStmt->execute());

	$TeamHasUserStmt = $dbh->prepare("INSERT INTO team_has_user (TeamID, SteamID64) VALUES (:teamID, :ID)");
	$TeamHasUserStmt->bindParam(":ID", $ID);
	$TeamHasUserStmt->bindParam(":teamID", $team1ID);
	var_dump($TeamHasUserStmt->execute());
	$TeamHasUserStmt->bindParam(":ID", $placeholderUserID);
	$TeamHasUserStmt->bindParam(":teamID", $team2ID);
	var_dump($TeamHasUserStmt->execute());

	$MatchStmt = $dbh->prepare("INSERT INTO `match` (MatchID, TimeStamp, Map, TeamID_1, TeamID_2, Provider_key) VALUES (:matchID, :time, :map, :team1, :team2, :key)");
	$MatchStmt->bindParam(":matchID", $matchID);
	$MatchStmt->bindParam(":time", $time);
	$MatchStmt->bindParam(":map", $map);
	$MatchStmt->bindParam(":team1", $team1ID);
	$MatchStmt->bindParam(":team2", $team2ID);
	$MatchStmt->bindParam(":key", $key);
	var_dump($MatchStmt->execute());

	$killsStmt = $dbh->prepare("INSERT INTO kills (SteamID64_Killer, SteamID64_Killed, TimeStamp, MatchID, Weapon, Provider_key) VALUES (:killer, :killed, :time, :matchID, :weapon, :key)");
	$killsStmt->bindParam(":killer",$ID);
	$killsStmt->bindParam(":killed",$placeholderUserID);
	$killsStmt->bindParam(":time", $time);
	$killsStmt->bindParam(":matchID",$matchID);
	$killsStmt->bindParam(":weapon",$weapon);
	$killsStmt->bindParam(":key",$key);
	$RoundsStmt = $dbh->prepare("INSERT INTO rounds (TimeStamp, WinningSide, MatchID, TeamID_Winner, SteamID64_MVP, Provider_key) VALUES (:time, :winnerStr, :matchID, :winnerID, :mvp, :key)");
	$RoundsStmt->bindParam("time", $time);
	$RoundsStmt->bindParam(":winnerStr",$winningside);
	$RoundsStmt->bindParam(":matchID",$matchID);
	$RoundsStmt->bindParam(":winnerID",$team1ID);
	$RoundsStmt->bindParam(":mvp",$ID);
	$RoundsStmt->bindParam(":key",$key);

	$i = 0;
	while ($i<16)
	{
		$time = $time + rand(30,90);
		if ($i == 15)
		{
			$winningside = "t";
		}
		var_dump($killsStmt->execute());
		var_dump($RoundsStmt->execute());
		$i++;
	}

	$MatchEndStmt = $dbh->prepare("INSERT INTO matchend (TimeStamp, MatchID) VALUES (:time, :matchID)");
	$MatchEndStmt->bindParam(":time", $time);
	$MatchEndStmt->bindParam(":matchID", $matchID);
	var_dump($MatchEndStmt->execute());

} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}
$dbh = null;
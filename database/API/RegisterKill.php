<?php

if ((isset($_GET["killerID"])&& isset($_GET["killedID"]) && isset($_GET["timeStamp"])&&isset($_GET["matchID"])&& isset($_GET["weapon"])&&isset($_GET["key"]))|| (isset($_GET["json"])&&isset($_GET["key"])))
{
	include "checkProvider.php";
	if (checkKey($_GET["key"]))
	{
		if (isset($_GET["json"]))
		{
			$argument = json_decode($_GET["json"], true);
		}
		else
		{
			$argument = $_GET;
		}
		try
		{
			$dbh = new PDO('mysql:host=localhost;dbname=trackstatdb', "root", "vikhram");
			$killStmt = $dbh->prepare("INSERT INTO `kills` (SteamID64_Killer, SteamID64_Killed, TimeStamp, MatchID, Weapon, Provider_key) VALUES (:killerID, :killedID, :timeStamp, :matchID, :weapon, :key)");
			$killStmt->bindParam(":killerID", $argument["killerID"]);
			$killStmt->bindParam(":killedID", $argument["killedID"]);
			$killStmt->bindParam(":timeStamp", $argument["timeStamp"]);
			$killStmt->bindParam(":matchID", $argument["matchID"]);
			$killStmt->bindParam(":weapon", $argument["weapon"]);
			$killStmt->bindParam(":key", $_GET["key"]);
			$killStmt->execute();

			print "done";
		} catch (PDOException $e)
		{
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$dbh = null;
	}
	else
	{
		print "Invalid key";
	}
}
else
{
	print "Invalid arguments";
}
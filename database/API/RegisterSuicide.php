<?php

if ((isset($_GET["ID"])&&  isset($_GET["timeStamp"])&&isset($_GET["matchID"])&& isset($_GET["key"]))|| (isset($_GET["json"])&&isset($_GET["key"])))
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
			$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");

			$suicideStmt = $dbh->prepare("INSERT INTO `suicide` (MatchID, SteamID64, TimeStamp, Provider_key) VALUES (:matchID, :ID, :timeStamp, :key)");
			$suicideStmt->bindParam(":matchID", $argument["matchID"]);
			$suicideStmt->bindParam(":ID", $argument["ID"]);
			$suicideStmt->bindParam(":timeStamp", $argument["timeStamp"]);
			$suicideStmt->bindParam(":key", $_GET["key"]);

			print($killStmt->execute());
		} catch (PDOException $e)
		{
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$dbh = null;
	}
}
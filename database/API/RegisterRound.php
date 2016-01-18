<?php

if ((isset($_GET["timeStamp"])&&isset($_GET["winningSide"])&&isset($_GET["matchID"])&&isset($_GET["winningTeamID"])&&isset($_GET["MVPID"])&&isset($_GET["key"]))|| (isset($_GET["json"])&&isset($_GET["key"])))
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

			$roundStmt = $dbh->prepare("INSERT INTO `rounds` (TimeStamp, WinningSide, MatchID, TeamID_Winner, SteamID64_MVP, Provider_key) VALUES (:time, :winningSide, :matchID, :winningID, :MVPID, :key)");
			$roundStmt->bindParam(":winningSide", $argument["winningSide"]);
			$roundStmt->bindParam(":winningID", $argument["winningID"]);
			$roundStmt->bindParam(":matchID", $argument["matchID"]);
			$roundStmt->bindParam(":MVPID", $argument["MVPID"]);
			$roundStmt->bindParam(":time", $argument["timeStamp"]);
			$roundStmt->bindParam(":key", $_GET["key"]);

			$roundStmt->execute();

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
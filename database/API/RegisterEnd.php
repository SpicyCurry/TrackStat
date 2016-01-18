<?php

if (isset($_GET["timeStamp"]) && isset($_GET["matchID"]) && isset($_GET["key"]))
{
	include "checkProvider.php";
	if (checkKey($_GET["key"]))
	{
		try
		{
			$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");

			$endStmt = $dbh->prepare("INSERT INTO `matchend` (TimeStamp, MatchID) VALUES (:end, :matchID)");
			$endStmt->bindParam(":end", $_GET["timeStamp"]);
			$endStmt->bindParam(":matchID", $_GET["matchID"]);

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

// Security leak. Anyone with a valid provider key can register a matchend
// to any unended match by guessing their ID (which is AI, so can be easy)
<?php

function checkUserPDO()
{
	$ID = $_SESSION["steam_steamid"];
	$time = time();
	try
	{
		$dbh = new PDO('mysql:host=localhost;dbname=trackstatdb', "root", "vikhram");
		$selectStmt =  $dbh->prepare("SELECT * FROM User WHERE SteamID64=:ID");
		$selectStmt->bindParam(":ID", $ID);
		$selectStmt->execute();
		if ($selectStmt->rowCount() == 1)
		{
			$updateStmt = $dbh->prepare("UPDATE User SET LastLogin=:time WHERE SteamID64=:ID");
			$updateStmt->bindparam(":ID", $ID);
			$updateStmt->bindParam(":time", $time);
			$updateStmt->execute();
		}
		else
		{
			$insertStmt = $dbh->prepare("INSERT INTO User (SteamID64, LastLogin) VALUES (:ID,:time)");
			$insertStmt->bindparam(":ID", $ID);
			$insertStmt->bindParam(":time", $time);
			$insertStmt->execute();
		}

	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}

	$dbh = null;
}

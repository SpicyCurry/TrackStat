<?php
// GET: Map, Json Teams, Key

if (isset($_GET["key"])&&isset($_GET["teams"])&&isset($_GET["map"]))
{
	include "checkProvider.php";
	if (checkKey($_GET["key"]))
	{
		$time = time();
		$teamNames = [0=>"CT", 1=>"T"];
		$teams = json_decode($_GET["teams"], true);
		try
		{
			$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");

			$teamStmt = $dbh->prepare("INSERT INTO `team` (`Provider_key`) VALUES (:key)");
			$teamStmt->bindParam(":key", $key);
			$teamStmt->execute();
			$CTID = $dbh->lastInsertId();
			$teamStmt->execute();
			$TID = $dbh->lastInsertId();

			$findPlayerStmt = $dbh->prepare("SELECT * FROM `user` WHERE SteamID64=:ID");
			$findPlayerStmt->bindParam(":ID", $ID);

			$addPlayerStmt = $dbh->prepare("INSERT INTO `user` (SteamID64, LastLogin) VALUES (:ID,:time)");
			$addPlayerStmt->bindparam(":ID", $ID);
			$addPlayerStmt->bindParam(":time", $time);

			$insertPlayerStmt = $dbh->prepare("INSERT INTO `team_has_user` (`TeamID`, `SteamID64`) VALUES (:teamID, :ID)");
			$insertPlayerStmt->bindParam(":teamID", $teamID);
			$insertPlayerStmt->bindParam(":ID", $ID);


			foreach ($teamNames as $name)
			{
				$teamID = ($name=="CT")? $CTID : $TID;
				foreach ($teams[$name] as $playerID)
				{
					$ID = $playerID;
					$findPlayerStmt->execute();
					if ($findPlayerStmt->rowCount() == 1)
					{
						$insertPlayerStmt->execute();
					}
					else
					{
						$addPlayerStmt->execute();
						$insertPlayerStmt->execute();
					}
				}
			}

			$matchStmt = $dbh->prepare("INSERT INTO `match` (TimeStamp, Map, TeamID_1, TeamID_2, Provider_key) VALUES (:time, :map, :teamID1, :teamID2, :key)");
			$matchStmt->bindParam(":time", $time);
			$matchStmt->bindParam(":map", $_GET["map"]);
			$matchStmt->bindParam(":teamID1", $CTID);
			$matchStmt->bindParam(":teamdID2", $TID);
			$matchStmt->bindParam(":key", $_GET["key"]);
			$matchStmt->execute();
			$matchID = $dbh->lastInsertId();
			print $matchID;
		} catch (PDOException $e)
		{
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$dbh = null;
	}''
}
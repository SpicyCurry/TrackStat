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

			if (isset($_GET["rounds"]) && isset($_GET["kills"]) && $_GET["end"])
			{
				$roundStmt = $dbh->prepare("INSERT INTO `rounds` (TimeStamp, WinningSide, MatchID, TeamID_Winner, SteamID64_MVP, Provider_key) VALUES (:time, :winningSide, :matchID, :winningID, :MVPID, :key)");
				$roundStmt->bindParam(":winningSide", $winnigSide);
				$roundStmt->bindParam(":winningID", $winningID);
				$roundStmt->bindParam(":matchID", $matchID);
				$roundStmt->bindParam(":MVPID", $MVPID);
				$roundStmt->bindParam(":time", $timeStamp);
				$roundStmt->bindParam(":key", $_GET["key"]);

				$roundsArray = json_decode($_GET["roundss"], true);
				foreach ($roundsArray as $round)
				{
					$winnigSide = $round["winningSide"];
					$winningID = $round["winningID"];
					$MVPID = $round["MVPID"];
					$timeStamp = $round["timeStamp"];
					$roundStmt->execute();
				}

				$killStmt = $dbh->prepare("INSERT INTO `kills` (SteamID64_Killer, SteamID64_Killed, TimeStamp, MatchID, Weapon, Provider_key) VALUES (:killerID, :killedID, :timestamp, :matchID, :weapon, :key)");
				$killStmt->bindParam(":killerID", $killerID);
				$killStmt->bindParam(":killedID", $killedID);
				$killStmt->bindParam(":timestamp", $timeStamp);
				$killStmt->bindParam(":matchID", $matchID);
				$killStmt->bindParam(":weapon", $weapon);
				$killStmt->bindParam(":key", $_GET["key"]);

				$killsArray = json_decode($_GET["kills"], true);
				foreach ($killsArray as $kill)
				{
					$killerID = $kill["killerID"];
					$killedID = $kill["killedID"];
					$timeStamp = $kill["timestamp"];
					$weapon = $kill["weapon"];
					$killStmt->execute();
				}

				$endStmt = $dbh->prepare("INSERT INTO `matchend` (TimeStamp, MatchID) VALUES (:end, :matchID)");
				$endStmt->bindParam(":end", $_GET["end"]);
				$endStmt->bindParam(":matchID", $matchID);

				if (isset($_GET["suicides"]))
				{
					$suicideStmt = $dbh->prepare("INSERT INTO `suicide` (MatchID, SteamID64, TimeStamp, Provider_key) VALUES (:matchID, :ID, :timeStamp, :key)");
					$suicideStmt->bindParam(":matchID", $matchID);
					$suicideStmt->bindParam(":ID", $ID);
					$suicideStmt->bindParam(":timeStamp", $timeStamp);
					$suicideStmt->bindParam(":key", $_GET["key"]);

					$suicideArray =  json_decode($_GET["suicides"], true);
					foreach($suicideArray as $suicide)
					{
						$ID = $suicide["ID"];
						$timeStamp = $suicide["timeStamp"];
						$suicideStmt->execute();
					}
				}
			}
			else
			{
				print $matchID;
			}
		} catch (PDOException $e)
		{
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$dbh = null;
	}
}
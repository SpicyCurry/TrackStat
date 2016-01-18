<?php
// GET: Map, Json Teams, Key, timestamp

if (isset($_GET["key"])&&isset($_GET["teams"])&&isset($_GET["map"])&&isset($_GET["timeStamp"]))
{
	include "checkProvider.php";
	if (checkKey($_GET["key"]))
	{
		$time = time();
		$teamNames = [0=>"CT", 1=>"T"];
		$teams = json_decode($_GET["teams"], true);
		if (isset($teams["CT"]) && isset($teams["T"]))
		{
			try
			{
				$dbh = new PDO('mysql:host=localhost;dbname=trackstatdb', "root", "vikhram");

				$teamStmt = $dbh->prepare("INSERT INTO `team`(`Provider_key`) VALUES  (:key)");
				$teamStmt->bindParam(":key", $_GET["key"]);
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

				$findProviderStmt = $dbh->prepare("SELECT * FROM `user_has_provider` WHERE user_SteamID64=:ID");
				$findProviderStmt->bindParam(":ID", $ID);

				$insertProviderStmt = $dbh->prepare("INSERT INTO `user_has_provider` (Provider_ProviderID, user_SteamID64) SELECT ProviderID, :ID FROM `provider` WHERE `key`=:key");
				$insertProviderStmt->bindParam(":ID", $ID);
				$insertProviderStmt->bindParam(":key", $_GET["key"]);

				foreach ($teamNames as $name)
				{
					$teamID = ($name=="CT")? $CTID : $TID;
					foreach ($teams[$name] as $playerID)
					{
						$ID = $playerID;
						$findPlayerStmt->execute();
						if ($findPlayerStmt->rowCount() == 0)
						{
							$addPlayerStmt->execute();
						}
						$insertPlayerStmt->execute();

						$findProviderStmt->execute();
						if ($findProviderStmt->rowCount()==0)
						{
							$insertProviderStmt->execute();
						}
					}
				}

				$matchStmt = $dbh->prepare("INSERT INTO `match` (TimeStamp, Map, TeamID_1, TeamID_2, Provider_key) VALUES (:time, :map, :teamID1, :teamID2, :key)");
				$matchStmt->bindParam(":time", $_GET["timeStamp"]);
				$matchStmt->bindParam(":map", $_GET["map"]);
				$matchStmt->bindParam(":teamID1", $CTID);
				$matchStmt->bindParam(":teamID2", $TID);
				$matchStmt->bindParam(":key", $_GET["key"]);
				$matchStmt->execute();

				$matchID = $dbh->lastInsertId();

				if (isset($_GET["rounds"]) && isset($_GET["kills"]) && $_GET["end"] && isset($_GET["suicides"]))
				{
					$roundStmt = $dbh->prepare("INSERT INTO `rounds` (TimeStamp, WinningSide, MatchID, TeamID_Winner, SteamID64_MVP, Provider_key) VALUES (:time, :winningSide, :matchID, :winningID, :MVPID, :key)");
					$roundStmt->bindParam(":winningSide", $winnigSide);
					$roundStmt->bindParam(":winningID", $winningID);
					$roundStmt->bindParam(":matchID", $matchID);
					$roundStmt->bindParam(":MVPID", $MVPID);
					$roundStmt->bindParam(":time", $timeStamp);
					$roundStmt->bindParam(":key", $_GET["key"]);

					$roundsArray = json_decode($_GET["rounds"], true);
					foreach ($roundsArray as $round)
					{
						$winnigSide = $round["winningSide"];
						$winningID = (($round["winningID"] == 1)?  $CTID : $TID);
						$MVPID = $round["MVPID"];
						$timeStamp = $round["timeStamp"];
						$roundStmt->execute();
					}

					$killStmt = $dbh->prepare("INSERT INTO `kills` (SteamID64_Killer, SteamID64_Killed, TimeStamp, MatchID, Weapon, Provider_key) VALUES (:killerID, :killedID, :timeStamp, :matchID, :weapon, :key)");
					$killStmt->bindParam(":killerID", $killerID);
					$killStmt->bindParam(":killedID", $killedID);
					$killStmt->bindParam(":timeStamp", $timeStamp);
					$killStmt->bindParam(":matchID", $matchID);
					$killStmt->bindParam(":weapon", $weapon);
					$killStmt->bindParam(":key", $_GET["key"]);

					$killsArray = json_decode($_GET["kills"], true);
					foreach ($killsArray as $kill)
					{
						$killerID = $kill["killerID"];
						$killedID = $kill["killedID"];
						$timeStamp = $kill["timeStamp"];
						$weapon = $kill["weapon"];
						$killStmt->execute();
					}

					$endStmt = $dbh->prepare("INSERT INTO `matchend` (TimeStamp, MatchID) VALUES (:end, :matchID)");
					$endStmt->bindParam(":end", $_GET["end"]);
					$endStmt->bindParam(":matchID", $matchID);
					$endStmt->execute();

					$suicideStmt = $dbh->prepare("INSERT INTO `suicide` (MatchID, SteamID64, TimeStamp, Provider_key) VALUES (:matchID, :ID, :timeStamp, :key)");
					$suicideStmt->bindParam(":matchID", $matchID);
					$suicideStmt->bindParam(":ID", $ID);
					$suicideStmt->bindParam(":timeStamp", $timeStamp);
					$suicideStmt->bindParam(":key", $_GET["key"]);

					$suicideArray =  json_decode($_GET["suicides"], true);
					foreach($suicideArray as $suicide)
					{
						$ID = $suicide["steamID"];
						$timeStamp = $suicide["timeStamp"];
						$suicideStmt->execute();
					}
					print "done";
				}
				else
				{
					$json = ["matchID"=>$matchID, "CTID"=>$CTID, "TID"=>$TID];
					print "<pre>";
					print json_encode($json, JSON_PRETTY_PRINT);
					print "</pre>";
				}
			} catch (PDOException $e)
			{
				print "Error!: " . $e->getMessage() . "<br/>";
				die();
			}
			$dbh = null;
		}
		else
		{
			print  "Invalid teams JSON";
		}
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
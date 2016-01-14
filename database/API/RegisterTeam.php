<?php
function assignTeam($teams, $key)
{
	$time = time();
	$teamNames = [0=>"CT", 1=>"T"];
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
			if ($name=="CT")
			{
				$teamID = $CTID;
			}
			else
			{
				$teamID = $TID;
			}
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
	} catch (PDOException $e)
	{
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	$dbh = null;

	return [0=>$CTID, 1=>$TID];
}

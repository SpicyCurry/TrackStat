<?php
if (isset($_GET["steamID"]) && isset($_GET["providerID"]))
{
	$ID = $_GET["steamID"];
	$providerID = $_GET["providerID"];
	try
	{
		$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");

		$teamStmt = $dbh->prepare("SELECT SteamID64 FROM `team_has_user` WHERE `TeamID`=:TeamID");
		$teamStmt->bindParam(":TeamID", $teamID);

		$roundStmt = $dbh->prepare("SELECT TimeStamp, WinningSide, TeamID_Winner, SteamID64_MVP FROM `rounds` WHERE MatchID=:matchID");
		$roundStmt->bindParam(":matchID", $matchID);

		$killsStmt = $dbh->prepare("SELECT SteamID64_Killer AS KillerID, SteamID64_Killed AS KilledID, TimeStamp, Weapon FROM kills WHERE MatchID=:matchID AND SteamID64_Killer=:ID");
		$killsStmt->bindParam(":matchID", $matchID);
		$killsStmt->bindParam(":ID", $ID);

		$deathStmt = $dbh->prepare("SELECT SteamID64_Killer AS KillerID, SteamID64_Killed AS KilledID, TimeStamp, Weapon FROM kills WHERE MatchID=:matchID AND SteamID64_Killed=:ID");
		$deathStmt->bindParam(":matchID", $matchID);
		$deathStmt->bindParam(":ID", $ID);

		$suicideStmt = $dbh->prepare("SELECT SteamID64, TimeStamp FROM suicide WHERE SteamID64=:ID AND MatchID=:matchID");
		$suicideStmt->bindParam(":ID", $ID);
		$suicideStmt->bindParam(":matchID", $matchID);

		$endStmt = $dbh->prepare("SELECT TimeStamp FROM matchend WHERE MatchID=:matchID");
		$endStmt->bindParam(":matchID", $matchID);

		$matchesStmt = $dbh->prepare("
SELECT
`team_has_user`.SteamID64,
`match`.MatchID,
`match`.TimeStamp AS StartTime,
`match`.map,
IF (`match`.TeamID_1=`team_has_user`.TeamID, `match`.TeamID_1, `match`.TeamID_2) AS FriendlyTeamID,
IF (`match`.TeamID_2=`team_has_user`.TeamID, `match`.TeamID_1, `match`.TeamID_2) AS EnemyTeamID,
`match`.Provider_Key
FROM `team_has_user`
INNER JOIN `match`
ON `team_has_user`.TeamID=`match`.TeamID_1 OR `team_has_user`.TeamID=`match`.TeamID_2
INNER JOIN `provider`
ON `provider`.key = `match`.Provider_Key
WHERE SteamID64 =:ID AND ProviderID =:providerID");
		$matchesStmt->bindParam(":ID", $ID);
		$matchesStmt->bindParam(":providerID", $providerID);
		$matchesStmt->execute();
		$array = [];

		while ($result = $matchesStmt->fetch(PDO::FETCH_ASSOC))
		{
			$matchID = $result["MatchID"];

			$endStmt->execute();
			if ($endStmt->rowCount()==1)
			{
				$result["MatchEnd"]=$endStmt->fetch(PDO::FETCH_NUM)[0];
			}

			$result["Teams"] = [];
			$teamID = $result["FriendlyTeamID"];
			$teamStmt->execute();
			$result["Teams"]["Friendlies"] = $teamStmt->fetch(PDO::FETCH_NUM);

			$teamID = $result["EnemyTeamID"];
			$teamStmt->execute();
			$result["Teams"]["Enemies"] = $teamStmt->fetch(PDO::FETCH_NUM);


			$roundStmt->execute();
			$result["Rounds"] = $roundStmt->fetchAll(PDO::FETCH_ASSOC);

			$killsStmt->execute();
			$result["Kills"] = $killsStmt->fetchAll(PDO::FETCH_ASSOC);

			$deathStmt->execute();
			$result["Deaths"]["Killed"] = $deathStmt->fetchAll(PDO::FETCH_ASSOC);

			$suicideStmt->execute();
			$result["Deaths"]["Suicide"] = $suicideStmt->fetchAll(PDO::FETCH_ASSOC);

			array_push($array, $result);
		}
		print "<pre>";
		print json_encode($array, JSON_PRETTY_PRINT);
		print "</pre>";

	} catch (PDOException $e)
	{
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	$dbh = null;
}
else
{
	print "Invalid arguments";
}
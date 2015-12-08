<?php
function updateSteamData()
{
	session_start();
	require($_SERVER["DOCUMENT_ROOT"] . "/steamauth/settings.php");

	$url = "http://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?appid=730";
	$key = "&key=" . $steamauth['apikey'];
	$ID = $_SESSION["steam_steamid"];
	$ID_full = "&steamid=" . $ID;
	$time = time();

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url . $key . $ID_full);
	$result = curl_exec($ch);
	curl_close($ch);

	$stat_array = json_decode($result, true)["playerstats"]["stats"];

	$total_kills = $stat_array["0"]["value"];
	$total_death = $stat_array["1"]["value"];
	$total_time_played = $stat_array["2"]["value"];
	$total_matches_won = $stat_array["127"]["value"];

	try
	{
		$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");
		$insertStmt = $dbh->prepare("INSERT INTO steam_data(SteamID64, TimeStamp, totalKills, totalDeaths, totalTimePlayed, totalMatchesWon) VALUES (:ID, :TimeStamp, :totalKills, :totalDeaths, :totalTimePlayed, :totalMatchesWon)");
		$insertStmt->bindParam(":ID", $ID);
		$insertStmt->bindParam(":TimeStamp", $time);
		$insertStmt->bindParam(":totalKills", $total_kills);
		$insertStmt->bindParam(":totalDeaths", $total_death);
		$insertStmt->bindParam(":totalTimePlayed", $total_time_played);
		$insertStmt->bindParam(":totalMatchesWon", $total_matches_won);
		if ($insertStmt->execute())
		{
			echo "Okay!";
		} else
		{
			echo "Failed!";
		}

	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	$dbh = null;
}
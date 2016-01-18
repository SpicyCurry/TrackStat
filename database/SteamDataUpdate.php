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
	$stat_json = [];
	foreach ($stat_array as $i)
	{
		$stat_json[$i["name"]]=$i["value"];
	}
	$stat_json = json_encode($stat_json, JSON_PRETTY_PRINT);

	try
	{
		$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");
		$insertStmt = $dbh->prepare("INSERT INTO steam_data(SteamID64, TimeStamp, json) VALUES (:ID, :TimeStamp, :json)");
		$insertStmt->bindParam(":ID", $ID);
		$insertStmt->bindParam(":TimeStamp", $time);
		$insertStmt->bindParam(":json", $stat_json);
		$insertStmt->execute();

	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	$dbh = null;
}

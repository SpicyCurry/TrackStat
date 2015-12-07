<?php

$ID = "76561197961713788";

$insert = [];

array_push( $insert, [$ID, 1449418223-86400*5, 17367-100, 16275-75,     1207118-3600*5, 269-5]);
array_push( $insert, [$ID, 1449418223-86400*4, 17367-75,  16275-70,     1207118-3600*4, 269-4]);
array_push( $insert, [$ID, 1449418223-86400*3, 17367-60,  16275-60,     1207118-3600*3, 269-3]);
array_push( $insert, [$ID, 1449418223-86400*2, 17367-45,  16275-40,     1207118-3600*2, 269-2]);
array_push( $insert, [$ID, 1449418223-86400*1, 17367-20,  16275-35,     1207118-3600*1, 269-1]);

try
{
	$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");
	$insertStmt = $dbh->prepare("INSERT INTO steam_data(SteamID64, TimeStamp, totalKills, totalDeaths, totalTimePlayed, totalMatchesWon) VALUES (:ID, :TimeStamp, :totalKills, :totalDeaths, :totalTimePlayed, :totalMatchesWon)");

	foreach($insert as $i)
	{
		$insertStmt->bindParam(":ID", $i[0]);
		$insertStmt->bindParam(":TimeStamp", $i[1]);
		$insertStmt->bindParam(":totalKills", $i[2]);
		$insertStmt->bindParam(":totalDeaths", $i[3]);
		$insertStmt->bindParam(":totalTimePlayed", $i[4]);
		$insertStmt->bindParam(":totalMatchesWon", $i[5]);
		var_dump($insertStmt->execute());
	}


} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}
$dbh = null;
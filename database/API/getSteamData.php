<?php
if (isset($_GET["steamID"]))
{
	$ID = $_GET["steamID"];
	try
	{
		$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");

		$selectStmt = $dbh->prepare("SELECT TimeStamp, json FROM steam_data WHERE SteamID64=:ID");
		$selectStmt->bindParam(":ID", $ID);
		$selectStmt->execute();

		$selectStmt->bindColumn("TimeStamp", $timestamp);
		$selectStmt->bindColumn("json", $json);
		$resultArray = [];
		while ($selectStmt->fetch())
		{
			$temp = json_decode($json, PDO::FETCH_ASSOC); //Wtf PDO::FETCH_ASSOC here?
			$temp["TimeStamp"] = $timestamp;
			array_push($resultArray, $temp);
		}
		print(json_encode($resultArray, JSON_PRETTY_PRINT));


	} catch (PDOException $e)
	{
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	$dbh = null;
}
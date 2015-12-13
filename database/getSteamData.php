<?php
session_start();
$ID = $_SESSION["steam_steamid"];
try
{
	$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");
	$selectStmt =  $dbh->prepare("SELECT TimeStamp, json FROM steam_data WHERE SteamID64=:ID");
	$selectStmt->bindParam(":ID", $ID);
	$selectStmt->execute();

	$selectStmt->bindColumn("TimeStamp",$timestamp);
	$selectStmt->bindColumn("json", $json);
	$resultArray = [];
	while($selectStmt->fetch())
	{
		$temp = json_decode($json, PDO::FETCH_ASSOC);
		$temp["TimeStamp"] = $timestamp;
		array_push($resultArray,$temp);
	}
	print(json_encode($resultArray, JSON_PRETTY_PRINT));


} catch (PDOException $e)
{
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}
$dbh = null;
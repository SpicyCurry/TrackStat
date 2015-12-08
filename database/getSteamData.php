<?php
session_start();
$ID = $_SESSION["steam_steamid"];
$provider = $_GET["provider"];
try
{

	$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");
	$selectStmt =  $dbh->prepare("SELECT * FROM ".$provider."_data WHERE SteamID64=:ID");
//	^ MAJOR SECURITY LEAK! ^ Will fix later (maybe).
//  $selectStmt->bindParam(":provider", $provider);
	$selectStmt->bindParam(":ID", $ID);
	$selectStmt->execute();

	print(json_encode($selectStmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));


} catch (PDOException $e)
{
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}
$dbh = null;

<?php
session_start();
$ID = $_SESSION["steam_steamid"];

try
{
	$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");
	$selectStmt =  $dbh->prepare("SELECT * FROM steam_data WHERE SteamID64=:ID");
	$selectStmt->bindParam(":ID", $ID);
	$selectStmt->execute();

	print(json_encode($selectStmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));


} catch (PDOException $e)
{
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}
$dbh = null;
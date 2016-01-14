<?php

if (isset($_GET["steamID"]))
{
	try
	{
		$dbh = new PDO('mysql:host=localhost;dbname=TrackStatDB', "root", "");

		$findProviderStmt = $dbh->prepare("SELECT * FROM `user_has_provider` WHERE user_SteamID64=:ID");
		$findProviderStmt->bindParam(":ID", $_GET["ID"]);
		$findProviderStmt->execute();
		print (json_encode($findProviderStmt->fetchAll(PDO::FETCH_NUM)));
	} catch (PDOException $e)
	{
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	$dbh = null;

}
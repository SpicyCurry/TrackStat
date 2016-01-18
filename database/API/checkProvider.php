<?php
function checkKey($key)
{
	try
	{
		$dbh = new PDO('mysql:host=localhost;dbname=trackstatdb', "root", "vikhram");

		$selectStmt = $dbh->prepare("SELECT * FROM `provider` WHERE `key` =:key");
		$selectStmt->bindParam(":key", $key);
		$selectStmt->execute();
		$num = $selectStmt->rowCount();
	} catch (PDOException $e)
	{
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	$dbh = null;
	if ($num == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}
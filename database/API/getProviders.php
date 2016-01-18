<?php

if (isset($_GET["steamID"]))
{
	try
	{
		$dbh = new PDO('mysql:host=localhost;dbname=trackstatdb', "root", "vikhram");

		$findProviderStmt = $dbh->prepare("SELECT ProviderID, ProviderName FROM `user_has_provider` INNER JOIN provider ON provider.ProviderID = user_has_provider.Provider_ProviderID WHERE user_SteamID64=:ID");
		$findProviderStmt->bindParam(":ID", $_GET["steamID"]);
		$findProviderStmt->execute();
		$temp = $findProviderStmt->fetchAll(PDO::FETCH_NUM);
		$i = 0;
		while ($i < count($temp))
		{
			$temp[$i] = $temp[$i][0];
			$i = $i +1;
		}
		print "<pre>";
		print (json_encode($temp));
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
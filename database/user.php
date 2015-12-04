<?php
session_start();

checkUser();

function checkUser()
{
    $mysqli = new mysqli("localhost", "server", "10blowjobsfromAlex", 'TrackStatDB');

    if ($mysqli->connect_errno)
    {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    $selectStmt = $mysqli->prepare("SELECT * FROM user WHERE SteamID64=?");
    var_dump($selectStmt);
    $ID = $_SESSION["steam_steamid"];
    var_dump($ID);

    $selectStmt->bind_param("s", $ID);
    $selectStmt->execute();

    $results = $selectStmt->get_result();
    $selectStmt->close();
    $time = time();
    var_dump($time);

    if ($results->num_rows > 0) {
        $updateStmt = $mysqli->prepare("UPDATE user SET LastLogin=? WHERE SteamID64=?");
        $updateStmt->bind_param("is", $time, $ID);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        $insertStmt = $mysqli->prepare("INSERT INTO user (SteamID64, LastLogin) VALUES (?,?)");
        $insertStmt->bind_param("si", $ID, $time);
        var_dump($_SESSION["steam_steamid"]);
        $insertStmt->execute();
        $insertStmt->close();
    }

    $mysqli->close();

}
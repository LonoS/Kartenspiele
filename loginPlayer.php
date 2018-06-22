<?php
$host = "localhost";
$datenbank = "Kratzen";
$userName = "root";
$password = "";


session_start();

if(isset($_SESSION["user"])){
    header("index.php");
}

if(isset($_POST["username"]) && !($_POST["username"] === "") && isset($_POST["PotID"]) && !($_POST["PotID"] === "")) {


    $pdo = new PDO("mysql:host={$host}; dbname={$datenbank}", $userName, $password);
    $pdo->exec("set names utf8");

    $podRES = $pdo->query("SELECT * FROM Pot");
    $guthaben;
    foreach ($podRES as $row){
        if($row["ID"] === $_POST["PotID"]){
            $guthaben = $row["Startguthaben"];
        }

    }
    if(!isset($guthaben)){
        echo "<p>ACHTUNG -> KEIN GÜLTIGER POT</p>";
        $_POST["username"] = "";
        $_POST["PotID"] = "";
        init();
    }

    $stmt = $pdo->prepare("INSERT INTO Spieler(BenutzerName,Guthaben, PotNumber) VALUES (:BenutzerName,:Guthaben,:PotNumber)");
    $stmt -> execute(["BenutzerName"=> $_POST["username"],"Guthaben"=>$guthaben, "PotNumber"=>$_POST["PotID"]]);



    $Userid = "";
    $res = $pdo->query("SELECT * FROM Spieler");
    foreach ($res as $obj){
        $Userid = $obj["ID"];
    }

    $_SESSION["user"] = $Userid;
    echo "<p>Hallo User {$Userid}</p>";
    header("location:index.php");

}
elseif (isset($_POST["RoomName"]) && !($_POST["RoomName"] === "") && isset($_POST["PotStart"]) && !($_POST["PotStart"] === "")){
    $pdo = new PDO("mysql:host={$host}; dbname={$datenbank}", $userName, $password);
    $pdo->exec("set names utf8");

    $stmt = $pdo->prepare("INSERT INTO Pot(Name,Startguthaben) VALUES (:PotName,:PotStart)");
    $stmt -> execute(["PotName"=> $_POST["RoomName"],"PotStart"=>$_POST["PotStart"]]);

    $PotID = "";
    $res = $pdo->query("SELECT * FROM Pot");
    foreach ($res as $obj){
        $PotID = $obj["ID"];
    }
    $_SESSION["potUser"] = $PotID;
    header("location:indexPot.php");

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<form action="loginPlayer.php" method="post">
    <h3>Als Spieler anmelden</h3>
    <p>Username</p>
    <p><input type="text" name="username"></p>

    <p>Deine Pot ID:</p>
    <p><input type="text" name="PotID"></p>

    <p><button type="submit">Beitreten</button></p>

    <h3>Neuen Spielraum erstellen</h3>

    <p>Spielraumname: </p>
    <p><input type="text" name="RoomName"></p>

    <p>Startguthaben:</p>
    <p><input type="text" name="PotStart"></p>
    <p><button type="submit">Erstellen</button></p>

</form>

</body>
</html>
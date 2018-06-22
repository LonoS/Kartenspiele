

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../Kartenspiele/CSS-Files/loginStyle.css">


</head>
<body>
<form action="loginPlayer.php" method="post" style="display: flex;">



    <div class="sheet">
        <h1 class="ueberschrift">Kratzen</h1>
        <h3 id="unter-ueberschrift" class="unter-ueberschrift">Anmelden</h3>

        <p id="beschreibung1" class="beschreibung">Benutzername</p>
        <input id="input1" class="username" autocomplete="off" type="text" name="username" placeholder="Nutzername">

        <p id="beschreibung2" class="beschreibung">Raumname</p>
        <input id="input2" class="potID" autocomplete="off" type="text" name="PotID" placeholder="Raum ID">

        <button id="beitreten" class="beitreten" type="submit">Beitreten</button>

        <button id="erstellen" class="erstellen" type=button style="width:auto;">Raum erstellen</button>





    </div>


</form>







</body>
</html>

<script defer="defer">
    const $ = document.querySelector.bind(document);
    const $$ = document.querySelectorAll.bind(document);

    var input1 = $("#input1");
    var input2 = $("#input2");
    var unter_ueberschrift = $("#unter-ueberschrift");
    var beschreibung1 = $("#beschreibung1");
    var beschreibung2 = $("#beschreibung2");
    var beitreten = $("#beitreten");
    var erstellen = $("#erstellen");


    console.log(erstellen);
    var event = erstellen.addEventListener("click", changeToErstellen);


    function changeToErstellen() {
        unter_ueberschrift.innerHTML = "Neuen Raum erstellen";

        beschreibung1.innerHTML = "Neuer Raum";
        beschreibung2.innerHTML = "Startguthaben";

        input1.name = "RoomName";
        input2.name = "PotStart";

        input1.placeholder = "RaumID";
        input2.placeholder = "Startguthaben";

        beitreten.innerHTML = "Erstellen";
        erstellen.innerHTML = "Anmelden";

        erstellen.removeEventListener(event);
        event = erstellen.addEventListener("click", changeToBeitreten);

    }
    function changeToBeitreten() {
        erstellen.addEventListener("click", changeToErstellen);

        unter_ueberschrift.innerHTML = "Anmelden";

        beschreibung1.innerHTML = "Nutzername";
        beschreibung2.innerHTML = "Raumname";

        input1.name = "username";
        input2.name = "potID";

        input1.placeholder = "Nutzername eingeben";
        input2.placeholder = "Raum-ID";

        beitreten.innerHTML = "Beitreten";
        erstellen.innerHTML = "Raum beitreten";


        changeToErstellen();

    }

</script>

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



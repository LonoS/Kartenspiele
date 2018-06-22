<?php
session_start();


$userID;
$userName;
$userAmmount;
$potNumber;

$host = "localhost";
$datenbank = "Kratzen";
$loginName = "root";
$loginPass = "";
$pdo = new PDO("mysql:host={$host}; dbname={$datenbank}", $loginName, $loginPass);

$reload = false;
function init()
{
    global $userName, $userAmmount, $userID, $pdo, $potNumber, $reload;



    if(!isset($_SESSION["user"])){
        header("location:loginPlayer.php");
    }
    $userID =  $_SESSION["user"];
    $res = $pdo->query("SELECT * FROM Spieler");
    foreach ($res as $row){
        if($row["ID"] === $userID){
            $potNumber = $row["PotNumber"];
            $userAmmount = $row["Guthaben"];
            $userName = $row["BenutzerName"];
        }
    }

    if(isset($_POST["addAmmount"])){
        if(!($_POST["addAmmount"] === "")){
            $reload = true;
            addAmmount($userAmmount);
        }

    }
    if(isset($_POST["decreaseAmmount"])){
        if(!($_POST["decreaseAmmount"] === "")){
            $reload = true;
            decreaseAmmount($userAmmount);
        }
    }


    if($reload == true){

        $reload = false;
        init();
    }



}


function addAmmount($ammount){
    global $pdo, $potNumber;
    $mehr = $_POST["addAmmount"];
    $_POST["addAmmount"] = "";

    $final = $mehr + $ammount;
    setGuthaben($final);


        $potGut = "";
        $res = $pdo->query("SELECT * FROM Pot");
        foreach ($res as $row){
            if($row["ID"] === $potNumber){
                $potGut = $row["Guthaben"];
            }
        }
        $potFinal = $potGut - $mehr;

        $stmt = $pdo->prepare("UPDATE Pot SET Guthaben = :Guthaben WHERE ID=:id");
        $stmt->execute(["Guthaben" => $potFinal, "id" => $potNumber]);



}
function decreaseAmmount($ammount){
    global $pdo, $potNumber;
    $mehr = $_POST["decreaseAmmount"];
    $_POST["decreaseAmmount"] = "";

    $final =  $ammount - $mehr;
    setGuthaben($final);



        $potGut = "";
        $res = $pdo->query("SELECT * FROM Pot");
        foreach ($res as $row){
            if($row["ID"] === $potNumber){
                $potGut = $row["Guthaben"];
            }
        }
        $potFinal = $potGut + $mehr;

        $stmt = $pdo->prepare("UPDATE Pot SET Guthaben = :Guthaben WHERE ID=:id");
        $stmt->execute(["Guthaben" => $potFinal, "id" => $potNumber]);




}

function setGuthaben($guthaben){
    global $pdo, $userID;

    $stmt = $pdo->prepare("UPDATE Spieler SET Guthaben = :Guthaben WHERE ID=:id");
    $stmt->execute(["Guthaben" => $guthaben, "id" => $userID]);

}



init();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Krotzn</title>
</head>
<body>
<?php
echo "<p style='text-align: right'>Pod: $potNumber</p>";
echo "<h1 style='text-align: center'>Willkommen {$userName} </h1>";
echo "<p>Dein Guthaben beträgt: $userAmmount</p>";
?>

<form action="index.php" method="post">
    <p>Zum Guthaben hinzufügen:</p>
    <p><input type="text" name="addAmmount"></p>

    <p>Vom Guthaben abziehen:</p>
    <p><input type="text" name="decreaseAmmount"></p>

    <p><button type="submit">Bestätigen</button></p>

</form>

</body>
</html>
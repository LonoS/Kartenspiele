<?php

$ID;
$Name;
$Guthaben;
function init(){
    session_start();

    global $ID, $Name, $Guthaben;

    $host = "localhost";
    $datenbank = "Kratzen";
    $loginName = "root";
    $loginPass = "";
    $pdo = new PDO("mysql:host={$host}; dbname={$datenbank}", $loginName, $loginPass);

    $erg = $pdo->query("SELECT * FROM Pot");
    foreach ($erg as $obj){
        if($obj["ID"] === $_SESSION["potUser"]){

            $ID = $obj["ID"];
            $Name = $obj["Name"];
            $Guthaben = $obj["Guthaben"];
        }
    }

}

function logout(){
    $host = "localhost";
    $datenbank = "Kratzen";
    $loginName = "root";
    $loginPass = "";
    $pdo = new PDO("mysql:host={$host}; dbname={$datenbank}", $loginName, $loginPass);

    $stmt = $pdo->prepare("DELETE FROM Pot WHERE ID = :id");
    $stmt->execute(["id" => $_SESSION["potUser"]]);

    session_destroy();

    header("location:index.php");

}

init();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="../Kartenspiele/CSS-Files/indexPodStyle.css">

</head>
<meta http-equiv="refresh" content="2" >
<body>
<?php
echo "<p style='text-align: right' class='podID'>Pot ID: $ID</p>";
echo "<h1 style='text-align: center' class='room'>Room $Name</h1>";
echo "<h3 style='text-align: center' class='podGuthaben'>Pot-Guthaben: $Guthaben €</h3>";

?>

</body>
</html>
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
    if(isset($_POST["addAmmountB"])){
        if(!($_POST["addAmmountB"] === "")){
            echo "<p>" . $_POST["addAmmountB"] . "</p>";

        }

    }

    if(isset($_POST["decreaseAmmount"])){
        if(!($_POST["decreaseAmmount"] === "")){
            $reload = true;
            decreaseAmmount($userAmmount, "notButtons");
        }
    }

    if(isset($_POST["decrease"])){
        if(!($_POST["decrease"] === "")){
            //$reload = true;
            $value = $_POST["decrease"];
            $value = str_replace(" €", "", $value);
            $value = str_replace(" ", "", $value);
            $value = (int) $value;
            decreaseAmmount($value, "buttons");

        }
    }
    if(isset($_POST["increase"])){
        if(!($_POST["increase"] === "")){
            //$reload = true;
            $value = $_POST["increase"];
            $value = str_replace(" €", "", $value);
            $value = str_replace(" ", "", $value);
            $value = (int) $value;
            addAmmount($value, "buttons");

        }
    }




    if($reload == true){

        $reload = false;
        init();
    }



}


function addAmmount($ammount, $where) {
    global $pdo, $potNumber;

    if ($where == "buttons") {
        $mehr = (int) str_replace(" €", "", $_POST["increase"]);
        $_POST["increase"] = "";


    }else{
        $mehr = $_POST["addAmmount"];
        $_POST["addAmmount"] = "";

    }


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
function decreaseAmmount($ammount, $where){
    global $pdo, $potNumber;
    if($where == "buttons"){
        $mehr = (int) str_replace(" €", "", $_POST["decrease"]);
    }else{
        $mehr = $_POST["decreaseAmmount"];

    }
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
    <link rel="stylesheet" href="../Kartenspiele/CSS-Files/main.css">

</head>
<body>
<?php
echo "<p style='text-align: right' class='pod'>Pod: $potNumber</p>";
echo "<h1 style='text-align: center' class='welcomeField'>Willkommen {$userName} </h1>";
echo "<div class='container'>";
echo "<div class='menuBand'>
    <li onclick='changeToAdd()' class='add' id='add'>Guthaben hinzufügen</li>
    <li   class='remove' id='remove'>Guthaben abziehen</li>
    
</div>";
echo "<div class='left'>";
?>


<form action="index.php" method="post">
<!--    <p>Zum Guthaben hinzufügen:</p>-->
<!--    <p><input type="text" name="addAmmount"></p>-->
    <p id="header">Vom Guthaben abziehen:</p>
    <div class="buttonsContainer">
    <?php
        global $text;
        $text = "remove";
        function createButtons($text) {
            if ($text == "remove") {

            $i = 1;
            while ($i < 11) {
                $out = $i * 10;
                echo "<input type='submit' id='buttons' class='buttons' name='decrease' value='$out €'> </input>";
                $i++;
                }
            }else{
                $i = 1;
                while ($i < 9){
                    echo "<button class='buttons'>1 / $i</button>";
                }

            }

        }
        createButtons($text);

    ?>
    </div>


    <p>Eigener Betrag: <input class="input" type="text" name="decreaseAmmount"></p>

    <p><button class="submit" type="submit">Bestätigen</button></p>

</form>
<?php
    echo "</div>";
    echo "</div>";
    echo "<p class='guthaben'>Dein Guthaben beträgt: $userAmmount</p>";
?>

</body>
</html>

<script defer="defer">

    const $ = document.querySelector.bind(document);
    const $$ = document.querySelectorAll.bind(document);


    var addMenuButton = $("#add");
    var removeMenuButton = $("#remove");
    var buttons = $$("#buttons");
    console.log(buttons);





    function changeToAdd() {
        buttons.forEach(function (entry) {
            entry.name = "increase"
        });
        $("#header").innerHTML = "Zu Guthaben hinzufügen";
        console.log("changed");

    }


</script>
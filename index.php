<?php 
  session_start();
  if(!isset($_SESSION['selectedDates']))
    $_SESSION['selectedDates'] = array();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Terminfindung</title>
    <link rel="stylesheet" href="style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <?php
        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $dbname = "terminfindung";
        
        // Create SQL connection
        $conn = new mysqli($servername, $username, $dbpassword, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    ?>
    <!--Header-->
    <a style="text-decoration: none;" href="index.php">
        <header>
            <h1>Barkeeper buchen<h1>
        </header>
    </a>
    <!--menübar unter Header-->
    <div id="menuBar">
        <button onclick="window.location.href = 'index.php?kathegory=calendar';">Terminanfrage</button>
        <?php
            if(!isset($_SESSION["password"])){
                echo "<button onclick=\"window.location.href = 'index.php?kathegory=login';\" style='float: right;'>Login</button>";
            }
            else{
                echo "<button onclick=\"window.location.href = 'index.php?kathegory=cocktails';\" >Cocktails</button>"; // Robins Cocktail Seite einbinden. Über Zutaten mit API cocktails finden
                echo "<button onclick=\"window.location.href = 'index.php?kathegory=logout';\" style='float: right;'>Logout</button>
                <button onclick=\"window.location.href = 'index.php?kathegory=accsettings';\" style='float: right;'>Konto</button>
                <button onclick=\"window.location.href = 'index.php?kathegory=aboutUs';\" style='float: right;'>Über uns</button>";
            }
        ?>
    </div>
    <?php
        // Zwischen eingebundenen Seiten unter menübar wählen
        if(isset($_GET["kathegory"])){
            $_GET['kathegory'] = test_input($_GET['kathegory']);

            if($_GET["kathegory"] == "calendar")
                include "calendar.php";
            elseif($_GET["kathegory"] == "login")
                include "Login.php";
            elseif($_GET["kathegory"] == "register")
                include "Register.php";
            elseif($_GET["kathegory"] == "accsettings")
                include "AccSettings.php";
            elseif($_GET["kathegory"] == "get-common-dates")
                include "GetCommonDates.php";
            elseif($_GET["kathegory"] == "logout"){
                session_unset();
                header("Location: index.php");
            }
        }
        elseif(isset($_GET["manage-members"])){
            $_GET['manage-members'] = test_input($_GET['manage-members']);
            include "ManageMembers.php";
        }
        elseif(isset($_GET["create-group"])){
            $_GET['create-group'] = test_input($_GET['create-group']);
            include "CreateGroup.php";
        }
        elseif(isset($_GET["delete-acc"])){
            $_GET['delete-acc'] = test_input($_GET['delete-acc']);
            require_once('AccSettings.php');
            delAcc($conn);
        }
        elseif(isset($_GET["leave-group"])){
            $_GET['leave-group'] = test_input($_GET['leave-group']);
            require_once('AccSettings.php');
            leaveGroup($conn);
        }
        elseif(isset($_GET['inv-link'])){
            $_GET['inv-link'] = test_input($_GET['inv-link']);
            $_SESSION['inv-link'] = $_GET['inv-link'];
            include "join_Group.php";
        }
        elseif(isset($_GET['forgot-passw'])){
            $_GET['forgot-passw'] = test_input($_GET['forgot-passw']);
            include "ResetPW.php";
        }
        else{
            echo "<h1>Die besten Barkeeper</h1>
            <h2 style='text-align: center;'>aus Bönnigheim und der Umgebung</h2><br><br>
            <ul style='margin-left: 20%;'>
                <li>Fragen Sie über den Kalender <b>Termine</b> bei uns an.</li>
                <li>Meistens nur am Wochenende <b>verfügbar</b></li>
                <li><b>Preise</b> varieren nach Personenanzahl und persönlichen Ansprüchen.</li> 
            </ul>
            ";
        }
        // Inputs harmlos machen
        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        // user-ID über Email adresse returnen
        function get_userID($email, $conn){
            if(isset($_SESSION['email'])){
                $sqlUserId = "SELECT id FROM user WHERE email='". $_SESSION['email'] . "'";
                $result = $conn->query($sqlUserId);
                if($result->num_rows > 0)
                    return $result->fetch_assoc()['id'];
                else
                    return null;
            }
        }
    ?>
  </body>
</html>
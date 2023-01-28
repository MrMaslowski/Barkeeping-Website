<!DOCTYPE html>
<html>
    <head>
        <script src="JavascriptFunctions.js"></script>
    </head>
    <body>
        <h1>Registrieren</h1>
        <div class="content">
        <form action="index.php?kathegory=register" method="post">
            <table>
            <tr><td>E-mail: </td><td><input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>"></td></tr>
            <tr><td>Passwort: </td><td><input type="password" name="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : '' ?>"></td></tr>
            <tr><td>Passwort wiederholen: </td><td><input type="password" name="password2" value="<?php echo isset($_POST['password2']) ? $_POST['password2'] : '' ?>"></td></tr>
            </table>
            <input type="submit">
        </form>
        <?php
            $email = $password = $password2 = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $email = test_input($_POST["email"]);
                $password = test_input($_POST["password"]);
                $password2 = test_input($_POST["password2"]);

                if(empty($email) || empty($password) || empty($password2))
                    echo "<p class='error'>*Füllen sie alle Felder richtig aus.</p>";
                else{
                    
                    // Prüfen ob mail schon verwendet
                    $sql = "SELECT email FROM user WHERE email = '" . $email . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0)
                        echo "<p class='error'>Email bereits verwendet</p>";

                    else{
                        if($password == $password2){
                            // In Datenbank eintragen & Sessions setzen
                            $password = password_hash($password, PASSWORD_DEFAULT);
                            $sql = "INSERT INTO user (email, passwort) VALUES (\"" . $email . "\", \"" . $password . "\")"; 

                            if ($conn->query($sql) === TRUE){
                                // echo "New record created successfully";
                                $_SESSION["email"] = $email;
                                $_SESSION["password"] = $password;

                                if(isset($_SESSION['inv-link'])){
                                    require_once('join_Group.php');
                                    join_Group($conn);
                                }
                                header("Location: index.php");
                            }
                            else
                                echo "Error: " . $sql . "<br>" . $conn->error;
                                
                        }
                        else
                            echo "Passwörter stimmen nicht überein";
                    }
                }
                
            }
        ?>
        </div>
    </body>
</html>
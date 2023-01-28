<!DOCTYPE html>
<html>
    <body>
        <h1>Login</h1>
        <div class="content">
            <form action="index.php?kathegory=login" method="post">
                <table>
                    <tr><td>E-mail: </td><td><input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>"></td></tr>
                    <tr><td>Passwort: </td><td><input type="password" name="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : '' ?>"></td></tr>
                </table>
                <input type="submit" value="Login">
                
                <p>Noch kein Account vorhanden? <a href="index.php?kathegory=register">Hier</a> registrieren.</p>
            </form>
            <?php

                $email = $password = "";

                // nach Button
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $email = test_input($_POST["email"]);
                    $password = test_input($_POST["password"]);

                    if(empty($email) || empty($password))
                        echo "<p style='color: red;'>*Füllen sie alle Felder richtig aus.</p>";
                    else{
                        $sql = "SELECT id, email, passwort FROM user WHERE email = '" . $email . "'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {

                            $result = $result->fetch_assoc();
                            if (password_verify($password, $result["passwort"])) {
                                $_SESSION["email"] = $email;
                                $_SESSION["password"] = $password;
                                $userID = $result['id'];
                                $today = date("Y-m-d");
                                echo $today;

                                // delete dates in past
                                $sql="DELETE FROM termine WHERE userId=".$userID." AND date<= DATE'".$today."'"; // today is not defined yet
                                if($conn->query($sql) === FALSE){
                                    echo "<p class='error'>*Error.</p>";
                                }

                                // set selected days
                                $sql = "SELECT date FROM termine WHERE userId = '" . $result['id'] . "'";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $_SESSION['selectedDates'][] = $row['date'];
                                    }
                                }
                                if(isset($_SESSION['inv-link'])){
                                    require_once('join_Group.php');
                                    join_Group($conn);
                                }

                                header("Location: index.php");
                            }
                            else
                                echo "<p class='error'>*Passwort passt nicht zur angegebenen Email.</p>";
                        }
                        else
                            echo "<p class='error'>*Email nicht registriert.</p>";
                    }
                    
                }
            ?>
        </div>
    </body>
</html>
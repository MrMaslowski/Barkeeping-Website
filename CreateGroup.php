<!DOCTYPE html>
<html>
    <body>
        <h1>Gruppe erstellen</h1>
        <div class="content">
        <form action="index.php?create-group=Gruppe+erstellen" method="post">
            <table>
            <tr><td>Gruppenname: </td><td><input type="text" name="groupname" value="<?php echo isset($_POST['groupname']) ? $_POST['groupname'] : '' ?>"></td></tr>
            </table>
            <input type="submit" value="Erstellen" name="createGroup">
        </form>
        <?php
            $name = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = test_input($_POST["groupname"]);

                if(empty($name))
                    echo "<p class='error'>*Füllen sie alle Felder richtig aus.</p>";
                else{
                    
                    // Prüfen ob name schon verwendet
                    $sql = "SELECT Name FROM gruppe WHERE Name = '" . $name . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0)
                        echo "<p class='error'>*Name bereits verwendet</p>";
                    // wenn angemeldet
                    elseif(isset($_SESSION["email"])){
                        // get OwnerID
                        $sqlowner = "SELECT id FROM user WHERE email = '" . $_SESSION["email"] . "'";
                        $resultowner = $conn->query($sqlowner)->fetch_assoc();

                        // create Invitation Link
                        while(TRUE){
                            $invitationLink = generateRandomString();

                            $sql = "SELECT id FROM gruppe WHERE invLink='". $invitationLink . "'";
                            $result=$conn->query($sql);

                            // when not already given to any group
                            if($result->num_rows == 0)
                                break;
                        }

                        // In Datenbank eintragen & Sessions setzen
                        $sql = "INSERT INTO gruppe (Name, ownerId, invLink) VALUES (\"" . $name . "\", \"" . $resultowner['id'] . "\", \"" . $invitationLink . "\")"; 

                        if ($conn->query($sql) === TRUE){
                            echo "New Group created successfully";
                            // get GroupID
                            $sqlgroup = "SELECT id FROM gruppe WHERE Name = '" . $name . "'";
                            $resultgroup = $conn->query($sqlgroup)->fetch_assoc();
                            $_SESSION["groupID"] = $resultgroup['id'];
                            
                            $sql2 = "INSERT INTO gruppenzuordnung (userId, groupId) VALUES (\"" . $resultowner['id'] . "\", \"" . $resultgroup['id'] . "\")"; 
                            if($conn->query($sql2) === TRUE){
                                echo "New Groupassignment created successfully";
                                header("Location: index.php?kathegory=get-common-dates");
                            }
                            else
                                echo "Error: " . $sql2 . "<br>" . $conn->error;
                        
                        }
                        else
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        
                    }
                    else
                        echo "<p class='error'>*Sie müssen sich zuerst anmelden.</p>";
                }
                
            }

            //generates random String with certain length (max length varchar: 65)
            function generateRandomString($length = 65) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
            }
        ?>
        </div>
    </body>
</html>
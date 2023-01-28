<!DOCTYPE html>
<html>
    <body>
        <h1>Mitglieder verwalten</h1>
        <div class="content grid-container">
                
            <?php
                $name = "";

                if(!isset($_SESSION['groupID']) || !isset($_SESSION["email"]) ){
                    echo "<p class='error'>Keine Gruppe ausgewählt oder/und nicht angemeldet!</p>";
                }

                // remove group Members
                if (isset($_POST['removeMembers']) && isset($_SESSION['groupID']) && isset($_SESSION["email"])) {
                    //remove selected members
                    foreach($_POST['groupmembers'] as $member){
                        //get userID
                        $sqlUserId = "SELECT id FROM user WHERE email='". $member . "'";
                        $result= $conn->query($sqlUserId)->fetch_assoc();

                        $sql = "DELETE FROM gruppenzuordnung WHERE userId=" . $result['id'] . " AND groupId=" . $_SESSION['groupID'];
                        if($conn->query($sql) === TRUE){
                            echo "Mitglieder erfolgreich entfernt.";
                        }
                        else{
                            echo "Löschung war unerfolgreich.";
                        }
                    }
                    // if group is empty, delete it
                    $sql="SELECT id FROM gruppenzuordnung WHERE groupId=".$_SESSION['groupID'];
                    if($conn->query($sql)->num_rows < 1){
                        $sql= "DELETE FROM gruppe WHERE id=".$_SESSION['groupID'];
                        if($conn->query($sql) === FALSE)
                            echo "<p class='error'>Gruppe konnte nicht gelöscht werden.</p>";
                    }

                }
            ?>
            <div>
                <?php
                    if(isset($_SESSION['groupID'])){
                        // get userIDs from current group
                        $sql = "SELECT userId FROM gruppenzuordnung WHERE groupId=" . $_SESSION['groupID'];
                        $result = $conn->query($sql);


                        echo "<form action='index.php?manage-members=Gruppemmitglieder+verwalten' method='post'>";
                        echo "<br><input type='submit' name='removeMembers' value='Entferne ausgewählte Gruppenmitglieder' onclick=\"return confirm('Sind sie sicher?');\"><br><br>";

                        // print emails from group members
                        if($result->num_rows > 0){

                            while($row = $result->fetch_assoc()){
                                $sql ="SELECT email FROM user WHERE id=". $row['userId'];
                                $username = $conn->query($sql)->fetch_assoc();
                                
                                echo "<input type='checkbox' name='groupmembers[]' value='" . $username['email'] . "'> " . $username['email'] . "<br>";
                            }
                        }
                            echo "</form>";
                    }
                    
                ?>
            </div>
        </div>
    </body>
</html>
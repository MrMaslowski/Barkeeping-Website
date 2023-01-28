<!DOCTYPE html>
<html>
    <body>
        <h1>Kontoübersicht</h1>
        <div class="content grid-container">
            <div>
                <?php
                   
                    if(isset($_SESSION['email'])){
                        if(isset($_POST['group'])){
                            // get groupIDs
                            $sqlgroupid = "SELECT id FROM gruppe WHERE Name = '". $_POST['group'] ."'";
                            $resultgroupid = $conn->query($sqlgroupid)->fetch_assoc();
                            $_SESSION['groupID'] = $resultgroupid['id'];
                        }
                        // show Account Data
                        $sqluser = "SELECT email, id FROM user WHERE email = '". $_SESSION['email'] ."'";
                        $resultuser = $conn->query($sqluser)->fetch_assoc();
                        // get groupIDs
                        $sqlgroupid = "SELECT groupId FROM gruppenzuordnung WHERE userId = '". $resultuser['id'] ."'";
                        $resultgroupid = $conn->query($sqlgroupid);

                        $groups = array();
                        if ($resultgroupid->num_rows > 0) {
                            // output data of each row
                            while($row = $resultgroupid->fetch_assoc()) {
                                // get groupNames
                                $sqlgroupname = "SELECT id, Name FROM gruppe WHERE id = '". $row['groupId'] ."'";
                                $resultgroupname = $conn->query($sqlgroupname)->fetch_assoc();
                                $groups[] = array($resultgroupname["id"], $resultgroupname["Name"]);
                            }
                        }

                        echo "<table class='acc-overview'>";
                        echo "<tr><td><b>E-Mail:  </b></td><td>" . $resultuser['email'] . "</td></tr>";
                        echo "<tr><td><b>Groups:  </b></td><td><form method='post' action='index.php?kathegory=accsettings'>";
                        
                        if(!empty($groups)){
                            foreach($groups as $group){echo "<input type='radio' name='group' value='". $group[1] . "' onchange='this.form.submit()'" . 
                                ((isset($_SESSION['groupID']) && $_SESSION['groupID'] == $group[0])? 'checked=\"checked\"':'') . 
                                ">" . $group[1] . "<br>";
                            }
                        }
                        else
                            echo "<p class='error'>*Keine Gruppen vorhanden.</p>";
                        
                        echo "</form></td></tr>";
                        echo "</table>";
                    }

                    function delAcc($conn){
                        if(isset($_SESSION['email'])){
                            // delete entries from this user and update ownerID from possible created Groups
                            
                            $sqlUser="DELETE FROM user WHERE email='".$_SESSION['email']."'";
                            $sqlDates="DELETE FROM termine WHERE userId=".get_userID($_SESSION['email'], $conn);
                            
                            $sqlgetcreatedGroups="SELECT id FROM gruppe WHERE ownerId=".get_userID($_SESSION['email'], $conn);
                            $result=$conn->query($sqlgetcreatedGroups);

                            // leave all created groups
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    // get memberIds from group
                                    $_SESSION['groupID'] = $row['id'];
                                    leaveGroup($conn);
                                }
                            }

                            if($conn->query($sqlDates) === TRUE && $conn->query($sqlUser) === TRUE){
                                // echo "<p>Konto gelöscht</p>";
                                session_unset();
                                header("Location: index.php");
                            }
                            else
                                echo "<p class='error'>*Error</p>";
                        }
                        else
                            echo "<p class='error'>*Error: Sie müssen sich zuerst anmelden.</p>";
                    }

                    function leaveGroup($conn){
                        if(isset($_SESSION['email']) && isset($_SESSION['groupID'])){
                            
                            // delete group link
                            $sqlGroup="DELETE FROM gruppenzuordnung WHERE userId=".get_userID($_SESSION['email'], $conn)." AND groupId=".$_SESSION['groupID'];
                            if ($conn->query($sqlGroup) === TRUE){

                                // check for other members
                                $sql ="SELECT id, userId FROM gruppenzuordnung WHERE groupId=".$_SESSION['groupID'];
                                $result = $conn->query($sql);

                                // if only member, delete group
                                if($result->num_rows == 0){
                                    $sql="DELETE FROM gruppe WHERE id=".$_SESSION['groupID'];

                                    if($conn->query($sql) === FALSE)
                                        echo "<p class='error'>*Fehler beim Löschen der Gruppe.</p>";
                                }
                                // make other meber owner if user is owner
                                else{
                                    $sqlGetOwner = "SELECT ownerId FROM gruppe WHERE id=".$_SESSION['groupID'];
                                    $resultOwner = $conn->query($sqlGetOwner)->fetch_assoc();

                                    if(get_userID($_SESSION['email'], $conn) == $resultOwner['ownerId']){
                                        // get memberIds from group
                                        $resultMember = $result->fetch_assoc();

                                        // change groupowner
                                        $sqlGroupOwner="UPDATE gruppe SET ownerId=". $resultMember['userId']. " WHERE ownerId=". get_userID($_SESSION['email'], $conn). " AND id=". $_SESSION['groupID'];

                                        if($conn->query($sqlGroupOwner) === FALSE)
                                            echo "<p class='error'>*Fehler beim Ändern des Gruppenbesitzers.</p>";
                                    }
                                }

                                unset($_SESSION['groupID']);
                                header("Location: index.php?kathegory=accsettings");
                            }
                        }
                        else
                            echo "<p class='error'>*Sie müssen angemeldet sein <b>und</b> eine Gruppe ausgewählt haben.</p>";
                    }
                ?>
            </div>
            <div>
                <form action="index.php" method="get">
                    <input type="submit" value="Konto löschen" name="delete-acc" onclick="return confirm('Sind sie sicher?');"><br><br>
                    <input type="submit" value="Gruppe verlassen" name="leave-group" onclick="return confirm('Sind sie sicher?');">
                </form>
            </div>
        </div>
    </body>
</html>
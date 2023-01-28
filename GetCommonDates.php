<!DOCTYPE html>
<html>
    <body>
        <h1>Gruppentermine</h1>
        <div class="content grid-container">
            <div>
                <?php
                
                    if(isset($_SESSION['email']) && isset($_SESSION['password'])){

                        // join Group
                        if(isset($_POST['join-group']) && !empty($_POST['groupLink'])){
                            $sql = "SELECT id FROM gruppe WHERE invLink='". $_POST['groupLink'] . "'";
                            $groupID = $conn->query($sql)->fetch_assoc();
                            
                            if(empty($groupID['id']))
                                echo "<p class='error'>*Gruppe nicht gefunden.</p>";
                            else{
                                //get userID
                                $sqlUserId = "SELECT id FROM user WHERE email='". $_SESSION['email'] . "'";
                                
                                //check if user already member
                                $sql = "SELECT id FROM gruppenzuordnung WHERE userId=" . $conn->query($sqlUserId)->fetch_assoc()['id'] . " AND groupId=" . $groupID['id'];

                                if($conn->query($sql)->num_rows == 0){
                                    $sql = "INSERT INTO gruppenzuordnung (userId, groupId) VALUES (". $conn->query($sqlUserId)->fetch_assoc()['id'] . "," . $groupID['id'] .")";
                                    if($conn->query($sql) === TRUE){
                                        //User is added to group
                                    }
                                    else
                                        echo "Error: " . $sql . "<br>" . $conn->error;
                                }
                            }
                        }


                        if(isset($_POST['group'])){
                            // get groupIDs and set/change sessions
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

                        //  create Table with infos
                        echo "<table class='acc-overview'>";
                        echo "<tr><td><b>E-Mail:  </b></td><td>" . $resultuser['email'] . "</td></tr>";
                        echo "<tr><td><b>Gruppe auswhählen: </b></td><td><form method='post' action='index.php?kathegory=get-common-dates'>";
                        
                        if(!empty($groups)){
                            foreach($groups as $group){echo "<input type='radio' name='group' value='". $group[1] . "' onchange='this.form.submit()'" . 
                                ((isset($_SESSION['groupID']) && $_SESSION['groupID'] == $group[0])? 'checked=\"checked\"':'') . 
                                ">" . $group[1] . "<br>";
                            }
                        }
                        else
                            echo "<p class='error'>*Keine Gruppen vorhanden.</p>";

                        echo "</form></td></tr>";

                        echo "<tr><td><b>Gruppen-Mitglieder </b></td><td>";
                        // print common dates
                        if(isset($_SESSION['groupID'])){
                            $termine = "SELECT email FROM user WHERE id IN (SELECT userId FROM gruppenzuordnung WHERE groupId=". $_SESSION['groupID'] .")";
                            $result = $conn->query($termine);
                            if ($result->num_rows > 0) {
                                // print members
                                while($row = $result->fetch_assoc()) {
                                    echo $row['email'] . "<br>";
                                }
                            }
                            else
                                echo "<p>keine Gemeinsammen Termine verfügbar.</p>";
                        }
                        else
                            echo "<p class='error'>Gruppe auswählen</p>";

                        echo "</td></tr>";

                        echo "<tr><td><b>Gemeinsamme Termine </b><br><br>
                        <form action=\"index.php?kathegory=get-common-dates\" method=\"post\">
                        
                        <label for=\"quantity\">Mindestlänge Zeitraum:</label><br>
                        <input type=\"number\" name=\"common_date_length\" min=\"1\" max=\"40\"><br>
                        <input type=\"submit\" value=\"Anwenden\">
                        </form></td><td>";
                        // print common dates
                        if(isset($_SESSION['groupID'])){
                            $termine = "SELECT date FROM termine WHERE userId IN (SELECT userId FROM gruppenzuordnung WHERE groupId=". $_SESSION['groupID'] .") GROUP BY date HAVING COUNT(date)=(SELECT COUNT(userId) FROM gruppenzuordnung WHERE groupId=" . $_SESSION['groupID'] . ")";
                            $result = $conn->query($termine);
                            
                            // dates in array while day adjoins. if date not adjoins, print array reset counter
                            if ($result->num_rows > 0) {
                                // output data of each row
                                $old_date;
                                $dates = array();

                                while($row = $result->fetch_assoc()) {

                                    // if differenz is more than one day, new block
                                    if(isset($old_date)){
                                        $datediff = strtotime($row['date']) - strtotime($old_date);
                                        $datediff = round($datediff / (60 * 60 * 24));

                                        // print array and reset it
                                        if($datediff>1){
                                            if(!isset($_POST['common_date_length']) || isset($_POST['common_date_length']) && $_POST['common_date_length'] <= count($dates)){
                                                // for($i = 0; $i < count($dates); $i++){
                                                //     echo $dates[$i] . "<br>";
                                                // }
                                                if(count($dates)>1){
                                                    echo $dates[0] . " - " . $dates[count($dates) -1] . "<br>";
                                                }
                                                else
                                                    echo $dates[0] . "<br>";
                                            }
                                            $dates = array();
                                            echo "<br>";
                                        }
                                    }
                                    
                                    $dates[] = $row['date'];

                                    $old_date = $row['date'];
                                }
                            }
                            else
                                echo "Keine Gemeinsammen Termine verfügbar.";

                            // print the last dates array, bigger than filter
                            if(!isset($_POST['common_date_length']) || isset($_POST['common_date_length']) && $_POST['common_date_length'] <= count($dates)){
                                // for($i = 0; $i < count($dates); $i++){
                                //     echo $dates[$i] . "<br>";
                                // }
                                if(count($dates)>1){
                                    echo $dates[0] . " - " . $dates[count($dates) -1] . "<br>";
                                }
                                else
                                    echo $dates[0] . "<br>";
                            }
                            $dates = array();
                        }
                        else
                            echo "<p class='error'>Gruppe auswählen</p>";

                        echo "</td></tr></table>";

                        // get group Link 
                        if(isset($_SESSION['groupID'])){
                            $sqlgrouplink = "SELECT invLink FROM gruppe WHERE id = ". $_SESSION['groupID'];
                            if($conn->query($sqlgrouplink)->num_rows > 0){
                                $resultgroupInvLink = $conn->query($sqlgrouplink)->fetch_assoc();
                                $groupInvLink = $resultgroupInvLink['invLink'];
                            }
                        }
                    }
                ?>
            </div>
            <div>
                <form action="index.php" method="get">
                    <input type="submit" value="Gruppe erstellen" name="create-group"><br><br>
                    <input type="submit" value="Gruppemmitglieder verwalten" name="manage-members"><br><br>
                </form>

                <span id="inv-to-group-label">Zur Gruppe einlanden: </span><i class="arrow down" id="groupInvArrow"></i><br>
                <span id="groupInvLine" style="display: none;">
                <br>
                <p>*Kopiere deinen Einladungs-<b>Link</b> und schicke ihn deinen Freunden/Kollegen</p>
                <input type="text" name="groupInvLink" value="<?php echo isset($groupInvLink)? $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?inv-link=" . $groupInvLink : ""; ?>" id="groupInvLink" readonly><br>
                </span><br>

            </div>
        </div>
        <script>
            var groupInvArrow = document.getElementById('groupInvArrow');
            var groupInvButton = document.getElementById('groupInvButton');

            groupInvArrow.addEventListener("click", function(e) {
                if(groupInvArrow.classList.contains("down"))
                    document.getElementById("groupInvLine").style.display = "inline";
                else
                    document.getElementById("groupInvLine").style.display = "none";

                document.getElementById("groupInvArrow").classList.toggle('down');
                document.getElementById("groupInvArrow").classList.toggle('up');
            }, false);
        </script>
    </body>
</html>
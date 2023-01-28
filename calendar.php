<!DOCTYPE html>
<html>
    <body>
        <h1>Termine anfragen</h1><br><br>
        <div class="grid-container">
            <div class="container">
                <div class="calendar">
                    <div class="month">
                        <i class="fas fa-angle-left prev"></i>
                        <div class="date">
                            <h1></h1>
                            <h2></h2>
                            <p></p>
                        </div>
                        <i class="fas fa-angle-right next"></i>
                    </div>
                    <div class="weekdays">
                        <div>Mo</div>
                        <div>Di</div>
                        <div>Mi</div>
                        <div>Do</div>
                        <div>Fr</div>
                        <div>Sa</div>
                        <div>So</div>
                    </div>
                    <div class="days"></div>
                </div>
            </div>
            <div>
                <div>
                    <form action="index.php?kathegory=calendar" method="post">
                        <input class="sendData" type="submit" value="Termine anfragen" name="sentDates">
                    </form>
                </div>
                <?php
                    // update dates
                    if(isset($_POST['sentDates']) && isset($_SESSION['email'])){
                        //Sessions in Datenbank eintragen
                        
                        // get UserID
                        $sql = "SELECT id FROM user WHERE email = '" . $_SESSION['email'] . "'";
                        $userID = $conn->query($sql)->fetch_assoc();

                        // delete previous dates
                        $sql = "DELETE FROM termine WHERE userId=" . $userID['id'];
                        if($conn->query($sql) === TRUE)
                            echo "<p>*Termine erfolgreich in Datenbank eingetragen</p>";
                        else
                            echo "Error: " . $conn->error;

                        // insert dates in databank
                        foreach($_SESSION['selectedDates'] as $newdate){
                        
                            $sql = "INSERT INTO termine (date, userId) VALUES (\"" . $newdate . "\", \"" . $userID['id'] . "\")"; 
                            if ($conn->query($sql) === TRUE){
                                // echo "New record created successfully";
                            }
                            else
                                echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    }
                    elseif(isset($_POST['sentDates']))
                        echo "<p>*Sie müssen sich zunächst einloggen.</p>";

                    // add currentdate to session
                    if(isset($_POST['select']) && isset($_GET['currentDate']) && !in_array($_GET['currentDate'], $_SESSION['selectedDates'])){
                        $_SESSION['selectedDates'][] = $_GET['currentDate'];
                    }
                    // delete currentdate from session
                    elseif(isset($_POST['select']) && isset($_GET['currentDate']) && in_array($_GET['currentDate'], $_SESSION['selectedDates'])){
                        $index = array_search($_GET['currentDate'], $_SESSION['selectedDates']);
                        array_splice($_SESSION['selectedDates'], $index, 1);
                    }

                    // write php sessions in javascript var
                    echo "<script type='text/javascript'>var selectedDays = [];";
                    foreach($_SESSION['selectedDates'] as $value){
                        echo "selectedDays.push('" . $value . "');";
                    }
                    echo "</script>";
                ?>
            </div>
        </div>
        <script type="text/javascript">
            var currentDate = '<?php if(isset($_GET['currentDate'])) {echo $_GET['currentDate'];} ?>';
        </script>
        <script src="calendar.js"></script>

    </body>
</html>
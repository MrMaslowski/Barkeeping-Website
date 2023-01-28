<!DOCTYPE html>
<html>
    <body>
        <?php
            join_Group($conn);

            function join_Group($conn){
                // join group
                if(isset($_SESSION['email'])){
                    // getgroupID
                    $sql="SELECT id FROM gruppe WHERE invLink='".$_SESSION['inv-link']."'";
                    $result= $conn->query($sql);
                    if($result->num_rows > 0){
                        // add group to user
                        $result = $result->fetch_assoc();
                        //check if user already member
                        $sqluser = "SELECT id FROM gruppenzuordnung WHERE userId=" . get_userID($_SESSION['email'], $conn) . " AND groupId=" . $result['id'];
    
                        if($conn->query($sqluser)->num_rows == 0){
                            $sql="INSERT INTO gruppenzuordnung (userId, groupId) VALUES (".get_userID($_SESSION['email'], $conn).",".$result['id'].")";
                            if($conn->query($sql) === FALSE)
                                echo "<p class='error'>*Datenbankeintrag fehlgeschlagen.</p>";
                            else{
                                echo "<p>Gruppeneintrag erfolgreich. Sehen sie unter ihrem Konto alle Gruppen ein, bei welchen Sie Mitglied sind.</p>";
                            }
                        }
                        else
                            echo "<p class='error'>*Sie sind bereits Mitglied.</p>";
                    }
                    else
                        echo "<p class='error'>*Link ungültig.</p>";
                    
                    unset($_SESSION['inv-link']);
                }else{
                    echo "<p class='error'>*Sie müssen sich zuerst anmelden.</p>";
                    include "Login.php";
                }
            }
            
        ?>
    </body>
</html>
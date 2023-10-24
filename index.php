<?php

require_once 'config.php';

if($_SERVER['REQUEST_METHOD'] == "POST"){
        // echo "Data Send";
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT admin_id, password FROM admins WHERE username = ? ";
        $run = $conn->prepare($sql);
        $run->bind_param("s", $username);
        $run->execute();

        $results = $run->get_result();

        
        if($results->num_rows==1){

            $admin = $results->fetch_assoc();

            if(password_verify($password, $admin['password'])){
                // echo "Password is correct";
                $_SESSION['admin_id'] = $admin['admin_id'];
                $conn->close();
                header('location: admin_dashboard.php');
            }
            else{
                $_SESSION['error'] = "Password is not correct!";
                $conn->close();
                header('location: index.php');
                exit; //posle redirekcija->da ne go izvrsave kodut podole
            }
        }
        else{
            $_SESSION['error'] = "Username does not exists";
            $conn->close();
            header('location: index.php');
            exit;
        }

        
        // var_dump($results);
    }
    



?>





<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
</head>
<body>


<form action="" method="POST">
    Username: <input type="text" name="username"><br>
    Password: <input type="password" name="password"><br>
    <input type="submit" value="Login">
</form>


<?php

if(isset($_SESSION['error'])){
    echo $_SESSION['error'];
    unset($_SESSION['error']);
}

?>


</body>
</html>
<?php

require_once 'config.php';

if($_SERVER['REQUEST_METHOD']=="POST"){
    $member_id = $_POST['member_id'];

    $sql = "DELETE FROM members WHERE member_id = ?";
    $run = $conn->prepare($sql);
    $run->bind_param("i", $member_id);

    if($run->execute()){
        $message = "Member deleted";
    }else{
        $message = "Member not deleted";
    }

    $_SESSION['success_message']=$message;
    header('location: admin_dashboard.php');
    exit();
}

?>
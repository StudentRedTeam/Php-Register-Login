<?php
  
    $connection=mysqli_connect("localhost","root","root","random");

    if (!$connection) {
        die("Connection Failed: " . mysqli_connect_error());
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $username = $_POST['username'];
        $checkUsernameQuery = "SELECT * FROM Users WHERE username=?";
        $checkStatement = mysqli_prepare($connection, $checkUsernameQuery);
        mysqli_stmt_bind_param($checkStatement, "s", $username);
        mysqli_stmt_execute($checkStatement);
        mysqli_stmt_store_result($checkStatement);

        if(mysqli_stmt_num_rows($checkStatement) > 0) {
            echo "Username already exists.";
        }
        else{
            echo "";
        }

        mysqli_stmt_close($checkStatement);
    }

    mysqli_close($connection);
?>
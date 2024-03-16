<?php

    $connection=mysqli_connect("localhost","root","root","random");

    if (!$connection) {
        die("Connection Failed: " . mysqli_connect_error());
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $email = $_POST['email'];
        $checkEmailQuery = "SELECT * FROM Users WHERE email=?";
        $checkStatement = mysqli_prepare($connection, $checkEmailQuery);
        mysqli_stmt_bind_param($checkStatement, "s", $email);
        mysqli_stmt_execute($checkStatement);
        mysqli_stmt_store_result($checkStatement);

        if (mysqli_stmt_num_rows($checkStatement) > 0) {
            echo "Email already exists.";
        }
        else{
            echo "";
        }
        mysqli_stmt_close($checkStatement);
    }

    mysqli_close($connection);
?>
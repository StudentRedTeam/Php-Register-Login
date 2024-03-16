<?php
    session_start();
    if(isset($_SESSION['username'])){
        header("Location: dashboard");
        exit(0);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login or Register</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Our Site</h1>
        </div>
        <div class="nav-links">
            <a href="login">Login</a>
            <a href="register">Register</a>
        </div>
    </div>
</body>
</html>

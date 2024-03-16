<?php 
    session_start();
    if(!isset($_SESSION['username'])){
       header("Location: login");
       exit(0); 
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .welcome-container {
            text-align: center;
            margin-top: 50px;
        }

        .welcome-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="welcome-container">
        <div class="welcome-message">
            <h1>Welcome <?php echo $_SESSION['fullname'];?></h1>
            <h1>Welcome <?php echo $_SESSION['email'];?></h1>
            <p><a href="updateName">Update Name</a> | <a href="updateemail">Update Email</a> | <a href="updatePassword">Update Password</a> | <a href="logout">Logout</a></p>
            <p></p>
        </div>
    </div>

</body>
</html>

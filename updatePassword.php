<?php

    session_start();
    if(!isset($_SESSION['username'])){
        header("Location: login");
        exit();
    }
    $connection=mysqli_connect("localhost","root","root","random");
    if(!$connection)
        die("Connetion failed" . mysqli_connect_error());

    if($_SERVER['REQUEST_METHOD']=="POST"){
        $currentUserPassword=$_POST['currentPassword'];
        $newUserPassword=$_POST['newPassword'];
        $username=$_SESSION['username'];
        $hashPassword=$_SESSION['password'];
            if(empty($currentUserPassword) && empty($newUserPassword)) {
                echo "<script>alert('Fill Field...'); window.location.href = 'updatePassword';</script>";
                exit();
            }
            if(password_verify($currentUserPassword,$hashPassword)){
                $newPasswordHash=password_hash($newUserPassword, PASSWORD_DEFAULT);
                $sql="UPDATE Users SET password = ? WHERE username = ?;";
                $updatePasswordPrepare=mysqli_prepare($connection, $sql);
                if($updatePasswordPrepare){
                    mysqli_stmt_bind_param($updatePasswordPrepare, "ss", $newPasswordHash, $username);
                    mysqli_stmt_execute($updatePasswordPrepare);
                    if(mysqli_stmt_error($updatePasswordPrepare))
                    {
                        echo "Error: " . mysqli_stmt_error($updatePasswordPrepare);
                    }
                    else
                    {
                        $_SESSION['password'] = $newPasswordHash;
                        echo "<script>alert('Password Changed!!'); window.location.href = 'dashboard';</script>";
                        exit();
                    }
                }
            }
            mysqli_stmt_close($updatePasswordPrepare);
    }
    mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>UpdatePassword</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Change Form</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
        <label for="currentPassword">Current Password:</label>
        <input type="password" id="currentPassword" name="currentPassword" required>

        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword" required>

        <input type="submit" name="save" value="Save Changes">
    </form>
</body>
</html>
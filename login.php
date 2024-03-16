<?php
    
    session_start();
    if(isset($_SESSION['username'])){
        header("Location: dashboard");
        exit();
    }

    $connection=mysqli_connect("localhost","root","root","random");
    
    if(!$connection)
        die("Connection Failed: " . mysqli_connect_error());
    
    if($_SERVER['REQUEST_METHOD']=="POST")
    {
        $username=$_POST['username'];
        $userPassword=$_POST['password'];
        $sql="SELECT * FROM Users WHERE username=?;";
        $statement=mysqli_prepare($connection, $sql);
        if($statement)
        {
            mysqli_stmt_bind_param($statement,"s",$username);
            mysqli_stmt_execute($statement);
            $result=mysqli_stmt_get_result($statement);

            if(mysqli_num_rows($result)>0)
            {
                $row=mysqli_fetch_assoc($result);
                $hashPassword=$row['password'];
                $pass=password_verify($userPassword, $hashPassword);
                $is_verified=$row['is_verified'];
                    if($is_verified==1)
                    {
                            if($pass)
                            {
                                $_SESSION['fullname']=$row['fullname'];
                                $_SESSION['username']=$row['username'];
                                $_SESSION['email']=$row['email'];
                                $_SESSION['password']=$row['password'];
                                echo "<script>alert('Login Successful..'); window.location.href = 'dashboard';</script>";
                                exit();
                            }
                            else
                            {
                                echo "<script>alert('Wrong Password, Try again...'); window.location.href = 'login';</script>";
                                exit();
                            }        
                    }
                    else
                    {
                        echo "<script>alert('You are not Verified!'); window.location.href = 'login';</script>";
                        exit();
                    }
                    
            }
            mysqli_stmt_close($statement);
        }
        else
        {
            header("Location: login");
            exit();
        }
    mysqli_close($connection);
    }
?>
<html lang="en">
<head><title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        table {
            border-collapse: collapse;
            width: 300px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        tr {
            border-bottom: 1px solid #ddd;
        }

        td {
            padding: 10px;
            text-align: left;
        }

        input {
            width: calc(100% - 20px);
            padding: 8px;
            box-sizing: border-box;
            margin-bottom: 5px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .forgot-password,
        .register-link {
            text-align: center;
            padding: 10px;
        }

        .forgot-password a,
        .register-link a {
            color: #4caf50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <table>
        <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
            <tr>
                <td><input type="text" name="username" placeholder="Username" required></td>
            </tr>
            <tr>
                <td><input type="password" name="password" placeholder="Password" required></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="save" value="Login"></td>
            </tr>
        </form>
        <tr>
            <td colspan="2" class="forgot-password">
                <a href="forgotPassword">Forgot Password?</a>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="register-link">
                <a href="register">Register</a>
            </td>
        </tr>
    </table>
</body>
</html>
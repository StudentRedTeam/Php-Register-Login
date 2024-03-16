<?php
    $connection=mysqli_connect("localhost","root","root","random");
    if(!$connection)
        die("Connection failed" . mysqli_connect_error());

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    function sendmail($email, $user_token ,$name)
    {
        require 'PHPMailer/Exception.php';
        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';

        $mail = new PHPMailer(true);

        try 
        {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';                     
            $mail->SMTPAuth  = true;
            $mail->Username   = 'Your Email';
            $mail->Password   = 'Your Password';                               
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;               

            //Recipients
            $mail->setFrom('Email', 'Name');
            $mail->addAddress("$email");              


            //Content
            $mail->isHTML(true);                                 
            $mail->Subject = ' Password Reset Request ';
            $mail->Body    = "<html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>Password Reset Request</title><style>body{font-family:Arial,sans-serif;background-color:#f4f4f4;color:#333;margin:0;padding:0}.container{max-width:600px;margin:20px auto;padding:20px;background-color:#fff;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,.1)}.header{text-align:center;margin-bottom:20px;font-size:24px;color:#007bff;background-size:cover;background-repeat:no-repeat}.content{line-height:1.6;font-size:16px;padding:20px;border-bottom:1px solid #ddd}.content p:first-child{font-weight:700}h2{font-size:18px;margin-bottom:10px}.button-container{text-align:center}.button{display:inline-block;padding:10px 20px;text-decoration:none;background-color:#007bff;color:#fff;border-radius:5px;font-weight:700;margin-top:20px}.footer{text-align:center;font-size:12px;padding:10px;border-bottom-left-radius:5px;border-bottom-right-radius:5px}</style></head><body><div class='container'><div class='header'><h1>Password Reset Request</h1></div><div class='content'><p>Welcome<strong>&nbsp;$name</strong>,</p><p>We received a request to reset the password for your account associated with the email address<strong>&nbsp;$email</strong>.</p><h2>Click on the following link to reset your password:</h2><p class='button-container'><a class='button' href='http://localhost/Project/passwordforgot.php?email=$email&user_token=$user_token'>RESET PASSWORD</a></p><p>For your security, please remember to change your password immediately after accessing the provided link.</p></div><div class='footer'><p>If you did not request a password reset, please ignore this email. This link is only valid for a limited time.</p></div></div></body></html>";
            $mail->send();
                return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    if($_SERVER['REQUEST_METHOD']=="POST")
    {
        if(isset($_POST['save']))
        {
            $email=$_POST['email'];
                if(isset($email))
                {
                    $searchEmail="SELECT * FROM users WHERE email = ?;";
                    $searchEmailPrepare=mysqli_prepare($connection, $searchEmail);
                        if($searchEmailPrepare)
                        {
                            mysqli_stmt_bind_param($searchEmailPrepare, "s", $email);
                            mysqli_stmt_execute($searchEmailPrepare);
                            $result=mysqli_stmt_get_result($searchEmailPrepare);
                                if($result)
                                {
                                    if(mysqli_num_rows($result)>0)
                                    {
                                        $row=mysqli_fetch_assoc($result);
                                        $name=$row['fullname'];
                                        $user_token=$row['user_token'];
                                        $verifyEmailorNot=$row['is_verified'];
                                        $hashed_password=$row['password'];
                                            if($verifyEmailorNot==1 && sendmail($email, $user_token, $name))
                                            {
                                                echo "<script>alert('If Email Exist send you a reset link...'); window.location.href = 'login';</script>";
                                                exit();
                                            }
                                            else
                                            {
                                                echo "<script>alert('Email Not Verified!!'); window.location.href = 'login';</script>";
                                                exit();
                                            }
                                    }
                                    else
                                    {
                                        echo "<script>alert('If Email Exist send you a reset link...'); window.location.href = 'login';</script>";
                                        exit();
                                    }
                                }
                        }                
                }
        }
    }
    
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            width: 100%;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST" autocomplete="off">
        <input type="email" name="email" placeholder="Enter Email" required><br>
        <input type="submit" name="save" value="Submit">
    </form>
</body>

</html>

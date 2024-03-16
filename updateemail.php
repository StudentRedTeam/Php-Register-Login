<?php
    session_start();
    if(!isset($_SESSION['username']))
    {
        header("Location: login");
        exit(0); 
     }

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    function sendemail($newemail, $fullname, $v_code)
    {
        require 'PHPMailer/Exception.php';
        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';                     
            $mail->SMTPAuth  = true;
            $mail->Username   = 'Your Email';
            $mail->Password   = 'Email Password';                               
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;               

            //Recipients
            $mail->setFrom('Email', 'Name');
            $mail->addAddress("$newemail");              


            //Content
            $mail->isHTML(true);                                 
            $mail->Subject = 'Action Required: Verify Your Email for Registration';
            $mail->Body    = "<html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><style>body{font-family:Arial,sans-serif;background-color:#f4f4f4;color:#333}.container{max-width:600px;margin:20px auto;padding:20px;background-color:#fff;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,.1)}.header{text-align:center;margin-bottom:20px;font-size:24px;color:#007bff;background-size:cover;background-repeat:no-repeat}.content{line-height:1.6;font-size:16px;padding:20px;border-bottom:1px solid #ddd}.content p:first-child{font-weight:700}h2{font-size:18px;margin-bottom:5px}ul{list-style:disc;padding:0 20px;text-align:center}.button{display:inline-block;padding:10px 20px;text-decoration:none;background-color:#007bff;color:#fff;border-radius:5px;font-weight:700;margin-top:10px}.footer{text-align:center;font-size:12px;padding:10px 0}</style></head><body><div class='container'><div class='header'><h1>Verify Your Email</h1></div><div class='content'><p>Dear $fullname,</p><p>Thank you for registering with Random GUY! We're thrilled to have you join our community.</p><h2>Complete your registration:</h2><ul><a class='button' href='http://localhost/random/verify.php?email=$newemail&v_code=$v_code'>Verify Email Address</a></ul><p>This helps us ensure your account is secure and protected.</p></div><div class='footer'><p>If you did not sign up for an account, please ignore this email.</p><p>© 2023 Random GUY. All rights reserved.</p></div></div></body></html>";
            $mail->send();
                return true;
        } catch (Exception $e) {
            return false;
        }
    }

    $connection=mysqli_connect("localhost", "root", "root", "random");
    if(!$connection)
        die("Connection failed" . mysqli_connect_error());

    if($_SERVER['REQUEST_METHOD']=="POST")
    {
        if(isset($_POST['save']))
        {
            $newemail=$_POST['newemail'];
            $email = $_SESSION['email'];
            $checkEmailQuery = "SELECT * FROM Users WHERE email=?";
            $checkStatement = mysqli_prepare($connection, $checkEmailQuery);
            if($checkStatement)
            {
                mysqli_stmt_bind_param($checkStatement, "s", $email);
                mysqli_stmt_execute($checkStatement);
                mysqli_stmt_store_result($checkStatement);

                if(mysqli_stmt_num_rows($checkStatement) > 0) 
                {
                    $updateEmailQuery="UPDATE users SET email = ? WHERE email = ?;";
                    $updatePrepare=mysqli_prepare($connection, $updateEmailQuery);
                    if($updatePrepare)
                    {
                        mysqli_stmt_bind_param($updatePrepare, "ss", $newemail, $email);
                        mysqli_stmt_execute($updatePrepare);
                        $affectedRows=mysqli_stmt_affected_rows($updatePrepare);
                            if($affectedRows > 0)
                            {
                                $v_code=$_SESSION['v_code'];
                                $_SESSION['email']=$newemail;
                                $fullname=$_SESSION['fullname'];
                                $value=0;
                                $updateVerify="UPDATE users SET is_verified = ? WHERE email = ?;";
                                $updateVerifyPrepare=mysqli_prepare($connection, $updateVerify);
                                if($updateVerifyPrepare)
                                {
                                    mysqli_stmt_bind_param($updateVerifyPrepare, "ss", $value, $newemail);
                                    mysqli_stmt_execute($updateVerifyPrepare);
                                    $change=mysqli_stmt_affected_rows($updatePrepare);

                                    if($change && sendemail($newemail, $fullname, $v_code))
                                    {
                                        echo "<script>alert('Verify email Then login again. '); window.location.href = 'logout';</script>";
                                        exit();
                                    }
                                }   
                            }
                            else
                            {
                                echo "<script>alert('Email not found or no changes made.'); window.location.href = 'dashboard';</script>";
                                exit();
                            }
                    }
                    else
                    {
                        echo "<script>alert('Error in preparing the query.'); window.location.href = 'dashboard';</script>";
                        exit();
                    }
                    mysqli_stmt_close($updatePrepare);
                }
                else
                {
                    echo "<script>alert('Enter correct email!!'); window.location.href = 'dashboard';</script>";
                    exit();
                }
                mysqli_stmt_close($checkStatement);
            }
        }
    }
    mysqli_close($connection);

?>
 <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    form {
      background-color: #fff;
      max-width: 400px;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    label {
      display: block;
      margin-bottom: 8px;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 16px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type="submit"] {
      background-color: #4caf50;
      color: white;
      border: none;
      padding: 12px 16px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    input[type="submit"]:hover {
      background-color: #45a049;
    }
  </style>
  <title>Update Email</title>
</head>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" autocomplete="off">
  <label for="newemail">Enter email</label>
  <input type="email" name="newemail" id="newemail" required><br>
  <input type="submit" name="save" value="Submit">
</form>
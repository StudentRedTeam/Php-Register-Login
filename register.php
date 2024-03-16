<?php
   session_start();
   if(isset($_SESSION['username']))
   {
       header("Location: dashboard");
       exit();
   }
   $connection=mysqli_connect("localhost","root","root","random");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    function sendemail($email, $v_code, $fullname)
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
            $mail->addAddress("$email");              


            //Content
            $mail->isHTML(true);                                 
            $mail->Subject = 'Action Required: Verify Your Email for Registration';
            $mail->Body    = "<html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><style>body{font-family:Arial,sans-serif;background-color:#f4f4f4;color:#333}.container{max-width:600px;margin:20px auto;padding:20px;background-color:#fff;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,.1)}.header{text-align:center;margin-bottom:20px;font-size:24px;color:#007bff;background-size:cover;background-repeat:no-repeat}.content{line-height:1.6;font-size:16px;padding:20px;border-bottom:1px solid #ddd}.content p:first-child{font-weight:700}h2{font-size:18px;margin-bottom:5px}ul{list-style:disc;padding:0 20px;text-align:center}.button{display:inline-block;padding:10px 20px;text-decoration:none;background-color:#007bff;color:#fff;border-radius:5px;font-weight:700;margin-top:10px}.footer{text-align:center;font-size:12px;padding:10px 0}</style></head><body><div class='container'><div class='header'><h1>Verify Your Email</h1></div><div class='content'><p>Dear $fullname,</p><p>Thank you for registering with Random GUY! We're thrilled to have you join our community.</p><h2>Complete your registration:</h2><ul><a class='button' href='http://localhost/random/verify.php?email=$email&v_code=$v_code'>Verify Email Address</a></ul><p>This helps us ensure your account is secure and protected.</p></div><div class='footer'><p>If you did not sign up for an account, please ignore this email.</p><p>© 2023 Random GUY. All rights reserved.</p></div></div></body></html>";
            $mail->send();
                return true;
        } catch (Exception $e) {
            return false;
        }
    }

    if(!$connection)
        die("Connection Failed: " . mysqli_connect_error());

        if($_SERVER['REQUEST_METHOD']=="POST")
        {
            $v_code=bin2hex(random_bytes(10));
            $default=0;
            $token=md5(rand());
            if(isset($_POST['save']))
            {
                $fullname=$_POST['fullname'];
                $username=$_POST['username'];
                $email=$_POST['email'];
                $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
                if(empty(trim($fullname)) || empty(trim($username)) || empty(trim($email)) || empty($_POST['password']))
                    die("All fields are required.");

                $insertSQL="INSERT INTO users(username, fullname, email, password, verification_code, is_verified, user_token) VALUES(?,?,?,?,?,?,?);";
                $insertPrepare=mysqli_prepare($connection, $insertSQL);
                if($insertPrepare)
                {
                    mysqli_stmt_bind_param($insertPrepare, "sssssss", $username, $fullname, $email, $password, $v_code, $default, $token);
                    mysqli_stmt_execute($insertPrepare);
                    $result=mysqli_stmt_affected_rows($insertPrepare);
                    if($result)
                    {
                        $_SESSION['v_code']=$v_code;
                        if(sendemail($email, $v_code, $fullname))
                        {
                            $_SESSION['fullname']=$fullname;
                            // $_SESSION['email']=$email;
                            $_SESSION['v_code']=$v_code;
                            echo "<script>alert('Registration successfully'); window.location.href = 'login';</script>";
                            exit();
                        }
                        else
                        {
                            echo "<script>alert('Registration Failed'); window.location.href = 'register';</script>";
                            exit();
                        }
                    }
                    else
                    {
                        echo "<script>alert('Registration Failed'); window.location.href = 'register';</script>";
                        exit();
                    }
                    mysqli_stmt_close($insertPrepare);
                }
                else
                {
                    echo "<script>alert('Registration Failed!!'); window.location.href = 'register';</script>";
                    exit();
                }               
            }
        }
    

    $CheckUsername="SELECT * FROM Users WHERE username=?;";
    $usernamePrepare=mysqli_prepare($connection,$CheckUsername);
    mysqli_stmt_bind_param($usernamePrepare,"s",$username);
    mysqli_stmt_execute($usernamePrepare);
    mysqli_stmt_get_result($usernamePrepare);
    $usernameExists=mysqli_stmt_num_rows($usernamePrepare);
        if($usernameExists>0){
            echo "<script>alert('Username Already Exists...'); window.location.href = 'register';</script>";
            exit();
        }
    mysqli_stmt_close($usernamePrepare);
       
    $CheckEmail="SELECT * FROM Users WHERE email=?;";
    $emailPrepare=mysqli_prepare($connection,$CheckEmail);
    mysqli_stmt_bind_param($emailPrepare,"s",$email);
    mysqli_stmt_execute($emailPrepare);
    mysqli_stmt_store_result($emailPrepare);
    $emailExists = mysqli_stmt_num_rows($emailPrepare);
        if($emailExists>0){
            echo "<script>alert('Email Already Exists...'); window.location.href = 'register';</script>";
            exit();
        }
    mysqli_stmt_close($emailPrepare);

    mysqli_close($connection);

?>
<html lang="en">
<head><title>Register</title>
<link href="css/css_register.css" rel="stylesheet">
</head>
<body>
    <table>
        <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST" id="registrationForm" onsubmit="return validateForm()">
            <tr>
                <td><input type="text" name="fullname" id="fullname" placeholder="Full name" onblur="checkFullName()" required></td>
            </tr>
            <tr>
                <td><input type="text" name="username" id="username" placeholder="Username" onblur="checkUsername()" required></td>
                <td><span id="usernameError"></span></td>
            </tr>
            <tr>
                <td><input type="email" name="email" id="email" placeholder="Email" onblur="checkEmail()" required></td>
                <td><span id="emailError"></span></td>
            </tr>
            <tr>
                <td><input type="password" name="password" id="password" placeholder="Password" onblur="checkPassword()" required></td>
                <td><span id="passwordError"></span></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="save" value="Register"></td>
            </tr>
        </form>
        <tr>
            <td colspan="2" class="register">
                <a href="login">Already registered?</a>
            </td>
        </tr>
        </table>
    <script>
        function checkUsername()
        {
            var username = document.getElementById('username').value;
            var usernameError = document.getElementById('usernameError');

            usernameError.innerHTML = '';

            if (username.trim() === '') {
                usernameError.innerHTML = 'Username cannot be empty';
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_username.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText.trim() !== '') {
                        usernameError.innerHTML = xhr.responseText;
                    }
                }
            };
            xhr.send('username=' + encodeURIComponent(username));
        }


        function checkEmail()
        {
            var email = document.getElementById('email').value;
            var emailError = document.getElementById('emailError');

            emailError.innerHTML = '';

            if (email.trim() === '') {
                emailError.innerHTML = 'Email cannot be empty';
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_email.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText.trim() !== '') {
                        emailError.innerHTML = xhr.responseText;
                    }
                }
            };
            xhr.send('email=' + encodeURIComponent(email));
        }

        function validateForm()
        {
            var usernameError = document.getElementById('usernameError').innerHTML;
            var emailError = document.getElementById('emailError').innerHTML;
            var passwordError = document.getElementById('passwordError').innerHTML;

            if (usernameError || emailError || passwordError) {
                alert('Please fix the errors before submitting the form.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
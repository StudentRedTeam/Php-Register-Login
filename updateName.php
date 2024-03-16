<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login");
        exit();
    }
    $connection=mysqli_connect("localhost","root","root","random");
    if(!$connection) {
        die("Connection failed" . mysqli_connect_error());
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $name = $_POST['newName'];
        if(empty($name)){
            echo "<script>alert('Empty Field!!'); window.location.href = 'updateName';</script>";
            exit();
        } 
        else{
            $session_name = $_SESSION['fullname'];
            if($session_name===$name) {
                echo "<script>alert('Same Name not allowed!!'); window.location.href = 'updateName';</script>";
                exit();
            }

            $CheckFullname = "SELECT * FROM Users WHERE fullname=?;";
            $fullnamePrepare = mysqli_prepare($connection, $CheckFullname);
            if($fullnamePrepare) {
                mysqli_stmt_bind_param($fullnamePrepare, "s", $session_name);
                mysqli_stmt_execute($fullnamePrepare);
                mysqli_stmt_store_result($fullnamePrepare);
                $fullnameExists = mysqli_stmt_num_rows($fullnamePrepare);

                $session_username = $_SESSION['username'];

                if($fullnameExists > 0){
                    $sql = "UPDATE Users SET fullname = ? WHERE username = ?;";
                    $update_query_prepare = mysqli_prepare($connection, $sql);

                    if($update_query_prepare){
                        mysqli_stmt_bind_param($update_query_prepare, "ss", $name, $session_username);
                        mysqli_stmt_execute($update_query_prepare);
                        $affected_rows = mysqli_stmt_affected_rows($update_query_prepare);

                        if($affected_rows > 0){
                            $_SESSION['fullname'] = $name;
                            echo "<script>alert('Name Changed!!'); window.location.href = 'dashboard';</script>";
                            exit();
                        }
                        else{
                            echo "<script>alert('Failed to Update!!'); window.location.href = 'updateName';</script>";
                            exit();
                        }
                        mysqli_stmt_close($update_query_prepare);
                    }
                    else{
                        echo "<script>window.location.href = 'updateName';</script>";
                        exit();
                    }
                }
            }
            else{
                exit();
            }
            mysqli_stmt_close($fullnamePrepare);
        }
    }
    mysqli_close($connection);
?>


<!DOCTYPE html>
<html lang="en">
<head><title>UpdateName</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
    <?php
        $currentFullName = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : '';
        echo "<input type='text' name='newName' placeholder='Current Name: $currentFullName'><br>";
        ?>
        <input type="submit" value="Update" name="save">
    </form>
</body>
</html>


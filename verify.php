<?php
	
	$connection=mysqli_connect("localhost","root","root","random");
	if(!$connection)
		die("Connection failed" . mysqli_connect_error());

	if(isset($_GET['email']) && isset($_GET['v_code']))
	{
		$email=$_GET['email'];
		$v_code=$_GET['v_code'];
		$checkingUserQuery="SELECT * from Users WHERE email = ? AND verification_code = ?;";
		$checkingUserPrepare=mysqli_prepare($connection, $checkingUserQuery);
		if($checkingUserPrepare)
		{
			mysqli_stmt_bind_param($checkingUserPrepare, "ss", $email, $v_code);
			mysqli_stmt_execute($checkingUserPrepare);
			$result=mysqli_stmt_get_result($checkingUserPrepare);
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_assoc($result);
				if($row['is_verified']==0)
				{
					$value=1;
					$updateQuery="UPDATE Users SET is_verified = ? WHERE email = ?;";
					$updateQueryPrepare=mysqli_prepare($connection, $updateQuery);
					if($updateQueryPrepare)
					{
						mysqli_stmt_bind_param($updateQueryPrepare, "ss", $value, $email);
						mysqli_stmt_execute($updateQueryPrepare);
						$result=mysqli_stmt_affected_rows($updateQueryPrepare);
						if($result == 1)
						{
							echo "<script>alert('Email Verified'); window.location.href = 'login';</script>";
	                        exit();
						}
						else
						{
							echo "<script>alert('Cannot run query!!'); window.location.href = 'login';</script>";
	                    	exit();
						}
					}
					else
					{
						echo "<script>alert('Cannot run query!!'); window.location.href = 'login';</script>";
	                	exit();
					}
					mysqli_stmt_close($updateQueryPrepare);
				}
				else
				{
					echo "<script>alert('Email already Verified'); window.location.href = 'login';</script>";
	            	exit();
				}
			}
			else
			{
				echo "<script>alert('Server is Down'); window.location.href = 'login';</script>";
				exit();
			}	
		}
		else
		{
			echo "<script>alert('Server is Down'); window.location.href = 'login';</script>";
			exit();
		}
	}
?>
password-reset.php

<?php
session_start();
include('../conn.php');
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forgot_password</title>
    <link href="../css/style.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <style>
        body{
            background-color: #0a0933;
        }
        </style>
</head>
<body>
<a class="navbar-brand" href="../index.php">
            <img src="../images/logo.png" alt="" />
            <span> TaskMasters Hub</span></a>
<div class = "py-5">
    <div class="container">
        <div class = "row justify-content-center">
            <div class ="col-md-6">


            <div class="card">
                <div class="card-header">
                    <h5> change password</h5>
                </div>
                <div class = "card-body p-4">
                    <form action ="" method="POST">
                        <input type="hidden" name="password_token" value="<?php if(isset($_GET['token'])){echo $_GET['token'];}?>">
                        <div class="form-group mb-3">
                            <label> Email Address</label>
                            <input type="email" name="email" value = "<?php if(isset($_GET['email'])){echo $_GET['email'];}?>"class ="form-control" placeholder="Enter email address">

                        </div>
                        <div class = "form-group mb-3">
                        <label> New password</label>
                            <input type="text" name="new_password" class ="form-control" placeholder="Enter new password">
                        </div>
                        <div class ="form-group mb-3">
                        <label> Confirm password</label>
                            <input type="text" name="confirm_password" class ="form-control" placeholder="Confirm password">
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" name="password_update" class="btn btn-success w-100">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php

if(isset($_POST['password_update']))
{
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $new_password = mysqli_real_escape_string($conn,$_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn,$_POST['confirm_password']);
    $token = mysqli_real_escape_string($conn,$_POST['password_token']);
    if(!empty($token))
    {
        if(!empty($email) && !empty( $new_password) && !empty( $confirm_password))
        {
            $check_token = "SELECT verify_token FROM client WHERE  verify_token='$token' LIMIT 1";
            $check_token_run = mysqli_query($conn,$check_token);
            if(mysqli_num_rows($check_token_run) > 0)
            {
                if($new_password == $confirm_password)
                {
                    $hashed_password = md5($new_password);
                    $update_password = "UPDATE client SET password='$hashed_password' WHERE verify_token ='$token' LIMIT 1";
                    $update_password_run = mysqli_query($conn,$update_password);
                    if($update_password_run)
                    {
                        $new_token = md5(rand());
                        $update_to_new_token = "UPDATE client SET verify_token ='$new_token' WHERE verify_token ='$token' LIMIT 1";
                        $update_to_new_token_run = mysqli_query($conn, $update_to_new_token);
                        echo "<script>alert('Password updated successfully');
                        window.location = '../login_client.php';</script>";
                        exit(0);
                    }
                    else
                    {
                        echo "<script>alert('Something went wrong ...This action cannot be performed');</script>";
                      //  window.location = 'password-change.php?token=$token&email=$email';</script>";
                      header('Location: ../login_client.php');
                        exit(0);
                    }
                }
                else
                {
                    echo "<script>alert('Password and confirm password doesn't match');</script>";
                   // window.location = 'password-change.php?token=$token&email=$email';</script>";
                   header('Location: password-reset.php');
                    exit(0);
                }
            }
        }
        else{
            echo "<script>alert('All fields are mandatory');</script>";
        //window.location = 'password-change.php?token=$token&email=$email';</script>";
        header('Location: password-reset.php');
        exit(0);
        }
    }
    else
    {
        echo "<script>alert('No token available');</script>";
        //window.location = 'password-change.php';</script>";
        header('Location: password-reset.php');
        exit(0);
    }
}
?>
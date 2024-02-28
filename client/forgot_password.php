

<?php
session_start();
include('../conn.php');
?>
<!DOCTYPE html>

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
    /* Additional CSS styles */
    .card {
        max-width: 400px;
        margin: 0 auto;
        /* Center the card horizontally */
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .btn-primary {
        width: 100%;
    }
    </style>
</head>
<body> 
    <a class="navbar-brand" href="../index.php">
            <img src="../images/logo.png" alt="" />
            <span> TaskMasters Hub</span></a>

<div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Forgot Password</h5>
                            <form class="needs-validation" method="post"  onsubmit="return validateForm()"
                                novalidate>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter email address"
                                        oninput="validateEmail(this.value)" required>
                                    <div id="email-warning" class="invalid-feedback"></div>
                                    <div id="email-error"></div>
                                </div>
                               
                                <div class="form-group">
                                    <button type="submit" name="password_reset_link" class="btn btn-primary">Send password Reset link</button>
                                    <a href="../login_client.php">Go Back to Login Page </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
</div>
 
</body></html>


<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
function  send_password_reset($get_email,$token)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'taskmasterhub2023@gmail.com'; // Your Gmail email address
        $mail->Password = 'tgzpeyjxxpjafund'; // Your Gmail password or App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('taskmasterhub2023@gmail.com', 'TaskMaster Hub');
        $mail->addAddress($get_email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset password notification';
        $email_template = "
            <h2>Forgot ypur password?</h2>
            
            <h5>You are receiving this email because we received a password reset request for your account</h5>
            <br><br>
            <a href='http://localhost/Taskmaster/client/password-reset.php?token=$token&email=$get_email'>Click me</a>";
        $mail->Body = $email_template;
        $mail->send();
     
        return true;
    } catch (Exception $e) {
        return false;
    }
}


if(isset($_POST['password_reset_link']))
{
    
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $token = md5(rand());
    $check_email = "SELECT email FROM client WHERE email='$email' LIMIT 1";
    $check_email_run = mysqli_query($conn,$check_email);
    if(mysqli_num_rows($check_email_run) > 0)
    {
       
        $row = mysqli_fetch_array($check_email_run);
       // $get_name = $row['name'];
        $get_email = $row['email'];
        $update_token = "UPDATE client SET verify_token = '$token' WHERE email='$get_email' LIMIT 1";
        $update_token_run =  mysqli_query($conn,$update_token);
        if($update_token_run)
        {
            send_password_reset($get_email,$token);
            echo "<script>alert('Password reset link is mailed');
            window.location = 'forgot_password.php';</script>";

        }
        else
        {
            echo "<script>alert('No email found');
            window.location = 'forgot_password.php';</script>";
        }
    }
    else
    {
        echo "<script>alert('Error');
        window.location = 'forgot_password.php';</script>";
    }

}


?>
<?php
session_start();
include('../conn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

// Function to send a welcome email
function sendWelcomeEmail($recipientEmail, $recipientName, $verifyToken) {
    $mail = new PHPMailer(true);

    
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Update with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'taskmasterhub2023@gmail.com'; // Update with your email
        $mail->Password = 'tgzpeyjxxpjafund'; // Update with your email password
        $mail->SMTPSecure = 'tls'; // Use TLS
        $mail->Port = 587; // Port for TLS

        // Recipients
        $mail->setFrom('taskmasterhub2023@gmail.com', 'TaskMaster Hub'); // Update with your email and name
        $mail->addAddress($recipientEmail, $recipientName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Our Website';
        $mail->Body = 'Hello ' . $recipientName . ',<br>Welcome to our website!<br><a href="http://localhost/Taskmaster/client/verify.php?token='. $verifyToken . '">Click here to verify your email</a>';

        $mail->send();
        return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $cname = $_POST['cname'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Generate a verification token (you can use any method you prefer)
    $verifyToken = md5(uniqid(rand(), true));

    // Perform basic server-side validation
    if (empty($username) || empty($cname) || empty($contact) || empty($email) || empty($password)) {
        echo "Please fill out all the fields.";
    } else if (!preg_match("/^\d{10}$/", $contact)) {
        echo "Contact number must be exactly 10 digits.";
    } else {
        // Hash the password using MD5 (not recommended for production)
        $hashed_password = md5($password);

        // SQL query to insert user into the database with verify_token and verify_status
        $sql = "INSERT INTO client (username, cname, contact, email, password, verify_token)
                VALUES ('$username', '$cname', '$contact', '$email', '$hashed_password', '$verifyToken')";

        if ($conn->query($sql) === TRUE) {
            $registrationSuccessful = true; // Registration was successful

            // Send a welcome email with the verification link
            if (sendWelcomeEmail($email, $cname, $verifyToken)) {
                // Registration successful. Welcome email sent!
                $_SESSION['status'] = 'Registration successful. Welcome email sent!';
            } else {
                // Registration successful, but there was an issue sending the welcome email.
                $_SESSION['status'] = 'Registration successful, but there was an issue sending the welcome email.';
            }

            header('Location: ../login_client.php');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Design by foolishdeveloper.com -->
    <title>Signup Page</title>
 
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet" />
    <!--Stylesheet-->
    <style media="screen">
      *,
*:before,
*:after{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}
body{
    background-color: #1a2e35;
}
.background{
    width: 430px;
    height: 520px;
    position: absolute;
    transform: translate(-50%,-50%);
    left: 50%;
    top: 50%;
}
.background .shape{
    height: 200px;
    width: 200px;
    position: absolute;
    border-radius: 50%;
}
.shape:first-child{
    background: linear-gradient(
        #826d19,
        #55104ceb
    );
    left: -80px;
    top: -80px;
}
.shape:last-child{
    
    right: -30px;
    bottom: -80px;
}
form{
    height: 630px;
    width: 450px;
    position: absolute;
    transform: translate(-50%,-50%);
    top: 50%;
    left: 50%;
    padding: 50px 35px;
}
form *{
    font-family: 'Poppins',sans-serif;
    color: #ffffff;
    letter-spacing: 0.5px;
    outline: none;
    border: none;
}
form h3{
    font-size: 32px;
    font-weight: 500;
    line-height: 42px;
    text-align: center;
}

label{
    display: block;
    margin-top: 30px;
    font-size: 16px;
    font-weight: 500;
}
input{
    display: block;
    height: 40px;
    width: 100%;
    background-color: rgba(255,255,255,0.07);
    border-radius: 3px;
    padding: 0 10px;
    margin-top: 2px;
    font-size: 14px;
    font-weight: 300;
}
#role{
    display: block;
    height: 40px;
    width: 100%;
    background-color: rgba(255,255,255,0.07);
    border-radius: 3px;
    padding: 0 10px;
    margin-top: 2px;
    font-size: 14px;
    font-weight: 300;
}
option{
    display: block;
    height: 40px;
    width: 100%;
    background-color: rgba(255,255,255,0.07);
    border-radius: 3px;
    padding: 0 10px;
    margin-top: 2px;
    font-size: 14px;
    font-weight: 300;
}
::placeholder{
    color: #e5e5e5;
}
button{
    margin-top: 14px;
    width: 100%;
    background-color: #ffffff;
    color: #080710;
    padding: 15px 0;
    font-size: 18px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
}
.social{
  margin-top: 30px;
  display: flex;
}
.social div{
  background: red;
  width: 150px;
  border-radius: 3px;
  padding: 5px 10px 10px 5px;
  background-color: rgba(255,255,255,0.27);
  color: #eaf0fb;
  text-align: center;
}
.social div:hover{
  background-color: rgba(255,255,255,0.47);
}
.social .fb{
  margin-left: 25px;
}
.social i{
  margin-right: 4px;
}
.error {
    color: red;
}

</style>
</head>
<body>
    <a class="navbar-brand" href="../index.php">
            <img src="../images/logo.png" alt="" />
            <span> TaskMasters Hub</span></a>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
        <a href="index.php" >Index Page</a>
    </div>
<body>
    <form action="" method="POST" onsubmit="return validateForm()">
        <h3>Signup Here</h3>

        <label for="username">Username</label>
        <input type="text" placeholder="Username" name="username" id="username" autocomplete="off">
        <span id="usernameError" class="error"></span>

        <label for="cname">Name</label>
        <input type="text" placeholder="Name" name="cname" id="cname" autocomplete="off">
        <span id="cnameError" class="error"></span>

        <label for="contact">Contact</label>
        <input type="text" placeholder="Contact" name="contact" id="contact" autocomplete="off">
        <span id="contactError" class="error"></span>

        <label for="email">Email</label>
        <input type="email" placeholder="Email" name="email" id="email" autocomplete="off">
        <span id="emailError" class="error"></span>

        <label for="password1">Password</label>
        <input type="password" placeholder="Password" id="password" name="password" autocomplete="off">
        <span id="passwordError" class="error"></span>

        <button type="submit">Signup</button><br><br>
        <a href="../login_client.php">I already have an account</a>
    </form>

    <script>
        const usernameInput = document.getElementById('username');
        const cnameInput = document.getElementById('cname');
        const contactInput = document.getElementById('contact');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        usernameInput.addEventListener('input', validateUsername);
        cnameInput.addEventListener('input', validateName);
        contactInput.addEventListener('input', validateContact);
        emailInput.addEventListener('input', validateEmail);
        passwordInput.addEventListener('input', validatePassword);

        function validateUsername() {
    const username = usernameInput.value.trim();
    const usernameError = document.getElementById('usernameError');
    if (username === '') {
        usernameError.textContent = 'Please enter a username.';
    } else if (username.length < 4) {
        usernameError.textContent = 'Username must be at least 4 characters long.';
    } else if (/\d/.test(username)) {
        usernameError.textContent = 'Username cannot contain numbers.';
    } else if (/\s/.test(username)) {
        usernameError.textContent = 'Username cannot contain spaces.';
    } else {
        usernameError.textContent = '';
    }
}

function validateName() {
    const cname = cnameInput.value.trim();
    const cnameError = document.getElementById('cnameError');

    // Check for leading or trailing spaces
    if (cname !== cnameInput.value) {
        cnameError.textContent = 'Name cannot have leading or trailing spaces.';
    } else {
        const words = cname.split(' ');

        // Check if each part is at least 4 characters long, doesn't contain numbers, and doesn't exceed 20 characters
        const invalidPart = words.find(part => part.length < 1 || /\d/.test(part) || part.length > 20);

        if (cname === '') {
            cnameError.textContent = 'Please enter a name.';
        } else if (invalidPart) {
            cnameError.textContent = 'Each word must be at least 4 characters long, not contain numbers, and not exceed 20 characters.';
        } else {
            cnameError.textContent = '';
        }
    }
}


function validateContact() {
    const contact = contactInput.value.trim();
    const contactError = document.getElementById('contactError');

    // Check if the input is empty
    if (contact === '') {
        contactError.textContent = 'Please enter a contact.';
    }
    // Check if the input contains non-numeric characters
    else if (!/^\d+$/.test(contact)) {
        contactError.textContent = 'Contact must contain only numbers.';
    }
    // Check if the input has exactly 10 digits
    else if (contact.length !== 10) {
        contactError.textContent = 'Contact must have exactly 10 digits.';
    } else {
        contactError.textContent = '';
    }
}

        function validateEmail() {
            const email = emailInput.value.trim();
            const emailError = document.getElementById('emailError');
            // This is a simple email validation using a regular expression
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailError.textContent = 'Please enter a valid email.';
            } else {
                emailError.textContent = '';
            }
        }

        function validatePassword() {
            const password = passwordInput.value.trim();
            const passwordError = document.getElementById('passwordError');
            if (password === '') {
                passwordError.textContent = 'Please enter a password.';
            } else if (password.length < 6) {
                passwordError.textContent = 'Password must be at least 6 characters long.';
            } else if (!/\d/.test(password)) {
                passwordError.textContent = 'Password must contain at least one number.';
            } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                passwordError.textContent = 'Password must contain at least one special character.';
            } else if (!/[A-Z]/.test(password)) {
                passwordError.textContent = 'Password must contain at least one uppercase letter.';
            } else {
                passwordError.textContent = '';
            }
        }

        function validateForm() {
            // Validate all fields when the form is submitted
            validateUsername();
            validateName();
            validateContact();
            validateEmail();
            validatePassword();

            // Check if any error messages are present
            const errorMessages = document.querySelectorAll('.error');
            for (const errorMessage of errorMessages) {
                if (errorMessage.textContent !== '') {
                    alert('Please fix the errors before submitting the form.');
                    return false;
                }
            }

            return true;
        }
    </script>

</body>
</html>

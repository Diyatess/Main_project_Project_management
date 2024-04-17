<?php
session_start();
include('../conn.php');

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    header('Location: client_dashboard.php');
    exit;
}

// Fetch user data from the database using the user's email
$sql = "SELECT cname, contact, username FROM client WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cname = $row['cname'];
    $contact = $row['contact'];
    $username = $row['username'];
} else {
    // Handle the case when the user's data is not found
    echo "User data not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update all the fields
    $newCName = $_POST['cname'];
    $newContact = $_POST['contact'];
    $newUsername = $_POST['username'];

    // Update the user's profile in the database
    $updateSql = "UPDATE client SET cname = '$newCName', contact = '$newContact', username = '$newUsername' WHERE email = '$email'";

    if ($conn->query($updateSql) === TRUE) {
        // Update successful
        $cname = $newCName; // Update the variable with the new name
        $contact = $newContact; // Update the variable with the new contact
        $username = $newUsername; // Update the variable with the new username
        echo '<script>alert("Profile updated successfully!");</script>'; // Display a success alert
        echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';
    } else {
        // Handle the case where the update query fails
        echo "Error updating profile: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-container {
            width: 50%;
            margin: 0 auto;
            padding: 20px 40px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .profile-container h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .profile-details {
            margin: 10px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .profile-details label {
            font-weight: bold;
        }

        .profile-details input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }
     
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="profile-container">
        <h2>Edit Profile:</h2>
        <form method="POST" onsubmit="return validateForm()">
            <div class="profile-details">
                <label for="cname">Name</label>
                <input type="text" placeholder="Name" name="cname" id="cname" value="<?php echo $cname; ?>" autocomplete="off">
                <span id="cnameError" class="error"></span>
            </div>
            <div class="profile-details">
                <label for="contact">Contact</label>
                <input type="text" placeholder="Contact" name="contact" id="contact" value="<?php echo $contact; ?>" autocomplete="off">
                <span id="contactError" class="error"></span>
            </div>
            <div class="profile-details">
            <label for="username">Username</label>
            <input type="text" placeholder="Username" name="username" id="username" value="<?php echo $username; ?>" autocomplete="off">
            <span id="usernameError" class="error"></span>
            </div>

            <div class="button-container">
                <button type="submit" class="edit-button">Save Changes</button>
                <a href="client_dashboard.php?email=<?php echo $email; ?>" style="float: right;">
                    <button class="edit-button" type="button">Back</button>
                </a>
            </div>
        </form>
    </div>
</div>

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


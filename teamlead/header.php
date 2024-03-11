<!-- header.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Lead Profile</title>
    <style>
   body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        /* Add or modify styles as needed */
        .container {
            margin:33px;
            justify-content: space-between;
            padding: 20px;
        }
        .main-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 76px;
            left: 0;
            background-color: #333;
            overflow-x: hidden;
            transition: 0.5s;
            text-align: left;
            padding-top: 60px;
            color: #fff;
        }

        .sidebar a {
            padding: 8px 16px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: 0.3s;
            margin: 15px 0;
        }

        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
        }

        h1 {
            font-size: 32px;
            color: #fff;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            padding: 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Style the sidebar */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 76px;
            left: -250px;
            background-color: #333;
            overflow-x: hidden;
            transition: 0.5s;
            text-align: left;
            padding-top: 60px;
            color: #fff;
        }

        .sidebar a {
            padding: 8px 16px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: 0.3s;
            margin: 15px 0;
        }

        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
        }

        .openbtn {
            font-size: 30px;
            cursor: pointer;
            position: fixed;
            z-index: 1;
            top: 10px;
            left: 10px;
            color: #00d2fc;
        }

        .icon {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Add a background color for the links */
        .sidebar a {
            background-color: #333;
        }

        /* On hover, the background color and text color change */
        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
        }

        a {
            text-decoration: none;
            color: #0074D9;
        }

        a.navbar-brand {
            color: black; /* Set the text color to black */
            text-decoration: none; /* Remove the underline */
            font-weight: bold;
            color: #fff;
            font-size: 24px;
            margin-left: 45px;
            padding: 0px;

        }

        img {
            width: 39px;
            height: 39px;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 24px;
        }
    </style>
    <script>

        // Logout function
        function logout() {
            // Clear the session or perform any other necessary logout tasks
            // Disable the ability to go back
            history.pushState(null, null, window.location.href);
            window.onpopstate = function (event) {
                history.go(1);
            };

            // Redirect to the login page
            window.location.replace("../login_client.php");
        }

        // Disable caching to prevent back button from showing the logged-in page
        window.onload = function () {
            window.history.forward();
            document.onkeydown = function (e) {
                if (e.keyCode === 9) {
                    return false;
                }
            };
        }

        // Redirect to the login page if the user tries to go back
        window.addEventListener('popstate', function (event) {
            window.location.replace("../login_client.php");
        });
    </script>
</head>
<body>
    <a class="navbar-brand" href="../index.php" style="float: left;">
        <img src="../images/logo.png" alt="" />
        <span> TaskMasters Hub</span>
    </a>
    
    
</body>
</html>

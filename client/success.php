<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Success</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="container mt-3">
        <h2>Payment has been successful</h2>

        <div class="alert alert-success">
            <strong>Please note your payment id!</strong><?php echo $_SESSION['payment_id'];?>
        </div>

        <?php
        // Get the email from the URL
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        //echo "<p>Email: $email</p>";
        ?>

    </div>

    <script>
        // Redirect to the client dashboard after 5 seconds
        setTimeout(function(){
            window.location.href = 'client_dashboard.php?email=<?php echo $email; ?>';
        }, 3000); // 3000 milliseconds = 3 seconds
    </script>

</body>
</html>

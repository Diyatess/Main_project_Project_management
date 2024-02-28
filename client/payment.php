<?php
include('../conn.php');
if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payment</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
/*main */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            text-align: center;
           

        }
        .dashboard-container {
            margin:-7px;
            
            padding-left: 388px;
        }

        .dashboard-box {
            max-width: 600px; /* Adjust the width as needed */
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 32px;
            color: #fff;
            text-align: center; /* Center the heading text */
        }
        a {
            text-decoration: none;
            color: #0074D9;
        }
        a.navbar-brand {
            color: black;  /* Set the text color to black */
            text-decoration: none;  /* Remove the underline */
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
    </style>
<body>
<header>
        <a class="navbar-brand" href="client_dashboard.php?email=<?php echo $email; ?>" style="float: right;">
            <span>Back</span>
        </a>
        <a class="navbar-brand" href="../index.php" style="float: left;">
                <img src="../images/logo.png" alt="" />
                <span> TaskMasters Hub</span>
            </a>
            <h2 style="color: #fff;padding: 26px;">Payment</h2>
    </header>
<div id="mySidebar" class="sidebar">
        <a href="client_dashboard.php?email=<?php echo $email; ?>">Dashboard</a>
        <a href="view_messages.php?email=<?php echo $email; ?>" class="red-dot">View Message</a>
        <a href="sugg.html">Suggestions/Add-ons</a>

        <a href="client_update.php?email=<?php echo $email; ?>">Update Profile</a>
        <a href="add_project.php?email=<?php echo $email; ?>">Add Project</a>
        <a href="payment.php?email=<?php echo $email; ?>">Make Payment</a>
        
    </div>

    <div class="openbtn" onclick="toggleSidebar()">&#9776;</div>

    <div id="container" class="dashboard-container">
    <div class="dashboard-box">
            <form id="paymentForm">
                <div class="mb-3">
                    <label for="amount" class="form-label">Payment Amount</label>
                    <input type="text" class="form-control" id="amount" name="amount" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" class="form-control" id="description" name="description" required>
                </div>

                <?php
                // Get the client email from the URL
                $clientEmail = $_GET['email'];
                ?>

                <input type="hidden" id="clientEmail" value="<?php echo $clientEmail; ?>">

                <button type="button" class="btn btn-primary buynow">Pay Now</button>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $(".buynow").click(function () {
        var amount = $("#amount").val();
        var description = $("#description").val();
        var clientEmail = $("#clientEmail").val();

        var options = {
            key: 'rzp_test_EwyzGhRmw2NUjI', // Replace with your actual Razorpay key
            amount: amount * 100,
            currency: 'INR',
            name: 'TaskMaster Hub',
            description: description,
            handler: function (response) {
                var paymentid = response.razorpay_payment_id;

                $.ajax({
                    url: "payment-process.php?email=" + clientEmail, // Fix: Use "email" as the parameter
                    type: "POST",
                    data: { payment_id: paymentid, amount: amount, description: description },
                    success: function (finalresponse) {
                        if (finalresponse == 'done') {
                            window.location.href = "success.php";
                        } else {
                            alert('Please check console.log to find error');
                            console.log(finalresponse);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log("Error:", xhr.responseText);
                    }
                });
            },
            theme: {
                color: "#3399cc"
            }
        };

        var rzp1 = new Razorpay(options);
        rzp1.open();
    });

    let sidebarOpen = false;

function toggleSidebar() {
    const sidebar = document.getElementById("mySidebar");
    if (sidebarOpen) {
        sidebar.style.left = "-250px";
    } else {
        sidebar.style.left = "0";
    }
    sidebarOpen = !sidebarOpen;
}

</script>

</body>

</html>

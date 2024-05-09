

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Bill Management</title>
</head>
<style>
    /* Style for the centered box */
    .center-box {
        width: 400px; /* Adjust the width as needed */
        margin: 0 auto; /* This centers the box horizontally */
        padding: 20px;
        background-color: #f0f0f0; /* Background color */
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow for depth */
    }

    /* Style for the heading inside the centered box */
    .center-box h2 {
        text-align: center;
        color: #333; /* Heading color */
    }

    /* Style for form elements */
    .center-box form label {
        display: block;
        margin-bottom: 10px;
        color: #555; /* Label color */
    }

    .center-box form input[type="text"],
    .center-box form input[type="number"],
    .center-box form select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc; /* Input border color */
        border-radius: 5px;
        box-sizing: border-box;
    }

    .center-box form input[type="submit"] {
        width: 100%;
        padding: 10px;
        background-color: #007bff; /* Submit button background color */
        color: #fff; /* Submit button text color */
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .center-box form input[type="submit"]:hover {
        background-color: #0056b3; /* Hover color for submit button */
    }
    .content{
        padding-left:36%;
    }
</style>
<body>
<div class="content">
        <?php
        session_start();
        if (isset($_SESSION['sname'])) {
            echo "<h2>WELCOME " . $_SESSION['sname'] . "</h2>";
        } else {
            echo "<p>Session data not found.</p>";
        }
        ?>
    </div>
    <div class="center-box">
    <h2>Supervisor Bill Management</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="tenantId">Tenant ID (or leave blank for all tenants):</label>
        <input type="text" id="tenantId" name="tenantId"><br><br>

        <label for="billType">Select Bill Type:</label>
        <select id="billType" name="billType" required>
            <option value="water">Water Bill</option>
            <option value="electricity">Electricity Bill</option>
            <option value="rent">Rent Amount</option>
            <option value="maintenance">Maintenance fees</option>
        </select><br><br>

        <label for="newAmount">New Amount (in Rs.):</label>
        <input type="number" id="newAmount" name="newAmount" min="0" required><br><br>

        <label for="paymentStatus">Payment Status:</label>
        <select id="paymentStatus" name="paymentStatus" required>
            <option value="paid">Paid</option>
            <option value="unpaid">Unpaid</option>
        </select><br><br>

        <input type="submit" name="submit" value="Update Bill">
    </form>
</div>
    <?php
    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tenantId = isset($_POST['tenantId']) ? $_POST['tenantId'] : null; // Tenant ID (null for all tenants)
        $billType = $_POST['billType']; // Bill type (water, electricity, rent)
        $newAmount = $_POST['newAmount']; // New amount to update
        $paymentStatus = ($_POST['paymentStatus'] == 'paid') ? 1 : 0; // Convert paymentStatus to numeric (1 or 0)

        // Database connection parameters
        $dbhost = 'localhost';
        $dbname = 'postgres';
        $dbuser = 'postgres';
        $dbpass = 'Keerthi23';

        // Create a database connection
        $conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
        if (!$conn) {
            die("Connection failed. Error: " . pg_last_error());
        }

        // Check if the tenant exists in the tenants table
        $tenantExistsQuery = "SELECT * FROM tenants WHERE tid = $1";
        $tenantExistsResult = pg_query_params($conn, $tenantExistsQuery, array($tenantId));
        $tenantExistsRowCount = pg_num_rows($tenantExistsResult);

        if ($tenantExistsRowCount > 0) {
            // Construct the update or insert query based on the selected bill type
            switch ($billType) {
                case 'water':
                case 'electricity':
                    $tableName = ($billType === 'water') ? 'water_bills' : 'electricity_bills';
                    // Check if record exists
                    $query = "SELECT * FROM $tableName WHERE tenant_id = $1";
                    $result = pg_query_params($conn, $query, array($tenantId));
                    $rowCount = pg_num_rows($result);

                    if ($rowCount > 0) {
                        // Update existing record
                        $query = "UPDATE $tableName SET amount = $1, payment_status = $2 WHERE tenant_id = $3";
                    } else {
                        // Insert new record
                        $query = "INSERT INTO $tableName (tenant_id, amount, payment_status) VALUES ($3, $1, $2)";
                    }
                    break;

                case 'rent':
                    $tableName = 'tenants';
                    // Update existing record
                    $query = "UPDATE $tableName SET rent_amt = $1, payment_status = $2 WHERE tid = $3";
                    break;

                case 'maintenance':
                    $tableName = 'maintenance_fees';
                                    // Check if record exists
                $query = "SELECT * FROM $tableName WHERE tenant_id = $1";
                $result = pg_query_params($conn, $query, array($tenantId));
                $rowCount = pg_num_rows($result);

                if ($rowCount > 0) {
                    // Update existing record
                    $query = "UPDATE $tableName SET amount = $1, payment_status = $2 WHERE tenant_id = $3";
                } else {
                    // Insert new record
                    $query = "INSERT INTO $tableName (tenant_id, amount, payment_status) VALUES ($3, $1, $2)";
                }
                break;

            default:
                echo "Invalid bill type.";
                exit;
        }

        // Execute the query with parameters
        $result = pg_query_params($conn, $query, array($newAmount, $paymentStatus, $tenantId));

        if ($result) {
            // Use JavaScript to show an alert message
            echo "<script>alert('Bill updated successfully.');</script>";
        } else {
            // Use JavaScript to show an alert message for an error
            echo "<script>alert('Failed to update bill: " . pg_last_error($conn) . "');</script>";
        }
    } else {
        // Tenant does not exist, show an alert message
        echo "<script>alert('Tenant not exists.');</script>";
    }

    // Close database connection
    pg_close($conn);
}
?>
</body>
</html>

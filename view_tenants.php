
<?php
session_start();

// Include your database connection details (replace with actual values)
$dbhost = 'localhost';
$dbname = 'postgres';
$dbuser = 'postgres';
$dbpass = 'Keerthi23';

$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
if (!$conn) {
    die("Connection failed. Error: " . pg_last_error());
}

$query = "SELECT t.tid, t.user_name, t.email, t.phone_no, t.move_in_date, t.flat_no, t.rent_amt,
                 w.amount AS water_bill_amount, w.paid AS water_bill_paid,
                 e.amount AS electricity_bill_amount, e.paid AS electricity_bill_paid,
                 m.amount AS maintenance_fee_amount, m.paid AS maintenance_fee_paid
          FROM tenants t
          LEFT JOIN water_bills w ON t.tid = w.tenant_id
          LEFT JOIN electricity_bills e ON t.tid = e.tenant_id
          LEFT JOIN maintenance_fees m ON t.tid = m.tenant_id";

$result = pg_query($conn, $query);

if (!$result) {
    die("Error retrieving tenant details: " . pg_last_error($conn));
}

$tenants = [];

while ($row = pg_fetch_assoc($result)) {
    $row['water_bill_paid'] = ($row['water_bill_paid'] === 't'); // Convert 't'/'f' to true/false
    $row['electricity_bill_paid'] = ($row['electricity_bill_paid'] === 't'); // Convert 't'/'f' to true/false
    $row['maintenance_fee_paid'] = ($row['maintenance_fee_paid'] === 't'); // Convert 't'/'f' to true/false
    $tenants[] = $row;
}

pg_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants Details</title>
    <style>
        table {
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table th {
            text-align: left;
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
        }

        .back-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h1>Tenants Details</h1><br>

<?php if (count($tenants) > 0): ?>

    <table border="1">
        <thead>
        <tr>
            <th>Tenant ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Move-in Date</th>
            <th>Flat Number</th>
            <th>Rent Amount</th>
            <th>Water Bill Amount</th>
            <th>Water Bill Paid</th>
            <th>Electricity Bill Amount</th>
            <th>Electricity Bill Paid</th>
            <th>Maintenance Fee Amount</th>
            <th>Maintenance Fee Paid</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tenants as $tenant): ?>
            <tr>
                <td><?= $tenant['tid'] ?></td>
                <td><?= $tenant['user_name'] ?></td>
                <td><?= $tenant['email'] ?></td>
                <td><?= $tenant['phone_no'] ?></td>
                <td><?= $tenant['move_in_date'] ?></td>
                <td><?= $tenant['flat_no'] ?></td>
                <td><?= $tenant['rent_amt'] ?></td>
                <td><?= $tenant['water_bill_amount'] ?></td>
               
                <td><?= $tenant['water_bill_paid'] ? 'Paid' : 'Not Paid' ?></td>
                <td><?= $tenant['electricity_bill_amount'] ?></td>
                
                <td><?= $tenant['electricity_bill_paid'] ? 'Paid' : 'Not Paid' ?></td>
               
                <td><?= $tenant['maintenance_fee_amount'] ?></td>
        
                <td><?= $tenant['maintenance_fee_paid'] ? 'Paid' : 'Not Paid' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php else: ?>

    <p>No tenants found.</p>

<?php endif; ?><br>
<a href="admin_dashboard.php"><button type="button" class="back-button"> Prev Page</button></a>

</body>
</html>

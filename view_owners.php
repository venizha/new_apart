<?php
session_start();

$dbhost = 'localhost';
$dbname = 'postgres';
$dbuser = 'postgres';
$dbpass = 'Keerthi23';

$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
if (!$conn) {
  die("Connection failed. Error: " . pg_last_error());
}

$query = "SELECT * FROM owners";

$result = pg_query($conn, $query);

if (!$result) {
  die("Error retrieving owners: " . pg_last_error($conn));
}

$owners = [];

while ($row = pg_fetch_assoc($result)) {
  $owners[] = $row;
}

pg_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Owners details</title>
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

<h1>Owners details</h1><br>

<?php if (count($owners) > 0): ?>

  <table border="1">
    <thead>
      <tr>
        <th>Owner ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone Number</th>
        <th>Move-in Date</th>
        <th>Flat Number</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($owners as $owner): ?>
        <tr>
          <td><?= $owner['oid'] ?></td>
          <td><?= $owner['user_name'] ?></td>
          <td><?= $owner['email'] ?></td>
          <td><?= $owner['phone_no'] ?></td>
          <td><?= $owner['move_in_date'] ?></td>
          <td><?= $owner['flat_no'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php else: ?>

  <p>No owners found.</p>

<?php endif; ?>
<a href="admin_dashboard.php"><button type="button" class="back-button"> Prev Page</button></a>

</body>
</html>

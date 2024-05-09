<?php
session_start();

if (!isset($_SESSION['user_name'])) {
  header("Location: resident.php");
  exit();
}

$userName = $_SESSION['user_name'];
$dbhost = 'localhost';
$dbname = 'postgres';
$dbuser = 'postgres';
$dbpass = 'Keerthi23';


$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
if (!$conn) {
  die("Connection failed. Error: " . pg_last_error());
}


$query = "SELECT user_name, oid,email,phone_no,move_in_date,flat_no FROM owners WHERE user_name = $1";
$result = pg_query_params($conn, $query, array($userName));

if (!$result) {
  die("Query failed. Error: " . pg_last_error($conn));
}

$ownerDetails = pg_fetch_assoc($result); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Owner Details</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      
    }
    table {
      width: 50%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
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
  <div class="owner-details">
    <h2>Owner-details</h2>
    <h1>Welcome <?php echo $ownerDetails['user_name']; ?>!!</h1>
    <table>
      <tr>
        <th>Name</th>
        <td><?php echo $ownerDetails['user_name']; ?></td>
      </tr>
      <tr>
        <th>ID</th>
        <td><?php echo $ownerDetails['oid']; ?></td>
      </tr>
      <tr>
        <th>Flat Number</th>
        <td><?php echo $ownerDetails['flat_no']; ?></td>
      </tr>
      <tr>
        <th>Phone number</th>
        <td><?php echo $ownerDetails['phone_no']; ?></td>
      </tr>
      <tr>
        <th>Move_in_date</th>
        <td><?php echo $ownerDetails['move_in_date']; ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?php echo $ownerDetails['email']; ?></td>
      </tr>
      
    </table>
    <br><br>
    <a href="resident.php"><button type="button" class="back-button"> GO TO PREV</button></a>

  </div>
</body>
</html>
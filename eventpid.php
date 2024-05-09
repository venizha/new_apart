<?php
session_start();

// Replace placeholders with your actual database credentials
$dbhost = 'localhost';
$dbname = 'postgres';
$dbuser = 'postgres';
$dbpass = 'Keerthi23';

// Connect to PostgreSQL database
$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
if (!$conn) {
  die("Connection failed: " . pg_last_error());
}

// Check if participant ID is set in the URL parameter
if (isset($_GET['pid'])) {
  $pid = pg_escape_string($conn, $_GET['pid']); // Escape user input for security

  // Query to retrieve event names for the participant
  $query = "SELECT e.e_name
            FROM events e
            JOIN program p ON e.prog_id = p.prog_id
            JOIN team t ON p.prog_id = t.prog_id
            JOIN team_members tm ON t.t_id = tm.t_id
            WHERE tm.p_id = $pid";

  $result = pg_query($conn, $query);
  if (!$result) {
    die("Query execution failed: " . pg_last_error($conn));
  }

  $eventNames = [];
  while ($row = pg_fetch_assoc($result)) {
    $eventNames[] = $row['e_name'];
  }
} else {
  $pid = null;
  $eventNames = [];
}

// Close database connection
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Names for Participant ID <?= $pid ?></title>
</head>
<body>

<h1>Event Names for Participant ID <?= $pid ?></h1>

<?php if (!empty($eventNames)): ?>
  <ul>
    <?php foreach ($eventNames as $eventName): ?>
      <li><?= $eventName ?></li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <?php if (isset($_GET['pid'])):  // Only display "No events found" if participant ID was provided ?>
    <p>No events found for Participant ID <?= $pid ?></p>
  <?php endif; ?>
<?php endif; ?>

</body>
</html>
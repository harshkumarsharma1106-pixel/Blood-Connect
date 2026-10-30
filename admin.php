<?php
include 'db.php';

// Handle filtering
$blood_filter = isset($_GET['blood_type']) ? $_GET['blood_type'] : '';
$availability_filter = isset($_GET['availability']) ? $_GET['availability'] : '';

$query = "SELECT * FROM donors WHERE 1=1";

if ($blood_filter) {
    $query .= " AND blood_type = '$blood_filter'";
}
if ($availability_filter) {
    $query .= " AND availability = '$availability_filter'";
}

$result = $conn->query($query);

// Fetch emergency requests
$emergency_result = $conn->query("SELECT * FROM emergency_requests ORDER BY created_at DESC");

// --- NEW CODE: FETCH URGENT OPEN SUPPORT TICKETS ---
$urgent_tickets_count = 0;
// Check if the tickets table exists to avoid errors on first run
$table_check = $conn->query("SHOW TABLES LIKE 'tickets'");
if ($table_check && $table_check->num_rows > 0) {
    $ticket_check = $conn->query("SELECT COUNT(*) as urgent_count FROM tickets WHERE priority = 'High' AND status = 'Open'");
    if ($ticket_check) {
        $ticket_row = $ticket_check->fetch_assoc();
        $urgent_tickets_count = (int)$ticket_row['urgent_count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | BloodConnect</title>
  <link rel="stylesheet" href="style.css">
  <style>
    html {
      scroll-behavior: smooth;
    }
    /* --- ADMIN STYLES --- */
    .admin-section {
      padding: 50px 8%;
    }
    .admin-section h2 {
      color: #c0392b;
      margin-bottom: 20px;
      text-align: center;
    }
    
    /* --- URGENT TICKETS ALERT STYLES --- */
    .ticket-alert-banner {
      background: #fdf2f2;
      border: 2px solid #ecf0f1;
      border-left: 6px solid #c0392b;
      padding: 18px 25px;
      border-radius: 8px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(192, 57, 43, 0.08);
      animation: pulse-alert 2s infinite alternate;
    }
    .ticket-alert-text h4 {
      margin: 0 0 5px 0;
      color: #c0392b;
      font-size: 1.15rem;
    }
    .ticket-alert-text p {
      margin: 0;
      color: #555;
      font-size: 0.95rem;
    }
    .ticket-alert-btn {
      background-color: #c0392b;
      color: white;
      padding: 10px 18px;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      font-size: 0.9rem;
      transition: background 0.3s ease;
    }
    .ticket-alert-btn:hover {
      background-color: #a93226;
    }
    @keyframes pulse-alert {
      0% { transform: scale(1); box-shadow: 0 4px 15px rgba(192, 57, 43, 0.05); }
      100% { transform: scale(1.005); box-shadow: 0 4px 20px rgba(192, 57, 43, 0.15); }
    }

    .request-alert-banner{
      background: #fdf2f2;
      border: 2px solid #ecf0f1;
      border-left: 6px solid #c0392b;
      padding: 18px 25px;
      border-radius: 8px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(192, 57, 43, 0.08);
      animation: pulse-alert 2s infinite alternate;
    }

    .request-alert-text h4 {
      margin: 0 0 5px 0;
      color: #c0392b;
      font-size: 1.15rem;
    }

    .request-alert-text p{
      margin: 0;
      color: #555;
      font-size: 0.95rem;
    }

    .request-alert-btn{
      background-color: #c0392b;
      color: white;
      padding: 10px 18px;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      font-size: 0.9rem;
      transition: background 0.3s ease;
    }

    .request-alert-btn:hover{
      background-color: #a93226;
    }

    @keyframes pulse-alert {
      0% { transform: scale(1); box-shadow: 0 4px 15px rgba(192, 57, 43, 0.05); }
      100% { transform: scale(1.005); box-shadow: 0 4px 20px rgba(192, 57, 43, 0.15); }
    }

    .filter-bar {
      background: #ffeaea;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 25px;
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
    }
    .filter-bar select, .filter-bar button {
      padding: 10px;
      font-size: 1rem;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .filter-bar button {
      background-color: #c0392b;
      color: white;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }
    .filter-bar button:hover {
      background-color: #a93226;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #f1f1f1;
    }
    th {
      background-color: #c0392b;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #fff5f5;
    }
    tr:hover {
      background-color: #fdeaea;
    }
    .emergency-section {
      margin-top: 60px;
    }
    .emergency-section h3 {
      text-align: center;
      color: #c0392b;
      margin-bottom: 15px;
    }
    .emergency-card {
      background: #ffeaea;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      border-left: 5px solid #c0392b;
    }
    .emergency-card strong {
      color: #c0392b;
    }



  </style>
</head>
<body>
  <header>
    <div class="logo" href="index.html">❤️ BloodConnect</div>
    <nav>
      <a href="index.html">Home</a>
      <a class="nav-link" href="dashboard.php">Manage Support Tickets</a>
    </nav>
  </header>

  <section class="admin-section">
    
    <!-- DYNAMIC LIVE ALERT BANNER FOR URGENT TICKETS -->
    <?php if ($urgent_tickets_count > 0): ?>
        <div class="ticket-alert-banner">
            <div class="ticket-alert-text">
                <h4>⚠️ Urgent Support Action Required</h4>
                <p>There are currently <strong><?= $urgent_tickets_count ?></strong> high-priority support ticket(s) waiting for attention.</p>
            </div>
            <a href="dashboard.php" class="ticket-alert-btn">Resolve Now →</a>
        </div>
    <?php endif; ?>
<!-- DYNAMIV LIVE LAERT BANNER FOR EMERGENCY REQUESTS -->
    <?php if ($emergency_result->num_rows > 0): ?>
      <div class="request-alert-banner">
        <div class="request-alert-text">
          <h4>🚨 Emergency Request Received</h4>
          <p>There are currently <strong><?= $emergency_result->num_rows ?></strong> emergency blood requests waiting for attention.</p>
        </div>
        <a href="#emergency-requests" class="request-alert-btn">View Requests →</a> 
      </div>
    <?php endif; ?>

    <h2>Admin Dashboard</h2>

    <div class="filter-bar">
      <form method="GET" action="admin.php">
        <select name="blood_type">
          <option value="">Filter by Blood Group</option>
          <option value="A+" <?= $blood_filter == 'A+' ? 'selected' : '' ?>>A+</option>
          <option value="A-" <?= $blood_filter == 'A-' ? 'selected' : '' ?>>A-</option>
          <option value="B+" <?= $blood_filter == 'B+' ? 'selected' : '' ?>>B+</option>
          <option value="B-" <?= $blood_filter == 'B-' ? 'selected' : '' ?>>B-</option>
          <option value="AB+" <?= $blood_filter == 'AB+' ? 'selected' : '' ?>>AB+</option>
          <option value="AB-" <?= $blood_filter == 'AB-' ? 'selected' : '' ?>>AB-</option>
          <option value="O+" <?= $blood_filter == 'O+' ? 'selected' : '' ?>>O+</option>
          <option value="O-" <?= $blood_filter == 'O-' ? 'selected' : '' ?>>O-</option>
        </select>

        <select name="availability">
          <option value="">Filter by Availability</option>
          <option value="anytime" <?= $availability_filter == 'anytime' ? 'selected' : '' ?>>Anytime</option>
          <option value="urgent_only" <?= $availability_filter == 'urgent_only' ? 'selected' : '' ?>>Urgent Only</option>
          <option value="camps_only" <?= $availability_filter == 'camps_only' ? 'selected' : '' ?>>Camps Only</option>
        </select>

        <button type="submit">Apply Filters</button>
        <a href="admin.php" style="text-decoration:none;">
          <button type="button" style="background:#777;">Reset</button>
        </a>
      </form>
    </div>

    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Blood Group</th>
        <th>Availability</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Registered On</th>
      </tr>
      <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['name']}</td>
                  <td>{$row['blood_type']}</td>
                  <td>{$row['availability']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['phone']}</td>
                  <td>{$row['created_at']}</td>
              </tr>";
          }
      } else {
          echo "<tr><td colspan='7' style='text-align:center;'>No donors found</td></tr>";
      }
      ?>
    </table>

    <div id="emergency-requests" class="emergency-section">
      <h3>Emergency Requests</h3>
      <?php
      if ($emergency_result->num_rows > 0) {
          while ($req = $emergency_result->fetch_assoc()) {
              echo "<div class='emergency-card'>
  <strong>Blood Needed:</strong> {$req['blood_type_needed']} <br>
  <strong>Requester:</strong> {$req['requester_name']}<br>
  <strong>Email:</strong> {$req['requester_email']}<br>
  <strong>Phone:</strong> {$req['requester_phone']}<br>
  <strong>Message:</strong> " . ($req['message'] ?: 'No details provided') . "<br>
  <small>Submitted on: {$req['created_at']}</small>
</div>";
          }
      } else {
          echo "<p style='text-align:center;'>No emergency requests yet.</p>";
      }
      ?>
    </div>
  </section>

  <footer>
    <p>© 2025 BloodConnect | Admin Portal</p>
  </footer>
</body>
</html>

<?php $conn->close(); ?>
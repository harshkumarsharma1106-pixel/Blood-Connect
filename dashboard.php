<?php
require_once 'db.php'; // Includes your central $conn connection instance

// Handle closing a ticket using MySQLi
if (isset($_GET['close_id'])) {
    $closeId = (int)$_GET['close_id'];
    $stmt = $conn->prepare("UPDATE tickets SET status = 'Closed' WHERE id = ?");
    $stmt->bind_param("i", $closeId);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// Fetch all open tickets using MySQLi
$query = "SELECT * FROM tickets WHERE status = 'Open' ORDER BY FIELD(priority, 'High', 'Medium', 'Low'), created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Manage Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <header>
        <div class="logo" href="index.html">❤️ BloodConnect</div>
        <nav>
          <a href="index.html">Home</a>
          <a href="admin.php">Admin Panel</a>
        </nav>
    </header>
    
    <div class="container-fluid p-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Support Agent Dashboard</h2>
            <a href="admin.php" class="btn btn-outline-secondary">← Back to Admin Dashboard</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Priority (AI Triaged)</th>
                            <th>Submitted At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$result || $result->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No open tickets available.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($ticket = $result->fetch_assoc()): ?>
                                <?php 
                                    $badgeColor = 'bg-secondary';
                                    if ($ticket['priority'] === 'High') $badgeColor = 'bg-danger';
                                    if ($ticket['priority'] === 'Medium') $badgeColor = 'bg-warning text-dark';
                                ?>
                                <tr>
                                    <td class="align-middle">#<?= $ticket['id'] ?></td>
                                    <td class="align-middle">
                                        <strong><?= htmlspecialchars($ticket['customer_name']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($ticket['email']) ?></small>
                                    </td>
                                    <td class="align-middle"><?= htmlspecialchars($ticket['subject']) ?></td>
                                    <td class="align-middle"><span class="badge <?= $badgeColor ?>"><?= $ticket['priority'] ?></span></td>
                                    <td class="align-middle"><?= $ticket['created_at'] ?></td>
                                    <td class="text-center align-middle">
                                        <a href="view_ticket.php?id=<?= $ticket['id'] ?>" class="btn btn-sm btn-primary">Process Ticket</a>
                                        <a href="dashboard.php?close_id=<?= $ticket['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Mark this ticket as resolved?')">Close</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php
require_once 'db.php'; // Using your unified $conn connection

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// FIXED: Using MySQLi selector matching instead of PDO
$stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();
$stmt->close();

if (!$ticket) {
    die("Ticket not found.");
}

$simulatedEmailSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $finalReply = $_POST['reply_message'];
    
    // FIXED: Using MySQLi to update state safely
    $updateStmt = $conn->prepare("UPDATE tickets SET status = 'Closed' WHERE id = ?");
    $updateStmt->bind_param("i", $id);
    $updateStmt->execute();
    $updateStmt->close();
    
    $simulatedEmailSent = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Process Ticket #<?= $ticket['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <header>
        <div class="logo">❤️ BloodConnect</div>
        <nav>
          <a href="index.html">Home</a>
          <a href="dashboard.php">Ticket Dashboard</a>
        </nav>
    </header>

    <div class="container mt-5" style="max-width: 900px;">
        <div class="mb-3">
            <a href="dashboard.php" class="text-decoration-none">← Back to Ticket Dashboard</a>
        </div>

        <?php if ($simulatedEmailSent): ?>
            <div class="alert alert-success shadow-sm p-4">
                <h4>Response Dispatched Successfully!</h4>
                <p class="mb-0">The ticket has been updated to <strong>Closed</strong> status. simulated response payload sent to: <code><?= htmlspecialchars($ticket['email']) ?></code></p>
                <a href="dashboard.php" class="btn btn-danger mt-3">Return to Ticket Dashboard</a>
            </div>
        <?php else: ?>

            <div class="row g-4">
                <!-- Customer Inquiry -->
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Customer Inquiry</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>From:</strong> <?= htmlspecialchars($ticket['customer_name']) ?> (<?= htmlspecialchars($ticket['email']) ?>)</p>
                            <p><strong>Subject:</strong> <?= htmlspecialchars($ticket['subject']) ?></p>
                            <hr>
                            <p class="text-muted">Message:</p>
                            <div class="p-3 bg-white border rounded" style="white-space: pre-wrap; min-height: 200px;"><?= htmlspecialchars($ticket['message']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- AI Smart Draft -->
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-danger">
                        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">AI Smart Draft</h5>
                            <span class="badge bg-light text-danger">Priority: <?= $ticket['priority'] ?></span>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Review, edit, and send this reply:</label>
                                    <textarea name="reply_message" class="form-control" rows="11" required><?= htmlspecialchars($ticket['ai_draft']) ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100">Approve & Send Email</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>
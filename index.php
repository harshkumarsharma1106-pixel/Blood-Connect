<?php
require_once 'db.php'; // Using your unified $conn connection
require_once 'AiService.php';

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    if ($name && $email && $subject && $message) {
        // Fetch AI Analysis (Triage + Draft)
        $aiAnalysis = AiService::analyzeTicket($subject, $message);
        
        // FIXED: Using MySQLi prepared statements instead of PDO
        $stmt = $conn->prepare("INSERT INTO tickets (customer_name, email, subject, message, priority, ai_draft) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $subject, $message, $aiAnalysis['priority'], $aiAnalysis['draft']);
        
        if ($stmt->execute()) {
            $successMessage = "Your ticket has been successfully submitted! Our AI-powered system has triaged it as <strong>" . $aiAnalysis['priority'] . "</strong> priority.";
        } else {
            $errorMessage = "Something went wrong saving your ticket. Please try again.";
        }
        $stmt->close();
    } else {
        $errorMessage = "Please fill out all fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Support Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <header>
    <div class="logo">❤️ BloodConnect</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="admin.php">Admin</a>
    </nav>
  </header>
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h3 class="mb-0">Helpdesk Support System</h3>
            </div>
            <div class="card-body p-4">
                <?php if(!empty($successMessage)): ?>
                    <div class="alert alert-success"><?= $successMessage ?></div>
                <?php endif; ?>
                <?php if(!empty($errorMessage)): ?>
                    <div class="alert alert-danger"><?= $errorMessage ?></div>
                <?php endif; ?>

                <form action="index.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">How can we help you?</label>
                        <textarea name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Submit Ticket</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
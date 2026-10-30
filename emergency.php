<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $blood_type_needed = $_POST['blood_type_needed'];
    $message = $_POST['message'] ?? '';

    $stmt = $conn->prepare("INSERT INTO emergency_requests (requester_name, requester_email, requester_phone, blood_type_needed, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $blood_type_needed, $message);

    if ($stmt->execute()) {
        echo "<div style='text-align:center; font-family:Arial; padding:20px;'>
                <h2 style='color:#c0392b;'>Emergency Request Submitted!</h2>
                <p>We’ve received your request and will contact donors as soon as possible.</p>
                <p><strong>Admin Contact:</strong> 9876543210</p>
                <a href='index.html' style='color:#c0392b; text-decoration:none;'>← Back to Home</a>
              </div>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Emergency Request | BloodConnect</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">❤️ BloodConnect</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="register_form.html">Become a Donor</a>
      <a href="emergency.php">Emergency</a>
      <a href="admin.php">Admin</a>
    </nav>
  </header>

  <section class="form-section">
    <h2>Request Blood Urgently</h2>
    <p class="subtitle">Fill in the details below so we can reach out to you quickly.</p>

    <form method="POST" class="form-card">
      <label>Your Name</label>
      <input type="text" name="name" placeholder="Full Name" required>

      <label>Your Email</label>
      <input type="email" name="email" placeholder="Email Address" required>

      <label>Your Phone</label>
      <input type="text" name="phone" placeholder="Contact Number" required>

      <label>Blood Group Needed</label>
      <select name="blood_type_needed" required>
        <option value="">Select Blood Group</option>
        <option>A+</option><option>A-</option>
        <option>B+</option><option>B-</option>
        <option>AB+</option><option>AB-</option>
        <option>O+</option><option>O-</option>
      </select>

      <label>Message / Details (optional)</label>
      <textarea name="message" placeholder="e.g., Location, urgency details..."></textarea>

      <button type="submit" class="btn">Submit Emergency Request</button>
    </form>
  </section>

  <footer>
    <p>© 2025 BloodConnect | Made with ❤️ to save lives</p>
  </footer>
</body>
</html>
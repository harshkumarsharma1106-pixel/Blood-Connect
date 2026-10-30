<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $blood_type = $_POST['blood_type'];
    $availability = $_POST['availability'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO donors (name, blood_type, availability, email, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $blood_type, $availability, $email, $phone);

    if ($stmt->execute()) {
        echo "<h3>Registration successful!</h3>";
        echo "<a href='index.html'>Back to registration</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "blood_donation";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add this to your existing global config/db file if not already included
if (!defined('GEMINI_API_KEY')) {
    define('GEMINI_API_KEY', 'AQ.Ab8RN6LiOdnRLVbbJh3jvOJfu0nLCP1-LDLhnEWIbsowpPGE-A');
}
if (!defined('GEMINI_API_URL')) {
    define('GEMINI_API_URL', "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . GEMINI_API_KEY);
}




?>
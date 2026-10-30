<?php
// chatbot.php
header('Content-Type: application/json');

// 1. Your verified API Key
$apiKey = 'AIzaSyCRXbcnAZCifu8Z949XSbzc8yO2rmLhvEw'; 

// 2. The working Gemini 2.5 Flash endpoint
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['response' => 'Hello! How can I help you today?']);
    exit;
}

// 3. System Instructions
$systemInstruction = "You are the BloodConnect AI Assistant. Help users with blood donation queries. Motto: Donate Blood, Save Lives. Register via register_form.html, request via emergency.php.";

// 4. Prepare the Payload
$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $systemInstruction . "\n\nUser Question: " . $userMessage]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// Bypass SSL for your local XAMPP/MAMP environment
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['response' => 'Connection Error: ' . curl_error($ch)]);
} elseif ($httpCode !== 200) {
    echo json_encode(['response' => "API Error (Code $httpCode): Check your API settings."]);
} else {
    $decoded = json_decode($result, true);
    // Correct mapping for the Gemini response structure
    $reply = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I couldn't process that.";
    echo json_encode(['response' => $reply]);
}

curl_close($ch);
?>
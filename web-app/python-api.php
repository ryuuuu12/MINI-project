<?php
require_once 'config.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get JSON data from Python script
$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

// Validate required fields
if (!isset($data['drowsiness_percentage']) || !isset($data['api_key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

// Simple API key validation
$valid_api_key = 'DROWSINESSDETECTION';
if ($data['api_key'] !== $valid_api_key) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid API key']);
    exit();
}

// Get current time and date in Asia/Kolkata timezone
$current_time = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
$detection_time = $current_time->format('H:i:s');
$detection_date = $current_time->format('Y-m-d');

// You should get user_id from session or request
// For now using a default user (you should modify this)
$user_id = 1;

// Insert data into database
$drowsiness_percentage = $conn->real_escape_string($data['drowsiness_percentage']);
$sql = "INSERT INTO data (user_id, drowsiness_percentage, detection_time, detection_date) 
        VALUES ('$user_id', '$drowsiness_percentage', '$detection_time', '$detection_date')";

if ($conn->query($sql)) {
    // Trigger ESP8266 - Write to file with timestamp
    $trigger_data = [
        'trigger' => 1,
        'percentage' => $drowsiness_percentage,
        'timestamp' => time()
    ];
    file_put_contents('esp_trigger.txt', json_encode($trigger_data));
    
    // Also make direct HTTP request to esp-api.php (as backup)
    $esp_url = 'https://drowsinessdetection-production.up.railway.app/esp-api.php';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $esp_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['trigger' => 1, 'percentage' => $drowsiness_percentage]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Add if SSL issues
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo json_encode([
        'success' => true,
        'message' => 'Drowsiness data saved and ESP triggered',
        'id' => $conn->insert_id,
        'esp_triggered' => true
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>

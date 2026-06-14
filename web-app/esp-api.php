<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// File to store trigger state
$trigger_file = 'esp_trigger.txt';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ESP8266 will POST to check for triggers
    if (isset($_POST['check']) && $_POST['check'] == 1) {
        if (file_exists($trigger_file)) {
            $content = file_get_contents($trigger_file);
            $trigger_data = json_decode($content, true);
            
            if ($trigger_data && isset($trigger_data['trigger']) && $trigger_data['trigger'] == 1) {
                // Check if trigger is still valid (within last 5 seconds)
                $timestamp = isset($trigger_data['timestamp']) ? $trigger_data['timestamp'] : 0;
                $current_time = time();
                
                if (($current_time - $timestamp) <= 5) {
                    // Return trigger true with percentage
                    $response = [
                        'trigger' => true,
                        'duration' => 10,
                        'percentage' => $trigger_data['percentage']
                    ];
                    echo json_encode($response);
                    
                    // Don't clear immediately - keep for 1 second to ensure ESP gets it
                    // Clear after returning response
                    $clear_data = ['trigger' => 0, 'timestamp' => $current_time];
                    file_put_contents($trigger_file, json_encode($clear_data));
                } else {
                    // Trigger expired
                    echo json_encode(['trigger' => false]);
                }
            } else {
                echo json_encode(['trigger' => false]);
            }
        } else {
            echo json_encode(['trigger' => false]);
        }
    }
    // PHP API will POST here to set trigger
    elseif (isset($_POST['trigger']) && $_POST['trigger'] == 1) {
        $trigger_data = [
            'trigger' => 1,
            'percentage' => isset($_POST['percentage']) ? $_POST['percentage'] : 85,
            'timestamp' => time()
        ];
        file_put_contents($trigger_file, json_encode($trigger_data));
        echo json_encode(['success' => true, 'message' => 'Trigger set', 'data' => $trigger_data]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // For debugging - check current trigger state
    if (file_exists($trigger_file)) {
        echo file_get_contents($trigger_file);
    } else {
        echo json_encode(['trigger' => false]);
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
?>
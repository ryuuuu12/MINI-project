<?php
header("Content-Type: text/html");

echo "<h1>ESP8266 Trigger Test</h1>";

// Manually set trigger
if (isset($_GET['trigger'])) {
    $trigger_data = [
        'trigger' => 1,
        'percentage' => 85,
        'timestamp' => time()
    ];
    file_put_contents('esp_trigger.txt', json_encode($trigger_data));
    echo "<p style='color:green'>✅ Trigger set manually!</p>";
}

// Clear trigger
if (isset($_GET['clear'])) {
    $clear_data = ['trigger' => 0, 'timestamp' => time()];
    file_put_contents('esp_trigger.txt', json_encode($clear_data));
    echo "<p style='color:orange'>⏰ Trigger cleared!</p>";
}

// Show current trigger state
echo "<h2>Current Trigger State:</h2>";
if (file_exists('esp_trigger.txt')) {
    $content = file_get_contents('esp_trigger.txt');
    $data = json_decode($content, true);
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    
    if ($data && isset($data['trigger']) && $data['trigger'] == 1) {
        $age = time() - $data['timestamp'];
        echo "<p style='color:red'>⚠️ TRIGGER ACTIVE! Age: {$age} seconds</p>";
    } else {
        echo "<p style='color:green'>✅ No active trigger</p>";
    }
} else {
    echo "<p>No trigger file found</p>";
}

echo "<hr>";
echo "<a href='?trigger=1'>Set Trigger</a> | ";
echo "<a href='?clear=1'>Clear Trigger</a> | ";
echo "<a href=''>Refresh</a>";
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $title = $_POST['title'] ?? '';
    $message = $_POST['message'] ?? '';
    $number = $_POST['number'] ?? '09761949189'; // Default number
    $senderName = $_POST['sendername'] ?? 'Thesis'; // Default sender name
    $apiKey = $_POST['apikey'] ?? '';
    $url = $_POST['url'] ?? ''; // Default URL if not passed

    // Check for missing API key
    if (empty($apiKey)) {
        echo json_encode(['status' => 'error', 'message' => 'API key missing']);
        exit;
    }

    // Prepare the message
    $smsMessage =  "Title: \n\n" . $title . $message . "\n\n" ;

    // Prepare the data for the cURL request
    $data = [
        'apikey' => $apiKey,
        'number' => $number,
        'message' => $smsMessage,
        'sendername' => $senderName
    ];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);  // Use the URL passed from JavaScript
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Execute the cURL request and get the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send SMS']);
    } else {
        // Success response
        echo json_encode(['status' => 'success', 'message' => 'SMS sent successfully']);
    }

    // Close the cURL session
    curl_close($ch);
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $number = $_POST['number'] ?? '09761949189';
    $duesId = $_POST['duesId'] ?? ''; 
    $message = $_POST['message'] ?? '';
    $apiKey = $_POST['apiKey'] ?? ''; 
    $senderName = $_POST['senderName'] ?? ''; 
    $url = $_POST['url'] ?? ''; 

    if (empty($apiKey)) {
        echo json_encode(['status' => 'error', 'message' => 'API key missing']);
        exit;
    }

    if (empty($senderName)) {
        echo json_encode(['status' => 'error', 'message' => 'Sender name missing']);
        exit;
    }

    if (empty($name) || empty($message) || empty($number)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $smsMessage = "Dear $name,\n\nThis is a reminder that you have unpaid dues on ANAK-PAWIS HOMEOWNERS' ASSOCIATION (APHOA), INC. Please make the payment as soon as possible to avoid deactivation of the account. Thank you for your prompt attention.";

    $data = [
        'apikey' => $apiKey,
        'number' => $number,
        'message' => $smsMessage,
        'sendername' => $senderName
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); // Use the URL passed from JavaScript
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);

    if ($response === false) {
        echo json_encode(['status' => 'error', 'message' => 'Error sending SMS']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'SMS sent successfully']);
    }

    curl_close($ch);
}
?>

<?php
require_once 'menu.php';

// Get USSD parameters
$sessionId = $_POST['sessionId'] ?? '';
$serviceCode = $_POST['serviceCode'] ?? '';
$phoneNumber = $_POST['phoneNumber'] ?? '';
$text = $_POST['text'] ?? '';

// Clean the phone number
$phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

// Sanitize and limit session ID to valid characters and length
$sessionId = preg_replace('/[^a-zA-Z0-9,-]/', '', $sessionId);
$sessionId = substr($sessionId, 0, 26);

// Process the USSD input
$textArray = explode('*', $text);
$userInput = end($textArray);

try {
    // Initialize session
    if (!empty($sessionId)) {
        session_id($sessionId);
    }
    session_start();

    // Store only necessary data in session
    if (!isset($_SESSION['menu_state'])) {
        $_SESSION['menu_state'] = [
            'current_menu' => 'main',
            'language' => 'EN',
            'phone' => $phoneNumber
        ];
    }

    // Create new Menu instance with session data
    $menu = new Menu(
        $_SESSION['menu_state']['phone'],
        $_SESSION['menu_state']
    );

    // Handle user input
    $response = $menu->handleInput($userInput);

    // Update session data
    $_SESSION['menu_state'] = [
        'current_menu' => $menu->getCurrentMenu(),
        'language' => $menu->getLanguage(),
        'phone' => $phoneNumber
    ];

    // Prepare the USSD response
    header('Content-type: text/plain');
    echo "CON " . $response;

} catch (Exception $e) {
    // Log the error
    error_log("USSD Error: " . $e->getMessage());
    
    // Send a user-friendly error message
    header('Content-type: text/plain');
    echo "END An error occurred. Please try again later.";
}

// Clean up
session_write_close();
?>
<?php
// scratch/chat_test.php
require_once __DIR__ . '/../config/config.php';

try {
    // Clean up test messages if any exist
    $pdo->exec("DELETE FROM messages WHERE MessageText LIKE 'Test message from %'");

    // User 3 is an Instructor (sir)
    // User 5 is a Student (student)
    $senderId = 5; // Student
    $receiverId = 3; // Instructor

    echo "Inserting test message from Student (ID: $senderId) to Instructor (ID: $receiverId)...\n";
    
    $stmt = $pdo->prepare("INSERT INTO messages (SenderID, ReceiverID, MessageText) VALUES (?, ?, ?)");
    $stmt->execute([$senderId, $receiverId, "Test message from Student"]);

    $messageId = $pdo->lastInsertId();
    echo "Message inserted successfully with MessageID: $messageId\n";

    echo "Fetching conversation history between user $senderId and $receiverId...\n";
    $stmt = $pdo->prepare("
        SELECT * FROM messages 
        WHERE (SenderID = ? AND ReceiverID = ?) OR (SenderID = ? AND ReceiverID = ?)
        ORDER BY SentAt ASC
    ");
    $stmt->execute([$senderId, $receiverId, $receiverId, $senderId]);
    $messages = $stmt->fetchAll();

    print_r($messages);

    if (count($messages) > 0 && $messages[0]['MessageText'] === "Test message from Student") {
        echo "\nSuccess! Database validation for messaging functions perfectly.\n";
    } else {
        echo "\nFailure: Message text does not match.\n";
    }

    // Clean up test data
    $pdo->exec("DELETE FROM messages WHERE MessageID = $messageId");
    echo "Cleaned up test message.\n";

} catch (Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
}

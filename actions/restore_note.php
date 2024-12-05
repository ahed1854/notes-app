<?php
session_start();
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $note_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        // First verify that the note belongs to the user and is in trash
        $stmt = $pdo->prepare("SELECT id FROM notes WHERE id = ? AND user_id = ? AND is_deleted = TRUE");
        $stmt->execute([$note_id, $user_id]);
        
        if ($stmt->fetch()) {
            // If verification passes, restore the note
            $stmt = $pdo->prepare("UPDATE notes SET is_deleted = FALSE WHERE id = ? AND user_id = ?");
            $stmt->execute([$note_id, $user_id]);
        }

        header("Location: ../trash.php?notification=" . urlencode("Note restored successfully") . "&type=success");
        exit();
    } catch(PDOException $e) {
        die("Error restoring note: " . $e->getMessage());
    }
} 
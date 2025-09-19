<?php
// Database connection
require_once 'db.php';

class Chat {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
    }

    public function sendMessage($userId, $message) {
        $stmt = $this->db->prepare("INSERT INTO general_chat (user_id, message, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $userId, $message);
        return $stmt->execute();
    }

    public function getMessages($limit = 50) {
        $stmt = $this->db->prepare("SELECT gc.message, gc.created_at, u.username FROM general_chat gc JOIN users u ON gc.user_id = u.id ORDER BY gc.created_at DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserChatHistory($userId, $limit = 50) {
        $stmt = $this->db->prepare("SELECT gc.message, gc.created_at FROM general_chat gc WHERE gc.user_id = ? ORDER BY gc.created_at DESC LIMIT ?");
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit();
    }
    
    $action = $_POST['action'] ?? '';
    $chat = new Chat();
    
    if ($action === 'send') {
        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
            exit();
        }
        
        if ($chat->sendMessage($_SESSION['user_id'], $message)) {
            echo json_encode(['success' => true, 'message' => 'Message sent']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
    } elseif ($action === 'get') {
        $messages = $chat->getMessages();
        echo json_encode(['success' => true, 'messages' => $messages]);
    }
}
?>
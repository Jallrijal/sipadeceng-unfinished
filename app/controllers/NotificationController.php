<?php
class NotificationController extends Controller {
    
    public function index() {
        if (!isLoggedIn()) {
            $this->redirect('auth/login');
        }
        
        $notifModel = new Notification();
        $userId = $_SESSION['user_id'];
        
        // Get all notifications (tidak hanya unread)
        $notifications = $notifModel->getAllUserNotifications($userId);
        
        // Mark all as read
        $notifModel->markAllAsRead($userId);
        
        $data = [
            'title' => 'Notifikasi',
            'page_title' => 'Notifikasi',
            'notifications' => $notifications
        ];
        
        $this->view('notification/index', $data);
    }
    
    public function markAsRead($id) {
        if (!isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $notifModel = new Notification();
        $userId = $_SESSION['user_id'];
        
        // Verify this notification belongs to the user
        $notification = $notifModel->getNotificationById($id, $userId);
        
        if (!$notification) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Notification not found']);
            exit;
        }
        
        $result = $notifModel->markAsRead($id, $userId);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Notifikasi ditandai sebagai sudah dibaca' : 'Gagal menandai notifikasi'
        ]);
    }
}

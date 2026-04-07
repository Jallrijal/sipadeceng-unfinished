<?php
class Notification extends Model {
    protected $table = 'notifications';
    
    public function getUserNotifications($userId, $unreadOnly = true) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        $params = [$userId];
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT 10";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getAllUserNotifications($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC";
        $params = [$userId];
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function markAsRead($id, $userId) {
        return $this->db->query(
            "UPDATE {$this->table} SET is_read = 1 WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );
    }
    
    public function getNotificationById($id, $userId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND user_id = ?";
        return $this->db->fetch($sql, [$id, $userId]);
    }
    
    public function markAllAsRead($userId) {
        return $this->db->query(
            "UPDATE {$this->table} SET is_read = 1 WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
    }
    
    public function sendNotification($userId, $message, $type = 'info', $relatedLeaveId = null) {
        $result = $this->create([
            'user_id' => $userId,
            'message' => $message,
            'type' => $type,
            'is_read' => 0,
            'related_leave_id' => $relatedLeaveId,
            'created_at' => getCurrentDateTime()
        ]);

        // Send email notification if user has email
        if ($result) {
            $userModel = new User();
            $user = $userModel->find($userId);
            if ($user && !empty($user['email'])) {
                require_once __DIR__ . '/../helpers/email_helper.php';
                $subject = 'Notifikasi Sistem Cuti - ' . ucfirst($type);
                $body = "Halo {$user['nama']},\n\n{$message}\n\nSalam,\nSipadeceng";
                sendEmail($user['email'], $subject, $body);
            }
        }

        return $result;
    }
    
    public function notifyAdmins($message, $type = 'info', $relatedLeaveId = null) {
        $userModel = new User();
        $admins = $userModel->getAdmins();
        
        foreach ($admins as $admin) {
            $this->sendNotification($admin['id'], $message, $type, $relatedLeaveId);
        }
    }
    
    public function clearOldNotifications($userId, $days = 30) {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->db->query(
            "DELETE FROM {$this->table} 
             WHERE user_id = ? AND is_read = 1 AND created_at < ?",
            [$userId, $date]
        );
    }
}
<?php
// Database helper functions for controllers
trait DatabaseHelper {
    private $_db;
    
    protected function db() {
        if (!$this->_db) {
            if (!class_exists('Database')) {
                require_once 'app/core/Database.php';
            }
            $this->_db = Database::getInstance();
        }
        return $this->_db;
    }
}
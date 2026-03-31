<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isPimpinan() {
    return isset($_SESSION['user_type']) && (
        $_SESSION['user_type'] === 'admin'
    );
}

function isPegawai() {
    return isset($_SESSION['user_type']) && (
        $_SESSION['user_type'] === 'user' || $_SESSION['user_type'] === 'pegawai'
    );
}

function isAtasan() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'atasan';
}

/**
 * Check if current atasan has kasubbag role
 */
function isKasubbag() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'kasubbag';
}

/**
 * Check if current atasan has kabag role
 */
function isKabag() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'kabag';
}

/**
 * Check if current atasan has sekretaris role
 */
function isSekretaris() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'sekretaris';
}

/**
 * Check if current atasan has ketua role
 */
function isKetua() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'ketua';
}

/**
 * Get atasan role (for atasans)
 */
function getAtasanRole() {
    return $_SESSION['atasan_role'] ?? null;
}

// Backwards-compatible wrappers
function isAdmin() {
    return isPimpinan();
}

function isUser() {
    return isPegawai() || isAtasan();
}

function requireLogin() {
    if (!isLoggedIn()) {
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        } else {
            header('Location: ' . baseUrl('auth/login'));
            exit;
        }
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Admin access required']);
            exit;
        } else {
            header('Location: ' . baseUrl('dashboard'));
            exit;
        }
    }
}

function requireUser() {
    requireLogin();
    if (!isUser()) {
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User access required']);
            exit;
        } else {
            header('Location: ' . baseUrl('dashboard'));
            exit;
        }
    }
}

function requireAtasan() {
    requireLogin();
    if (!isAtasan()) {
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Atasan access required']);
            exit;
        } else {
            header('Location: ' . baseUrl('dashboard'));
            exit;
        }
    }
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
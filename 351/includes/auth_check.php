<?php
/**
 * auth_check.php — Role-based access helper
 *
 * Usage at the top of any protected page:
 *
 *   require_once __DIR__ . '/../../includes/auth_check.php';
 *   require_role(['student', 'professor']);
 *
 * Or just enforce login (any role):
 *   require_login();
 */
 
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }
}
 
/**
 * Restrict a page to specific roles.
 * Redirects to dashboard with an ?error= message if the role doesn't match.
 *
 * @param array $allowed_roles  e.g. ['student', 'professor']
 */
function require_role(array $allowed_roles) {
    require_login();
 
    $role = $_SESSION['role'] ?? '';
 
    if (!in_array($role, $allowed_roles, true)) {
        header("Location: /dashboard.php?error=access_denied");
        exit;
    }
}

function require_admin() {
    require_login();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header("Location: /dashboard.php?error=access_denied");
        exit;
    }
}

/**
 * Returns true if the current user has one of the given roles.
 * Useful for conditionally showing UI elements.
 *
 * @param array $roles
 * @return bool
 */
function has_role(array $roles): bool {
    $role = $_SESSION['role'] ?? '';
    return in_array($role, $roles, true);
}
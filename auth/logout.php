<?php
session_start();
// Clear session data and the session cookie, then destroy the session.
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
// Start a fresh session only to carry the "logged out" message.
session_start();
$_SESSION['flash'] = ['type' => 'info', 'text' => 'You are logged out.'];
header('Location: ../index.php');
exit;

<?php
// Use Sri Lanka time so "today" and the 7-day window match the user's local date.
date_default_timezone_set('Asia/Colombo');

// Start the session once; the cookie is hidden from JavaScript (httponly).
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Escape output to prevent XSS. Use for every value printed into HTML.
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Trim whitespace from user input. Escaping happens on output with e().
function clean($value)
{
    return trim((string) $value);
}

// Counts characters, not bytes, so non-English text is measured correctly.
function text_length($value)
{
    return (int) preg_match_all('/./su', (string) $value);
}

// Send a redirect and stop the script so no further code runs.
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

// Page guard: send visitors who are not logged in to the login page.
// $base is the path prefix back to the site root.
function require_login($base = '')
{
    if (!is_logged_in()) {
        set_flash('warning', 'Log in to open your dashboard.');
        redirect($base . 'auth/login.php');
    }
}

// Flash messages are shown once on the next page load, then removed (see get_flash).
function set_flash($type, $text)
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}

function get_flash()
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

// CSRF protection: one random token per session, added to every form as a hidden field
// and compared on submit with csrf_valid().
function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid()
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

function valid_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && strlen($email) <= 120;
}

// 3 to 30 characters: letters, numbers and underscores only.
function valid_username($username)
{
    return preg_match('/^[A-Za-z0-9_]{3,30}$/', $username) === 1;
}

// Password rule: at least 8 characters with a letter and a number.
function valid_password($password)
{
    return strlen($password) >= 8 && preg_match('/[A-Za-z]/', $password) === 1 && preg_match('/[0-9]/', $password) === 1;
}

// Rejects impossible dates such as 2025-02-30 by re-formatting the parsed date.
function valid_date($date)
{
    $parsed = DateTime::createFromFormat('Y-m-d', $date);
    return $parsed !== false && $parsed->format('Y-m-d') === $date;
}

// Allowed activity names, used for the dropdown and to validate submitted values.
function activity_options()
{
    return ['Running', 'Walking', 'Cycling', 'Swimming', 'Strength training', 'Yoga', 'HIIT', 'Other'];
}

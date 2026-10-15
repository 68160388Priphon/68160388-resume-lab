<?php
// crud_bootstrap.php - helper functions used by skills.php, skill_create.php,
// skill_edit.php and skill_delete.php (rebuilt for the CRUD lab).
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escape output to prevent XSS
function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// CSRF protection
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): void {
    echo '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void {
    $sent = (string)($_POST['csrf_token'] ?? '');
    $real = (string)($_SESSION['csrf_token'] ?? '');
    if ($sent === '' || $real === '' || !hash_equals($real, $sent)) {
        http_response_code(400);
        exit('Invalid CSRF token');
    }
}

// Validate a record id: returns a positive int, or 0 if invalid
function valid_id($value): int {
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? 0 : (int)$id;
}

// The lab always works on the first (demo) profile. Returns 0 if none exists.
function profile_id(mysqli $conn): int {
    $res = mysqli_query($conn, 'SELECT id FROM profile ORDER BY id ASC LIMIT 1');
    if (!$res) {
        return 0;
    }
    $row = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return $row ? (int)$row['id'] : 0;
}

// Page layout
function page_start(string $title): void {
    echo '<!doctype html><html lang="th"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>' . h($title) . '</title>'
       . '<style>'
       . 'body{font-family:Arial,Helvetica,sans-serif;max-width:720px;margin:24px auto;padding:0 16px;color:#222}'
       . 'table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:8px;text-align:left}'
       . 'th{background:#f3f3f3}label{display:block;margin-top:12px;font-weight:bold}'
       . 'input,select{width:100%;padding:8px;margin-top:4px;box-sizing:border-box}'
       . 'button{padding:8px 16px;cursor:pointer}.danger{background:#c62828;color:#fff;border:0}'
       . '.err{background:#fdecea;color:#b71c1c;padding:10px;border-radius:4px}'
       . '.ok{background:#e8f5e9;color:#1b5e20;padding:10px;border-radius:4px}'
       . '.alert{background:#fff8e1;color:#795500;padding:10px;border-radius:4px;margin:12px 0}'
       . '.muted{color:#666}'
       . '</style></head><body>'
       . '<p><a href="skills.php">Skills</a> | <a href="skill_create.php">Add skill</a></p>'
       . '<h1>' . h($title) . '</h1>';
}

function page_end(): void {
    echo '</body></html>';
}
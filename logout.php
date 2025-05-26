<?php
session_start();

// bum pusty array
$_SESSION = [];

// cookie tez usun 
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Zniszczarka
session_destroy();

// przekieruj na główną strone
header("Location: index.php");
exit;
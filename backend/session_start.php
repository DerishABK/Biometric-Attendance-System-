<?php
/**
 * Centralized Session Initialization
 * This file ensures all pages in the project use consistent session cookie parameters.
 */

// Set session name BEFORE starting the session
session_name('PRISON_V2_SESSION');

if (session_status() === PHP_SESSION_NONE) {
    // Force path to root or detect it. Using / is safest for cross-directory access.
    session_set_cookie_params([
        'path' => '/',
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Prevent browser from caching protected pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>

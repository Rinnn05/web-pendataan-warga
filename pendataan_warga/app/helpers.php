<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
function is_logged_in(){ return isset($_SESSION['user_id']); }
function require_login(){ if(!is_logged_in()){ header('Location: login.php'); exit; } }
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

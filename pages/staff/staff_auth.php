<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'police'])) {
    header('Location: ../staff/login.php');
    exit();
}

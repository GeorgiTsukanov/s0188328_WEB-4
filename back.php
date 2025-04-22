<?php
$config = require_once __DIR__ . '/../../config/s0188328_WEB_4.php';
require_once 'WorkWithSQL.php';
require_once 'Validate.php';

$fio = validateInput($_POST['fio']);
$phone = "8".(str_replace("-", "", validateInput($_POST['phone'])));
$email = validateInput($_POST['email']);
$birthday = validateInput($_POST['birthday']);
$gender = validateInput($_POST['gender']);
$biography = validateInput($_POST['biography']);
$languages = $_POST['languages'];

$error = isValidatePost($fio, $email, $phone, $birthday, $gender, $languages);

if (!empty($error)){
    setcookie('form_errors', json_encode($error), 0, '/');
    setcookie('fio', $fio, 0, '/');
    setcookie('phone', $phone, 0, '/');
    setcookie('email', $email, 0, '/');
    setcookie('birthday', $birthday, 0, '/');
    setcookie('gender', $gender, 0, '/');
    setcookie('biography', $biography, 0, '/');
    setcookie('languages', json_encode($languages), 0, '/');
    // Перенаправляем обратно на форму с ошибками
    header('Location: form.php');
    exit;
}
else {
    pushApplication($config ,makeAssociativeArray($fio), $phone, $email, $birthday, $gender, $biography, $languages);
    setcookie('fio', $fio, time() + 365 * 24 * 3600, '/');
    setcookie('phone', $phone, time() + 365 * 24 * 3600, '/');
    setcookie('email', $email, time() + 365 * 24 * 3600, '/');
    setcookie('birthday', $birthday, time() + 365 * 24 * 3600, '/');
    setcookie('gender', $gender, time() + 365 * 24 * 3600, '/');
    setcookie('biography', $biography, time() + 365 * 24 * 3600, '/');
    setcookie('languages', json_encode($languages), time() + 365 * 24 * 3600, '/');
}
?>
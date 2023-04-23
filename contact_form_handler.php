<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
</head>
</html>
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'config.php';
require 'PHPMailer-6.2.0/src/Exception.php';
require 'PHPMailer-6.2.0/src/PHPMailer.php';

/* ОТПРАВКА ФОРМЫ В БД */
const TABLE = '`Contact Table 3`';

$arrayMessage = [];

$db = new mysqli(HOST, USER, PASS, BASE)
    or die('Ошибка подключения к базе данных');

#Create table "Contact Table"
$query = 'CREATE TABLE IF NOT EXISTS ' . TABLE . '(
    `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
    `Наименование объекта` VARCHAR(64),
    `Площадь` VARCHAR(64),
    `Проектирование` CHAR(1),
    `Строительство` CHAR(1),
    `Согласование документов` CHAR(1),
    `Другое` CHAR(1),
    `Email` VARCHAR(255),
    `Телефон` DECIMAL(11)
);';

$db->query($query)
    or die('Ошибка при создании таблицы');

$objectName = $_POST['object'];
$area = $_POST['area'];
$type_1 = $_POST['type-1'];
($type_1 == 'on') ? $type_1 = '✓' : $type_1 = '—';
$type_2 = $_POST['type-2'];
($type_2 == 'on') ? $type_2 = '✓' : $type_2 = '—';
$type_3 = $_POST['type-3'];
($type_3 == 'on') ? $type_3 = '✓' : $type_3 = '—';
$type_4 = $_POST['type-4'];
($type_4 == 'on') ? $type_4 = '✓' : $type_4 = '—';
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];

/*$user_pass_phrase = sha1($_POST['verify']);*/
/*if ($_SESSION['pass_phrase'] == $user_pass_phrase) {*/
    if (isset($objectName) &&
        isset($area) &&
        isset($email) &&
        isset($phone_number)) {
        if (!(empty($objectName) &&
            empty($area) &&
            empty($email) &&
            empty($phone_number))) {
            #Security SQL Injection
            $objectName = $db->real_escape_string(trim($objectName));
            $area = $db->real_escape_string(trim($area));
            $email = $db->real_escape_string(trim($email));
            $phone_number = $db->real_escape_string(trim($phone_number));

            $query = 'INSERT INTO ' . TABLE . '(`Наименование объекта`, `Площадь`, `Проектирование`, `Строительство`, `Согласование документов`, `Другое`, `Email`, `Телефон`)' .
                "VALUES('{$objectName}', '{$area}', '{$type_1}', '{$type_2}', '{$type_3}', '{$type_4}', '{$email}', '{$phone_number}')";

            $db->query($query)
                or die('Ощибка записи в таблицу [' . $query . ']');
            $arrayMessage['successful'] = 'Ваша заявка принята!';


            /*$objectName = null;
            $area = null;
            $type_1 = null;
            $type_2 = null;
            $type_3 = null;
            $type_4 = null;
            $email = null;
            $phone_number = null;*/
            /*$user_pass_phrase = null;*/
            unset($arrayMessage['error']);
        }
        else
            if (empty($objectName)) $arrayMessage['error']['objectName'] = 'Поля с наименованием объекта не выбрано!';
        if (empty($area)) $arrayMessage['error']['area'] = 'Площадь объекта не выбрана!';
        if (empty($email)) $arrayMessage['error']['email'] = 'Email не указан!';
        if (empty($phone_number)) $arrayMessage['error']['phone_number'] = 'Номер телеона не указан!';
    }
    else
        $arrayMessage['error']['global'] = 'Обновите страницу произошла непредвиденная ошибка!';
/*}
else
    $arrayMessage['error']['verify'] = 'Enter passphrase!';*/

/* ОТПРАВКА ФОРМЫ НА ПОЧТУ */
$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->setLanguage('ru', 'PHPMailer-6.2.0/language/');
$mail->IsHTML(true);

// От кого письмо
$mail->setFrom('vyacheslavserebr@gmail.com', 'Вячеслав Серебрянников');
// Кому письмо
$mail->addAddress($email);
// Тема письма
$mail->Subject = 'Здравствуйте, вам оставили заявку!';

$body = '<h1>Прошу сделать расчёт полной стоимости объекта со следующими данными:</h1>';

$body .= '<p><strong>Наименование объекта:</strong> '.$objectName.'</p>';
$body .= '<p><strong>Площадь:</strong> '.$area.'</p>';
$body .= '<p><strong>Проектирование:</strong> '.$type_1.'</p>';
$body .= '<p><strong>Строительство:</strong> '.$type_2.'</p>';
$body .= '<p><strong>Согласование документов:</strong> '.$type_3.'</p>';
$body .= '<p><strong>Другое:</strong> '.$type_4.'</p>';
$body .= '<p><strong>Email:</strong> '.$email.'</p>';
$body .= '<p><strong>Номер телефона:</strong> '.$phone_number.'</p>';


$mail->Body = $body;

$mail->send();

/*header('Location: ');*/
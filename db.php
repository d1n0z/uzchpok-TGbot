<?php

$servername = '51.81.117.19';

$username = 'bot_dev';
$password = 'CAkJE3PaaOwH1t0e';
$dbname = 'uzcom_usr';

$conn_first = mysqli_connect($servername, $username, $password, $dbname);
$conn_first->set_charset('utf8');

$username = 'bot_developer';
$dbname = 'telegram_users';

$conn_users = mysqli_connect($servername, $username, $password, $dbname);
$conn_users->set_charset('utf8');

$dbname = 'uzpopka_club';

$conn_second = mysqli_connect($servername, $username, $password, $dbname);
$conn_second->set_charset('utf8');

if (!$conn_users or !$conn_first or !$conn_second) {
    die('Connection failed: ' . mysqli_connect_error());
}
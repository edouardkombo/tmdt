<?php
error_reporting(-1);
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');

require '../Database.php';
require '../Manager.php';

$manager = new Manager();
$manager->connect();
$manager->setLimit(3);
$manager->setSuccessMessage('Your message will be published in less than %d minute(s)');
$manager->setErrorMessage('Unable to deliver your message, please try again!');
$manager->getPostVars();
$manager->csrfProtect();
echo $manager->execute();
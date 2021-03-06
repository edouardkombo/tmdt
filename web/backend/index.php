<?php
error_reporting(-1);
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
header("Access-Control-Allow-Origin: http://themilliondollartalk.com");
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: text/html; charset=utf-8');

require '../../vendor/autoload.php';
require '../../Database.php';
require '../../Manager.php';

\Codebird\Codebird::setConsumerKey("LB8JWqYaEpX09Gmfr76UxhTfS", "CTN8HJ1Z1OLDrEVMdEp4tORqchFuwX85I1FaC72oqyGU9EfgBz");
$cb = \Codebird\Codebird::getInstance();
$cb->setToken("2927004143-bv4Yk0TtIeD3whWMUN3aB9CXt5P3W6fpsa8Bs8U", "NN9pblrURwaR6AtDjM5WD8LjjAH2oDzgWUm5wdoUs3I2b");

$manager = new Manager();
$manager->connect();
$manager->setCodebird($cb);
$manager->getPostVars();
$manager->csrfProtect();
$manager->checkSemanticErrorInMessage();
echo $manager->execute();
<?php
error_reporting(-1);
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');

require '../vendor/autoload.php';
require '../Database.php';
require '../Manager.php';

\Codebird\Codebird::setConsumerKey("LB8JWqYaEpX09Gmfr76UxhTfS", "CTN8HJ1Z1OLDrEVMdEp4tORqchFuwX85I1FaC72oqyGU9EfgBz");
$cb = \Codebird\Codebird::getInstance();
$cb->setToken("2927004143-bv4Yk0TtIeD3whWMUN3aB9CXt5P3W6fpsa8Bs8U", "NN9pblrURwaR6AtDjM5WD8LjjAH2oDzgWUm5wdoUs3I2b");


$manager = new Manager();
$manager->connect();
$manager->setLimit(5);
$manager->setCodebird($cb);
$manager->setSuccessMessage('Your message will be published in less than %d minute(s)');
$manager->setErrorMessage('Unable to deliver your message, please try again!');
$manager->setHashtagSemanticMessage('You must hashtag at least one object or one place (ex: #Paris)!');
$manager->setAtSemanticMessage('You must define your pseudo at the end your story (ex: @Marc277)!');
$manager->setNoSexMessage('You must specify your gender (ex: @female, @male)!');
$manager->setNoAgeMessage('You must specify your age (ex: @28)!');
$manager->setHasEmptyMessage('You must write yout story in the text field first!');
$manager->getPostVars();
$manager->csrfProtect();
$manager->checkForEmptyMessage();
$manager->checkSemanticErrorInMessage();
echo $manager->execute();
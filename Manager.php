<?php

use \PDO;

class Manager {

    /**
     *
     * @var PDO
     */
    protected $pdo;
    
    public function __construct($dataseParams)
    {
        $database   = $dataseParams['database'];
        $_server    = $dataseParams['server'];
        $username   = $dataseParams['username'];
        $password   = $dataseParams['password'];
        $dns        = "mysql:host=$_server;dbname=$database";
        $this->connect($dns, $username, $password);
    }
    
    public function connect($dns, $username, $password)
    {
        $this->pdo  = new PDO($dns, $username, $password, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        if (!is_object($this->pdo)) {
            throw new Exception("unable to connect to database");
        }

        $limit = 3;       
    }
}

$origin = filter_input(INPUT_SERVER, 'HTTP_ORIGIN');
$method = filter_input(INPUT_POST, 'mdm');

function getTimeBeforePublish($pdo, $selectAllSql, $selectLastSql)
{
    $allNew             = $pdo->prepare($selectAllSql);
    $allNew->execute();
    $newEntriesNumber   = count($allNew->fetchAll());
    
    $lastDuration       = $pdo->prepare($selectLastSql);
    $lastDuration->execute();
    $duration           = $lastDuration->fetch();
    
    $_duration          = '+1 minute';  
    $lastAvailableMessageTime = date("Y-m-d H:i:s") - date("Y-m-d H:i:s", strtotime(date($duration['created_at'])." $_duration"));
    
    
    return $time = ceil((($newEntriesNumber * (60+$lastAvailableMessageTime))/3)/60);
}

if (isset($method)) {
    $message    = filter_input(INPUT_POST, 'mdm');   
    
    $values = array(
        ':message'   => $message,       
        ':createdAt' => date('Y-m-d H:i:s')
    );
    $stmt   = $pdo->prepare($insertSql);
    
    foreach ($values as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    $data   = $stmt->execute();
}

$timeToPublish = getTimeBeforePublish($pdo, $selectAllSql, $selectLastSql);
$messageReturn = "Your message will be posted in approximatively $timeToPublish minute(s)";
$jsonResult = "{'success':'1', 'message':'$messageReturn'}";
$jsonError  = "{'errors':{'name':'Une erreur s'est produite!}}";
echo json_decode($jsonResult);
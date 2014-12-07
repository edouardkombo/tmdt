<?php
$server = array(
    'database' => 'tmdt',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '',
    'verbose' => false
);

$database   = $server['database'];
$_server    = $server['server'];
$username   = $server['username'];
$password   = $server['password'];
$dns        = "mysql:host=$_server;dbname=$database";
$pdo        = new PDO($dns, $username, $password, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

if (!is_object($pdo)) {
    throw new Exception("unable to connect to database");
}

$selectSql      = "SELECT * FROM tmdt.posts WHERE ended_at IS NULL ORDER BY id DESC LIMIT 0,3";
$selectAllSql   = "SELECT id FROM tmdt.posts WHERE ended_at IS NULL ORDER BY id DESC";
$selectLastSql  = "SELECT created_at FROM tmdt.posts WHERE ended_at IS NOT NULL ORDER BY id DESC LIMIT 0,1";
$helpSelectSql  = "SELECT * FROM tmdt.posts ORDER BY id DESC LIMIT 0,3";
$updateSql      = "UPDATE tmdt.posts SET views=:views,shown_at=:shownAt,ended_at=:endedAt WHERE id=:id";
$insertSql      = "INSERT INTO tmdt.posts SET username=:username,message=:message,picture=:picture,url=:url,created_at=:createdAt";

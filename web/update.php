<?php
require '../config.php';

$stmt   = $pdo->prepare($selectSql);
$stmt->execute();
$data   = $stmt->fetchAll();

if (count($data) < 3) {
    $stmt   = $pdo->prepare($helpSelectSql);
    $stmt->execute();
    $data   = $stmt->fetchAll();    
}

foreach ($data as $key => $val) {
    $currentDate    = date('Y-m-d H:i:s');
    
    $duration       = '+2 minutes';  
    $_referenceDate = date("Y-m-d H:i:s", strtotime(date($val['created_at'])." $duration"));
    
    $views          = (integer) $val['views'];
    $views++;
    $referenceDate  = ($currentDate>=$_referenceDate) ? $currentDate : NULL ;    
    $shownAt        = (!empty($val['shown_at'])) ? $val['shown_at'] : $currentDate;
    
    $values = array(
        ':views'   => $views,
        ':shownAt' => $shownAt,
        ':endedAt' => $referenceDate,
        ':id'      => $val['id']
    );
    
    $_stmt   = $pdo->prepare($updateSql);
    foreach ($values as $k => $v) {
        $params = (($k === ':id') || ($k === ':views')) ? PDO::PARAM_INT : PDO::PARAM_STR; 
        $_stmt->bindValue($k, $v, $params);
    }    
    $_stmt->execute();
    
    $data[$key]['views'] = $views;
    $data[$key]['shown_at'] = $shownAt; 
    $data[$key]['ended_at'] = $referenceDate;     
}

print json_encode($data);
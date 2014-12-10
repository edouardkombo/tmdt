<?php
header('Content-Type: text/html; charset=utf-8');

require '../config.php';

$origin = filter_input(INPUT_SERVER, 'HTTP_ORIGIN');
$method = filter_input(INPUT_POST, 'submit');

$allowed = array(
    'https://www.sandbox.paypal.com', 
    'https://www.paypal.com',
    'https://paypal.com',    
);

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
    $message    = filter_input(INPUT_POST, 'message');
    $picture    = filter_input(INPUT_POST, 'picture');
    $username   = filter_input(INPUT_POST, 'username');    
    $url        = filter_input(INPUT_POST, 'url');    
    
    $values = array(
        ':message'   => $message,
        ':username'  => $username,        
        ':picture'   => $picture,
        ':url'       => $url,        
        ':createdAt' => date('Y-m-d H:i:s')
    );
    $stmt   = $pdo->prepare($insertSql);
    
    foreach ($values as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    $data   = $stmt->execute();
    if (is_bool($data) && (true === $data)) {
        header('Location: http://www.themilliondollartalk.com');
    }
}

/*if (!in_array($origin, $allowed)) {
    throw new \Exception('You are not authorized to use this link!');
}*/
?>
<!doctype html>
<html ng-app="TheMillionDollarTalk">
<head>
    <title>The million dollar talk</title>
    <meta name="description" content="The Million Dollar Talk - Share special message with someone special and the whole world. The million dollar talk is a bet between two friends whose the goal was to get one million dollar if they let people share special messages to the world..">
    <meta name="keywords" content="the million dollar talk, million dollar bet, million dollar talk, share messages with the world, share special messages with the world">
    <meta name="author" content="Edouard Kombo"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <!-- jQuery, Angular -->
    <script src="http://code.jquery.com/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="http://code.angularjs.org/1.1.5/angular.js" type="text/javascript"></script>
    <link href='http://fonts.googleapis.com/css?family=Dosis:200,300,400,500,600,700,800' rel='stylesheet' type='text/css'/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,700,600,300' rel='stylesheet' type='text/css'>
    <!-- Bootstrap -->
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet" type="text/css"/>
    <!-- AngularStore app -->
    <script src="js/list/modernizr.custom.js" type="text/javascript"></script>
    <link href="css/font-awesome-4.0.1/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="css/component.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/style_cube.css"/>
</head>
<body>
    <div class="container">
      <div class="container-shadow" style='background-color:#FFFFFF;padding:10px;'>
            <h1 class="well">
                The million dollar talk 
            </h1>
            <div style='padding:10px;'>
                <h4>
                    Write your ephemeral message now, it will be shown in approximatively <?php echo getTimeBeforePublish($pdo, $selectAllSql, $selectLastSql); ?> minute(s)
                </h4>
                <h5 style='background-color:orange;padding:5px;color:#FFFFFF;'>
                    <u>Notice:</u><br/>
                    Be positive, polite, respect international laws and your country laws, share respectful and non pornographic content or you could be banned.<br/>
                    Make sure to alert desired people to visit http://themilliondollartalk.com before submitting this form.
                </h5>
                <form method="POST" action="<?php echo filter_input(INPUT_SERVER, 'PHP_SELF'); ?>">
                    <label for='username'>Your nickname:</label>
                    <input type='text' name='username' maxlength="45"><br/>                    
                    
                    <label for='message'>Your special message (150 car):</label>
                    <input type='text' name='message' maxlength="150"><br/>

                    <label for='picture'>Add a picture url from the web (Only pictures that agrees with international laws):</label>
                    <input type='text' name='picture'><br/>
                    
                    <label for='url'>Add a website url, (Only website content that agrees with international laws):</label>
                    <input type='text' name='url'><br/>                    

                    <input type='submit' class='btn btn-default' name='submit' value='Post my megainfo'>
                </form>
            </div>
       </div>
    </div>

</body>
</html>



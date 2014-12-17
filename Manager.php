<?php

class Manager extends Database{

    /**
     *
     * @var PDO
     */
    protected $pdo;
    
    /**
     *
     * @var string 
     */
    protected $limit;

    /**
     *
     * @var string 
     */
    protected $successMessage;
    
    /**
     *
     * @var string 
     */
    protected $errorMessage;     
    
    /**
     *
     * @var string 
     */
    protected $resultMessage; 
    
    /**
     *
     * @var array 
     */
    protected $resultDatas;
    
    /**
     *
     * @var string 
     */
    protected $postedMessage;     
    
    /**
     *
     * @var string 
     */
    protected $sql;
    
    /**
     *
     * @var boolean 
     */
    protected $error = false;    
    
    /**
     * Constructor
     */
    public function __construct()
    {

    }
    
    /**
     * Connect to database
     * 
     * @throws Exception
     */
    public function connect()
    {
        $database   = $this->databaseParams['database'];
        $_server    = $this->databaseParams['server'];
        $username   = $this->databaseParams['username'];
        $password   = $this->databaseParams['password'];
        
        $dns        = "mysql:host=$_server;dbname=$database";
        $this->pdo  = new \PDO($dns, $username, $password, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        if (!is_object($this->pdo)) {
            throw new \Exception("unable to connect to database");
        }      
    }
    
    /**
     * Set query limit
     * 
     * @param  integer $limit Limit number of query
     * @return integer
     */
    public function setLimit($limit)
    {
        return $this->limit = (integer) $limit;
    }
    
    /**
     * Set success message
     * 
     * @param  string $message Success message
     * @return string
     */
    public function setSuccessMessage($message)
    {
        return $this->successMessage = (string) $message;
    }
    
    /**
     * Set error message
     * 
     * @param  string $message Success message
     * @return string
     */
    public function setErrorMessage($message)
    {
        return $this->errorMessage = (string) $message;
    }    
    
    /**
     * Get POST vars 
     * 
     * @return boolean
     */
    public function getPostVars()
    {
        $this->postedMessage    = filter_input(INPUT_POST, 'mdm');
        $this->action           = filter_input(INPUT_POST, 'action'); 

        return (boolean) true;
    }        
    
    /**
     * Get sql query to retrieve
     * 
     * @param  string  $nature Type of sql to retrieve
     * @param  integer $limit  Limit query
     * @return string
     */
    private function getQuery($nature, $limit = null)
    {
        $sql = '';
        switch($nature) {
            case "basic":
                $sql = "SELECT * FROM tmdt.posts WHERE ended_at IS NULL ORDER BY id DESC LIMIT 0,$limit";
                break;
            case "all":
                $sql = "SELECT * FROM tmdt.posts ORDER BY id DESC LIMIT 0,$this->limit";
                break;            
            case "countNew":
                $sql = "SELECT count(id) as nb FROM tmdt.posts WHERE ended_at IS NULL ORDER BY id DESC";
                break;
            case "update":
                $sql = "UPDATE tmdt.posts SET views=:views,shown_at=:shownAt,ended_at=:endedAt WHERE id=:id";
                break;
            case "insert":
                $sql = "INSERT INTO tmdt.posts SET message=:message,created_at=:createdAt";
                break; 
        }
        return (string) $sql;
    }
    
    /**
     * Return time before message publication message
     * 
     * @param  string $message success message
     * @return string
     */
    private function getTimeBeforePublish()
    {
        $selectNewSql   = $this->getQuery('countNew');
        $selectLastSql  = $this->getQuery('basic', 1);
        
        $allNew             = $this->pdo->prepare($selectNewSql);
        $allNew->execute();
        $newSql             = $allNew->fetch();

        $lastDuration       = $this->pdo->prepare($selectLastSql);
        $lastDuration->execute();
        $duration           = $lastDuration->fetch();

        $_duration          = '+1 minute';  
        $lastAvailableMessageTime = date("Y-m-d H:i:s") - date("Y-m-d H:i:s", 
                strtotime(date($duration['created_at'])." $_duration"));


        $time = ceil((($newSql['nb']*(60+$lastAvailableMessageTime))/3)/60);
        $result = (string) sprintf($this->successMessage, $time);
        
        return (string) $this->resultMessage = $result;
    }    
    
    /**
     * Trigger error
     * 
     * @return boolean
     */
    private function triggerError()
    {
        $this->error         = true;
        $this->resultMessage = $this->errorMessage;
        
        return (boolean) $this->error;
    }
    
    /**
     * Protect form from CSRF attacks
     * 
     * @return boolean
     */
    public function csrfProtect()
    {
        $this->getTimeBeforePublish();
        
        $origin     = filter_input(INPUT_SERVER, 'HTTP_ORIGIN');
        $accepts    = array('http://localhost:8080', 'http://localhost',
            'http://www.themilliondollartalk.com');

        if (!in_array($origin, $accepts) OR ($this->action === NULL)) {
            $this->triggerError();
        }

        return (boolean) $this->error;
    }
    
    /**
     * Execute sql queries and return datas to angular Controller
     */
    public function execute()
    {
        if (false === $this->error) {
            if ($this->action === 'insert') {
                $this->insert();
            } elseif ($this->action === 'update') {
                $this->update();
            }

            $datas = array('success' => array(
                'message'=>$this->resultMessage,
                'datas' => $this->resultDatas
            ));
            
        } else {
            $this->triggerError();
            $datas = array('error' => array(
                'message'   => $this->resultMessage,
                'datas'     => ''
            ));                      
        }
        print json_encode($datas);
    }
    
    /**
     * Insert query
     * 
     * @return array
     */
    private function insert()
    {
        $insertSql  = $this->getQUery('insert');

        $values = array(
            ':message'   => $this->postedMessage,       
            ':createdAt' => date('Y-m-d H:i:s')
        );
        $stmt   = $this->pdo->prepare($insertSql);

        foreach ($values as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $result = $stmt->execute();
        
        if (!$result) {
            $this->triggerError();
        }
        
        return array();
    }
    
    /**
     * Update query
     * 
     * @return array
     */
    private function update()
    {
        $selectSql      = $this->getQuery('basic', 3); 
        $lastThreeSql   = $this->getQuery('all');        
        $updateSql      = $this->getQUery('update');
        
        $stmt   = $this->pdo->prepare($selectSql);
        $stmt->execute();
        $data   = $stmt->fetchAll();

        if (count($data) < 3) {
            $stmt   = $this->pdo->prepare($lastThreeSql);
            $stmt->execute();
            $data   = $stmt->fetchAll();    
        }

        foreach ($data as $key => $val) {
            $currentDate    = date('Y-m-d H:i:s');

            $duration       = '+1 minutes';  
            $_referenceDate = date("Y-m-d H:i:s", strtotime(
                    date($val['created_at'])." $duration"));

            $views          = (integer) $val['views'];
            $views++;
            $referenceDate  = ($currentDate>=$_referenceDate) ? 
                    $currentDate : NULL ;    
            $shownAt        = (!empty($val['shown_at'])) ? 
                    $val['shown_at'] : $currentDate;

            $values = array(
                ':views'   => $views,
                ':shownAt' => $shownAt,
                ':endedAt' => $referenceDate,
                ':id'      => $val['id']
            );

            $_stmt   = $this->pdo->prepare($updateSql);
            foreach ($values as $k => $v) {
                $params = (($k === ':id') || ($k === ':views')) ? 
                        PDO::PARAM_INT : PDO::PARAM_STR; 
                $_stmt->bindValue($k, $v, $params);
            }    
            $_stmt->execute();

            $data[$key]['views'] = $views;
            $data[$key]['shown_at'] = $shownAt; 
            $data[$key]['ended_at'] = $referenceDate;    
        }
        
        return (array) $this->resultDatas = $data;
    }
}
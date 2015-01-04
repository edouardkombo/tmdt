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
    protected $range;
    
    /**
     *
     * @var string 
     */
    protected $lang;
    
    /**
     *
     * @var mixed
     */
    protected $lasts;    
    
    /**
     *
     * @var string 
     */
    protected $allGenders = '[@male|@female|@man|@woman|@girl|@boy|@homme|@femme|@garçon|@fille]'; 
    
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
     * @var object
     */
    protected $codebird;     
    
    /**
     *
     * @var boolean 
     */
    protected $error = false;    
    

    /**
     *
     * @var array 
     */
    protected $jsonDatas = array();    
    
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
     * @param  Object $cb Codebird object
     * @return object
     */
    public function setCodebird($cb)
    {
        return $this->codebird = (object) $cb;
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
        $this->lang             = filter_input(INPUT_POST, 'lang'); 
        $this->range            = filter_input(INPUT_POST, 'range');
        $this->limit            = filter_input(INPUT_POST, 'limit');
        $this->lasts            = filter_input(INPUT_POST, 'lasts');        

        return (boolean) true;
    }        
    
    /**
     * Get sql query to retrieve
     * 
     * @param  string  $nature Type of sql to retrieve
     * @return string
     */
    private function getQuery($nature)
    {
        $sql = '';
        switch($nature) {
            case "select":
                $sql = "SELECT * FROM tmdt.posts WHERE valid=:valid AND lang=:lang ORDER BY id DESC LIMIT $this->limit,$this->range";
                break;
            case "lasts":
                $sql = "SELECT * FROM tmdt.posts WHERE valid=:valid AND lang=:lang AND created_at>=:createdAt ORDER BY id DESC";
                break;             
            case "insert":
                $sql = "INSERT INTO tmdt.posts SET message=:message,created_at=:createdAt,ip=:ip,lang=:lang";
                break; 
            
        }
        return (string) $sql;
    }    
    
    /**
     * Trigger error
     * 
     * @return boolean
     */
    private function triggerError()
    {
        $this->error         = true;
        
        return (boolean) $this->error;
    }
    
    /**
     * Protect form from CSRF attacks
     * 
     * @return boolean
     */
    public function csrfProtect()
    {
        $origin     = filter_input(INPUT_SERVER, 'HTTP_ORIGIN');
        $accepts    = array(
            'http://localhost:8080', 
            'http://localhost:80', 
            'http://localhost', 
            NULL,
            'http://www.themilliondollartalk.com'
        );

        if (!in_array($origin, $accepts) OR ($this->action === NULL)) {        
            $this->triggerError();
        }

        return (boolean) $this->error;
    }    
    
    /**
     * Check semantic error inside messages
     * 
     * @return boolean
     */
    public function checkSemanticErrorInMessage()
    {
        if ((false === $this->error) && !empty($this->postedMessage)) {
            if (!preg_match('[#]',$this->postedMessage)) {;
                $this->error    = true;
            }
            if (!preg_match('[@]',$this->postedMessage)) {                           
                $this->error    = true;
            } else {
                if (!preg_match($this->allGenders, $this->postedMessage)) {                           
                    $this->error    = true;
                }
                if (!preg_match('~@[0-9]~', $this->postedMessage)) {                           
                    $this->error    = true;
                }
                if (!preg_match('~[A-Za-z0-9_-]+@[A-Za-z0-9_-]+\.([A-Za-z0-9_-][A-Za-z0-9_]+)~', $this->postedMessage)) {                           
                    $this->error    = true;
                }              
            }
        }    
        
        if (true === $this->error) {
            $this->jsonDatas = array('error' => array(
                'datas'     => array()
            ));
        }
        
        return (boolean) $this->error;
    }
    
    /**
     * Execute sql queries and return datas to angular Controller
     */
    public function execute()
    {
        if (false === $this->error) {
            ($this->action === 'insert') ? $this->insert() : $this->select();

            if (empty($this->jsonDatas)) {
                $this->jsonDatas = array('success' => array(
                    'datas' => $this->resultDatas
                ));
            }   
        } else {
            $this->triggerError();
            if (empty($this->jsonDatas)) {
                $this->jsonDatas = array('error' => array(
                    'datas'     => array()
                ));
            }
        }

        print json_encode($this->jsonDatas);
    }
    
    /**
     * Insert query
     * 
     * @return array
     */
    private function insert()
    {
        $insertSql  = $this->getQUery('insert');
        $ipAddress  = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        
        $values = array(
            ':message'      => $this->postedMessage,       
            ':createdAt'    => date('Y-m-d H:i:s'),
            ':lang'         => $this->lang,
            ':ip'           => $ipAddress
        );
        
        $stmt   = $this->pdo->prepare($insertSql);

        foreach ($values as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $result = $stmt->execute();
        
        if (!$result) {
            $this->triggerError();
        } else {
            $this->socialNetworksPost();
        }
        
        return array();
    }
    
    /**
     * Post the truncated message on Twitter and Facebook
     * 
     * @return object 
     */
    private function socialNetworksPost()
    {
        $message = $this->truncate($this->postedMessage, 110, '...');
        $params = array(
          'status' => "$message http://goo.gl/76xGNe"
        );
        return $this->codebird->statuses_update($params);        
    }
    
    /**
     * Truncate strings
     * 
     * @param  string  $string String to truncate
     * @param  integer $width  Number of letters to allow
     * @param  string  $etc    text to show at the end
     * @return string
     */
    private function truncate($string, $width, $etc = '...')
    {
        $wrapped = explode('$trun$', wordwrap($string, $width, '$trun$', false), 2);
        return (string) $wrapped[0] . (isset($wrapped[1]) ? $etc : '');
    }    
    
    /**
     * Update query
     * 
     * @return array
     */
    private function select()
    {
        if ($this->lasts !== 'false') {
            $selectSql      = $this->getQuery('lasts'); 

            $values = array(
                ':lang'     => $this->lang,
                ':valid'    => 1,
                ':createdAt'=> date('Y-m-d H:i:s', time() - $this->lasts)
            );            
        } else {
            $selectSql      = $this->getQuery('select'); 

            $values = array(
                ':lang'     => $this->lang,
                ':valid'    => 1,
            );
        }

        $stmt   = $this->pdo->prepare($selectSql);
        foreach ($values as $k => $v) {
            $params = ($k === ':valid') ? PDO::PARAM_INT : PDO::PARAM_STR; 
            $stmt->bindValue($k, $v, $params);
        }         
        
        $stmt->execute();
        $data   = $stmt->fetchAll();

        $emailPattern = '/[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/';
        
        
        foreach ($data as $key => $var) {
            $data[$key]['message'] = preg_replace_callback($emailPattern, function($matches) {
                $segments = explode('@', $matches[0]);
                return $segments[0];           
            }, $var['message']);
        }
        
        return (array) $this->resultDatas = $data;
    }
}

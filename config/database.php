<?php
    
$dbConfig =    array(   
                        'host' => '127.0.0.1',
                        'user' => 'electiongame',
                        'password' => '12345',
                        'db' => 'electiongame_dev1', 
                    );


class Db
{

    private $_host;
    private $_user;
    private $_password;
    private $_db;

    private $_connection = null;

    public function __construct($config) 
    {
        $this->_host = $config['host'];
        $this->_user = $config['user'];
        $this->_password = $config['password'];
        $this->_db = $config['db'];

    } 


    public function getConnection()
    {

        if ( ! isset( $this->_connection ) ) {

            $this->_connection = mysql_connect($this->_host, $this->_user, $this->_password);

            if ( ! $this->_connection ) {
                throw new Exception(__FILE__.": ".__LINE__.": counldn't open the database connection");
            }

            mysql_select_db($this->_db, $this->_connection );

        }

        return $this->_connection;        

    }


    
    public function __destruct()
    {
        if ( isset( $this->_connection) ) {
            mysql_close($this->_connection);
        }
    }


    private function runQuery($sql, array $bind)
    {

        foreach ( $bind as $k=>$v ) {
            $bind[$k] =  mysql_real_escape_string( $v, $this->getConnection() );
        }

        $sql = vsprintf( $sql, $bind );
                
        return mysql_query( $sql, $this->getConnection() );

    }


    public function nonquery($sql,array $bind)
    {
        $result = $this->runQuery( $sql, $bind );

        if ( ! $result ) {
            throw $this->getException( $sql );
        } else {
            return $result;
        }
    }

    public function query($sql,array $bind)
    {

        $result = $this->runQuery( $sql, $bind );

        if ( ! $result ) {
            throw $this->getException( $sql );
        }

        $array = array();
    
        $i = 0;
        while ( $row = mysql_fetch_row( $result, MYSQL_ASSOC ) ) {
            $array[$i++] = $row;
        }

        return $array;

    }

    protected function getException($sql)
    {
        return new Exception ( 'Database error: Sql is '.$sql.", Error is: ".mysql_error());
    }



}

$theDb = new Db( $dbConfig );

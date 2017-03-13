<?php 

	/**
	 * @abstract Phalcon 抽象层读写分离
	 *
	 */
	class MyDb{
	    
	    private static $_readConnection;
	    private static $_writeConnection;
	    private $_dbname;

		/**
		 * \Phalcon\Db\Adapter constructor
		 *
		 * @param integer $suffix DB后缀
		 */
		public function __construct($suffix = 0){ 
		    $this->_dbname = USERDB_PREFIX . $suffix;
		}
		
        private static function _getWriteConnect()
        {
            if(self::$_writeConnection === null){
                self::$_writeConnection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                    "host"      => MASTER_HOST,
                    "username"  => MASTER_USERNAME,
                    "password"  => MASTER_PASSWORD,
                    "dbname"    => $this->_dbname,
                    "options"   => array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES \'".MASTER_CHARSET."\'"
                    )
                ));
            }
            return self::$_writeConnection;
        }
        
        private static function _getReadConnect()
        {
            if(self::$_readConnection === null){
                self::$_readConnection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                    "host"      => SLAVE_HOST,
                    "username"  => SLAVE_USERNAME,
                    "password"  => SLAVE_PASSWORD,
                    "dbname"    => $this->_dbname,
                    "options"   => array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES \'".SLAVE_CHARSET."\'"
                    )
                ));
            }
            return self::$_readConnection;
        }


		public function fetchOne($sqlQuery, $fetchMode=null, $placeholders=null){ 
		    $conn = $this->_getReadConnect();
		    return $conn->fetchOne($sqlQuery, $fetchMode, $placeholders);
		}


		public function fetchAll($sqlQuery, $fetchMode=null, $placeholders=null){ }


		public function insert($table, $values, $fields=null, $dataTypes=null){ }


		public function update($table, $fields, $values, $whereCondition=null, $dataTypes=null){ 
		    $conn = $this->_getWriteConnect();
		    return $conn->update($table, $fields, $values, $whereCondition, $dataTypes);
		}


		public function delete($table, $whereCondition=null, $placeholders=null, $dataTypes=null){ }


		public function limit($sqlQuery, $number){ }

		///...其他方法都需要覆盖一下

	}
	
	//使用
	$db = new MyDb(749);
    $row = $db->fetchOne('SELECT * FROM crowd_pages', \Phalcon\DB::FETCH_ASSOC);
	$db->update(...)
	//....等等

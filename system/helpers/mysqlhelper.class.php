<?php
	//Mysql Helper
	class MysqlHelper{
		private $host;
		private $username;
		private $userpassword;
		private $charset;
		private $appname;
		private $last_sql;
		private $error;
		private $errno;

		function __construct(){
			$this->host = 'localhost';
			$this->appname='www_meilizone_com';
			$this->username = 'root';
			$this->userpassword = 'sasasa';
			$this->charset = 'UTF8';
			$this->port = '3306';
		}
		/*
			@return bool
		*/
		public function runSql($sql){
			$this->last_sql = $sql;
			$dblink = $this->db_write();
			if($dblink === false){
				return false;
			}
			$ret = mysqli_query($dblink,$sql);
			$this->save_error($dblink);
			return $ret;
		}
		/*
			@return array Ê§°Ü·µ»Øfalse
		*/
		public function getData($sql){
			$this->last_sql = $sql;
			$data = array();
			$i = 0;
			$dblink = $this->db_write();
			if($dblink === false){return false;}
			$result = mysqli_query($dblink,$sql);
			$this->save_error($dblink);
			if(is_bool($result)){
				return $result;
			}
			while($Array = mysqli_fetch_array($result,MYSQL_ASSOC)){
				$data[$i++] = $Array;
			}
			mysqli_free_result($result);
			if(count($data)>0){return $data;}
			return NULL;
		}
		/*
			@return array Ê§°Ü·µ»Øfalse
		*/
		public function getLine($sql){
			$data = $this->getData($sql);
			if($data){
				return @reset($data);
			}
			return false;
			
		}
		/*
			@return array Ê§°Ü·µ»Øfalse
		*/
		public function getVar($sql){
			$data = $this->getLine($sql);
			if($data){
				return $data[@reset(@array_keys($data))];
			}
			return false;
		}
		/*
			@return int
		*/
		public function affectedRows($sql){
			$result = mysqli_affected_rows($this->db_write());
			return $result;
		}
		/*
			@return int
		*/
		public function lastId(){
			$result = mysqli_insert_id($this->db_write());
			return $result;
		}
		/**/
		public function closeDb(){
			if(isset($this->db_read)){
				@mysqli_close($this->db_read);
			}
			if(isset($this->db_write)){
				@mysqli_close($this->db_write);
			}
		}
		public function escape($str){
			if(isset($this->db_read)){
				$db = $this->db_read;
			} else if(isset($this->db_write)){
				$db = $this->db_write;
			}else{
				$db = $this->db_read();
			}
			return mysqli_real_escape_string($db,$str);
		}
		/*
			@return string
		*/
		public function error(){
			return $this->error;
		}
		/*
			@return int
		*/
		public function errno(){
			return $this->errno;
		}
		private function save_error($dblink){
			$this->error = mysqli_error($dblink);
			$this->errno = mysqli_errno($dblink);
		}
		private function connect(){
			$db = mysqli_init();
			mysqli_options($db,MYSQLI_OPT_CONNECT_TIMEOUT,5);
			if(!mysqli_real_connect($db,$this->host,$this->username,$this->userpassword,$this->appname,$this->port)){
				$this->error = mysqli_connect_error();
				$this->errno = mysqli_connect_errno();
				return false;
			}
			mysqli_set_charset($db,$this->charset);
			return $db;
		}
		private function db_read(){
			if(isset($this->db_read) && mysqli_ping($this->db_read)){
				return $this->db_read;
			}else{
				$this->db_read = $this->connect();
				return $this->db_read;
			}
		}
		private function db_write(){
			if(isset($this->db_write) && mysqli_ping($this->db_write)){
				return $this->db_write;
			}else{
				$this->db_write = $this->connect();
				return $this->db_write;
			}
		}

	}
?>

<?php 
function prex($name){
	echo "<pre>";
	var_dump($name);
	exit;
}
class mypdo{
	public $host = 'localhost';
	public $username = 'Chang';
	public $password = '12345';
	public $database = 'mrok';
	public $con = null;
	
	function __construct (){
		return $this->db_connect();
	}
	
	public function db_connect(){
		try{
			$db_conn = new PDO('mysql:host='.$this->host.';dbname='.$this->database, $this->username, $this->password);
			$db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$db_conn->query('SET NAMES UTF8');
			$db_conn->query("set time_zone = '+8:00'");
			return $this->con = $db_conn;
		}catch(PDOException $e){
			echo '與資料庫連線發生錯誤!: <br />';
			echo '<pre>';
			print_r($e);
			echo '</pre>';
			$db_conn=false;
		}
		return $db_conn;
	}
	
	public function query($sql,$query_data=[]){
		if($this->con == null){
			$db_conn = $this->db_connect();
		}else{
			$db_conn = $this->con;
		}
		$stmt = $db_conn->prepare($sql);
		
		if($stmt){
			$stmt->execute($query_data);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}else{
			echo "Error：<br>";
			print_r($db_conn->errorInfo());
		}
	}
	
	public function select($db_name,$arr,$where){
		$where_str = "";
		$select_str ="";
		if(!isset($where['AND'])){
			if(!isset($where[0])){
				foreach($where as $key=>$val){
					$where_str = "$key=:$key";
					$query_data[":$key"] = $val;
				}
			}else{
				foreach($where[0] as $key=>$val){
					$where_str = "$key=:$key";
					$query_data[":$key"] = $val;
				}
			}
		}else{
			foreach($where['AND'] as $key=>$val){
				$where_str = $where_str." AND $key=:$key";
				$query_data[":$key"] = $val;
			}
			$where_str = substr($where_str,5);
		}
		
		if(isset($where['ORDER'])){
			$where_str = $where_str." ORDER BY ".$where['ORDER'][0];
		}
		
		if(isset($where['LIMIT'])){
			$limit_str = implode(",",$where['LIMIT']);
			$where_str = $where_str." LIMIT ".$limit_str;
		}
		
		if($arr == "*")$select_str = "*";
		if($arr != "*"){
			foreach($arr as $val){
				$select_str = $select_str.",$val";
			}
			$select_str = substr($select_str,1);
		}
		$sql="SELECT $select_str FROM {$db_name} WHERE $where_str";

		return $this->query($sql,$query_data);
	}
	
	
	public function db_update($sql,$query_data=[]){
		// prex($sql);
		$sql_type = explode(" ",$sql);
		
		if($this->con == null){
			$db_conn = $this->db_connect();
		}else{
			$db_conn = $this->con;
		}
		$stmt = $db_conn->prepare($sql);
		if($sql_type[0] == 'INSERT'){
			if($stmt){
				$stmt->execute($query_data);
				return $db_conn->lastInsertId();
			}else{
				echo "Error：<br>";
				print_r($db_conn->errorInfo());
			}
		}
		if($sql_type[0] == 'UPDATE' || $sql_type[0] == 'DELETE'){
			if($stmt){
				$stmt->execute($query_data);
				$status = $stmt->rowCount();
				if($status != 0){
					return true;
					
				}else{
					return false;
				}
			}else{
				echo "Error：<br>";
				print_r($db_conn->errorInfo());
			}
		}

	}
	

	public function insert($db_name,$arr=[]){
		
		foreach($arr as $key=>$val){
			$field_arr[] = "`{$key}`";
			$value_arr[] = ":".$key;
			$query_data[":$key"] = $val;
		}
		
		$field_str=implode(",",$field_arr);
		$value_str=implode(",",$value_arr);
		$sql = "INSERT INTO {$db_name} ($field_str) values($value_str)";
		
		return $this->db_update($sql,$query_data);
	}
	public function update($db_name,$arr=[],$where_arr=[]){
		$update_str = "";
		$where_str = "";
		foreach($arr as $key=>$val){
			$update_str = $update_str.",$key=:$key";
			$query_data[":$key"] = $val;
		}
		foreach($where_arr as $key=>$val){
			$where_str = $where_str." AND $key='$val'";
		}
		$where_str = substr($where_str, 5);
		$update_str = substr($update_str,1);
		$sql = "UPDATE `{$db_name}` SET $update_str WHERE $where_str";
		
		return $this->db_update($sql,$query_data);
	}
	public function delete($db_name,$arr){
		$where_str = "";
		foreach($arr as $key=>$val){
			$where_str = $where_str." AND $key=:$key";
			$query_data[":$key"] = $val;
		}
		$where_str = substr($where_str, 5);
		$sql = "DELETE FROM {$db_name} WHERE $where_str";
		return $this->db_update($sql,$query_data);
	}
	
	
}


<?php
include("/pdo.php");
$database = new mypdo;
$db_name = "member";
// $where = [
	// "id"	=>	1
// ];
$where = ['AND'=>["id"=>1,"status"=>2],'LIMIT'=>[0,10],'ORDER'=>["id ASC"]];
$arr = ["id","user"];
$data = $database->select($db_name,$arr,$where);



prex($data);
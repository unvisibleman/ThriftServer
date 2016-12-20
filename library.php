<?php

function getArgs(){
	$method = $_SERVER['REQUEST_METHOD'];
	//$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
	//$input = json_decode(file_get_contents('php://input'),true);
//	echo "Method:", $method, " Request:", var_dump($_GET);
	return ['method' => $method, 'args' => $_GET];
}

function connect(){
	$connection = mysql_connect("localhost", "igor", "126709"); // hostname username passwd
	if (!$connection)
		die("Connection error: " . mysql_error());
	$db_select = mysql_select_db("savebudget");
	// mysql_query($connection, 'SET NAMES \'latin1\'');
	if (!$db_select)
		die("DB selection error: " . mysql_error());
	mysql_set_charset('utf8');
	return $connection;
}

function store_token($login, $token){
	$memcache = new Memcache;
	$memcache->connect('localhost', 11211) or die('Memcache error');
	$memcache->set($token, $login, false, 60*60*24); // avalible for one day
}

function get_login($token){
	$memcache = new Memcache;
	$memcache->connect('localhost', 11211) or die('Memcache error');
	return $memcache->get($token);
}

function get_uid($login){
	$res = mysql_query("SELECT id FROM users WHERE login = '$login'");
	$row = mysql_fetch_assoc($res);
	return $row['id'];
}

?>

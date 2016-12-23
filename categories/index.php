<?php

//	echo "categories::";
	include('../library.php');
	$db = connect();
	header('Content-Type: application/json');

	$arg = getArgs();
	switch($arg['method']){
		case 'GET':
//			echo "get::";
			$ret = cat_list($_GET['token']);
			echo json_encode($ret);
			break;
		case 'PUT':
			// reserved - return "userError"
			break;
		case 'POST':
//			echo "add::";
			$ret = cat_add($_POST['name']);
			echo json_encode($ret);
			break;
		case 'DELETE':
//			echo "del::";
			$ret = cat_del($_POST['name']);
			echo json_encode($ret);
			break;
	}


function cat_list($token){
	if($token == '')
		return ['code' => 400, 'data' => "Empty token"];

	$uid = get_uid(get_login($token));
	$dFrom = date("Y.m") . ".1";
	$dTo = date("Y.m") . ".31";
	$req = mysql_query("SELECT * FROM categories");
	if($req){
		$data = array();
		while($line =  mysql_fetch_assoc($req)){
			//$data[] = $line["name"];
			$cat_id = $line["id"];
			$sumQuery = mysql_query("SELECT sum(price) AS s FROM budget WHERE user_id='$uid' AND date>='$dFrom' AND date<='$dTo' AND category_id='$cat_id'");
			if(!sumQuery)
				return ['code' => 500, 'data' => mysql_error()];
			$sum = (mysql_fetch_assoc($sumQuery)["s"]);
			if(!isset($sum)) $sum=0; // не позволять передавать NULL
			$data[] = ['name' => $line["name"], 'sum' => $sum ];
		}
		return ['code' => 200, 'data' => $data];
	}else{
		return ['code' => 500, 'data' => mysql_error()];
	}
}

function cat_add($name){
	// TODO: добавление категории
}

function user_del($name){
	// TODO: удаление категории
}

?>

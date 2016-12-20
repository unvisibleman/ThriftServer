<?php

//	echo "categories::";
	include('../library.php');
	$db = connect();
	header('Content-Type: application/json');

	$arg = getArgs();
	switch($arg['method']){
		case 'GET':
//			echo "get::";
			$ret = cat_list();
			echo json_encode($ret);
			break;
		case 'PUT':
			// reserved - return "userError"
			break;
		case 'POST':
//			echo "add::";
			$ret = cat_add($arg['arg']['name']);
			echo json_encode($ret);
			break;
		case 'DELETE':
//			echo "del::";
			$ret = cat_del($arg['args']['name']);
			echo json_encode($ret);
			break;
	}


function cat_list(){
	$req = mysql_query("SELECT name FROM categories");
	if($req){
		$data = array();
		while($line =  mysql_fetch_assoc($req))
			$data[] = $line;
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

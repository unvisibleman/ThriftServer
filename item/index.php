<?php

//	echo "item::";
	include('../library.php');
	$db = connect();

	$arg = getArgs();
	switch($arg['method']){
		case 'GET':
//			echo "get::";
			$ret = item_get($arg['args']['token'], $arg['args']['m'], $arg['args']['y'], $arg['args']['cat']);
			echo json_encode($ret);
			break;
		case 'PUT':
//			echo "edit::";
			$ret = item_edit($arg['args']['token'], $arg['args']['id'], $arg['args']['price'], $arg['args']['date'], $arg['args']['title'], $arg['args']['group'], $arg['args']['cat']);
			echo json_encode($ret);
			break;
		case 'POST':
//			echo "add::";
			$ret = item_add($arg['args']['token'], $arg['args']['price'], $arg['args']['date'], $arg['args']['title'], $arg['args']['group'], $arg['args']['cat']);
			echo json_encode($ret);
			break;
		case 'DELETE':
//			echo "del::";
			$ret = item_del($arg['args']['token'], $arg['args']['id']);
			echo json_encode($ret);
			break;
	}

// GET http://invisiblelab.tk/api/item/?token=098f6bcd4621d373cade4e832627b4f6&m=12&y=2016&cat=1
function item_get($token, $month, $year, $category){
	if($token=='' || $month=='' || $year=='' || $category=='' ){
        return ['code' => 400, data => 'Not all arguments'];
    }

	$uid = get_uid(get_login($token));
	$dateFrom = $year.'.'.$month.'.1';
	$dateTo = $year.'.'.$month.'.31'; // TODO: узнать, реагирует ли MySQL на месяцы, в кот. <31 дней
	$res = mysql_query("SELECT * FROM budget WHERE user_id='$uid' and date >='$dateFrom' AND date <= '$dateTo'");
	if($res){
		$data = array();
		while($line =  mysql_fetch_assoc($res)) $data[] = $line;
		return ['code' => 200, 'data' => $data];
	}else{
		return ['code' => 500, 'data' => mysql_error()];
	}
}

// PUT http://invisiblelab.tk/api/item/?token=098f6bcd4621d373cade4e832627b4f6&id=7&price=10&date=2016.12.14&title=me&group=0&cat=1
function item_edit($token, $id, $price, $date, $title, $group, $category){
	if($token=='' || $id=='' || $price=='' || $date=='' || $title=='' || $group=='' || $category=='' ){
        return ['code' => 400, data => 'Not all arguments'];
    }

	$uid = get_uid(get_login($token));
	$confimQuery = mysql_query("SELECT * FROM budget WHERE id='$id'");
	$ownerUID = mysql_fetch_assoc($confimQuery)['user_id'];
	if($uid == $ownerUID){
		$res = mysql_query("UPDATE budget SET price='$price', date='$date', title='$title', group_id='$group', category_id='$category' WHERE id='$id'");
		if($res){
			return ['code' => 200];
		}else{
			return ['code' => 500, 'data' => mysql_error()];
		}
	}else{
		return ['code' => 401, 'data' => 'This is not your item'];
	}
}

// POST http://invisiblelab.tk/api/item/?token=098f6bcd4621d373cade4e832627b4f6&price=10&date=2016.12.14&title=tit&group=0&cat=1
function item_add($token, $price, $date, $title, $group, $category){
//	echo $token, $price, $date, $title, $group, $category;
	if($token=='' || $price=='' || $date=='' || $title=='' || $group=='' || $category=='' ){
        return ['code' => 400, data => 'Not all arguments'];
    }
	$uid = get_uid(get_login($token));
	$res = mysql_query("INSERT INTO budget (price, date, title, group_id, category_id, user_id) VALUES ('$price', '$date', '$title', '$group', '$category', '$uid' )");
	if($res){
		return ['code' => 200];
	}else{
		return ['code' => 500, 'data' => mysql_error()];
	}
}

// DELETE http://invisiblelab.tk/api/item/?token=098f6bcd4621d373cade4e832627b4f6&id=5
function item_del($token, $id){
	if($token=='' || $id=='' ){
        return ['code' => 400, data => 'Not all arguments'];
    }
	$uid = get_uid(get_login($token));
	$res = mysql_query("SELECT user_id FROM budget WHERE id='$id'");
	if($res){
		$del = mysql_query("DELETE FROM budget WHERE id='$id'");
		if($del)
			return ['code' => 200];
		else
			return ['code' => 500, 'data' => mysql_error()];
	}else{
		return ['code' => 401, 'data' => 'This is not your item'];
	}

}

?>

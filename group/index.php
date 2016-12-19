<?php

//	echo "group::";
	include('../library.php');
	$db = connect();

	$arg = getArgs();
	switch($arg['method']){
		case 'GET':
//			echo "delUser::";
			$ret = group_deluser($arg['args']['token'], $arg['args']['u']);
			echo json_encode($ret);
			break;
		case 'PUT':
//			echo "addUser::";
			$ret = group_adduser($arg['args']['token'], $arg['args']['u']);
			echo json_encode($ret);
			break;
		case 'POST':
//			echo "create::";
			$ret = group_create($arg['args']['token'], $arg['args']['name']);
			echo json_encode($ret);
			break;
		case 'DELETE':
//			echo "del::";
			$ret = group_del($arg['args']['token']);
			echo json_encode($ret);
			break;
	}

// удалить пользователя из своей группы
// GET http://invisiblelab.tk/api/group/?token=dd97813dd40be87559aaefed642c3fbb&u=rita
function group_deluser($token, $user2del){
	if($token=='' || $user2del=='' ){
        return ['code' => 400, data => 'Not all arguments'];
    }
	$mineUid = get_uid(get_login($token));
	$otherUid = get_uid($user2del);
	$mineGid = mysql_fetch_assoc(mysql_query("SELECT group_id FROM users WHERE id='$mineUid'"))['group_id'];
	$otherGid = mysql_fetch_assoc(mysql_query("SELECT group_id FROM users WHERE id='$otherUid'"))['group_id'];
	
	if( $mineGid != 1 && $mineGid == $otherGid){ // если юзеры состоят в одно не дефолтной группе
		$req = mysql_query("UPDATE users SET group_id=1 WHERE id='$otherUid'"); // кидаем юзера в дефолтную группу
		return ['code' => 200];
	}else{
		return ['code' => 400, 'data' => 'You are not in group'];
	}
}

// добавить пользователя в свою (не дефолтную) группу
// PUT http://invisiblelab.tk/api/group/?token=dd97813dd40be87559aaefed642c3fbb&u=rita
function group_adduser($token, $user2add){
	if($token=='' || $user2add=='' ){
        return ['code' => 400, data => 'Not all arguments'];
    }
	$uid = get_uid(get_login($token));
	$res = mysql_query("SELECT group_id FROM users WHERE id='$uid'");
	$gid = mysql_fetch_assoc($res)['group_id'];
	$uid2add = get_uid($user2add);
	if( $gid != 1 ){ // если группа не дефолтная
		$req = mysql_query("UPDATE users SET group_id='$gid' WHERE id='$uid2add'"); // прписываем его юзеру
		return ['code' => 200];
	}else{
		return ['code' => 400, 'data' => 'You are not in group'];
	}
}

// добавить группу и добавить себя туда
// POST http://invisiblelab.tk/api/group/?token=dd97813dd40be87559aaefed642c3fbb&name=commiFamily
function group_create($token, $name){
	if($token=='' || $name=='' ){
        return ['code' => 400, 'data' => 'Not all arguments'];
    }
	$uid = get_uid(get_login($token));
	$add = mysql_query("INSERT INTO groups (name) VALUES ('$name')"); // создать группу
	if($add){
		$getId = mysql_query("SELECT max(group_id) AS f FROM groups"); // получаем её айдишник
		$groupId = mysql_fetch_assoc($getId)['f'];
		$req = mysql_query("UPDATE users SET group_id='$groupId' WHERE id='$uid'"); // прписываем его юзеру
		return ['code' => 200];
	}else{
		return ['code' => 500, 'data' => mysql_error()];
	}
}

// удалить себя из группы или группу целиком (если в ней кроме себя никого нет)
// DELETE http://invisiblelab.tk/api/group/?token=dd97813dd40be87559aaefed642c3fbb
function group_del($token){
	if($token=='' ){
        return ['code' => 400, 'data' => 'Not all arguments'];
    }
	$uid = get_uid(get_login($token));
	$oneQuery = mysql_query("SELECT count(*) AS f FROM users WHERE group_id=(SELECT group_id FROM users WHERE id='$uid')");
	if($oneQuery){
		$one = mysql_fetch_assoc($oneQuery)['f'];
		if($one==1){ // если юзер в группе один (и это вы), то удаляем её и обновляем группу юзера на дефолтную 1
			$delGroup = mysql_query("DELETE FROM groups WHERE group_id=(SELECT group_id FROM users WHERE id='$uid')");
			$resetGroup = mysql_query("UPDATE users SET group_id=1 WHERE id='$uid'");
			return ['code' => 200];
		} else {
			return ['code' => 400, 'data' => 'You are (not) alone'];
		}
	}else{
		return ['code' => 500, 'data' => mysql_error()];
	}
}

?>

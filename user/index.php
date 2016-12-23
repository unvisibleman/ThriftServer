<?php
	// author: igor
//	echo "user::";
	include('../library.php');
	$db = connect();
	$arg = getArgs();
	switch($arg['method']){
		case 'GET':
//			echo "auth::";
			$ret = user_auth($_GET['login'], $_GET['password']);
			echo json_encode($ret);
			break;
		case 'PUT':
			// reserved - return "userError"
			break;
		case 'POST':
//			echo "add::";
			$ret = user_add($_POST['login'], $_POST['password']);
			echo json_encode($ret);
			break;
		case 'DELETE':
//			echo "del::";
			$ret = user_del($_GET['token']);
			echo json_encode($ret);
			break;
	}


function user_auth($login, $password){
    if($password == '' || $login == ''){
		// Empty login or password
        return ['code' => 400, 'data' => "Empty login or password"];
    }
    $res = mysql_query("SELECT id FROM users WHERE login = '$login' AND password = '$password'");
    if (mysql_num_rows($res) == 1){
        // ok
		$usrToken = md5($login . date());
		store_token($login, $usrToken);
        return ['code' => 200, 'data' => $usrToken ];
    } else {
        // error
        return ['code' => 400, 'data' => 'User not regestered yet' ];
    }
}

function user_add($login, $password){
	if($password == '' || $login == ''){
		// Empty login or password
        return ['code' => 400, 'data' => "Empty login or password"];
    }
    $collision_check=mysql_query("SELECT * FROM users WHERE login='$login'");
    if(mysql_num_rows($collision_check) > 0){
        // 'Пользователь с такими логином существует';
		return ['code' => 409, 'data' => "Already exist user with login ".$login];
    }else{
        $res = mysql_query("INSERT INTO users (login, group_id, password) VALUES ('$login', 1, '$password')");
		if($res){
            // 'Пользователь добавлен';
	        return ['code' => 200];
		} else {
			return ['code' => 500, 'data' => mysql_error($res)];
		}
    }
}

function user_del($token){
	if($token == ''){
        // 'Не указан токен';
        return ['code' => 400];
    }
	$login = get_login($token);
	if($login == ''){
		// токен недействительный
		return ['code' => 400, 'data' => 'Invalid or out-of-date token'];
	}
	$res = mysql_query("DELETE FROM users WHERE login = '$login'");
    if ($res){
        // ok, deleted
        return ['code' => 200];
    } else {
        // 'не удалось удалить'
        return ['code' => 500];
    }
}

?>

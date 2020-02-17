<?php
	// 使用者登入
	include('../../connect.php');
	
	// 定義每天多少錢
	$baseMoney = 500;
	
	// 製造使用者name
	$name = md5('ricktopia' . $_SERVER["REMOTE_ADDR"]);
	
	// 判斷是否使用過此系統
	$sql = 'SELECT money, lastLogin FROM `daily_lottery_user` WHERE name = ?';
	$stmt = $conn -> prepare($sql);
	$stmt -> bind_param('s', $name);
	$stmt -> execute();
	$result = $stmt -> get_result();
	
	// 如果該使用者不在資料庫中, 則新增使用者
	if($result -> num_rows == null) {
		$sql = 'insert into daily_lottery_user values(null, ?, ?, ?)';
		$stmt = $conn -> prepare($sql);
		$stmt -> bind_param('sis', $name, $money, $lastLogin);
		
		$money = $baseMoney;
		$lastLogin = date("Y-m-d");
	} else {
		// 如果該使用者存在, 則判斷是否要增加錢
		$row = $result -> fetch_assoc();
		$lastLogin = $row['lastLogin'];
		$money = $row['money'];
		
		// 計算日期差異
		$diff = date_diff(date_create($lastLogin), date_create(date("Y-m-d")));
		
		// 增加金錢
		$money = $money + $diff->format('%a') * $baseMoney;
		
		// 複寫上次登入時間為現在
		$lastLogin = date("Y-m-d");
		
		$sql = 'UPDATE `daily_lottery_user` SET `money` = ?, `lastLogin` = ? WHERE name = ?';
		$stmt = $conn -> prepare($sql);
		$stmt -> bind_param('iss', $money, $lastLogin, $name);
	}
	$stmt -> execute();
	
	$stmt -> close();
	$conn -> close();
	
	// 回傳所剩金額及name
	$rtnData = array();
	array_push($rtnData, $name);
	array_push($rtnData, $money);
	array_push($rtnData, $_SERVER["REMOTE_ADDR"]);
	
	echo json_encode($rtnData);
?>
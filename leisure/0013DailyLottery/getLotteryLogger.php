<?php
	// 使用者登入
	include('../../connect.php');
	
	$rtnData = array();
	
	// 取得抽獎次數
	$sql = 'SELECT COUNT(*) as ct FROM `daily_lottery_log`';
	$result = $conn -> query($sql);
	$row = $result -> fetch_assoc();
	$count = $row['ct'];
	array_push($rtnData, $count);
	
	// 取得使用者人數
	$sql = 'SELECT MAX(id) as ct FROM `daily_lottery_user`';
	$result = $conn -> query($sql);
	$row = $result -> fetch_assoc();
	$count = $row['ct'];
	array_push($rtnData, $count);
	
	// 查詢log資料
	$sql = 'SELECT * FROM `daily_lottery_log` ORDER BY `money` DESC, `getMoney` LIMIT 50';
	$result = $conn -> query($sql);
	
	$logData = array();
	if($result -> num_rows > 0) {
		while($row = $result -> fetch_assoc()) {
			array_push($logData, array($row['name'], $row['getMoney'], $row['money']));
		}
	}
	array_push($rtnData, $logData);
	
	// 查詢user資料
	$sql = 'SELECT name, money FROM `daily_lottery_user` ORDER BY `money` DESC LIMIT 50';
	$result = $conn -> query($sql);
	
	$userData = array();
	if($result -> num_rows > 0) {
		while($row = $result -> fetch_assoc()) {
			array_push($userData, array($row['name'], $row['money']));
		}
	}
	array_push($rtnData, $userData);
	
	$conn -> close();
	
	echo json_encode($rtnData);
?>
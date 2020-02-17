<?php
	// 取得和設定, 線上人數和歷史人數
	include('./connect.php');
	$rtnData = array();
	
	// 取得使用者ip和當前時間
	$var0 = "0";
	if (!empty($_SERVER["REMOTE_ADDR"]) && count($_SERVER["REMOTE_ADDR"]) <= 20) {
		$var0 = $_SERVER["REMOTE_ADDR"];
	}
	
	date_default_timezone_set("Asia/Taipei");
	$var1 = date("Y-m-d H:i:s");
	
	// 將資料庫內今天前的資料全部清空
	$sql = 'DELETE FROM `visitPeople` WHERE date < "'.mb_substr($var1, 0, 11 , 'utf-8').'00:00:00"';
	$conn -> query($sql);
	
	// 先查詢此人是否已在資料庫中
	$sql = 'SELECT id FROM `visitPeople` WHERE ip = ?';
	$stmt = $conn -> prepare($sql);
	$stmt -> bind_param('s', $var0);
	$stmt -> execute();
	$result = $stmt -> get_result();
	
	// 如果該使用者不在資料庫中, 則新增使用者
	if($result -> num_rows == null) {
		$sql = 'insert into visitPeople values(null, ?, ?)';
		$stmt = $conn -> prepare($sql);
		$stmt -> bind_param('ss', $var0, $var1);
	} else {
		// 如果該使用者存在, 則更新資料庫
		$sql = 'UPDATE `visitPeople` SET `date` = ? WHERE ip = ?';
		$stmt = $conn -> prepare($sql);
		$stmt -> bind_param('ss', $var1, $var0);
	}
	$stmt -> execute();
	
	
	// 取得當前線上人數(整點內)
    $sql = 'SELECT ip FROM `visitPeople` WHERE date > "'.mb_substr($var1, 0, 14 , 'utf-8').'00:00"';
	$result = $conn -> query($sql);
	
	$onlineNum = 0;
	if($result -> num_rows > 0) {
		while($row = $result -> fetch_assoc()) {
			$onlineNum++;
		}
	}
	array_push($rtnData, $onlineNum);
	
	
	// 取得歷史來訪人數(id最大值)
	$sql = "SELECT MAX(id) FROM `visitPeople`";
	$result = $conn -> query($sql);
    $row = $result -> fetch_assoc();
	array_push($rtnData, $row['MAX(id)']);
	
	$stmt -> close();
	$conn -> close();
	
	echo json_encode($rtnData);
?>
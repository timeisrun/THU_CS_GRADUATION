<?php
	// 抽獎抽起來
	
	// 判斷來源
	$userName = $_POST['userName'];
	
	// 製造使用者name
	$name = md5('ricktopia' . $_SERVER["REMOTE_ADDR"]);
	
	// 如果使用者名稱錯誤則return
	if($userName != $name) {
		echo '驗證失敗';
		return;
	}
	
	include('../../connect.php');
	
	// 判斷是否使用過此系統
	$sql = 'SELECT money FROM `daily_lottery_user` WHERE name = ?';
	$stmt = $conn -> prepare($sql);
	$stmt -> bind_param('s', $name);
	$stmt -> execute();
	$result = $stmt -> get_result();
	
	$flag = 0; // 抽獎失敗
	
	// 如果該使用者存在, 則執行抽獎程序
	if($result -> num_rows != null) {
		$row = $result -> fetch_assoc();
		$money = $row['money'];
		
		if($money < 100) {
			$flag = 2; // 金錢不足
			return;
		}
		
		// 2019-04-25 財產過5000萬則機率驟降
		$score = rand(1, $money > 4999999 ? 2000000 : 100000);
		
		if ($score > 49507) {
			$score = 0;
		} else if ($score > 35647) {
			$score = 10;
		} else if ($score > 24427) {
			$score = 50;
		} else if ($score > 11765) {
			$score = 100;
		} else if ($score > 8281) {
			$score = 200;
		} else if ($score > 4359) {
			$score = 500;
		} else if ($score > 2781) {
			$score = 1000;
		} else if ($score > 381) {
			$score = 7887;
		} else if ($score > 132) {
			$score = 30678;
		} else if ($score > 7) {
			$score = 314159;
		} else if ($score > 1) {
			$score = 1000000;
		} else if ($score == 1) {
			$score = 9999999;
		}
		
		// 增加金錢
		$money = $money + $score - 100;
		
		$sql = 'UPDATE `daily_lottery_user` SET `money` = ? WHERE name = ?';
		$stmt = $conn -> prepare($sql);
		$stmt -> bind_param('is', $money, $name);
		$stmt -> execute();
		
		// 寫log
		if($score) {
			$sql = 'insert into daily_lottery_log values(?, ?, ?)';
			$stmt = $conn -> prepare($sql);
			$stmt -> bind_param('sis', $name, $score, date("Y-m-d H:i:s"));
			$stmt -> execute();
		}
		
		$flag = 1; // 抽獎成功
	}
	
	$stmt -> close();
	$conn -> close();
	
	// 回傳
	$rtnData = array();
	array_push($rtnData, $flag);
	array_push($rtnData, $name);
	array_push($rtnData, $score);
	array_push($rtnData, $money);
	array_push($rtnData, $_SERVER["REMOTE_ADDR"]);
	
	echo json_encode($rtnData);
?>
<?php
	// 取得使用總人數
	include('./connect.php');
	$rtnData = array();
	
	$sql = "SHOW TABLE STATUS LIKE 'login_data'";
    $result = $conn -> query($sql);
    $row = $result -> fetch_assoc();
	array_push($rtnData, $row['Auto_increment']-1);
	
	$sql = "SELECT MAX(date) FROM `login_data`";
	$result = $conn -> query($sql);
    $row = $result -> fetch_assoc();
	array_push($rtnData, $row['MAX(date)']);
	
	$conn -> close();
	
	echo json_encode($rtnData);
?>
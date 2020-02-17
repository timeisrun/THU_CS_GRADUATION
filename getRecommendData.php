<?php
	// 取得推薦閱讀資料
	include('./connect.php');
	$rtnData = array();
	
	$sql = "SELECT name, address FROM recommend ORDER BY LENGTH(name)";
    $result = $conn -> query($sql);
	
	if($result -> num_rows > 0) {
		while($row = $result -> fetch_assoc()) {
			$data = array();
			array_push($data, $row["name"]);
			array_push($data, $row["address"]);
			array_push($rtnData, $data);
		}
	}
	
	$conn -> close();
	
	echo json_encode($rtnData);
?>
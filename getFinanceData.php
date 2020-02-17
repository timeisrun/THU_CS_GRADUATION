<?php
	$financeID = $_POST['financeID'];
	
	$data = json_decode(file_get_contents('http://mis.twse.com.tw/stock/api/getStock.jsp?ch='.$financeID.'.tw&json=1'));
	$data = file_get_contents('http://mis.twse.com.tw/stock/api/getStockInfo.jsp?ex_ch='.($data -> msgArray[0] -> key).'&json=1&delay=0&_='.round(microtime(true) * 1000));
	
	echo $data;
?>
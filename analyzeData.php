<?php
	/**
	 * 取得指定標籤前的字串
	 * @param $userData String 使用者資料集
	 * @param $start int 起始位置
	 * @param $target String 目標字串
	 * @return Array {最終結尾, 目標字串}
	 */
	function getHtmlText($userData, $start, $end, $target) {
		$flag = mb_strpos($userData, $target, $start, 'UTF-8');
		
		if(!$flag || $flag > $end) {
			return array($end, '資料錯誤');
		}
		
		$coeff = 1;
		while(true) {
			$start = mb_strpos($userData, '>', $flag-$coeff, 'UTF-8');
			
			if(!$flag || $flag > $end) {
				return array($end, '資料錯誤');
			}
			
			if($start > $flag) {
				$coeff++;
			} else {
				break;
			}
		}
		
		return array($flag+1, mb_substr($userData, $start+1, $flag-$start-1 , 'utf-8'));
	}
	
	/**
	 * 課程名稱比較
	 * @param $mainStr String 比較字串A
	 * @param $subStr String 比較字串B
	 * @return Boolean 回傳是否相符
	 */
    function cmpClassName($mainStr, $subStr) {
        // 如果完全相等
        if(strcmp($mainStr, $subStr) == 0) {
			return true;
		}
		
        // 如果是體育課(完全相符或是部分相符)
		$isSports = strcmp('大一體育', $subStr) == 0 || strcmp('大二體育', $subStr) == 0;
        $isSpartsPart = mb_strpos('0'.$mainStr, $subStr, 0, 'UTF-8') != null;
		if($isSports && $isSpartsPart) {
			return true;
		}

        // 以下列舉名稱為相同之課程
        $enumeration = array(
            array('微積分Ｂ', '微積分'),
            array('微積分Ｂ【上】', '微積分'),
            array('微積分Ｂ【下】', '微積分'),
            array('微積分Ｃ', '微積分'),
            array('微積分乙〈一〉', '微積分'),
            array('微積分乙〈二〉', '微積分'),
            array('微積分甲〈一〉', '微積分'),
            array('微積分甲〈二〉', '微積分'),
            array('JAVA程式設計', 'JAVA 程式設計'),
            array('JAVA程式設計與實作', 'Java程式設計與實作'),
            array('JAVA程式設計與實作', 'JAVA 程式設計'),
            array('JAVA程式設計與實作', 'JAVA 程式設計與實作'),
            array('C程式設計', 'C 程式設計'),
            array('C程式設計與實作', 'C 程式設計與實作'),
            array('Web程式設計', 'WEB程式設計'),
            array('全民國防教育軍事訓練–國防政策', '軍訓'),
            array('全民國防教育軍事訓練–防衛動員', '軍訓'),
            array('中文：中國文學選讀', '中文'),
            array('中文：文學欣賞與實用', '中文'),
            array('中文：古典散文選讀', '中文'),
            array('中文：現代文學閱讀與寫作', '中文'),
            array('中文：古典小說選讀', '中文'),
            array('中華文化的發展', '歷史'),
            array('近代世界的形成', '歷史'),
            array('業界實習一', '專題實作'),
            array('程式設計檢定（CPE）', '程式設計檢定(CPE)'),
            array('超大型積體電路(VLSI)設計導論', 'VLSI設計導論(超大型積體電路(VLSI)設計導論)')
        );

        for($i=0; $i<count($enumeration); $i++) {
            if(strcmp($mainStr, $enumeration[$i][0]) == 0 && strcmp($subStr, $enumeration[$i][1]) == 0) {
				return true;
			}
        }
		
        return false;
    }
	
	/**
	 * 判斷是否為系選修
	 * @param $mainStr String 比較字串
	 * @return Boolean 回傳結果
	 */
	 function cmpSeries($mainStr) {
        $enumeration = array(
            '物件導向分析與設計',
            '軟體專案管理',
            'WEB程式設計',
            '使用者經驗',
            '軟體生命週期管理',
            '企業計算',
            '軟體工程實習',
            '人機介面設計',
            '軟體架構',
            '雲端計算',
            '無線感測網路',
            '服務創新',
            '社群網路',
            '資料庫',
            '影像視訊處理應用',
            '機率統計',
            '行動裝置系統與應用',
            'VLSI設計導論',
            '嵌入式系統設計與實作',
            '數位訊號處理導論',
            '嵌入式系統與軟體工程',
            'FPGA系統設計與應用',
            '資訊工程職涯發展',
            '創業精神與行動',
            '數位控制元件與應用',
            '物聯網與感測',
            '工程數學',
            '嵌入式系統設計',
            '驅動程式設計',
            '自由開源軟體',
            '軟式計算',
            '進階資料結構',
            '3D列印實作',
            '微算機系統',
            'AWS雲端安全實務',
            'AWS雲端平台系統實務',
            '資料分析與軟體應用',
            '網路資訊行為',
            '應用密碼學',
            '組合數學',
            '網路安全原理與實務',
            '計算機組織',
            '無線網路技術與應用',
            '嵌入式系統導論',
            '硬體描述語言設計與模擬',
            '系統分析與設計',
            '進階邏輯設計'
        );

        for($i=0; $i<count($enumeration); $i++) {
            if(strcmp($mainStr, $enumeration[$i]) == 0) {
				return true;
			}
        }
        return false;
    }
	
	// ----------程式開始執行----------
	
	try {
		// 先檢核使用者的輸入
		$choiceYear = $_POST['choiceYear'];
		$choiceTeam = $_POST['choiceTeam'];
		$userData = $_POST['userData'];
		
		if ($userData == null) {
			echo json_encode('【未貼上成績資料, 請重新輸入!】');
			return;
		}
		
		// 爬取資工網頁必選必修科目表
		$getPage = 0;
		switch($choiceYear) {
			case 101: $getPage = 37; break;
			case 102: $getPage = 40; break;
			case 103: $getPage = 43; break;
			case 104: $getPage = 152; break;
			case 105: $getPage = 266; break;
			case 106: $getPage = 275; break;
			case 107: $getPage = 281; break;
			case 108: $getPage = 287;
		}
		$csList = file_get_contents('http://www.cs.thu.edu.tw/web/class1/detail.php?cid=1&id='.($getPage+$choiceTeam));
		
		if(!$csList) {
			throw new Exception('東海資工網頁掛掉，取得必選必修科目表失敗');
		}
		
		// ----------分解資工必選必修科目表----------
		$startFlag = mb_strpos($csList, 'table table-bordered table-hover', 0, 'UTF-8');
		$endFlag = mb_strpos($csList, 'fancybox-button', $startFlag, 'UTF-8');
		
		// 將必選必修切成不同寬度的陣列
		$csData = array();
		while(true) {
			$row = array();
			$trFlag = mb_strpos($csList, '</tr>', $startFlag, 'UTF-8');
			while($trFlag) {
				$tdPos = mb_strpos($csList, '</td>', $startFlag, 'UTF-8');
				if(!$tdPos || $tdPos > $trFlag || $tdPos > $endFlag) {
					break;
				}
				$data = getHtmlText($csList, $startFlag, $endFlag, '</td>');
				$startFlag = $data[0];
				array_push($row, $data[1]);
			}
			if($startFlag >= $endFlag || !$trFlag) {
				break;
			}
			
			$startFlag = $trFlag + 1;
			array_push($csData, $row);
		}
		
		// 整理不同寬度的陣列
		$index = 0;
		$onesCount = 0;
		$csFinishData = array();
		$tempData = array();
		for($i=0; $i<4;) {
			$index++;
			// 如果為1個項目, 跳過, 並將計數加1
			if(count($csData[$index]) == 1 || strcmp($csData[$index][0], '人文學科') == 0 || strcmp($csData[$index][0], '社會學科') == 0 || strcmp($csData[$index][0], '自然學科') == 0 || strcmp($csData[$index][0], '文明與經典領域學科') == 0) {
				$onesCount++;
				if($onesCount == 7 ||$onesCount == 8 || $onesCount == 9 || $onesCount == 10) {
					array_push($tempData, $csFinishData);
					$i++;
					$csFinishData = array();
					
					// 當第一列放置時, 需要增加通識陣列空位
					if($i == 1) {
						array_push($tempData, array());
					}
				}
			} else if(count($csData[$index]) == 3) {
				// 這邊邏輯很難搞主要是為了一個課程有上下學期的複製程序
				if(mb_strpos('a'.$csData[$index][1], '0', 0, 'UTF-8')) {
					if(mb_strpos('a'.$csData[$index][1], '-', 0, 'UTF-8')) {
						array_push($csFinishData, array($csData[$index][0], $csData[$index][2]));
					} else {
						array_push($csFinishData, array($csData[$index][0], $csData[$index][2]));
						array_push($csFinishData, array($csData[$index][0], $csData[$index][2]));
					}
				} else {
					if(mb_strpos('a'.$csData[$index][1], '-', 0, 'UTF-8')) {
						// 此處符合會需要讓學分/2
						array_push($csFinishData, array($csData[$index][0], $csData[$index][2]/2));
						array_push($csFinishData, array($csData[$index][0], $csData[$index][2]/2));
					}
				}
			} else {
				if(mb_strpos('a'.$csData[$index][2], '0', 0, 'UTF-8')) {
					if(mb_strpos('a'.$csData[$index][2], '-', 0, 'UTF-8')) {
						array_push($csFinishData, array($csData[$index][0], $csData[$index][3]));
					} else {
						array_push($csFinishData, array($csData[$index][0], $csData[$index][3]));
						array_push($csFinishData, array($csData[$index][0], $csData[$index][3]));
					}
				} else {
					if(mb_strpos('a'.$csData[$index][2], '-', 0, 'UTF-8')) {
						// 此處符合會需要讓學分/2
						array_push($csFinishData, array($csData[$index][0], $csData[$index][3]/2));
						array_push($csFinishData, array($csData[$index][0], $csData[$index][3]/2));
					}
				}
			}
		}
		
		// 取出殘餘有用資料
		$SRNumber = array();
		array_push($SRNumber, $csData[count($csData)-3][1]); // 必修學分
		for($i=5; $i<count($csData); $i++) {
			if(strcmp($csData[$i][0], '人文學科') == 0) {
				array_push($SRNumber, $csData[$i][2]); // 通識學分
				break;
			}
		}
		array_push($SRNumber, $csData[count($csData)-2][1]); // 選修學分
		array_push($SRNumber, mb_substr($csData[count($csData)-2][2], 15, 2, 'UTF-8')); // 系選修
		array_push($SRNumber, mb_substr($csData[count($csData)-2][2], 22, 2, 'UTF-8')); // 分組必選與選修
		array_push($SRNumber, $csData[count($csData)-1][1]); // 總學分數
			
		// 最後完成的必選必修
		$csData = $tempData;
		
		// 增加系選修, 共選修和無家可歸
		array_push($csData, array());
		array_push($csData, array());
		array_push($csData, array());
		
		// 擴增csData欄位(名稱, 學分, 對應ID)
		for($i=0; $i<count($csData); $i++) {
			for($j=0; $j<count($csData[$i]); $j++) {
				array_push($csData[$i][$j], -1);
			}
		}
		
		
		// ----------分解使用者成績總表----------
		$startFlag = mb_strpos($userData, 'breadcrumb position-right', 0, 'UTF-8');
		$endFlag = mb_strpos($userData, 'well', $startFlag, 'UTF-8');
		
		// 擷取使用者學號, 姓名, 組別
		$data = getHtmlText($userData, $startFlag, $endFlag, '</li>');
		$startFlag = $data[0];
		$userId = $data[1];
		
		$data = getHtmlText($userData, $startFlag, $endFlag, '</li>');
		$startFlag = $data[0];
		$userName = $data[1];
		
		$data = getHtmlText($userData, $startFlag, $endFlag, '</li>');
		$startFlag = $data[0];
		$userTeam = $data[1];
		
		// 開始拆解html
		$startFlag = mb_strpos($userData, 'no-more-tables', $startFlag, 'UTF-8');
		$allData = array();
		while(true) {
			// 因為成績總表為6成績一欄, 故跑6次
			// 2020-01-06 學生資訊系統突然改成7列, 多了GPA
			$row = array();
			for($i=0; $i<7; $i++) {
				$data = getHtmlText($userData, $startFlag, $endFlag, '</td>');
				$startFlag = $data[0];
				array_push($row, $data[1]);
			}
			if($startFlag >= $endFlag) {
				break;
			}
			
			array_push($allData, $row);
		}
		
		// 取得勞作成績
		$data = getHtmlText($userData, $startFlag, mb_strlen($userData), '</p>');
		$startFlag = $data[0];
		$workData = $data[1];
		
		
		// 資料比較
		for($i=0; $i<count($allData); $i++) {
			$keepFlag = true;
			for($j=0; $j<5 && $keepFlag; $j++) {
				for($k=0; $k<count($csData[$j]) && $keepFlag; $k++) {
					// 如果名稱欄位空白則跳過
					if($csData[$j][$k][0] == '') {
						continue;
					}
					
					if(cmpClassName($allData[$i][3], $csData[$j][$k][0])) {
						// 如果該符合課程未匹配過
						if($csData[$j][$k][2] == -1) {
							$csData[$j][$k][2] = $i;
							$keepFlag = false;
						} else {
							// 如果重複資料之下一列為同樣資料, 則跳至csData下一列
							if($k+1 < count($csData[$j]) && cmpClassName($allData[$i][3], $csData[$j][$k+1][0])) {
								continue;
							}
							
							// 如果該符合課程及格
							$score = $allData[$i][5];
							if($score == '通過' || $score == '抵免' || $score > 59) {
								// 原本資料丟入無家可歸
								array_push($csData[7], array('', '', $csData[$j][$k][2]));
								// 將本筆資料放入表格
								$csData[$j][$k][2] = $i;
							} else {
								// 不及格丟入無家可歸
								array_push($csData[7], array('', '', $i));
							}
							$keepFlag = false;
						}
					}
				}
			}
			
			// 通識課程判斷
			$score = $allData[$i][5];
			$className = 'a'.$allData[$i][3];
			if ($keepFlag && (mb_strpos($className, '人文', 0, 'UTF-8') || mb_strpos($className, '社會', 0, 'UTF-8') || mb_strpos($className, '自然', 0, 'UTF-8') || mb_strpos($className, '文明與經典', 0, 'UTF-8'))) {
				// 不及格的通識丟無家可歸
				if($score == '通過' || $score == '抵免' || $score > 59) {
					array_push($csData[1], array('', '', $i));
					$keepFlag = false;
				}
			}
			
			// 系選修判斷
			if($keepFlag && cmpSeries($allData[$i][3])) {
				// 不及格的通識丟無家可歸
				if($score == '通過' || $score == '抵免' || $score > 59) {
					array_push($csData[5], array('', '', $i));
					$keepFlag = false;
				}
			}
			
			// 及格之課程放置於共選修內
			if($keepFlag && ($score == '通過' || $score == '抵免' || $score > 59)) {
				array_push($csData[6], array('', '', $i));
				$keepFlag = false;
			}
			
			// 都不匹配則放入無家可歸
			if($keepFlag) {
				array_push($csData[7], array('', '', $i));
			}
		}
		
		// userTeam更改成中文
		switch($choiceTeam) {
			case 0: $choiceTeam = '數創組'; break;
			case 1: $choiceTeam = '資電組'; break;
			case 2: $choiceTeam = '軟工組'; break;
		}
		
		// 儲存使用者資料進資料庫
		include('./connect.php');

		$sql = 'insert into login_data values(null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
		$stmt = $conn -> prepare($sql);
		$stmt->bind_param('ssssssssssss', $userId, $userName, $userTeam, $var0, $var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8);

		date_default_timezone_set("Asia/Taipei");
		$var0 = date("Y-m-d H:i:s");
		$val1 = null;
		$val2 = null;
		$val3 = null;
		$val4 = null;
		$val5 = null;
		$val6 = null;
		$val7 = null;
		$val8 = null;

		if (!empty($_SERVER["HTTP_CLIENT_IP"]) && count($_SERVER["HTTP_CLIENT_IP"]) <= 40) $var1 = $_SERVER["HTTP_CLIENT_IP"];
		if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]) && count($_SERVER["HTTP_X_FORWARDED_FOR"]) <= 40) $var2 = $_SERVER["HTTP_X_FORWARDED_FOR"];
		if (!empty($_SERVER["HTTP_X_FORWARDED"]) && count($_SERVER["HTTP_X_FORWARDED"]) <= 40) $var3 = $_SERVER["HTTP_X_FORWARDED"];
		if (!empty($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"]) && count($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"]) <= 40) $var4 = $_SERVER["HTTP_X_CLUSTER_CLIENT_IP"];
		if (!empty($_SERVER["HTTP_FORWARDED_FOR"]) && count($_SERVER["HTTP_FORWARDED_FOR"]) <= 40) $var5 = $_SERVER["HTTP_FORWARDED_FOR"];
		if (!empty($_SERVER["HTTP_FORWARDED"]) && count($_SERVER["HTTP_FORWARDED"]) <= 40) $var6 = $_SERVER["HTTP_FORWARDED"];
		if (!empty($_SERVER["REMOTE_ADDR"]) && count($_SERVER["REMOTE_ADDR"]) <= 40) $var7 = $_SERVER["REMOTE_ADDR"];
		if (!empty($_SERVER["HTTP_VIA"]) && count($_SERVER["HTTP_VIA"]) <= 40) $var8 = $_SERVER["HTTP_VIA"];
		
		$stmt->execute();
		
		$rtnData = array();
		array_push($rtnData, 'success');
		array_push($rtnData, $userId);
		array_push($rtnData, $userName);
		array_push($rtnData, $userTeam);
		array_push($rtnData, $choiceYear);
		array_push($rtnData, $choiceTeam);
		array_push($rtnData, $workData);
		array_push($rtnData, $csData);
		array_push($rtnData, $allData);
		array_push($rtnData, $SRNumber);
		
		$stmt -> close();
		$conn -> close();
		
		echo json_encode($rtnData);
	} catch (Exception $e) {
		echo json_encode($e->getMessage());
	}
?>
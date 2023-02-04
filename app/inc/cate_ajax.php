<?
	//header("Content-Type: text/html; charset=ecu-kr");
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");	
	
	$code = $_POST['code'];
	$type = $_POST['type'];
	
	$query = "";
	if(!empty($code) && strlen($code) >= 12){
			$depth = 0;
			for($i=0;$i<4;$i++){
				$codeSate = substr($code,3*$i,3);
				
				if($codeSate != "000"){
					$query .= 'code'.chr(65+$i).'='.$codeSate;
					$depth++;
				}else{
					if($depth == $i){
						$query .= 'code'.chr(65+$i).'!='.$codeSate;
					}else{
						$query .= 'code'.chr(65+$i).'='.$codeSate;
					}

				}
				if($i!=3){
					$query .= " AND ";
				}
			}
		}
	
	$cateSql = "SELECT code_name,codeA,codeB,codeC,codeD,type FROM tblproductcode WHERE ";
	$cateSql .= $query;
	$cateSql .= " AND type NOT LIKE 'S%' and group_code != 'NO' ";
	$cateSql .= " ORDER BY sequence DESC";
	if($type != "p"){
		$cateSql .= " LIMIT 0, 4 ";
	}
	if(false !== $cateRes = mysql_query($cateSql)){
		$cateNums = mysql_num_rows($cateRes);
		$returnMsg ="";
		if($cateNums > 0){
			while($cateRow = mysql_fetch_object($cateRes)){
				//if(preg_match("/X/i",$cateRow->type)){
				$returnCode = $cateRow->codeA.$cateRow->codeB.$cateRow->codeC.$cateRow->codeD;
				$returnMsg .=  "<li><a href=\"./productlist.php?code=".$returnCode."\">";
				$returnMsg .= $cateRow->code_name;
				$returnMsg .= "</a></li>";
			//	}
			}
		}
	}
	
	echo rawurlencode( iconv("CP949", "UTF-8", $returnMsg));
?>

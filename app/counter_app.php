<?
//$Dir="../";
//include_once($Dir."lib/init.php");
//include_once($Dir."lib/lib.php");

function getUnescape($str){
	return urldecode(preg_replace_callback('/%u([[:alnum:]]{4})/', 'UnescapeFunc', $str));
}

function UnescapeFunc($str){
	return mb_convert_encoding(chr(hexdec(substr($str[1], 2, 2))).chr(hexdec(substr($str[1],0,2))),"UHC","UTF-16LE");
}

$ref=$_REQUEST["ref"];
$url=$_ShopInfo->getShopurl();

/*
if(strlen(getenv("HTTP_REFERER"))==0) { 
	echo "사이트에 링크하셔야 나옵니다.";
	exit; 
}
*/

$cookie_set = $_COOKIE["shop_counter"];

$cookie_set_N = $_COOKIE["shop_counter_N"];
$N_date = date("Ymd");

$date = date("YmdH");
$date2 = substr($date,0,8);
$date3 = substr($date,0,6);
echo $cookie_set;
//1일 접속자
if (!$cookie_set) {
	//금일 확인
	//tblcontact_ip
	
	//금일 이전 자료는 삭제
	$sql_del = "DELETE FROM tblcontact_ip WHERE date != '".$N_date."' ";
	mysql_query($sql_del,get_db_conn());
	
	//금일 자료 업데이트
	$ip_k = $_SERVER['REMOTE_ADDR'];
	$sql_ip = "SELECT count(*) as cnt FROM tblcontact_ip WHERE date = '".$N_date."' and ip = '".$ip_k."' ";
	$result_ip = mysql_query($sql_ip,get_db_conn());
	if($row_ip = mysql_fetch_object($result_ip)) {
		$ip_cnt = (int)$row_ip->cnt;
	}
	if($ip_cnt == 0){
		//IP 추가
		mysql_query("INSERT INTO tblcontact_ip (date,ip) VALUES ('".$N_date."', '".$ip_k."')",get_db_conn());

		//INSERT  or UPDATE
		//tblcounter_contact
		$sql_date = "SELECT count(*) as cnt FROM tblcounter_contact WHERE date = '".$N_date."' ";
		$result_date = mysql_query($sql_date,get_db_conn());
		if($row_date = mysql_fetch_object($result_date)) {
			$date_cnt = (int)$row_date->cnt;
		}
		if($date_cnt == 0){
			mysql_query("INSERT INTO tblcounter_contact (date,cnt) VALUES ('".$N_date."',1)",get_db_conn());
		}else{
			mysql_query("UPDATE tblcounter_contact SET cnt=cnt+1 WHERE date='".$N_date."' ",get_db_conn());
		}

		//쿠키추가
		setcookie("shop_counter_N",$N_date,0,"/".RootPath);
	}
}
//1일 접속자
mysql_query("INSERT INTO tblcounter (date,pagecnt) VALUES ('".$date."',1)",get_db_conn());

if (mysql_errno()==1062) {
	mysql_query("UPDATE tblcounter SET pagecnt=pagecnt+1 WHERE date='".$date."' ",get_db_conn());
}

if ($cookie_set != $date) {
	// 시간별 카운트
	mysql_query("INSERT INTO tblcounter (date,pagecnt) VALUES ('".$date."',1)",get_db_conn());
	if (mysql_errno()==1062) {
		mysql_query("UPDATE tblcounter SET cnt=cnt+1 WHERE date='".$date."' ",get_db_conn());
	}
	setcookie("shop_counter",$date,0,"/".RootPath);
}

// 페이지뷰 설정
$page = ereg_replace("http://","",getenv("HTTP_REFERER"));
if (strpos($page,"/")!=0) {
	$pageurl = substr($page,0,strpos($page,"/"));
	$pageview = substr($page,strpos($page,"/")+1);
	// 상품코드 추출 
	if (strpos($page,"productdetail.php")>0 && strpos($pageview,"productcode=")>0) {
		$shopdetail = $pageview;
		$pos = strpos($shopdetail,"productcode=")+12;
		$productcode = substr($shopdetail,$pos,18);
		if (strlen($productcode)>12) {
			mysql_query("INSERT INTO tblcounterproduct (date,productcode) VALUES ('".$date2."','".$productcode."')",get_db_conn());
			if (mysql_errno()==1062) {
				mysql_query("UPDATE tblcounterproduct SET cnt=cnt+1 WHERE date='".$date2."' AND productcode='".$productcode."'",get_db_conn());
			}
		}
	} else if (strpos($page,"productlist.php")>0 && strpos($pageview,"code=")>0) {
		$codelink = $pageview;
		$pos = strpos($codelink,"code=")+5;
		$str = substr($codelink,$pos,12);
		$code="";
		for($i=0;$i<strlen($str);$i++){
			if("0"<=$str[$i] && $str[$i]<="9") $code.=$str[$i];
		}

		if (strlen($code)==3 || strlen($code)==12) {
			if(strlen($code)==3) $code.="000000000";
			mysql_query("INSERT INTO tblcountercode (date,code) VALUES ('".$date2."','".$code."')",get_db_conn());
			if (mysql_errno()==1062) {
				mysql_query("UPDATE tblcountercode SET cnt=cnt+1 WHERE date='".$date2."' AND code='".$code."' ",get_db_conn());
			}
		} 
	} else if (strpos($page,"productsearch.php")>0 && strpos($pageview,"search=")>0) {
		//검색어
		$shopdetail = $pageview;
		$pos = strpos($shopdetail,"search=")+7;
		if ($pos>7) {
			$shopdetail = substr($shopdetail,$pos);
			if(strpos($shopdetail,"&")!=false) {
				$pos = strpos($shopdetail,"&");
				$search = substr($shopdetail,0,$pos);
			} else {
				$search = $shopdetail;
			}
			if (strlen($search)>0) {
				$search=urldecode($search);
				mysql_query("INSERT INTO tblcounterkeyword (date,search) VALUES ('".$date2."','".$search."')",get_db_conn());
				if (mysql_errno()==1062) {
					mysql_query("UPDATE tblcounterkeyword SET cnt=cnt+1 WHERE date='".$date2."' AND search='".$search."'",get_db_conn());
				}
			}
		}
	}
	if (strpos($pageview,"?")!=false) $pageview = substr($pageview,0,strpos($pageview,"?"));
}

// 페이지뷰체크
if (strlen($pageview)>0) {
	mysql_query("INSERT INTO tblcounterpageview (date,page) VALUES ('".$date2."','".$pageview."')",get_db_conn());
	if (mysql_errno()==1062) {
		mysql_query("UPDATE tblcounterpageview SET cnt=cnt+1 WHERE date='".$date2."' AND page='".$pageview."'",get_db_conn());
	}

	// 주문체크
	if (strpos($pageview,"orderend.php")>0) {
		mysql_query("INSERT INTO tblcounterorder (date) VALUES ('".$date."')",get_db_conn());
		if (mysql_errno()==1062) {
			mysql_query("UPDATE tblcounterorder SET cnt=cnt+1 WHERE date='".$date."'",get_db_conn());
		}
	}
}
// 링크URL체크
if (strlen(trim($ref))>0 && strpos($ref,$url)===false) {
	$ref = preg_replace('!^(http[s]?://)!','',$ref);
	$searchword = preg_replace('!^(http[s]?://)!','',$searchword);

	if (strpos($ref,"/")!=0) $ref = substr($ref,0,strpos($ref,"/"));

	$result = mysql_query("SELECT COUNT(*) as cnt FROM tblcountersearchdomain WHERE domain='".$ref."'",get_db_conn());
	$row=mysql_fetch_object($result);
	$cnt = $row->cnt;
	mysql_free_result($result);

	// 검색엔진사이트일경우
	if ($cnt==1)  {
		mysql_query("INSERT INTO tblcountersearchengine (date,domain) VALUES ('".$date2."','".$ref."') ",get_db_conn());
		if (mysql_errno()==1062) {
			mysql_query("UPDATE tblcountersearchengine SET cnt=cnt+1 WHERE date='".$date2."' AND domain='".$ref."'",get_db_conn());
		}

		switch ($ref) {
			case "kr.search.yahoo.com":
				$searchquery="p=";
				break;
			case "search.naver.com":
			case "web.search.naver.com":
			case "searchplus.nate.com":
			case "search.nate.com":
			case "search.hanafos.com":
				$searchquery="query=";
				break;
			case "search.korea.com":
			case "search.freechal.com":
				$searchquery="query=";
				break;
			case "search.paran.com":
				$searchquery="Query=";
				break;
			default :
				$searchquery="q=";
				break;
		}

		if (strpos($searchword,$searchquery)>0) {
			//if (($ref=="www.google.co.kr" && strpos($searchword,"UTF-8")>0) || ($ref=="search.msn.co.kr" && strpos($searchword,"MSNH")<=0)){
			if ($ref=="www.google.co.kr" || ($ref=="search.msn.co.kr" && strpos($searchword,"MSNH")<=0) || ($ref=="search.daum.net" && strpos($searchword,"utf8")>0) || ($ref=="kr.search.yahoo.com" && strpos($searchword,"kr-search_top")>0)){
				$searchword=@mb_convert_encoding(urldecode($searchword),"utf-8","auto");
			}
			$searchword = trim(urldecode(substr($searchword,strpos($searchword,$searchquery)+strlen($searchquery))));
			if (strpos($searchword,"&")>0) $searchword = substr($searchword,0,strpos($searchword,"&"));
			if (strlen($searchword)>0) {
				if($ref=="search.naver.com" || $ref=="web.search.naver.com") {
					$searchword = getUnescape($searchword);
				}
				mysql_query("INSERT INTO tblcountersearchword (date,domain,search) VALUES ('".$date3."','".$ref."','".$searchword."') ",get_db_conn());
				if (mysql_errno()==1062) {
					mysql_query("UPDATE tblcountersearchword SET cnt=cnt+1 WHERE date='".$date3."' AND domain='".$ref."' AND search='".$searchword."'",get_db_conn());
				}
			}
		}
	} else {
		mysql_query("INSERT INTO tblcounterdomain (date,domain) VALUES ('".$date2."','".$ref."') ",get_db_conn());
		if (mysql_errno()==1062) {
			mysql_query("UPDATE tblcounterdomain SET cnt=cnt+1 WHERE date='".$date2."' AND domain='".$ref."'",get_db_conn());
		}
	}
}

//header("Content-type: image/gif"); 
$array = array(71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59); 
//foreach ($array as $asc) echo chr($asc);
?>
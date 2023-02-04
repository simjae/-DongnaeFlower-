<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$mode=$_REQUEST["mode"];
$coupon_code=$_REQUEST["coupon_code"];

$code=$_REQUEST["code"];
$productcode=$_REQUEST["productcode"];
if(strlen($code)==0) {
	$code=substr($productcode,0,12);
}
$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$likecode=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;

$sort=$_REQUEST["sort"];
$brandcode=$_REQUEST["brandcode"];

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

function getBCodeLoc($brandcode,$code="",$color1="9E9E9E",$color2="9E9E9E") {}


$_cdata="";
$_pdata="";
if(strlen($productcode)==18) {
	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_cdata=$row;
		if($row->group_code=="NO") {	//숨김 분류
			echo "<html></head><body onload=\"alert('판매가 종료된 상품입니다.');location.href='./main.php';\"></body></html>";exit;
		} else if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			Header("Location:./login.php?chUrl=".getUrl());
			exit;
		} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');history.go(-1);\"></body></html>";exit;
		}

		//Wishlist 담기

	} else {
		echo "<html></head><body onload=\"alert('해당 분류가 존재하지 않습니다.');location.href='./main.php';\"></body></html>";exit;
	}
	mysql_free_result($result);

	$sql = "SELECT a.* ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode='".$productcode."' AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_pdata=$row;

		$sql = "SELECT * FROM tblproductbrand ";
		$sql.= "WHERE bridx='".$_pdata->brand."' ";
		$bresult=mysql_query($sql,get_db_conn());
		$brow=mysql_fetch_object($bresult);
		$_pdata->brandcode = $_pdata->brand;
		$_pdata->brand = $brow->brandname;

		mysql_free_result($result);

		if($_pdata->assembleuse=="Y") {
			$sql = "SELECT * FROM tblassembleproduct ";
			$sql.= "WHERE productcode='".$productcode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=@mysql_fetch_object($result)) {
				$_adata=$row;
				mysql_free_result($result);
				$assemble_list_pridx = str_replace("","",$_adata->assemble_list);
				
				if(strlen($assemble_list_pridx)>0) {
					$sql = "SELECT pridx,productcode,productname,sellprice,quantity,tinyimage FROM tblproduct ";
					$sql.= "WHERE pridx IN ('".str_replace(",","','",$assemble_list_pridx)."') ";
					$sql.= "AND assembleuse!='Y' ";
					$sql.= "AND display='Y' ";
					$result=mysql_query($sql,get_db_conn());
					while($row=@mysql_fetch_object($result)) {
						$_acdata[$row->pridx] = $row;
					}
					mysql_free_result($result);
				}
			}
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');history.go(-1);\"></body></html>";exit;
	}
} else {
	echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');location.href='main.php'\"></body></html>";exit;
}



$query_mc = "select use_mobile_site, use_auto_redirection, use_cross_link, skin,main_item_sort  from tblmobileconfig";
$row_mc = mysql_fetch_array(mysql_query($query_mc));
//모바일사이트 사용여부
if($row_mc[use_mobile_site]=="N") {	alertHistoryBack("현재 모바일 사이트는 운영되지 않습니다."); exit; }
$skin_name = $row_mc[skin]; if($skin_name=="") {	$skin_name = "defalut1";	}
?>


<? include $skinPATH."productdetail_tab02_popup.php"; ?>



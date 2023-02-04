<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


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


$_cdata="";
$_pdata="";
if(strlen($productcode)==18) {
	
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

	} else {
		echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');\"></body></html>";exit;
	}
} else {
	echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');\"></body></html>";exit;
}
?>


<?

$query_mc = "select use_mobile_site, use_auto_redirection, use_cross_link, skin,main_item_sort  from tblmobileconfig";
$row_mc = mysql_fetch_array(mysql_query($query_mc));
//모바일사이트 사용여부
if($row_mc[use_mobile_site]=="N") {	alertHistoryBack("현재 모바일 사이트는 운영되지 않습니다."); exit; }
$skin_name = $row_mc[skin]; if($skin_name=="") {	$skin_name = "defalut1";	}
?>

<? include $skinPATH."productdetail_image_popup.php"; ?>


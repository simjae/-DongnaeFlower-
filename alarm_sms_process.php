<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");

$alarm_id = $_REQUEST["alarm_id"];
$alarm_mobile = $_REQUEST["alarm_mobile"];
$alarm_productcode = $_REQUEST["alarm_productcode"];


echo $sql = "SELECT * FROM tblalarm_sms WHERE alarm_productcode = '".$alarm_productcode."' and alarm_mobile = '".$alarm_mobile."' and alarm_send = 0 ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);

if($row->no){
	echo "<script>alert(\"알림이 신청된 상품입니다.\");parent.wrapWindowhide();</script>";
	exit;	
}else{
	if($alarm_productcode and $alarm_mobile ){
	$date_now = date("Y-m-d H:i:s");
	$sql = "INSERT tblalarm_sms SET ";
	$sql.= "alarm_productcode	= '".$alarm_productcode."', ";
	$sql.= "alarm_id			= '".$alarm_id."', ";
	$sql.= "alarm_mbid			= '".$_ShopInfo->getMemid()."', ";
	$sql.= "alarm_mobile	= '".$alarm_mobile."', ";
	$sql.= "app_day	= '".$date_now."' ";
	mysql_query($sql,get_db_conn());
		echo "<script>alert(\"신청 완료되었습니다.\");parent.wrapWindowhide();</script>";
		echo "<script>parent.onicon();</script>";
	exit;
	}else{
		echo "<script>alert(\"연락처 정보가 잘못입력 되었습니다.\");parent.wrapWindowhide();</script>";
	}
}
?>
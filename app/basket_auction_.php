<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

// 주문타입별 장바구니 테이블
$basket = "tblbasket_quick";
$basketWhere = "id='".$_ShopInfo->getMemid()."'";
//초기화
$sql = "DELETE FROM ".$basket." WHERE and ordertype='q' and ".$basketWhere;
mysql_query($sql,get_db_conn());


$date=date("YmdHis");

$productcode = "";
$productcode = $_POST['productcode'];
if($_POST['quantity']!=''){
	$sql = "
	productcode='$productcode',
	quantity='{$_POST['quantity']}',
	date='$date',
	id='{$_ShopInfo->getMemid()}'
	";
	mysql_query("INSERT tblbasket_quick SET ".$sql,get_db_conn());
}
echo json_encode(array(
	"result" => "ok_quick",
	"code" => 7000,
	"msg" => ""
));
exit;
?>
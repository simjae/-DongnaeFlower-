<?
// 상품가격 0원 오류 관련 수정함 2016-04-07 Seul
include "header.php";
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");

$shopconfig = shopconfig();

include $skinPATH."checkup.php"; 	



$sql_mb="SELECT * FROM tblmember where id = '".$_ShopInfo->getMemid()."' ";
$result_mb=mysql_query($sql_mb,get_db_conn());
$row_mb=mysql_fetch_object($result_mb);

$order_type = $row_mb->order_type;
if($order_type < 0){
	$URL = $Dir."app/select_order.php";
}
else if($order_type == 1){
	$URL = $Dir."app/form_request.php";
}
else if($order_type == 2){
	$URL = $Dir."app/talk_request.php";
}
//로그인후 메인으로 이동 20210713 김성식
$URL = $Dir."app/main.php";
echo "<html><head><title></title></head><body><script>setTimeout(function(){location.replace('".$URL."');},2000)</script></body></html>";



?>

<script>
$(document).ready(function(){
	$("#gnb_button").hide();
	$("#prsearch").hide();
	$("#basket").hide();
});
	var broswerInfo = navigator.userAgent;
	if(broswerInfo.indexOf("dongne-flower user android")>-1){
		BRIDGE.appLoginProc("<?=$memid?>","<?=$passwd?>");
//		alert("앱으로 접속하셨습니다.");
	}
</script>
</body>
</html>
<?php
include "header.php";

include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;
include_once($Dir."lib/check_login.php");

mysql_free_result($result);

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

$cdate = date("YmdH");
if($_data->coupon_ok=="Y") {
	$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='".$cdate."' OR date_end='') ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$coupon_cnt = $row->cnt;
	mysql_free_result($result);
} else {
	$coupon_cnt=0;
}
?>


<?
include ($skinPATH."mypage.php");
?>

<form name=form2 method=post action="<?=$Dir?>m/orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
</form>
<form name=form3 method=post action="<?=$Dir.FrontDir?>deliverypop.php" target="delipop">
<input type=hidden name=ordercode>
</form>
<form name=form4 action="<?=$Dir?>m/mypage_personalview.php" method=post target="mypersonalview">
<input type=hidden name=idx>
</form>

<div id="layer_popup" style="display: none; position: fixed; padding: 15% 3%; box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 999; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="PopupClose()">
		<div style="position:absolute;top:3%;right:3%;color:#fff;font-size:4em;font-weight:500;">×</div>
	</div>
	<div style="position: relative; width: 100%; height: 100%; background-color: rgb(255, 255, 255); z-index: 0; overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;">
		<iframe frameborder="0" id="layer_content" name="layer_content" src="about:blank" style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
	</div>
</div>


<script type="text/javascript">
<!--
function reviewWrite(prcode){
	$("#layer_popup").show();
	$("#layer_content").attr("src","./prreview_write_pop.php?productcode="+prcode);

}

function OrderDetailPop(ordercode) {
	
	$("#layer_popup").show();
	$("#layer_content").attr("src","orderdetailpop.php?ordercode="+ordercode);
//		document.detailform.ordercode.value=ordercode;
//		window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
//		document.detailform.submit();
}
function PopupClose(){
	$("#layer_popup").hide();
	$("#layer_content").attr("src","about:blank");
}

function DeliSearch(deli_url){
	window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
}
function DeliveryPop(ordercode) {
	document.form3.ordercode.value=ordercode;
	window.open("about:blank","delipop","width=600,height=370,scrollbars=no");
	document.form3.submit();
}
function ViewPersonal(idx) {
	window.open("about:blank","mypersonalview","width=600,height=450,scrollbars=yes");
	document.form4.idx.value=idx;
	document.form4.submit();
}
//-->
</script>

<? include ("footer.php") ?>

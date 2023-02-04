<?php
include "./inc/lib.php";

$use_pay_method = "";
$actionresult = "";
$van_code = "";
$returnPaymethod = "";
switch($paymethod){
	case "C":  //신용카드
	$use_pay_method = "100000000000";
	$actionresult = "card";
	$van_code = "";
	$returnPaymethod = "CARD";
	break;
	case "V":  //계좌이체
	$use_pay_method="010000000000";
	$actionresult = "acnt";
	$van_code = "";
	$returnPaymethod = "BANK";
	break;
	case "O":  //가상계좌
	$use_pay_method="001000000000";
	$actionresult = "vcnt";
	$van_code = "";
	$returnPaymethod = "VCNT";
	break;
}
?>
<form name="frm" action="<?=$Dir?>m/paygate/A/order.php">
	<!-- 주문번호 -->
	<input type="hidden" name='ordr_idxx' value='<?=$ordercode?>'>
	<!-- good_name(상품명) -->
	<input type="hidden" name='good_name' value='<?=$goodname?>'>
	<!-- hp_mny(결제금액) -->
	<input type="hidden" name='good_mny' value='<?=$last_price?>' > 
	<!-- <input type="hidden" name='good_mny' size="9" maxlength="9" value='1004' >  -->
	<!-- buyr_name(주문자이름) -->
	<input type="hidden" name='buyr_name' value="<?=$sender_name?>">
	<!-- buyr_tel1(주문자 연락처) -->
	<input type="hidden" name='buyr_tel1' value='<?=$sender_tel?>'>
	<!-- buyr_tel2(주문자 핸드폰 번호) -->
	<input type="hidden" name='buyr_tel2' value='<?=$sender_tel?>'>
	<!-- buyr_mail(주문자 E-mail) -->
	<input type="hidden" name='buyr_mail' value='<?=$sender_email?>'>
	<input type="hidden" name='van_code' value='<?=$van_code?>'>
	<input type="hidden" name='use_pay_method' value='<?=$use_pay_method?>'>
	<input type="hidden" name='actionresult' value='<?=$actionresult?>'>
	<input type="hidden" name='paymethod' value='<?=$returnPaymethod?>'>
</form>

<script>
	var f = document.frm;
	f.method = "post";
	f.submit();
</script>
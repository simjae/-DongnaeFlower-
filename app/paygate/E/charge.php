<?php
header("Content-Type:text/html; charset=utf-8;");
$ediDate = date( "YmdHis" );
$tomorrow = mktime( 0, 0, 0, date( "m" ), date( "d" ) + 1, date( "Y" ) );
$vDate = date( "Ymd", $tomorrow );
$sitecd = $_REQUEST['sitecd'];
$mid = $_REQUEST['mid'];
$merchantKey = $_REQUEST['mertkey'];
$merchantKey = rawurldecode( $merchantKey );
$merchantKey = str_replace( "__STR_EQUAL__", "=", $martkey );
$PayMethodArray = array( "C" => "CARD", "V" => "BANK", "O" => "VBANK", "M" => "CELLPHONE" );
$PayMethod = $PayMethodArray[$paymethod];
$escrow = preg_match( "/^(Q|P)$/", $paymethod ) ? "Y" : "N";
$escrow = $escrow == "N" ? "0" : "1";
$price = $last_price;
$URL = $_SERVER['SERVER_NAME'];
$str_src = $ediDate.$martid.$price.$merchantKey;
$hash_String = base64_encode( md5( $str_src ) );
$_devicelist = "/(Android 4.4)/";
if ( preg_match( $_devicelist, $_SERVER['HTTP_USER_AGENT'] ) )
{
    $closeType = "kikat";
}
else
{
    $closeType = "Y";
}
?>
<html>
<head>
<title>NICE PG :: NICEPAY</title>
<meta charset="utf-8"/>
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
<script language="javascript">
/**
	스마트폰 결제 요청
*/
/*function goPay(form) {
	
	form.method = "post";
	//form.action = "./test.php";
	form.action = "https://web.nicepay.co.kr/smart/interfaceURL.jsp";
	//
	var title = "payment";
	
	
	//window.open("",title,status);
	//form.target = title;
	//form.submit();
	//var status = "width=100,height=100,status=yes,menubar=no,toolbar=no,location=no,scrollbars=no,directories=no";
	window.open("",title,"status=yes,menubar=no,toolbar=no,location=no,scrollbars=no,directories=no");
	form.target = title;
	form.submit();
	
}*/

function goPay(form) {
	
	form.target = "_self";
	form.method = "post";
	form.action = "https://web.nicepay.co.kr/smart/interfaceURL.jsp";
	form.submit();
}

</script>
</head>
<body onload="goPay(document.tranMgr);">
<form name="tranMgr" accept-charset="euc-kr">
		<!-- 상품 갯수 -->
        <input type="hidden" name="GoodsCnt" value="1"/>		
		<!-- 주문번호 -->
        <input type="hidden" name="Moid" value="<?=$ordercode?>"/>
		<!-- 구매자 전화번호 -->
        <input type="hidden" name="BuyerTel" value="<?=$sender_tel?>"/>	
		<!-- 구매자 이메일 주소  -->
        <input type="hidden" name="BuyerEmail" value="<?=$sender_email?>"/>	
        <!-- 구매자 주소 -->
        <input type="hidden" name="BuyerAddr"  value="<?=$raddr1?>"/>	
		          
		<!-- 가상계좌 입금완료일  -->
		<input type="hidden" name="VbankExpDate"  value="<?=$vDate?>"/>	
        <!-- 상점에서 여분으로 사용할 값을 지정하여 주십시요. 그대로 전달됩니다  -->
		<input type="hidden" name="MallReserved"  value=""/>
		<!-- 결과를 전달받을 url을 지정하십시요.  -->
        <input type="hidden" name="ReturnURL" value="https://<?=$URL?>/app/payresult.php">
		<!-- DB 결과값을 저장할 url을 절대경로로 지정하십시요 -->
        <input type="hidden" name="RetryURL" value="https://<?=$URL?>/app/paygate/E/charge_result.php">


		<!-- 휴대폰 결제 상품구분 1:실물, 0:컨텐츠 -->
		<input type="hidden" name="GoodsCl" value="1"/>

		<!-- APP 내 WebView로 연동하는 경우만 사용합니다. -->
		<!--<input type="hidden" name="WapUrl"  value="nicepaysample://"/>	-->					    <!-- ISP 및 계좌이체 리턴 URL (앱 스키마 명 입력) -->
		<!--<input type="hidden" name="IspCancelUrl"  value="nicepaysample://ISPCancel"/">	-->		<!-- ISP 취소 URL(앱 스키마 명 입력) -->

		<!-- 상점에서 결과를 전달받을 인코딩을 설정하여 주십시요 (utf-8 또는 euc-kr) -->
		<input type="hidden" name="CharSet" value="utf-8"/>

		<input type="hidden" name="PayMethod" value="<?=$PayMethod?>"/>
		<input type="hidden" name="Amt" size="25px;" value="<?=$price?>">
		<input type="hidden" name="GoodsName" size="25px;" value="<?=$goodname?>"/>
		<input type="hidden" name="BuyerName" size="25px;" value="<?=$sender_name?>">
		<input type="hidden" name="MID" size="25px;" value="<?=$martid?>">
		<input type="hidden" name="WndClose" size="25px;" value="<?=$closeType?>">
		<!-- 사용자 ID -->
        <input type="hidden" name="MallUserID" value="<?=$_ShopInfo->getmemid()?>"/>
		<!-- 수정 하지 마십시요.-->
		<input type="hidden" name="EncryptData" value="<?=$hash_String?>"/>
        <input type="hidden" name="ediDate"  value="<?=$ediDate?>"/>

        <!-- <div class="btn">
			<img class="right" src="/m/paygate/E/images/btn_next.png"  onClick="goPay(document.tranMgr);"/>
        </div> -->
</form>
</body>
</html>


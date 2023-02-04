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
	����Ʈ�� ���� ��û
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
		<!-- ��ǰ ���� -->
        <input type="hidden" name="GoodsCnt" value="1"/>		
		<!-- �ֹ���ȣ -->
        <input type="hidden" name="Moid" value="<?=$ordercode?>"/>
		<!-- ������ ��ȭ��ȣ -->
        <input type="hidden" name="BuyerTel" value="<?=$sender_tel?>"/>	
		<!-- ������ �̸��� �ּ�  -->
        <input type="hidden" name="BuyerEmail" value="<?=$sender_email?>"/>	
        <!-- ������ �ּ� -->
        <input type="hidden" name="BuyerAddr"  value="<?=$raddr1?>"/>	
		          
		<!-- ������� �ԱݿϷ���  -->
		<input type="hidden" name="VbankExpDate"  value="<?=$vDate?>"/>	
        <!-- �������� �������� ����� ���� �����Ͽ� �ֽʽÿ�. �״�� ���޵˴ϴ�  -->
		<input type="hidden" name="MallReserved"  value=""/>
		<!-- ����� ���޹��� url�� �����Ͻʽÿ�.  -->
        <input type="hidden" name="ReturnURL" value="https://<?=$URL?>/app/payresult.php">
		<!-- DB ������� ������ url�� �����η� �����Ͻʽÿ� -->
        <input type="hidden" name="RetryURL" value="https://<?=$URL?>/app/paygate/E/charge_result.php">


		<!-- �޴��� ���� ��ǰ���� 1:�ǹ�, 0:������ -->
		<input type="hidden" name="GoodsCl" value="1"/>

		<!-- APP �� WebView�� �����ϴ� ��츸 ����մϴ�. -->
		<!--<input type="hidden" name="WapUrl"  value="nicepaysample://"/>	-->					    <!-- ISP �� ������ü ���� URL (�� ��Ű�� �� �Է�) -->
		<!--<input type="hidden" name="IspCancelUrl"  value="nicepaysample://ISPCancel"/">	-->		<!-- ISP ��� URL(�� ��Ű�� �� �Է�) -->

		<!-- �������� ����� ���޹��� ���ڵ��� �����Ͽ� �ֽʽÿ� (utf-8 �Ǵ� euc-kr) -->
		<input type="hidden" name="CharSet" value="utf-8"/>

		<input type="hidden" name="PayMethod" value="<?=$PayMethod?>"/>
		<input type="hidden" name="Amt" size="25px;" value="<?=$price?>">
		<input type="hidden" name="GoodsName" size="25px;" value="<?=$goodname?>"/>
		<input type="hidden" name="BuyerName" size="25px;" value="<?=$sender_name?>">
		<input type="hidden" name="MID" size="25px;" value="<?=$martid?>">
		<input type="hidden" name="WndClose" size="25px;" value="<?=$closeType?>">
		<!-- ����� ID -->
        <input type="hidden" name="MallUserID" value="<?=$_ShopInfo->getmemid()?>"/>
		<!-- ���� ���� ���ʽÿ�.-->
		<input type="hidden" name="EncryptData" value="<?=$hash_String?>"/>
        <input type="hidden" name="ediDate"  value="<?=$ediDate?>"/>

        <!-- <div class="btn">
			<img class="right" src="/m/paygate/E/images/btn_next.png"  onClick="goPay(document.tranMgr);"/>
        </div> -->
</form>
</body>
</html>


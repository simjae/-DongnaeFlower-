<?php
header("Content-Type:text/html; charset=utf-8;");
/*
*******************************************************
* <결제요청 파라미터>
* 결제시 Form 에 보내는 결제요청 파라미터입니다.
* 샘플페이지에서는 기본(필수) 파라미터만 예시되어 있으며, 
* 추가 가능한 옵션 파라미터는 연동메뉴얼을 참고하세요.
*******************************************************
*/
$URL = $_SERVER['SERVER_NAME'];
$merchantKey      = $martkey;   // 상점키
$merchantID       = $martid;                                                       // 상점아이디
$goodsCnt         = "1";                                                                // 결제상품개수
$goodsName        = $goodname;                                                       // 결제상품명
$price            = $last_price;                                                       // 결제상품금액	
$buyerName        = $sender_name;                                                           // 구매자명
$buyerTel         = $sender_tel;                                                      // 구매자연락처
$buyerEmail       = $sender_email;                                                  // 구매자메일주소
$moid             = $ordercode;                                                  // 상품주문번호
$PayMethodArray	  = array( "C" => "CARD", "V" => "BANK", "O" => "VBANK", "M" => "CELLPHONE" );
$PayMethod        = $PayMethodArray[$paymethod];
$ReturnURL        = "https://".$URL."/app/paygate/E_SMART/charge_result.php";            // Return URL
//$ReturnURL        = "https://".$URL."/app/paygate/E_SMART/payResult.php";            // Return URL
$CharSet          = "utf-8";                                                            // 결과값 인코딩 설정
/*
*******************************************************
* <해쉬암호화> (수정하지 마세요)
* SHA-256 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
*******************************************************
*/ 
$ediDate = date("YmdHis");
$hashString = bin2hex(hash('sha256', $ediDate.$merchantID.$price.$merchantKey, true));

?>
<!DOCTYPE html>
<html>
<head>
<title>NICEPAY PAY REQUEST(UTF-8)</title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes, target-densitydpi=medium-dpi"/>
<link rel="stylesheet" type="text/css" href="./css/import.css"/>
<script type="text/javascript">

//스마트폰 결제 요청
function goPay(form) {
	
	var broswerInfo = navigator.userAgent;
	if(broswerInfo.indexOf("dongne-flower user android")>-1){
		BRIDGE.setPayFlag(true);
	}
    document.getElementById("vExp").value = getTomorrow();   
    document.tranMgr.submit();
    document.charset = "euc-kr";
}
//가상계좌 입금만료일 설정 (today +1)
function getTomorrow(){
    var today = new Date();
    var yyyy = today.getFullYear().toString();
    var mm = (today.getMonth()+1).toString();
    var dd = (today.getDate()+1).toString();
    if(mm.length < 2){mm = '0' + mm;}
    if(dd.length < 2){dd = '0' + dd;}
    return (yyyy + mm + dd);
}
</script>
</head>
<body onload="goPay(document.tranMgr);">
<form name="tranMgr" method="post" target="_self" action="https://web.nicepay.co.kr/v3/smart/smartPayment.jsp" accept-charset="euc-kr">
	<!--결제 수단-->
    <input type="hidden" name="PayMethod" value="<?=$PayMethod?>"/>
	<!--결제 상품명-->
    <input type="hidden" name="GoodsName" value="<?=$goodsName?>">
	<!--결제 상품개수-->
	<input type="hidden" name="GoodsCnt" value="<?=$goodsCnt?>">
	<!--결제 상품금액-->
	<input type="hidden" name="Amt" value="<?=$price?>">
	<!--구매자명-->
    <input type="hidden" name="BuyerName" value="<?=$buyerName?>">
    <!--구매자 연락처-->
    <input type="hidden" name="BuyerTel" value="<?=$buyerTel?>">
	<!--상품 주문번호-->
    <input type="hidden" name="Moid" value="<?=$moid?>">
	<!--상점 아이디-->
    <input type="hidden" name="MID" value="<?=$merchantID?>">
              
    <!-- 옵션 -->
	<input type="hidden" name="ReturnURL" value="<?=$ReturnURL?>"/>       <!-- Return URL -->		     
	<input type="hidden" name="CharSet" value="<?=$CharSet?>"/>           <!-- 인코딩 설정 -->              
	<input type="hidden" name="GoodsCl" value="1"/>                       <!-- 상품구분 실물(1), 컨텐츠(0) -->
	<input type="hidden" name="VbankExpDate" id="vExp"/>                  <!-- 가상계좌입금만료일 -->                        
	<input type="hidden" name="BuyerEmail" value="<?=$buyerEmail?>"/>     <!-- 구매자 이메일 -->             				  

	<!-- 변경 불가능 -->
	<input type="hidden" name="EncryptData" value="<?=$hashString?>"/>    <!-- 해쉬값 -->
	<input type="hidden" name="ediDate" value="<?=$ediDate?>"/>           <!-- 전문 생성일시 --> 
	<input type="hidden" name="AcsNoIframe" value="Y"/>					<!-- 나이스페이 결제창 프레임 옵션 (변경불가) -->				
			  

	  <!--
      <div class="btngroup">
        <a href="#" class="btn_blue" onClick="goPay();">요 청</a>
      </div>
	  -->
</form>
</body>
</html>
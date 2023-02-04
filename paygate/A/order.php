<?
	$Dir = "../../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	/* ============================================================================== */
	/* =   PAGE : 결제 요청 PAGE                                                    = */
	/* = -------------------------------------------------------------------------- = */
	/* =   이 페이지는 Payplus Plug-in을 통해서 결제자가 결제 요청을 하는 페이지    = */
	/* =   입니다. 아래의 ※ 필수, ※ 옵션 부분과 매뉴얼을 참조하셔서 연동을        = */
	/* =   진행하여 주시기 바랍니다.                                                = */
	/* = -------------------------------------------------------------------------- = */
	/* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
	/* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do			        = */
	/* = -------------------------------------------------------------------------- = */
	/* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
	/* ============================================================================== */


	/* ============================================================================== */
	/* =   환경 설정 파일 Include                                                   = */
	/* = -------------------------------------------------------------------------- = */
	/* =   ※ 필수                                                                  = */
	/* =   테스트 및 실결제 연동시 site_conf_inc.jsp 파일을 수정하시기 바랍니다.    = */
	/* = -------------------------------------------------------------------------- = */

	include "./cfg/site_conf_inc.php";       // 환경설정 파일 include
	$kcp_actionresult = $_POST['actionresult'];
	$kcp_paymethod = $_POST['paymethod'];

	/* = -------------------------------------------------------------------------- = */
	/* =   환경 설정 파일 Include END                                               = */
	/* ============================================================================== */


	/* kcp와 통신후 kcp 서버에서 전송되는 결제 요청 정보 */
	$req_tx          = $_POST[ "req_tx"         ]; // 요청 종류         
	$res_cd          = $_POST[ "res_cd"         ]; // 응답 코드         
	$tran_cd         = $_POST[ "tran_cd"        ]; // 트랜잭션 코드     
	$ordr_idxx       = $_POST[ "ordr_idxx"      ]; // 쇼핑몰 주문번호   
	$good_name       = $_POST[ "good_name"      ]; // 상품명            
	$good_mny        = $_POST[ "good_mny"       ]; // 결제 총금액       
	$buyr_name       = $_POST[ "buyr_name"      ]; // 주문자명          
	$buyr_tel1       = $_POST[ "buyr_tel1"      ]; // 주문자 전화번호   
	$buyr_tel2       = $_POST[ "buyr_tel2"      ]; // 주문자 핸드폰 번호
	$buyr_mail       = $_POST[ "buyr_mail"      ]; // 주문자 E-mail 주소
	$use_pay_method  = $_POST[ "use_pay_method" ]; // 결제 방법         
	$ipgm_date       = $_POST[ "ipgm_date"      ]; // 가상계좌 마감시간 
	$enc_info        = $_POST[ "enc_info"       ]; // 암호화 정보       
	$enc_data        = $_POST[ "enc_data"       ]; // 암호화 데이터     
	$van_code        = $_POST[ "van_code"       ];
	$cash_yn         = $_POST[ "cash_yn"        ];
	$cash_tr_code    = $_POST[ "cash_tr_code"   ];
	/* 기타 파라메터 추가 부분 - Start - */
	$param_opt_1    = $_POST[ "param_opt_1"     ]; // 기타 파라메터 추가 부분
	$param_opt_2    = $_POST[ "param_opt_2"     ]; // 기타 파라메터 추가 부분
	$param_opt_3    = $_POST[ "param_opt_3"     ]; // 기타 파라메터 추가 부분
	/* 기타 파라메터 추가 부분 - End -   */


	$tablet_size     = "1.0"; // 화면 사이즈 조정 - 기기화면에 맞게 수정(갤럭시탭,아이패드 - 1.85, 스마트폰 - 1.0)
	$url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
	<title>*** KCP [AX-HUB Version] ***</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="No-Cache">
	<meta http-equiv="Pragma" content="No-Cache">
	<meta name="viewport" content="width=device-width, user-scalable=<?=$tablet_size?>, initial-scale=<?=$tablet_size?>, maximum-scale=<?=$tablet_size?>, minimum-scale=<?=$tablet_size?>">

	<link href="css/style.css" rel="stylesheet" type="text/css" id="cssLink"/>

	<!-- 거래등록 하는 kcp 서버와 통신을 위한 스크립트-->
	<script type="text/javascript" src="./js/approval_key.js"></script>

	<script type="text/javascript">
		var controlCss = "css/style_mobile.css";
		var isMobile = {
			Android: function() {
				return navigator.userAgent.match(/Android/i);
			},
			BlackBerry: function() {
				return navigator.userAgent.match(/BlackBerry/i);
			},
			iOS: function() {
				return navigator.userAgent.match(/iPhone|iPad|iPod/i);
			},
			Opera: function() {
				return navigator.userAgent.match(/Opera Mini/i);
			},
			Windows: function() {
				return navigator.userAgent.match(/IEMobile/i);
			},
			any: function() {
				return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
			}
		};

		if(isMobile.any()){
			document.getElementById("cssLink").setAttribute("href", controlCss);
		}
	</script>

	<script type="text/javascript">
		/* 주문번호 생성 예제 */
		function init_orderid(){
			var today = new Date();
			var year  = today.getFullYear();
			var month = today.getMonth() + 1;
			var date  = today.getDate();
			var time  = today.getTime();

			if (parseInt(month) < 10){
				month = "0" + month;
			}

			if (parseInt(date) < 10){
				date  = "0" + date;
			}

			//var order_idxx = "TEST" + year + "" + month + "" + date + "" + time;
			var ipgm_date  = year + "" + month + "" + date;

			//document.order_info.ordr_idxx.value = order_idxx;
			document.order_info.ipgm_date.value = ipgm_date;
		}

		/* kcp web 결제창 호츨 (변경불가) */
		function call_pay_form(){
			var v_frm = document.order_info;

			document.getElementById("sample_wrap").style.display = "none";
			document.getElementById("layer_all").style.display  = "block";

			v_frm.target = "frm_all";

			// 인코딩 방식에 따른 변경 -- Start
			if(v_frm.encoding_trans == undefined){
				v_frm.action = PayUrl;
			}else{
				if(v_frm.encoding_trans.value == "UTF-8"){
					v_frm.action = PayUrl.substring(0,PayUrl.lastIndexOf("/")) + "/jsp/encodingFilter/encodingFilter.jsp";
					v_frm.PayUrl.value = PayUrl;
				}else{
					v_frm.action = PayUrl;
				}
			}
			// 인코딩 방식에 따른 변경 -- End

			if (v_frm.Ret_URL.value == ""){
				/* Ret_URL값은 현 페이지의 URL 입니다. */
				alert("연동시 Ret_URL을 반드시 설정하셔야 됩니다.");
				return false;
			}else{
				v_frm.submit();
			}
		}

		/* kcp 통신을 통해 받은 암호화 정보 체크 후 결제 요청 (변경불가) */
		function chk_pay(){
			self.name = "tar_opener";
			var pay_form = document.pay_form;

			if (pay_form.res_cd.value == "3001" ){
				alert("사용자가 취소하였습니다.");
				pay_form.res_cd.value = "";
				location.href='/m/main.php';

			}else if (pay_form.res_cd.value == "3000" ){
				alert("30만원 이상 결제를 할 수 없습니다.");
				pay_form.res_cd.value = "";
			}

			//document.getElementById("sample_wrap").style.display = "block";
			document.getElementById("layer_all").style.display  = "none";

			if (pay_form.enc_info.value){
				pay_form.submit();
			}
		}

		/* 에스크로 장바구니 상품 상세 정보 생성 예제 */
		function create_goodInfo(){
			var chr30 = String.fromCharCode(30);
			var chr31 = String.fromCharCode(31);

			var good_info = "seq=1" + chr31 + "ordr_numb=20060310_0001" + chr31 + "good_name=양말" + chr31 + "good_cntx=2" + chr31 + "good_amtx=1000" + chr30 + "seq=2" + chr31 + "ordr_numb=20060310_0002" + chr31 + "good_name=신발" + chr31 + "good_cntx=1" + chr31 + "good_amtx=1500" + chr30 + "seq=3" + chr31 + "ordr_numb=20060310_0003" + chr31 + "good_name=바지" + chr31 + "good_cntx=1" + chr31 + "good_amtx=1000";

			document.order_info.good_info.value = good_info;
		}
	</script>

	<style>
		#sample_wrap .tbl td {padding:5px 2px;}
	</style>
</head>

<body onload="init_orderid();chk_pay();create_goodInfo();<?=($kcp_paymethod?"kcp_AJAX();":"")?>">
	<form name="order_info" method="post">
		<div id="sample_wrap" style="display:none;">
			<!-- 타이틀 -->
			<h1><span>결제 요청</span></h1>
			<div class="sample">
				<!-- 주문 정보 -->
				<table class="tbl" cellpadding="0" cellspacing="0" style="display:none;">
					<tr>
						<th>주문 번호</th>
						<td><input type="hidden" name="ordr_idxx" class="w200" value="<?=$ordr_idxx?>" readonly><?=$ordr_idxx?></td>
					</tr>
					<tr>
						<th>상품명</th>
						<td><input type="hidden" name="good_name" class="w100" value="<?=$good_name?>" readonly><?=$good_name?></td>
					</tr>
					<tr>
						<th>결제 금액</th>
						<td><input type="hidden" name="good_mny" class="w100" value="<?=$good_mny?>" readonly><?=number_format($good_mny)?>원</td>
					</tr>
					<tr>
						<th>주문자명</th>
						<td><input type="hidden" name="buyr_name" class="w100" value="<?=$buyr_name?>" readonly><?=$buyr_name?></td>
					</tr>
					<tr>
						<th>E-mail</th>
						<td><input type="hidden" name="buyr_mail" class="w200" value="<?=$buyr_mail?>"><?=$buyr_mail?></td>
					</tr>
					<!-- <tr>
						<th>전화번호</th>
						<td><input type="hidden" name="buyr_tel1" class="w100" value="<?=$buyr_tel1?>"><?=$buyr_tel1?></td>
					</tr> -->
					<tr>
						<th>휴대폰번호</th>
						<td><input type="hidden" name="buyr_tel2" class="w100" value="<?=$buyr_tel2?>"><?=$buyr_tel2?>
						<input type="hidden" name="buyr_tel1" class="w100" value="<?=$buyr_tel1?>"></td>
					</tr>
				</table>

				<!-- 에스크로 정보 필드 (에스크로 신청 가맹점은 필수로 값 세팅)
				<h2>&sdot; 에스크로 정보</h2>
				<table class="tbl" cellpadding="0" cellspacing="0">
				  <tr>
					<th>수취인이름</th>
					<td><input type="text" name="rcvr_name" class="w100" value="홍길동"></td>
				  </tr>
				  <tr>
					<th>수취인 연락처</th>
					<td><input type="text" name="rcvr_tel1" class="w100" value="02-2108-1000"></td>
				  </tr>
				  <tr>
					<th>수취인 휴대폰번호</th>
					<td><input type="text" name="rcvr_tel2" class="w100" value="010-0000-0000"></td>
				  </tr>
				  <tr>
					<th>수취인 E-mail</th>
					<td><input type="text" name="rcvr_mail" class="w200" value="honggilsoon@test.co.kr"></td>
				  </tr>
				  <tr>
					<th>수취인 우편번호</th>
					<td><input type="text" name="rcvr_zipx" class="w100" value="157864"></td>
				  </tr>
				  <tr>
					<th>수취인 주소</th>
					<td><input type="text" name="rcvr_add1" class="w300" value="서울시 구로구 구로동"></td>
				  </tr>
				  <tr>
					<th>수취인 상세주소</th>
					<td><input type="text" name="rcvr_add2" class="w300" value="170-5 우림 E-biz"></td>
				  </tr>
				</table> -->

				<!-- 결제 요청/처음으로 이미지 -->
				<div class="footer">
					<b>
						※ PC에서 결제요청시 오류가 발생합니다. ※<br/>
						※ 아래 결제요청 버튼을 터치하여 결제를 진행합니다. ※
					</b>
				</div>

				<div class="btnset" id="display_pay_button" style="display:block">
					<input type="button" class="submit" value="결제요청" onclick="kcp_AJAX();" style="display:none;" />
					<a href="/m/main.php" class="home">처음으로</a>
				</div>
			</div>

			<!--footer-->
			<div class="footer">Copyright (c) KCP INC. All Rights reserved.</div>
			<!--//footer-->

			<!-- 공통정보 -->
			<input type="hidden" name="req_tx"          value="pay">                           <!-- 요청 구분 -->
			<input type="hidden" name="shop_name"       value="<?= $g_conf_site_name ?>">      <!-- 사이트 이름 --> 
			<input type="hidden" name="site_cd"         value="<?= $g_conf_site_cd   ?>">      <!-- 사이트 코드 -->
			<input type="hidden" name="currency"        value="410"/>                          <!-- 통화 코드 -->
			<input type="hidden" name="eng_flag"        value="N"/>                            <!-- 한 / 영 -->
			<!-- 결제등록 키 -->
			<input type="hidden" name="approval_key"    id="approval">
			<!-- 인증시 필요한 파라미터(변경불가)-->
			<input type="hidden" name="pay_method"      value="<?=$kcp_paymethod?>">
			<input type="hidden" name="ActionResult"      value="<?=$kcp_actionresult?>">
			<input type="hidden" name="van_code"        value="<?=$van_code?>">
			<!-- 신용카드 설정 -->
			<input type="hidden" name="quotaopt"        value="12"/>                           <!-- 최대 할부개월수 -->
			<!-- 가상계좌 설정 -->
			<input type="hidden" name="ipgm_date"       value=""/>
			<!-- 가맹점에서 관리하는 고객 아이디 설정을 해야 합니다.(필수 설정) -->
			<input type="hidden" name="shop_user_id"    value=""/>
			<!-- 복지포인트 결제시 가맹점에 할당되어진 코드 값을 입력해야합니다.(필수 설정) -->
			<input type="hidden" name="pt_memcorp_cd"   value=""/>
			<!-- 현금영수증 설정 -->
			<input type="hidden" name="disp_tax_yn"     value="Y"/>
			<!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
			<input type="hidden" name="Ret_URL"         value="<?=$url?>">
			<!-- 화면 크기조정 -->
			<input type="hidden" name="tablet_size"     value="<?=$tablet_size?>">

			<input type="hidden" name="encoding_trans" value="UTF-8" />
			<input type='hidden' name='PayUrl' >

			<?
				/* ============================================================================== */
				/* =   에스크로결제 사용시 필수 정보                                            = */
				/* = -------------------------------------------------------------------------- = */
				/* =   결제에 필요한 주문 정보를 입력 및 설정합니다.                            = */
				/* = -------------------------------------------------------------------------- = */
			?>
			<!-- 에스크로 사용유무 에스크로 사용 업체(가상계좌, 계좌이체 해당)는 escw_used 를 Y로 세팅 해주시기 바랍니다.-->
			<input type="hidden" name="escw_used" value="N">
			<!-- 장바구니 상품 개수 -->
			<!-- <input type='hidden' name='bask_cntx' value="1"> -->
			<!-- 장바구니 정보(상단 스크립트 참조) -->
			<input type='hidden' name='good_info' value="" />
			<!-- 에스크로 결제처리모드 KCP 설정된 금액 결제(사용 : 설정된금액적용: 사용안함: -->
			<!-- <input type="hidden" name='pay_mod'   value=""> -->
			<!-- 배송소요기간 -->
			<!-- <input type="hidden" name='deli_term' value='03'> -->

			<?
				/* = -------------------------------------------------------------------------- = */
				/* =   에스크로결제 사용시 필수 정보  END                                       = */
				/* ============================================================================== */


				/* ============================================================================== */
				/* =   옵션 정보                                                                = */
				/* = -------------------------------------------------------------------------- = */
				/* =   ※ 옵션 - 결제에 필요한 추가 옵션 정보를 입력 및 설정합니다.             = */
				/* = -------------------------------------------------------------------------- = */
				/* 카드사 리스트 설정
				예) 비씨카드와 신한카드 사용 설정시
				<input type="hidden" name='used_card'    value="CCBC:CCLG">

				/*  무이자 옵션
						※ 설정할부    (가맹점 관리자 페이지에 설정 된 무이자 설정을 따른다)                             - "" 로 설정
						※ 일반할부    (KCP 이벤트 이외에 설정 된 모든 무이자 설정을 무시한다)                           - "N" 로 설정
						※ 무이자 할부 (가맹점 관리자 페이지에 설정 된 무이자 이벤트 중 원하는 무이자 설정을 세팅한다)   - "Y" 로 설정
				<input type="hidden" name="kcp_noint"       value=""/> */

				/*  무이자 설정
						※ 주의 1 : 할부는 결제금액이 50,000 원 이상일 경우에만 가능
						※ 주의 2 : 무이자 설정값은 무이자 옵션이 Y일 경우에만 결제 창에 적용
						예) 전 카드 2,3,6개월 무이자(국민,비씨,엘지,삼성,신한,현대,롯데,외환) : ALL-02:03:04
						BC 2,3,6개월, 국민 3,6개월, 삼성 6,9개월 무이자 : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04
				<input type="hidden" name="kcp_noint_quota" value="CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09"/> */

				/* KCP는 과세상품과 비과세상품을 동시에 판매하는 업체들의 결제관리에 대한 편의성을 제공해드리고자, 
				   복합과세 전용 사이트코드를 지원해 드리며 총 금액에 대해 복합과세 처리가 가능하도록 제공하고 있습니다
				   복합과세 전용 사이트 코드로 계약하신 가맹점에만 해당이 됩니다
				   상품별이 아니라 금액으로 구분하여 요청하셔야 합니다
				   총결제 금액은 과세금액 + 부과세 + 비과세금액의 합과 같아야 합니다. 
				   (good_mny = comm_tax_mny + comm_vat_mny + comm_free_mny)
				
					<input type="hidden" name="tax_flag"       value="TG03">  <!-- 변경불가	   -->
					<input type="hidden" name="comm_tax_mny"   value=""    >  <!-- 과세금액	   --> 
					<input type="hidden" name="comm_vat_mny"   value=""    >  <!-- 부가세	   -->
					<input type="hidden" name="comm_free_mny"  value=""    >  <!-- 비과세 금액 --> */
				/* = -------------------------------------------------------------------------- = */
				/* =   옵션 정보 END                                                            = */
				/* ============================================================================== */
			?>
		</div>
	</form>

	<!-- 스마트폰에서 KCP 결제창을 레이어 형태로 구현-->
	<div id="layer_all" style="position:absolute; left:0px; top:0px; width:100%;height:100%; z-index:1; display:none;">
		<iframe name="frm_all" frameborder="0" marginheight="0" marginwidth="0" border="0" width="100%" height="100%" scrolling="auto"></iframe>
	</div>

	<form name="pay_form" method="post" action="pp_ax_hub.php">
		<input type="hidden" name="req_tx"         value="<?=$req_tx?>">               <!-- 요청 구분          -->
		<input type="hidden" name="res_cd"         value="<?=$res_cd?>">               <!-- 결과 코드          -->
		<input type="hidden" name="tran_cd"        value="<?=$tran_cd?>">              <!-- 트랜잭션 코드      -->
		<input type="hidden" name="ordr_idxx"      value="<?=$ordr_idxx?>">            <!-- 주문번호           -->
		<input type="hidden" name="good_mny"       value="<?=$good_mny?>">             <!-- 휴대폰 결제금액    -->
		<input type="hidden" name="good_name"      value="<?=$good_name?>">            <!-- 상품명             -->
		<input type="hidden" name="buyr_name"      value="<?=$buyr_name?>">            <!-- 주문자명           -->
		<input type="hidden" name="buyr_tel1"      value="<?=$buyr_tel1?>">            <!-- 주문자 전화번호    -->
		<input type="hidden" name="buyr_tel2"      value="<?=$buyr_tel2?>">            <!-- 주문자 휴대폰번호  -->
		<input type="hidden" name="buyr_mail"      value="<?=$buyr_mail?>">            <!-- 주문자 E-mail      -->
		<input type="hidden" name="cash_yn"		   value="<?=$cash_yn?>">              <!-- 현금영수증 등록여부-->
		<input type="hidden" name="enc_info"       value="<?=$enc_info?>">
		<input type="hidden" name="enc_data"       value="<?=$enc_data?>">
		<input type="hidden" name="use_pay_method" value="<?=$use_pay_method?>">
		<input type="hidden" name="cash_tr_code"   value="<?=$cash_tr_code?>">

		<!-- 추가 파라미터 -->
		<input type="hidden" name="param_opt_1"	   value="<?=$param_opt_1?>">
		<input type="hidden" name="param_opt_2"	   value="<?=$param_opt_2?>">
		<input type="hidden" name="param_opt_3"	   value="<?=$param_opt_3?>">
	</form>
</body>
</html>
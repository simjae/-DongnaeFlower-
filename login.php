<?php
include_once("header.php");

$KAKAO_REST_API_KEY = "79f468c86daf413eff38ec853168a448";
$KAKAO_REDIRECT_URI = "https://dongne-flower.com/api/kakao_login_proc.php";

$shopconfig = shopconfig();

$chUrl=trim(urldecode($_REQUEST["chUrl"]));

if(strlen($_ShopInfo->getMemid())>0) {	
	if (strlen($chUrl)>0) { $onload=$chUrl; }
	else { $onload="./main.php"; }

	echo '<script>location.href="'.$onload.'";</script>';
	exit;
}


?>
<style>
.bannerWrap{
	margin: 30px;
}
.bannerWrap .bannerTitle{
	font-size: 21px;
    color: #393939;
    text-align: center;
    margin:15px;
    line-height: 30px;
}


.bannerWrap .bannerTitle span{
	font-weight: 500;
	color: #282828;
}
.noticeBox {
	margin: 0 auto;
    position: relative;
    width: 250px;
    height: 20px;
    text-align: center;
    line-height: 20px;
    background-color: #fff;
    border: 1px solid #e9387b;
    border-radius: 30px;
    margin-bottom: 15px;
}
.noticeBox:before {
	border: 6px solid;
	border-color: #e9387b transparent transparent transparent;
	content: "";
	position: absolute;
	top: 100%;
	left: 50%;
	transform: translateX(-50%);
}
.noticeBox:after {
	content: "";
	position: absolute;
	width: 0;
	height: 0;
	top: 100%;
	right: 50%;
	transform: translateX(50%);
	border: 4px solid;
	border-color: #fff transparent transparent transparent;
}
.noticeBox .noticeContent{
	color: #e9387b;
	position: relative;
	font-size: 14px;
}
.noticeBox .noticeContent span{
	font-weight: 900;
	color: #e72975;
}
.noticeBox .noticeContent img{
	width: 10px;
	margin: 0 5px -3px 2px;
}
.lastBanner{
	font-size: 16px;
	text-align: center;
	color: #282828;
	margin-top: 40px;
	padding: 30px;
}
.lastBanner span{
	font-weight: 500;
}



</style>

<div id="login">
	<div class="h_area2">
		<h2>로그인 / 회원가입</h2>
	</div>
	<div class="wrapper">
		<div class="bannerWrap">
			<div class="bannerTitle">지금 동네꽃집에 가입하고 <br><span>1만원 할인 </span>받으세요!</div>
		</div>
		<div class="noticeBox">
			<div class="noticeContent"><img src="/app/images/login_Lightning.svg" alt=""><span>클릭 한 번</span>으로<span> 3초 만</span>에 회원가입</div>
		</div>
		<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
		<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
		<input type="hidden" name="shopurl" value="<?=getenv("HTTP_HOST")?>" />
		<IFRAME id="loginiframe" name="loginiframe" style="display:none;" /></IFRAME>
		<?}?>
		<input type="hidden" name="appFlag" value="Y">
		<input type="hidden" name="checkType" id="checkType" value="" />
		<!--
		<table style="width:90%;margin:auto;">
		<tr>
			<td colspan="2">
				<div style="font-weight:100;font-size:14px;line-height:24px">
					휴대전화번호를 입력해 주세요
				</div>
			</td>
		</tr>
		<tr>
			<td style="width:80%">
				<input type="tel" name="phoneNum" id="phoneNum" class="phoneNum" title="전화번호" placeholder="010-1234-5678" maxlength="13" value="<?=$_COOKIE[phone_num]?>" style="font-size: 18px;letter-spacing: 2px;font-weight: 500;" onkeydown="javascript: if (event.keyCode == 13) {CheckForm();}"/>
			</td>
			<td style="width:20%">
				<div class="login_bt" onClick="CheckForm()">확인</div>
			</td>
		</tr>
		</table>
		-->
		<?
		
			if ($arSnsinfo["kko"]["state"] == "Y") {
				echo $kakao->login();
			}
			if ($arSnsinfo["nhn"]["state"] == "Y") {
				echo $naver->login();
			}
		?>
		<script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>
        <div style="width:90%;background:#000000;border-radius:8px;margin:auto;overflow;hidden;">
			<div id="appleid-signin" data-color="black" data-border="true" data-type="sign in"
				  data-mode="center-align"
				  data-type="sign-in"
				  data-color="black"
				  data-border="false"
				  data-height="45"
			></div>
		</div>
        <script type="text/javascript">
            AppleID.auth.init({
                clientId : 'com.dongneFlower.ios.user.login',
                scope : 'name email',
                redirectURI : 'https://dongne-flower.com/sns/apple.php',
                state : 'dongne-flower',
                usePopup : false //or false defaults to false
            });
        </script>

		<div class="lastBanner">꽃이 필요할 땐, <span>동네꽃집</span></div>
		<? if($shopconfig->type != 'free'){ ?>
		<?php if ($_data->sns_login_type == "Y" && count($arSnsinfo) > 0) { ?>
		<!-- SNS 로그인 버튼 -->
		<ul class="sns_login">
			<?php
			if ($arSnsinfo["fb"]["state"] == "Y") {
				echo "<li>".$facebook->login()."</li>";
			}
			?>
		</ul>

		<!--
		<ul class="sns_login">
			<li>
				<a href="#"><h1>N</h1><p>네이버 로그인</p></a>
			</li>
			<li>
				<a href="#"><h1>K</h1><p>카카오 로그인</p></a>
			</li>
			<li>
				<a href="#"><h1>F</h1><p>페이스북 로그인</p></a>
			</li>
		</ul>
		-->
		<?php } ?>
		<? } ?>
		
	</div>
</div>
<div class="bot_index wrapper" style="position:fixed;bottom:0px;border:1px solid #9e9e9e36;">
	<ul>
		<li><a href="agreement.php" rel="external">이용약관</a></li>
		<li><a href="privercy.php" rel="external">개인정보취급방침</a></li>
		<!--<li><a href="http://www.ftc.go.kr/www/bizCommList.do?key=232" target="_blank">사업자확인</a></li>-->
		<? /*if($configRow['use_cross_link']=="Y"){ ?><li><a href="/main/main.php?pc=ON" rel="external">PC버전</a></li><? } */?>
	</ul>
	<p>상호: 디어플로리스트 주식회사 | 대표: 류재언 
	<br>개인정보관리책임자: 김성식 | 전화: 02-3461-0100 | 이메일: dearflorist.co@gmail.com
	<br>
	주소: 서울특별시 서초구 강남대로 27 화훼 사업센터 B1-23 
	<br>사업자등록번호: 109-88-01283 | 통신판매: 제 2019-서울서초-2473호</p>
</div>


<script type="text/javascript">
<!--
$(document).ready(function() {
	$(".move_scroll").hide();
	$("#gnb_button").hide();
	$("#prsearch").hide();
	$("#basket").hide();
	
	$('#phoneNum').keyup(function(e){
	  $(this).val(autoHypenPhone( $(this).val() ));  
	});	
	$("#appleid-signin").children().css({"-webkit-font-smoothing": "antialiased",
    "-moz-osx-font-smoothing": "grayscale",
    "height": "45px",
    "min-height": "30px",
    "max-height": "64px",
    "position": "relative",
    "letter-spacing": "initial",
    "margin": "auto"});
});
function autoHypenPhone(str){
      str = str.replace(/[^0-9]/g, '');
      var tmp = '';
      if( str.length < 4){
          return str;
      }else if(str.length < 8){
          tmp += str.substr(0, 3);
          tmp += '-';
          tmp += str.substr(3);
          return tmp;
      }else{              
          tmp += str.substr(0, 3);
          tmp += '-';
          tmp += str.substr(3, 4);
          tmp += '-';
          tmp += str.substr(7);
          return tmp;
      }
  
      return str;
}



function CheckForm() {

	try {
		if(document.form1.phoneNum.value.length==0) {
			alert("휴대전화번호를 입력하세요.");
			document.form1.phoneNum.focus();
			return;
		}
		else if(document.form1.phoneNum.value.length!=13) {
			alert("올바른 휴대전화번호를 입력하세요.");
			document.form1.phoneNum.focus();
			return;
		}
		document.form1.target = "";
		<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
		if(typeof document.form1.ssllogin!="undefined"){
			if(document.form1.ssllogin.checked==true) {
				document.form1.target = "loginiframe";
				document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>login.php';
			}
		}
		<?}?>
//		document.form1.submit();
		checkPhoneNum();
	} catch (e) {
		alert("로그인 페이지에 문제가 있습니다.\n\n운영자에게 문의하시기 바랍니다.");
	}
}

function checkPhoneNum(){
	var formData = $("form[name=form1]").serialize() ;
	$.ajax({
		type : 'post',
		url : '/api/user_phone_check.php',
		data : formData,
		dataType : 'json',
		error: function(xhr, status, error){
			alert("데이터 통신중에 오류가 발생했습니다.");
		},
		success : function(json){
			
			if(json["result"] == "Y"){
				$("#checkType").val("mem");
				document.form1.action='phone_num_login.php';
				document.form1.submit();
			}
			else if(json["result"] == "N"){
				$("#checkType").val("reg");
				document.form1.action='phone_num_admission.php';
				document.form1.submit();
			}
			else if(json["result"] == "E"){
				alert("올바른 휴대전화번호를 입력하세요.");
				document.form1.phoneNum.focus();
			}
		}
	});
}



function CheckOrder() {
	if(document.form1.ordername.value.length==0) {
		alert("주문자 이름을 입력하세요.");
		document.form1.ordername.focus();
		return;
	}
	if(document.form1.ordercodeid.value.length==0) {
		alert("주문번호 6자리를 입력하세요.");
		document.form1.ordercodeid.focus();
		return;
	}
	if(document.form1.ordercodeid.value.length!=6) {
		alert("주문번호는 6자리입니다.\n\n다시 입력하세요.");
		document.form1.ordercodeid.focus();
		return;
	}
	document.form2.ordername.value=document.form1.ordername.value;
	document.form2.ordercodeid.value=document.form1.ordercodeid.value;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}

	

function order(){
	var _form = document.orderForm;
	if(_form.ordername.value == ""){
		alert("주문자명을 입력해주세요.");
		_form.ordername.focus();
		return;
	}else if(_form.ordercodeid.value == ""){
		alert("주문번호를 입력해주세요.");
		_form.ordercodeid.focus();
		return;

	}
	window.open("about:blank","orderpop");
	document.orderForm.submit();
}
//->
</script>
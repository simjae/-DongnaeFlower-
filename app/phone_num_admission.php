<?php
// 상품가격 0원 오류 관련 수정함(비회원 구매 시 연결링크 수정) 2016-04-07 Seul
include_once("header.php");

$shopconfig = shopconfig();

$chUrl=trim(urldecode($_REQUEST["chUrl"]));

if(strlen($_ShopInfo->getMemid())>0) {	
	if (strlen($chUrl)>0) { $onload=$chUrl; }
	else { $onload="./main.php"; }

	echo '<script>location.href="'.$onload.'";</script>';
	exit;
}


if(strpos($chUrl,"?") && (ereg("order.php",$chUrl) || ereg("order3.php",$chUrl))){
	$orderParm =  substr($chUrl, strpos($chUrl,"?"));
	$chUrl = substr($chUrl,0,strpos($chUrl,"?"));
}
?>


<div id="login">

	<div class="wrapper">
		<?if($_POST["checkType"] == "reg"){?>
			<div style="margin:10% 0 5% 0;font-weight:100;text-align:center;margin-top:50px;font-size:14px;line-height:26px">
				동네꽃집 회원가입을 통해
				<br>원활한 서비스를 이용 하실 수 있습니다.
			</div>
			<h1 style="margin:30px 0 60px 0;font-weight:normal;text-align:center">동네꽃집 <span style="font-weight: 200">가입을 도와드릴게요.</span></h1>
		<?}
		else if($_POST["checkType"] == "mem"){
		?>
			<div style="margin:100px 0 100px 0;font-weight:100;text-align:center;margin-top:50px;font-size:14px;line-height:26px">
				동네꽃집 비밀번호를 잊으셨나요?
				<br>재설정 후에 서비스를 이용 하실 수 있습니다.
			</div>
			<h1 style="margin:30px 0 60px 0;font-weight:normal;text-align:center">동네꽃집 <span style="font-weight: 200">비밀번호를 재설정합니다.</span></h1>
		<?}?>
		<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
		<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
		<IFRAME id="loginiframe" name="loginiframe" style="display:none;" /></IFRAME>
		<?}?>
		<input type="hidden" name="appFlag" value="Y">
		<input type="hidden" name="checkType" value="<?=$_POST["checkType"]?>" />
		<input type="hidden" name="authSeq" id="authSeq" value="" />
		<table style="width:80%;margin:auto;">
		<tr>
			<td style="width:70%">
				<input type="text" class="phoneNum" name="phoneNum" title="전화번호" placeholder="010-1234-1234" value="<?=$_POST[phoneNum]?>" style="font-size: 18px;letter-spacing: 2px;font-weight: 500;" readonly/>
			</td>
			<td style="width:30%">
				<div class="login_bt" onClick="sendSms();">인증하기</div>
			</td>
		</tr>
		<tr>
			<td style="width:70%">
				<input type="number" class="admissionNum disabled" id="admissionNum" name="admissionNum" title="인증번호" placeholder="인증번호를 입력해주세요." value="" maxlength="6"/>
			</td>
			<td style="width:30%">
				<div class="admissionBtn disabled" id="admissionBtn" onClick="CheckForm()">확인</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<br><div id="viewTimer"></div>
			</td>
		</tr>
		</table>

		<div style="font-weight:100;text-align:center;margin-top:50px;font-size:16px;line-height:26px">
			인증번호가 도착하지 않을 경우 ‘인증하기’ 버튼을 다시 눌러주세요.
		</div>
		<? if($shopconfig->type != 'free'){ ?>
		<?php if ($_data->sns_login_type == "Y" && count($arSnsinfo) > 0) { ?>
		<!-- SNS 로그인 버튼 -->
		<ul class="sns_login">
			<?php
			if ($arSnsinfo["nhn"]["state"] == "Y") {
				echo "<li>".$naver->login()."</li>";
			}
			if ($arSnsinfo["kko"]["state"] == "Y") {
				echo "<li>".$kakao->login()."</li>";
			}
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



<script type="text/javascript">
<!--
$(document).ready(function() {
	$(".move_scroll").hide();
	$("#gnb_button").hide();
	$("#prsearch").hide();
	$("#basket").hide();
});
function CheckForm() {

	try {
		if(document.form1.phoneNum.value.length==0) {
			alert("휴대전화번호를 입력하세요.");
			document.form1.phoneNum.focus();
			return;
		}
		if(document.form1.admissionNum.value.length!=6) {
			alert("인증번호를 입력하세요.");
			document.form1.admissionNum.focus();
			return;
		}
		var formData = $("form[name=form1]").serialize() ;
		$.ajax({
			type : 'post',
			url : '/api/auth_check.php',
			data : formData,
			dataType : 'json',
			error: function(xhr, status, error){
				alert("데이터 통신중에 오류가 발생했습니다.");
			},
			success : function(json){
				if(json["result"] == "Y"){
					$("#authSeq").val(json["authSeq"]);
					document.form1.target = "";
					<?if($_POST["checkType"] == "reg"){?>
						document.form1.action='/app/phone_member_join.php';
					<?}
					else if($_POST["checkType"] == "mem"){
					?>
						document.form1.action='/sms/phone_pw_change.php';
					<?}?>
					document.form1.submit();
				}
				else if(json["result"] == "N"){
					alert("인증번호가 틀렸습니다.");
					document.form1.admissionNum.focus();
				}
				else if(json["result"] == "E"){
					alert("오류가 발생했습니다. 로그인 화면으로 이동합니다.");
					location.href="/app/login.php";
				}
			}
		});
	
	} catch (e) {
		alert("로그인 페이지에 문제가 있습니다.\n\n운영자에게 문의하시기 바랍니다.");
	}
}

function sendSms(){
	var formData = $("form[name=form1]").serialize() ;
	$.ajax({
		type : 'post',
		url : '/sms/auth_sms_send.php',
		data : formData,
		dataType : 'json',
		error: function(xhr, status, error){
			alert("데이터 통신중에 오류가 발생했습니다.");
		},
		success : function(json){
			if(json["result"] == "Y"){
				timerStart();
				alert("인증번호가 발송되었습니다.");
			}
			else if(json["result"] == "E"){
				alert("오류가 발생했습니다. 로그인 화면으로 이동합니다.");
				location.href="/app/login.php";
			}
		}
	});
}

var tid = null;
var setTime = 180;
function timerStart(){
	if(tid){
		clearInterval(tid);
	}
	setTime = 180;
	tid=setInterval('msg_time()',1000);
	$("#admissionNum").removeClass("disabled");
	$("#admissionBtn").removeClass("disabled");
	
};
function msg_time() {	// 1초씩 카운트
			
	m = Math.floor(setTime / 60) + "분 " + (setTime % 60) + "초";	
	
	var msg = "남은 시간 : <font color='red'>" + m + "</font>";
	
	$("#viewTimer").html(msg);
			
	setTime--;		
	
	if (setTime <= 0) {	
		
		clearInterval(tid);		// 타이머 해제
		setTime = 180;
		$("#admissionNum").addClass("disabled");
		$("#admissionBtn").addClass("disabled");
		$("#admissionNum").val("");
		$("#viewTimer").html("");
	}
	
}


//->
</script>
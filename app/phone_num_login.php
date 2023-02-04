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

$phoneNum = !_empty($_POST['phoneNum'])?trim($_POST['phoneNum']):"";
$phoneNumArr = explode("-",$phoneNum);

$phoneNum = str_replace("-","",$phoneNum);
$checkSQL = "SELECT 
				id
			FROM tblmember 
			WHERE 
				mobile='".$phoneNum."'";
$checkSQL_result = mysql_query($checkSQL,get_db_conn());
$id = mysql_fetch_object($checkSQL_result)->id;

?>


<div id="login">

	<div class="wrapper">

		<h1 style="margin:25% 0 60px 0;font-weight:normal;text-align:center">동네꽃집 <span style="font-weight: 200">로그인을 <br>도와드릴게요.</span></h1>
		<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
		<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
		<input type="hidden" name="shopurl" value="<?=getenv("HTTP_HOST")?>" />
		<IFRAME id="loginiframe" name="loginiframe" style="display:none;" /></IFRAME>
		<?}?>
		<input type="hidden" name="appFlag" value="Y">
		<input type="hidden" name="checkType" value="<?=$_POST["checkType"]?>" />
		<input type="hidden" class="id" name="id" value="<?=$id?>" />
		<table style="width:95%;margin:auto;">
		<tr>
			<td style="width:170px">
				<input type="text" class="phoneNum" name="phoneNum" title="전화번호" value="<?=$_POST[phoneNum]?>" style="font-size: 18px;border:none;letter-spacing: 1px;font-weight: 500;" readonly/>
			</td>
			<td>
				<div style="font-weight:100;text-align:center;font-size:14px;">
					회원님. 어서오세요~
					<br>동네꽃집 입니다.
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="password" class="password" name="passwd" title="비밀번호" placeholder="비밀번호" value="<?=$save_pw?>" onkeydown="javascript: if (event.keyCode == 13) {CheckForm();}" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="login_bt" onClick="CheckForm()">확인</div>
			</td>
		</tr>
		</table>

		<div style="font-weight:100;text-align:center;margin-top:50px;font-size:16px;line-height:26px">
			<a href="login.php">회원가입</a> / <a href="javascript:passwordReset()">비밀번호 재설정</a>
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
		if(document.form1.id.value.length==0) {
			alert("회원 아이디를 입력하세요.");
			return;
		}
		if(document.form1.passwd.value.length==0) {
			alert("비밀번호를 입력하세요.");
			document.form1.passwd.focus();
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
		document.form1.submit();
	} catch (e) {
		alert("로그인 페이지에 문제가 있습니다.\n\n운영자에게 문의하시기 바랍니다.");
	}
}

function passwordReset() {

	try {
		if(document.form1.phoneNum.value.length==0) {
			alert("휴대전화번호를 입력하세요.");
			document.form1.phoneNum.focus();
			return;
		}
		document.form1.target = "";
		document.form1.action='phone_num_admission.php';
		document.form1.submit();
	} catch (e) {
		alert("로그인 페이지에 문제가 있습니다.\n\n운영자에게 문의하시기 바랍니다.");
	}
}

$(document).ready(function() {
	document.form1.id.focus();

	$(".input_pw").keydown(function(e){
		if(e.keyCode == 13){
			CheckForm();
		}
	});
});
//->
</script>
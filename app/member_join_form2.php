<style>
input{border-radius: 8px;border: solid 1px #e3e3e3;}
.productImage .file{
	display: inline-block;
    height: 40px;
    padding: 0 10px;
    vertical-align: middle;
    border: 1px solid #dddddd;
    width: 78%;
}
.productImage label {
	padding: 13px 9px;
    font-size: 0.85em;
    height: 40px;
    line-height: 37px;
    border-radius: 8px;
    color: white;
    background-color: #505050;
}
.productImage input[type="file"] {
    position: absolute;
    width: 0;
    height: 0;
    padding: 0;
    overflow: hidden;
    border: 0;
}
.productImage .fileFake {
	border-radius: 8px;
    border: solid 1px #e3e3e3;
    height: 40px;
    line-height: 40px;
    padding-left: 0.9em;
    box-sizing: border-box;
    background: #ffffff;
    color: #848484;
    width: 80%;
    font-size: 14px;
    letter-spacing: -1px;
}


</style>
<table cellpadding="0" cellspacing="0" width="100%">
	<? if($_data->resno_type!="N" && strlen($adultauthid)>0){ ###### 서신평 아이디가 존재하면 실명인증 안내멘트######?>
		<tr>
			<td>- 입력하신 이름과 주민번호의 <font color="#F02800"><b>실명확인</b></font>이 되어야 회원가입을 완료하실 수 있습니다.</td>
		</tr>
	<? } ?>
</table>
<!--
<ul style="margin:20px;letter-spacing:-0.05em;">
	<li><span class="font_orange">(＊)</span>는 필수입력 항목입니다.</li>
</ul>
-->
<?
$img_path="../data/profilephoto/";

$sql_mb="SELECT * FROM tblmember where id = '".$_ShopInfo->getMemid()."' ";
$result_mb=mysql_query($sql_mb,get_db_conn());
$row_mb=mysql_fetch_object($result_mb);

$email = $row_mb->email;
$name = $row_mb->name;
$mobile = $row_mb->mobile;
$gender = $row_mb->gender;
$birth3 = $row_mb->birth;
$profile_photo = $img_path.$row_mb->profile_photo;

if($birth3){
	$birth2=str_replace('-','',$birth3);
	$birth=substr($birth2, 0, 4)."-".substr($birth2, 4, 2)."-".substr($birth2, 6, 2);
}

if($row_mb->news_yn=="Y") {
	$news_mail_yn="Y";
	$news_sms_yn="Y";
} else if($row_mb->news_yn=="M") {
	$news_mail_yn="Y";
	$news_sms_yn="N";
} else if($row_mb->news_yn=="S") {
	$news_mail_yn="N";
	$news_sms_yn="Y";
} else if($row_mb->news_yn=="N") {
	$news_mail_yn="N";
	$news_sms_yn="N";
}

//$push_yn = $row_mb->push_yn;
mysql_free_result($row_mb);

?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="memJoinForm">
	<? if (!$loginType) { ?>
	<tr>
		<p style="color: #333333;font-weight: bold;line-height: 1.5em;font-size: 14px;margin-left: 20px;letter-spacing: -1px;padding: 20px 0px 0px 0;">기본정보</p>
	</tr>
	<tr>
		<td style="overflow:hidden;">
			<INPUT type="text" name="id" value="<?=$_ShopInfo->getMemid();?>" placeholder="아이디" class="input" style="float:left;width:100%;border-right:0px;" readonly />
		</td>
	</tr>
	<tr>
		<td>
		<INPUT type=password name="passwd1" value="<?=$passwd1?>" maxLength="20" class="input" placeholder="비밀번호" /></td>
	</tr>
	<tr>
		<td>
		<INPUT type=password placeholder="비밀번호 확인" name="passwd2" value="<?=$passwd2?>" maxLength="20" class="input" /></td>
	</tr>
	<tr>
		<td style="overflow:hidden;">
			<INPUT type=text placeholder="이메일" name="email" value="<?=$email?>" maxLength="100" style="WIDTH:80%;border-right:none;" class="input" />
			<A href="javascript:mailcheck();" class="basic_button grayLineBtn1" style="width:18%;float:right;">중복확인</a>
		</td>
	</tr>
	<tr>
		<td><INPUT type="text" name="name" value="<?=$name?>" maxLength="15" class="input" placeholder="이름" readonly/></td>
	</tr>
	<tr>
		<td><INPUT type="text" name="mobile" value="<?=$mobile?>" maxLength="15" class="input"  placeholder="휴대전화" /></td>
	</tr>
	<? } ?>

	<? if($ext_cont['reqgender'] != 'H'){ ?>
	<tr>
		<td>
			<p><? if($ext_cont['reqgender'] == 'Y'){?><?}?>성별</p>
			<label><INPUT type="radio" name="gender" value="1" class="formCheckbox" <? if($gender == "1"){ echo "checked"; } ?> /> 남성</label>
			<span style="padding:0px 5px"></span>
			<label><INPUT type="radio" name="gender" value="2" class="formCheckbox" <? if($gender == "2"){ echo "checked"; } ?> /> 여성</label>
		</td>
	</tr>
	<? } ?>

	<? if($ext_cont['reqbirth'] != 'H'){ ?>
	<tr>
		<td>
		<? if($ext_cont['reqbirth'] == 'Y'){?><?}?>
		<INPUT type="text" name="birth" id="birth_day" value="<?=$birth?>" maxLength="10" class="input"  placeholder="생년월일(예 <?=date('Y-m-d')?>)"/><!--<span style="display:block;font-size:0.9em;padding:5px 0px;"> 예)<?=date('Y-m-d')?></span>--></td>
	</tr>
	<? } ?>
	<tr>
		<td>
			<div class="productImage">
				<p>프로필 사진</p>
				<div>
					<img src="<?php echo $profile_photo?>" width="120" onerror="this.src='skin/basic/svg/usermodify_profile.svg'" alt="" style="width:150px !important;margin: 20px 0 20px 0;" align="center"/>
				</div>
				<input class="fileFake" value="선택된 파일 없음"disabled>
				<label for="file">파일 선택</label>
				<input id="file" type="file" name="profile_photo" accept="image/*" style="width:100%;" class="file" />
				<p style="color: #d03b71;margin-left: 20px;">*권장사이즈 : 150 X 150</p>
			</div>
		</td>
	</tr>
<?
	if($recom_ok=="Y") {
		if($recom_url_ok=="Y" && $_COOKIE['url_id'] != ""){
			if($_data->recom_addreserve >0){
?>
	<tr>
		<td>
		<p>추가적립금</p>
		<?=$_COOKIE['url_name']?>(<?=$_COOKIE['url_id']?>)님의 초대로 적립금 <?=$_data->recom_addreserve?>원을 추가 적립해 드립니다.<input type="hidden" name="rec_id" value="<?=$_COOKIE['url_id']?>"></td>
	</tr>
<?
		}else{
?>
	<tr>
		<td>
		<p>추천인</p>
		<?=$_COOKIE['url_name']?>(<?=$_COOKIE['url_id']?>)님의 초대를 받았습니다.<input type="hidden" name="rec_id" value="<?=$_COOKIE['url_id']?>" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;"></td>
	</tr>
<?
		}
	}else{
?>
	<tr>
		<td>
		<p>추천 ID</p>
		<INPUT type="text" name="rec_id" maxLength="12" value="<?=$rec_id?>" class="input" /></td>
	</tr>
<?
		}
	}

	if(strlen($straddform)>0) {
		echo $straddform;
	}
?>
	<tr>
		<td>
			<ul class="joinCheckList">
				<!--
				<li class="policy" style="overflow:hidden;">
					<label><INPUT type="checkbox" name="policy" id="policy" class="formCheckbox" value="Y"  /> 이용약관<span class="point7">필수</span></label>
					<span style="float:right" class="viewPolicyBtn">내용보기 ></span>
				</li>
				<li class="protect" style="overflow:hidden;">
					<label><INPUT type="checkbox" name="protect" id="protect"  class="formCheckbox" value="Y"  /> 개인정보 취급방침<span class="point7">필수</span></label>
					<span style="float:right" class="viewProtectBtn">내용보기 ></span>
				</li>
				-->
				<!--
				<li class="protectUse" style="overflow:hidden;">
					<label><INPUT type="checkbox" name="protectuse" id="protectuse" class="formCheckbox" value="Y"  /> 개인정보 수집 및 이용(필수)</label>
					<span style="float:right" class="viewprotectUseBtn">내용보기 ></span>
				</li>
-->
				<li class="receiveMail"><label class="checkLabel"for="mailCheck"><INPUT id="mailCheck"type="checkbox" name="news_mail_yn" class="formCheckbox" value="Y" <?=(($news_mail_yn=="Y") ? "checked" : "")?> /> 이메일 수신동의</label></li>
				<li class="receiveSms"><label class="checkLabel"for="snsCheck"><INPUT id="snsCheck" type="checkbox" name="news_sms_yn" class="formCheckbox" value="Y" <?=(($news_sms_yn=="Y") ? "checked" : "")?> /> APP푸시 수신동의</label></li>
				<? /*<li class="receiveSms"><label><INPUT type="checkbox" name="push_yn" class="formCheckbox" value="Y" <?//=(($push_yn=="Y") ? "checked" : "")?//> /> 푸쉬 수신동의</label></li>*/ ?>
			</ul>
		</td>
	</tr>
</table>

<!-- 약관확인/동의 버튼
<div class="policyBtn">
	<div class="basic_button viewPolicyBtn">회원약관</div>
	<div class="basic_button viewProtectBtn">개인정보취급방침</div>
</div>
 -->
<!-- 약관 전체보기 -->
<div id="policyView" class="policyView" style="display:none">
	<DIV class="viewBox1">
		<div class="viewCloseBtn"><a href="#" style="color: #333333;font-size: 40px;text-decoration:none;">×</a></div>
		<h4>회원가입 약관</h4>
		<div class="viewBox2"><?=$agreement?></div>
	</DIV>
</div>

<!-- 개인정보취급방침 전체보기 -->
<div id="ProtectView" class="policyView" style="display:none">
	<DIV class="viewBox1">
		<div class="viewCloseBtn"><a href="#" style="color: #333333;font-size: 40px;text-decoration:none;">×</a></div>
		<h4>개인정보취급방침</h4>
		<div class="viewBox2"><?=$privercy?></div>
	</DIV>
</div>

<!-- 개인정보 수집 및 이용 안내보기 -->
<div id="protectUseView" class="policyView" style="display:none">
	<DIV class="viewBox1">
		<div class="viewCloseBtn"><a href="#" style="color: #333333;font-size: 40px;text-decoration:none;">×</a></div>
		<h4>개인정보 수집 및 이용 안내</h4>
		<div class="viewBox2">
		1)개인정보 수집항목<br>
		성별, 생년월일, 주소, 전화번호, 이메일주소, 메일수신여부, 추천인<br><br>

		2)개인정보의 수집목적<br>
		- 마케팅 및 광고: 신규 서비스(제품) 개발 및 특화, 이벤트 등 광고성 정보 전달, 인구통계학적 특성에 따른 서비스 제공 및 광고 게재<br>
		- 서비스 이용 및 상담 및 유지보수
		<br><br>

		3)개인정보 보유기간<br>
		당사는 개인정보의 수집목적 또는 회원 탈퇴시에는 회원의 개인정보를 지체 없이 파기합니다.<br><br>

		※선택항목 개인정보의 경우 수집/이용 동의 여부와 관계 없이 회원으로 가입할 수 있습니다.
		</div>
	</DIV>
</div>

<div class="basic_btn_area">
	<a href="javascript:CheckForm();"><span  class="join_bt">수정 하기</span></a>
</div>

<!-- 생년월일 달력 호출 -->
<link rel="stylesheet" href="/css/jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>

<script type="text/javascript">

//생년월일 달력 처리
$(function() {
	$("#birth_day").datepicker({
		dateFormat: 'yy-mm-dd',
		prevText: '이전 달',
		nextText: '다음 달',
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		showMonthAfterYear: true,
		changeMonth: true,
		changeYear: true,
		yearSuffix: '년',
		maxDate: '+0d',
		yearRange: "-100:+0"	
	});
});
//-->
$(document).ready(function(){ 
  var fileTarget = $("#file"); 
  fileTarget.on('change', function(){ // 값이 변경되면
      var cur=$(".productImage input[type='file']").val();
    $(".fileFake").val(cur);
  }); 
}); 

</script>
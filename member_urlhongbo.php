<?
include_once("header.php");

$mode=$_POST["mode"];

if(!_empty($_ShopInfo->getMemid())){
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_mdata=$row;
		$sendUrl_id = $row->url_id;
		$sendId = $row->id;
		$sendName = $row->name;
		$sendEmail = $row->email;
	}
	mysql_free_result($result);
}
if($_data->recom_url_ok != "Y"){
	echo "<html><head><title></title></head><body onload=\"alert('홍보적립금이 설정되어있지않습니다.');window.close();\"></body></html>";exit;
}

if($mode=="send" && $sendUrl_id && $sendName) {
	$arEmails=explode(",", $_POST["in_email"]);
	$message=$_POST["in_message"];
	
	$mess2=$row->email."로 메일을 ";
	for($i=0;$i<sizeof($arEmails);$i++) {
		SendUrlMail($_data->shopname, $_data->shopurl, $_data->design_mail, $message, $sendEmail, $arEmails[$i], $sendName, $sendUrl_id, $sendId, $_data->recom_memreserve);
	}
	echo "<html><head><title></title></head><body onload=\"alert('메일이 전송되었습니다.'); location.href='/m/member_urlhongbo.php'; \"></body></html>";exit;
}

$hongboUrl = "http://".$_data->shopurl."?token=".$sendUrl_id;
$hongboTle = sprintf("[%s]에 가입하세요.",$_data->shopname);

$sAddRecom = "";
if($_data->reserve_join >0){
	$sAddRecom = $_data->shopname." 가족이 되시면 <span style=\"color:#CC0035\">".$_data->reserve_join."원</span>의 적립금을 드림니다.<br/>";
}
if($_data->recom_ok == "Y") {
	$arRecomType = explode("", $_data->recom_memreserve_type);

	if($arRecomType[0] == "A"){
		$sAddRecom.= "소개 받은 친구들의 신규회원가입시 <span style=\"color:#CC0035\">".$_data->recom_memreserve."원</span>의 적립금을 받으실 수 있답니다.</span>";
		$sAddRecom2 ="회원님의 URL로 들어와 신규회원가입을 할 경우 <span style=\"color:#CC0035\">".number_format($_data->recom_memreserve)."원</span>의 적립금을 드립니다.";
	}else if($arRecomType[0] == "B"){
		$sAddRecom .= "소개 받은 친구들의 첫 구매가 완료될 때마다 <span style=\"color:#CC0035\">";
		$sAddRecom2 = "회원님에 URL주소로 들어오실 경우 회원님에게 <span style=\"color:#CC0035\">";
		if($arRecomType[1] == "A"){
			if($arRecomType[2] == "N"){
				$sAddRecom .= $_data->recom_memreserve."원의";
				$sAddRecom2 .= $_data->recom_memreserve."원</span>의";
			}else if($arRecomType[2] == "Y"){
				$sAddRecom .= "구매금액의 ".$_data->recom_memreserve."%의";
				$sAddRecom2 .= "구매금액의 ".$_data->recom_memreserve."%</span>의";
			}
		}else if($arRecomType[1] == "B"){
			$sAddRecom .= "구매금액에 따른";
			$sAddRecom2 .= "구매금액에 따른</span>";
		}
		$sAddRecom .= " 적립금</span>을 받으실 수 있답니다.";
		$sAddRecom2 .=" 적립금을 드리며<br>단,친구분들이 첫 구매가 완료될때 적립금을 지급해드립니다.";
	}
}

// SMS 홍보 발송
if( $mode == "sms_urlhongbo" ) {
	$sql="SELECT * FROM tblsmsinfo ";
	$result=mysql_query($sql,get_db_conn());
	if($rowsms=mysql_fetch_object($result)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;

		$sender = $_POST["send1"].$_POST["send2"].$_POST["send3"];
		$cell = $_POST["cel1"].$_POST["cel2"].$_POST["cel3"];

		$msg_hongbo = "[".$_data->shopname."]".$sendName."님이 " .$_data->shopname. "(".$hongboUrl.")을 추천하셨어요!!";

		$etcmsg = "가입추천 URL";

		$use_mms = $rowsms->use_mms;

		$temp=SendSMS2($sms_id, $sms_authkey, $cell, "", $sender, 0, $msg_hongbo, $etcmsg, $use_mms);
		$resmsg=explode("[SMS]",$temp);
		echo "<html></head><body onload=\"alert('".$resmsg[1]."'); location.href='/m/member_urlhongbo.php'; \"></body></html>";
		exit;
	}
}
$functionname = "";
$nologin ="";
if(strlen($_ShopInfo->getMemid()) > 0){
	$functionname = 'this.selectionStart=0; this.selectionEnd=this.value.length;';
}else{
	$functionname = 'nologin()';
	$nologin ='onClick="nologin();"';
}

$appname = $_data->shopname;
$appid =$_SERVER['HTTP_HOST'];
$cacaostorycontent = "";
$cacaostoryimgsrc = "http://".$_SERVER['HTTP_HOST']."/m/upload/logo.gif";
?>
<div id="content">
	<div class="h_area2">
		<h2>홍보적립금혜택</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<script type="text/javascript" src="../m/js/kakao.link.js"></script>
	<script>
		
		function IsMailCheck(email) {
			isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$/;
			if(isMailChk.test(email)) {
				return true;
			} else {
				return false;
			}
		}
		function CheckForm() {
			if(document.form1.in_email.value.length==0) {
				alert("이메일을 입력하세요.");
				document.form1.in_email.focus();
				return;
			}
			var email = document.form1.in_email.value;
			if(email.indexOf(",") >0){
				arEmail = email.split(",");
				for(i=0;i<arEmail.length;i++){
					if(!IsMailCheck(arEmail[i].trim())) {
						alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
						document.form1.in_email.focus(); return;
					}
				}
			}else{
				if(!IsMailCheck(email.trim())) {
					alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
					document.form1.in_email.focus(); return;
				}
			}
			if(document.form1.in_message.value.length==0) {
				alert("내용을을 입력하세요.");
				document.form1.in_message.focus();
				return;
			}
			document.form1.mode.value="send";
			document.form1.submit();
		}

		function goFaceBook()
		{
			var href = "http://www.facebook.com/sharer.php?u=" + encodeURIComponent('<?=$hongboUrl?>') + "&t=" + encodeURIComponent('<?=$hongboTle?>');
			var a = window.open(href, 'Facebook', '');
			if (a) {
				a.focus();
			}
		}

		function goTwitter()
		{
			var href = "http://twitter.com/share?text=" + encodeURIComponent('<?=$hongboTle?>') + " " + encodeURIComponent('<?=$hongboUrl ?>');
			var a = window.open(href, 'Twitter', '');
			if (a) {
				a.focus();
			}
		}
		function nologin(){
			alert('전용 홍보URL은 회원전용 기능입니다.\n회원 로그인 후 이용해 주세요.');
			window.location='/m/login.php?chUrl='+"<?=getUrl()?>";
		}
		function setstate(){
			var _obj = document.getElementById('urlhongbo');
			_obj.readOnly= true;
			return;
		
		}
</SCRIPT>
	<section id="sec_urlprom_wrap">
		<div class="div_urltopmsg">
			<h2>적립금이 차곡차곡!<br />친구에게 쇼핑몰을 소개해 주세요!</h2>
			<div style="margin-top:15px;">
				<ul style="padding-left:60px; background:url('/images/design/detail_pop_email_img01.gif') no-repeat; background-size:auto 50px;">
					<li>아래 회원님의 <b>URL주소</b>를 복사해 주세요!</li>
					<li><b>복사 된URL주소</b>와 함께 다른 분들이 쇼핑몰을 가입할 수 있도록 <b><font color="#E6B044">카페, 블로그, 각종 SNS</font></b> 등을 통해 쇼핑몰을 소개해주세요.</li>
				</ul>

				<ul style="height:50px; margin-top:10px; padding-top:8px; padding-left:60px; background:url('/images/design/detail_pop_email_img02.gif') no-repeat; background-size:auto 50px;">
					<li><?=$sAddRecom2?></li>
				</ul>
			</div>
		</div>

		<div class="div_urlarea">
			<h4>· 홍보URL</h4>
			<p><input type="text" name="urlhong" id="urlhongbo" value="<?=$hongboUrl?>" onClick="<?=$functionname?>;setstate();" /></p>
			<p class="p_urlareamsg">
			* 주소를 터치 하시면 나타나는 메뉴에서 복사할 수 있습니다.<br/>
			* 모바일 환경에 따라 복사가 되지 않을 경우 주소부분을 길게 터치하시면, 전체 선택 후 복사가 가능합니다.<br/>
			
			</p>
		</div>
		<?
			$smsCount = smsCountValue();
			if( $smsCount > 0 AND strlen($_ShopInfo->getMemid())>0 AND $_ShopInfo->getMemid()!="deleted" ){
		?>
		<script>
			function sms_urlhongbo_send () {
				if(document.form2.send1.value.length==0) {
					alert("SMS 발신자 번호를 입력하세요.");
					document.form2.send1.focus();
					return false;
				}
				if(document.form2.send2.value.length==0) {
					alert("SMS 발신자 번호를 입력하세요.");
					document.form2.send2.focus();
					return false;
				}
				if(document.form2.send3.value.length==0) {
					alert("SMS 발신자 번호를 입력하세요.");
					document.form2.send3.focus();
					return false;
				}
				if(document.form2.cel1.value.length==0) {
					alert("SMS 수신자 번호를 입력하세요.");
					document.form2.cel1.focus();
					return false;
				}
				if(document.form2.cel2.value.length==0) {
					alert("SMS 수신자 번호를 입력하세요.");
					document.form2.cel2.focus();
					return false;
				}
				if(document.form2.cel3.value.length==0) {
					alert("SMS 수신자 번호를 입력하세요.");
					document.form2.cel3.focus();
					return false;
				}
				document.form2.submit();
			}
		</script>
		<div class="div_smsarea">
			<h4>· SMS로 소개하기</h4>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type="hidden" name="mode" value="sms_urlhongbo">
				<div class="div_smsinfo">
					<p>
						<span>발신자 번호</span> :
						<input type="number" name="send1" size="5" maxlength="4" <?=$nologin?>>-<input type="number" name="send2" size="5" maxlength="4" <?=$nologin?>>-<input type="number" name="send3" size="5" maxlength="4" <?=$nologin?>>
					</p>
					<br/>
					<p>
						<span>수신자 번호</span> :
						<input type="number" name="cel1" size="5" maxlength="4" <?=$nologin?>>-<input type="number" name="cel2" size="5" maxlength="4" <?=$nologin?>>-<input type="number" name="cel3" size="5" maxlength="4" <?=$nologin?>>
					</p>
					<br/>
					<p class="p_btnsmsarea">
						<button id="btn_sendsms" class="button white bigrounded" onClick="return sms_urlhongbo_send();">SMS발송</button>
					</p>
				</div>
			</form>
		</div>
		<?
			}
			if($_data->sns_ok == "Y"){
		?>
		<div class="div_snsarea sns_wrap">
			<h4>· SNS홍보하기</h4>
			<?
				$kakaotalk_func=$kakaostory_func="";
				if($_ShopInfo->getMemid() ==""){
					$kakaotalk_func=$kakaostory_func='javascript:nologin();';
				}else{
					$kakaotalk_func="javascript:executeKakaoLink('".$hongboTle."','".$hongboUrl."','".$appid."','".$appname."');";
					$kakaostory_func="javascript:executeKakaoStoryLink('".$hongboUrl."','".$appid."','".$appname."','".$hongboTle."','','".$cacaostoryimgsrc."');";
				}
			?>
			<span>
				<a href="javascript:goTwitter();"><div class="snstwitter"></div></a>
				<a href="javascript:goFaceBook();"><div class="snsfacebook"></div></a>
				<a href="<?=$kakaotalk_func?>"><div class="snskakaotalk"></div></a>
				<a href="<?=$kakaostory_func?>"><div class="snskakaostory"></div></a>
			</span>
		</div>
		<?
			}
		?>
		<div class="div_mailarea">
			<h4>· E-mail 홍보하기</h4>
			<div>
				<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
					<input type=hidden name=mode value="">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tbody>
							<tr>
								<th>이메일 :</th>
								<td><input type="text" name="in_email" <?=$nologin?>></td>
							</tr>
							<tr>
								<th>내&nbsp;&nbsp;&nbsp;&nbsp;용 :</th>
								<td>
<textarea name="in_message" rows="3" noresize <?=$nologin?>>
<?=$sendName?>님께서 <?=$_data->shopname?>(<?=$hongboUrl?>)을 추천하셨어요!!
</textarea>
<!--
<?//=$sendName?>님께서 귀하께 <?//=$_data->shopname?>을 추천하셨습니다.
매일 쏟아지는 <?//=$_data->shopname?>의 혜택을 만나보세요.
<?//=$hongboUrl?>
-->
								</td>
							</tr>
						</tbody>
					</table>
				</form>
				<div class="div_btnmailarea">
					<button class="button white bigrounded" onClick="<?=($_ShopInfo->getMemid() != "")? 'CheckForm()':'nologin()'?>;" id="btn_sendmail">추천하기</button>
				</div>
			</div>
		</div>
	</section>

</div>
<?
include_once("footer.php");
?>
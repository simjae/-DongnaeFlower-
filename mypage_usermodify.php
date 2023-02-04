<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
		exit;
	}

	include "header.php";

	//솔루션타입 확인
	$shopconfig = shopconfig();

	$ip = getenv("REMOTE_ADDR");

	//회원가입 약관정보 가져오기
	$sql="SELECT agreement,agreement2,privercy FROM tbldesign ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$agreement=$row->agreement;
	$agreement2=$row->agreement2;
	$privercy_exp=@explode("=", $row->privercy);
	$privercy=$privercy_exp[1];
	mysql_free_result($result);

	//일반회원 약관
	if(strlen($agreement)==0) {
		$buffer="";
		$fp=fopen($Dir.AdminDir."agreement.txt","r");
		if($fp) {
			while (!feof($fp)) {
				$buffer.= fgets($fp, 1024);
			}
		}
		fclose($fp);
		$agreement=$buffer;
	}

	//도매회원 약관
	if(strlen($agreement2)==0) {
		$buffer2="";
		$fp=fopen($Dir.AdminDir."agreement2.txt","r");
		if($fp) {
			while (!feof($fp)) {
				$buffer2.= fgets($fp, 1024);
			}
		}
		fclose($fp);
		$agreement2=$buffer2;
	}

	//개인정보취급방침
	if(strlen($privercy)==0) {
		$buffer="";
		$fp=fopen($Dir.AdminDir."privercy2.txt","r");
		if($fp) {
			while (!feof($fp)) {
				$buffer.= fgets($fp, 1024);
			}
		}
		fclose($fp);
		$privercy=$buffer;
	}

	$reserve_join=(int)$_data->reserve_join;
	$recom_ok=$_data->recom_ok;
	$recom_url_ok=$_data->recom_url_ok;
	$armemreserve=explode("", $_data->recom_memreserve_type);
	$recom_memreserve=(int)$_data->recom_memreserve;
	$recom_addreserve=(int)$_data->recom_addreserve;
	$recom_limit=$_data->recom_limit;
	if(strlen($recom_limit)==0) $recom_limit=9999999;
	$group_code=$_data->group_code;
	$member_addform=$_data->member_addform;

	unset($adultauthid);
	unset($adultauthpw);
	if(strlen($_data->adultauth)>0) {
		$tempadult=explode("=",$_data->adultauth);
		if($tempadult[0]=="Y") {
			$adultauthid=$tempadult[1];
			$adultauthpw=$tempadult[2];
		}
	}

	$type=$_POST["type"];

	$extconf = array();
	if(false !== $eres = mysql_query("select * from extra_conf where type='memconf'",get_db_conn())){
		if(mysql_num_rows($eres)){
			while($erow = mysql_fetch_assoc($eres)){
				$extconf[$erow['name']] = $erow['value'];
			}
		}
	}

	unset($straddform);
	unset($scriptform);
	unset($stretc);


	if(strlen($member_addform)>0) {
		$straddform.="<tr><td height=\"10\" colspan=\"4\"></td></tr>";
		$straddform.="<tr height=\"23\" bgcolor=\"#585858\">\n";
		$straddform.="	<td colspan=4 align=center style=\"font-size:11px;\"><font color=\"FFFFFF\" ><b>추가정보를 입력하세요.</b></font></td>\n";
		$straddform.="</tr>\n";
		$straddform.="<tr><td height=\"5\" colspan=\"4\"></td></tr>";
		
		$fieldarray=explode("=",$member_addform);
		$num=sizeof($fieldarray)/3;
		for($i=0;$i<$num;$i++) {
			if (substr($fieldarray[$i*3],-1,1)=="^") {
				$fieldarray[$i*3]="<font color=\"#F02800\"><b>＊</b></font><font color=\"#000000\"><b>".substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1)."</b></font>";
				$field_check[$i]="OK";
			} else {
				$fieldarray[$i*3]="<font color=\"#000000\"><b>".$fieldarray[$i*3]."</b></font>";
			}
			
			$stretc.="<tr>\n";
			$stretc.="	<td align=\"left\"  style=\"padding-left:14px\">".$fieldarray[$i*3]."</td>\n";
			
			$etcfield[$i]="<input type=text name=\"etc[".$i."]\" value=\"".$etc[$i]."\" size=\"".$fieldarray[$i*3+1]."\" maxlength=\"".$fieldarray[$i*3+2]."\" id=\"etc_".$i."\" class=\"input\" style=\"BACKGROUND-COLOR:#F7F7F7;\">";
			
			$stretc.="	<td colspan=\"3\">".$etcfield[$i]."</td>\n";
			$stretc.="</tr>\n";
			$stretc.="<tr>\n";
			$stretc.="	<td height=\"10\" colspan=\"4\" background=\"".$Dir."images/common/mbjoin/memberjoin_p_skin_line.gif\"></td>";
			$stretc.="</tr>\n";
			
			if ($field_check[$i]=="OK") {
				$scriptform.="try {\n";
				$scriptform.="	if (document.getElementById('etc_".$i."').value==0) {\n";
				$scriptform.="		alert('필수입력사항을 입력하세요.');\n";
				$scriptform.="		document.getElementById('etc_".$i."').focus();\n";
				$scriptform.="		return;\n";
				$scriptform.="	}\n";
				$scriptform.="} catch (e) {}\n";
			}
		}
		$straddform.=$stretc;
	}


	if($type=="modfiy") {
		$img_path="../data/profilephoto/";
		
		$loginType		= $_POST["loginType"];
		$oldpasswd		= $_POST["oldpasswd"];
		$passwd1		= $_POST["passwd1"];
		$passwd2		= $_POST["passwd2"];
		$name			= trim($_POST["name"]);
		$nickname		= trim($_POST["nickname"]);
		$email			= trim($_POST["email"]);
		$news_mail_yn	= $_POST["news_mail_yn"];
		$news_sms_yn	= $_POST["news_sms_yn"];
		$home_tel		= str_replace("-","",trim($_POST["home_tel"]));
		$zipcode_home	= trim($_POST["home_post1"]);

		$birth					= trim($_POST["birth"]);
		$gender				= trim($_POST["gender"]);

		$home_addr1		= trim($_POST["home_addr1"]);
		$home_addr2		= trim($_POST["home_addr2"]);
		$mobile			= str_replace("-","",trim($_POST["mobile"]));
		$zipcode_office	= trim($_POST["office_post1"]);
		$office_addr1	= trim($_POST["office_addr1"]);
		$office_addr2	= trim($_POST["office_addr2"]);
		$rec_id			= trim($_POST["rec_id"]);
		$etc			= $_POST["etc"];
		$profile_photo_tmp			= $_FILES['profile_photo']['tmp_name'];
		//$profile_photo_name			= $_FILES['profile_photo']['name'];
		$profile_photo_name			= time().uniqid();
		$profile_photo_size			= $_FILES['profile_photo']['size'];

		if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
			$news_yn="Y";
		} else if($news_mail_yn=="Y") {
			$news_yn="M";
		} else if($news_sms_yn=="Y") {
			$news_yn="S";
		} else {
			$news_yn="N";
		}

		$sql = "UPDATE tblmember SET ";
		if(strlen($passwd1)>0) {
			$sql.= "passwd	= '".md5($passwd1)."', ";
		}
		$sql.= "email		= '".$email."', ";
		$sql.= "mobile		= '".$mobile."', ";
		$sql.= "news_yn		= '".$news_yn."', ";
		if(strlen($gender)>0){
			$sql.= "gender	= '".$gender."', ";
		}
		$sql.= "birth			= '".$birth."', "; //생년월일
		if ($profile_photo_tmp != NULL) {
			$sql.= "profile_photo   = '".$profile_photo_name."' "; //프로필 사진
		}
		$sql.= "WHERE id	='".$_ShopInfo->getMemid()."' ";
		$update=mysql_query($sql,get_db_conn());
		move_uploaded_file($profile_photo_tmp,$img_path.$profile_photo_name);

		echo "<html></head><body onload=\"alert('회원정보가 수정되었습니다.');location.href='/app/mypage.php';\"></body></html>";exit;
	}

	if(strlen($news_mail_yn)==0) $news_mail_yn="Y";
	if(strlen($news_sms_yn)==0) $news_sms_yn="Y";

	//성별, 생년월일 필드값 상태
	$ext_cont = array();
	$esql = "select * from extra_conf where type='memconf'";
	if(false !== $eres = mysql_query($esql,get_db_conn())){
		$erowcount = mysql_num_rows($eres);
		if($erowcount>0){
			while($erow = mysql_fetch_assoc($eres)){
				$ext_cont[$erow['name']] = $erow['value'];
			}
		}else{
			$ext_cont['reqgender']=$ext_cont['reqbirth']="H";
		}
	}
?>

<div id="content">
	<div class="h_area2">
		<h2>회원정보 수정</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="type" value="" />
		<input type="hidden" name="idChk" value="<?=$idChk?>" />
		<input type="hidden" name="mailChk" value="<?=$mailChk?>" />
		<?if (strlen($loginType)>0) {?>
		<input type="hidden" name="loginType" value="<?=$loginType?>" />
		<? } ?>
		<input type="hidden" name="agreement" value="N" />
		<input type="hidden" name="privercy" value="N" />
		<? if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MJOIN"]=="Y"){ ?>
		<input type="hidden" name="shopurl" value="<?=getenv("HTTP_HOST")?>" />
		<? } ?>
		<? include ("member_join_form2.php"); ?>
	</form>
</div>

<script type="text/javascript">
	<!--
	function IsMailCheck(email) {
		isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$/;
		if(isMailChk.test(email)) {
			return true;
		} else {
			return false;
		}
	}

	function CheckFormData(data) {
		var numstr = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		var thischar;
		var count = 0;
		data = data.toUpperCase( data )

		for ( var i=0; i < data.length; i++ ) {
			thischar = data.substring(i, i+1 );
			if ( numstr.indexOf( thischar ) != -1 )
				count++;
		}
		if ( count == data.length )
			return(true);
		else
			return(false);
	}

	function CheckForm() {
		var form = document.form1,
			gendercheck = "<?=$extconf['reqgender']?>",
			birthcheck  = "<?=$extconf['reqbirth']?>",
			gendercount = 0;

		if(gendercheck == "Y"){
			for(var i=0;i<form.gender.length;i++){
				if(form.gender[i].checked==true){
					gendercount++;
				}
			}
		}


	<? if (!$loginType) { /* SNS에서 넘어 오지 않았을 때 */ ?>
		if(form.id.value.length==0) {
			alert("아이디를 입력하세요."); form.id.focus(); return;
		}


		if(form.passwd1.value != ""){
			if(form.passwd1.value!=form.passwd2.value) {
				alert("비밀번호가 일치하지 않습니다."); form.passwd2.focus(); return;
			}
		}

		if(form.email.value.length==0) {
			alert("이메일을 입력하세요."); form.email.focus(); return;
		}
		if(!IsMailCheck(form.email.value)) {
			alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요."); form.email.focus(); return;
		}
		if(form.mailChk.value=="0") {
			alert("이메일 중복 체크를 하셔야 합니다!");
			mailcheck();
			return;
		}

		if(form.name.value.length==0) {
			alert("고객님의 이름을 입력하세요."); form.name.focus(); return;
		}
		if(form.name.value.length>10) {
			alert("이름은 한글 5자, 영문 10자 이내로 입력하셔야 합니다."); form.name.focus(); return;
		}
//		if(form.mobile.value.length==0) {
//			alert("휴대전화를 입력하세요."); form.mobile.focus(); return;
//		}
	<? } /* SNS에서 넘어 오지 않았을 때 */ ?>

		if(gendercheck == "Y" && gendercount <= 0){
			alert("성별을 선택하세요");form.gender.value.focus();return;
		}
		if(birthcheck == "Y" && form.birth.value==""){
			alert("생년월일을 입력하세요");form.birth.value.focus();return;
		}

	<?=$scriptform?>

		form.type.value="modfiy";

	<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
		form.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>member_join.php';
	<?}?>
		if(confirm("수정 하겠습니까?")) {
			form.submit();
		} else {
			return;
		}
	}

	//이메일 중복체크(wrap_layer_popup은 footer.php에 있음)
	function mailcheck() {
		var _form = document.form1;
		if(!IsMailCheck(_form.email.value)) {
			alert("이메일 형식이 맞지않습니다.\n확인 후 다시 입력하세요.");
			_form.mailChk.value="0";
			_form.email.focus();
			return;
		}else{
			$('#show_contents').html("");
			$.post('mailcheck.php?email='+_form.email.value, function(data){
				$('#show_contents').html(data);
			});

			$('#wrap_layer_popup').dialog({
				create:function(){
					$(this).parent().css({position:"fixed"});
				},
				title: '이메일 중복체크',
				modal: true,
				width: '90%',
				height: 'auto',
				/*
				buttons: {
					"닫기": function() {
						$(this).dialog("close");
					}
				}
				*/
			});
		}
	}

	//약관보기
	$(document).ready(function() {
		$(".viewPolicyBtn").on("click", function() {
			$("#policyView").show();
		});
		$("#policyView .viewCloseBtn").on("click", function() {
			$("#policyView").hide();
		});

		$(".viewProtectBtn").on("click", function() {
			$("#ProtectView").show();
		});
		$("#ProtectView .viewCloseBtn").on("click", function() {
			$("#ProtectView").hide();
		});

		$(".viewprotectUseBtn").on("click", function() {
			$("#protectUseView").show();
		});
		$("#protectUseView .viewCloseBtn").on("click", function() {
			$("#protectUseView").hide();
		});
	});
	//-->
</script>

<? include_once('footer.php'); ?>
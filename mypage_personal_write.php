<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once("header.php");
if(strlen($_ShopInfo->getMemid())==0) {
	echo "<script>window.close();</script>"; exit;
}

if($_data->personal_ok!="Y") {
	echo "<html></head><body onload=\"alert('본 쇼핑몰에서는 1:1고객문의 게시판 기능을 사용하지 않습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.');window.close();\"></body></html>";exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('잘못된 접근입니다.');window.close()\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('잘못된 접근입니다.');window.close()\"></body></html>";exit;
	}
}
mysql_free_result($result);
$smsCount = smsCountValue ();
$boardsmsSQL = "SELECT smsused, leavenumber FROM personalboard_admin WHERE type='SMS' LIMIT 0, 1";
$smsused=$numberlist="";
if(false !== $boardsmsRes = mysql_query($boardsmsSQL,get_db_conn())){
	$boardsmsrowcount = mysql_num_rows($boardsmsRes);

	if($boardsmsrowcount >0){
		$smsused = mysql_result($boardsmsRes,0,0);
		$numberlist = mysql_result($boardsmsRes,0,1);
	}
	mysql_free_result($boardsmsRes);
}

$smsinfoSQL = "SELECT id, authkey, return_tel FROM tblsmsinfo";

if(false !== $smsinfoRes = mysql_query($smsinfoSQL,get_db_conn())){
	$smsinforowcount = mysql_num_rows($smsinfoRes);
	
	if($smsinforowcount>0){
		$smsinfo_id = mysql_result($smsinfoRes,0,0);
		$smsinfo_authkey = mysql_result($smsinfoRes,0,1);
		$smsinfo_returntel = mysql_result($smsinfoRes,0,2);
	}
	mysql_free_result($smsinfoRes);
}

$mode=$_POST["mode"];
$up_subject=$_POST["up_subject"];
$up_email=$_POST["up_email"];
$up_content=$_POST["up_content"];

if($mode=="write") {
	$ip=$_SERVER["REMOTE_ADDR"];
	$date=date("YmdHis");

	$sql = "INSERT tblpersonal SET ";
	$sql.= "id			= '".$_mdata->id."', ";
	$sql.= "name		= '".$_mdata->name."', ";
	$sql.= "email		= '".$up_email."', ";
	$sql.= "ip			= '".$ip."', ";
	$sql.= "subject		= '".$up_subject."', ";
	$sql.= "date		= '".$date."', ";
	$sql.= "content		= '".$up_content."' ";
	if(mysql_query($sql,get_db_conn())) {
		
		if($smsCount>0 && strlen($smsinfo_id)>0 && strlen($smsinfo_authkey)>0 && strlen($smsinfo_returntel)>0 && $smsused = "Y" && strlen($numberlist)>0){
			$senddate ="0"; //실시간 반영
			$sendetcmsg="1:1문의 글등록 알림";
			$smssendmsg = "1:1 게시판에 신규문의가 등록되었습니다.";
			$temp=SendSMS($smsinfo_id,$smsinfo_authkey,$numberlist , "", $smsinfo_returntel, 0, $smssendmsg, $sendetcmsg);
		}
		echo '<script>alert("정상적으로 등록되었습니다.");location.href="./mypage_personal_list.php";</script>';
		exit;
	} else {
		$onload="<script>alert('문의글 등록중 오류가 발생하였습니다.');</script>";
	}
}

?>
<div id="content">
	<div class="h_area2">
		<h2>1:1문의</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<div id="personal_container" class="mtom">
		<!--<h2>1:1 문의를 통한 문의내역 및 답변을 볼 수 있습니다.</h2>-->
		<form name="personalForm" method="post" action="<?=$PHP_SELF?>">
			<table border="0" cellpadding="0" cellspacing="0" class="mtom_write">
				<tr>
					<th>문의제목</th>
					<td><input type="text" name="up_subject" class="mtomInput" value="" /></td>
				</tr>
				<tr>
					<th>이메일</th>
					<td><input type="text" name="up_email" class="mtomInput" value="<?=$_mdata->email?>" /></td>
				</tr>
				<tr>
					<th>문의내용</th>
					<td><textarea name="up_content" class="mtomTextarea"></textarea></td>
				</tr>
			</table>

			<div class="personal_box personal_btn_box">
				<a href="#" class="basic_button grayBtn" id="btn_submit">문의등록</a>
				<a href="#" id="btn_reset" class="basic_button">다시작성</a>
			</div>

			<input type="hidden" name="mode" value="write">
		</form>
	</div>
</div>

<script>
	var form = document.personalForm;
	$(".personal_box #btn_submit").on('click', function(){
		
		if($("input[name=up_subject]").val() == "" || $("input[name=up_subject]").val() == null){
			alert("제목을 입력하세요.");
			$("input[name=up_subject]").focus();
			
			return false;
		}else if($("input[name=up_email]").val() =="" || $("input[name=up_email]").val() == null){
			alert("메일을 입력하세요.");
			$("input[name=up_email]").focus();
			
			return false;
		}else if($("textarea[name=up_content]").val() =="" || $("textarea[name=up_content]").val() == null){
			alert("내용을 입력하세요.");
			$("textarea[name=up_content]").focus();
			return false;
		}else{
			$("#btn_submit").hide();
			if(confirm("등록하시겠습니까?")){
				form.submit();
				return;
			}else{
				$("#btn_submit").show();
				return false;
			}
		}
	});

	$("#btn_reset").click(function(){
			form.reset();
			$("input[name=up_email]").val("");
	});

	$("input[name=up_email]").focusout(function(){
		var check =/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;   
		if(check.test($("input[name=up_email]").val()) === false){
			alert("유효하지 이메일 입니다.");
			$("input[name=up_email]").focus();
			return false;
		}
	});
</script>

<? include_once('footer.php'); ?>
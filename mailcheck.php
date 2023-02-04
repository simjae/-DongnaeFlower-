<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$email=$_REQUEST["email"];

if(strlen($email)<=0) {
	$message="<p style=\"margin:20px;\"><font color=#FF3300><b>이메일이 입력되지 않았습니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
} else if(strtolower($email)=="admin") {
	$message="<p style=\"margin:20px;\"><font color=#FF3300><b>사용 불가능한 이메일입니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
} else {
	$sql = "SELECT email FROM tblmember WHERE email='".$email."' ";
	$result = mysql_query($sql,get_db_conn());

	if ($row=mysql_fetch_object($result)) {
		$message="<p style=\"margin:20px;\"><font color=#ff0000><b>이메일이 중복되었습니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
	} else {
		$sql = "SELECT id FROM tblmemberout WHERE id='".$id."' ";
		$result2 = mysql_query($sql,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			$message="<p style=\"margin:20px;\"><font color=#ff0000><b>이메일이 중복되었습니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
		} else {
			$message="<p style=\"margin:20px;\"><b>사용가능한 이메일입니다.</b></p><a href=\"javascript:useEmail();\" class=\"ui-button ui-corner-all ui-widget\">이메일 사용하기</a>";
		}
		mysql_free_result($result2);
	}
	mysql_free_result($result);
}

unset($body);
$sql="SELECT body FROM ".$designnewpageTables." WHERE type='iddup'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
}
mysql_free_result($result);


if(strlen($body)>0) { //개별디자인 사용시
	$pattern=array("(\[MESSAGE\])","(\[OK\])");
	$replace=array($message,"JavaScript:window.close()");
	$body = preg_replace($pattern,$replace,$body);
	if (strpos(strtolower($body),"table")!=false) $body = "<pre>".$body."</pre>";
	else $body = ereg_replace("\n","<br>",$body);

	echo $body;

}else{ //템플릿 사용시
?>
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<tr>
		<td height="80" align="center" class="msgBox"><?=$message?></td>
	</tr>
</TABLE>
<? } ?>

<script type="text/javascript">
<!--
	//아이디 사용하기
	function useEmail(){
		document.form1.mailChk.value="1";
		document.form1.id.value="<?=$email?>";
		$('#wrap_layer_popup').dialog("close"); //모달창 닫기
	}

	//창닫기
	function close(){
		$('#wrap_layer_popup').dialog("close"); //모달창 닫기
	}
//-->
</script>
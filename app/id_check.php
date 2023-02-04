<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$id=$_REQUEST["id"];

if(strlen($id)<4 || strlen($id)>12) {
	$message="<p style=\"margin:20px;\"><font color=#FF3300><b>아이디는 4~12자까지 입력 가능합니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
} else if(!IsAlphaNumeric($id)) {
	$message="<p style=\"margin:20px;\"><font color=#FF3300><b>사용 불가능한 문자가 사용되었습니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
} else if(!eregi("(^[0-9a-zA-Z]{4,12}$)",$id)) {
	$message="<p style=\"margin:20px;\"><font color=#FF3300><b>사용 불가능한 문자가 사용되었습니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
} else if(eregi("(\'|\"|\,|\.|&|%|<|>|/|\||\\\\|[ ])",$id)) {
	$message="<p style=\"margin:20px;\"><font color=#FF3300><b>사용 불가능한 문자가 사용되었습니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
} else if(strlen($id)<=0) {
	$message="<p style=\"margin:20px;\"><font color=#FF3300><b>아이디가 입력되지 않았습니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
} else if(strtolower($id)=="admin") {
	$message="<p style=\"margin:20px;\"><font color=#FF3300><b>사용 불가능한 아이디입니다.</b></font></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
} else {
	$sql = "SELECT id FROM tblmember WHERE id='".$id."' ";
	$result = mysql_query($sql,get_db_conn());

	if ($row=mysql_fetch_object($result)) {
		//$message="<font color=#ff0000><b>아이디가 중복되었습니다.</b></font>";
		$message="<p style=\"margin:20px;\"><font color=#ff0000><b>아이디가 중복되었습니다.</b></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
	} else {
		$sql = "SELECT id FROM tblmemberout WHERE id='".$id."' ";
		$result2 = mysql_query($sql,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			$message="<p style=\"margin:20px;\"><font color=#ff0000><b>아이디가 중복되었습니다.</b></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:close();\">닫기</a>";
		} else {
			$message="<p style=\"margin:20px;\"><b>사용가능한 아이디입니다.</b></p><a class=\"ui-button ui-corner-all ui-widget\" href=\"javascript:useId();\">아이디 사용하기</a>";
		}
		mysql_free_result($result2);
	}
	mysql_free_result($result);
}

unset($body);
$sql="SELECT body FROM tbldesignnewpage WHERE type='iddup'";
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
	<TR>
		<td height="80" align="center" class="msgBox"><?=$message?></td>
	</tr>
</TABLE>
<? } ?>

<script type="text/javascript">
<!--
	//아이디 사용하기
	function useId(){
		document.form1.idChk.value="1";
		$('#wrap_layer_popup').dialog("close"); //모달창 닫기
	}

	//창닫기
	function close(){
		$('#wrap_layer_popup').dialog("close"); //모달창 닫기
	}
//-->
</script>
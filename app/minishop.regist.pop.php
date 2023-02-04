<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$sellvidx=$_POST["sellvidx"];
$mode=$_POST["mode"];
$email_yn=$_POST["email_yn"];

if(strlen($_ShopInfo->getMemid())==0 || strlen($sellvidx)==0) {
	echo "<html></head><body onload=\"window.close()\"></body></html>";exit;
}

$sql = "SELECT brand_name FROM tblvenderstore WHERE vender='".$sellvidx."' ";
$result=mysql_query($sql,get_db_conn());
if(!$row=mysql_fetch_object($result)) {
	echo "<html></head><body onload=\"window.close()\"></body></html>";exit;
}
$brand_name=$row->brand_name;
mysql_free_result($result);

$sql = "SELECT * FROM tblregiststore WHERE id='".$_ShopInfo->getMemid()."' AND vender='".$sellvidx."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$isregist=true;
	if($mode=="update" && preg_match("/^(Y|N)$/",$email_yn)) {
		if($email_yn=="N") {
			$sql = "UPDATE tblregiststore SET email_yn='N' ";
			$sql.= "WHERE id='".$_ShopInfo->getMemid()."' AND vender='".$sellvidx."' ";
			mysql_query($sql,get_db_conn());
		}
	} else {
		$mode="";
	}
} else {
	$isregist=false;
	$sql = "INSERT tblregiststore SET ";
	$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
	$sql.= "vender		= '".$sellvidx."', ";
	$sql.= "email_yn	= 'Y' ";
	if(mysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblvenderstorecount SET cust_cnt=cust_cnt+1 ";
		$sql.= "WHERE vender='".$sellvidx."' ";
		mysql_query($sql,get_db_conn());
	}
}
mysql_free_result($result);

?>

<!DOCTYPE HTML>
<HTML>
<HEAD>
<meta charset="UTF-8">
<title>미니샵 단골매장 등록</title>
<META http-equiv="X-UA-Compatible" content="IE=edge" />
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<link rel="stylesheet" href="./css/skin/default.css" />
<link rel="stylesheet" href="./css/common.css" />

<style>
	BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {color:000000;font-size:0.95em}
</style>

<SCRIPT LANGUAGE="JavaScript">
	<!--
	function PageResize() {
		var oWidth = document.all.table_body.clientWidth + 10;
		var oHeight = document.all.table_body.clientHeight + 55;

		window.resizeTo(oWidth,oHeight);
	}
	<?if($isregist==false){?>
	function fnConfirm() {
		email_yn="Y";
		if(document.all["cusMemo"][1].checked==true) email_yn="N";
		document.form1.email_yn.value=email_yn;
		document.form1.mode.value="update";
		document.form1.submit();
	}
	<?}?>
	//-->
</SCRIPT>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<table border=0 cellpadding=0 cellspacing=0 width=400 style="table-layout:fixed;" id=table_body>
<tr>
	<td align=center>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr><td height=5></td></tr>
	<tr>
		<td style="padding:15px;font-weight:bold">미니샵 단골매장 등록</td>
	</tr>
	<tr><td height=2 bgcolor=red></td></tr>
	<tr><td height=25></td></tr>
	<tr>
		<td align=center>

		<? if($isregist==true){ ?>
			<? if($mode=="update"){ ?>

			<div style="margin-bottom:15px">
				<strong><span style="color:red"><?=$brand_name?></span> 미니샵을 단골매장으로 등록되었습니다.</strong>
				<p style="line-height:140%;color:#585858">
					<?if($email_yn=="Y"){?>
					판매자가 보내는 메일 수신에 동의하셨습니다.<br />
					고객님께 유익한 정보가 전달 될 수 있도록<br />최선의 노력을 다하겠습니다. 감사합니다.
					<?}else{?>
					판매자가 보내는 메일 수신을 거부 하였습니다.<br />
					단골 미니샵에서 보내는 메일 수신을 원하실 경우<br />‘마이페이지 > 단골 미니샵’에서 변경 하실 수 있습니다.
					<?}?>
				</p>
			</div>

			<? }else{ ?>

			<div style="margin-bottom:15px">
				<strong><span style="color:red"><?=$brand_name?></span> 미니샵은 이미 단골 미니샵으로 등록 되었습니다.</strong>
			</div>

			<? } ?>

			<a href="javascript:window.close();" class="basic_button grayBtn" style="padding:0em 1.6em">확인</a>

		<? }else{ ?>

		<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=sellvidx value="<?=$sellvidx?>">
			<input type=hidden name=mode>
			<input type=hidden name=email_yn>
		</form>

		<div style="margin-bottom:15px">
			<strong><span style="color:red">( <?=$brand_name?> )</span> 미니샵을 단골매장으로 등록 합니다.</strong>
			<p style="line-height:14pt;color:#585858">
				단골매장로 등록 하시면 판매자의 이벤트나 <br />
				상품 정보에 대한 메일을 받으실 수 있습니다.<br />미니샵 판매자가 보내는 메일을 수신 하시겠습니까?
			</p>
		</div>

		<div style="margin-bottom:15px">
			<input type="radio" name="cusMemo" value="Y" checked> <label for="">동의함</label>
			<input type="radio" name="cusMemo" value="N"> <label for="">동의하지 않음</label>
		</div>

		<a href="javascript:fnConfirm();"><img src="<?=$Dir?>images/minishop/btnConfirm.gif" border=0></a>

		<?}?>

		</td>
	</tr>
	<tr><td height=25></td></tr>
	<tr><td height=2 bgcolor=red></td></tr>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
			<input type="button" value="창닫기" onclick="window.close();" class="basic_button" style="padding:0em 1.2em">
		</td>
	</tr>
	<tr><td height=20></td></tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>
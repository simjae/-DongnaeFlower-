<?
include_once("header.php");

$code=$_REQUEST["code"];
if(strlen($code)>0) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='newpage' AND code='".$code."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$isnew=true;
		unset($newobj);
		$newobj->subject=$row->subject;
		$newobj->menu_type=$row->leftmenu;
		$filename=explode("",$row->filename);
		$newobj->member_type=$filename[0];
		$newobj->menu_code=$filename[1];
		$newobj->body=$row->body;
		$newobj->body=str_replace("[DIR]",$Dir,$newobj->body);
		if(strlen($newobj->member_type)>1) {
			$newobj->group_code=$newobj->member_type;
			$newobj->member_type="G";
		}
	}
	mysql_free_result($result);
}
if($isnew!=true) {
	echo "<html><head><title></title></head><body onload=\"alert('해당 페이지가 존재하지 않습니다.');history.go(-1);\"></body></html>";exit;
}

if($newobj->member_type=="Y") {
	if(strlen($_ShopInfo->getMemid())==0) {
		echo("<script>location.replace('../m/login.php?chUrl=".getUrl()."');</script>");
		exit;
	}
} else if($newobj->member_type=="G") {
	if(strlen($_ShopInfo->getMemid())==0 || $newobj->group_code!=$_ShopInfo->getMemgroup()) {
		if(strlen($_ShopInfo->getMemid())==0) {
			echo("<script>location.replace('../m/login.php?chUrl=".getUrl()."');</script>");
			exit;
		} else {
			echo "<html><head><title></title></head><body onload=\"alert('해당 페이지 접근권한이 없습니다.');location.href='/m/main.php'\"></body></html>";exit;
		}
	}
}
?>
<div id="content">
	<div class="h_area2">
		<h2><?=$newobj->subject?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<div id="container">
		<?=$newobj->body?>
	</div>
</div>
<script>
	$('#container img').attr('width','100%');
</script>
<?
include_once("footer.php");
?>
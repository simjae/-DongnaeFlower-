<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	echo "<script>window.close();</script>"; exit;
}

include "header.php";

$idx=$_GET["idx"];

$sql = "SELECT * FROM tblpersonal WHERE id='".$_ShopInfo->getMemid()."' AND idx='".$idx."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_pdata=$row;
} else {
	echo "<html></head><body onload=\"alert('해당 문의내역이 없습니다.');window.close();\"></body></html>";exit;
}
mysql_free_result($result);
?>

<? include $skinPATH."mypage_personal_view.php" ;?>

		
<? include "footer.php"; ?>

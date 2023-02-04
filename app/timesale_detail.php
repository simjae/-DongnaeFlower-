<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/venderlib.php");
	include_once("./inc/function.php");

	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:".$Dir."app/login.php?chUrl=".getUrl());
		exit;
	}

	include "header.php";

?>

<?
	
	include $skinPATH."timesale_detail.php";
	
?>

<? include "footer.php";?>
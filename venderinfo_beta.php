<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	// include_once($Dir."lib/init.debug.php");
	//이게 디버그확인해준는 php
	include_once($Dir."lib/lib.php");
	
	$vidx = isset($_GET['vidx'])?trim($_GET['vidx']):"";
	$pagetype = isset($_GET['pagetype'])?trim($_GET['pagetype']):"";
	$vender = isset($_GET['vender'])?trim($_GET['vender']):"";
	if($pagetype=="pop"){
		include_once($Dir."/app/header_pop.php");
	}
	else{
		include_once($Dir."/app/header.php");
	}
	
	include $skinPATH."venderinfo_beta.php";
if($pagetype!="pop"){
	include_once($Dir."/app/footer.php");
} ?>
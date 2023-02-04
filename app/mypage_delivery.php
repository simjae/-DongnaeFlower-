<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."lib/venderlib.php");
	include_once("./inc/function.php");

	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:".$Dir."app/login.php?chUrl=".getUrl());
		exit;
	}

	include "header.php";

	$currentPage = $_REQUEST["page"];
	if(!$currentPage) $currentPage = 1; 

	$recordPerPage = 3; // 페이지당 게시글 리스트 수 
	$pagePerBlock = 2; // 블록 갯수

	$pagetype="board";
	$cdate = date("YmdH");

	if($_data->coupon_ok=="Y") {
		$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='".$cdate."' OR date_end='') ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$coupon_cnt = $row->cnt;
		mysql_free_result($result);
	} else {
		$coupon_cnt=0;
	}

	$totalRecord = ($row->cnt > 0)? $row->cnt:0;
?>

<?
	if($type=='insert'){ //수정
		include $skinPATH."mypage_delivery_insert.php";
	}else if($type=='modify'){
		include $skinPATH."mypage_delivery_modify.php";
	}else{
		include $skinPATH."mypage_delivery.php";
	}
?>

<? include "footer.php";?>
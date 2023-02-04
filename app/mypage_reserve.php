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

	if($_data->reserve_maxuse<0) {
		echo "<html><head><title></title></head><body onload=\"alert('본 쇼핑몰에서는 적립금 기능을 지원하지 않습니다.');location.href='".$Dir.FrontDir."mypage.php'\"></body></html>";exit;	
	}

	//리스트 세팅
	$setup[page_num] = 10;
	$setup[list_num] = 10;

	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];

	if ($block != "") {
		$nowblock = $block;
		$curpage  = $block * $setup[page_num] + $gotopage;
	} else {
		$nowblock = 0;
	}

	if (($gotopage == "") || ($gotopage == 0)) {
		$gotopage = 1;
	}

	$maxreserve=$_data->reserve_maxuse;

	$reserve=0;
	$sql = "SELECT id,name,reserve FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$id=$row->id;
		$name=$row->name;
		$reserve=$row->reserve;
	} else {
		echo "<html><head><title></title></head><body onload=\"alert('회원정보가 존재하지 않습니다.');location.href='".$_SERVER[PHP_SELF]."?type=logout'\"></body></html>";exit;
	}
	mysql_free_result($result);


	/* 6개월 까지만 조회하기 위해서 */
	$e_year=(int)date("Y");
	$e_month=(int)date("m");
	$e_day=(int)date("d");
	$stime=mktime(0,0,0,($e_month-6),$e_day,$e_year);
	$s_year=(int)date("Y",$stime);
	$s_month=(int)date("m",$stime);
	$s_day=(int)date("d",$stime);
	$s_curtime=mktime(0,0,0,$s_month,$s_day,$s_year);
	$s_curdate=date("YmdHis",$s_curtime);
	$e_curtime=mktime(24,59,59,$e_month,$e_day,$e_year);
	$e_curdate=date("YmdHis",$e_curtime);
?>

<!--<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>-->

<SCRIPT LANGUAGE="JavaScript">
	<!--
	function GoPage(block,gotopage) {
		document.form1.block.value=block;
		document.form1.gotopage.value=gotopage;
		document.form1.submit();
	}
	function OrderDetailPop(ordercode) {
		document.form2.ordercode.value=ordercode;
		window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
		document.form2.submit();
	}
	//-->
</SCRIPT>

<? include $skinPATH."mypage_reserve.php"; ?>

<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
</form>

<form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
	<input type=hidden name=ordercode>
</form>

<? include "footer.php"; ?>
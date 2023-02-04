<?include_once('./productdetail.php');?>

<div class="h_area2" style="position: fixed;top:constant(safe-area-inset-top) + 60px; top:calc(env(safe-area-inset-top) + 60px);width: 100% ;z-index: 50; margin-top: 5px;padding-top: 10px;">
	<h2>마감 할인</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
	<a class="btn_prev" style="margin-top:10px;" rel="external"><span>이전</span></a>
</div>
<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
?>
<?include_once('./productdetail_timesale_top.php');?>
<?
	include_once("footer.php");
?>

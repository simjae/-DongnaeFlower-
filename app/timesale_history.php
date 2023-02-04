<?
// 상품가격 0원 오류 관련 수정함 2016-04-07 Seul
include "header.php";
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");

include_once($Dir."lib/check_login.php");
?>

<div class="h_area2">
	<h2>바로 구매하기 주문내역</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
	<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
</div>
<form name="form1" action="" method=post>

<? include $skinPATH."timesale_history.php"; ?>
</form>



<? include "footer.php"; ?>
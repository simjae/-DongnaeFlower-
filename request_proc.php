<?
// 상품가격 0원 오류 관련 수정함 2016-04-07 Seul
include "header.php";
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");

$URL = $Dir."app/proposalList.php";

echo "<html><head><title></title></head><body><script>setTimeout(function(){location.replace('".$URL."');},5000)</script></body></html>";


?>

<form name="form1" method=post>

<? include $skinPATH."request_proc.php"; ?>
</form>
</body>
</html>

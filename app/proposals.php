<?
// 상품가격 0원 오류 관련 수정함 2016-04-07 Seul
include "header.php";
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;

//회원전용일 경우 로긴페이지로...
if($_data->member_buygrant=="Y" && strlen($_ShopInfo->getMemid())==0) {
    //Header("Location:./login.php?chUrl=".getUrl());
    echo "<script>location.href='./login.php?chUrl=".getUrl()."'</script>";
    exit;
    
}
$aoidx = isset($_POST["aoidx"]) ? $_POST["aoidx"] : (isset($_GET['aoidx']) ? $_GET['aoidx'] : "");
if(strlen($_ShopInfo->getMemid()) > 0){
    //체크일 갱신
    $sql ="UPDATE auction_order SET chkDate =now() WHERE userid='".$_ShopInfo->getMemid()."' AND aoidx='".$aoidx."'";
    mysql_query($sql,get_db_conn());
    
}



?>

<form name="form1" action="" method=post>

<? include $skinPATH."proposals.php"; ?>
</form>
<div id="image_popup" style="display: none; position: fixed; padding: 15% 3%; box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 999; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="ImageClose()">
		<div style="position:absolute;top:3%;right:3%;color:#fff;font-size:4em;font-weight:500;">×</div>
	</div>
	<div style="position: relative; width: 100%; height: fit-content; margin: 0px;">
		<img id="image_content" src="" style="position: absolute; left: 0px; top: 0px; width: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden;">
	</div>
</div>
<script>
function ImageShow(src){
	$("#image_popup").show();
	$("#image_content").attr("src",src);
}
function ImageClose(){
	$("#image_popup").hide();
	$("#image_content").attr("src","");
}
</script>

<? include "footer.php"; ?>
<?
// 상품가격 0원 오류 관련 수정함 2016-04-07 Seul
include "header.php";
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");
?>


<form name="form1" action="request_proc.php" method=post>
	<input type="hidden" name="type" />
<? include $skinPATH."talk_request.php"; ?>
</form>


<div id="delivery_popup" style="display: none; position: fixed; padding: 15% 3%; box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 999; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="ReceiverClose()">
		<div style="position:absolute;top:6%;right:3%;padding-top:env(safe-area-inset-top);padding-top:constant(safe-area-inset-top);color:#fff;font-size:4em;font-weight:500;">×</div>
	</div>
	<div style="position: relative; width: 100%; height: 100%; background-color: rgb(255, 255, 255); z-index: 0; overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;">
		<iframe frameborder="0" id="delivery_content" src="about:blank" style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
	</div>
</div>
<script>
function CheckForm() {
	if($("input[name=receiveDate]").val().length==0) {
		alert("배송날짜 미지정");
		return;
	}
	if($("input[name=receiveTime]").val().length==0) {
		alert("배송시간 미지정");
		return;
	}
	if($("input[name=addr1]").val().length==0) {
		alert("주소 미지정");
		return;
	}
	if($("input[name=purpose]").val().length==0) {
		alert("용도 미지정");
		return;
	}
	if($("input[name=productType]").val().length==0) {
		alert("종류 미지정");
		return;
	}
	if($("input[name=priceRange]").val().length==0) {
		alert("가격대 미지정");
		return;
	}
	if($("input[name=style]").val().length==0) {
		alert("스타일 미지정");
		return;
	}

	document.form1.type.value="insert";
	document.form1.submit();
}

//주소록 팝업창
function ReceiverShow(){
	$("#delivery_popup").show();
	$("#delivery_content").attr("src","/app/mydeliveryModal.php");
}
function ReceiverClose(){
	$("#delivery_popup").hide();
	$("#delivery_content").attr("src","about:blank");
}
</script>
<? include "footer.php"; ?>
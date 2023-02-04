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

?>

<form name="form1" action="request_proc.php" method=post>
<?
	if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {
?>
	<input type="hidden" name="shopurl" value="<?=getenv("HTTP_HOST")?>" />
<?
	}
?>

	<input type="hidden" name="type" />
<?
	include $skinPATH."form_request.php"; 
?>
</form>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {


	if($("input[name=receiveDate]").val().length==0) {
		alert("배송날짜를 입력하세요.");
		$("input[name=receiveDate]").focus();
		return;
	}
	if($("select[name=receiveTime]").val().length==0) {
		alert("배송시간을 입력하세요.");
		$("select[name=receiveTime]").focus();
		return;
	}
	if($(".addr_item").length==0) {
		alert("주소를 하나 이상 입력하세요.");
		return;
	}
	if($("select[name=purpose]").val().length==0) {
		alert("용도를 입력하세요.");
		$("select[name=purpose]").focus();
		return;
	}
	if($("select[name=productType]").val().length==0) {
		alert("종류를 입력하세요.");
		$("select[name=productType]").focus();
		return;
	}
	if($("select[name=priceRange]").val().length==0) {
		alert("가격대를 입력하세요.");
		$("select[name=priceRange]").focus();
		return;
	}
	if($("select[name=style]").val().length==0) {
		alert("스타일을 입력하세요.");
		$("select[name=style]").focus();
		return;
	}

	document.form1.type.value="insert";
	document.form1.submit();
}
//-->
</SCRIPT>


<!-- iOS에서는 position:fixed 버그가 있음, 적용하는 사이트에 맞게 position:absolute 등을 이용하여 top,left값 조정 필요 -->
<div id="layer" style="display:none;position:fixed;padding:15% 3%;box-sizing:border-box;background:rgba(0,0,0,0.7);z-index:999;-webkit-overflow-scrolling:touch;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="closeDaumPostcode()">
		<div style="position:absolute;top:3%;right:3%;color:#fff;font-size:4em;font-weight:500;">&times;</div>
	</div>
</div>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
	//우편번호 찾기 화면을 넣을 element
	var element_layer = document.getElementById('layer');

	function closeDaumPostcode() {
		//iframe을 넣은 element를 안보이게 한다.
		element_layer.style.display = 'none';
	}

	function addr_search_for_daumapi(obj) {
		new daum.Postcode({
			oncomplete: function(data) {
				// 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

				// 각 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var addr = ''; // 주소 변수
				var extraAddr = ''; // 참고항목 변수

				//사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
				if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
					addr = data.roadAddress;
				} else { // 사용자가 지번 주소를 선택했을 경우(J)
					addr = data.jibunAddress;
				}

				// 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
				if(data.userSelectedType === 'R'){
					// 법정동명이 있을 경우 추가한다. (법정리는 제외)
					// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
					if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
						extraAddr += data.bname;
					}
					// 건물명이 있고, 공동주택일 경우 추가한다.
					if(data.buildingName !== '' && data.apartment === 'Y'){
						extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
					// 조합된 참고항목을 해당 필드에 넣는다.
					$(obj).parent().parent().find("input[name=addr2]").val(extraAddr);

				} else {
					$(obj).parent().parent().find("input[name=addr2]").val('');
				}

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				$(obj).parent().parent().find("input[name=zip]").val(data.zonecode);
				$(obj).parent().parent().find("input[name=addr1]").val(addr);
				// 커서를 상세주소 필드로 이동한다.
				if(typeof($(obj).parent().parent().find("input[name=addr2]")) != "undefined"){
					$(obj).parent().parent().find("input[name=addr2]").focus();
				}
				// iframe을 넣은 element를 안보이게 한다.
				// (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
				element_layer.style.display = 'none';
			},
			width : '100%',
			height : '100%',
			maxSuggestItems : 5
		}).embed(element_layer);

		// iframe을 넣은 element를 보이게 한다.
		element_layer.style.display = 'block';

		// iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
		initLayerPosition();
	}

	// 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
	// resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
	// 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
	function initLayerPosition(){
		var width = 100; //우편번호서비스가 들어갈 element의 width
		var height = 100; //우편번호서비스가 들어갈 element의 height
		var borderWidth = 0; //샘플에서 사용하는 border의 두께

		// 위에서 선언한 값들을 실제 element에 넣는다.
		element_layer.style.width = width + '%';
		element_layer.style.height = height + '%';
		element_layer.style.border = borderWidth + 'px solid #ddd';
		// 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
		//element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
		//element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
		element_layer.style.left = '0%';
		element_layer.style.top = '0%';
	}
	function addAddr(name,tel1,tel2,post,addr1,addr2){
		var html = "<div class=\"addr_item\">";
		html += "		<div class=\"closeBtn\" onclick=\"addrRemove(this)\">×</div>";
		html += "		<div class=\"addrStr_group\">";
		html += "			<input type=\"hidden\" name=\"zip[]\" value=\"" + post + "\"/>";
		html += "			<input type=\"hidden\" name=\"addr1[]\" value=\"" + addr1 + "\"  />";
		html += "			<input type=\"hidden\" name=\"addr2[]\" value=\"" + addr2 + "\"  />";
		html += "			<input type=\"hidden\" name=\"tel[]\" value=\"" + tel1 + "\"  />";
		html += "			<input type=\"hidden\" name=\"rcvName[]\" value=\"" + name + "\"  />";
		html += "			<p class=\"addrStr\">";
		html += addr1;
		html += "			</p>";
		html += "			<p class=\"addrStr\">";
		html += addr2;
		html += "			</p>";
		html += "			<p class=\"nameTelStr\">";
		html += name + "  (" + tel1 + ")";
		html += "			</p>";
		html += "		</div>";
		html += "		<div class=\"order_input_group\">";
		html += "			<div class=\"order_receiveType\">";
		html += "				<input type=\"hidden\" class=\"receiveType\" name=\"receiveType[]\" value=\"0\"  />";
		html += "				<div class=\"typeBtn select\" onclick=\"receiveTypeSel(this,'0')\">배송</div>";
		html += "				<div class=\"typeBtn\" onclick=\"receiveTypeSel(this,'1')\">픽업</div>";
		html += "			</div>";
		html += "			<div class=\"order_quantity\">";
		html += "				수량 : ";
		html += "				<input type=\"button\" value=\"-\" class=\"qty_button\" onClick=\"quantityControlForm('minus',this);\" />";
		html += "				<input class=\"qty_input\" type=\"number\" name=\"prodNum[]\" size=\"2\" value=\"1\" />";
		html += "				<input type=\"button\" value=\"+\" class=\"qty_button\" onClick=\"quantityControlForm('plus',this);\" />";
		html += "			</div>";
		html += "		</div>";
		html += "		<div style=\"clear:both;\"></div>";
		html += "	</div>";
		$("#addr_group").append(html);
	}
	function resetAddr(){
		$(".addr_item").remove();
	}
	
	
function quantityControlForm(mode, obj){
	if(mode != null || mode != 'undifined'){
		var quantituObj = $(obj).parent().find(".qty_input");
		if(mode == 'plus'){
			quantituObj.val(parseInt(quantituObj.val()) + 1);
		}

		if(mode == 'minus'){
			if(quantituObj.val() > 1){
			quantituObj.val(parseInt(quantituObj.val()) - 1);
			}else{
				alert("최소 구매가능한 수량은 1개 입니다.");
			}
		}
	}
}


function receiveTypeSel(obj,val){
	var btnObjs = $(obj).parent().find(".typeBtn");
	var inputObj = $(obj).parent().find(".receiveType").val(val);
	btnObjs.each(function(obj) {
		$(this).removeClass("select")
	})
	$(obj).addClass("select")
}

function addrRemove(obj){
	$(obj).parent().remove();
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
<div id="delivery_popup" style="display: none; position: fixed; padding: 15% 3%; box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 999; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="ReceiverClose()">
		<div style="position:absolute;top:3%;right:3%;color:#fff;font-size:4em;font-weight:500;">×</div>
	</div>
	<div style="position: relative; width: 100%; height: 100%; background-color: rgb(255, 255, 255); z-index: 0; overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;">
		<iframe frameborder="0" id="delivery_content" src="about:blank" style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
	</div>
</div>
<? include "footer.php"; ?>
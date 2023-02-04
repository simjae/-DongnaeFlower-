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
<?
$mingiftprice = 0;
if(false !== $gres = mysql_query("select min(gift_startprice) from tblgiftinfo",get_db_conn())){
	if(mysql_num_rows($gres)) $mingiftprice = mysql_result($gres,0,0);
}
?>

<form name="form1" action="ordersend.php" method=post>
	<input type="hidden" name="shopurl" value="<?=getenv("HTTP_HOST")?>" />
	<input type="hidden" name="id" value="<?=$_ShopInfo->getMemid()?>" />

	<div id="content">

		<div class="select_order_wrap">
			
			<h1 style="margin-top:15%;font-weight:normal;text-align:center">주문방법 <span style="font-weight: 200">을 선택해 주세요</span></h1>
			<div id="order_type_select" class="order_type_select">
				<div class="select">
					<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
						<tr>
							<td style="width:15%">
								<input type="radio" name="order_type" value="2" checked>
							</td>
							<td style="width:85%">
								1:1 채팅으로 주문 할게요.
							</td>
						</tr>
					</table>
				</div>
				<div>
					<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
						<tr>
							<td style="width:15%">
								<input type="radio" name="order_type" value="1">
							</td>
							<td style="width:85%">
								주문서로 주문 할게요.
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="basic_btn_area" style="background:#f2f2f2">
				<table id="talkInfo" cellpadding="0" cellspacing="0" border="0" style="width:100%;height:35vh;">
					<tr>
						<td style="width:50%;padding:20px 20px 0 20px;vertical-align:top;">
							<div style="box-shadow: 2px 2px 2px 2px #aaa;background: url(../app/skin/basic/img/snap01.png) no-repeat;background-size: 100% auto;background-position:top center;height:100%;">
							</div>
						</td>
						<td style="width:50%;padding:10px;vertical-align:top;">
							<div style="border-bottom:1px solid #199151;text-align:center;line-height:30px;">
								1:1 채팅
							</div>
							<div style="text-align:left;margin-top:10px;line-height:18px;font-size:0.9em;font-weight:100">
								대화를 통해
								<br>주문을 진행합니다.
								<br>
								<br>
								마치 플로리스트와
								<br>대화를 하듯 주문서를
								<br>작성해보시면 어떨까요?
							</div>
						</td>
					</tr>
				</table>
				<table id="formInfo" cellpadding="0" cellspacing="0" border="0" style="width:100%;height:35vh;display:none;">
					<tr>
						<td style="width:50%;padding:20px 20px 0 20px;vertical-align:top;">
							<div style="box-shadow: 2px 2px 2px 2px #aaa;background: url(../app/skin/basic/img/snap02.png) no-repeat;background-size: 100% auto;background-position:top center;height:100%;">
							</div>
						</td>
						<td style="width:50%;padding:10px;vertical-align:top;">
							<div style="border-bottom:1px solid #199151;text-align:center;line-height:30px;">
								주문서
							</div>
							<div style="text-align:left;margin-top:10px;line-height:18px;font-size:0.9em;font-weight:100">
								주문을 한눈에 확인하며
								<br>주문할 수 있어요
								<br>
								<br>
								주문시 필요한 항목을 작성하시면
								<br>동네꽃집에서 꼼꼼히 확인하고
								<br>플로리스트를 찾아드릴께요.
							</div>
						</td>
					</tr>
				</table>
				<a href="javascript:setOrderType();"><span class="set_bt" style="margin: 0;">설정하기</span></a>
			</div>
		</div>
	</div>
</form>
<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function() {
		$("#order_type_select div").click(function(){
			var selObj = $(this);
			$(selObj).parent().find("div").each(function(obj) {
				$(this).removeClass("select")
			})
			$(selObj).addClass("select")
			$(selObj).find("input:radio").prop("checked", true);
			if($("input[name=order_type]:checked").val() == "1"){
				$("#talkInfo").fadeOut(200,function(){$("#formInfo").fadeIn(200)});
			}
			else{
				$("#formInfo").fadeOut(200,function(){$("#talkInfo").fadeIn(200)});
			}
		});

	});
	
	function setOrderType(){
	var formData = $("form[name=form1]").serialize() ;
	$.ajax({
		type : 'post',
		url : '/api/order_type_update.php',
		data : formData,
		dataType : 'json',
		error: function(xhr, status, error){
			alert("통신중에 오류가 발생했습니다.");
		},
		success : function(json){
			if(json["result"] == "Y"){
				if(json["order_type"] == "1"){
					location.href="/app/form_request.php";
				}
				else{
					location.href="/app/talk_request.php";
				}
			}
			else if(json["result"] == "E"){
				alert("처리중에 오류가 발생했습니다");
				location.href="/app/login.php";
			}
		}
	});
}
</script>


</body>
</html>
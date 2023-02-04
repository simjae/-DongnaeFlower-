	<div style="height:calc(constant(safe-area-inset-bottom) + 80px);height:calc(env(safe-area-inset-bottom) + 80px);" class="footer_gap">
	</div>
	<div id="talkRequest" style="position:fixed;z-index:1100;overflow:hidden;width:100%;top:0px;display:none">
		<? include $skinPATH."talk_request_test.php"; ?>
	</div>
	<?
	$iconOnArr = array();
	switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)){
		case "main.php":
			$iconOnArr[0] = "_on"; break;
		case "timesale_product.php":
			$iconOnArr[2] = "_on"; break;
		case "mypage.php":
			$iconOnArr[3] = "_on"; break;
	}
	?>
	<div id="bottom">
		<div class="bot_copy wrapper" style="position:fixed;bottom:0px;height:calc(constant(safe-area-inset-bottom) + 80px);height:calc(env(safe-area-inset-bottom) + 80px);z-index:800;font-size:10px;padding-top: 20px;padding-bottom:20px;">
			<ul>
				<li style="width:20%;text-align:center;padding:0px;" onclick="location.href='/app/main.php'">
					<img src="/app/skin/basic/svg/icon_01<?=$iconOnArr[0]?>.svg"  style="height: 24px; cursor:pointer;">
					<br>홈
				</li>
				<li style="width:20%;text-align:center;padding:0px;" onclick="talkRequestOpen()">
					<img src="/app/skin/basic/svg/icon_02.svg"  style="height: 24px; cursor:pointer;">
					<br>스페셜 오더
				</li>
				<li style="width:20%;text-align:center;padding:0px;" onclick="callTimesale('list');">
					<img src="/app/skin/basic/svg/icon_06<?=$iconOnArr[2]?>.svg"  style="height: 24px; cursor:pointer;">
					<br>마감 할인
				</li>
				
				<li style="width:20%;text-align:center;padding:0px;" onclick="location.href='/app/mypage.php'">
					<img src="/app/skin/basic/svg/icon_04<?=$iconOnArr[3]?>.svg"  style="height: 24px; cursor:pointer;">
					<br>마이페이지
				</li>
				<li style="width:20%;text-align:center;padding:0px;" onclick="location.href='#'" id="vdsearch">
					<img src="/app/skin/basic/svg/icon_05.svg" style="height: 24px; cursor:pointer;">
					<br>꽃집 검색
				</li>
			</ul>
		</div>
		
		<div id="vdmosearch" style="display:none;position:fixed;top:0px;left:0px;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:1000;">
			<div style="position:absolute;top:5%;left:0px;width:100%;text-align:right;right:1%;"><a href="#" class="close_modal" style="color:#fff;font-size:2.5em;font-weight:lighter;padding-right:5%;">×</a></div>
			<div style="display:table;width:100%;height:100%;">
				<div style="display:table-cell;font-size:0px;text-align:center;vertical-align:middle;">
					<form name="tvdSearchForm" action="vendersearch.php" method="get">
					<input type="hidden" name="mode" value="search" />
					<input type="hidden" name="terms" value="brand_name" />
					<input type="text" name="vdsearch" value="" placeholder="꽃집을 검색하세요." style="font-size:0.9rem;height:40px;width:80%;padding:0px 5px;border:0px solid rgba(255,255,255,0.5);box-sizing:border-box;color:#b0afaf;border-bottom:1px solid #b0afaf;" />
					<input type="button" value="" id="btn_vdsearch_submit" style="height:40px;width:20px;margin-top:0px;padding:0px 10px;background:url('/app/skin/basic/img/search2.png') no-repeat;background-size:100%;background-position:center;border:none;vertical-align:top;border-bottom:1px solid #b0afaf;" />
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="move_scroll">
		<a href="#gotop" rel="external"><div class="top"><img src="/m/skin/basic/img/icon_arrow_bottom01.png"></div></a>
		<a href="#bottom" rel="external"><div class="bottom"><img src="/m/skin/basic/img/icon_arrow_bottom01.png"></div></a>
	</div>

	<!-- jquery ui 모달창 BG 스타일 -->
	<style>
		.ui-widget-overlay {background:#aaa;opacity:.5;filter:Alpha(Opacity=50);z-index:9999;}
		.ui-widget-shadow {margin:-8px 0 0 -8px;padding:8px;background:#aaa;opacity: .5;filter:Alpha(Opacity=50);border-radius:8px;}
		.ui-dialog .ui-dialog-content{padding:0.5em 0em;height:100% !important;}
	</style>
	<!-- jquery ui 모달창 BG 스타일 -->

	<!-- jquery ui 모달 팝업 -->
	<div id="wrap_layer_popup" style="display:none;">
		<div id="show_contents" style="height:100%;"></div>
	</div>

	<script type="text/javascript">
		<!--
		$(function(){
			// selectbox design
			$('.basic_select').jqTransform();
		});
		function showVdsearch(){
			$('#vdmosearch').fadeIn(200);
		}
		$(document).ready(function() {
			//꽃집검색 - 하단메뉴
			$('#vdsearch').click(function(){
				showVdsearch();
			});
			$('.close_modal').click(function(){
				$('#vdmosearch').fadeOut(200);
			});
			$("#btn_vdsearch_submit").click(function(){
				var _form = document.tvdSearchForm;

				if($("#vdmosearch input[name=vdsearch]").val() == "" || $("#vdmosearch input[name=vdsearch]").val() == ""){
					alert("검색어를 입력하세요.");
					$("#vdmosearch input[name=vdsearch]").focus();
					return false;
				}else{
//					$("#vdmosearch input[name=vdsearch]").hide();
					_form.submit();
					return;
				}
			});
			
			window.nativeObj.nativeType.get();
			window.nativeObj.coordinate.getFromNative();
		});
		function callTimesale(listType){
			if(listType == "undefinede"){
				listType = "";
			}
			var form = $('<form></form>');
			form.attr('action', '/app/timesale_product.php');
			form.attr('method', 'post');
			form.appendTo('body');
			form.append($('<input type="hidden" value="' + window.nativeObj.coordinate.longitude + '" name=pointx>'));
			form.append($('<input type="hidden" value="' + window.nativeObj.coordinate.latitude + '" name=pointy>'));
			form.append($('<input type="hidden" value="' + listType + '" name=listType>'));
			form.submit();
		}
		function callTimesale2(listType){
			if(listType == "undefinede"){
				listType = "";
			}
			var form = $('<form></form>');
			form.attr('action', '/app/timesale_product_beta.php');
			form.attr('method', 'post');
			form.appendTo('body');
			form.append($('<input type="hidden" value="' + window.nativeObj.coordinate.longitude + '" name=pointx>'));
			form.append($('<input type="hidden" value="' + window.nativeObj.coordinate.latitude + '" name=pointy>'));
			form.append($('<input type="hidden" value="' + listType + '" name=listType>'));
			form.submit();
		}
		//-->
		function talkRequestClose(){
			$("#talkRequest").fadeOut(200);
		}
		function talkRequestOpen(){
			var targetVender = $("input[name=targetVender]").val();
			$("#titleWrap h2").html("스페셜 오더");
			if(targetVender != ""){
				//단골주문 진행중이면 초기화
				resetTalk("","");
			}
			$("#talkRequest").fadeIn(200);
		}
		function targetVenderRequestOpen(vidx,brand_name){
			var targetVender = $("input[name=targetVender]").val();
			$("#titleWrap h2").html("단골 주문");
			if(targetVender == ""){
				//스페셜오더 진행중이면 초기화
				resetTalk(vidx,brand_name);
			}
			$("#talkRequest").fadeIn(200);
		}
		
		
		function iframePopupClose(){
			$("#iframe_layer_popup").hide();
			$("#iframe_layer_content").attr("src","about:blank");
		}

		function iframePopupOpen(url){
			$("#iframe_layer_popup").show();
			$("#iframe_layer_content").attr("src",url);

		}
		
		var nativeObj = {
			coordinate:{
				getFromNative: function(val){
					if(nativeObj.nativeType.val == "Android"){
						this.latitude = BRIDGE.get_latitude();
						this.longitude = BRIDGE.get_longitude();
					}else if(nativeObj.nativeType.val == "iOS"){
//						window.location="jscall://appLoginProc";
						window.webkit.messageHandlers.getCoordinate.postMessage("");
					}else{
						navigator.geolocation.getCurrentPosition(function(pos) {
							window.nativeObj.coordinate.latitude = pos.coords.latitude;
							window.nativeObj.coordinate.longitude = pos.coords.longitude;
						}, function(error) {
						  console.error(error);
						}, {
						  enableHighAccuracy: false,
						  maximumAge: 0,
						  timeout: Infinity
						});
					}
				},
				latitude:0,
				longitude:0
			},
			userToken: {
				getFromNative: function(){

				},
				val:""
			},
			userState: false,
			nativeType: {
				get: function(){
					var agent = navigator.userAgent;
					is_android = agent.indexOf('dongne-flower user android') > -1;
					is_ios = agent.indexOf('dongne-flower user ios') > -1;
					if(is_android)
						this.val = "Android";
					else if(is_ios)
						this.val = "iOS";
					else
						this.val = "Web";
				},
				val: "Web"
			}
		};
		


	</script>

	
	<div id="iframe_layer_popup" style="display: none; position: fixed;padding-top:calc(60px + env(safe-area-inset-top));padding-top:calc(60px + constant(safe-area-inset-top));  box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 910; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
		<div style="position:absolute;top: 76px;right:3%;color:#929292;font-size:2.5em;z-index: 900;"  onclick="iframePopupClose()">×</div>
		<div style="position: absolute; left: 0px; top: 50px; width: 100%; height: calc(100% - 50px); overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;background:#ffffff">
			<iframe frameborder="0" id="iframe_layer_content" name="iframe_layer_content" src="about:blank" style="width: 100%; height: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
		</div>
	</div>
</body>
</html>
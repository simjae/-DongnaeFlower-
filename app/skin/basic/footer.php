<style>
.imgSize{
width: 15px;
}
.flex{
	display: flex;
}

.statusWrap{
    width: calc(100vw - 55px);
	height: calc(50vh - 40px);
}
.statusGroup{
	display: flex;
	padding-left:15px;
	padding-top:10px;
}
.statusTitle{
	margin:20px 0;
	font-size: 1.7em;
    color: #282828;
    font-weight: 900;
}
.statusSubTitle{
	margin-bottom: 10px;
	padding-left:15px;
}
.statusSubTitle > p{
	color:#464646;
	font-size: 1.2em;
	font-weight: 500;
}
.logoName{
	border-radius: 20px;
	background-color: #e61e6e;
}
.statusLabel_selected{
	background-color: #e61e6e;	
	color: #ffffff;
	font-weight: 500;
	font-size: 1.0em;
	border-radius: 20px;
	padding: 3px 10px;
	margin-top: 5px;
}
.statusLabelY_selected{
	background-color: #464646;	
	color: #ffffff;
	font-weight: 500;
	font-size: 1.0em;
	border-radius: 20px;
	padding: 3px 10px;
	margin-top: 5px;
}
.statusLabel{
	background-color: #e6e6e6;	
	color: #646464;
	font-weight: 300;
	font-size: 1.0em;
	border-radius: 20px;
	padding: 3px 10px;
	margin-top: 5px;
}
.myOrderListWrap{
	margin-top: 10px;
}
.myOrderListTitle{
	background-color: #e6e6e6;
    padding: 10px;
    font-size: 1.3em;
    font-weight: 900;
	color: #282828;
	border-top-left-radius: 20px;
    border-top-right-radius: 20px;
}	
.myOrderListSubTitle{
	border-bottom-left-radius: 20px;
    border-bottom-right-radius: 20px;
}
.myOrderWrap{
	overflow: hidden;
	margin:auto;
	border-radius:14px;
	isolation: isolate;
	margin-right: var(--grid-gap-horizontal);
}

.reviewBtn{
	float:left;
	margin-left:80px;
	width:90px;
	background-color:#e61e6e;
	color:#ffffff;
	font-weight:500;
	font-size:1.0rem;
	border-radius:20px;
	padding:3px;
}

</style>

	
	
	<div style="height:calc(constant(safe-area-inset-bottom) + 80px);height:calc(env(safe-area-inset-bottom) + 80px);" class="footer_gap">
	</div>
	<div id="talkRequest" style="position:fixed;z-index:1100;overflow:hidden;width:100%;top:0px;display:none">
		<? include $skinPATH."talk_request.php"; ?>
	</div>
	<?
	$member_id = $_ShopInfo->getMemid();
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
		<div class="bot_copy wrapper" style="position:fixed;bottom:0px;height:auto;z-index:800;font-size:10px;padding-top: 0px;padding-bottom:20px;border-top-left-radius: 20px;border-top-right-radius: 20px;box-shadow: 0px 4px 5px 1px #464646;">
			<div class="myOrderBtn flex" style="border-bottom: solid 1px #9e9e9e36;justify-content: center;font-size: 1.5em;color: #282828;font-weight: 900;padding-bottom:15px;display:none">
				<font style="float:left;color:#e61e6e;">내 주문정보</font>
				<img style="margin-left: 20px;float:left;" class="orderArrow imgSize" src="/app_beta/skin/basic/svg/productFlower_arrow_under.svg" alt="">
			</div>
			
			<div class="orderStatus">
				<div class="myOrderWrap" style="height:100%;">
					<!-- swiper-slider append target -->
					<div class="swiper-wrapper orderStatusWrapper">
					</div>
					<div class="swiper-orderStatus-page" style="position:absolute;bottom:80px;">
					</div>
				</div>
			</div>
			<input type="hidden" id="slideIndex">
			
			<div class="ui segment order_list_loader" style="-webkit-box-shadow:none;border:none;height:287px;display:none;">
				<p></p>
				<div class="ui active inverted dimmer" style="padding:0px;">
					<div class="ui text loader">
						<div style="margin-top:10px;">주문 정보를 불러오는 중입니다</div>
						<div style="margin-top:5px;">조금만 기다려 주세요 :)</div>
					</div>
				</div>
			</div>
			
			<ul style="padding-top:10px;">
				<li style="width:20%;text-align:center;padding:0px;" onclick="location.href='/app/main.php'">
					<img src="/app/skin/basic/svg/icon_01<?=$iconOnArr[0]?>.svg"  style="height: 24px; cursor:pointer;">
					<br>홈
				</li>
				<li style="width:20%;text-align:center;padding:0px;" onclick="talkRequestOpen()">
					<img src="/app/skin/basic/svg/icon_02.svg"  style="height: 24px; cursor:pointer;">
					<br>꽃집 제안받기
				</li>
				<li style="width:20%;text-align:center;padding:0px;" onclick="callTimesale('list');">
					<img src="/app/skin/basic/svg/icon_06<?=$iconOnArr[2]?>.svg"  style="height: 24px; cursor:pointer;">
					<br>바로 구매
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
					<input type="hidden" name="terms" value="all" />
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
		var orderSwipeSettings = {
			loop: false,
			pagination: {
				el: '.swiper-orderStatus-page',
				clickable: true,
				type : 'bullets'
			}
		}
		
		var orderWrapSwiper = null;
		
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
					// $("#vdmosearch input[name=vdsearch]").hide();
					_form.submit();
					return;
				}
			});
			window.nativeObj.nativeType.get();
			window.nativeObj.coordinate.getFromNative();
			
			myOrderBtnShowEvent();
			
			var member_id = "<?=$member_id?>";
			var cookieCheck = getCookie("orderStatus");
			if (cookieCheck == "Y"){
				callOrderStatusEvent(member_id,false,null);
			} else {
				callOrderStatusEvent(member_id,true,null);
			}
			
			setRefreshBtnClickEvent();
		});
		
		function getCookie(name) {
			var cookie = document.cookie;
			if (document.cookie != "") {
				var cookieArray = cookie.split("; ");
				for (var i=0; i<cookieArray.length; i++) {
					var cookieName = cookieArray[i].split("=");
					if (cookieName[0] == "orderStatus") {
						return cookieName[1];
					}
				}
			 }
		}
		
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
		//-->
		function talkRequestClose(){
			$("#talkRequest").fadeOut(200);
		}
		function talkRequestOpen(){
			var targetVender = $("input[name=targetVender]").val();
			$("#titleWrap h2").html("꽃집 제안");
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

		function getMobileOS() {
			var mobile = (/iphone|ipad|ipod|android/i.test(navigator.userAgent.toLowerCase()));
			var mobileOS = "";

			if (mobile) {
				var userAgent = navigator.userAgent.toLowerCase();
				if ((userAgent.search("iphone") > -1) || (userAgent.search("ipod") > -1)|| (userAgent.search("ipad") > -1)){
					mobileOS = "ios";
				} else {
					mobileOS = "android";
				}
			}
			return mobileOS;
		}

		
		function myOrderBtnShowEvent(){
			$('.myOrderBtn').click(function(){
				$('.orderStatus').toggle();
				if ($('.orderStatus').css('display') == "none") {
					setCookie("orderStatus", "N");
					$('.orderArrow').attr('src','/app_beta/skin/basic/svg/productFlower_arrow_up.svg');
				} else {
					setCookie("orderStatus", "Y");
					$('.orderArrow').attr('src','/app_beta/skin/basic/svg/productFlower_arrow_under.svg');
				}
			});
		}
		
		function callOrderStatusEvent(member_id,toggleFlg,slideIndex) {
			$.ajax({
				url: "/api/order_status.php",
				type: "get",
				async: false,
				data: "member_id=" + member_id,
				dataType: "json",
				success : function(data) {
								if (data.length > 0) {
									$('.myOrderBtn').show();
									$('.bot_copy').css('padding-top','15px');
									var mobileOS = getMobileOS();
									var cookieCheck = getCookie("orderStatus");
									
									if (cookieCheck == "Y") {
										$('.orderArrow').attr('src','/app_beta/skin/basic/svg/productFlower_arrow_under.svg');
									} else {
										$('.orderArrow').attr('src','/app_beta/skin/basic/svg/productFlower_arrow_up.svg');
									}
									$('.bot_copy').css('border-radius','25px 25px 0px 0px');
									$('.orderStatus').css('border-bottom','solid 1px #9e9e9e36');
									orderStatusSplitEvent(data,toggleFlg);
									orderWrapSwiper = new Swiper('.myOrderWrap', orderSwipeSettings);
									if (slideIndex != null) {
										orderWrapSwiper.slideTo(slideIndex,0,true);
									}
								} else {
									$('.orderStatus').hide();
									$('.bot_copy').css('padding-top','0px');
								}
							}
			});
		}
		
		function orderStatusSplitEvent(data,toggleFlg){
			var strDiv = "";
			for(var i = 0; i < data.length; i++){
			strDiv += '<div class="swiper-slide" style="width:100%;">';
			strDiv += '	<div class="statusGroup">';
			strDiv += '		<div class="statusTitle" style="width:100%;text-align:left;">' + data[i].proc_title + '</div>';
			strDiv += '		<img class="imgSize refreshBtn" src="/app_beta/skin/basic/svg/spinArrow.svg" alt="" style="margin-right:30px;">';
			strDiv += '	</div>';
			strDiv += '	<div class="statusSubTitle" style="float:left;">';
			strDiv += '		<p>' +data[i].proc_ment+ '</p>';
			strDiv += '	</div>';
			var marginTop = "";
			if (data[i].deli_gbn == "Y") {
			strDiv += '	<div class="reviewBtn" status="' + data[i].reviewStatus + '" productcode="' + data[i].productcode + '" onclick="callReview(this);">리뷰 남기기</div>';	
			marginTop = "margin-top:20px;";
			}
			var locationLink = "";
			if (data[i].order_type == "꽃집 제안") {
				locationLink = 'location.href="proposalList.php"';
			} else {
				locationLink = 'location.href="timesale_history.php"';
			}
			strDiv += "	<div class='statusIconTable' style='padding:10px 4% 10px 4%;' onClick='" + locationLink + "'>";
			strDiv += '		<TABLE>';
			strDiv += '			<TR>';
			strDiv += '				<TD style="width:80px;">';
			strDiv += '					<img class="iconImg" style="width:65px;" src="' + data[i].proc_status1_img + '" alt="">';
			strDiv += '				</TD>';
			strDiv += '				<TD style="width:5px;">';
			strDiv += '				</TD>';
			strDiv += '				<TD style="width:80px;">';
			strDiv += '					<img class="iconImg" style="width:65px;" src="' + data[i].proc_status2_img + '" alt="">';
			strDiv += '				</TD>';
			strDiv += '				<TD style="width:5px;">';
			strDiv += '				</TD>';
			strDiv += '				<TD style="width:80px;">';
			strDiv += '					<img class="iconImg" style="width:65px;margin-left:5px;" src="' + data[i].proc_status3_img + '" alt="">';
			strDiv += '				</TD>';
			strDiv += '				<TD style="width:5px;">';
			strDiv += '				</TD>';
			strDiv += '				<TD style="width:80px;">';
			strDiv += '					<img class="iconImg" style="width:65px;margin-left:5px;" src="' + data[i].proc_status4_img + '" alt="">';
			strDiv += '				</TD>';
			strDiv += '			</TR>';
			strDiv += '			<TR>';
			strDiv += '				<TD><div class="' + data[i].proc_status_label[0] + '">' + data[i].proc_status1_comment + '</div></TD>';
			strDiv += '				<TD style="vertical-align:middle;">.....</TD>';
			strDiv += '				<TD><div class="' + data[i].proc_status_label[1] + '">꽃 준비 중</div></TD>';
			strDiv += '				<TD style="vertical-align:middle;">.....</TD>';
			strDiv += '				<TD><div class="' + data[i].proc_status_label[2] + '">' + data[i].proc_status3_comment + '</div></TD>';
			strDiv += '				<TD style="vertical-align:middle;">.....</TD>';
			strDiv += '				<TD><div class="' + data[i].proc_status_label[3] + '">수령 완료</div></TD>';
			strDiv += '			</TR>';
			strDiv += '		</TABLE>';
			strDiv += '	</div>';
			strDiv += '	<div class="myOrderListWrap" style="padding-left:4%;padding-right:4%;margin-bottom:25px;">';
			strDiv += '		<div class="myOrderListTitle">' + data[i].order_type + '</div>';
			strDiv += '		<div class="myOrderListSubTitle" style="background-color: #f3f3f3;padding:10px ;">';
			strDiv += '			<div style="font-size: 1.2em;font-weight: 300;color: #8c8c8c;margin-bottom: 5px;">' + data[i].com_name + '</div>';
			strDiv += '			<div style="font-size: 1.3em;font-weight: 900;color: #282828;">' + data[i].order_info + '</div>';
			if (data[i].order_type == "바로 구매") {
			strDiv += '			<div style="font-size: 1.3em;font-weight: 900;color: #282828;">' + data[i].receiveDateTime + '</div>';	
			}
			strDiv += '		</div>';
			strDiv += '	</div>';
			strDiv += '</div>';
			}
			
			$('.orderStatusWrapper').html(strDiv);
			setRefreshBtnClickEvent();
			
			if (toggleFlg == true) {
				$('.orderStatus').hide();
			}
			$('.orderStatus').css('border-bottom','solid 1px #9e9e9e36');
			$('.order_list_loader').hide();
		}
		
		function callReview(obj){
			var slideIndex = $(obj).parent().index();
			$('#slideIndex').val(slideIndex);
			
			var aoidx = $(obj).attr("aoidx");
			var status = $(obj).attr("status");
			var productcode = $(obj).attr("productcode");
			
			iframePopupOpen('/app/prreview_' + status + '_pop.php?productcode=' + productcode + '&aoidx=' + aoidx)
		}
		
		function reviewProc(aoidx,productcode){
			iframePopupClose();
			
			var slideIndex = $('#slideIndex').val();
			orderWrapSwiper.removeSlide(slideIndex);
			orderWrapSwiper.update();
		}
		
		function setRefreshBtnClickEvent() {
			$('.refreshBtn').unbind();
			$('.refreshBtn').click(function() {
				var member_id = "<?=$member_id?>";
				var slideIndex = $('.refreshBtn').index(this);
				orderWrapSwiper.destroy();
				$('.orderStatus').css('border-bottom','none');
				$('.order_list_loader').show();
				callOrderStatusEvent(member_id,false,slideIndex);
			});
		}
	</script>

	
	<div id="iframe_layer_popup" style="display: none; position: fixed;padding-top:calc(60px + env(safe-area-inset-top));padding-top:calc(60px + constant(safe-area-inset-top));  box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 910; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
		<div style="position:absolute;top: 76px;right:3%;color:#929292;font-size:2.5em;z-index: 900;"  onclick="iframePopupClose()">×</div>
		<div style="position: absolute; left: 0px; top: 50px; width: 100%; height: calc(100% - 50px); overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;background:#ffffff">
			<iframe frameborder="0" id="iframe_layer_content" name="iframe_layer_content" src="about:blank" style="width: 100%; height: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
		</div>
	</div>
</body>
</html>

<script>
	var map = null;
	function initMap(paramData) {
		$('#map').html('');
		map = null;
		var options = null;
		if(map == null){
			var pointx = ""
			var pointy = ""
			
			naver.maps.Service.geocode({
					address: '서울'
				}, function(status, response) {
					if (status !== naver.maps.Service.Status.OK) {
						return alert('Something wrong!');
					}
					pointx = <?=$pointx?>;
					pointy = <?=$pointy?>;
					map = new naver.maps.Map('map', {
									center: new naver.maps.LatLng(pointy, pointx),
									zoom: 18,
									logoControl: false
								});	
					// do Something
					marker = new naver.maps.Marker({
						position: new naver.maps.LatLng(pointy, pointx),
						zIndex: 100,
						icon: {
							content: [
										'<div class="timesaleMapNowPoint" >',
											'현재 내 위치',
										'</div>'
									].join('')
							,size: { width: 80, height: 30 }
						}
					});
					marker.setMap(map);
					marker.setMap(map);
				$.ajax({
					type : 'post',
					url : '/api/timesale_map_select.php',
					data: paramData,
					dataType: "json",
					error: function(xhr, status, error){
						alert("데이터 통신중에 오류가 발생했습니다.");
					},
					success : function(options){
						$('.timesaleMapGroup').remove();
						for (var i = 0, ii = options.length; i < ii; i++) {
							// info, text 속성을 넣기 위해 HTML 마커 사용
							marker = new naver.maps.Marker({
								position: new naver.maps.LatLng(options[i].pointy, options[i].pointx),
								zIndex: 100,
								icon: {
									content: [
												'<div class="timesaleMapGroup" value="'+ options[i].sell_count +'" shopName="' + options[i].brand_name + '" dongName="' + options[i].dongName + '" vidx="' + options[i].vidx + '" onclick="setShopInfo(this)">',
													'<img src="/app/skin/basic/svg/map_spot.svg" style="width:55px;">',
												'</div>'
											].join('')
									,size: { width: 55, height: 60 }
								}
							});
							marker.setMap(map);
						}
					}
				});
			});
		}	
	}
	
	function setShopInfo(obj){
		$(".timesaleMapInfoWrap").fadeIn(200);
		var shopName = $(obj).attr("shopName");
		var dongName = $(obj).attr("dongName");
		var vidx = $(obj).attr("vidx");
		$("#shopName").html(shopName);
		$("#shopAddr").html(dongName);
		$(".timesaleMapInfoWrap").attr("vidx",vidx);
	}
	
	function shopLink(obj){
		var vidx = $(obj).attr("vidx");
		location.href = "/app/vender_timesale.php?vidx=" + vidx + "&refURL=timesale_product_map";
	}
	
	//POPUP FILTER SCRIPT START
	function setStoreFlgChangeEvent() {
		$('.storeFlgOption').click(function() {
			if ($('#storeFlg').attr('toggleFlg') == 'N') {
				if ($('#storeFlgStatus').find('font').text() == '') {
					$('#storeFlg01').css('border-radius','15px 15px 0 0');
					$('#storeFlg02').css('border-radius','0 0 15px 15px');
					$('#storeFlg02').show();
				} else if ($('#storeFlgStatus').find('font').text() != '필터 적용중'){
					$('#storeFlgStatus').css('border-radius','15px 15px 0 0');
					$('#storeFlgStatus').attr('storeFlgVal',$('#storeFlgVal').val());
					if ($('#storeFlgVal').val() == 'Y') {
						$('#storeFlg01').css('border-radius','0 0 15px 15px');
						$('#storeFlg01').show();
					} else {
						$('#storeFlg02').css('border-radius','0 0 15px 15px');
						$('#storeFlg02').show();
					}
				} else {
					$('#storeFlgStatus').css('border-radius','15px 15px 0 0');
					$('#storeFlg01').css('border-radius','0');
					$('#storeFlg02').css('border-radius','0 0 15px 15px');
					$('#storeFlg01').show();
					$('#storeFlg02').show();
				}
				
				$('#storeFlg').attr('toggleFlg','Y');
			} else if ($('#storeFlg').attr('toggleFlg') == 'Y') {
				var storeFlgVal = $(this).attr('storeFlgVal');
				
				if ($(this).attr('id') != "storeFlgStatus") {
					$('#storeFlgVal').val(storeFlgVal);
				
					$('#storeFlgStatus').find('font').text($(this).text());
					$('#storeFlgStatus').attr('storeFlgVal',storeFlgVal);
					$('#storeFlgStatus').css('border-radius','15px 15px 15px 15px');
					$('#storeFlgStatus').show();
					
					$('#storeFlg01').find('img').hide();
					$('#storeFlg01').hide();
					$('#storeFlg02').hide();
					
					$('#storeFlg').attr('toggleFlg','N');
					
					var paramData = checkParameter(false);
					initMap(paramData);
				} else {
					$('#storeFlgStatus').css('border-radius','15px 15px 15px 15px');
					$('.storeFlgOption').not(this).hide();
					
					$('#storeFlg').attr('toggleFlg','N');
				}
			}
		});
	}

	function setOpenFilterBtnClickEvent() {
		$('#openFilterBtn').click(function() {
			//전체 꽃집 보기 필터 값 유지
			$('#storeFlg').attr('toggleFlg','N');
			if ($('#storeFlgStatus').find('font').text() == '') {
				$('#storeFlg01').css('border-radius','15px');
				$('#storeFlg02').hide();
			} else {
				$('#storeFlgStatus').css('border-radius','15px');
				$('#storeFlg01').hide();
				$('#storeFlg02').hide();
			}
			
			//가격 필터 값 적용
			var minprice = $('#minprice').val();
			var maxprice = $('#maxprice').val();
			setPrdPriceSlideEventPop(minprice,maxprice);
			
			//꽃 형태 필터 값 적용
			var prdTypeVal = $('#prdTypeVal').val().split(",");
			var selectedPrdNum = 0;
			$('.prdType').removeClass('selected');
			for (var i=1; i<=$('.prdType').length; i++) {
				for (var k=0; k<prdTypeVal.length; k++) {
					if ($('.prdType').eq(i).attr('prdType') == prdTypeVal[k]) {
						$('.prdType').eq(i).addClass('selected');
						selectedPrdNum++;
					}
				}
			}
			if (selectedPrdNum == 11) {
				$('.prdType').eq(0).addClass('selected');
			}
			
			//화분 크기 필터 값 적용
			var potSizeVal = $('#potSizeVal').val().split(",");
			var selectedPotNum = 0;
			$('.potSize').removeClass('selected');
			for (var i=1; i<$('.potSize').length; i++) {
				for (var k=0; k<potSizeVal.length; k++) {
					if ($('.potSize').eq(i).attr('potSize') == potSizeVal[k]) {
						$('.potSize').eq(i).addClass('selected');
						selectedPotNum++;
					}
				}
			}
			if (selectedPotNum == 5) {
				$('.potSize').eq(0).addClass('selected');
			}
			
			//할인상품필터 값 적용
			var timesaleFlg = $('#timesaleFlgVal').val();
			if (timesaleFlg == "Y") {
				$("input:checkbox[id='timesaleFlg']").prop("checked", true);
			} else {
				$("input:checkbox[id='timesaleFlg']").prop("checked", false);
			}
			
			//맵 필터 표시
			$('#mapFilter').show();
			popCloseClickEvent();
			setResetBtnClickEvent();
		});
	}
	
	function setMyGPSBtnClickEvent() {
		$('#myGPSBtn').click(function() {
			//전체 꽃집 보기 필터 값 유지
			$('#storeFlg').attr('toggleFlg','N');
			if ($('#storeFlgStatus').find('font').text() == '') {
				$('#storeFlg01').css('border-radius','15px');
				$('#storeFlg02').hide();
			} else {
				$('#storeFlgStatus').css('border-radius','15px');
				$('#storeFlg01').hide();
				$('#storeFlg02').hide();
			}
			
			var paramData = checkParameter(false);
			initMap(paramData);
		});
	}
	
	function setResetBtnClickEvent() {
		$('#resetBtn').click(function() {
			$('#storeFlgVal').val('N');
			$('#storeFlgStatus').find('font').text('');
			$('#storeFlg').attr('toggleFlg','N');
			$('#storeFlgStatus').hide();
			$('#storeFlg01').show();
			$('#storeFlg01').find('img').show();
			$('#storeFlg01').css('border-radius','15px');
			$('#storeFlg02').hide();
			$('#storeFlg02').css('border-radius','15px');
			
			$('#tempmin').val(0);
			$('#tempmax').val(70000);
			$('#minprice').val(0);
			$('#maxprice').val(70000);
			var minprice = $('#minprice').val();
			var maxprice = $('#maxprice').val();
			setPrdPriceSlideEventPop(minprice,maxprice);
			
			$('#prdTypeVal').val('');
			$('.prdType').removeClass('selected');
			
			$('#potSizeVal').val('');
			$('.potSize').removeClass('selected');
			
			$('#timesaleFlgVal').val('N');
			$("input:checkbox[id='timesaleFlg']").prop("checked", false);
		});
	}

	function setPrdPriceSlideEventPop(minprice,maxprice) {
		var price =[0, 30000, 50000,70000,100000, 150000, 200000 , 500000];
		var minIdx=0;
		var maxIdx=0;
		for (var i=0; i<price.length; i++) {
			if (minprice == price[i]) {
				minIdx = i;
			}
			if (maxprice == price[i]) {
				maxIdx = i;
			}
		}
		
		$( function() {
			$( "#slider-range-max-pop" ).slider({
				range: true,
				min: 0,
				max: 7,
				values: [ minIdx, maxIdx ],
				slide: function( event, ui ) {
					var min = ui.values[0];
					var max = ui.values[1];
					$('#tempmin').val(price[min]);
					$('#tempmax').val(price[max]);
				}	
			});
			$( "#amount" ).val( "$" + $( "#slider-range-max-pop" ).slider( "values", 0 ) +
			" - $" + $( "#slider-range-max-pop" ).slider( "values", 1 ) );
		});
	}

	function setPrdTypeClickEvent() {
		$('.prdType').click(function() {
			if ($(this).attr('prdType') == "all") {
				if ($(this).hasClass('selected')) {
					$('.prdType').removeClass('selected');
				} else {
					$('.prdType').addClass('selected');
					
				}
			} else {
				if ($('.prdType').eq(0).hasClass('selected')) {
					$('.prdType').removeClass('selected');
				}
				if ($(this).hasClass('selected')) {
					$(this).removeClass('selected');
				} else {
					$(this).addClass('selected');
				}
			}
		});
	}
	
	function setPotSizeClickEvent() {
		$('.potSize').click(function() {
			$('#potSizeVal').val();
			if ($(this).attr('potSize') == "all") {
				if ($(this).hasClass('selected')) {
					$('.potSize').removeClass('selected');
				} else {
					$('.potSize').addClass('selected');
				}
			} else {
				if ($('.potSize').eq(0).hasClass('selected')) {
					$('.potSize').removeClass('selected');
				}
				if ($(this).hasClass('selected')) {
					$(this).removeClass('selected');
				} else {
					$(this).addClass('selected');
				}
			}
		});
	}

	function checkParameter(priceFlg) {
		var paramData = "";
		if (priceFlg == true) {
			//가격 필터 값 취득
			var tempmin = $('#tempmin').val();
			var tempmax = $('#tempmax').val();
			
			//가격 필터 값 예외처리
			if (tempmin >= 0) {
				$('#minprice').val(tempmin);
			} else {
				alert('올바른 가격을 입력해 주세요');
				return;
			}
			if (tempmax > 0) {
				$('#maxprice').val(tempmax);
			} else {
				alert('올바른 가격을 입력해 주세요');
				return;
			}
			var minprice = $('#minprice').val();
			var maxprice = $('#maxprice').val();
			paramData += "min_price=" + minprice + "&max_price=" + maxprice + "&";
		}
		
		//꽃 형태 필터 값 취득
		var prdTypeVal = "";
		for (var i=1; i<=$('.prdType').length; i++) {
			if ($('.prdType').eq(i).hasClass('selected')) {
				prdTypeVal += ($('.prdType').eq(i).attr('prdType') + ",");
			}
		}
		prdTypeVal = prdTypeVal.slice(0,-1);
		$('#prdTypeVal').val(prdTypeVal);
		
		//화분 크기 필터 값 취득
		var potSizeVal = "";
		for (var i=1; i<=$('.potSize').length; i++) {
			if ($('.potSize').eq(i).hasClass('selected')) {
				potSizeVal += ($('.potSize').eq(i).attr('potSize')+',');
			}
		}
		potSizeVal = potSizeVal.slice(0,-1);
		$('#potSizeVal').val('');
		$('#potSizeVal').val(potSizeVal);
		
		//할인상품 필터 값 취득
		var timesaleFlg = $("#timesaleFlgVal").val();
		
		//전체/등록꽃집 필터 값 취득
		var storeFlg = $('#storeFlgVal').val();
		
		paramData += "product_type=" + prdTypeVal + "&flower_pot_size=" + potSizeVal + "&timesale_flg=" + timesaleFlg + "&store_flg=" + storeFlg;
		return paramData;
	}


	//필터 적용버튼
	function setFilterApplyBtnClickEvent() {
		$('#filterApplyBtn').click(function() {
			$('#storeFlgVal').val('Y');
			
			$('#storeFlgStatus').find('font').text('필터 적용중');
			$('#storeFlgStatus').attr('storeFlgVal',$('#storeFlgVal').val());
			$('#storeFlgStatus').css('border-radius','15px 15px 15px 15px');
			$('#storeFlgStatus').show();
			
			$('#storeFlg01').find('img').hide();
			$('#storeFlg01').hide();
			$('#storeFlg02').hide();
			
			$('#mapFilter').hide();
			var paramData = checkParameter(true);
			initMap(paramData);
		});
	}
	
	function setTimesaleFlgClickEvent(){
		$('#timesaleFlg').click(function(){
			if($("input:checkbox[id='timesaleFlg']").attr("checked") == "checked"){
				$("input:checkbox[id='timesaleFlg']").attr("checked",false);
				$('#timesaleFlgVal').val('N');
			}else{
				$("input:checkbox[id='timesaleFlg']").attr("checked",true);
				$('#timesaleFlgVal').val('Y');
			}
		});
	}
	
	function popCloseClickEvent(){
		$('#popClose').click(function(){
			$('#mapFilter').hide();
			if($('#storeFlg').attr('toggleFlg') == 'Y'){
				$('#storeFlg').attr('toggleFlg','N');
				setStoreFlgChangeEvent();
			}
		});
	}
	//POPUP FILTER SCRIPT END
</script>
<div id="map" style="width:100%;height:calc(100vh - 259px - constant(safe-area-inset-bottom) - constant(safe-area-inset-top));height:calc(100vh - 259px - env(safe-area-inset-bottom) - env(safe-area-inset-top));overflow:hidden;"></div>
<div class="timesaleMapInfoWrap" style="bottom:60px;" onclick="shopLink(this);">
	<div class="infoBox">
		<div class="mapIcon">
			<img src="/app/skin/basic/svg/adr_list.svg" style="height: 20px;vertical-align: middle;">
		</div>
		<div class="shopName" id="shopName">
			
		</div>
		<div class="shopAddr" id="shopAddr">
			
		</div>
		<div class="rightArrow">
			&#xE001
		</div>
	</div>
</div>
<div id="storeFlg" toggleFlg='N' style="float:left;position:fixed;top:210px;left:15px;z-index:9999;">
	<div id="storeFlgStatus" class="storeFlgOption" style="width:140px;height:25px;background-color:#464646;border-radius:20px;color:#ffffff;padding-top:8px;padding-left:15px;font-size:1.0rem;display:none;">
		<font></font>
		<img src="/app/skin/basic/svg/filterAngleDown.svg" style="float:right;margin-top:6px;margin-right:10px;width:10px;">
	</div>
	<div id="storeFlg01" class="storeFlgOption" style="width:140px;height:25px;background-color:#464646;border-radius:20px;color:#ffffff;padding-top:8px;padding-left:15px;font-size:1.0rem;" storeFlgVal="N">
		<font>전체 꽃집 보기</font>
		<img src="/app/skin/basic/svg/filterAngleDown.svg" style="margin-top:6px;margin-right:10px;width:10px;float:right;">
	</div>
	<div id="storeFlg02" class="storeFlgOption" style="width:140px;height:25px;background-color:#464646;color:#ffffff;padding-top:8px;padding-left:15px;font-size:1.0rem;display:none;" storeFlgVal="Y">
		<font>등록 꽃만 보기</font>
	</div>
</div>

<div id="myGPSBtn" style="float:right;position:fixed;top:210px;right:15px;z-index:999;">
	<img src="/app/skin/basic/svg/myGps.svg" style="width: 35px;height:35px;" alt="">
</div>

<div id="openFilterBtn" style="float:right;position:fixed;top:210px;right:70px;z-index:999;">
	<img src="/app/skin/basic/svg/mapSearchBtn.svg" style="width: 35px;height:35px;" alt="">
</div>

<div id="mapFilter" style="display:none;z-index:9999">
	<input type="hidden" id="prdTypeVal" value="">
	<input type="hidden" id="potSizeVal" value="">
	<input type="hidden" id="timesaleFlgVal" value="N">
	<input type="hidden" id="storeFlgVal" value="N">
	
	<div class="mapFilterWrap">
		<div style="display: flex;justify-content: space-between;padding:20px 32px 0 32px;">
			<div id="resetBtn" style="width: 25px;">
				<img src="/app/skin/basic/svg/timesale_pop_reset.svg" alt="">
			</div>
			<div id="popClose" style="width: 15px;">
				<img style="position: relative;top: 7px;" src="/app/skin/basic/svg/timesale_pop_close.svg" alt="">
			</div>
		</div>
		<div style="overflow: scroll;">
			<div style="border-bottom:solid 1px #9e9e9e36">
				<div class="pop_section_pd">
					<span class="popTitle">상품필터</span>
					<span class="popTitleNoti">* 찾으시는 상품만 골라 볼 수 있어요!</span>
				</div>
			</div>
			<div style="border-bottom:solid 1px #9e9e9e36">
				<div class="pop_section_pd" style="padding-bottom: 0;">
					<div class="popSectionTitle">가격</div>
					<div class="priceGroup">
						<div id="slider-range-max-pop"></div>
						<input type="hidden" id="minprice" name="minprice" value="0">
						<input type="hidden" id="maxprice" name="maxprice" value="70000">
						<input type="hidden" id="tempmin" name="tempmin" value="0">
						<input type="hidden" id="tempmax" name="tempmax" value="70000">
					</div>
					<div class="priceList d_f_J">
						<div></div>
						<div class="priceListFont">3만원</div>
						<div class="priceListFont">5만원</div>
						<div class="priceListFont">7만원</div>
						<div class="priceListFont">10만원</div>
						<div class="priceListFont">15만원</div> 
						<div class="priceListFont">20만원</div>
						<div class="priceListFont">최대</div>
					</div>
				</div>
			</div>
			<div style="border-bottom:solid 1px #9e9e9e36">
				<div class="pop_section_pd">
					<div class="popSectionTitle">꽃 형태</div>
					<div style="overflow: hidden;">
						<div class="prdType" prdType="all" >
							<div class="popPrdTypeFont">전체보기</div>
						</div>
					<?
						$shape_sql = "SELECT keyText, valText FROM item_mst
										where keyText ='productType'";
						$shape_result = mysql_query($shape_sql,get_db_conn());
						$shapename = array();
						$i = 0;
						while($shapename_row = mysql_fetch_object($shape_result)) {
							$shapename[$i]['valText']=$shapename_row->valText; 
							$i++;
						}
						mysql_free_result($shape_result);
						$num = 0;
						for($i = $num; $i < count($shapename); $i++){
					?>
						<div class="prdType" prdType="<?=$shapename[$i]['valText']?>">
							<div class="popPrdTypeFont"><?=$shapename[$i]['valText']?></div>
						</div>
					<?	
						}
					?>
					</div>
				</div>
			</div>
			<div style="border-bottom:solid 1px #9e9e9e36">
				<div class="pop_section_pd">
					<div class="popSectionTitle">화분 크기</div>
					<div style="overflow: hidden;">
						<div class="potSize" potSize="all">
							<div class="popPotFont">전체보기</div>
						</div>
					<?
					$shape_sql = "SELECT * FROM item_mst WHERE keyText ='flowerPot'";
					$shape_result = mysql_query($shape_sql,get_db_conn());
					$shapename = array();
					$i = 0;
					while($shapename_row = mysql_fetch_object($shape_result)) {
						$shapename[$i]['valText']=$shapename_row->valText; 
						$shapename[$i]['val01']=$shapename_row->val01;
						$i++;
					}
					+
					mysql_free_result($shape_result);
					$num = 0;
					for($i = $num; $i < count($shapename); $i++){
						?>
						<div class="potSize" potSize="<?=$shapename[$i]['val01']?>">
							<div class="popPotFont"><?=$shapename[$i]['valText']?></div>
						</div>
						<?	
					}
					?>
					</div>	
				</div>
			</div>
			<div style="border-bottom:solid 1px #9e9e9e36">
				<div class="pop_section_pd" style="display: flex; ">
					<div style="line-height: 23px;">
						<span class="popSectionTitle">할인 상품만 보기</span>
						<span class="popTitleNoti">*마감할인 상품이 해당됩니다</span>
					</div>
					<div class="toggle-switch" style="margin-left: 28px;">
						<input type="checkbox" id="timesaleFlg" style="display: none;">
						<label for="timesaleFlg">
							<span class="toggle-track"></span>
						</label>
					</div>
				</div>
			</div>
			<div id="filterApplyBtn" class="popApplyBtn">필터 적용하기</div>
		</div>
	</div>
</div>

<style>
#mapFilter{
	position: fixed;
	bottom: 0;
	background-color: #ffffff;
	width: 100%;
	height: calc(660px + env(safe-area-inset-bottom));
	height: calc(660px + constant(safe-area-inset-bottom));
	z-index:999;
	border-top-left-radius: 20px;
	border-top-right-radius: 20px;
	box-shadow: 0px 3px 5px 1px #464646;
}
.popTitle{
	color: #282828;
	font-size: 1.5em; /* 3.5pt */
	font-weight: 500;
}
.popTitleNoti{
    font-size: 1.0em;
    color: #969696;
    font-weight: 400;
    margin-left: 10px;
}
.popSectionTitle{
	color: #282828;
	font-size: 1.3em; /* 3pt */
	font-weight: 500;
}

.pop_section_pd{
	padding: 22px 20px 22px 34px;
}
.popApplyBtn{
	color: #ffffff;
	font-size: 1.4em;/* 3.4 */
	font-weight: 900;
	text-align: center;
	/* height: 100px; */
	padding: 20px;
	background-color: #e51e6e;
}
.priceListFont{
	color: #282828;
	font-weight: 400;
	font-size: 1.0em;
}
.prdType{
	border-radius: 20px;
    font-size: 1em;
    border: 1px solid #c8c8c8;
    color: #282828;
    font-weight: 500;
    text-align: center;
    width: 20%;
    margin-right: 4px;
    float: left;
    margin-top: 10px;
    padding: 3px 5px;
}
.popPrdTypeFont{
}
.popPotFont{
}
.potSize{
	border-radius: 20px;
    font-size: 1em;
    border: 1px solid #c8c8c8;
    color: #282828;
    font-weight: 500;
    text-align: center;
    width: 45%;
    float: left;
    padding: 3px;
    margin-top: 10px;
}
.potSize:nth-child(odd) {
	margin-right: 6px;
}
.selected{background-color: #464646; color: #ffffff;}
/* toggle css */
.toggle-track{
	display: inline-block;
	position: relative;
	width: 60px;
	height: 25px;
	border-radius:60px;
	background: #8b8b8b;
	background: #464646;
}
.toggle-track:before{
	content:'';
	display: block;
	position: absolute;
	top: -4px;
	left: -4px;
	width: 20px;
	height: 20px; 
	margin: 5px;
	background: #fff;
	border-radius:100%;
	border:1px solid #8b8b8b;
	transition:left 0.3s;
}
.toggle-switch input[type=checkbox] + label .toggle-track:after{
	content:'OFF';
	display: inline-block;
	position: absolute;
	right: 8px;
	top: 4px;
	color: #fff;
}

.toggle-switch input[type=checkbox]:checked + label .toggle-track{
	background: #464646;
}
.toggle-switch input[type=checkbox]:checked + label .toggle-track:before{
	left: 30px;
	border:1px solid #464646;
}
.toggle-switch input[type=checkbox]:checked + label .toggle-track:after{
	content:'ON';
	left: 5px;
}
</style>

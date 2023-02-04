<script>
        var map = null;
        function initMap() {
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
					$.ajax({
						type : 'post',
						url : '/api/timesale_map_select.php',
						data : '',
						dataType : 'json',
						error: function(xhr, status, error){
							alert("데이터 통신중에 오류가 발생했습니다.");
						},
						success : function(options){
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
			location.href = "/app/vender_timesale.php?vidx=" + vidx;
		}
	
</script>
<div class="filter_wrapper">
	<form name=filterForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
		<input type="hidden" id="dfVal" name="dfVal" value="">
		<input type="hidden" id="ptfVal" name="ptfVal" value="">
		<input type="hidden" id="pfVal" name="pfVal" value="">
		<select id="distanceFilter" id="priceFilterVal" name="distanceFilter" class="input basic_input productType filter">
			<option value="N">정렬</option>
			<option value="endAsc">빠른종료시간</option>
			<option value="endDesc">늦은종료시간</option>
			<option value="sellpriceDesc">높은가격</option>
			<option value="sellpriceAsc">낮은가격</option>
			<option value="distance">가까운거리</option>
		</select>
		<select id="distanceFilter" id="priceFilterVal" name="distanceFilter" class="input basic_input productType filter" style="margin-left:10px;">
			<option value="N">거리</option>
			<option value="1">1km 이내</option>
			<option value="3">3km 이내</option>
			<option value="5">5km 이내</option>
			<option value="10">10km 이내</option>
		</select>
		<select id="productTypeFilter" name="productTypeFilter" class="input basic_input productType filter" style="margin-left:10px;">
			<option value="N">꽃 형태</option>
		<?
			$sql = "SELECT * FROM item_mst WHERE keyText='productType' ORDER BY sortNum";
			//echo $sql;
			$result2=mysql_query($sql,get_db_conn());
			while($row2=mysql_fetch_object($result2)) {
		?>
				<option value="<?=$row2->seq?>"><?=$row2->valText?></option>
		<?	}
			mysql_free_result($result2);?>
		</select>
		<select id="priceFilter" name="priceRangeFilter" class="basic_input priceRange filter" style="margin-left:10px;">
			<option value="N">가격 범위</option>
		<?
			$sql = "SELECT * FROM item_mst WHERE keyText='priceRange' ORDER BY sortNum";
			//echo $sql;
			$result3=mysql_query($sql,get_db_conn());
			while($row3=mysql_fetch_object($result3)) {
		?>
				<option value="<?=$row3->seq?>"><?=$row3->valText?></option>
		<?	}
			mysql_free_result($result3);?>
		</select>
	</form>
</div>
<div id="map" style="width:100%;height:calc(100vh - 305px - constant(safe-area-inset-bottom) - constant(safe-area-inset-top));height:calc(100vh - 305px - env(safe-area-inset-bottom) - env(safe-area-inset-top));overflow:hidden;"></div>
<div class="timesaleMapInfoWrap" onclick="shopLink(this);">
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
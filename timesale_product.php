<?
include_once('header.php');
include_once($Dir."app/inc/paging_inc.php");

$listType = $_REQUEST["listType"];
if($listType == ""){
	$listType = "list";
}
$currentPage=$_REQUEST["page"];
if(!$currentPage){ $currentPage=1; }

$pointx = $_REQUEST["pointx"];
$pointy = $_REQUEST["pointy"];

$itemcount = 4; // 페이지당 게시글 리스트 수
$rowcount = 7; // 등록된 전체상품 수

$qry = " ";

if($code){
	$qry.= "AND cc.categorycode LIKE '".$likecode."%' ";
	$add_query="&code=".$code;
}

$qry.="AND a.display='Y' ";

?>
<style>
	.sortDropdown{
	text-align: left;
	}
	.sortDropbtn{
		line-height: 25px;
		width: 90px;
		cursor : pointer;
	}
	.sortDropbtn img{width: 14px;margin-left: 4px;}
	.sortDropdown-content{
		position: absolute;
		text-align: left;
		z-index: 100;
		background-color: #ffffff;
		border-bottom-right-radius: 20px;
    	border-bottom-left-radius: 20px;
		border: solid 1px #c8c8c8;
		margin-left: -1px;
	}
	.changeSort{
		border-bottom-right-radius: 0px !important;
		border-bottom-left-radius: 0px !important;
	}
	.sortDropdown-content div{
		display: block;
		text-decoration: none;
		color: rgb(37, 37, 37);
		font-size: 12px;
		padding: 5px 5px 5px 9px;
		border-top: 1px solid #c8c8c8;
		width: 76px;
	}

	.timesale_menu{box-sizing:border-box;overflow:hidden;}
	.timesale_menu li{width:50%;text-align:center;font-size: 15px;}
	.timesale_menu li a{display:block;padding:12px 5px;}
	.timesale_menu li.selected{border-bottom:3px solid #242424;box-sizing:border-box; font-weight: 600;color:#282828;}
	.product_category{background-color: #78be64;padding: 2px 5px;color:#282828;border: 1px solid #282828;z-index: 10;border-radius: 14px;font-weight: bold;line-height: 14px;font-size: 13px;position: absolute;top: 10px;left: 10px;}
	.product_productTypeText{background-color: #ff8264;padding: 2px 5px;color:#282828;border: 1px solid #282828;z-index: 10;border-radius: 14px;font-weight: bold;line-height: 14px;font-size: 13px;position: absolute;top: 10px;left: 10px;}
	.productTimeWrap{background: #f0f0f0;border-radius: 5px;width: 70%;height: 20px;font-size:0.9rem;}
</style>
<script>
	var mobileOS = "";
	$(document).ready(function() {
		mobileOS = getMobileOS();
		var pointx = "<?=$pointx?>";
		var pointy = "<?=$pointy?>";
		
		if (pointx.length == 0) {
			pointx = window.nativeObj.coordinate.longitude;
		}
		if (pointy.length == 0) {
			pointy = window.nativeObj.coordinate.latitude;
		}
		if (pointx.length == 0 || pointx == 0 || pointy.length == 0 || pointy == 0) {
			//alert('GPS 좌표 수신중 문제가 발생했습니다. 좌표 설정을 확인해주세요');
		}
		var listType = "<?=$listType?>";
		var paramData = "";
		if (listType == "list") {
			paramData = "search_flg=all&mb_addr_point_x=" + pointx + "&mb_addr_point_y=" + pointy;
			$('.timesale_menu').css('margin-top','0px');

			setCategoryBtnClickEvent();
			sortDrop();
			sortDropdownEvent();
			setResetBtnClickEvent();
			setFilterBtnClickEvent();
			setPrdTypeBtnClickEvent();
			setPrdTypeApplyBtnClickEvent();
			setPrdPriceApplyBtnClickEvent();		
			setPotSizeBtnClickEvent();
			setPotSizeApplyBtnClickEvent();
			setPrdPriceSlideEvent(0,70000);
		} else {
			paramData = "timesale_flg=N&store_flg=N";
			
			var cssTop = "";
			if (mobileOS == "ios") {
				cssTop = "240px";
				marginTop = "59px";
			} else {
				cssTop = "200px";
				marginTop = "65px";
			}
			
			$('.mapHeightObject').css('height',marginTop);
			$('#mapBtn').css('background-color','#ffffff');
			
			$('#storeFlg').css('top',cssTop);
			$('#myGPSBtn').css('top',cssTop);
			$('#openFilterBtn').css('top',cssTop);
			
			setStoreFlgChangeEvent();
			setOpenFilterBtnClickEvent();
			setMyGPSBtnClickEvent();
			setPrdPriceSlideEventPop(0,70000);
			setPrdTypeClickEvent();
			setPotSizeClickEvent();
			setFilterApplyBtnClickEvent();
			setTimesaleFlgClickEvent();
		}

		menuSelecter('<?=$listType?>',paramData);
		
		setBtnPrevClickEvent();
	});

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
	
	function setBtnPrevClickEvent() {
		$('.btn_prev').click(function() {
			location.href = "main.php";
		})
	}

	function menuSelecter(type,paramData){
		if(type == "list"){
			$("#listBtn").addClass("selected");
		}
		else if(type == "map"){
			$("#mapBtn").addClass("selected");
		}
		callApiEvent(paramData);
	}
	
	function setPrdPriceSlideEvent(minprice,maxprice) {
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
			$( "#slider-range-max" ).slider({
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
			$( "#amount" ).val( "$" + $( "#slider-range-max" ).slider( "values", 0 ) +
			" - $" + $( "#slider-range-max" ).slider( "values", 1 ) );
		});
	}

<?if ($listType == "list") {?>
	function setCategoryBtnClickEvent() {
		$('.categoryBtn').click(function() {
			$('.sortDropdown').removeClass('changeSort');
			var categoryVal = $(this).attr('category');
			if (categoryVal == "all") {
				$('.categoryBtn').not(this).removeClass('selected');
				if (!$(this).hasClass('selected')) {
					$(this).addClass('selected');
				}
			} else {
				$('.categoryBtn').eq(0).removeClass('selected');
				addSelectedClassEvent($(this));
			}
			
			if ($('.categoryBtn.selected').length == 0) {
				$('.categoryBtn').eq(0).addClass('selected');
				changeSelectedImgEvent($('.categoryBtn').eq(0));
			}
			var selectedCategory =  $('.categoryBtn.selected');
			if(!$(".filterBtn").eq(1).hasClass('disabledbutton')){
				$(".filterBtn").eq(1).addClass("disabledbutton");
			}
			if(!$(".filterBtn").eq(2).hasClass('disabledbutton')){
				$(".filterBtn").eq(2).addClass("disabledbutton");
			}
			
			$('.shapeWrap').hide();
			$('.priceWrap').hide();
			$('.sortDropdown-content').hide();
			$('.potSizeWrap').hide();
			$('.filterBtn').removeClass('selected');
			removeCheckedClassEvent();
			var paramData = checkParameter();
			callApiEvent(paramData);
			
			changeSelectedImgEvent($(this));
			
			for(var i = 0; i < selectedCategory.length; i++){
				var attrCategory = selectedCategory.eq(i).attr('category');
				if(attrCategory == "all" || attrCategory == "TS"){
					$(".filterBtn").eq(1).removeClass("disabledbutton");
					$(".filterBtn").eq(2).removeClass("disabledbutton");
					return;
				}else if(attrCategory == "FP"){
					$(".filterBtn").eq(2).removeClass("disabledbutton");
				}else{
					$(".filterBtn").eq(1).removeClass("disabledbutton");
				}	
			}
		});
	}

	function setResetBtnClickEvent() {
		$('.resetBtn').click(function() {
			$('.sortDropdown').removeClass('changeSort');
			$('.sortDropdown').removeClass('selected');
			$('.sortDropdown').css('background-color','#ffffff');
			$('.sortDropdown').css('border','solid 1px #c8c8c8');
			$('.sortDropdown').css('color','#282828');
			$('.sortDropbtn img').attr('src','/app/skin/basic/svg/timesale_product_underArrow.svg');
			$('.sortArrowImg').removeClass('sortArrWhite');
			$('.sortDropText').attr('sort','1');
			$('.sortDropText').text('짧은 거리순');
			
			$('.filterBtn').removeClass('checked');
			$('.filterBtn').removeClass('selected');
			$('.prdTypeBtn').removeClass('selected');
			$('#selectedPrdType').val('');
			$('#selectedPotSize').val('');
			$('.potSizeBtn').removeClass('selected');
			
			$('#minprice').val(0);
			$('#maxprice').val(70000);
			$('#tempmin').val(0);
			$('#tempmax').val(70000);
			setPrdPriceSlideEvent(0,70000);
			
			$('.shapeWrap').hide();
			$('.priceWrap').hide();
			$('.sortDropdown-content').hide();
			$('.potSizeWrap').hide();
			
			$('.filterBtn').eq(0).find('.filterFontD').text('꽃 가격');
			$('.filterBtn').eq(0).find('.filterFontD').css('font-size','0.85rem');
			$('.filterBtn').eq(1).find('.filterFontD').text('꽃 형태');
			$('.filterBtn').eq(2).find('.filterFontD').text('화분 크기');
			
			var paramData = checkParameter();
			callApiEvent(paramData);
		});
	}
	
	function sortDrop(){
		$('.sortDropbtn').click(function(){
			$('.shapeWrap').hide();
			$('.priceWrap').hide();
			$('.potSizeWrap').hide();
			$('.filterBtn').removeClass('selected');
			$('.sortDropdown').addClass('changeSort');
			
			if ($('.sortDropdown-content').css('display') == "none") {
				$('.sortDropdown-content').show();
				$('.sortDropbtn img').attr('src','/app/skin/basic/svg/timesale_product_upArrow.svg');
				removeCheckedClassEvent();
			} else {
				$('.sortDropdown-content').hide();
				$('.sortDropbtn img').attr('src','/app/skin/basic/svg/timesale_product_underArrow.svg');
				$('.sortDropdown').removeClass('changeSort');
			}

			setPrdPriceCheckEvent();
			setPrdTypeCheckEvent();
		});
	}
	function sortDropdownEvent(){
		$('.sortBtn').click(function(){
			
			var sortText =$(this).text();
			var sortVal = $(this).attr('sort');
			
			$('.sortDropText').attr('sort',sortVal);
			$('.sortDropText').text(sortText);
			$('.sortDropbtn img').attr('src','/app/skin/basic/svg/timesale_product_underArrow.svg');
			$('.sortDropdown').addClass('selected');
			
			$('.sortDropdown-content').hide();
			$('.sortDropdown').removeClass('changeSort');
			
			if($('.sortDropdown').hasClass('selected')){
				$('.sortArrowImg').addClass('sortArrWhite');
				$('.sortDropdown').css('background-color','#464646');
				$('.sortDropdown').css('color','#ffffff');
			}else{
				$('.sortDropdown').css('background-color','#ffffff');
				$('.sortDropdown').css('color','#282828');
				$('.sortArrowImg').removeClass('sortArrWhite');
			}
			$('.sortDropdown').removeClass('changeSort');

			var paramData = checkParameter();
			callApiEvent(paramData);
		});
	}

	function setFilterBtnClickEvent() {
		$('.filterBtn').click(function() {
			var filterBtnCnt = $('.filterBtn').length;
			var filterVal = $(this).attr('filter');
			
			if (filterVal == "prdPrice") {
				var minprice = $('#minprice').val();
				var maxprice = $('#maxprice').val();
				
				$('#tempmin').val(minprice);
				$('#tempmax').val(maxprice);
				$('.filterBtn').not(this).removeClass('selected');
				
				addSelectedClassEvent($(this));
				removeCheckedClassEvent();
				
				$('.shapeWrap').hide();
				$('.potSizeWrap').hide();
				$('.sortDropdown-content').hide();
				$('.sortDropdown').removeClass('changeSort');
				$('.priceWrap').toggle();
				
				setPrdPriceSlideEvent(minprice,maxprice);
			} else if (filterVal == 'prdType') {
				$('.filterBtn').not(this).removeClass('selected');
				addSelectedClassEvent($(this));
				removeCheckedClassEvent();
				
				$('.prdTypeBtn').removeClass('selected');
				var selectedPrdType = $('#selectedPrdType').val();
				var prdTypeArr = selectedPrdType.split(",");
				
				for  (var i=0; i<$('.prdTypeBtn').length; i++) {
					var prdTypeBtn = $('.prdTypeBtn').eq(i);
					for (var j=0; j<prdTypeArr.length; j++) {
						if (prdTypeBtn.attr('prdType') == prdTypeArr[j]) {
							prdTypeBtn.addClass('selected');
						}
					}
				}
				$('.shapeWrap').toggle();
				$('.potSizeWrap').hide();
				$('.priceWrap').hide();
				$('.sortDropdown').removeClass('changeSort');
				$('.sortDropdown-content').hide();
			}else if (filterVal == 'potSize'){
				$('.filterBtn').not(this).removeClass('selected');
				addSelectedClassEvent($(this));
				removeCheckedClassEvent();
				
				$('.potSizeBtn').removeClass('selected');
				var selectedPotSize = $('#selectedPotSize').val();
				var potSizeArr = selectedPotSize.split(",");
				
				for  (var i=0; i<$('.potSizeBtn').length; i++) {
					var potSizeBtn = $('.potSizeBtn').eq(i);
					for (var j=0; j<potSizeArr.length; j++) {
						if (potSizeBtn.attr('potSize') == potSizeArr[j]) {
							potSizeBtn.addClass('selected');
						}
					}
				}
				$('.shapeWrap').hide();
				$('.priceWrap').hide();
				$('.sortDropdown').removeClass('changeSort');
				$('.sortDropdown-content').hide();
				$('.potSizeWrap').toggle();
			}
		});
	}

	function setPrdTypeBtnClickEvent() {
		$('.prdTypeBtn').click(function() {
			var prdTypeVal = $(this).attr('prdType');
			if (prdTypeVal == "all") {
				if ($(this).hasClass('selected')) {
					$('.prdTypeBtn').removeClass('selected');
				} else {
					$('.prdTypeBtn').addClass('selected');
				}
			} else {
				var prdTypeBtnCnt = $('.prdTypeBtn.selected').length;
				if (prdTypeBtnCnt > 0) {
					$('.prdTypeBtn').eq(0).removeClass('selected');
				}
				addSelectedClassEvent($(this));
			}
		});
	}

	function setPotSizeBtnClickEvent() {
		$('.potSizeBtn').click(function() {
			var potSizeVal = $(this).attr('potSize');
			if (potSizeVal == "all") {
				if ($(this).hasClass('selected')) {
					$('.potSizeBtn').removeClass('selected');
				} else {
					$('.potSizeBtn').addClass('selected');
				}
			} else {
				var potSizeBtnCnt = $('.potSizeBtn.selected').length;
				if (potSizeBtnCnt > 0) {
					$('.potSizeBtn').eq(0).removeClass('selected');
				}
				addSelectedClassEvent($(this));
			}
		});
	}

	function setPrdTypeApplyBtnClickEvent() {
		$('.prdTypeApplyBtn').click(function() {
			var prdTypeCnt = $('.prdTypeBtn').length;
			var selectedCnt = 0;
			var selectedPrdType = "";
			for (var i=0; i<prdTypeCnt; i++) {
				if ($('.prdTypeBtn').eq(i).hasClass('selected')) {
					var prdType = $('.prdTypeBtn').eq(i).attr('prdType');
					selectedPrdType += prdType + ",";
					selectedCnt++;
				}
			}
			selectedPrdType = selectedPrdType.slice(0,-1);
			
			if (selectedCnt > 0) {
				$('.filterBtn').eq(1).removeClass('selected');
				$('.filterBtn').eq(1).addClass('checked');
				$('#selectedPrdType').val(selectedPrdType);
				if (selectedCnt == 12) {
					selectedCnt--;
				}
				$('.filterBtn').eq(1).find('.filterFontD').text('+' + selectedCnt);
			} else {
				alert('한개 이상의 꽃 형태를 선택해 주세요.');
				return;
			}
			
			var paramData = checkParameter();
			callApiEvent(paramData);
			$('.shapeWrap').hide();
		});
	}

	function setPrdPriceApplyBtnClickEvent() {
		$('.prdPriceApplyBtn').click(function() {
			var tempmin = $('#tempmin').val();
			var tempmax = $('#tempmax').val();
			
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
			
			$('.filterBtn').eq(0).removeClass('selected');
			$('.filterBtn').eq(0).addClass('checked');
			
			var minprice = ($('#minprice').val()/10000);
			if (minprice == 0) {
				minprice = "";
			}
			var maxprice = ($('#maxprice').val()/10000) + "만";
			
			$('.filterBtn').eq(0).find('.filterFontD').text(minprice + "～" + maxprice);
			$('.filterBtn').eq(0).find('.filterFontD').css('font-size','0.7rem');
			
			var paramData = checkParameter();
			callApiEvent(paramData);
			$('.priceWrap').hide();
		});
	}
	
	function setPotSizeApplyBtnClickEvent() {
		$('.potSizeApplyBtn').click(function() {
			var potSizeCnt = $('.potSizeBtn').length;
			var selectedCnt = 0;
			var selectedPotSize = "";
			for (var i=0; i<potSizeCnt; i++) {
				if ($('.potSizeBtn').eq(i).hasClass('selected')) {
					var potSize = $('.potSizeBtn').eq(i).attr('potSize');
					selectedPotSize += potSize + ",";
					selectedCnt++;
				}
			}
			selectedPotSize = selectedPotSize.slice(0,-1);
			if (selectedCnt > 0) {
				$('.filterBtn').eq(2).removeClass('selected');
				$('.filterBtn').eq(2).addClass('checked');
				$('#selectedPotSize').val(selectedPotSize);
				if (selectedCnt == 6) {
					selectedCnt--;
				}
				$('.filterBtn').eq(2).find('.filterFontD').text('+' + selectedCnt);
			} else {
				alert('한개 이상의 화분 크기를 선택해 주세요.');
				return;
			}
			var paramData = checkParameter();
			callApiEvent(paramData);
			$('.potSizeWrap').hide();
		});
	}

	function addSelectedClassEvent(target) {
		if (target.hasClass('selected')) {
			target.removeClass('selected');
		} else {
			target.addClass('selected');
			
		}
	}
	
	function changeSelectedImgEvent(category) {
		var categoryVal = category.attr('category');
		var btnImg = category.find('img');
		var imgName = "";
		
		if (category.hasClass('selected')) {
			if (categoryVal == "all") {
				$(".filterBtn").eq(1).removeClass("disabledbutton");
				$(".filterBtn").eq(2).removeClass("disabledbutton");
				$('.categoryBtn').eq(0).find('img').attr('src','/app/images/category/category_all_selected.gif');
				$('.categoryBtn').eq(0).find('.filterFontS').css('font-weight','1000');
				
				var imgNameArr = ['category_todayflower.png','category_flowerpot.png','category_timesale.png'];
				for (var i=1; i<=3; i++) {
					var categoryBtn = $('.categoryBtn').eq(i);
					categoryBtn.find('img').attr('src','/app/images/category/' + imgNameArr[(i-1)]);
					categoryBtn.find('.filterFontS').css('font-weight','400');
				}
			} else {
				switch (categoryVal) {
					case "TS":
						imgName = "category_timesale_selected.gif";
					break;
					case "FP":
						imgName = "category_flowerpot_selected.gif";
					break;
					case "TF":
						imgName = "category_todayflower_selected.gif";
					break;
				}
				
				btnImg.attr('src','/app/images/category/' + imgName);
				category.find('.filterFontS').css('font-weight','1000');
				
				$('.categoryBtn').eq(0).find('img').attr('src','/app/images/category/category_all.png');
				$('.categoryBtn').eq(0).find('.filterFontS').css('font-weight','400');
			}
		} else {
			switch (categoryVal) {
				case "all":
					imgName = "category_all.png";
					$(".filterBtn").eq(1).removeClass("disabledbutton");
					$(".filterBtn").eq(2).removeClass("disabledbutton");
				break;
				case "TS":
					imgName = "category_timesale.png";
				break;
				case "FP":
					imgName = "category_flowerpot.png";
					$(".filterBtn").eq(2).addClass("disabledbutton");
				break;
				case "TF":
					imgName = "category_todayflower.png";
					$(".filterBtn").eq(1).addClass("disabledbutton");
					$(".filterBtn").eq(2).removeClass("disabledbutton");
				break;
			}
			
			category.find('.filterFontS').css('font-weight','400');
			btnImg.attr('src','/app/images/category/' + imgName);
		}
	}

	function removeCheckedClassEvent() {
		if (!$('.filterBtn').eq(1).hasClass('checked')) {
			$('.prdTypeBtn').removeClass('selected');
		}
	}
	
	function setPrdPriceCheckEvent() {
		var filterBtn = $('.filterBtn').eq(0);
		if (!filterBtn.hasClass('checked')) {
			filterBtn.removeClass('selected');
		}
	}
	
	function setPrdTypeCheckEvent() {
		var filterBtn = $('.filterBtn').eq(1);
		var selectedPrdType = $('#selectedPrdType').val();
		if (!selectedPrdType.length > 0) {
			filterBtn.removeClass('selected');
		}
	}
	
	function checkParameter(){
		var distanceCheck =false;
		var priceCheck =false;
		var typeCheck =false;
		var potCheck =false;
		var sortCheck =false;
		
		var categoryCnt  = $('.categoryBtn').length;
		var categoryParam = "";
		var tempParam="";
		
		for(var i = 0; i < categoryCnt; i++){ 
			if($('.categoryBtn').eq(i).hasClass('selected')){
				categoryParam += $('.categoryBtn').eq(i).attr('category') + "," ;
			}
		}
		if (categoryParam.length > 0) {
			categoryParam = categoryParam.slice(0, -1);
		} else {
			categoryParam = "all";
		}
		var sortVal = $('.sortDropText').attr('sort');
		if(sortVal.length > 0){
			sortCheck =true;
		}
		var pointx = "<?=$pointx?>";
		var pointy = "<?=$pointy?>";
		if (pointx.length == 0) {
			pointx = window.nativeObj.coordinate.longitude;
		}
		if (pointy.length == 0) {
			pointy = window.nativeObj.coordinate.latitude;
		}
		if (pointx.length == 0 || pointx == 0 || pointy.length == 0 || pointy == 0) {
			alert('GPS 좌표 수신중 문제가 발생했습니다. 좌표 설정을 확인해주세요');
		}
		var min_price = 0;
		var max_price = 0;
		if ($('.filterBtn').eq(0).hasClass('checked')) {
			var min_price = $('#minprice').val();
			var max_price = $('#maxprice').val();
			priceCheck = true;
		}
		
		var prdTypeCnt = $('.prdTypeBtn').length;
		var prdTypeParam = "";
		for(var i = 0; i < prdTypeCnt; i++){ 
			if($('.prdTypeBtn').eq(i).hasClass('selected')){
				prdTypeParam += $('.prdTypeBtn').eq(i).attr('prdType') +"," ;
			}
		}
		
		var potSizeCnt = $('.potSizeBtn').length;
		var potSzieParam = "";
		
		for(var i = 0; i < potSizeCnt; i++){ 
			if($('.potSizeBtn').eq(i).hasClass('selected')){
				potSzieParam += $('.potSizeBtn').eq(i).attr('potSize') +"," ;
			}
		}
		
		if (categoryParam.includes("TS") || categoryParam.includes("TF")) {
			if(prdTypeParam.length > 0){
				typeCheck = true;
			}
			prdTypeParam = prdTypeParam.slice(0, -1);
		}
		
		if (categoryParam.includes("FP")) {
			if(potSzieParam.length > 0){
				potCheck = true;
			}
			potSzieParam = potSzieParam.slice(0, -1);
		}

		if (categoryParam == "all") {
			if(prdTypeParam.length > 0){
				typeCheck = true;
			}
			prdTypeParam = prdTypeParam.slice(0, -1);
			
			if(potSzieParam.length > 0){
				potCheck = true;
			}
			potSzieParam = potSzieParam.slice(0, -1);
		}
		
		if(distanceCheck){
			tempParam += "&distance_flg="+ distance_flg;
		}
		if(sortCheck){
			tempParam += "&sortType="+ sortVal;
		}
		if(priceCheck){
			tempParam += "&min_price=" + min_price+ "&max_price=" + max_price;
		}
		if(typeCheck){
			tempParam += "&productType=" + prdTypeParam;
		}
		if(potCheck){
			tempParam += "&flowerPotSize=" + potSzieParam;
		}
		
		var paramData ="search_flg="+ categoryParam + "&mb_addr_point_x=" + pointx + "&mb_addr_point_y=" + pointy + tempParam;
		console.log(paramData);
		return paramData;
	}
<?}?>

<?if (strlen($listType) > 0) {?>
	function callApiEvent(paramData) {
		<?if ($listType == "list") {?>
			$('.product_list_loader').show();
			$('.product_list').remove();
			callOrderNowEvent(paramData);
		<?} else {?>
			initMap(paramData);
		<?}?>
	}
<?}?>
</script>
<style>
.sortArrWhite{
	filter: invert(96%) sepia(0%) saturate(7484%) hue-rotate(16deg) brightness(101%) contrast(108%);
}
.disabledbutton {pointer-events: none;opacity: 0.4;}
.filter_wrapper{padding:10px;overflow:hidden;}
.filter {color:#231815;line-height:20px;border:1px solid #cccccc;border-radius:14px;width:22%;height:25px;float:left;font-size:0.5rem;   background: url(/app/skin/basic/images/select_arrow.gif) no-repeat 90% 50%;-webkit-appearance: none;-moz-appearance: none;appearance: none;padding:0 20px;	}
.d_f_J{display: flex;justify-content: space-around;}
.selectFilterWrap{border-bottom: 1px  solid #9e9e9e36;}
.selectFilterGruop{padding: 15px;}
.filterRound{color: #cccccc;text-align: center;border-radius:40px;border:2px solid #ffffff;}
.filterFontS{font-size: 1.1em;color: #282828;}
.detailFilterWrap{border-bottom: 1px  solid #9e9e9e36;}
.detailFilterGruop{padding: 20px;}
.filterList{border-radius: 13px;font-size: 0.8rem;border: 1px  solid #c8c8c8;color:#282828;font-weight: 500;text-align:center;width: 90px;}
.checked{background-color: #464646; color: #ffffff;}


.subFilterList{border-radius: 20px;font-size: 1em;border: 1px  solid #c8c8c8;color:#282828;font-weight: 500;text-align: center;width: 90px;}

.detailFilterWrap .selected{border: 1px  solid #231815;background-color: #ffffff; color: #231815;}
.shapeWrap .selected{background-color: #464646;color:#ffffff;border: 1px  solid #464646;}
.potSizeWrap .selected{background-color: #464646;color:#ffffff;border: 1px  solid #464646;}
.filterFontD{padding: 5px; font-size: 1.0em;}
.shapeWrap{padding: 10px;}
.shapeGroup{margin-bottom: 10px;}
.applyBtn{border: 1px solid #282828;border-radius: 20px;width: 50%;color:#282828;font-size: 1.1em;text-align: center;}
.priceWrap{padding: 20px;}
.priceGroup{margin: 10px 30px 10px 10px;}
.priceList{font-size: 1.2em;color:#282828;text-align: center;font-weight: 500;margin: 10px 20px 20px 30px;}
.floatBg{float: left;position: absolute;width: 95%;border-bottom: 1px solid;background-color: #ffffff;z-index: 99;}

/*스와이프 css*/ 
.slider-wrapper {width:100%;display: inline-block;position: relative;font-family: arial;}
.ui-widget.ui-widget-content{padding: 2px; border-radius: 20px;}
.ui-widget-header{background-color: #464646;}
.ui-slider-handle.ui-corner-all.ui-state-default{background-color: #ffffff;width: 7%; font-size: 18px; border-radius: 50%;border:solid 1px #e5e5e5;}
.ui-slider-horizontal .ui-slider-handle{margin-left:-0.8em;}
.ui-slider .ui-slider-handle{width: 1em;}
.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active, a.ui-button:active, .ui-button:active, .ui-button.ui-state-active:hover{background:#464646!important;font-weight:normal;color:#ffffff!important;width: 7%;}
</style>
<div class="h_area2" style="position: fixed;top:constant(safe-area-inset-top) + 60px; top:calc(env(safe-area-inset-top) + 60px);width: 100% ;z-index: 50; margin-top: 5px;padding-top: 10px;">
	<h2>바로 구매</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
	<a style="margin-top: 10px;" class="btn_prev" rel="external"><span>이전</span></a>
</div>
<?if ($listType == "list") {?>
<div class="selectFilterWrap" style="margin-top:50px;">
	<div class="selectFilterGruop d_f_J">
		<div style="width:75px;height:75px;" class="filterRound categoryBtn" category="all">
			<img style="padding-top:2px;margin-left:5px;width:45px;height:45px;" src="/app/images/category/category_all_selected.gif" alt="">
			<div class="filterFontS" style="font-weight:1000;">모든꽃</div>
		</div>
		<div style="width:75px;height:75px;" class="filterRound categoryBtn" category="TF">
			<img style="padding-top:4px;width:45px;height:45px;" src="/app/images/category/category_todayflower.png" alt="">
			<div class="filterFontS" style="margin-top:-2px;">꽃</div>
		</div>
		<div style="width:75px;height:75px;" class="filterRound categoryBtn" category="FP">
			<img style="padding-top:2px;width:45px;height:45px;" src="/app/images/category/category_flowerpot.png" alt="">
			<div class="filterFontS" style="margin-top: 0px;">화분</div>
		</div>
		<div style="width:75px;height:75px;" class="filterRound categoryBtn" category="TS">
			<img  style="padding-top:3px;margin-left:10px;width:45px;height:45px;" src="/app/images/category/category_timesale.png" alt="">
			<div class="filterFontS" style="margin-top:-1px;">마감할인</div>
		</div>
	</div>
</div>
<div class="detailFilterWrap">
	<div class="detailFilterGruop d_f_J">
		<div class="filterList resetBtn" style="display: flex; justify-content: center;width:30px;border-radius: 50%;">
			<img style="width: 10px;" src="/app/skin/basic/svg/spinArrow.svg" alt="">
		</div>
		<div class="filterList sortDropdown">
			<div class="sortDropbtn">
				<span class="sortDropText" style="margin-left: 6px;" sort="1">짧은 거리순</span>
				<img class="sortArrowImg" style="width:10px;" src="/app_beta/skin/basic/svg/timesale_product_underArrow.svg" alt="">
			</div>
			<div class="sortDropdown-content" style="display: none;">
			<?
			$shape_sql = "SELECT keyText, valText FROM item_mst
							where keyText ='sortType'";
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
				if($i == 0){
				?>
					<div style="border:0px;" class="sortBtn" sort="<?=$i?>" sortType="<?=$shapename[$i]['valText']?>"><?=$shapename[$i]['valText']?>순</div>
				<?
				}else{
				?>
					<div class="sortBtn" sort="<?=$i?>" sortType="<?=$shapename[$i]['valText']?>"><?=$shapename[$i]['valText']?>순</div>
				<?
				}
			}
			?>
			</div>
		</div> 
		<div class="filterList filterBtn" filter="prdPrice" style="width:67px;font-size: 1.0em;">
			<div class="filterFontD">꽃 가격</div>
		</div>
		<div class="filterList filterBtn" filter="prdType" style="width:67px;font-size: 1.0em;">
			<div class="filterFontD">꽃 형태</div>
		</div>
		<div class="filterList filterBtn" filter="potSize" style="width:67px;font-size: 1.0em;">
			<div class="filterFontD">화분 크기</div>
		</div>
	</div>
</div>
<?}?>
<form name="priceform" action="<?echo $_SERVER['PHP_SELF'];?>" method="post">
	<input type=hidden name="mode" value="price">
	<div class="priceWrap floatBg subFilter" style="display: none;width: calc(100vw - 40px);">
		<div class="priceGroup">
			<div id="slider-range-max"></div>
			<input type="hidden" id="minprice" name="minprice" value="0">
			<input type="hidden" id="maxprice" name="maxprice" value="70000">
			<input type="hidden" id="tempmin" name="tempmin" value="0">
			<input type="hidden" id="tempmax" name="tempmax" value="70000">
		</div>
		<div class="priceList d_f_J">
			<div></div><div>3만원</div><div>5만원</div><div>7만원</div><div>10만원</div><div>15만원</div><div>20만원</div><div>최대</div>
		</div>
		<div style="display: flex;justify-content: end;">
			<div style="margin-right: 25px; width:39%;" class="prdPriceApplyBtn applyBtn"><div class="filterFontD">적용하기</div></div>
		</div>
	</div>
</form>

<div class="shapeWrap floatBg subFilter" style="display: none;">
	<div class="shapeGroup">
		<div>
			<div class="subFilterList prdTypeBtn" prdType="all" style="float: left;margin-left:5px;margin-top:10px;"><div class="filterFontD">전체보기</div></div>
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
			<div class="subFilterList prdTypeBtn" prdType="<?=$shapename[$i]['valText']?>" style="float: left; margin-left:5px;margin-top:10px;">
				<div class="filterFontD"><?=$shapename[$i]['valText']?></div>
			</div>
		<?	
	}
	?>
			<input id="selectedPrdType" type="hidden" value="">
			<input id="selectedPotSize" type="hidden" value="">
		</div>
	</div>
	<div style="float:right; margin:15px 15px 0 15px; width:39%;" id="shapeApply" class="prdTypeApplyBtn applyBtn filterFontD">적용하기</div>
</div>

<div class="potSizeWrap floatBg subFilter" style="display: none;">
	<div class="shapeGroup">
		<div>
			<div class="subFilterList potSizeBtn" potSize="all" style="float: left;margin-left:25px;margin-top:10px; width:40%;"><div class="filterFontD">전체보기</div></div>
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
	mysql_free_result($shape_result);
	$num = 0;
	for($i = $num; $i < count($shapename); $i++){
		?>
			<div class="subFilterList potSizeBtn" potSize="<?=$shapename[$i]['val01']?>" style="float: left; margin-left:25px;margin-top:10px;width:40%;">
				<div class="filterFontD"><?=$shapename[$i]['valText']?></div>
			</div>
		<?	
	}
	?>
			<input id="selectedPotSize" type="hidden" value="">
		</div>
	</div>
	<div style="float:right; margin:15px 25px 15px 15px; width:37%;" id="potSizeApply" class="potSizeApplyBtn applyBtn filterFontD">적용하기</div>
</div>

<div class="mapHeightObject"></div>

<div class="msTimeSaleProduct">
	<div class="timesale_menu">
		<ul class="swiper-wrapper">
			<li class="swiper-slide" id="listBtn"><a href="javascript:callTimesale('list')" rel="external">사진으로 보기</a></li>
			<li class="swiper-slide" id="mapBtn"><a href="javascript:callTimesale('map')" rel="external">지도로 보기</a></li>
		</ul>
	</div>
	<?if($listType == "list"){
	?>
		<div id="timesale_list">
			<? include $skinPATH."productlist_cp_time.php"; ?>
		</div>
	<?}
	if($listType == "map"){
	?>
		<div id="timesale_map" style="position:relative;">
			<? include $skinPATH."productlist_cp_time_map.php"; ?>
		</div>
	<?}?>
</div>

<? include_once('footer.php'); ?>

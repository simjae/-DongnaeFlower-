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
	.timesale_menu{box-sizing:border-box;overflow:hidden;}
	.timesale_menu li{width:50%;text-align:center;font-size: 15px;}
	.timesale_menu li a{display:block;padding:12px 5px;}
	.timesale_menu li.selected{border-bottom:3px solid #242424;box-sizing:border-box; font-weight: 600;color: black;}
	.product_category{background: #8bc570;padding: 2px 5px;color: #655735;border: 1px solid #655735;z-index: 10;border-radius: 14px;font-weight: bold;line-height: 14px;font-size: 13px;position: absolute;top: 10px;left: 10px;}
	.product_productTypeText{background: #f1926e;padding: 2px 5px;color: #655735;border: 1px solid #655735;z-index: 10;border-radius: 14px;font-weight: bold;line-height: 14px;font-size: 13px;position: absolute;top: 10px;left: 10px;}
	.productTimeWrap{background: #f2f2f2;border-radius: 20px;width: 70%;height: 20px;}
</style>
<script>
	
	var swiper = new Swiper('.mypage_menu', {
		slidesPerView: 'auto',
		spaceBetween: 10,
		freeMode: true
	});
	var settings = {
							loop: true,
							pagination: {
								el: '.swiper-page-num',
								type: 'fraction'
							}
						}
		
	$(document).ready(function() {
		// var paramData = "search_flg=all" 
		// callOrderNowEvent(paramData);
		console.log(<?=$pointx?>);
		console.log(<?=$pointy?>);
		setsubFilterListCheckEvent();
		$( function() {
			$( "#slider-range-max" ).slider({
				range: true,
				min: 0,
				max: 7,
				values: [ 3, 4 ],
				slide: function( event, ui ) {
					var price =[0, 30000, 50000,70000,100000, 150000, 200000 , 500000];
					var minprice = ui.values[0];
					var maxprice = ui.values[1];
					$('#minprice').val(price[minprice]);
					$('#maxprice').val(price[maxprice]);

				}	
			});
			$( "#amount" ).val( "$" + $( "#slider-range-max" ).slider( "values", 0 ) +
			" - $" + $( "#slider-range-max" ).slider( "values", 1 ) );
		} );

		setfilterRoundSelectEvent();
		menuSelecter('<?=$listType?>');
		
		$('#resetBtn').click( function () {
			$('.subFilter').hide();
			$('.filterList').removeClass('selected');
			$('.filterList').css('border','1px solid #9e9e9e36');
			var min_price = $('#minprice').val(50000);
			var max_price = $('#maxprice').val(70000);
			$('.subFilterList').removeClass('selected');
			var paramData = checkParameter();
			callOrderNowEvent(paramData);
		}); 
		$('#nearBtn').click( function () {
			$('.subFilter').hide();
			if(!$(this).hasClass('selected')){
				$(this).addClass('selected');
			}else{
				$(this).removeClass('selected');
			}
			if(!$('#shapeBtn').hasClass('selected')){
				$('#shapeBtn').css('border','1px solid #9e9e9e36');
				$('.subFilterList').removeClass('selected');
			}
			if(!$('#priceBtn').hasClass('selected')){
				$('#priceBtn').css('border','1px solid #9e9e9e36');
				var min_price = $('#minprice').val(50000);
				var max_price = $('#maxprice').val(70000);
			}
		}); 
		$('#priceBtn').click( function () {
			if(!$(this).hasClass('selected')){
				$('#priceBtn').css('border','1px solid #231815');
			}
			if(!$('#shapeBtn').hasClass('selected')){
				$('#shapeBtn').css('border','1px solid #9e9e9e36');
				$('.subFilterList').removeClass('selected');
			}
			
			$('.subFilter').hide();
			$('.priceWrap').show();
		}); 
		$('#shapeBtn').click( function () {
			if(!$(this).hasClass('selected')){
				$('#shapeBtn').css('border','1px solid #231815');
				$('.subFilterList').removeClass('selected');
			}
			if(!$('#priceBtn').hasClass('selected')){
				$('#priceBtn').css('border','1px solid #9e9e9e36');
				var min_price = $('#minprice').val(50000);
				var max_price = $('#maxprice').val(70000);
			}
			$('.subFilter').hide();
			$('.shapeWrap').show();
		}); 
	});
	function checkSubCategoryEvent(){
		var subCategory = $('.filterList');
		for(var i = 0; i < subCategory.length; i++){
			if(subCategory.eq(i).hasClass('selected')){
				subCategory.eq(i).removeClass('unChecked');
				subCategory.eq(i).addClass('checked');
			}
		}
	}

	function menuSelecter(type){
		if(type == "list"){
			$("#listBtn").addClass("selected");
		}
		else if(type == "map"){
			initMap();
			$("#mapBtn").addClass("selected");
		}
	}
	function setFilterChangeEvent() {
		$('.filter').change(function() {
			$(this).css({'border':'2px solid #e61e6e'});
			$(this).css({'color':'#e61e6e'});
			
			var distanceFilter = $("#distanceFilter option:selected").val();
			var productTypeFilter = $("#productTypeFilter option:selected").val();
			var priceFilter = $("#priceFilter option:selected").val();
			$('#dfVal').val(distanceFilter);
			$('#ptfVal').val(productTypeFilter);
			$('#pfVal').val(priceFilter);
			
			document.filterForm.submit;
		});
	}
	function setfilterRoundSelectEvent(){
		//All,Ts,fp,tf카테고리 selected추가 funtion
		$('.filterRound').click(function(){
			var all_check = false;
			if ($('#category_all').hasClass('selected')) {
				all_check = true;
			}
			
			if($(this).hasClass("selected")){
				if($(this).hasClass("all")) {
					$('.filterRound').not(this).removeClass('selected');
				}else{
					$(this).removeClass( 'selected' );
					// $(this).not(hasClass('all')).removeClass('selected');
				}
			}else{
				if($(this).hasClass("all")){
					$(this).addClass("selected");
					$('.filterRound').not(this).removeClass('selected');
				} else {
					if (all_check) {
						$('#category_all').removeClass('selected');
					}
					$(this).addClass("selected");
				}
			}
			if(!$('#priceBtn').hasClass('selected')){
				$('#priceBtn').css('border','1px solid #9e9e9e36');
				$('#minprice').val(null);
				$('#maxprice').val(null);
			}
			if(!$('#shapeBtn').hasClass('selected')){
				$('#shapeBtn').css('border','1px solid #9e9e9e36');
				$('.subFilterList').removeClass('selected');
			}
			$('.subFilter').hide();
			$('.priceWrap').hide();
			var paramData = checkParameter();
			callOrderNowEvent(paramData);
		});
	}
	function setsubFilterListCheckEvent(){
		//성공
		$('.subFilterList').click(function(){
			if($(this).attr('productType') == 'all'){
				if($(this).hasClass("selected")){
					$(this).removeClass('selected');
				}else{
					$(this).addClass("selected");
				}
				$('.subFilterList').not(this).removeClass('selected');
			}else{
				if($(this).hasClass("selected")){
					$(this).removeClass('selected');
				}	
				else{
					$(this).addClass('selected');
				}
				$('.subFilterList[productType = all]').removeClass('selected');
			} 
		});
	}

	function applyBtnEvent(tagId){
		//적용하기버튼 function
		var tagBtn = tagId.id;
		if(tagBtn == "priceApply"){
			var min_price = $('#minprice').val();
			var max_price = $('#maxprice').val();
			if(0 <= min_price && 0 < max_price){
				$('#priceBtn').addClass("selected");
				var paramData = checkParameter();
				console.log("가격적용"+paramData);
				callOrderNowEvent(paramData);
				$('.priceWrap').hide();
			}else{
				alert('범위를 다시 선택해주세요.');
			}
		}else if(tagBtn == "shapeApply"){
			if(!$('subFilterList').attr(productType)==""){
				$('#shapeBtn').addClass("selected");
				var paramData = checkParameter();
				callOrderNowEvent(paramData);
				$('.shapeWrap').hide();
			}else{
			alert('1개이상 선택해주세요.');
			}
		}
	}
	
	function checkParameter(){
		//모든 selected 가져오는 function
		var category_cnt  = $('.filterRound').length;
		var filterRoundStr = "";
		var min_price = $('#minprice').val();
		var max_price = $('#maxprice').val();
		console.log("min"+min_price);
		console.log("max"+max_price);
		var distance_flg = "";
		for(var i = 0; i < category_cnt; i++){ 
			if($('.filterRound').eq(i).hasClass('selected')){
				filterRoundStr += $('.filterRound').eq(i).attr('category_val') + "," ;
			}
		}
		filterRoundStr = filterRoundStr.slice(0, -1);
	
		var subCategory_cnt = $('.filterList').length;
		var subCategoryStr ="";
		for(var i = 0; i < subCategory_cnt; i++){ 
			if($('.filterList').eq(i).hasClass('selected')){
				subCategoryStr += $('.filterList').eq(i).attr('subCategory') + "," ;
			}
		}
		subCategoryStr = subCategoryStr.slice(0, -1);
	
		var productType_cnt = $('.subFilterList').length;
		var productTypeStr = "";
		for(var i = 0; i < productType_cnt; i++){ 
			if($('.subFilterList').eq(i).hasClass('selected')){
				productTypeStr += $('.subFilterList').eq(i).attr('productType') +"," ;
			}
		}
		productTypeStr = productTypeStr.slice(0, -1);
		var paramData ="search_flg="+ filterRoundStr +"&distance_flg="+distance_flg+ "&min_price=" + min_price+ "&max_price=" + max_price + "&productType=" + productTypeStr;
		console.log(paramData);
		return paramData;
	}





function callOrderNowEvent(paramData) {
	$.ajax({
		url: "/api/order_now.php",
		type: "get",
		data: paramData,
		dataType: "json",
	}).done(function(data) {
		if (data.length > 0) {
			for (var i=0;i<data.length;i++) {
				var pridx = data[i].pridx;
				var category = data[i].category;
				var productTypeText = data[i].productTypeText;
				var productname = data[i].productname;
				var sellprice = data[i].sellprice;
				var consumerprice = data[i].consumerprice;
				var discount = data[i].discount;
				var distance = data[i].distance;
				var productImg = data[i].product_img;
			}
			orderNowSplitEvent(data);	

		}else{
			alert('조회결과가 없습니다.')
		}
	});
	}
	
	function orderNowSplitEvent(data){
		console.log(data);
		$('.product_list').remove();
		for(var i = 0; i < data.length; i++){
			productAppendEvent(data[i],i);
		}
		
	}
	
	function productAppendEvent(data,num){

		var strDiv="";
		var category = data.category;
		var  backgroundUrl = "/data/shopimages/product/";
		var imgArray = data.product_img.split(",");
		strDiv +=	"<ul class='product_list' style='width:45%;float: left;margin-left: 13px;margin-top: 10px;'>";
		strDiv +=		"<li class='product_item remainTimeBox'setstamp='dcsm_"+num+"' endstamp='" +data.end+"'>";
		strDiv +=			"<div class='product_view' style='border-radius: 10px;'>";
		strDiv +=				"<div class='product_img'>";
		strDiv +=					"<div class='productImage' style='height:100%;''>";
		strDiv +=						"<div class='swiper-wrapper'>";
		for(var i = 0; i < imgArray.length; i++){
		strDiv +=							"<div class='swiper-slide' style='width:100%;height: 200px;min-height:100px;border:none;padding:0px;background:url(/data/shopimages/product/" + imgArray[i]+") no-repeat;background-position:center;background-size:cover;'></div>";
		}
		strDiv +=						"</div>";
		strDiv +=						"<div class='swiperPageWrap' style='bottom:10px;'>";
		strDiv +=							"<div class='swiper-page-num'></div>";
		strDiv +=						"</div>";
		strDiv +=					"</div>";
		switch(category){
			case 'TS' : 
				if(data.discount>0){
					strDiv +=		"<div class='product_sale' style='background:#fbe17f;padding: 2px 5px;color:#655735;border: 1px solid #655735;'>-"+data.discount+"%</div>";
				} 
			break;
			case 'FP':
				strDiv +=			"<div class='product_category'>화분</div>";
			break;
			case 'TF':
				strDiv +=		"<div class='product_productTypeText'>"+data.productTypeText+"</div>";
			break;				
		}
		strDiv +=				"</div>";
		strDiv +=				"<div class='shopInfo'>";
		strDiv +=					"<div class='shop_title'>" + data.productname +"</div> ";
		strDiv +=					"<div class='shop_content'>";
		strDiv +=						"<img src='/app/skin/basic/svg/review_star_on.svg' style='height:16px;' alt='star'>";
		strDiv +=						"<span class='count1'><?=$avg_marks?></span>";
		strDiv +=						"<span class='count2'>(<?=$marks_count?>)</span>";
		strDiv +=					"</div>";
		strDiv +=					"<div class='btnWrap'>";
		strDiv +=						"<div class='distBtn black' value='나와의 거리Km'>"+data.distance+"KM";
		strDiv +=						"</div>";
		strDiv +=						"<div class='distBtn white' value='상세 정보 보기'>";
		strDiv +=						"</div>";
		strDiv +=					"</div>";
		strDiv +=				"</div>";
		strDiv +=			"</div>";
		strDiv +=			"<div class='product_info'>";
		strDiv +=				"<div class='prodContentWrap'>";
		strDiv +=					"<div class='prodName' style ='font-size:1.0em;'>"+ data.com_name +"</div>";
		strDiv +=					"<div class='prodName' style ='font-size:1.0em;color: #424242;'>"+ data.productname +"</div>";
		strDiv +=				"</div>";
		strDiv +=				"<div class='priceText' style='margin-top: 0px;padding:0px 7px;'><span style='font-size:1.0em;font-weight: 800;margin-right: 10px;color:#424242'>"+data.sellprice+"원</span><span style='font-size:1.0em;color: ##03031d;font-weight: 300; text-decoration: line-through;'>"+data.consumerprice+"원</span></div>";
		strDiv +=				"<div class='productTimeWrap'>";
		strDiv +=					"<div class='prodContentWrap'>";
		strDiv +=						"<div class='timeimg' id='dcsm_"+num+"' style='padding:2px 0px 20px 28px;background-position: 0px 4px;'></div>";
		strDiv +=					"</div>";
		strDiv +=				"</div>";
		strDiv +=			"</div>";
		strDiv +=		"</li>";
		strDiv +=	"</ul>";
		strDiv +="</div>";
		$('.product_a').unbind();
		$('.product_a').append(strDiv);
		var timeSaleImageSwiper = new Swiper('.productImage', settings);
		intCountdown();
	}


	
	
	

//--------------------------------------------------//--
	
	
	
		

</script>
<style>
.filter_wrapper{
	padding:10px;
	overflow:hidden;
}
.filter {
	color:#231815;
	line-height:20px;
	/*
	text-align:center;
	*/	
	border:1px solid #cccccc;
	border-radius:14px;
	width:22%;
	height:25px;
	float:left;
	font-size:0.5rem;   
	background: url(/app/skin/basic/images/select_arrow.gif) no-repeat 90% 50%;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding:0 20px;
	
}
.d_f_J{
	display: flex;
    justify-content: space-around;
}
.selectFilterWrap{border-bottom: 1px  solid #9e9e9e36;}
.selectFilterGruop{
	padding: 15px;
}
.filterRound{
	color: #cccccc;
	text-align: center;
}
.filterFontS{
	margin-top: 5px;
	font-size: 1.1em;
	color: #242424;
}
.detailFilterWrap{
	border-bottom: 1px  solid #9e9e9e36;
}
.detailFilterGruop{
	padding: 20px;
}
.filterList{
	border-radius: 20px;
	font-size: 1em;
	border: 1px  solid #9e9e9e36;
	font-weight: 500;
	text-align:center;
	width: 90px;
}
.checked{background-color: #242424; color: #ffffff;}
.unChecked{color: black; background-color: #ffffff;}

.subFilterList{
	border-radius: 20px;
	font-size: 1em;
	border: 1px  solid #9e9e9e36;
	font-weight: 500;
	text-align: center;
	width: 90px;
}
.detailFilterWrap .selected{border: 1px  solid #231815;background-color: #231815; color: #ffffff;}
.shapeWrap .selected{border: 1px  solid #231815;}
.filterFontD{padding: 5px;font-size: 1.1em;}
.shapeWrap{padding: 10px;}
.shapeGroup{margin-bottom: 10px;}
.applyBtn{border: 1px solid #231815;
	border-radius: 20px;
	width: 50%;
	color:#242424;
	font-size: 1.1em;
	text-align: center;}
.priceWrap{padding: 20px;}
.priceGroup{margin: 10px 30px 10px 10px;}
.priceList{font-size: 1.2em;color:#242424;text-align: center;font-weight: 500;margin: 10px 20px 20px 30px;}
.floatBg{float: left;position: absolute;width: 95%;border-bottom: 1px solid;background-color: #ffffff;z-index: 99;}


.slider-wrapper {width:100%;display: inline-block;position: relative;font-family: arial;}
.ui-widget.ui-widget-content{padding: 2px; border-radius: 20px;}
.ui-widget-header{background-color: #242424;}
.ui-slider-handle.ui-corner-all.ui-state-default{background-color: #ffffff; font-size: 18px; border-radius: 50%;border:solid 1px #e5e5e5;}
.ui-slider-horizontal .ui-slider-handle{margin-left:-0.8em;}
.ui-slider .ui-slider-handle{width: 1em;}




</style>
<div class="h_area2">
	<h2>마감 할인</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
	<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
</div>
<div class="selectFilterWrap">
	<div class="selectFilterGruop d_f_J">
		<div style="padding-top: 15px;" class="filterRound all" id="category_all" category_val="all">
			<img style="width:40px;" src="/app/skin/basic/svg/allFlower.svg" alt="">
			<div class="filterFontS">모든꽃</div>
		</div>
		<div style="padding-top: 8px;" class="filterRound ts" id="timeSaleBtn" category_val="TS">
			<img  style="width:45px;" src="/app/skin/basic/svg/timeSale.svg" alt="">
			<div class="filterFontS">마감할인</div>
		</div>
		<div class="filterRound fp" id="potBtn" category_val="FP">
			<img style="width:40px;" src="/app/skin/basic/svg/pot.svg" alt="">
			<div class="filterFontS">화분</div>
		</div>
		<div class="filterRound tf" id="todayBtn" category_val="TF">
			<img style="width:45px; padding-top:3px;" src="/app/skin/basic/svg/todayFlower.svg" alt="">
			<div class="filterFontS">오늘의꽃</div>
		</div>
	</div>
</div>
<div class="detailFilterWrap">
	<div class="detailFilterGruop d_f_J">
		<div id="resetBtn" class="filterList" subCategory="reset" style="display: flex; justify-content: center;">
			<img style="width: 10px;" src="/app/skin/basic/svg/spinArrow.svg" alt="">
			<div class="filterFontD" >초기화</div>
		</div>
		<div id="nearBtn" class="filterList" subCategory="distance">
			<div class="filterFontD">가까운 거리 순</div>
		</div>
		<div id="priceBtn" class="filterList"subCategory="price" >
			<div class="filterFontD">꽃 가격</div>
		</div>
		<div id="shapeBtn" class="filterList" subCategory="shape">
			<div class="filterFontD">꽃 형태</div>
		</div>
	</div>
</div>
<form name="priceform" action="<?echo $_SERVER['PHP_SELF'];?>" method="post">
	<input type=hidden name="mode" value="price">
	<div class="priceWrap floatBg subFilter" style="display: none;">
		<div class="priceGroup">
			<div id="slider-range-max"></div>
			<input type="hidden" id="minprice" name="minprice" value="50000">
			<input type="hidden" id="maxprice" name="maxprice" value="70000">
		</div>
		<div class="priceList d_f_J">
			<div></div><div>3만원</div><div>5만원</div><div>7만원</div><div>10만원</div><div>15만원</div><div>20만원</div><div>최대</div>
		</div>
		<div style="display: flex;justify-content: end;">
			<div onclick="applyBtnEvent(this)" style="margin-right: 25px; width:39%;" id="priceApply" class="applyBtn"><div class="filterFontD">적용하기</div></div>
		</div>
	</div>
</form>

<div class="shapeWrap floatBg subFilter" style="display: none;">
	<div class="shapeGroup">
		<div>
			<div class="subFilterList" productType="all" style="float: left;margin-left:5px;margin-top:10px;"><div class="filterFontD">전체보기</div></div>
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
			<div class="subFilterList" productType="<?=$shapename[$i]['valText']?>" style="float: left; margin-left:5px;margin-top:10px;">
				<div class="filterFontD"><?=$shapename[$i]['valText']?></div>
			</div>
		<?	
	}
	?>
		</div>
	</div>
	<div onclick="applyBtnEvent(this)" style="float:right; margin:15px 15px 0 15px; width:39%;" id="shapeApply" class="applyBtn filterFontD">적용하기</div>
</div>

<div class="msTimeSaleProduct">
	<div class="timesale_menu">
		<ul class="swiper-wrapper">
			<li class="swiper-slide" id="listBtn"><a href="javascript:callTimesale2('list')" rel="external">사진</a></li>
			<li class="swiper-slide" id="mapBtn"><a href="javascript:callTimesale2('map')" rel="external">지도</a></li>
		</ul>
	</div>
	<?if($listType == "list"){
	?>
		<div id="timesale_list">
			<? include $skinPATH."productlist_cp_time_beta.php"; ?>
		</div>
	<?}
	if($listType == "map"){
	?>
		<div id="timesale_map">
			<? include $skinPATH."productlist_cp_time_map_beta.php"; ?>
		</div>
	<?}?>
</div>






<? include_once('footer.php'); ?>
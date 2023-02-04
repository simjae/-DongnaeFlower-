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
</style>
	
<script>
	var swiper = new Swiper('.mypage_menu', {
		slidesPerView: 'auto',
		spaceBetween: 10,
		freeMode: true
	});
	$(document).ready(function() {
		menuSelecter('<?=$listType?>');
	});
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
</style>
<div class="h_area2">
	<h2>마감 할인</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
	<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
</div>
<div class="msTimeSaleProduct">
	<div class="timesale_menu">
		<ul class="swiper-wrapper">
			<li class="swiper-slide" id="listBtn"><a href="javascript:callTimesale('list')" rel="external">사진</a></li>
			<li class="swiper-slide" id="mapBtn"><a href="javascript:callTimesale('map')" rel="external">지도</a></li>
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
		<div id="timesale_map">
			<? include $skinPATH."productlist_cp_time_map.php"; ?>
		</div>
	<?}?>
</div>

<? include_once('footer.php'); ?>
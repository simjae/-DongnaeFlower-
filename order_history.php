<?
include_once('header.php');

?>
<style>
	.order_history_menu{box-sizing:border-box;overflow:hidden;}
	.order_history_menu li{width:50%;text-align:center;}
	.order_history_menu li a{display:block;padding:12px 5px;font-size: 15px;}
	.order_history_menu li.selected{border-bottom:3px solid #242424;box-sizing:border-box;}
	.h_area2 h2 {
    display: block;
    background: #ffffff;
    text-align: center;
    font-size: 1.7em;
    padding:8px 12px 12px 12px;
    color: #000000;
    font-weight: bold;
}
.selected{
	color: black;
	font-weight: bold;
}
</style>
	
<script>
	var swiper = new Swiper('.order_history_menu', {
		slidesPerView: 'auto',
		spaceBetween: 10,
		freeMode: true
	});
	function menuSelecter(type){
		if(type == "sporder_history"){
			$("#timesale_history").hide();
			$("#sporder_history").show();
			$("#timesaleBtn").removeClass("selected");
			$("#sporderBtn").addClass("selected");
		}
		else if(type == "timesale_history"){
			$("#timesale_history").show();
			$("#sporder_history").hide();
			$("#listBtn").removeClass("selected");
			$("#sporderBtn").removeClass("selected");
			$("#timesaleBtn").addClass("selected");
		}
	}
</script>
<div class="h_area2">
	<h2>주문 내역</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
	<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
</div>
<div class="order_history_menu">
	<ul class="swiper-wrapper">
		<li class="swiper-slide selected" id="sporderBtn"><a href="javascript:menuSelecter('sporder_history')" rel="external">꽃집 제안</a></li>
		<li class="swiper-slide" id="timesaleBtn"><a href="javascript:menuSelecter('timesale_history')" rel="external">바로 구매</a></li>
	</ul>
</div>
<div id="sporder_history">
	<? include $skinPATH."proposalList.php"; ?>
</div>
<div id="timesale_history" style="display:none">
	<? include $skinPATH."timesale_history.php"; ?>
</div>
	

<? include_once('footer.php'); ?>
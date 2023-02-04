<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once("./inc/function.php");
if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:./login.php?chUrl=".getUrl());
	exit;
}

include "header.php";

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:./login.php?chUrl=".getUrl());
	exit;
}

$pageType = $_REQUEST["pageType"];
if($pageType == ""){
	$pageType = "couponDown";
}

?>
<script>
	$(document).ready(function() {
		menuSelecter('<?=$pageType?>');
	});
	function menuSelecter(type){
		if(type == "couponDown"){
			$("#myBtn").removeClass("selected");
			$("#downBtn").addClass("selected");
			$("#couponDown").show();
			$("#myCoupon").hide();
		}
		else if(type == "myCoupon"){
			$("#myBtn").addClass("selected");
			$("#downBtn").removeClass("selected");
			$("#myCoupon").show();
			$("#couponDown").hide();
		}
	}
</script>
<style>
	.coupon_menu{box-sizing:border-box;overflow:hidden;}
	.coupon_menu li{width:50%;text-align:center;font-size: 15px;}
	.coupon_menu li a{display:block;padding:12px 5px;}
	.coupon_menu li.selected{border-bottom:3px solid #242424;box-sizing:border-box; font-weight: 600;color: black;}
	#couponDown{display:none;}
	#myCoupon{display:none;}
	
</style>
<div id="content">
	<div class="h_area2">
		<h2>할인쿠폰</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<div class="coupon_menu">
		<ul class="swiper-wrapper">
			<li class="swiper-slide" id="downBtn"><a href="javascript:menuSelecter('couponDown')" rel="external">쿠폰 다운로드</a></li>
			<li class="swiper-slide" id="myBtn"><a href="javascript:menuSelecter('myCoupon')" rel="external">내 쿠폰 목록</a></li>
		</ul>
	</div>
	<div id="couponDown">
		<? include $skinPATH."mypage_coupon_down.php"; ?>
	</div>
	<div id="myCoupon">
		<? include $skinPATH."mypage_coupon.php"; ?>
	</div>
</div>
<? include "footer.php";?>

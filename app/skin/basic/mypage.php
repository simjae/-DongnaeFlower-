<?
	$profile_photo=($row->profile_photo?"background:url('/data/shopimages/member/".$row->profile_photo."') no-repeat;background-position:center;background-size:cover;":"");
?>

<style>
	#content{
		overflow: overlay;
	}
	.mypage_profile{position:relative;margin:0px;padding:20px 15px;color:blac;overflow:hidden;}
	.mypage_profile .btn_mypage_modify{position: absolute;top: 76px;right: 0px;padding: 4px 20px;border:1px solid #c0c1c1;border-radius: 5px;color: black;}
	.mypage_profile h4{margin:0;padding:0px 0px 17px 0px;font-size:23px;font-weight:bold; color: black;}
	.mypage_menu{margin:15px;box-sizing:border-box;overflow:hidden;}
	.mypage_menu li{width:auto; display: flex; font-weight: 500;margin: 20px; color: black; font-size: 20px padding-bottom: 15px;}
	.mypage_menu img{width:18px; margin-right: 15px;}
	.mypage_menu li a{display:block;padding:16px 7px;font-size: 18px;}
	.mypage_menu li.selected{border-bottom:3px solid #242424;box-sizing:border-box;}
	.mypage_meminfo{margin:10px 15px 40px 15px;padding:20px 25px;border:1px solid #eee;}
	.mypage_meminfo h4{margin:0;margin-bottom:5px;padding:0;color:#bbb;font-weight:normal;}
	.mypage_meminfo ul{overflow:hidden;}
	.mypage_meminfo li{float:left;width:45%;color:#242424;font-weight:bold;}
	.banner{width: 100%;}
	.arrow{width: 8px;padding-left: 13px;position: relative;top: 2px;left: 11px;}
	.banner img{width: 100%;}
	.banner h2{font-size: 1.4em;z-index:1;position:relative;top: 40px;left: 15px;color: #DC2872;}
	.mypage_point{font-size: 15px;color: black;font-weight: bold;margin:  15px 0 0 0;}
	.mypage_point #point{margin-right: 30px;}
	.mypage_point #price{color:#DC2872;margin-top:10px;}
	
</style>

<div id="content">
	<div class="mypage_profile" style="<?=$profile_photo?>">
		<div style="position:absolute;top:0px;left:0px;width:100%;height:100%;z-index:1; "></div>
		<div style="position:relative;z-index:2;padding:20px;"><h4><?=$_ShopInfo->memname?> 님</h4>
			<span style="font-size: 14px; color:#47403e"><?=($row->profile_photo?"":"오늘도 설레는 하루 보내세요.")?></span>
			<div class="mypage_point">
				<span id="point">적립금</span>
				<span id="price"><?=number_format($reserve)?></span><span>원</span>
			</div>
			<a href="mypage_usermodify.php" class="btn_mypage_modify">개인 정보 수정<img class="arrow" src="/app/skin/basic/svg/mypage8.svg"></a>
			
		</div>
	</div>
	<div class="banner">
		<!-- <h2>서두르세요!! 오늘 하루만, 할인 쿠폰 받기</h2><img src="/app/skin/basic/svg/mypage9.svg"> -->
		<img src="/app/skin/basic/img/mypage_banner.png" alt="">
	</div>
	<div class="mypage_menu">
		<ul>
			<li><img src="/app/skin/basic/svg/mypage3.svg"><a href="order_history.php" rel="external">주문내역</a></li>
			<li><img src="/app/skin/basic/svg/mypage1.svg"><a href="mypage_delivery.php" rel="external">배송지 설정</a></li>
			<li><img src="/app/skin/basic/svg/mypage2.svg"><a href="mypage_coupon.php" rel="external">할인쿠폰</a></li>
			<li><img src="/app/skin/basic/svg/mypage10.svg"><a href="venderfavorite.php" rel="external">단골꽃집</a></li>
			<li><img src="/app/skin/basic/svg/mypage5.svg"><a href="prreview_myreview.php" rel="external">나의 리뷰</a></li>
			<li><img src="/app/skin/basic/svg/mypage6.svg"><a href="board_list.php?board=faq" rel="external">자주 묻는 질문</a></li>
			<li><img src="/app/skin/basic/svg/mypage7.svg"><a href="community.php" rel="external">고객 지원</a></li>
			<!--
			<li><img src="/app/skin/basic/svg/mypage4.svg"><a href="payment_history.php" rel="external">결제관리</a></li>
			-->
		</ul>
	</div>

	<? /*
	<div class="h_area2">
		<h2>마이페이지</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<!-- 카테고리 list -->
	<div class="category_list">
		<ul class="list_type04">
			<li><a href="orderlist.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon01.png"></span>주문내역</a></li>
			<li><a href="mypage_delivery.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon09.png"></span>배송지 등록</a></li>
			<li><a href="mypage_personal_list.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon02.png"></span>1:1 문의</a></li>
			<li><a href="board_list.php?board=all" rel="external"><span><img src="/app/images/skin/default/mypage_icon03.png"></span>내가 쓴 글 모아보기</a></li>
			<li><a href="basket.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon04.png"></span>장바구니</a></li>
			<li><a href="wishlist.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon05.png"></span>위시리스트</a></li>
			<li><a href="mypage_reserve.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon06.png"></span>적립금</a></li>
			<li><a href="mypage_coupon.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon07.png"></span>쿠폰사용내역</a></li>
			<li><a href="mypage_coupon_down.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon08.png"></span>쿠폰다운로드</a></li>
			<? if($_data->memberout_type!="N"){ ?>
			<li><a href="mypage_memberout.php" rel="external"><span><img src="/app/images/skin/default/mypage_icon10.png"></span>회원탈퇴</a></li>
			<? } ?>
		</ul>
	</div>
	<!-- //카테고리 list -->
	*/ ?>

</div>

<script>
	var swiper = new Swiper('.mypage_menu', {
		slidesPerView: 'auto',
		spaceBetween: 10,
		freeMode: true
	});

	//나의 리뷰
	var swiper = new Swiper('.mypage_myreivew', {
		slidesPerView: 2,
		spaceBetween: 10
	});

	//위시리스트
	var swiper = new Swiper('.mypage_wishlist', {
		slidesPerView: 2,
		spaceBetween: 10
	});

	//최근 본 상품
	var swiper = new Swiper('.mypage_viewproduct', {
		slidesPerView: 2,
		spaceBetween: 10
	});
</script>
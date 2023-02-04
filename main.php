<?
	$main_check=1;

	include_once('header.php'); 
	include_once($Dir."lib/mobile_eventpopup.php");
	include_once($Dir."lib/check_login.php");
	include_once("counter_app.php");
	
	$targetMonth = "202112";
	$mf_sql = "SELECT * FROM monthly_flower WHERE month = '".$targetMonth."'";
	$mf_result = mysql_query($mf_sql,get_db_conn());
	$imgArr = "";
	while($mf_row = mysql_fetch_object($mf_result)) {
		$imgArr .= $mf_row->cont.",";
	}
	$imgArr = substr($imgArr,0,-1);
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&display=swap" rel="stylesheet">
<script>

	var images = ["06.png", "07.png", "08.png", "09.png","10.png","11.png"];
	$(function () { 
		var i = 0; 
		$("#monthlyFlower").css("background-image", "url(../data/shopimages/monthlyFlower/" + images[i] + ")"); 
		$('#monthlyFlower').css('background-size','cover');
		$('#monthlyFlower').css('background-color','rgb(255 236 239)');
		setInterval(function () { 
			i++; 
			if (i == images.length) { 
				i = 0; 
			} 
			$("#monthlyFlower").fadeOut(700, function () { 
				$(this).css("background-image", "url(../data/shopimages/monthlyFlower/" + images[i] + ")"); 
				$('#monthlyFlower').css('background-size','cover');
				$('#monthlyFlower').css('background-color','rgb(255 236 239)');
				$(this).fadeIn(500); 
			}); 
		}, 4000); 
	});







	$(document).ready(function() {
		getMobileOperatingSystem();
		var settings = {
							loop: true,
							pagination: {
								el: '.swiper-page-num',
								type: 'fraction'
							},
							slidesPerView: 1,
							spaceBetween: 5,
							autoplay: {
							delay: 3000,
							},
							speed:1000
						}
		var swiper = new Swiper('.hotStore', {
			slidesPerView: 2.5,
			spaceBetween: 14
		});
		var swiper = new Swiper('.reviewSwiper', {
			slidesPerView: 1.5,
			spaceBetween: 20,
			// autoplay : {  // 자동 슬라이드 설정 , 비 활성화 시 false
			// 	delay : 3000,   // 시간 설정
			// 	disableOnInteraction : true,  // false로 설정하면 스와이프 후 자동 재생이 비활성화 되지 않음
			// 	},
		});
		var bannerSwiper = new Swiper('.couponBanner',settings);
		var swiper = new Swiper('.shopFocus', {
			slidesPerView: 2.5,
			spaceBetween: 14
		});
		
		var swiper = new Swiper('.venderSwiper', {
			slidesPerView: 1,
			spaceBetween: 5,
			autoplay: {
			delay: 3000,
			}
		});
		
		var vCnt = $('#vCnt').val();
		//odoo.default({ el:'.vCntNum', from: '000', to: vCnt, animationDelay: 500 });
		
		var tsCnt = $('#tsCnt').val();
		//odoo.default({ el:'.tsCntNum', from: '000', to: tsCnt, animationDelay: 500 });

		setCntEvent();
		notiBannerCouponClickEvent();
	});	

	function notiBannerCouponClickEvent(){
		$('.notiBannerCoupon').click(function(){
			$(location).attr("href", "mypage_coupon.php");
		});
	}	


	function setCntEvent(){
		var vCnt = $('#vCnt').val();
		var tsCnt = $('#tsCnt').val();
		$({ val : 0 }).animate({ val : vCnt }, {
			duration: 1500,
			step: function() {
			var num = numberWithCommas(Math.floor(this.val));
			$(".count_num").text(num);
			},
			complete: function() {
			var num = numberWithCommas(Math.floor(this.val));
			$(".count_num").text(num);
			}
		});
		
		function numberWithCommas(x) {
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
		
		$({ val : 0 }).animate({ val : tsCnt }, {
			duration: 1500,
			step: function() {
			var num = numberWithCommas(Math.floor(this.val));
			$(".count_num2").text(num);
			},
			complete: function() {
			var num = numberWithCommas(Math.floor(this.val));
			$(".count_num2").text(num);
			}
		});
		
		function numberWithCommas(x) {
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
	}

	function getMobileOperatingSystem() {
		var currentOS;
		var mobile = (/iphone|ipad|ipod|android/i.test(navigator.userAgent.toLowerCase()));

		if (mobile) {
		// 유저에이전트를 불러와서 OS를 구분합니다.
		var userAgent = navigator.userAgent.toLowerCase();
		if (userAgent.search("android") > -1)
			currentOS = "android";
		else if ((userAgent.search("iphone") > -1) || (userAgent.search("ipod") > -1)
					|| (userAgent.search("ipad") > -1))
			currentOS = "ios";

		else
			currentOS = "else";
		} else {
		// 모바일이 아닐 때
		currentOS = "nomobile";
		}
		

	}




</script>
<!-- 내용 -->
<style>
.notiBox{ margin: 14px 14px 0 14px;width: auto;border-radius: 10px;overflow: hidden;background: rgb(255,240,220);background: linear-gradient(107deg, rgba(255,240,220,1) 0%, rgba(255,255,255,1) 80%);}
.notiBox .notiGruop{margin:5px 10px 20px 25px; display: flex;}
.notiBox .notiGruop .notiImg{width: 95px; height:80px;}
.notiBox .notiGruop .textGroup >div{margin-bottom: 5px;}
.notiBox .notiGruop .notiCnt{font-size: 2.6em; font-weight:800; color: #FFA000;margin-bottom: 20px;margin-top: 25px;}
.notiBox .notiGruop .notiTitle{color:#282828;font-weight: bold; font-size: 1.3em;}
.notiBox .notiGruop .notiSub{color:#464646; font-size: 1.15em;}
.notiBanner{background-color: #FFF0DC; margin: 14px 14px 0 14px;width: auto;border-radius: 10px;overflow: hidden;}
.notiBanner .notiGruop{margin:30px 25px 0 25px; display: flex; justify-content: space-between;}
.notiBanner .notiGruop .textGroup > div{margin-bottom: 10px;}
.notiBanner .notiGruop .textGroup .notiTitle{font-size: 1.6em; font-weight: bold; color:#e61e6e;}
.notiBanner .notiGruop .textGroup .notiSubTitle{font-size: 1.4em; font-weight: bold; color:#282828;}
.notiBanner .notiGruop .textGroup .notiSub{font-size: 1em;color: #464646;margin-top:15px;}
.notiBanner .notiGruop .notiImg img{width:150px; position: relative; bottom: 20px;}
.swiper-pagination-bullet-active {border-radius: 50%;background: #756d6c;}
.swiper-pagination-bullet{border-radius: 50%;}
.swiperPageWrap{margin: -20% auto 0 87%;border-radius: 4px;position: relative;bottom: 15px;right: 15px; }
.reviewContent{
	font-size: 1.1em;
	line-height: 20px;
	display: -webkit-box;
	max-height:62px;
	overflow:hidden; 
	vertical-align:top; 
	text-overflow: ellipsis;
	word-break:break-all; 
	-webkit-box-orient:vertical; 
	-webkit-line-clamp:3
}
.reviewName{color: #282828;
    font-size: 1.1em;
    font-weight: 500;
	margin: 5px 0;
}
.reviewComName{
	text-overflow:ellipsis;
	white-space:nowrap;
    color: #f2387e;
    font-size: 11px;
    overflow: hidden;
    text-shadow: 0px 0px 2px #ffffff;
    border-radius: 20px;
    background: #ffecef;
    width: 70px;
    padding: 5px;
    text-align: center;
    font-weight: 500;
}
.mainMenuWarp{width: calc(50vw - 22px);margin:14px 0 0 14px;}
.mainMenuGroup{background-color: #f7f7f7;border-radius: 10px;}
.mainMenuTitleGroup{display: flex; padding:20px 20px 10px 25px;justify-content: space-between;}
.mainMenuTitle{font-weight: bold;font-size: 1.5em;line-height: 1.7em;color: #282828;white-space: nowrap;}
.mainMenuSubTitleGroup{padding:0 0 20px 25px;color: #464646;}
.mainMenuSubTitle{font-size: 1.2em;}
/*팝업 css*/
#orderPopWrap{
	position: fixed;
    box-sizing: border-box;
    background: rgba(0, 0, 0, 0.7);
    z-index: 910;
    width: 100%;
    height: 100%;
    border: 0px solid rgb(221, 221, 221);
    left: 0%;
    top: 0%;
}
.orderPopGroup{
	position: absolute;
    top: 25%;
    width: 100%;
    height: 40%;
    min-width: 300px;
    background: #ffffff;
    border-radius: 20px;
    text-align: center;
}
.popTitleGroup{
	padding: 20px 0;
	color: #282828;
	font-size: 1.4em;
	font-weight: 900;
}
.popContetGroup{
	padding: 20px 0;
	border-bottom: 1px solid #d3d3d3;
	border-top: 1px solid #d3d3d3;
}
.popImg{
	width: 65px;
}
.popContentTitleGroup{
	padding: 10px 0;
}
.popContentTitle{
	color: #282828;
    font-size: 1.2em;
    font-weight: 900;
    margin: 5px 0;
}
.popContentSubTitleGroup{
	padding: 10px 0;
}
.popContentSubTitle{
	color: #282828;
    font-size: 1.1em;
    font-weight: 400;
    margin: 3px 0;
}
.popBtnGroup{
	padding: 13px 0;
    display: flex;
    justify-content: space-around;
}
.popBtnClose{
	background-color: #ffffff;
    border-radius: 30px;
    border: 1px solid #d3d3d3;
    color: #282828;
    font-weight: 900;
    font-size: 1.2em;
    text-align: center;
    width: 45%;
}
.popBtnTimeSale{
	background-color: #e61e6e;
    border-radius: 30px;
    color: #ffffff;
    font-weight: 900;
    font-size: 1.2em;
    text-align: center;
	width: 45%;
}
.popBtnGroup .checkbox{
	display: none;
}
.popBtnGroup .label{
	position: relative;
	font-size: 1.2em;
	padding-left: 25px;
	user-select: none;
}
.popBtnGroup .check-mark{
	width: 15px;
	height: 15px;
	background-color: #ffffff;
    border: 1px solid #d3d3d3;
	position: absolute;
	left:0;
	display: inline-block;
	top: 0;
border-radius: 50%;
}
.popBtnGroup .label .checkbox:checked + .check-mark{
	background-color: #e61e6e  ;
	transition: .1s;
}
.popBtnGroup .label .checkbox:checked + .check-mark:after{
	content: "";
	position: absolute;
	width: 7px;
	transition: .1s;
	height: 5px;
	background: #e61e6e  ;
	top:40%;
	left:50%;
	border-left: 2px solid #fff;
	border-bottom: 2px solid #fff;
	transform: translate(-50%, -50%) rotate(-45deg);  
	}
/*팝업 css*/




</style>
<div id="main">
	<!-- 메인 비주얼 -->
	<div id="main_visual">
		<!-- slide-delay 속성으로 인터벌 조정 및 오토플레이 유무 설정가능 -->
		<!--box 시작 -->
		<?
		//가입된 총 꽃집의 수 
		$cntSql = "SELECT COUNT( vender )as vCnt FROM tblvenderinfo WHERE disabled =0";
		$cntResult = mysql_query($cntSql, get_db_conn());
		while ($row = mysql_fetch_object ($cntResult)) {
			$vCnt = $row-> vCnt;
		}
		mysql_free_result ($cntResult);
		if($vCnt>0){
		?>
		<div class="notiBox" style="background-image:url('/app/skin/basic/svg/main_todayFlowerBanner.svg');background-repeat:no-repeat;margin-top:5px;" onClick="callTimesale('list')">
			<div class="notiGruop">
				<div class="notiTextWrap" style="position: relative;top: 4px;}">
					<div class="notiCnt vCntNum">
						<input type="hidden" id="vCnt" value="<?=$vCnt?>">
						<div class="count_num"></div>
					</div>
					<div class="textGroup">
						<div class="notiTitle">준비된 오늘의 꽃집</div>
						<div class="notiSub">우리 동네 플로리스트를 만나보세요</div>
					</div>
				</div>
			</div>
		</div>


		<div style="display: flex;">
			<div class="mainMenuWarp" onclick="talkRequestOpen();">
				<div class="mainMenuGroup">
					<div class="mainMenuTitleGroup">
						<div class="mainMenuTitle">꽃집 제안받기</div>
						<div><img style="width: 22px;" src="/app/skin/basic/svg/main_proposals.svg" alt=""></div>
					</div>
					<div class="mainMenuSubTitleGroup">
						<p class="mainMenuSubTitle">꽃집에서</p>
						<p class="mainMenuSubTitle">제안서를 보내줘요</p>
					</div>
				</div>
			</div>
			<div class="mainMenuWarp" onclick="callTimesale('')">
				<div class="mainMenuGroup">
					<div class="mainMenuTitleGroup">
						<div class="mainMenuTitle">바로 구매하기</div>
						<div ><img style="width: 23px;" src="/app/skin/basic/svg/main_today.svg" alt=""></div>
					</div>
					<div class="mainMenuSubTitleGroup">
						<p class="mainMenuSubTitle">동네 꽃집들을</p>
						<p class="mainMenuSubTitle">둘러보고 주문하세요</p>
					</div>
				</div>
			</div>
		</div>

		<div style="display: flex;">
			<div class="mainMenuWarp"  onclick="location.href='/app/venderfavorite.php'">
				<div class="mainMenuGroup">
					<div class="mainMenuTitleGroup">
						<div class="mainMenuTitle">단골 꽃집</div>
						<div ><img style="width: 23px;" src="/app/skin/basic/svg/main_venderfavorite.svg" alt=""></div>
					</div>
					<div class="mainMenuSubTitleGroup">
						<p class="mainMenuSubTitle">나만의 단골 꽃집</p>
					</div>
				</div>
			</div>
			<div class="mainMenuWarp" onclick="showVdsearch();">
				<div class="mainMenuGroup">
					<div class="mainMenuTitleGroup">
						<div class="mainMenuTitle">꽃집 검색</div>
						<div ><img style="width: 23px;" src="/app/skin/basic/svg/main_flowerSearch.svg" alt=""></div>
					</div>
					<div class="mainMenuSubTitleGroup">
						<p class="mainMenuSubTitle">우리 동네 꽃집 찾기</p>
					</div>
				</div>
			</div>
		</div>

		<!--이달의 꽃 -->
		<div style="margin-top: 14px;margin-left: 15px;height: 125px;overflow: hidden;background-color: rgb(255 236 239);border-radius: 10px;width: 390px;" onClick="callTimesale('list')">
			<div class="notiBox" id="monthlyFlower" style="margin:0;background-color:rgb(255 236 239);height:125px;background:url();"></div>
			<div class="notiGruop" style="position: relative; bottom:120px; left:23px;">
				<div style="width:calc(70vw - 20px);margin-top: 23px;float:left;">
					<div class="notiTitle" style="font-size: 1.3em;font-weight: 900;color: #282828;">동네꽃집이 추천하는</div>
					<div style="font-size: 1.5em;color: #e61e6e;font-weight: 900;margin-top: 10px;">이달의 꽃</div>
					<div style="display: flex; margin-top:20px;">	
						<div style="color: #464646;font-size: 1.1em;">꽃으로 마음을 표현해 보세요!</div>
						<div><img style="width: 20px;margin-left:8px" src="/app/skin/basic/svg/tigerIcon.svg" alt=""></div>
					</div>
				</div>
			</div>
		</div>
		
	</div>

	<!-- couponBanner container -->
	<div class="couponBanner" style="box-sizing:border-box;overflow:hidden;margin-bottom:20px;">
		<div class="swiper-wrapper" style="margin-bottom: 70px;" >
			<div class="swiper-slide bannerWrap">
				<div class="notiBanner notiBannerNewYear" style="background-color:#1e5050; width: calc(100vw - 28px);">
					<div style="height: 124px;width: 346px;position: relative;top: 18px;left: 38px;">
						<img src="/app/skin/basic/svg/2022_tiger.svg" style="width: 400px;position: relative;bottom: 20px;right: 40px;">
					</div>
				</div>
			</div>

			<div class="swiper-slide bannerWrap" onclick="location.href='/app/mypage_coupon.php'">
				<div class="notiBanner notiBannerNewYear" style="background-color:#1e5050; width: calc(100vw - 28px);">
					<div style="height: 124px;width: 346px;position: relative;top: 18px;left: 38px;">
						<img src="/app/skin/basic/svg/commencement_banner.svg" style="width: 400px;position: relative;bottom: 20px;right: 40px;">
					</div>
				</div>
			</div>
			
			<!-- <div class="swiper-slide bannerWrap">
				<div class="notiBanner notiBannerNewYear" style="background-color:#1e5050; width: calc(100vw - 28px);">
					<div style="height: 124px;width: 346px;position: relative;top: 18px;left: 38px;">
						<img src="/app/skin/basic/svg/main_newYear_user.svg" style="width: 400px;position: relative;bottom: 20px;right: 40px;">
					</div>
				</div>
			</div> -->
			
			<div class="swiper-slide bannerWrap">
				<div class="notiBanner notiBannerCoupon" style="width: calc(100vw - 28px);">
					<div class="notiGruop">
						<div class="notiTextWrap">
							<div class="textGroup">
								<div class="notiTitle">1만원 쿠폰팩</div>
								<div class="notiSubTitle">신규 회원가입 이벤트</div>
								<div class="notiSub">지급일로부터 2주 안에 사용하세요!</div>
							</div>
						</div>
						<div class="notiImg"> 
							<img src="/app/skin/basic/svg/mainCoupon_new.svg" alt="">
						</div>
					</div>
				</div>	
			</div>
			
			<div class="swiper-slide bannerWrap">
				<div class="notiBanner notiBannerReview" style="width: calc(100vw - 28px);">
					<div class="notiGruop">
						<div class="notiTextWrap">
							<div class="textGroup">
								<div class="notiTitle">1천원, 5백원 적립금</div>
								<div class="notiSubTitle">리뷰를 작성해 주세요</div>
								<div class="notiSub">소중한 리뷰는 꽃집에 큰 힘이 됩니다!</div>
							</div>
						</div>
						<div class="notiImg"> 
							<img src="/app/skin/basic/svg/mainCoupon_review.svg" alt="">
						</div>
					</div>
				</div>	
			</div>
		</div>
		<div class="swiperPageWrap">
			<div class="swiper-page-num"></div>
		</div>
	</div>
	
	<?
	}
	?>
	<div class="mainDiv" style="margin-top: 10px;"></div>
	<div class="main_display">
		<div class="title">
			<div class="icon">
				<img src="/app/skin/basic/svg/main_review_icon.svg">
			</div>
			<div class="text">
				리뷰 둘러보기
			</div>
		</div>
		<div class="displayContent">
			<div class="reviewSwiper" style="margin-bottom:40px;padding:0px 15px 10px 15px;box-sizing:border-box;">
				<div class="swiper-wrapper reviewListWrap">
				<?
					$selectSQL = "SELECT tpv.name, tpv.date, tpv.marks, tpv.img, tpv.content, tp.vender, tv.com_name
									FROM tblproductreview AS tpv
									LEFT JOIN tblproduct AS tp 
									ON tpv.productcode = tp.productcode
									LEFT JOIN tblvenderinfo AS tv 
									ON tp.vender = tv.vender
									WHERE marks >=4
									AND img IS NOT NULL
									ORDER BY DATE DESC
									LIMIT 20";
					$result=mysql_query($selectSQL,get_db_conn());
					$num_rows = mysql_num_rows($result);
					if($num_rows > 0){
						while($row=mysql_fetch_object($result)) {

							$date = $row -> date;
							$dateTime = strval(substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2));
							$strdate = strval($date);
							$name = $row -> name;
							$vender = $row -> vender;
							$com_name = $row -> com_name;
							$reName = preg_replace('/.(?=.$)/u','*',$name);
							if(strlen($row->img)>0){
								$background_url = "/data/shopimages/productreview/".$row->img;
							}
							else{
								$background_url = "/images/no_img.gif";
							}
							
				?>			
							<div class="swiper-slide shopWrap" style="overflow:hidden; width:250px;border-radius: 10px;margin-bottom: 10px;"onclick="iframePopupOpen('/app/venderinfo.php?vidx=<?=$vender?>&pagetype=pop')">
								<div class="shopImage" style="background:url('<?=$background_url?>') no-repeat;border-radius:0px;background-size:cover;min-height:280px;border-top-left-radius: 10px;border-top-right-radius: 10px;width: 100%;border: 0px!important;"></div>
								<div class="shopInfoWrap"style="padding:10px;margin-top:0;border-left: solid #9e9e9e36 1px;border-right: solid #9e9e9e36 1px;">
									<div style="display: flex;justify-content: space-between;">
										<div class="reviewName"><?=$reName?></div>
										<div class="reviewComName" ><?=$com_name?></div>
									</div>
									<div class="starWrap" style="float:left;">
									<?for($i = 0 ; $i < 5 ; $i++){
										$reviewStarImg = "review_star_off";
										if($i < $row->marks){
											$reviewStarImg = "review_star_on";
										}
										echo "<img style=\"width: 13px;\" \"class=\"memberStar\" src=\"/app/skin/basic/svg/".$reviewStarImg.".svg\">";
									}?>		
									</div>
									<font style=" margin-left:10px;font-weight: 500; font-size:12px; color:#a0a0a0;"><?=$dateTime?></font>
								</div>
								<div style="height:70px; color: #282828;border: solid #9e9e9e36 1px;padding:7px 10px 0px 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;">
									<div class="reviewContent"><?=$row->content?></div>
								</div>
							</div>
				<?
						}
					}
				?>
				</div>
			</div>
		</div>
	</div>
	<div class="mainDiv" style="margin-top: 10px;"></div>
	<!-- 메인 꽃집 진열 -->
	<div class="main_display">
		<div class="title">
			<div class="icon">
				<img src="/app/skin/basic/svg/main_title_icon01.svg">
			</div>
			<div class="text">
				요즘 뜨는 우리 동네 꽃집
			</div>
		</div>
		<div class="displayContent">
			<div class="hotStore" style="margin-bottom:40px;padding:0px 15px;box-sizing:border-box;overflow:hidden;">
				<div class="swiper-wrapper">
				<?
					$selectSQL = "SELECT 
									(SELECT 
										cont 
									FROM
										vender_multicontents AS pm
									WHERE
										pm.vender = VI.vender
									LIMIT 1
									) AS venderImage,
									IFNULL(
										(SELECT 
											ROUND(AVG(marks),1)
										FROM 
											tblproductreview AS tpr
										LEFT JOIN
											tblproduct AS tp
										ON
											tpr.productcode = tp.productcode
										WHERE
											tp.vender = VI.vender
										GROUP BY tp.vender)
									,0) AS avg_marks,
									VS.brand_name,
									VI.vender,
									VI.com_addr
								FROM 
									tblvenderinfo AS VI
								LEFT JOIN
									tblvenderstore AS VS
								ON
									VI.id = VS.id
								WHERE
									VI.disabled = 0
									AND
									(
									SELECT 
										COUNT(pm.cont)
									FROM
										vender_multicontents AS pm
									WHERE
										pm.vender = VI.vender) > 0
								ORDER BY 
									avg_marks DESC
								LIMIT 12";
					$result=mysql_query($selectSQL,get_db_conn());
					$num_rows = mysql_num_rows($result);
					if($num_rows > 0){
						while($row=mysql_fetch_object($result)) {
							$dongName = "";
							$addrArr=explode('(',$row->com_addr);
							if( count($addrArr)>1 ){
								$dongNameArr=explode(',',$addrArr[1]);
								$dongName = $dongNameArr[0];
								$dongName = str_replace(")","",$dongName);
							}
							if(strlen($row->venderImage)>0){
								$background_url = "/data/shopimages/vender/".$row->venderImage;
							}
							else{
								$background_url = "/images/no_img.gif";
							}
				?>
							<div class="swiper-slide shopWrap" style="width:140px;" onclick="iframePopupOpen('/app/venderinfo.php?vidx=<?=$row->vender?>&pagetype=pop')">
								<div class="shopImage" style="background:url('<?=$background_url?>') no-repeat;background-size:cover"></div>
								<div class="shopInfoWrap">
									<div class="shopName"><?=$row->brand_name?></div>
									<div class="starIcon">
										<img src="/app/skin/basic/svg/icon_review_star.svg">
									</div>
									<div class="marks" style="font-weight: 500;"><?=$row->avg_marks?></div>
								</div>
								<div class="shopBannerWrap">
									<div class="banner red">신규</div>
									<?if(strlen($dongName)>0){?>
										<div class="banner"><?=$dongName?></div>
									<?}?>
								</div>
							</div>
				<?
						}
					}
				?>
				</div>
			</div>
		</div>
	</div>
	
	<!-- 메인 꽃집 진열 -->
	<!--
	<div class="main_display">
		<div class="title">
			<div class="icon">
				<img src="/app/skin/basic/svg/main_title_icon02.svg">
			</div>
			<div class="text">
				우리 동네 포커스
			</div>
		</div>
		<div class="displayContent">
			<div class="shopFocus" style="margin-bottom:40px;padding:0px 15px;box-sizing:border-box;overflow:hidden;">
				<div class="swiper-wrapper">
				<?
					$selectSQL = "SELECT 
									(SELECT 
										cont 
									FROM
										vender_multicontents AS pm
									WHERE
										pm.vender = VI.vender
									LIMIT 1
									) AS venderImage,
									IFNULL(
										(SELECT 
											ROUND(AVG(marks),1)
										FROM 
											tblproductreview AS tpr
										LEFT JOIN
											tblproduct AS tp
										ON
											tpr.productcode = tp.productcode
										WHERE
											tp.vender = VI.vender
										GROUP BY tp.vender)
									,0) AS avg_marks,
									VS.brand_name,
									VI.vender,
									VI.com_addr
								FROM 
									tblvenderinfo AS VI
								LEFT JOIN
									tblvenderstore AS VS
								ON
									VI.id = VS.id
								WHERE
									VI.disabled = 0
								ORDER BY 
									avg_marks DESC
								LIMIT 6";
					$result=mysql_query($selectSQL,get_db_conn());
					$num_rows = mysql_num_rows($result);
					if($num_rows > 0){
						while($row=mysql_fetch_object($result)) {
							$dongName = "";
							$addrArr=explode('(',$row->com_addr);
							if( count($addrArr)>1 ){
								$dongNameArr=explode(',',$addrArr[1]);
								$dongName = $dongNameArr[0];
								$dongName = str_replace(")","",$dongName);
							}
							if(strlen($row->venderImage)>0){
								$background_url = "/data/shopimages/vender/".$row->venderImage;
							}
							else{
								$background_url = "/images/no_img.gif";
							}
							$width=getimagesize($background_url);
							if($width[1]>$width[0]){ //세로가 가로보다 길 때
								$background_size="110% auto";
							}else{ //가로가 세로보다 길 때
								$background_size="auto 110%";
							}
				?>
							<div class="swiper-slide shopWrap" style="width:140px;" onclick="iframePopupOpen('/app/venderinfo.php?vidx=<?=$row->vender?>&pagetype=pop')">
								<div class="shopImage" style="background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>"></div>
								<div class="shopInfoWrap">
									<div class="shopName"><?=$row->brand_name?></div>
									<div class="starIcon">
										<img src="/app/skin/basic/svg/icon_review_star.svg">
									</div>
									<div class="marks"><?=$row->avg_marks?></div>
								</div>
								<div class="shopBannerWrap">
									<div class="banner red">신규</div>
									<?if(strlen($dongName)>0){?>
										<div class="banner"><?=$dongName?></div>
									<?}?>
								</div>
							</div>
				<?
						}
					}
				?>
				</div>
			</div>
		</div>
	</div>
	-->
</div>

<div id="orderPopWrap" style="display:none;">
	<div class="orderPopGroup">
		<div class="popTitleGroup">주문 취소</div>
		<div class="popContetGroup">
			<div>
				<img class="popImg" src="/app/skin/basic/svg/order_popup_cancel.gif" alt="">
			</div>
			<div class="popContentTitleGroup">
				<div class="popContentTitle">아쉽게도 꽃집 사정으로 인해</div>
				<div class="popContentTitle">주문이 취소되었습니다</div>
			</div>
			<div class="popContentSubTitleGroup">
				<div class="popContentSubTitle">결제하신 주문은 자동 환불 되었으니</div>
				<div class="popContentSubTitle">걱정하지 마세요!</div>
			</div>
		</div>
		<div class="popBtnGroup">
			<div class="popBtnClose" onclick="orderPopClose();">
				<div style="margin: 10px 0;">확인 후 닫기</div>
			</div>
			<div class="popBtnTimeSale" onclick="callTimesale('list');">
				<div style="margin: 10px 0;">다른 꽃 보러가기</div>
			</div>
		</div>
	</div>
</div>


<?
	echo $onload;
	include_once($Dir."lib/mobile_eventlayer.php");
	include_once('footer.php');
?>

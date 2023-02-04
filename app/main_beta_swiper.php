<?
	$main_check=1;

	include_once('header.php'); 
	include_once($Dir."lib/mobile_eventpopup.php");
	
	include_once($Dir."lib/check_login.php");
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&display=swap" rel="stylesheet">
<script>

	$(document).ready(function() {
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
							}
						}
		var swiper = new Swiper('.hotStore', {
			slidesPerView: 2.5,
			spaceBetween: 14
		});
		var bannerSwiper = new Swiper('.couponBanner',settings);
		var swiper = new Swiper('.shopFocus', {
			slidesPerView: 2.5,
			spaceBetween: 14
		});
	});
	
</script>
<!-- 내용 -->
<style>
.notiBox{background-color: #FFF0DC; margin: 14px 14px 0 14px;width: auto;border-radius: 10px;overflow: hidden;}
.notiBox .notiGruop{margin:25px 30px; display: flex;}
.notiBox .notiGruop .notiImg{border: solid 1px; border-radius: 10px; width: 80px; height:80px;}
.notiBox .notiGruop .notiTextWrap{margin:10px 0 0 20px;}
.notiBox .notiGruop .textGroup >div{margin-bottom: 5px;}
.notiBox .notiGruop .notiCnt{font-size: 2.6em; font-weight:800; color: #FFA000;margin-bottom: 20px;}
.notiBox .notiGruop .notiTitle{color:#282828;font-weight: bold; font-size: 1.3em;}
.notiBox .notiGruop .notiSub{color:#464646; font-size: 1.15em;}
.notiBanner{background-color: #FFF0DC; margin: 14px 14px 0 14px;width: auto;border-radius: 10px;overflow: hidden;}
.notiBanner .notiGruop{margin:30px 25px 0 25px; display: flex; justify-content: space-between;}
.notiBanner .notiGruop .textGroup > div{margin-bottom: 10px;}
.notiBanner .notiGruop .textGroup .notiTitle{font-size: 1.6em; font-weight: bold; color:#d13c71;}
.notiBanner .notiGruop .textGroup .notiSubTitle{font-size: 1.4em; font-weight: bold; color:#282828;}
.notiBanner .notiGruop .textGroup .notiSub{font-size: 1em;color: #464646;margin-top:15px;}
.notiBanner .notiGruop .notiImg img{width:150px; position: relative; bottom: 20px;}
.swiper-pagination-bullet-active {border-radius: 50%;background: #756d6c;}
.swiper-pagination-bullet{border-radius: 50%;}

	

</style>
<div id="main">
	<!-- 메인 비주얼 -->
	<div id="main_visual">
		<!-- slide-delay 속성으로 인터벌 조정 및 오토플레이 유무 설정가능 -->
		
		<div class="main01">
			<div class="character" style="float: none;">
				<img src="/app/skin/basic/svg/venderinfo_character.svg" style="margin: 15px 0 5px 25px; width: 16vw; ">
			</div>
			<div class="textBox"style="float: none;margin:0 0 0 27px;">
				<div class="content01" style="margin:0">
					<span style="font-size:1.1em"><?=$_ShopInfo->memname?></span>님 안녕하세요!
				</div>
				<div class="content02">
					딱 맞는 꽃을 골라보세요
				</div>
			</div>
		</div>
		<!--box 시작 -->
		<?
		
		
		//가입된 총 꽃집의 수 
		$cntSql = "SELECT COUNT( vender )as vCnt FROM tblvenderinfo WHERE disabled =0";
		$cntResult = mysql_query($cntSql, get_db_conn());
		while ($row = mysql_fetch_object ($cntResult)) {
			$vCnt =  $row-> vCnt;
		}
		mysql_free_result ($cntResult);
		if($vCnt>0){
		?>
		<div class="notiBox">
			<div class="notiGruop">
				<div class="notiImg"> 
					<?
						$imgSql = "SELECT
										vm.vender,
										vm.cont
									FROM
										vender_multicontents vm
									WHERE
										vm.vender IN
										(
										SELECT * FROM
										(
											SELECT
												vv.vender
											FROM
												tblvenderstorevisit vv
												LEFT JOIN vender_multicontents vm ON
												vv.vender = vm.vender
											GROUP BY
												vv.vender
											ORDER BY
												SUM(vv.cnt) DESC
											LIMIT 5
										) AS tmp
										)
									GROUP BY
										vm.vender";
						$imgResult = mysql_query($imgSql, get_db_conn());
						while ($imgRows = mysql_fetch_object($imgResult)) {
							$cont =  $imgRows->cont;
					?>
					<img src="/data/shopimages/vender/<?=$cont?>" alt="">
					<?
						}
						mysql_free_result($imgResult);
					?>
				</div>
				<div class="notiTextWrap">
					<div class="notiCnt"><?=$vCnt?></div>
					<div class="textGroup">
						<div class="notiTitle">준비된 오늘의 꽃집</div>
						<div class="notiSub">우리 동네 플로리스트를 만나보세요</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mainBox" onclick="talkRequestOpen();">
			<div class="content01" style="margin: 25px 30px;">
				<div class="textBox" style="position: relative;top: 8px;">
					<div class="title">
						<div style="float: left;">스페셜 오더</div> 
						<div>
							<img style="width: 10px; margin:6px 0 0 25px" src="/app/skin/basic/svg/mainArrow.svg" alt="">
						</div>
					</div>
					<div class="explain">
						원하는 꽃을 문의하면
						<br>꽃집에서 제안서를 보내줘요
					</div>
					<div class="subExplain">
						3시간 이상 여유있을 때, 추천해요!
					</div>
				</div>
				<div class="imageBox">
					<img src="/app/skin/basic/svg/main01.svg">
				</div>
			</div>
		</div>
		<div class="notiBox" style="background-color: #FFEBEB;">
			<div class="notiGruop">
				<div class="notiImg"> 
				<?
					$tsSql ="SELECT
								ts.pridx,
								pm.cont
							FROM
								todaysale ts
								LEFT JOIN product_multicontents pm ON
								ts.pridx = pm.pridx
							WHERE
								ts.end >= DATE_ADD(NOW(),INTERVAL -7 day)
							GROUP BY
								pridx";

					$tsResult = mysql_query($tsSql, get_db_conn());
					$tsCnt = mysql_num_rows($tsResult);
					$i = 0;
					while ($tsRow = mysql_fetch_object ($tsResult)) {
						if ($i < 5) {
							$tsImg = $tsRow -> cont;
					?>
					<img src="/data/shopimages/product/<?=$tsImg?>" alt="">
					<?
							$i++;
						}
					}
					mysql_free_result ($tsResult);
					?>
				</div>
				<div class="notiTextWrap">
					<div class="notiCnt" style="color:#d2396e;"><?=$tsCnt?></div>
					<div class="textGroup">
						<div class="notiTitle">지난주 마감 할인 꽃</div>
						<div class="notiSub"><?=$tsCnt?>개의 신선한 꽃이 올라왔어요</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mainBox" onclick="callTimesale('')">
			<div class="content01" style="margin: 25px 30px;">
				<div class="textBox" style="position: relative;top: 8px;">
					<div class="title">
						<div style="float: left;">마감 할인</div>
						<div>
							<img style="width: 10px; margin:6px 0 0 25px" src="/app/skin/basic/svg/mainArrow.svg" alt="">
						</div>
					</div>
					<div class="explain">
						신선한 꽃을 근처 꽃집에서
						<br>저렴하게 구매할 수 있어요
					</div>
					<div class="subExplain">
						급하게 꽃이 필요할 때, 추천해요!
					</div>
				</div>
				<div class="imageBox">
					<img src="/app/skin/basic/svg/main02.svg">
				</div>
			</div>
		</div>
		<div class="mainBox half" onclick="location.href='/app/venderfavorite.php'">
			<div class="content01">
				<div class="textBox">
					<div class="title" style="display: flex;">
						<div>단골 주문</div>
						<div>
							<img style="width: 10px; margin:4px 0 0 25px" src="/app/skin/basic/svg/mainArrow.svg" alt="">
						</div>
					</div>
					<div class="explain">
						자주 사는 꽃집에서
						<br>바로 주문하세요
					</div>
				</div>
			</div>
		</div>
		<div class="mainBox half" onclick="showVdsearch();">
			<div class="content01">
				<div class="textBox">
					<div class="title" style="display: flex;">
					<div>꽃집 검색</div>
						<div>
							<img style="width: 10px; margin:4px 0 0 25px" src="/app/skin/basic/svg/mainArrow.svg" alt="">
						</div>
					</div>
					<div class="explain">
						내 주변 꽃집을
						<br>찾아보세요
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- couponBanner container -->
	<div class="couponBanner" style="box-sizing:border-box;overflow:hidden;">
		<div class="swiper-wrapper" style="margin-bottom: 70px;" >
			<div class="swiper-slide bannerWrap">
				<div class="notiBanner" style="width: calc(100vw - 28px);">
					<div class="notiGruop">
						<div class="notiTextWrap">
							<div class="textGroup">
								<div class="notiTitle">최대 1만원 할인 쿠폰</div>
								<div class="notiSubTitle">어플 신규 가입 이벤트</div>
								<div class="notiSub">지급일로부터 2개월 안에 사용하세요!</div>
							</div>
						</div>
						<div class="notiImg"> 
							<img src="/app/skin/basic/svg/mainCoupon_new.svg" alt="">
						</div>
					</div>
				</div>	
			</div>
			<div class="swiper-slide bannerWrap">
				<div class="notiBanner" style="width: calc(100vw - 28px);">
					<div class="notiGruop">
						<div class="notiTextWrap">
							<div class="textGroup">
								<div class="notiTitle">최대 1만원 할인 쿠폰</div>
								<div class="notiSubTitle">어플 신규 가입 이벤트</div>
								<div class="notiSub">지급일로부터 2개월 안에 사용하세요!</div>
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
	<div class="mainDiv"></div>
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


<?
	echo $onload;
	include_once($Dir."lib/mobile_eventlayer.php");
	include_once('footer.php');
?>
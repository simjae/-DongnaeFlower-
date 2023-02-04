<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	// include_once($Dir."lib/init.debug.php");
	//이게 디버그확인해준는 php
	include_once($Dir."lib/lib.php");

	$refURL = $_REQUEST["refURL"];

	$vidx = isset($_GET['vidx'])?trim($_GET['vidx']):"";
	$pagetype = isset($_GET['pagetype'])?trim($_GET['pagetype']):"";
	//vidx가 파라미터값 던진값
	//꽃집에는 이미 vidx value를 가지고있고 클릭시에 파라미터값으로 value를 던져줌 
	//echo는 묻지도 따지지도않고 클라이언트 화면에 그냥 출력됨, 그러므로 다시 admin에서 sql 확인요망
	$curpage = isset($_GET['page'])?trim($_GET['page']):"1";

	if($pagetype=="pop"){
		include_once("header_pop.php");
	}
	else{
		include_once("header.php");
	}
	
	$_MiniLib=new _MiniLib($vidx);
	$_MiniLib->_MiniInit();
	$_minidata=$_MiniLib->getMiniData();

	//_pr($_minidata);
	if((strlen($vidx) <= 0) || $vidx == "0"){
		echo '<script>alert(\"필수값이 누락되어 페이지를 열람하실 수 없습니다.\");history.go(-1);</script>';exit;
	}

	$infoSQL = "SELECT 
					COUNT(p.pridx) AS prcount
					, (SELECT brand_name FROM tblvenderstore WHERE vender='".$vidx."' ) AS brand_name
					, v.com_owner
					, v.com_image
					, v.id
					, v.com_addr_pointx
					, v.com_addr_pointy
					, vs.brand_description
					, v.com_addr
					, vs.cust_info
					, IFNULL(
						(SELECT 
							ROUND(AVG(marks),1)
						FROM 
							tblproductreview AS tpr
						LEFT JOIN
							tblproduct AS tp
						ON
							tpr.productcode = tp.productcode
						WHERE
							tp.vender = v.vender
						GROUP BY tp.vender)
						,0) AS avg_marks
					, IFNULL(
						(SELECT 
							count(tp.vender)
						FROM 
							tblproductreview AS tpr
						LEFT JOIN
							tblproduct AS tp
						ON
							tpr.productcode = tp.productcode
						WHERE
							tp.vender = v.vender
						GROUP BY tp.vender)
						,0) AS marks_count   ";
	$infoSQL .= "FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ON(p.vender = v.vender) ";
	$infoSQL .= "LEFT JOIN tblvenderstore AS vs ON p.vender = vs.vender ";
	$infoSQL .= "WHERE v.vender = '".$vidx."' AND p.display='Y' ";

	$imgsrc = $Dir."data/shopimages/vender/";
	
	if(false !== $infoRes = mysql_query($infoSQL,get_db_conn())){
		$infoNumRows = mysql_num_rows($infoRes);
		if($infoNumRows > 0){
			$prcount = mysql_result($infoRes,0,0);
			$brand_name = mysql_result($infoRes,0,1);
			$corprep = mysql_result($infoRes,0,2);
			$imagerep = mysql_result($infoRes,0,3);
			$corpid = mysql_result($infoRes,0,4);
			$com_addr_pointx = mysql_result($infoRes,0,5);
			$com_addr_pointy = mysql_result($infoRes,0,6);
			$brand_description = mysql_result($infoRes,0,7);
			$com_addr = mysql_result($infoRes,0,8);
			$cust_info = mysql_result($infoRes,0,9);
			$avg_marks = mysql_result($infoRes,0,10);
			$marks_count = number_format(mysql_result($infoRes,0,11));
			
		}else{
			echo '<script>alert(\"등록된 입점사가 아닙니다.\");history.go(-1);</script>';exit;
		}
		
		mysql_free_result($infoRes);
	}else{
		echo '<script>alert(\"연결이 지연되었습니다.\n 잠시 후 다시 시도 해 주시기 바랍니다.\");history.go(-1);</script>';exit;
	}
?>
<style>
	.shopIntro .shop_main .shopEdit{margin: 20px 0 0 0;}
	.shopIntro .shop_main .shopEdit h3{color:#000000;}
	.shopIntro .shop_main .shopEdit .content{width: 100%; height:auto; line-height: 140%; margin: 20px 0 0 0;}
	.shopIntro .shop_main .shopEdit .content img{width: 100%;height:auto;}
	.shopReview{background-color:#ffeeee; height:70px;display: flex; justify-content: space-between; color:#000000;}
	.shopReview div{margin:25px;}
	.shopReview .reviewText {position: relative;right: 8px;}
	.shopReview .reviewText span{ font-size: 15px; margin-right: 5px;}
	.shopReview .reviewCount span{margin-left: 1px; font-size: 17px;}
	.member{margin:0 15px 0 15px;}
	.memberWrap{display: flex;margin: 0 0 20px 5px;}
	.memberTitle{margin:5px 0 0 10px;}
	.member{margin:20px 15px 20px 15px;}
	.member .memberWrap .circle{overflow:hidden;width:40px;height:40px;border-radius:100%;background-color: white; ;box-shadow: 2px 0px 6px grey;}
	.member .memberWrap .circle img{width:40px;}
	.member .memberWrap .memberTitle .starWrap {margin-top:5px;}
	.member .memberWrap .memberTitle .starWrap .memberStar{width:14px;margin-right:2px;}
	.memberImg{width: 100%;margin-bottom:40px; }
	.memberImg img{width: 100%;height:auto; }
	
	.shopIntro .displayContent{
	}
	.shopIntro .displayContent .shopWrap{
		border-radius: 8px;
		border:solid #b9b8b8 1px!important;
		overflow:hidden;
	}
	.shopIntro .displayContent .shopWrap .shopImage{
		width:100%;
		height:200px;
		border:none;
		padding:0px;
		background-position:center!important;
	}
	.shopIntro .displayContent .shopWrap .soldout{
		color:#ffffff;
		font-weight: 500;
		line-height: 1.5em;
		background:rgba(0,0,0,0.5);
		width:100%;
		height:100%;
		position: absolute;
		top:0px;
		left:0px;
		z-index:100;
		border-radius:8px;
	}
	.shopIntro .displayContent .shopWrap .soldout .textWrap{
		font-size: 1.5em;
		margin: 80px auto;
		width: fit-content;
	}
	.shopIntro .displayContent .shopWrap .shopInfoWrap{
		margin-top:10px;
		color:#000000;
		font-size:1.1em;
		overflow:hidden;
	}
	.shopIntro .displayContent .shopWrap .shopInfoWrap .shopName{
		float:left;
		width:75%;
		overflow:hidden;
		text-overflow:ellipsis;
		white-space:nowrap;
	}
	.shopIntro .displayContent .shopWrap .shopInfoWrap .starIcon{
		float:left;
		width:12px;
		margin-left:3px;
	}
	.shopIntro .displayContent .shopWrap .shopInfoWrap .marks{
		float:right;
	}
	.shopIntro .displayContent .shopWrap .shopBannerWrap{
		margin-top:2px;
		font-size:0.9em;
		font-weight: 500;
	}
	.shopIntro .displayContent .shopWrap .shopBannerWrap .banner:first-child{
		margin-left:0px!important;
	}
	.shopIntro .displayContent .shopWrap .shopBannerWrap .banner{
		width:fit-content;
		float:left;
		background:#eaeaea;
		line-height:0.9em;
		margin:3px;
		padding:5px 10px;
		border-radius: 12px;
	}

	.shopIntro .displayContent .shopWrap .shopBannerWrap .banner.red{
		color:#f2387e;
		background:#ffecef;
		text-shadow: 0px 0px 2px #ffffff
	}

	.shopIntro .title{
		margin:14px;
		overflow: hidden;
	}
	.shopIntro .title .text{
		color:#000000;
		font-weight:500;
		margin:10px 0;
		font-size:1.3em;
		float:left;
	}
	.shopIntro .displayContent .shopWrap .product_info{
		margin: 10px;
	}
	.shopIntro .displayContent .shopWrap .product_info .prodContentWrap {
		overflow:hidden;
		padding:0;
	}
	.shopIntro .displayContent .shopWrap .product_info .prodContentWrap .prodName{
		overflow:hidden;
		text-overflow:ellipsis;
		white-space:nowrap;
		color: #000000;
	}

	.shopIntro .displayContent .shopWrap .product_info .prodContentWrap .timeimg{
		font-weight: 500;
		padding: 10px 0px 10px 20px;
		background: url(/app/skin/basic/svg/proposalProgressTimeIcon.svg) no-repeat;
		background-position: left;
		background-size: 12px;
		color: #e61e6e;
		float:left;
	}.shopIntro .displayContent .shopWrap .product_info .prodContentWrap .timeimg span{
		color: #000000;
	}
	.shopIntro .displayContent .shopWrap .product_info .prodContentWrap .priceText{
		float:right;
		padding: 10px 0px;
		font-size: 1.2em;
		color: #000000;
	}
	.productTimeWrap{background: #f0f0f0;border-radius:20px;}
</style>
<script>
	var imageSettings = {
						loop: true,
						pagination: {
							el: '.swiper-page-num',
							type: 'fraction'
						}
					}
	
	$(document).ready(function() {
		var productsSwiper = new Swiper('.timesaleProduct', {
			slidesPerView: 2.1,
			spaceBetween: 10
		});
		var timeSaleImageSwiper = new Swiper('.timeSaleImage', imageSettings);
		intCountdown();
		
		$('.btn_prev').click(function() {
			var refURL = "<?=$refURL?>";
			if (refURL == "timesale_product") {
				callTimesale('list');
			} else {
				callTimesale('map');
			}
		});
		venderinfoDrop();
	});
	
	function intCountdown(){
		$('.remainTimeBox').each(function(idx,el){
			dday = $(el).attr('endstamp');
			sid = $(el).attr('setstamp');
			reverse_counter(sid, dday);

		});
		setTimeout("intCountdown()", 1000);
	}
	
	function reverse_counter(va1, va2){
		today = new Date();
		d_day = new Date(va2);
		days = (d_day - today) / 1000 / 60 / 60 / 24;
		daysRound = Math.floor(days);
		hours = (d_day - today) / 1000 / 60 / 60 - (24 * daysRound);
		hoursRound = Math.floor(hours);
		minutes = (d_day - today) / 1000 /60 - (24 * 60 * daysRound) - (60 * hoursRound);
		minutesRound = Math.floor(minutes);
		seconds = (d_day - today) / 1000 - (24 * 60 * 60 * daysRound) - (60 * 60 * hoursRound) -
		(60 * minutesRound);
		secondsRound = Math.round(seconds);


			sec = "";
			min = "<span>:</span>";
			hr = "<span>" + hoursRound + ":</span>";
			dy = "일: ";

			hoursRound = hoursRound < 10 ? "0" + hoursRound : hoursRound;
			minutesRound = minutesRound < 10 ? "0" + minutesRound : minutesRound;
			secondsRound = secondsRound < 10 ? "0" + secondsRound : secondsRound;
			
			hoursRound = hoursRound == "00" ? "<span>" + hoursRound + "</span>" : hoursRound;
			minutesRound = minutesRound == "00" ? "<span>" + minutesRound + "</span>" : minutesRound;
			
	//		$("#"+va1).html(daysRound + dy + hoursRound + hr + minutesRound + min + secondsRound + sec);
			$("#"+va1).html(hr + minutesRound + min + secondsRound + sec);
	}
</script>

<div class="shopIntro">
	<div class="h_area2" style="position: fixed;top:constant(safe-area-inset-top) + 60px; top:calc(env(safe-area-inset-top) + 60px);width: 100% ;z-index: 50; margin-top: 5px;padding-top: 10px;">
		<h2><?=$brand_name?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
			<a class="btn_prev" rel="external" style="margin-top:10px;"><span>이전</span></a>
	</div>
	<div class="title" style="margin-top:70px;">
		<div class="text">
			<?=$brand_name?>의 바로 구매 상품
		</div>
	</div>
	<div class="displayContent">
		<div class="timesaleProduct" style="margin-bottom:40px;padding:0px 15px;box-sizing:border-box;overflow:hidden;">
			<div class="swiper-wrapper">
			<?
				$selectSQL = "SELECT
									a.* 
									,t.category
									,t.t_consumerprice
									,t.start
									,t.end
								FROM
									tblproduct a 
									INNER JOIN todaysale t USING(pridx) 
									LEFT OUTER JOIN  tblproductgroupcode b  ON 
									a.productcode=b.productcode 
								WHERE
									start <='".date('Y-m-d H:i')."'
									AND end >='".date('Y-m-d H:i')."'
									AND a.vender = '".$vidx."'
									
								UNION
								
								SELECT
									a.* 
									,t.category
									,t.t_consumerprice
									,t.start
									,t.end
								FROM
									tblproduct a 
									INNER JOIN today_flower t USING(pridx) 
									LEFT OUTER JOIN  tblproductgroupcode b  ON 
									a.productcode=b.productcode 
								WHERE
									start <='".date('Y-m-d H:i')."'
									AND end >='".date('Y-m-d H:i')."'
									AND a.vender = '".$vidx."'
									
								UNION
								
								SELECT
									a.*
									,t.category
									,t.t_consumerprice
									,t.start
									,t.end
								FROM
									tblproduct a 
									INNER JOIN flower_pot t USING(pridx) 
									LEFT OUTER JOIN  tblproductgroupcode b  ON 
									a.productcode=b.productcode 
								WHERE
									start <='".date('Y-m-d H:i')."'
									AND end >='".date('Y-m-d H:i')."'
									AND a.vender = '".$vidx."'";
				$result=mysql_query($selectSQL,get_db_conn());
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0){
					$i=0;
					while($row=mysql_fetch_object($result)) {
						
						$pridx = $row->pridx;
						$productname=$row->productname;
						$productcode = $row->productcode;
						$productmsg=$row->productmsg;
						$quantity = $row->quantity;
						$prconsumerprice=number_format($row->consumerprice);
						$sellprice=number_format($row->sellprice);
						$discountRate = 100 - round($row->sellprice / $row->consumerprice ,2) * 100;
						$enddateN = date('D M d Y H:i:s O', strtotime($row-end)); 
						$productTypeText = $row->productTypeText;
						$category = $row->category;
						$t_consumerprice = $row->t_consumerprice;
						
						$linkUrl = "";
						if($quantity == "" || $quantity > 0){
							if ($category == "TS") {
								$linkUrl = "location.href='productdetail_timesale.php?productcode=".$productcode."&vidx=".$vidx."&refURL=vender_timesale'";
							} else {
								$linkUrl = "location.href='productdetail_today_flower.php?productcode=".$productcode."&vidx=".$vidx."&refURL=vender_timesale'";
							}
						}
				
			?>
						<div class="swiper-slide shopWrap remainTimeBox" style="overflow:hidden" setstamp="dcsm_<?=$i?>" endstamp='<?=$enddateN?>' onclick="<?=$linkUrl?>">
							<div class="shopImage" style="overflow:hidden;">
								<div class="timeSaleImage" style="height:100%;">
									<div class="swiper-wrapper">
										<?
										$imageSQL="SELECT cont FROM product_multicontents WHERE pridx='".$pridx."' ORDER BY midx";
										$imageRes = mysql_query($imageSQL,get_db_conn());
										
										while($imageRow = mysql_fetch_object($imageRes)){
											$background_url = "/data/shopimages/product/".$imageRow->cont;
											
											echo "<div class='swiper-slide' style=\"width:100%;height:auto;min-height:100px;border:none;padding:0px;background:url('".$background_url."') no-repeat;background-size:cover;background-position:center;\")\"></div>\n";

										}
										?>
									</div>
									<div class="swiperPageWrap">
										<div class="swiper-page-num"></div>
									</div>
								</div>
								<?
								$strDiv = "";
								$categoryText = "";
								$infoBackgroundColor = "";
								$backgroundColor = "";
								$prdLabel = "";
								$prLabelWidth = "";
								$clabelWidth = "";
								$marginLeft = "";
								
								if (stristr($_SERVER['HTTP_USER_AGENT'],'ipad') || stristr($_SERVER['HTTP_USER_AGENT'],'iphone')) {
									$device ="ios";
								} else if (stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
									$device ="android";
								} else {
									$device = "other";
								}
								
								switch($category){
									case 'TF':
										$categoryText = "꽃";
										$infoBackgroundColor = "#ff9678";
										$backgroundColor = "#ff8064";
										$prdLabel = $productTypeText;
										$prLabelWidth = "98px";
										if ($device == "ios") {
											$clabelWidth = "65px";
										} else {
											$clabelWidth = "63px";
										}
										$marginLeft = "13.5px";
										$timewrapColor = "#ffffff";
									break;
									case 'FP':
										$categoryText = "화분";
										$infoBackgroundColor = "#a0c88c";
										$backgroundColor = "#78bb64";
										$prdLabel = $productTypeText;
										$prLabelWidth = "154px";
										if ($device == "ios") {
											$clabelWidth = "110px";
										} else {
											$clabelWidth = "108px";
										}
										$marginLeft = "13px";
										$timewrapColor = "#ffffff";
									break;
									case 'TS': 
										if($discountRate>0){
											$infoBackgroundColor = "#ffdc96";
											$backgroundColor = "#ffd56e";
											$categoryText = "마감할인";
											$prdLabel = "-".$discountRate."%";
											$prLabelWidth = "105px";
											if ($device == "ios") {
												$clabelWidth = "40px";
											} else {
												$clabelWidth = "38px";
											}
											$marginLeft = "12.5px";
										}
									break;
								}
								$strDiv .= '<div class="categoryInfo" style="background-color:'.$infoBackgroundColor.';padding:8px;" >';
								$strDiv .= '	<div class="prodLabel" style="margin-left:5px;width:'.$prLabelWidth.';height:20px;border:2px solid #282828;border-radius:25px;background-color:#282828;">';
								$strDiv .= '		<font style="margin-left:8px;float:left;color:white;padding-top:2px;">'.$categoryText.'</font>';
								$strDiv .= '		<div class="categoryLabel" style="text-align:center;padding-top:2px;margin-left:'.$marginLeft.';width:'.$clabelWidth.';float:left;height:18px;border-radius:25px;background-color:'.$backgroundColor.';color:#282828;">'.$prdLabel;
								$strDiv .= '		</div>';
								$strDiv .= '	</div>';
								$strDiv .= '</div>';
								?>
							</div>
							
							<?if($quantity != "" && $quantity < 1){
							?>
								<div class="soldout">
									<div class="textWrap">
										판매완료
									</div>
								</div>
							<?}?>
							<?echo $strDiv?>
							<div class="product_info">
								<div class="prodContentWrap">
									<div class="prodName" style="font-size:1.0rem;color:#282828;">
										<?=$productname?>
									</div>
								</div>
								<div class="prodContentWrap">
									<div class="sellPrice" style="color:#282828;font-size:1.0rem;font-weight:500;float:left;"><?=$sellprice?>원</div>
									<?if ($category == "TS") {?>
									<div class="consumerprice" style="margin-left:10px;color:#464646;font-size:1.0rem;float:left;text-decoration: line-through;"><?=$t_consumerprice?>원</div>
									<?}?>
								</div>
								<div class="prodContentWrap" style="width:55%;">
									<div class='productTimeWrap' style="background-color:<?=$timewrapColor?>">
										<div class='prodContentWrap' style="height:20px;">
											<?if ($category == "TS") {?>
											<div class='timeimg' id='dcsm_<?=$i?>' style='margin-left:10px;padding:2px 0px 20px 17px;background-position: 0px 4px;'></div>
											<?}?>
										</div>
									</div>
								</div>
								<p><span class="prname"><a href="productdetail_timesale.php?productcode=<?=$productcode.($vidx?"&vidx=".$vidx:"")?>" rel="external"><?=$msgreservation?></a></span></p>
							</div>
						</div>
			<?
						$i++;
					}
				}
				else{
			?>
					<div>
						진행중인 타임세일이 없습니다.
					</div>
			<?
				}
			?>
			</div>
		</div>
	</div>
</div>
<div class="mainDiv"></div>
<div class="venderinfoWrap" >
	<div style="border-bottom: solid 5px #f0f0f0;">
		<div class="fn_s3_w9 venderinfoBtn" style="font-size: 1.3em;font-weight: 900;color: #282828;padding: 20px;">
			판매 꽃집 정보 <img src="/app/skin/basic/svg/productFlower_arrow_under.svg" alt="arrow" style="width:15px;margin-left:10px;">
		</div>
	</div>
	<div class="venderinfoGroup" style="display:none;">
	<?
	$pagetype = "include";
	include $skinPATH."venderinfo.php";
	include_once("footer.php");
	?>
	</div>
</div>
<script type="text/javascript">
	function venderinfoDrop(){
		//벤더 드랍
		$('.venderinfoBtn').click(function(){
			if(!$('.venderinfoBtn').hasClass('select')){
				$('.venderinfoBtn').addClass('select');
				$('.venderinfoBtn').find('img').attr('src','/app/skin/basic/svg/productFlower_arrow_up.svg');
				$('.venderinfoGroup').show();
			}else{
				$('.venderinfoBtn').removeClass('select');
				$('.venderinfoBtn').find('img').attr('src','/app/skin/basic/svg/productFlower_arrow_under.svg');
				$('.venderinfoGroup').hide();
			}
		})
	}
</script>
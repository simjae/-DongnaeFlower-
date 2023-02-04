
<style>
.orderList{
	margin: 0 14px;
	padding: 20px 0;
    border-bottom: solid 1px #ebebeb;
    overflow:hidden;
}
.orderList .imgWrap{
	margin-right: 15px;
    width: 100px;
	float: left;
}
.orderList .infoWrap{
	float: left;
	overflow:hidden;
	width: calc(100vw - 145px)
}
.orderList .infoWrap .infoLeft{
	float: left;
	width:fit-content;
}
.orderList .infoWrap .infoRight{
	float: right;
	width:fit-content;
}
.orderList .infoWrap .infoLeft .infoDate{
	font-size: 1em;
}
.orderList .infoWrap .infoLeft .infoName{
	margin:10px 0;
	color: #00000b;
    font-size: 1.5em;
    line-height: 1.5em;
    font-weight: 500;
}
.orderList .infoWrap .btnWrap{
	width: 120px;
	width: calc(100vw - 145px);
	float:left;
}
.orderList .infoWrap .btnWrap .infoBtn{
	float: left;
	margin: 5px;
	cursor: pointer;
	color: #16161c;
	border-radius: 14px;
	padding: 5px 12px;
    box-shadow: 1px 1px 2px 1px #e2e2e2;
	font-size: 0.8em;
}
.orderList .infoWrap .btnWrap .infoBtn:first-child{
	margin-left: 0;
}
.orderList .infoWrap .btnWrap .infoBtn.status{
	color: #DC2872;
	border:1px solid #DC2872;
}

.orderList .infoWrap .btnWrap .infoBtn.reviewwrite{
}
.orderList .infoWrap .btnWrap .infoBtn.reviewwrite::after{
    content: "리뷰 남기기";
	color:#ec2b80;
}
.orderList .infoWrap .btnWrap .infoBtn.reviewview{
}
.orderList .infoWrap .btnWrap .infoBtn.reviewview::after{
    content: "내 리뷰 보기";
	color:#000000;
}
.orderList .infoWrap .btnWrap .infoBtn.none{
	display:none;
}
.infoWrap .infoRight .priceWrap{
    padding-top: 34px;
    font-size: 15px;
    font-weight: 500;
    color: #00000b;
}
</style>
<div id="content1">
	<?
	$reviewSQL="SELECT 
						toi.id, 
						tpc.pridx,
						tp.vender, 
						tvs.brand_name, 
						tp.ordercode, 
						tp.productcode, 
						tp.productname, 
						tp.price, 
						toi.reserve, 
						tp.date,
						tp.status AS order_status,
						tpc.maximage,
						(
							SELECT
								IF(reviewWrite = 'Y','R',deli_gbn)
							FROM
								tblorderlog
							WHERE
								productcode = tp.productcode
							ORDER BY
								createDate DESC
							LIMIT 1
						) deli_gbn
					FROM
						tblorderproduct AS tp
						LEFT JOIN tblorderinfo AS toi ON
						tp.ordercode = toi.ordercode
						LEFT JOIN tblproduct AS tpc ON
						tpc.productcode = tp.productcode
						LEFT JOIN tblvenderstore AS tvs ON
						tp.vender = tvs.vender
					WHERE toi.id = '".$_ShopInfo->getMemid()."' AND tp.productcode LIKE '800%' ORDER BY date DESC";
	
	$reviewResult = mysql_query($reviewSQL,get_db_conn());
	while($reviewRow = mysql_fetch_object($reviewResult)){
		$pridx=$reviewRow->pridx;
		$brand_name=$reviewRow->brand_name;
		$reserve=number_format($reviewRow->reserve);
		$vender= $reviewRow->vender;
		$maximage=$reviewRow->maximage;
		$deli_gbn=$reviewRow->deli_gbn;
		$ordercode=$reviewRow->ordercode;
		$productcode=$reviewRow->productcode;
		$price = number_format($reviewRow->price);
		$date=$reviewRow->date;
		$order_status = $reviewRow->order_status;
		$fDate = date("Y년 m월 d일", strtotime( $date ) );
		$hour = date("H",strtotime( $date ));
		if ($hour > 12) {
			$hour = $hour - 12;
			$fTime = " 오후 " . $hour."시";
		}
		else if($hour == 12){
			$fTime = " 오후 " . $hour."시";
		}
		else {
			$fTime = " 오전 " . $hour."시";
		}
		$linkUrl = "location.href='timesale_detail.php?ordercode=".$ordercode."'";
		$reviewClass = "none";
		switch($deli_gbn){
			case 'N': $statusText =  "접수대기중";  break;
			case 'S': $statusText =  "상품준비중";  break;
			case 'T': $statusText =  "제작완료";  break;
			case 'Y': $statusText =  "거래완료"; $reviewClass = "reviewwrite"; $reviewStatus="write"; break;
			case 'R': $statusText =  "거래완료"; $reviewClass = "reviewview"; $reviewStatus="view"; break;
		}
		
		if ($order_status == "RC") {
			$statusText =  "환불완료";
			$linkUrl = "return false";
		}
	?>
		<div class="orderList">
			<div class="imgWrap">
				<div class="shopImage" style="overflow:hidden;">
					<div class="timeSaleImage" style="height:100%;">
						<div class="swiper-wrapper">
							<?
							$imageSQL="SELECT cont FROM product_multicontents WHERE pridx='".$pridx."' ORDER BY midx";
							$imageRes = mysql_query($imageSQL,get_db_conn());
							
							while($imageRow = mysql_fetch_object($imageRes)){
								$background_url = "/data/shopimages/product/".$imageRow->cont;
								echo "<div class='swiper-slide' style=\"width:100%;height:auto;min-height:100px;border:none;padding:0px;background:url('".$background_url."') center;background-position:center;background-size:cover;\")\"></div>\n";

							}
							?>
						</div>
						<div class="swiperPageWrap">
							<div class="swiper-page-num"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="infoWrap">
				<div onclick="<?=$linkUrl?>">
					<div class="infoLeft">
						<div class="infoDate"><?=$fDate.$fTime?></div>
						<div class="infoName"><?=$brand_name?></div>
					</div>
					<div class="infoRight">
						<div class="priceWrap"><?=$price?>원</div>
					</div>
				</div>
				<div class="btnWrap">
					<div class="infoBtn" onclick="iframePopupOpen('/app/venderinfo.php?vidx=<?=$vender?>&pagetype=pop')">
						꽃집정보
					</div>
					<div class="infoBtn status">
						<?=$statusText?>
					</div>
					<div class="infoBtn <?=$reviewClass?>" id="reviewBtn<?=$productcode?>" onclick="callReview(this);" status="<?=$reviewStatus?>" productcode="<?=$productcode?>" aoidx="0">
					</div>
				</div>
			</div>
		</div>
<?
	}
?>
</div>
<script>
	var imageSettings = {
						pagination: {
							el: '.swiper-page-num',
							type: 'fraction'
						}
					}
	$(document).ready(function() {
		var timeSaleImageSwiper = new Swiper('.timeSaleImage', imageSettings);
	});
	
	function callReview(obj){
		var aoidx = $(obj).attr("aoidx");
		var status = $(obj).attr("status");
		var productcode = $(obj).attr("productcode");
		iframePopupOpen('/app/prreview_' + status + '_pop.php?productcode=' + productcode + '&aoidx=' + aoidx)
	}
	
	function reviewProc(aoidx,productcode){
		$("#reviewBtn"+productcode).attr("status","view");
		$("#reviewBtn"+productcode).removeClass("reviewwrite");
		$("#reviewBtn"+productcode).addClass("reviewview");
		iframePopupClose();
	}
</script>


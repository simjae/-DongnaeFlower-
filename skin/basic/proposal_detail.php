<style>
.left{
	float: left;
}
.right{
	float: right;
}
.question_order_wrap{
	
}
.question_order_wrap .contentWrap{
	overflow: hidden;
	margin: 0px auto;
    padding: 0px 15px;
    border: 0px;
    font-size: 14px;
    background: #ffffff;
    overflow: hidden;
	border-bottom: solid 1px;

}
.question_order_wrap .contentWrap .infoGroup{
	overflow: hidden;
	padding: 20px 10px 0 20px;
}
.question_order_wrap .contentWrap .infoRow .bellIcon {
	margin:5px 15px 0 0;
    width: 18px;
}
.question_order_wrap .contentWrap .infoRow .dateText {
	margin-top: 5px;
    margin-left: 18px;
    font-size: 1.2em;
    color: #000000;
    line-height: 1.7em;
}
.question_order_wrap .contentWrap .addrRow{
	margin-top: 5px;
	font-size: 12px;
}
.question_order_wrap .contentWrap .addrRow .addr{
	margin-left: 33px;
}
.question_order_wrap .contentWrap .addrRow .starIcon{
    width: 11px;
    margin: 1px 5px 0px 10px;
}
.question_order_wrap .contentWrap .addrRow .reviewAverage{
	margin-left: 5px;
}
.question_order_wrap .contentWrap .priceRow{
	margin-top:5px;
}
.question_order_wrap .contentWrap .priceRow .pick{
	margin-left: 10px;
	border-radius: 30px;
    padding: 5px 10px;
    background: #83838330;
	font-size: 12px;
}
.question_order_wrap .contentWrap .priceRow .pick:first-child{
	margin-left: 33px;
}
.question_order_wrap .contentWrap .priceRow .dateText{
	font-size: 1.2em;
    color: #000000;
    line-height: 2em;
}
.question_order_wrap .contentWrap .imgRow{
	width: 330px;
    height: 200px;
	margin: 5px 0 20px 0;
	overflow:hidden;
}
.question_order_wrap .chatWrap{
	margin: 0px auto;
    padding: 0px 15px;
    border: 0px;
    font-size: 1.2em;
    line-height: 1.5em;
    overflow: hidden;
}
.question_order_wrap .chatWrap .chatRow .chatProfile {
    width: 50px;
    height: 50px;
    background: url("/app/skin/basic/svg/talkProfileIcon.svg");
    margin: 8px 8px 8px 0;
}
.question_order_wrap .chatWrap .chatRow{
	margin-top: 25px;
	overflow:hidden;
}

.question_order_wrap .chatWrap .chatRow .chatQustion{
	background-color: #ffc0cb91;
    overflow: hidden;
    border-radius: 9px;
    padding: 10px;
	margin-right: 11px;
}
.question_order_wrap .chatWrap .chatRow .chatcontent{
	background-color: #ffc0cb91;
    overflow: hidden;
    border-radius: 9px;
    padding: 15px;
	margin-right: 11px;
	width:calc(100vw - 130px);
    color: #000;
}
.question_order_wrap .chatWrap .chatRow .chatTime{
	float:right;
    font-size: 12px;
}
.question_order_wrap .chatWrap .chatRow .button{
	background-color: palevioletred;
	border-radius: 20px;
}
.question_order_wrap .telBtn{
	width:100%;
	color:#ffffff;
	font-size:1.5em;
	line-height:1.5em;
	font-weight:600;
	text-align:center;
	padding:20px 0;
	background:#ec2b80;
	margin-top:30px;
}
.swiperPageWrap {
	margin-top: -50px;
}
.btnRow {
	overflow:hidden;
}
.btnRow .btnWrap {float:right;}
.btnRow .btnWrap .shopBtn{
	margin-left: 5px;
	display:table; 
	float:left;
	border-radius: 20px;
	border:solid #000000 1px;
	position: relative;
	box-shadow: 3px 3px 5px 0px RGBA(0,0,0,0.05);
	font-size: 0.8em;
	padding: 3px 12px;
	font-weight:500;
}
.btnRow .btnWrap .shopBtn::after{
	content: attr(value);
}
.btnRow .btnWrap .shopBtn.favSel{
	color: #ffffff;
	border: solid #ec2b80 1px;
	background: #ec2b80;
}
</style>
<script>
	var proposalsSettings = {
						pagination: {
							el: '.swiper-page-num',
							type: 'fraction'
						}
					}
	$(document).ready(function() {
		proposalsSwiper = new Swiper('#proposalsSwiper', proposalsSettings);
	});
	function toggleBookmarkEvent(obj) {	
		var formData = $("form[name=venderForm]").serialize() ;
		$.ajax({
			url: "/api/vender_favorite.php",
			type: "post",
			data: formData,
			dataType : 'json',
			error: function(xhr, status, error){
				alert("찜하기 처리중에 오류가 발생했습니다.");
			},
			success : function(data){
				if(data["proc"]=="del"){
					alert("단골꽃집에서 삭제되었습니다.");
					$(obj).removeClass("favSel");
				}
				else if(data["proc"]=="ins"){
					alert("단골꽃집으로 등록되었습니다.");
					$(obj).addClass("favSel");
				}
			}
		})
	}
</script>
<div id="content">
	<div class="h_area2">
		<h2>문의 및 주문</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<?
	$aoidx = isset($_POST["aoidx"]) ? $_POST["aoidx"] : (isset($_GET['aoidx']) ? $_GET['aoidx'] : "");
	$sql="SELECT 
			ao.addr
			,ao.addr1
			,ao.addr2
			,ao.addr3
			,ao.addr4
			,ao.addr5
			,ao.aoidx
			,ao.comment
			,ao.createDate
			,ao.creater
			,ao.del_flag
			,ao.maxPrice
			,ao.minPrice
			,ao.orderType
			,ao.priceText
			,ao.prodNum
			,ao.productType
			,ao.productTypeText
			,ao.purpose
			,ao.purposeText
			,ao.rcvName
			,ao.receiveDate
			,ao.receiveTime
			,ao.closeDate
			,ao.closeTime
			,ao.status
			,ao.style1
			,ao.styleText1
			,ao.styleImage1
			,ao.style2
			,ao.styleText2
			,ao.styleImage2
			,ao.style3
			,ao.styleText3
			,ao.styleImage3
			,ao.talkOrder
			,ao.tel
			,ao.updateDate
			,ao.updater
			,ao.userid
			,ao.zip
			,op.productcode
			,op.price
			,aop.deli_price
			,op.receiveTypeText
			,pd.pridx
			,vi.vender
			,vi.com_addr
			,vs.brand_name
			,oi.vender_050_tel
			,DATE_FORMAT( CONCAT( ao.receiveDate, ' ', ao.receivetime ) , '%Y-%m-%d %H:%i' ) AS receiveDateTime
			,DATE_FORMAT( CONCAT( ao.closeDate, ' ', ao.closetime ) , '%Y-%m-%d %H:%i' ) AS closeDateTime
			,ROUND(
				(5671 
					* acos(
						cos(radians(ao.ao_addr_pointx)) 
						* cos(radians(CAST(vi.com_addr_pointx AS DECIMAL(18,15)))) 
						* cos(radians(CAST(vi.com_addr_pointy AS DECIMAL(18,15))) - radians(ao.ao_addr_pointy))
						+ sin(radians(ao.ao_addr_pointx)) 
						* sin(radians(CAST(vi.com_addr_pointx AS DECIMAL(18,15))))
					)
				),1
			) AS distance 
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
					tp.vender = vi.vender
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
					tp.vender = vi.vender
				GROUP BY tp.vender)
				,0) AS marks_count 
			,aop.deli_type
			, (	SELECT 
							COUNT(frequenter_idx)
						FROM 
							tblfrequenter 
						WHERE 
							vender = vi.vender 
							AND member_id = '".$_ShopInfo->getMemid()."') AS favo_cnt 
		FROM
			auction_order AS ao
		LEFT JOIN
			tblorderproduct AS op
		ON
			ao.aoidx = op.aoidx
		LEFT JOIN
			tblorderinfo AS oi
		ON
			op.ordercode = oi.ordercode
		LEFT JOIN
			tblproduct AS pd
		ON
			op.productcode = pd.productcode
		LEFT JOIN
			auction_order_proposal AS aop
		ON
			pd.pridx = aop.pridx
		LEFT JOIN
			tblvenderinfo AS vi
		ON
			aop.vender = vi.vender
		LEFT JOIN
			tblvenderstore AS vs
		ON
			op.vender = vs.vender
		WHERE
			ao.userid = '".$_ShopInfo->getMemid()."'
			AND ao.aoidx = '".$aoidx."'";
	$result1=mysql_query($sql,get_db_conn());
	$numRows = mysql_num_rows($result1);
	
	while($row=mysql_fetch_object($result1)) {
		$receiveDateTime = $row->receiveDateTime;
		$receiveDateArr = explode("-",$row->receiveDate);
		$receiveTimeArr = explode(":",$row->receiveTime);
		$receiveHour = getHourText((int)$receiveTimeArr[0]);
		$receiveDateTimeStr = $receiveDateArr[0]."년 "
		.((int)$receiveDateArr[1])."월 "
		.((int)$receiveDateArr[2])."일 "
		.$receiveHour;
		
		$closeDateTime = $row->closeDateTime;
		$closeDateArr = explode("-",$row->closeDate);
		$closeDateTimeStr = $closeDateArr[0]."년 "
		.$closeDateArr[1]."월 "
		.$closeDateArr[2]."일 "
		.$row->closeTime;
		
		$enddateN = date('D M d Y H:i:s O', strtotime($closeDateTime));

		$addr = str_replace("="," ",$row->addr);
		$rcvName = $row->rcvName;
		$tel = str_replace("-"," ",$row->tel);
		$prodNum = $row->prodNum;
		$receiveTypeText = $row->receiveTypeText;
		$purposeText = $row->purposeText;
		$productTypeText = $row->productTypeText;
		$priceText = $row->priceText;
		$styleText1 = $row->styleText1;
		$styleText2 = $row->styleText2;
		$styleText3 = $row->styleText3;
		$status = $row->status;
		$aoidx = $row->aoidx;
		$pridx = $row->pridx;
		$favo_cnt = $row->favo_cnt;
		$favoBtnClass = $favo_cnt>0?"favSel":"";
		$orderStatus = $row->orderStatus;
		$productcode = $row->productcode;
		$vidx = $row->vender;
		$brand_name = $row->brand_name;
		$vender_050_tel=$row->vender_050_tel;
		$orderPrice = $row->price;
		$com_addr = $row->com_addr;
		$com_addr_arr = explode(" ",$com_addr);
		if(count($com_addr_arr) >= 3){
			$com_addr_simple = $com_addr_arr[1]." ".$com_addr_arr[2];
		}
		$avg_marks = $row->avg_marks;
		$marks_count = number_format($row->marks_count);
		
		
		if($row->deli_price == 0){
			$deliPriceText = "배송비 무료";
		}
		else{
			$deliPriceText = "배송비 +".number_format($row->deli_price)."원";
		}
	?>
		<form name="venderForm" method="post">
			<input type="hidden" name="vender" value="<?=$vidx?>">
			<input type="hidden" name="member_id" value="<?=$_ShopInfo->getMemid()?>">
		</form>
		<div class="question_order_wrap">
			<!-- 종합 START -->
			<div class="contentWrap">
				<div class="infoGroup">
					<div class="infoRow">
						<div class="bellIcon left"><img src="/app/skin/basic/svg/question_main01.svg"></div>
						<div class="dateText"><?=$brand_name?></div>
					</div>
					<div class="addrRow">
						<div class="addr left"><?=$com_addr_simple?></div>
						<div class="starIcon left"><img src="/app/skin/basic/svg/review_star_on.svg" alt=""></div>
						<div class="reviewAverage"><?=$avg_marks?> (<?=$marks_count?>)</div>
					</div>
					<div class="btnRow">
						<div class="btnWrap">
							<div class="shopBtn <?=$favoBtnClass?>" value="찜하기" onclick="toggleBookmarkEvent(this);">
							</div>
							<?
							$tvRequestScript = "targetVenderRequestOpen('".$vidx."','".$brand_name."')";
							?>
							<div class="shopBtn" value="단골 주문하기" onclick="<?=$tvRequestScript?>">
							</div>
						</div>
					</div>
					<div class="priceRow">
						<div class="pick left"><?=$receiveTypeText?></div>
						<?
						if($receiveTypeText=="배송"){
						?>
							<div class="pick left"><?=$deliPriceText?></div>
						<?}?>
						<div class="dateText right"><?=number_format($orderPrice)?>원</div>
					</div>
					<div id="proposalsSwiper" class="swiper-primage imgRow right">
						<div class="swiper-wrapper">
							<?
							$imageSQL="SELECT cont FROM product_multicontents WHERE pridx='".$pridx."' ORDER BY midx";
							$imageRes = mysql_query($imageSQL,get_db_conn());
							
							while($imageRow = mysql_fetch_object($imageRes)){
								$background_url = "/data/shopimages/product/".$imageRow->cont;
								echo "<div class='swiper-slide' style=\"width:100%;height:auto;min-height:100px;border:none;padding:0px;background:url('".$background_url."') no-repeat;background-position:center;background-size:cover;\"></div>\n";

							}
							?>
						</div>
						<div class="swiperPageWrap">
							<div class="swiper-page-num"></div>
						</div>
					</div>
				</div>
				
				
			</div>
			<div class="chatWrap">
			<?
				$statusSQL="SELECT 
								* 
							FROM 
								tblorderlog
							WHERE 
								aoidx='".$aoidx."' AND
								NOT(productcode LIKE '99%' OR productcode LIKE 'COU%')
							ORDER BY
								idx";
				$statusRes = mysql_query($statusSQL,get_db_conn());
				if($receiveTypeText=="배송"){
					$message["N"] = $receiveDateTimeStr."까지 <br>".$addr."로 보내드릴께요!";
					$message["S"] = "플로리스트가 고객님의 꽃을 준비중입니다. <br> 배송이 시작되면 다시 알려드려요.";
					$message["X"] = "꽃이 주문지로 배송중입니다. <br> 배송기사 연락처 : <a href=\"tel:##deli_num##\">##deli_num##</a>";
				}
				else{
					$message["N"] = $receiveDateTimeStr."까지 <br> 픽업하실 수 있게 준비하겠습니다!";
					$message["S"] = "플로리스트가 고객님의 꽃을 준비중입니다. <br> 준비가 완료되면 다시 알려드려요.";
					$message["T"] = "주문하신 꽃이 준비되었어요. <br>".$com_addr."에서 꽃을 찾아가세요.";
				}
				$message["Y"] = "주문이 완료되었습니다. <br>꽃은 마음에 드셨나요? <br>리뷰를 남겨주시면 적립금을 드립니다.";
				$message["R"] = "정성스런 리뷰 감사합니다. <br>다음에는 더 좋은꽃으로 보답드릴께요:)";
				while($statusRow = mysql_fetch_object($statusRes)){
					$createdate = $statusRow->createdate;
					$deli_gbn = $statusRow->deli_gbn;
					$deli_num = $statusRow->deli_num;
					$reviewWrite = $statusRow->reviewWrite;
					if($deli_gbn == "X"){
						$message["X"] = str_replace("##deli_num##", $deli_num, $message["X"]);
					}
					if($reviewWrite == "Y"){
						$deli_gbn = "R";
					}
				?>
						<div class="chatRow">
							<div class="chatProfile left"></div>
							<div class="chatcontent left">
								<?=$message[$deli_gbn]?>
							</div>
							<div class="chatTime"><?=$createdate?></div>
						</div>
				<?}?>
			</div>
			<?if($vender_050_tel){?>
				<div class="telBtn" onclick="location.href='tel:<?=$vender_050_tel?>'">
					전화로 문의하기
				</div>
			<?}?>
		</div>
		<!-- 종합 END -->
	<?} ?>
	
</div>
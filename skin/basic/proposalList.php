<?
$mode = $_POST["mode"];
$aoidx = $_POST["aoidx"];

if ($mode == "cancel") {
	if (strlen(aoidx) > 0) {
		$ao_sql = "UPDATE auction_order SET status=2 WHERE aoidx=".$aoidx;
		mysql_query($ao_sql,get_db_conn());
		echo "<html></head><body onload=\"alert('스페셜오더가 취소되었습니다.');location.href='proposalList.php';\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('존재하지 않는 스페셜오더 입니다.\\n\\n올바른 스페셜오더를 선택해 주세요.');location.href='proposalList.php'\"></body></html>";exit;
	}
}
?>
<style>
.proposalHeader {width:100%;background-color:#f5f5f5;height:30px;padding-top:15px;padding-left:20px;font-size:1.2rem;font-weight:400px;}
</style>
<div id="content">
	<!-- 주문결제 -->
	<input type="hidden" name="msg_type" value="1" />
	<input type="hidden" name="addorder_msg" value="" />
	<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>" />

	<div class="proposal_list_wrap">
			
		<!-- 종합 START -->
		<div class="info_group">
			<?
			$sql="SELECT 
					ao.aoidx
					,ao.addr
					,ao.addr1
					,ao.addr2
					,ao.addr3
					,ao.addr4
					,ao.addr5
					,ao.targetVender
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
					,ao.receiveType
					,ao.receiveTypeText
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
					,op.status AS order_status
					,vs.brand_name
					,DATE_FORMAT( CONCAT( ao.receiveDate, ' ', ao.receivetime ) , '%Y-%m-%d %H:%i' ) AS receiveDateTime
					,DATE_FORMAT( CONCAT( ao.closeDate, ' ', ao.closetime ) , '%Y-%m-%d %H:%i' ) AS closeDateTime
					,DATE_FORMAT( CONCAT( ao.closeDate, ' ', ao.closetime ) , '%Y-%m-%d %H:%i:%s' ) >= NOW() AS orderStatus
					,(SELECT COUNT(aop01.aopidx) FROM auction_order_proposal AS aop01 WHERE aop01.aoidx = ao.aoidx AND aop01.userid = '".$_ShopInfo->getMemid()."' AND aop01.del_flag=false) AS proposalCount
					,(SELECT COUNT(aop02.aopidx) FROM auction_order_proposal AS aop02 WHERE aop02.aoidx = ao.aoidx AND aop02.userid = '".$_ShopInfo->getMemid()."' AND aop02.del_flag=false AND aop02.updateDate > ao.chkDate) unchkCount
					,(SELECT deli_gbn FROM tblorderlog WHERE aoidx = ao.aoidx ORDER BY createDate DESC LIMIT 1) deli_gbn
				FROM
					auction_order AS ao
				LEFT JOIN
					tblorderproduct AS op
				ON
					ao.aoidx = op.aoidx
				LEFT JOIN
					tblvenderstore AS vs
				ON
					op.vender = vs.vender
				WHERE
					ao.userid = '".$_ShopInfo->getMemid()."'
				ORDER BY ao.updateDate DESC, ao.status DESC ,ao.aoidx DESC";
			$result=mysql_query($sql,get_db_conn());
			$num_rows = mysql_num_rows($result);
			$cnt=0;
			if($num_rows > 0){
				while($row=mysql_fetch_object($result)) {
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

					$targetVender = $row->targetVender;
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
					$basketStatus = $row->basketStatus;
					$proposalCount = $row->proposalCount;
					$unchkCount = $row->unchkCount;
					$orderStatus = $row->orderStatus;
					$productcode = $row->productcode;
					$brand_name = $row->brand_name;
					$orderPrice = $row->price;
					$order_status = $row->order_status;
					$deli_gbn = $row->deli_gbn;
					$timeBgClass = "";
					if($unchkCount > 0){
						$bellOn = "_on";
					}
					else{
						$bellOn = "_off";
					}
					
					if(($orderStatus && $status == 1) || $status == 3){
						if($status == 3){
							$linkUrl='location.href="proposal_detail.php?aoidx='.$aoidx.'"';
						}
						else{
							$linkUrl='location.href="proposals.php?aoidx='.$aoidx.'"';
						}
						
						switch($deli_gbn){
							case 'N': $statusText =  "접수대기중";  break;
							case 'S': $statusText =  "상품준비중";  break;
							case 'X': $statusText =  "배송중";  break;
							case 'T': $statusText =  "제작완료";  break;
							case 'Y': $statusText =  "거래완료";  break;
						}
						
						if ($order_status == "RC") {
							$statusText =  "환불완료";
							$linkUrl = "return false";
							$style_rc = "border:none;border-radius:0px;box-shadow:none;border-bottom:1px solid #9e9e9e36;";
						}
					?>
						<div class="proposalProgress" style="<?=$style_rc?>">
							<?
								if ($status == 1) {
							?>
							<div class="proposalHeader">
								<font class="proposalHeaderMessage" style="float:left;">꽃집에서 제안을 받고 있어요</font>
								<font class="cancelOrderBtn" onClick="setCancelOrderEvent(<?=$aoidx?>)" style="float:left;margin-left:135px;">×</font>
							</div>
							<?}?>
							<div class="contentWrap" onclick='<?=$linkUrl?>'>
								<div class="infoWrap">
									<div class="progressIcon">
										<img src="/app/skin/basic/svg/proposalProgressIcon.svg">
									</div>
									<div class="dateText">
										<?=$receiveDateTimeStr?>
									</div>
									<?if($status == 1){
										if($targetVender > 0){
											$timeBgClass = "targetVender";?>
											<div class="proposalTvCountWrap <?=$bellOn?>" value="<?=$proposalCount?>">
												단골주문
											</div>
										<?
										}
										else{
										?>
										<div class="proposalCountWrap <?=$bellOn?>" value="<?=$proposalCount?>">
											<img src="/app/skin/basic/svg/proposalProgresBellIcon<?=$bellOn?>.svg">
										</div>
									<?	}
									}
									else{
									?>
										<div class="proposalStatusBtn" value="<?=$statusText?>">
										</div>
									<?}?>
								</div>
								<?if($status == 1){?>
									<div class="bannerWrap">
										<div class="infoBanner">
											<?=$purposeText?>
										</div>
										<div class="infoBanner">
											<?=$productTypeText?>
										</div>
										<div class="infoBanner">
											<?=$priceText?>
										</div>
										<?if($styleText1 != ""){?>
											<div class="infoBanner">
												<?=$styleText1?>
											</div>
										<?}?>
										<?if($styleText2 != ""){?>
											<div class="infoBanner">
												<?=$styleText2?>
											</div>
										<?}?>
										<?if($styleText3 != ""){?>
											<div class="infoBanner">
												<?=$styleText3?>
											</div>
										<?}?>
									</div>
								<?}
								else{?>
									<div class="brandWrap">
										<div class="brandName">
											<?=$brand_name?>
										</div>
										<div class="orderPrice">
											<?php echo number_format($orderPrice)?>원
										</div>
									</div>
								<?}?>
								<div class="addrWrap">
									<?=$addr?>
									<br>
									<?=$rcvName?> 
									<?=$tel?> 
								</div>
								<?if($status == 1){?>
									<div class="infoWrap">
										<div class="progressIcon">
											<img src="/app/skin/basic/svg/proposalProgressTimeIcon.svg">
										</div>
										<div class="dateText">
											제안서 마감까지 남은 시간
										</div>
										<div class="proposalTimeWrap <?=$timeBgClass?>" setstamp="dcsm_<?=$aoidx?>" endstamp='<?=$enddateN?>'>
											<div id="dcsm_<?=$aoidx?>"></div>
										</div>
									</div>
								<?}?>
							</div>
						</div>
					<?
					}
					else if($status >= 4){
						$reviewStatus = "write";
						if($status == 5){
							$reviewStatus = "view";
						}
						$linkUrl = "proposal_detail.php?aoidx=".$aoidx;
					?>
						<div class="proposalClose" id="proposalClose<?=$aoidx?>">
							<div class="contentWrap">
								<div class="infoWrap">
									<div class="progressIcon">
										<img src="/app/skin/basic/svg/proposalCloseIcon.svg">
									</div>
									<div class="dateText" onclick="location.href='<?=$linkUrl?>'">
										<?=$receiveDateTimeStr?>
									</div>
									<div id="reviewBtn<?=$aoidx?>" class="proposalReviewBtn <?=$reviewStatus?>" value="<?=$aoidx?>" onclick="callReview(this);" status="<?=$reviewStatus?>" productcode="<?=$productcode?>" aoidx="<?=$aoidx?>">
									</div>
								</div>
								<div class="brandWrap" onclick="location.href='<?=$linkUrl?>'">
									<div class="brandName">
										<?=$brand_name?>
									</div>
									<div class="orderPrice">
										<?php echo number_format($orderPrice)?>원
									</div>
								</div>
								<div class="addrWrap" onclick="location.href='<?=$linkUrl?>'">
									<?=$addr?>
									<br>
									<?=$rcvName?> 
									<?=$tel?> 
								</div>
							</div>
						</div>
					<?
					}
					else if((!$orderStatus && $status == 1) || $status == 2) {
					?>
						<div class="proposalClose" onclick="location.href='proposals.php?aoidx=<?=$aoidx?>'">
							<div class="contentWrap">
								<div class="infoWrap">
									<div class="progressIcon">
										<img src="/app/skin/basic/svg/proposalCloseIcon.svg">
									</div>
									<div class="dateText">
										<?=$receiveDateTimeStr?>
									</div>
								</div>
							</div>
							<div class="overWrap">
								<div class="cancelLine"></div>
							</div>
						</div>
					<?
					}
					?>
			<?
				}
			}
			else{
			?>
			<div style="text-align:center">
				<h2 style="margin-top:40px">진행중인 주문이 없습니다.</h2>
			</div>
			<?
			}
			?>
		</div>
		<!-- 종합 END -->
	</div>
	<!-- 주문정보 END -->
	<script type="text/javascript">
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

		var timeText = "";
		sec = "초 ";
		min = "분 ";
		hr = "시간 ";
		dy = "일 ";
		
		if(daysRound == 0 && hoursRound < 1){
			timeText = "<span>" + minutesRound + "</span>" + min + "<span>" + secondsRound + "</span>"  + sec;
		}
		else if(daysRound == 0){
			timeText = "<span>" + hoursRound + "</span>"  + hr + "<span>" + minutesRound + "</span>"  + min;
		}
		else{
			timeText = "<span>" + daysRound + "</span>"  + dy + "<span>" + hoursRound + "</span>"  + hr;
		}

//		$("#"+va1).html(daysRound + dy + hoursRound + hr + minutesRound + min + secondsRound + sec);
//		$("#"+va1).html(hoursRound + hr + minutesRound + min + secondsRound + sec);
		$("#"+va1).html(timeText);
	}

	function intCountdown(){
		$('.proposalTimeWrap').each(function(idx,el){
			dday = $(el).attr('endstamp');
			sid = $(el).attr('setstamp');
			reverse_counter(sid, dday);

		});
		setTimeout("intCountdown()", 1000);
	}

	$(function(){
		intCountdown();
	});
	
	function callReview(obj){
		var productcode = $(obj).attr("productcode");
		var aoidx = $(obj).attr("aoidx");
		var status = $(obj).attr("status");
		iframePopupOpen('/app/prreview_' + status + '_pop.php?productcode=' + productcode + '&aoidx=' + aoidx)
	}
	function reviewProc(aoidx,productcode){
		$("#reviewBtn"+aoidx).attr("status","view");
		$("#reviewBtn"+aoidx).removeClass("write");
		$("#reviewBtn"+aoidx).addClass("view");
		iframePopupClose();
	}
	//-->

	function setCancelOrderEvent(aoidx) {
		if(confirm("※선택하신 스페셜오더를 취소할 경우 복구가 불가능합니다.\n\※선택하신 스페셜오더를 취소하시겠습니까?")) {
			document.form1.mode.value="cancel";
			document.form1.aoidx.value=aoidx;
			document.form1.action="<?=$_SERVER[PHP_SELF]?>";
			document.form1.submit();
		}
	}
	</script>
</div>
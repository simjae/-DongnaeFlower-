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
					,vs.brand_name
					,DATE_FORMAT( CONCAT( ao.receiveDate, ' ', ao.receivetime ) , '%Y-%m-%d %H:%i' ) AS receiveDateTime
					,DATE_FORMAT( CONCAT( ao.closeDate, ' ', ao.closetime ) , '%Y-%m-%d %H:%i' ) AS closeDateTime
					,DATE_FORMAT( CONCAT( ao.closeDate, ' ', ao.closetime ) , '%Y-%m-%d %H:%i:%s' ) >= NOW() AS orderStatus
					,(SELECT COUNT(aop01.aopidx) FROM auction_order_proposal AS aop01 WHERE aop01.aoidx = ao.aoidx AND aop01.userid = '".$_ShopInfo->getMemid()."' AND aop01.del_flag=false) AS proposalCount
					,(SELECT COUNT(aop02.aopidx) FROM auction_order_proposal AS aop02 WHERE aop02.aoidx = ao.aoidx AND aop02.userid = '".$_ShopInfo->getMemid()."' AND aop02.del_flag=false AND aop02.updateDate > ao.chkDate) unchkCount
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
					if($unchkCount > 0){
						$bellOn = "_on";
					}
					else{
						$bellOn = "_off";
					}
					
					if(($orderStatus && $status == 1) || $status == 3){
						if($status == 3){
							$linkUrl = "proposal_detail.php?aoidx=".$aoidx;
						}
						else{
							$linkUrl = "proposals.php?aoidx=".$aoidx;
						}
						$statusText = "상품준비중";
					?>
						<div class="proposalProgress" onclick="location.href='<?=$linkUrl?>'">
							<div class="contentWrap">
								<div class="infoWrap">
									<div class="progressIcon">
										<img src="/app/skin/basic/svg/proposalProgressIcon.svg">
									</div>
									<div class="dateText">
										<?=$receiveDateTimeStr?>
									</div>
									<?if($status == 1){?>
										<div class="proposalCountWrap <?=$bellOn?>" value="<?=$proposalCount?>">
											<img src="/app/skin/basic/svg/proposalProgresBellIcon<?=$bellOn?>.svg">
										</div>
									<?}
									else{?>
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
										<div class="proposalTimeWrap" setstamp="dcsm_<?=$aoidx?>" endstamp='<?=$enddateN?>'>
											<div id="dcsm_<?=$aoidx?>"></div>
										</div>
									</div>
								<?}?>
							</div>
						</div>
					<?
					}
					else if($status >= 4){
						$btnClass = "write";
						$linkScript = "iframePopupOpen('/app/prreview_write_pop.php?productcode=".$productcode."')";
						if($status == 5){
							$btnClass = "view";
						}
					?>
						<div class="proposalClose" onclick="<?=$linkScript?>">
							<div class="contentWrap">
								<div class="infoWrap">
									<div class="progressIcon">
										<img src="/app/skin/basic/svg/proposalCloseIcon.svg">
									</div>
									<div class="dateText">
										<?=$receiveDateTimeStr?>
									</div>
									<div class="proposalReviewBtn <?=$btnClass?>" value="<?=$aoidx?>">
									</div>
								</div>
								<div class="brandWrap">
									<div class="brandName">
										<?=$brand_name?>
									</div>
									<div class="orderPrice">
										<?php echo number_format($orderPrice)?>원
									</div>
								</div>
								<div class="addrWrap">
									<?=$addr?>
									<br>
									<?=$rcvName?> 
									<?=$tel?> 
								</div>
							</div>
						</div>
					<?
					}
					else if(!$orderStatus && $status == 1){
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
	
	//-->
	</script>
</div>
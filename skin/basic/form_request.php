<style>
.order_wrap .order_group{background:#f4f4f4;padding:20px;margin-top:8px;border-radius:4px;}
.order_wrap h2{padding-bottom:10px;margin-bottom:10px;border-bottom:solid 1px #e0e0e0;}
</style>
<div id="content">
	<div class="h_area2">
		<h2>
			<div class="mini ui buttons">
			  <div class="mini ui button" style="font-weight:400;background:#ffffff;color:#777777;border:1px solid #333333;" onclick="location.href='talk_request.php'">1:1 채팅 주문</div>
			  <div class="mini ui button" style="font-weight:400;background: #118d45;color:#ffffff;">주문서 주문</div>
			</div>
		</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<!-- 주문결제 -->
	<input type="hidden" name="msg_type" value="1" />
	<input type="hidden" name="addorder_msg" value="" />
	<input type="hidden" name="orderType" value="1" />

	<div class="order_wrap">
			
		<!-- 배송일시 START -->
		<div class="order_group">
			<h2>언제까지 필요하신가요?</h2>
			<?
				$times = mktime();
				//시작시간
				$startHour = 10;
				//종료시간
				$lastHour = 20;
				//주문가능 시간차
				$gapHour = 5;
				//카렌더 검색가중치
				$gapDay = 0;
				$todayDate = date("Y-m-d", $times+(3600*$gapHour)); 
				$realHour = date("H", $times);
				$nowHour = $realHour + $gapHour;
				if ($nowHour < $startHour){
					$nowHour = $startHour;
				}
				else if($nowHour  >= $lastHour){
					$nowHour = $startHour;
					$todayDate = date("Y-m-d", $times+(3600*24)); 
					$gapDay = 1;
				}
			?>
			<table cellpadding="0" cellspacing="0" border="0" class="order_table">
				<tr>
					<td class="order_deli_type1" style="width:50%">
						<INPUT type="text" name="receiveDate" id="receiveDate" maxLength="10" class="basic_input receiveDate" placeholder="날짜(예 <?=date($todayDate)?>)" value="<?=date($todayDate)?>" readonly/>
					</td>
					<td style="width:50%;text-align:right;">
						<select name="receiveTime" class="basic_input receiveTime">
							<?for( $i=$nowHour ; $i<20 ; $i++ ){
								$time_str01 = $i . ':00';
							?>
								<option value="<?=$time_str01?>"  <? if($i == $nowHour){ echo "selected"; } ?>><?=$time_str01?></option>
							<?}?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<!-- 배송일시 END -->

		<!-- 주소입력 START -->
		<div class="order_group">
			<h2>꽃이 필요한 지역이 어디신가요?</h2>
			<h4>가능한 플로리스트들을 찾기 위해 주소를 입력해 주세요</h4>
			<div id="addr_group">
				
			</div>
			<table cellpadding="0" cellspacing="0" border="0" class="order_table">
				<tr>
					<td style="width:50%">
						<button type="button" onClick="resetAddr()" class="basic_button grayBtn" style="vertical-align:top;width:100%">초기화</button>
					</td>
					<td style="width:50%">
						<button type="button" onClick="ReceiverShow()" class="basic_button grayBtn" style="vertical-align:top;width:100%;color: #ffffff;border: solid 1px #118d45;background: #118d45;">주소 추가</button>
					</td>
				</tr>
			</table>
		</div>
		<!-- 주소입력 END -->
		

		<!-- 용도/종류 END -->
		<div class="order_group">
			<h2>꽃의 용도와 원하는 종류는 무엇인가요?</h2>
			<h4>특별한 날 어떤 꽃을 윈하시는지 알려주세요</h4>
			<table cellpadding="0" cellspacing="0" border="0" class="order_table">
				<tr>
					<td class="order_deli_type1" style="width:50%">
						
						<select name="purpose" class="basic_input purpose">
						<?
							$sql = "SELECT * FROM item_mst WHERE keyText='purpose' ";
							//echo $sql;
							$result1=mysql_query($sql,get_db_conn());
							while($row1=mysql_fetch_object($result1)) {
						?>
								<option value="<?=$row1->seq?>"><?=$row1->valText?></option>
						<?	}
							mysql_free_result($result1);?>
						</select>
					</td>
					<td style="width:50%;text-align:right;">
						<select name="productType" class="basic_input productType">
						<?
							$sql = "SELECT * FROM item_mst WHERE keyText='productType' ";
							//echo $sql;
							$result2=mysql_query($sql,get_db_conn());
							while($row2=mysql_fetch_object($result2)) {
						?>
								<option value="<?=$row2->seq?>"><?=$row2->valText?></option>
						<?	}
							mysql_free_result($result2);?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<!-- 용도/종류 END -->

		<!-- 가격대 START -->
		<div class="order_group">
			<h2>원하는 가격대와 스타일을 선택해 주세요</h2>
			<h4>취향에 맞는 꽃을 추천해 드릴께요</h4>
			<table cellpadding="0" cellspacing="0" border="0" class="order_table">
				<tr>
					<td style="width:50%">
						<select name="priceRange" class="basic_input priceRange">
						<?
							$sql = "SELECT * FROM item_mst WHERE keyText='priceRange' ";
							//echo $sql;
							$result3=mysql_query($sql,get_db_conn());
							while($row3=mysql_fetch_object($result3)) {
						?>
								<option value="<?=$row3->seq?>"><?=$row3->valText?></option>
						<?	}
							mysql_free_result($result3);?>
						</select>
					</td>
					<td style="width:50%;text-align:right;">
						<select name="style" class="basic_input style">
						<?
							$sql = "SELECT * FROM item_mst WHERE keyText='style' ";
							//echo $sql;
							$result4=mysql_query($sql,get_db_conn());
							while($row4=mysql_fetch_object($result4)) {
						?>
								<option value="<?=$row4->seq?>"><?=$row4->valText?></option>
						<?	}
							mysql_free_result($result4);?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<!-- 가격대 END -->
		
		<!-- 코멘트 START -->
		<div class="order_group">
			<h2>플로리스트에게 남길 메모를 작성해주세요</h2>
			<textarea name="comment" class="comment"></textarea>
		</div>
		<!-- 코멘트 END -->
		
		<!-- 버튼 -->
		<section class="basic_btn_area">
			<!-- <? if($row_cfg[use_bank]!="Y" && $row_cfg[use_creditcard]!="Y" && $row_cfg[use_mobilephone]!="Y") { ?>
			<a href="javascript:alert('결제방식이 설정되어있지 않습니다. 관리자에게 문의하세요~')" class="button black bigrounded">결제하기</a>
			<? } else { ?>
			<a href="javascript:CheckForm();" class="button black bigrounded">결제하기</a>
			<? }?>
			<a href="javascript:ordercancel('cancel');" class="button white bigrounded">주문취소</a> -->

			<a href="javascript:ordercancel('cancel');" class="basic_button">주문취소</a>
			<a href="javascript:CheckForm();" class="basic_button greenBtn">신청하기</a>
		</section>
		<!-- //버튼 -->
	</div>
	<!-- 주문정보 END -->

	
	<script type="text/javascript">

	//생년월일 달력 처리
	$(function() {
		$("#receiveDate").datepicker({
			dateFormat: 'yy-mm-dd',
			prevText: '이전 달',
			nextText: '다음 달',
			monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNames: ['일','월','화','수','목','금','토'],
			dayNamesShort: ['일','월','화','수','목','금','토'],
			dayNamesMin: ['일','월','화','수','목','금','토'],
			showMonthAfterYear: true,
			changeMonth: true,
			changeYear: true,
			minDate: '+<?=$gapDay?>d',
			yearRange: "-100:+0"	
		});
	});
	//-->
	</script>
</div>
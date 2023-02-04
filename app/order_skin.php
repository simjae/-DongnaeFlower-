<input type="hidden" name="msg_type" value="1" />
<input type="hidden" name="addorder_msg" value="" />
<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>" />
<script>

	var settings = {
						loop: true,
						pagination: {
							el: '.swiper-page-num',
							type: 'fraction'
						}
					}
	
	$(document).ready(function() {
		var orderSwiperObj = new Swiper('.image_wrap', settings);
		$('#phoneNum').keyup(function(e){
			$(this).val(autoHypenPhone( $(this).val() ));  
		});	
	});
</script>
<style>
.btm_s_1{
	border-bottom:1px solid #9e9e9e66;
}
.group_pd{
	padding: 0 32px;
	margin-bottom: 20px;
}
.dfx{
	display:flex;
}
.flex_sbw{
	display: flex;
	justify-content: space-between;
}
.pmSectionTitle{
	color: #282828;
    font-size: 1.2em;
    font-weight: 900;
    padding: 20px 0;
}
.pmSectionSubTitle{
	color: #282828;
    font-size: 1.1em;
    font-weight: 400;
    padding: 5px 0;
    text-align: left;
}
.pmSectionContent{
	color: #282828;
    font-size: 1.1em;
    font-weight: 500;
	padding: 5px 0;
	text-align: right;
}
.cardBtn{
	padding: 2px 8px;
    background-color: #f0f0f0;
    border-radius: 20px;
    font-size: 0.9em;
    color: #464646;
    font-weight: 500;
}
.pmBtn{
	color: #ffffff;
    font-size: 1.4em;
    font-weight: 900;
    text-align: center;
    height: 40px;
    padding: 20px;
    position: fixed;
    bottom: 122px;
    z-index: 900;
    width: calc(100vw - 40px);
    bottom: 0;
    height: calc(env(safe-area-inset-bottom) + 65px);
    height: calc(constant(safe-area-inset-bottom) + 65px);
    background-color: #e51e6e;
}
.payTypeBtn{
	border: solid 1px #a0a0a0;
    border-radius: 30px;
    padding: 4px 20px;
    color: #282828;
    font-weight: 400;
}
.selectpay{
	color: #ffffff;
	background-color: #464646;
	border: solid 1px #464646;
}
input[name=sel_paymethod]{
	display: none;
}
.unUsedFont{
	color: #e61e6e;
	font-size: 1.0em;
	font-weight: 900;
	margin-left: 5px;
}
.unUsedPoint{
	border-radius: 20px;
    border: 1px solid #a0a0a0;
    width: 100px;
}
.totalPriceFont{
	color: #e61e6e;
    font-size: 1.3em;
    font-weight: 900;
    text-align: right;
    padding: 5px 0;
}
.currentPriceFont{
    color: #e61e6e;
    font-size: 1.2em;
    font-weight: 900;
    padding: 5px 0;
}
.NoticeGroup{
	padding: 20px 0 50px 0px;
    background-color: #f3f3f2;
    text-align: center;
    font-size: 1em;
}
.order_table input{
	color: #282828;
    font-weight: 500;
    border-radius: 20px;
    border: 1px solid #a0a0a0;
    width: 100px;
}
.order_textarea{
	color: #282828;
    font-weight: 500;
    border-radius: 20px;
    border: 1px solid #a0a0a0;
    width: 355px;
}
.order_wrap .delivery_title{
	margin: 0px;
	padding: 0px;
}
#limitedCouponName{
	margin: 10px 0;
	background-color: #f5f5f5;
    border-radius: 30px;
    color: #282828;
    font-size: 1.0em;
    font-weight: 400;
}
#unlimitedCouponName{
	margin: 10px 0;
	background-color: #f5f5f5;
    border-radius: 30px;
    color: #282828;
    font-size: 1.0em;
    font-weight: 400;
}
.coutponList{
	width: 95%;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    word-wrap: break-word;
    line-height: 1.2em;
    height: 1.1em;
}
.couponRemoveBtn{
	background-color: #ffffff;
    border-radius: 50%;
    width: 15px;
    text-align: center;
	margin-right: 10px;
    font-size: 1.0em;
}
</style>
<div class="order_wrap" style="margin: 0px">
	<!-- 주문상품 출력 START -->
			<div class="order_pr_info">
			<?
				$couponable = false;
				$reserveuseable = false;
				$productRealPrice = 0;

				if($basketItems['productcnt'] <1){ ?> 
				<div style="height:30px;">등록된 상품이 없습니다.</div>
			<?
				}else{
					$timgsize = 80;
					foreach($basketItems['vender'] as $vender=>$vendervalue){

						for($i=0;$i<count($vendervalue['products']);$i++){
							$product = $vendervalue['products'][$i];

							if(!$couponable && $product['cateAuth']['coupon'] == 'Y'){
								$chkcoupons = array();
								$chkcoupons = getMyCouponList($product['productcode']);
								if(_array($chkcoupons)) $couponable = true;
							}

							if(!$reserveuseable && $product['cateAuth']['reserve'] == 'Y') $reserveuseable = true;
			?>
		<div class="btm_s_1">
			<div class="group_pd">
				<div class="pmSectionTitle">주문 상품 확인</div>
				<div class="order_pr_loop" style="margin: 0px;">
					<div class="image_wrap">
						<div class="swiper-wrapper">
							<?
							$imageSQL="SELECT cont FROM product_multicontents WHERE pridx='".$product['pridx']."' ORDER BY midx";
							$imageRes = mysql_query($imageSQL,get_db_conn());
							
							while($imageRow = mysql_fetch_object($imageRes)){
								$background_url = "/data/shopimages/product/".$imageRow->cont;
								echo "<div class='swiper-slide' style=\"width:100%;height:auto;min-height:100px;border:none;padding:0px;background:url('".$background_url."') no-repeat;background-position:center;background-size:cover;\" onclick=\"ImageShow('".((strlen($imageRow->cont)>0) ? "/data/shopimages/product/".$imageRow->cont : "/images/no_img.gif")."')\"></div>\n";

							}
							?>
						</div>
						<div class="swiperPageWrap">
							<div class="swiper-page-num"></div>
						</div>
					</div>
					<?
						$receiveDateTime = $product['receiveDateTime'];
						$receiveDateArr = explode("-",$product['receiveDate']);
						$receiveDateTimeStr = $receiveDateArr[0]."년 "
						.$receiveDateArr[1]."월 "
						.$receiveDateArr[2]."일 "
						.$product['receiveTime'];
						$receiveType = $product['receiveType'];
						if($receiveType==0){
							$addr = str_replace("="," ",$product['addr']);
							$addr = explode("주소 :",$product['addr']);
						}
						else{
							$addr = $vendervalue['conf']['com_addr'];
						}
						$prodNum = $product['prodNum'];
						$receiveTypeText = $product['receiveTypeText'];
						$purposeText = $product['purposeText'];
						$productTypeText = $product['productTypeText'];
						$priceText = $product['priceText'];
						$styleText = $product['styleText'];
						$aoidx = $product['aoidx'];
						$rcvName = $product['rcvName'];
						$tel = $product['tel'];
						$orderType = "스페셜 오더";
						if($aoidx==""){
							$orderType = "마감 할인";
						}
					?>
					<div class="orderRightInfo" style="width: 62%;margin-top: 12px;">
					<!-- -->
						<div class="flex_sbw">
							<div class="pmSectionSubTitle">주문 방식</div>
							<div class="pmSectionContent"><?=$orderType?></div>
						</div>
						<div class="flex_sbw">
							<div class="pmSectionSubTitle">꽃집 이름</div>
							<div class="pmSectionContent">
							<?
							if($vender > 0) {
								echo $vendervalue['conf']['com_name'];
							}
							?>
							</div>
						</div>
						<div class="flex_sbw">
							<div class="pmSectionSubTitle">배송 방식</div>
							<div class="pmSectionContent">
							<?
								if($product['receiveType']=="0"){
									echo "입력 주소지로 배달";
								}
								else{
									echo "꽃집 픽업";
								}
							?>
							</div>
						</div>
						<div class="flex_sbw">
							<div class="pmSectionSubTitle">상품 가격</div>
							<div class="currentPriceFont"><?=number_format($product['realprice'])?>원</div>
						</div>
					<!-- -->
					</div>
				</div>
			</div>
		</div>
		<div class="btm_s_1">
			<div class="group_pd">
				<div class="rcvInfo" style="margin: 0px;">
					<table>
						<div class="pmSectionTitle">주소 확인</div>
						<div>
							<div class="flex_sbw">
								<div class="pmSectionSubTitle">
							<?		
									if($receiveType==0){
										echo "배달 주소";
									}
									else{
										echo "픽업 주소";
									}
							?>
								</div>
								<div class="pmSectionContent" style="width: 70%;text-align: end;">
							<?			
									if($receiveType==0){
										echo $addr[1];
									}else{
										echo $addr;
									}
							?>
								</div>
							</div>
							<div class="flex_sbw">
								<div class="pmSectionSubTitle">받으시는 분</div>
								<div class="pmSectionContent">
									<span>
										<?	
										if($receiveType==0){
											$tel1 = substr($tel, 0, 3); 
											$tel2 = substr($tel, 3, 4); 
											$tel3 = substr($tel, 7, 4); 
											echo $rcvName." ".$tel1." ".$tel2." ".$tel3;
										}else{
											$result = str_replace("-"," ",$mobile);
											echo $name." ".$result;
										}
										?>
									</span>
								</div>
							</div>
						</div>
					</table>
				</div>
			</div>
		</div>
					
			<?			}// end for
					} // end foreach
				} // end if
			?>
	</div>
	<!-- 주문상품 출력 END -->

	<!-- 주문정보 START -->
	<!-- 주문자 정보 입력 START -->
	<div class="delivery_title" style="display: none;">
		<h2>주문자 정보</h2>
		<? if (!_empty($_ShopInfo->getMemid())) { ?>
		<div class="btn_defaultinfo"><label><input type="checkbox" name="ord_info_save" value="Y"/> 기본 정보로 저장</label></div>
		<? } ?>
	</div>
	<div class="order_info_table_wrap" style="display: none;">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
			<tr>
				<td>
					<input type="text" name="sender_name" value="<?=$name?>" placeholder="보내는 사람" class="basic_input" style="width:100%" />
				</td>
			</tr>
			<tr>
				<td>
					<input type="tel" name="sender_tel" value="<?=$mobile?>" maxlength="15" placeholder="주문자 연락처" id="phoneNum" class="basic_input" style="width:100%" />
				</td>
			</tr>
			<tr>
				<td>
					<input type="email" name="sender_email" id="o_email" maxlength="80" value="<?=$email?>" placeholder="이메일 주소" class="basic_input" style="width:100%" />
				</td>
			</tr>
			<tr>
				<td>
				<textarea name="order_prmsg" id="o_text" class="order_textarea" rows="5" placeholder="배송 요청 사항"></textarea></td>
			</tr>
		</table>
	</div>
	<!-- 주문자 정보 입력 END -->
	<!-- 비회원 개인정보 수집동의 START -->
	<? if(strlen($_ShopInfo->getMemid()) <= 0){ ?>
		<div class="delivery_title">
			<h2>비회원 개인정보 수집동의</h2>
		</div>
		<div class="persnal_info_wrap">
			<div class="persnal_clause">
				<?=strip_tags($privercybody, "<p>")?>
			</div>
		</div>
		<div class="persnal_clause_btn_wrap" style="margin-bottom:50px">
			<input type="radio" class="radio" id="idx_dongiY" name="dongi" value="Y" /><label for="idx_dongiY" class="clause_btn_true">동의함</label>&nbsp;&nbsp;&nbsp;
			<input type="radio" class="radio" id="idx_dongiN" name="dongi" value="N" /><label for="idx_dongiN" class="clause_btn_false">동의 안함</label>
		</div>
	<? } ?>
	<!-- 비회원 개인정보 수집동의 END -->

	<!-- 구매시 혜택 사항 START -->
	<?
		if(substr($ordertype,0,6)!= "pester" && $socialshopping != "social" && !_empty($_ShopInfo->getMemid()) && (($reserveuseable && $okreserve > 0 && ($user_reserve -$_data->reserve_maxuse) > 0) || (($_data->coupon_ok=="Y" && checkGroupUseCoupon()) || $couponable))){
			if ($_data->reserve_maxuse>=0 && $user_reserve!=0) {
				if($okreserve<0){
					$okreserve=(int)($sumprice*abs($okreserve)/100);
					if($reserve_maxprice>$sumprice) $okreserve=0;
					else if($okreserve>$user_reserve) $okreserve=$user_reserve;
				}
			}
			if($_data->reserve_maxuse > $user_reserve) $okreserve = 0;
			else $okreserve = min($okreserve,$basketItems['reserve_price']);
		if( ($_data->coupon_ok=="Y" && checkGroupUseCoupon() && $couponable) OR ($reserveuseable && $okreserve > 0 && ($user_reserve -$_data->reserve_maxuse) > 0) ) {
	?>
	<div class="btm_s_1">
		<div class="group_pd">
			<div class="delivery_title">
				<div class="pmSectionTitle">할인쿠폰</div>
				<div class="order_benefit_wrap">
					<table cellpadding="0" cellspacing="0" border="0" class="order_table">
						<!-- 쿠폰 적용 사항 START -->
						<tr>
							<td style="padding: 0px;">
								<div class="flex_sbw">
								<input type="hidden" name="coupon_price" id="coupon_price" class="basic_input" maxlength="8" value="0" readonly="readonly" />
									<div>
										<span class="pmSectionSubTitle">사용가능 쿠폰</span>
										<?
										$sql = "SELECT
													ci.coupon_code,
													ci.coupon_name,
													ci.sale_type,
													ci.sale_money,
													ci.bank_only,
													ci.productcode,
													ci.mini_price,
													ci.use_con_type1,
													ci.use_con_type2,
													ci.use_point,
													cis.date_start,
													cis.date_end 
													FROM
														tblcouponinfo ci
														LEFT JOIN tblcouponissue cis ON
														ci.coupon_code = cis.coupon_code
													WHERE
														cis.id='social_K_1891458663' AND
														cis.date_start<='".date("YmdH")."' AND
														(cis.date_end>='".date("YmdH")."' OR cis.date_end='') AND
														cis.used='N'";
										// echo $sql;
										$result = mysql_query($sql,get_db_conn());
										$total_count = mysql_num_rows($result);
										?>
										<span class="unUsedFont"><?=$total_count?></span>
									</div>
									<div>
										<span onclick="coupon_check()" class="cardBtn">쿠폰 선택</span>
									</div>
								</div>
								<div id="limitedCouponName"></div>
								<div id="unlimitedCouponName"></div>
							</td>
						</tr>
						<!-- 쿠폰 적용 사항 END -->
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="btm_s_1">
		<div class="group_pd">
			<div class="delivery_title">
				<div class="order_benefit_wrap">
					<table cellpadding="0" cellspacing="0" border="0" class="order_table">
						<!-- 적립금 적용 사항 START -->
						<tr>
							<?
								if($reserveuseable){
									if($_data->reserve_maxprice <= $basketItems['sumprice']+$basketItems['sumpricevat']){ //20150318 적립금 기준금액 측정 조건식 수정

										$okreserve = $okreserve + $basketItems['deli_price'];
										if($okreserve > 0 && $user_reserve - $_data->reserve_maxuse >= 0){
							?>
								<td class="order_benefit_type2">
									<div class="pmSectionTitle">
										적립금
										<span style="color: #969696;font-weight:500;font-size:0.7em;margin-left:10px;">* 적립금은 1,000원 이상부터 사용 가능합니다!</span>
									</div>
									<div class="flex_sbw">
										<div>
											<span class="pmSectionSubTitle">잔여 적립금</span>
											<span class="unUsedFont"><?=number_format($user_reserve)?>원</span>
										</div>
										<div>
											<input type="hidden" name="okreserve" value="<?=$okreserve?>" />
											<input dir="rtl" class="unUsedPoint" name="usereserve" id="usereserve" type="text" style="color: #282828;font-weight: 500;border-radius: 20px;border: 1px solid #a0a0a0;text-indent: 5px;width: 100px;value="0"  <?=($okreserve<1)?'disabled="disabled"':''?>">
											원
										</div>
									</div>
								</td>
							<? }else{ ?>
								<td>보유적립금 <span class="order_special_char"><?=number_format($_data->reserve_maxuse)?>원</span> 이상 일 경우 사용가능합니다.</td>
							<?
										}
									}else{
										echo "<td>적립금 사용 조건에 해당되지 않습니다.</td>";
									}
								}else{
							?>
								<td>적립금 사용 조건에 해당되지 않습니다.</td>
							<? } ?>
						</tr>
						<!-- 적립금 적용 사항 END -->
					</table>
				</div>
			</div>
		</div>
	</div>
	<?}?>

	<?
		if(!$reserveuseable ||  $okreserve <= 0 || ($user_reserve -$_data->reserve_maxuse) < 0) {
	?>
		<input type="hidden" name="oriuser_reserve" class="st02_1" maxlength="8" value="<?=number_format($user_reserve)?>" />
		<input type="hidden" name="usereserve" value="0" />
	<?
		}
	}else{
	?>
		<input type="hidden" name="usereserve" id="usereserve" value="0" />
	<?
		}
	?>
	<?
		if(!_empty($_ShopInfo->getMemid()) && $_data->coupon_ok !="Y" || !$couponable) {
	?>
		<span id="disp_coupon" style="display:none">0</span>
	<?
		}
	?>
	<!-- 구매시 혜택 사항 END -->

	<!-- 사은품 적용 사항 START -->
	<?
		if( $giftInfoSetArray[0] == "C" OR ( $giftInfoSetArray[0] == "M" AND !_empty($_ShopInfo->getMemid()) ) ){
	?>
	<div id="giftSelectArea">
		<h2>구매 사은품 </h2>
		<div class="freegift_wrap">
			<input type="hidden" name="gift01" id="gift01" class="mobile_input" maxlength="8" readonly value="<?=$basketItems['gift_price']?>" />
			<table cellpadding="0" cellspacing="0" class="freegift_table">
				<tr>
					<td>
						<div id="noGiftOptionArea">선택 가능한 사은품이 없습니다.</div>

						<table cellpadding="0" cellspacing="0" border="0" width="100%" id="giftOptionBox" style="display:none">
							<tr>
								<td class="freegift_image_area"><img src="/images/no_img.gif" id="gift_img" height="50" /></td>
							</tr>
							<tr>
								<td style="text-align:left;" class="freegift_choice">
									<select name="giftval_seq">
										<option value="">사은품 선택</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<div id="giftOptionArea">
										<table class="noborder" cellpadding="0" cellspacing="0">
											<tr>
												<td>옵션1</td>
												<td><select name="giftOpt1"></select></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="lastTD"><input type="text" name="gift_msg" class="basic_input freegift_sendmsg" maxlength="50" disabled="disabled" placeholder="사은품 관련 요청사항 입력(50자)" /></td>
				</tr>
			</table>
		</div>
	</div>
	<? } ?>

	<!-- 사은품 적용 사항 END -->
	<? if($sumprice>0 && !_empty($group_type)){ ?>
	<div class="order_group_wrap">
	<h2>회원등급 정책</h2>
		<?=$groupMemberSale?>
	</div>
	<? } ?>

	<!-- 결제 방법 선택 START -->
	<?
		$arrpayinfo=explode("=",$_data->bank_account);

		$arrbankaccount=explode(",",$arrpayinfo[0]);
		$arrpaynum=count($arrbankaccount);
	?>
	<div class="btm_s_1" id="orderPaySelt">
		<div class="group_pd">
			<div class="payment_type_wrap" id="orderPaySel">
				<div class="pmSectionTitle" style="text-align: left;">결제수단</div>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="paytype_list">
							<?
								$paytype_sql = "SELECT use_bank, use_creditcard, use_transferaccount, use_virtualaccount FROM tblmobileconfig";
								$paytype_result = mysql_query($paytype_sql, get_db_conn());
								$paytype_row = mysql_fetch_object($paytype_result);
								
								$usepg_sql = "SELECT pg_use FROM tblmobilepg WHERE pg_section = 'mobile' ";
								$usepg_result = mysql_query($usepg_sql, get_db_conn());
								$usepg_row = mysql_fetch_object($usepg_result);
								
								if($paytype_row->use_bank == 'Y'){
							?>
							
							<input type="radio" id="paytype_1" class="paytype" name="sel_paymethod" value="B" onClick="change_paymethod(1);"><label for="paytype_1" style="display:none"><span class="payTypeBtn btn_paytype paytype_1" onClick="paymentControl('bankaccount');">무통장</span></label>

							<?
								}
					//			if(strlen($_data->card_id) > 0){
									if($paytype_row->use_creditcard == 'Y' && $usepg_row->pg_use == 'Y'){
			
							?>
							<input type="radio" id="paytype_2" class="paytype" name="sel_paymethod" value="C" onClick="change_paymethod(2);"><label for="paytype_2"><span class="payTypeBtn btn_paytype paytype_2" onClick="paymentControl('creditcard');">신용카드</span></label>
							<?
									}
					//			}
								if(strlen($_data->trans_id)>0) {
									if($paytype_row->use_transferaccount == 'Y' && $usepg_row->pg_use == 'Y'){
							?>
							<input type="radio" id="paytype_3" class="paytype" name="sel_paymethod" value="V" onClick="showBankAccount('hide')"><label for="paytype_3"><span class="payTypeBtn btn_paytype paytype_3" onClick="paymentControl('transferaccount');">계좌이체</span></label>
							<?
									}
								}
		//						if(strlen($_data->virtual_id)>0) {
									if($paytype_row->use_virtualaccount == 'Y' && $usepg_row->pg_use == 'Y'){
							?>
							<input type="radio" id="paytype_4" class="paytype" name="sel_paymethod" value="V" onClick="showBankAccount('hide')"><label for="paytype_4"><span class="payTypeBtn btn_paytype paytype_4" onClick="paymentControl('virtualaccount');">가상계좌</span></label>
							<?
									}
		//						}
							?>
						</td>
					</tr>
					<tr id="pay_account_list" style="display:none">
						<td class="borderline">
							<table class="pay_account_table" cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td class="account_select_wrap">
										<span class="basic_select">
											<select name="pay_data1" id="pay_data1" style="width:100%" onChange="accountControl(this.value);">
												<option value="dont">선택하세요</option>
												<?
													//무통장
													if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<100000) {
														if(preg_match("/^(Y|N)$/", $_data->payment_type)) {//결제방법이 모든결제 OR 온라인결제가 선택되었을 경우
															if (strlen($arrpayinfo[0])>0) {
																$tok = strtok($arrpayinfo[0],",");
																$count = 1;
																while ($tok) {
																	$account_info = explode(" ",$tok);
																	$account_division = explode(":", $account_info[2]);
																	$account_holder = substr($account_division[1],0,-1);
												?>
																	<option value="<?=$tok?>" <?=($arrpaynum==1?($count==1?"selected":""):"")?>><?=$account_info[0]?></option>
												<?
																	$tok = strtok(",");
																	$count++;
																}
															}
														}
													}
													//무통장
													if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<100000) {
														if(preg_match("/^(Y|N)$/", $_data->payment_type)) {//결제방법이 모든결제 OR 온라인결제가 선택되었을 경우
															echo $pmethodlist[0];
														}
													}
												?>
											</select>
										</span>
									</td>
								</tr>
								<tr id="account_info_list" style="<?=($arrpaynum==1?"":"display:none;")?>">
									<td>
										<table cellpadding="0" width="100%" cellspacing="0" border="0" class="account_info_table">
											<colgroup>
												<col width="25%" />
												<col width="" />
											</colgroup>
											<tr>
												<th>계좌번호</th>
												<td id="pay_account"><?=($arrpaynum==1?$account_info[1]:"")?></td>
											</tr>
											<tr>
												<th>예금주</th>
												<td id="pay_holder"><?=($arrpaynum==1?$account_info[2]:"")?></td>
											</tr>
											<tr>
												<th>입금자명</th>
												<td><input type="text" name="bankname" class="basic_input mobile_input transfetname" value="" placeholder="입금자명이 다를경우 입력" /></td>
											</tr>
										</table>
									</td>
								</tr>

								<? if( $pgInfo["PG"] == "A" ){ //KCP사용중일 때 출력 ?>
								<tr>
									<td>
										<script language="javascript">
											function numkeyCheck(e) {
												var keyValue = event.keyCode;
												if( ((keyValue >= 48) && (keyValue <= 57)) ) return true;
												else return false;
											}

											function selectType(e) {
												if(e == "1"){
													$("#HP1").attr("readonly",false).attr("disabled",false);
													$("#HP2").attr("readonly",false).attr("disabled",false);
													$("#HP3").attr("readonly",false).attr("disabled",false);
													
													$("#Account1").attr("false",true).attr("disabled",true); 
													$("#Account2").attr("false",true).attr("disabled",true); 
													$("#Account3").attr("false",true).attr("disabled",true); 

													$("#HP1").css("background-color","#ffffff");
													$("#HP2").css("background-color","#ffffff");
													$("#HP3").css("background-color","#ffffff");
													
													$("#Account1").css("background-color","#e2e2e2");
													$("#Account2").css("background-color","#e2e2e2");
													$("#Account3").css("background-color","#e2e2e2");

												}else if(e == "2"){
													$("#HP1").attr("readonly",true).attr("disabled",true);
													$("#HP2").attr("readonly",true).attr("disabled",true);
													$("#HP3").attr("readonly",true).attr("disabled",true);
													
													$("#Account1").attr("false",false).attr("disabled",false); 
													$("#Account2").attr("false",false).attr("disabled",false); 
													$("#Account3").attr("false",false).attr("disabled",false); 

													$("#HP1").css("background-color","#e2e2e2");
													$("#HP2").css("background-color","#e2e2e2");
													$("#HP3").css("background-color","#e2e2e2");
													
													$("#Account1").css("background-color","#ffffff");
													$("#Account2").css("background-color","#ffffff");
													$("#Account3").css("background-color","#ffffff");

												}else{
													$("#HP1").attr("readonly",true).attr("disabled",true);
													$("#HP2").attr("readonly",true).attr("disabled",true);
													$("#HP3").attr("readonly",true).attr("disabled",true);
													
													$("#Account1").attr("false",true).attr("disabled",true); 
													$("#Account2").attr("false",true).attr("disabled",true); 
													$("#Account3").attr("false",true).attr("disabled",true); 

													$("#HP1").css("background-color","#e2e2e2");
													$("#HP2").css("background-color","#e2e2e2");
													$("#HP3").css("background-color","#e2e2e2");
													
													$("#Account1").css("background-color","#e2e2e2");
													$("#Account2").css("background-color","#e2e2e2");
													$("#Account3").css("background-color","#e2e2e2");
												}
											}

											$(function() {
												selectType('3');
												$(".inputs").keydown(function(e) {
													var charLimit = $(this).attr("maxlength");
													var keys = [8, 9, /*16, 17, 18,*/ 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 144, 145];

													if (e.which == 8 && this.value.length == 0) {
														$(this).prev('.inputs').focus();
													} else if ($.inArray(e.which, keys) >= 0) {
														return true;
													} else if (this.value.length >= charLimit) {
														$(this).next('.inputs').focus();
														return false;
													} else if (e.shiftKey || e.which <= 47 || e.which >= 106) {
														return false;
													} else if (e.shiftKey || (e.which >= 58 && e.which <= 95)) {
														return false;
													}
												}).keyup (function () {
													var charLimit = $(this).attr("maxlength");
													if (this.value.length >= charLimit) {
														$(this).next('.inputs').focus();
														return false;
													}
												});
											});
											
										</script>

										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td style="text-align:left">
													<h2 style="margin:20px 0px 10px 0px">현금영수증 신청</h2>
													<div id="AccountLayer_Receipt_Cash" style="padding: 3px 0px;">
														<ul class="noticetxt">
															<li style="margin-bottom: 10px;">
																<label style="cursor:pointer"><input name="NumType" onclick="selectType('0');" type="radio" value="0" style="width:20px;height:20px;vertical-align:middle;" checked /> 신청하지 않음</label>
															</li>
															<li style="margin-bottom: 20px;">
																<label><input name="NumType" onclick="selectType('1');" type="radio" value="1" style="width:20px;height:20px;vertical-align:middle;" /> 소득공제용 (휴대폰번호)</label>
																<div style="margin-top:5px">
																	<input name="HP1" class="basic_input" id="HP1" size="4" onKeyPress="return numkeyCheck(event);"  type="text" maxlength="3" />&nbsp;-&nbsp;
																	<input name="HP2" class="basic_input" id="HP2" size="4" onKeyPress="return numkeyCheck(event);" type="text" maxlength="4" />&nbsp;-&nbsp;
																	<input name="HP3" class="basic_input" id="HP3" size="4" onKeyPress="return numkeyCheck(event);" type="text" maxlength="4" />
																</div>
															</li>
															<li>
																<label><input name="NumType" onclick="selectType('2');" type="radio" value="2" style="width:20px;height:20px;vertical-align:middle;" /> 지출증빙용 (사업자번호)</label>
																<div style="margin-top:5px">
																	<input name="Account1" class="basic_input" id="Account1" size="4" onKeyPress="return numkeyCheck(event);" type="text" maxlength="3" />&nbsp;-&nbsp;
																	<input name="Account2" class="basic_input" id="Account2" size="4" onKeyPress="return numkeyCheck(event);" type="text" maxlength="2" />&nbsp;-&nbsp;
																	<input name="Account3" class="basic_input" id="Account3" size="4" onKeyPress="return numkeyCheck(event);" type="text" maxlength="5" />
																</div>
															</li>
														</ul>
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<? } ?>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div style="height: 10px;background: #f3f3f2;"></div>
	<!-- 총 결제내역  -->
	<div class="btm_s_1">
		<div class="group_pd">							
			<div class="delivery_title">
				<div class="pmSectionTitle">총 결제 내역</div>	
			</div>	
			<div>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<th class="pmSectionSubTitle">상품 가격</th>
						<td class="pmSectionContent"><span><?=number_format($sumprice+$sumpricevat)?></span>원</td>
					</tr>
					<tr>
						<th class="pmSectionSubTitle">배송비</th>
						<td class="pmSectionContent">
							<span id="disp_deliprice"><!-- <?//=number_format($basketItems['deli_price'])?> -->0</span>원
							<!-- <input type='hidden' name='disp_deliprice_temp' id='disp_deliprice_temp' value='0'> -->
						</td>
					</tr>
					<? if(!_empty($_ShopInfo->getMemid())){ ?>
						<? if($_data->coupon_ok =="Y" && $couponable){ ?>
							<tr>
								<th class="pmSectionSubTitle">할인쿠폰</th>
								<td class="pmSectionContent"><span id="disp_coupon">0</span>원</td>
							</tr>
						<? } ?>
						<tr>
							<th class="pmSectionSubTitle">적립금</th>
							<td class="pmSectionContent"><span id="disp_reserve">0</span>원</td>
						</tr>
					<? } ?>
					<tr>
						<th class="pmSectionSubTitle lastTH">최종 결제금액</th>
						<td class="lastTD totalPriceFont"><span id="disp_last_price"><?=number_format($basketItems['sumprice']+$basketItems['deli_price']+$basketItems['sumpricevat'])?></span>원</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div>
		<div class="NoticeGroup">
			<p>동네꽃집은 통신판매의 당사자가 아닌, 통신판매중개자입니다.</p>
			<p>따라서 상품 거래 정보 및 거래에 대하여 책임을 지지 않습니다.</p>
		</div>
	</div>
	<div class="pmBtn" onclick="javascript:CheckForm();" >
		<span id="pmBtn"><?=number_format($basketItems['sumprice']+$basketItems['deli_price']+$basketItems['sumpricevat'])?></span>원 결제하기
	</div>
	<!-- 버튼 -->
	<!-- <section class="basic_btn_area"> -->
		<!-- <? if($row_cfg[use_bank]!="Y" && $row_cfg[use_creditcard]!="Y" && $row_cfg[use_mobilephone]!="Y") { ?>
		<a href="javascript:alert('결제방식이 설정되어있지 않습니다. 관리자에게 문의하세요~')" class="button black bigrounded">결제하기</a>
		<? } else { ?>
		<a href="javascript:CheckForm();" class="button black bigrounded">결제하기</a>
		<? }?>
		<a href="javascript:ordercancel('cancel');" class="button white bigrounded">주문취소</a> -->

		<!-- <a href="javascript:ordercancel('cancel');" class="basic_button">주문취소</a> -->
		<!-- <a href="javascript:CheckForm();" class="basic_button orangeBtn">결제하기</a> -->
	<!-- </section> -->
	<!-- //버튼 -->
</div>
<!-- 주문정보 END -->

<Script>
	$(function(){
		$('select[name=giftval_seq]').change( function(){ resetGiftOptions();});
	});

	secGifts = "";
	function secGift(vls) {
		f = document.form1;

		$("gift_"+secGifts).style.display = "none";
		tmp = eval("f.img_"+secGifts);
		$("gift_img").src = tmp.value;

		$("gift_"+vls).style.display = "block";
		tmp = eval("f.img_"+vls);
		$("gift_img").src = tmp.value;

		secGifts = vls;
	}

	function accountControl(idx){
		var cainfo = false;
		if(idx != 'dont'){
			var account_info = idx.split(' ');

			if((account_info[2].charAt(0) == '(') ||((account_info[2].charAt(account_info[2].length - 1)) == ')')){
				var account_holder = account_info[2].substr(0,account_info[2].lastIndexOf(')')).slice(1).split(':');
				var holder = account_holder[1];
				cainfo = true;
			}
			if(cainfo != false){
				$('#account_info_list').removeAttr('style');
				$('#pay_account').text(account_info[1]);
				$('#pay_holder').text(holder);
			}
		}else{
			$('#account_info_list').css('display','none');
		}
	}
	function paymentControl(idx){
		switch(idx){
			case 'bankaccount':
				$('.btn_paytype').removeClass('selectpay');
				$('.paytype_1').addClass('selectpay');
				showBankAccount('show');
			break;
			case 'creditcard':
				$('.btn_paytype').removeClass('selectpay');
				$('.paytype_2').addClass('selectpay');
				showBankAccount('hide');
			break;
			case 'transferaccount':
				$('.btn_paytype').removeClass('selectpay');
				$('.paytype_3').addClass('selectpay');
				$('#pay_account_list').css('display','none');
			break;
			case 'virtualaccount':
				$('.btn_paytype').removeClass('selectpay');
				$('.paytype_4').addClass('selectpay');
				$('#pay_account_list').css('display','none');
			break;
		}
	}
	$('.clause_btn_true').click(function(){
		$('.clause_btn_false').removeClass('selected');
		$('.clause_btn_true').addClass('selected');
	});
	$('.clause_btn_false').click(function(){
		$('.clause_btn_true').removeClass('selected');
		$('.clause_btn_false').addClass('selected');
	});
	$(function(){
		$('select[name=giftval_seq]').change( function(){	resetGiftOptions();});
	//	$( "<div></div>" ).after( "<p></p>" ).addClass( "foo" ).filter( "p" ).attr( "id", "bar" ).html( "hello" ).end().appendTo( "body" );
	});

	// 적용 가능 사은품 가져 오기
	function giftchoices(gprice){
		var tempgprice = parseInt($('input[name=gift01]').val()); // 적용전 (현) 사은품 지급가능 구매금액
		gprice = parseInt(gprice); // 적용될 사은품 지급가능 구매금액
		var noGift = ($('input[name=possible_gift_price_used]').val() == 'N');
		if(!noGift){
			if(isNaN(gprice)) gprice = tempgprice;
			if(isNaN(gprice) || gprice < 1) gprice = 0;
		}else{
			gprice = 0;
		}

		// 사은품 지급가능 구매금액에 변동이 없고 사은품이 선택 되어 있을경우 사은품 변동 안함
		var index = $("select[name=giftval_seq] option").index( $("select[name=giftval_seq] option:selected") );
		if( tempgprice == gprice && index > 0 ) {
			return false;
		}

		$('input[name=gift01]').val(gprice);
		if(gprice >= mingiftprice){
			//if($('#giftSelectArea')) $('#giftSelectArea').css('display','');
			$.post( '/json_order.php',{'act':'getGife','gift_price':gprice},function(data){
				if(data.err == 'ok'){
					giftReset(data.items);
				}else{
					alert(data.err);
				}
			},'json');
		}else{
			if($('#giftSelectArea')) $('#giftSelectArea').css('display','none');
			$('#noGiftOptionArea').css('display','');
			$('#giftOptionBox').css('display','none');
			$('input[name=gift_msg]').attr('disabled','disabled');
		}
	}

	// 사은품 초기화
	function giftReset(items){
		$('select[name=giftval_seq]').find('option:gt(0)').remove();
		resetGiftOptions();

		if($(items).length < 1){
			if($('#giftSelectArea')) $('#giftSelectArea').css('display','none');
			$('#noGiftOptionArea').css('display','');
			$('#giftOptionBox').css('display','none');
			$('input[name=gift_msg]').attr('disabled','disabled');

		}else{
			if($('#giftSelectArea')) $('#giftSelectArea').css('display','');
			$('#noGiftOptionArea').css('display','none');
			$('#giftOptionBox').css('display','');

			if($('input[name=gift_msg]').attr('disabled'))   $('input[name=gift_msg]').removeAttr('disabled');
			if($.isArray(items)){
				$(items).each(function(idx,itm){
					addGiftSelSelect(itm);
				});
			}else{
				$(items).each(function(idx,itm){
					for(p in itm) addGiftSelSelect(itm[p]);
				});
			}
		}
	}

	// 사은품 옵션 초기화
	function resetGiftOptions(){
		var $gift = $("select[name=giftval_seq] option:selected");
		var index =$("select[name=giftval_seq] option").index($gift);
		$('#giftOptionArea').html('');

		if($.trim($($gift).data('imgsrc')).length < 1){
			$('#gift_img').attr('src',"/images/no_img.gif");
		}else{
			$('#gift_img').attr('src','<?=$Dir?>data/shopimages/etc/'+$($gift).data('imgsrc'));
		}

		if(index > 0){
			$items = $($gift).data('options');
			//alert($($items).size());
			if($($items).size() >0){
				var str = '<table border="0" cellpadding="0" cellspacing="0" style="margin-top:4px;width:100%">';
				if($.isArray($items)){
					$($items).each(function(idx,itm){
						str += '<tr><td style="width:50px;">옵션 '+(idx+1)+' :</td>';
						str += '<td><select name="giftOpt'+(idx+1)+'" style="width:100%">';
						$name = itm.name;

						$(itm.items).each(function(idx,sitm){
							str += '<option value="'+sitm[0]+'">'+$name+' : '+sitm[0]+'</option>';
						});
						str += '</select></td></tr>';
					});
				}else{
					$($items).each(function(idx,oitm){
						for(p in oitm){
							itm = oitm[p];
							str += '<tr><td style="width:50px;">옵션 '+(p)+' :</td>';
							str += '<td><select name="giftOpt'+(p)+'" style="width:100%">';
							$name = itm.name;
							$(itm.items).each(function(idx,sitm){
								/*
								for(q in sitm){
									alert(q);
									str += '<option value="'+sitm[q]+'">'+$name+' : '+sitm[q]+'</option>';
								}*/
								str += '<option value="'+sitm[0]+'">'+$name+' : '+sitm[0]+'</option>';
							});
							str += '</select></td></tr>';
						}
					});
				}
				str += '</table>';
				$('#giftOptionArea').html(str);
				//alert(str);
			}
		}
	}

	function addGiftSelSelect(itm){
		$('<option value="'+itm.gift_regdate+'">'+itm.gift_name+'</option>').data('imgsrc',itm.gift_image).data('options',itm.options).appendTo("select[name=giftval_seq]");

	}
	function autoHypenPhone(str){
		  str = str.replace(/[^0-9]/g, '');
		  var tmp = '';
		  if( str.length < 4){
			  return str;
		  }else if(str.length < 8){
			  tmp += str.substr(0, 3);
			  tmp += '-';
			  tmp += str.substr(3);
			  return tmp;
		  }else{              
			  tmp += str.substr(0, 3);
			  tmp += '-';
			  tmp += str.substr(3, 4);
			  tmp += '-';
			  tmp += str.substr(7);
			  return tmp;
		  }
	  
		  return str;
	}

	function couponRemoveBtnEvent(){
		//couponpop에서 호출함
		$('.couponRemoveBtn').unbind();
		$('.couponRemoveBtn').click(function(){
			var btnFlag  = $(this).attr('btnFlag');
			// $('.limitedCouponGroup').remove();	
			// $('.unlimitedCouponGroup').remove();	
			if(btnFlag == "Y"){
				// alert(btnFlag);
				$('.limitedCouponGroup').remove();	
			}else{
				// alert(btnFlag);
				$('.unlimitedCouponGroup').remove();	
			}
			calprice();

		});
	}
	
	function calprice(){
		/*var totalPrice = "<?=$sumprice + $sumpricevat?>";
		var deliPrice = $('#disp_deliprice').text().replace(/,/g,"");
		var reservePrice = $('#disp_reserve').text().replace(/,/g,"");
		console.log("reservePrice:" + reservePrice);
		var couponPrice = 0;
		var lastPrice = (parseInt(totalPrice) + parseInt(deliPrice)) - parseInt(reservePrice.replace("-",""));
		console.log('LASTPRICE : ' + lastPrice);
		
		if ($('.couponPrice').length > 0) {
			couponPrice = $('.couponPrice').val();
			var dan = couponPrice.slice(-1);
			if (dan == "%") {
				couponPrice = totalPrice * (parseInt(couponPrice.slice(0,-1).replace(/,/g,""))/100);
			} else {
				couponPrice = parseInt(couponPrice.slice(0,-1).replace(/,/g,""));
			}
		}
		couponPrice = Math.floor(couponPrice/100) * 100;
		console.log("COUPONPRICE : " + couponPrice);
		lastPrice -= couponPrice;
		$('#coupon_price').val(couponPrice);
		$('#gift01').val(lastPrice);
		
		couponPrice = numberFormat(couponPrice);
		lastPrice = numberFormat(lastPrice);
		
		$('#disp_coupon').text(couponPrice);
		$('#disp_last_price').text(lastPrice);
		$('#pmBtn').text(lastPrice);
		console.log("pmBtn" +$('#pmBtn').text());*/
		
		var totalPrice = "<?=$sumprice + $sumpricevat?>";
		var couponPrice = 0;

		if ($('.couponPrice').length > 0) {
			couponPrice = $('.couponPrice').val();
			var dan = couponPrice.slice(-1);
			if (dan == "%") {
				couponPrice = parseInt(totalPrice) * (parseInt(couponPrice.slice(0,-1).replace(/,/g,""))/100);
			} else {
				couponPrice = parseInt(couponPrice.slice(0,-1).replace(/,/g,""));
			}
		}

		couponPrice = Math.floor(couponPrice/100) * 100;
		$('#coupon_price').val(couponPrice);

		solvPrice();

		var couponlist = ""; // 쿠폰 리스트
		var dcpricelist = ""; // 할인액 리스트
		var drpricelist = ""; // 적립액 리스트
		var couponproduct = ""; // 쿠폰사용 상품 리스트 (쿠폰코드_상품코드_옵션1idx_옵션2idx)
		var couponBankOnly = ""; // if (현금 사용시 가능한 쿠폰이 선택된 경우 ) Y else N

		$('.parentCouponWrap').each(function(){
			couponlist = $(this).attr('couponlist');
			dcpricelist = $(this).attr('dcpricelist');
			drpricelist = $(this).attr('drpricelist');
			couponproduct = $(this).attr('couponproduct');
			if($(this).attr('bank_only') == "Y") couponBankOnly = "Y";
		});
		
		document.getElementById("couponlist").value = "|" + couponlist;
		document.getElementById("dcpricelist").value = "|" + dcpricelist;
		document.getElementById("drpricelist").value = "|" + drpricelist;
		document.getElementById("couponproduct").value = "|" + couponproduct;
		document.getElementById("couponBankOnly").value = couponBankOnly;
	
	}
	
	function numberFormat(getNum ){
		let rNum = (getNum/1).toFixed(0).replace('.', ',');
		return rNum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
		
</script>


<div id="delivery_popup" style="display: none; position: fixed; padding: 15% 3%; box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 999; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="ReceiverClose()">
		<div style="position:absolute;top:3%;right:3%;color:#fff;font-size:4em;font-weight:500;">×</div>
	</div>
	<div style="position: relative; width: 100%; height: 100%; background-color: rgb(255, 255, 255); z-index: 0; overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;">
		<iframe frameborder="0" id="delivery_content" src="about:blank" style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
	</div>
</div>

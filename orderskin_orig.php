<input type="hidden" name="msg_type" value="1" />
<input type=hidden name="addorder_msg" value="">
<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>" />


<!-- 주문상품 테이블 START -->
<div class="order_wrap">
	<table class="order_pr_container" cellpadding="0" cellspacing="0">
		<tbody>
	<?
		$couponable = false;
		$reserveuseable = false;
		if($basketItems['productcnt'] <1){ 
	?>
			<tr>
				<td style="height:30px;">등록된 상품이 없습니다.</td>
			</tr>
	<?
		}else{
			$timgsize = 75;
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
			<tr>
				<td class="order_pr_wrap">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td class="order_pr_img_wrap">
								<img src="<?=$product['tinyimage']['src']?>" <? if($product['tinyimage'][$product['tinyimage']['big']] > $timgsize) echo $product['tinyimage']['big'].'="'.$timgsize.'"'; ?> />
							</td>
							<td>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td class="order_pr_title">
											상품명 :
										</td>
										<td class="order_pr_infomsg">
											<a href="./productdetail_tab01.php?productcode=<?=$product['productcode']?>">
												<font color="#000000">
													<b>
														<?=cutStr($product['productname'],22)?>
													</b>
												</font>
											</a>
											<?
												if(_array($product['option1']) || _array($product['option2']) || !_empty($product['optvalue'])){
											?>
													<span class="order_option_wrap">
														<br/>
														<img border=0 src="../images/common/basket/001/basket_skin3_icon002.gif">
														<?=$product['option1'][$product['opt1_idx']]?>
														<? 
															if(_array($product['option2'])) {
																echo ' / '.$product['option2'][$product['opt2_idx']]; 
															}
															if(!_empty($product['optvalue'])) {
																echo $product['optvalue']."\n";
															}
														?>
													</span>
											<?
												}	
											?>
											<br />
											<span class="order_constraint">
											<? if($product['bankonly'] == 'Y'){ ?>현금결제,<? } ?>
											<? if($product['setquota'] == 'Y'){ ?>무이자,<? } ?>
											<?
												// 혜택 및 제한 사항
												$sptxt = array();
												if($product['cateAuth']['coupon'] == 'N') array_push($sptxt,'할인쿠폰적용불가');
												if($product['cateAuth']['reserve'] == 'N') array_push($sptxt,'적립금적용불가');
												if($product['cateAuth']['gift'] == 'N') array_push($sptxt,'사은품적용불가');
												if($product['cateAuth']['refund'] == 'N') array_push($sptxt,'교환/반품불가');
												if(_array($sptxt)){
													echo implode(',<br/>',$sptxt);
												}
											?>
											</span>
										</td>
									</tr>
									<tr>
										<td class="order_pr_title">
											수&nbsp;&nbsp;&nbsp;&nbsp;량 :
										</td>
										<td class="order_pr_infomsg">
										<?=$product['quantity']?>
										</td>
									</tr>
									<!--<tr>
										<td class="order_pr_title">
											판매가 :
										</td>
										<td class="order_pr_infomsg">
											<?=number_format($product['sellprice']-$product['group_discount'])?>원
										</td>
									</tr>
									-->
									<tr>
										<td class="order_pr_title">
											구매가 :
										</td>
										<td class="order_pr_infomsg">
											<?=number_format($product['realprice'])?>원
										</td>
									</tr>
									<tr>
										<td class="order_pr_title">
											적립금 :
										</td>
										<td class="order_pr_infomsg">
											<?=number_format($product['reserve'])?>원
										</td>
									</tr>
									<tr>
										<td class="order_pr_title">
											배송비 :
										</td>
										<td class="order_pr_infomsg">
											<? if($product['deli_price']>0){
												if($row->deli=="Y"){ ?>유료배송<br><?=number_format($product['deli_price']*$product['quantity'])?>원
												<?		}else if($row->deli=="N") { ?>유료배송<br /><?=number_format($product['deli_price'])?>원<?		}
												}else if($product['deli']=="F" || $product['deli']=="G"){ echo ($product['deli']=="F"?'개별무료':'착불')?><?
												}else{
												if($product['vender'] > 0) echo '입점사 기본배송';
												else echo '기본배송비';
												}
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<?			}// end for
				} // end foreach
			} // end if
		?>	
		</tbody>
		<tfoot>
			<tr>
				<td class="order_pr_total_wrap">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>결제금액&nbsp;:&nbsp;<span><?=number_format($basketItems['sumprice'])?></span>원</td>
							<td><span>(배송비 <?=number_format($basketItems['deli_price'])?>원)</span></td>
						</tr>
					</table>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<!-- 주문상품 테이블 END -->
<!-- 그룹정책 테이블 START -->
<?
// 그룹 할인 또는 적립
	if($sumprice>0 && !_empty($group_type)) {
		$salemoney=0;
		$salereserve=0;
		switch($group_type){
			case 'SW':
				$salemoney=$group_addmoney; break;
			case 'SP':
				$salemoney=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00"; break;
			case 'RW':
				$salereserve=$group_addmoney; break;
			case 'RP':
				$salereserve=$reserve*($group_addmoney-1); break;
			case 'RQ':
				$salereserve=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00"; break;
		}
		if(!_empty($_ShopInfo->getMemid()) && !_empty($group_code) && substr($group_code,0,1)!="M") $arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"");
?>
<div class="order_wrap order_gap">
	<div class="order_group_wrap">
		<B><?=$name?></B>님은 <B><FONT COLOR="#EE1A02">[<?=$org_group_name?>]</FONT></B>회원입니다.<br />
		<FONT COLOR="#EE1A02"><B><?=number_format($group_usemoney)?>원</B></FONT> 이상 <?=$arr_dctype[$group_payment]?>구매시,
		<?
			if($group_type=="RW") echo "적립금에 <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>원</font>을 추가 적립해 드립니다.";
			else if($group_type=="RP") echo "구매 적립금의 <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>배</font>를 적립해 드립니다.";
			else if($group_type=="SW") echo "구매금액 <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>원</font>을 추가 할인해 드립니다.";
			else if($group_type=="SP") echo "구매금액의 <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>%</font>를 추가 할인해 드립니다.";
		?>
	</div>
</div>
<?	
	} 
?>
<!-- 그룹정책 테이블 END -->
<!-- 주문정보 START -->
<div class="order_wrap">
	<!-- 주문자 정보 입력 START -->
	<div class="order_title">
		<h4>주문자정보 입력</h4>
	</div>
	<div class="order_info_table_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
			<tr>
				<th>
					이름
				</th>
				<td>
					<input type="text" name="sender_name" class="mobile_input mobile_text" value="<?=$name?>" />
				</td>
			</tr>
			<tr>
				<th>
					연락처
				</th>
				<td>
					<input type="tel" name="sender_tel1" class="mobile_input mobile_number" value="<?=$mobile[0]?>" maxlength="4"/> -
					<input type="tel" name="sender_tel2" class="mobile_input mobile_number" value="<?=$mobile[1]?>" maxlength="4"/> -
					<input type="tel" name="sender_tel3" class="mobile_input mobile_number" value="<?=$mobile[2]?>" maxlength="4"/>
				</td>
			</tr>
			<tr>
				<th>
					이메일
				</th>
				<td>
					<input type="email" name="sender_email" id="o_email" class="mobile_input mobile_text" maxlength="80" value="<?=$email?>" />
				</td>
			</tr>
		</table>
	</div>
	<!-- 주문자 정보 입력 END -->
	<!-- 배송 정보 입력 START -->
	<div class="order_title delivery_title">
		<h4>배송정보 입력</h4> <div class="btn_same"><button type="button" onClick="SameCheck();">주문자와 동일</button></div>
	</div>
	<div class="delivery_info_table_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
			<tr>
				<th>
					받는사람
				</th>
				<td>
					<input type="text" id="o_name" name="receiver_name" class="mobile_input mobile_text" />
				</td>
			</tr>
			<tr>
				<th>
					전화번호
				</th>
				<td>
					<input type="tel" name="receiver_tel11" id="o_number2" class="mobile_input mobile_number" maxlength="4" /> - 
					<input type="tel" name="receiver_tel12" id="o_number2" class="mobile_input mobile_number" maxlength="4" /> - 
					<input type="tel" name="receiver_tel13" id="o_number2" class="mobile_input mobile_number" maxlength="4" />
				</td>
			</tr>
			<tr>
				<th>비상전화
				</th>
				<td>
					<input type="tel" name="receiver_tel21" id="o_number2" class="mobile_input mobile_number" maxlength="4"> - 
					<input type="tel" name="receiver_tel22" id="o_number2" class="mobile_input mobile_number" maxlength="4"> - 
					<input type="tel" name="receiver_tel23" id="o_number2" class="mobile_input mobile_number" maxlength="4">
				</td>
			</tr>
			<tr>
				<th>
					주소
				</th>
				<td class="order_deli_type1">
					<input type="text" name="rpost1" class="mobile_input post_field mobile_number" maxlength="4" /> - <input type="text" name="rpost2" class="mobile_input post_field mobile_number" maxlength="4"/>
					<button type="button" onClick="javascript:get_post();" class="btn_address_search"><span>주소찾기</span></button><br/>
					<input type="text" name="raddr1" class="mobile_input address_field mobile_text" value="" /><br/>
					<input type="text" name="raddr2" class="mobile_input address_field mobile_text" value="" />
				</td>
			</tr>
			<tr>
				<th>
					전달사항
				</th>
				<td class="order_deli_type1">
					<textarea name="order_prmsg" id="o_text" class="order_textarea" rows="5"></textarea>
				</td>
			</tr>
		</table>
	</div>
	<!-- 배송 정보 입력 END -->
	<!-- 비회원 개인정보 수집동의 START -->
	<?
		if(strlen($_ShopInfo->getMemid()) <= 0){
	?>
		<div class="order_title">
			<h4>비회원 개인정보 수집동의</h4>
		</div>
		<div class="persnal_info_wrap">
			<div class="persnal_clause">
				<?=strip_tags($privercybody, "<p>")?>
			</div>
			<div class="persnal_clause_btn_wrap">
				<input type="radio" id="idx_dongiY" name="dongi" value="Y" /><label for="idx_dongiY" class="clause_btn_true">동의함</label>&nbsp;&nbsp;&nbsp;
				<input type="radio" id="idx_dongiN" name="dongi" value="N" /><label for="idx_dongiN" class="clause_btn_false">동의 안함</label>
			</div>
		</div>
	<?
		}
	?>
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
	?>
	<div class="order_title">
		<h4>구매시 혜택 적용</h4>
	</div>
	<div class="order_benefit_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
				<!-- 쿠폰 적용 사항 START -->
			<tr>
				<th>
					쿠폰
				</th>
							<?
				if($_data->coupon_ok=="Y" && checkGroupUseCoupon() && $couponable) { //쿠폰 사용가능 여부 체크 
			?>
				<td class="order_benefit_type1">
					<input type="text" name="coupon_price" id="coupon_price" onclick="coupon_check()" class="st02_1 mobile_input" maxlength="8" value="0" readonly="readonly" /> 원
					 <!-- <a href="javascript:coupon_check()" onmouseover="window.status='쿠폰선택';return true;" class>쿠폰 선택</a>
					 <a href="javascript:resetCoupon()">쿠폰적용 취소</a><br> -->
					 <button type="button" onClick="coupon_check();">쿠폰적용</button>
					 <button type="button" onClick="coupon_check();">적용취소</button><br/>
				</td>
			<?}else{?>
				<td>
					쿠폰사용 조건에 해당되지 않습니다.
				</td>
			<?}?>
			</tr>
			<!-- 쿠폰 적용 사항 END -->
			<!-- 적립금 적용 사항 START -->
		
			<tr>
				<th class="order_benefit_type2">
					적립금
				</th>
				<?
					if($reserveuseable or $okreserve > 0){
						if(($user_reserve -$_data->reserve_maxuse) > 0){
				?>
				<td class="order_benefit_type2" style="padding:2px 0px;">
					<input type="hidden" name="okreserve" value="<?=$okreserve?>" />
					보유 적립금 : <input type="text" name="oriuser_reserve" class="st02_1 mobile_input save_reserve" maxlength="8" value="<?=number_format($user_reserve)?>" readonly="readonly" /> 원
					<br/>사용 적립금 :
					<input type="text" name="usereserve" id="usereserve" class="st02_1 mobile_input" maxlength="8" value="0"  <?=($okreserve<1)?'disabled="disabled"':''?>  /> 원 사용<br />
					<!--<span style="color:red"> <span style="font-weight:bold"><?=number_format($okreserve)?>원</span> 까지 적립금으로 사용하여 구매하실수 있습니다.</span><br />-->
				</td>
				<?
						}else{
				?>
					<td>
						보유적립금 <span class="order_special_char"><?=number_format($_data->reserve_maxuse)?>원</span> 이상 일 경우 사용가능합니다.
					</td>
				<?
						}
			}else{
					
				?>
				<td>
				적립금 사용 조건에 해당되지 않습니다.
				</td>
				<?}?>
			</tr>
	
			<!-- 적립금 적용 사항 END -->
		</table>
		
	</div>
	<?
		if($_data->coupon_ok !="Y" || !$couponable) { 
	?>
			<input type="hidden" name="coupon_price" id="coupon_price" value="0" />
	<?
		}

		if(!$reserveuseable ||  $okreserve <= 0 || ($user_reserve -$_data->reserve_maxuse) <= 0) {
	?>
			<input type="hidden" name="okreserve" value="0" />
			<input type="hidden" name="oriuser_reserve" class="st02_1" maxlength="8" value="<?=number_format($user_reserve)?>" />
			<input type="hidden" name="usereserve" id="coupon_price" value="0" />
	<?
		}
	}else{
	?>
		<input type="hidden" name="usereserve" id="usereserve" value="0" />
		<input type="hidden" name="coupon_price" id="coupon_price" value="0" />
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
		<div class="order_title">
			<h4>구매 사은품 </h4>
		</div>
		<div class="freegift_wrap">
			<input type="hidden" name="gift01" id="gift01" class="mobile_input" maxlength="8" readonly value="<?=$basketItems['gift_price']?>" />
			<table cellpadding="0" cellspacing="0" class="freegift_table">
				<tr>
					<th>사은품 선택</th>
					<td>
						<div id="noGiftOptionArea">선택 가능한 사은품이 없습니다.</div>
						<table cellpadding="0" cellspacing="0" border="0" class="noborder" width="100%" id="giftOptionBox" style="display:none">
							<tr>
								<td class="freegift_image_area">
									<img src="/images/no_img.gif" id="gift_img" height="50"/>
								</td>
							</tr>
							<tr>
								<td class="freegift_choice">
									<div style="width:100%; text-align:left;">
										<select name="giftval_seq" class="st13_1_1">
											<option value="">:: 사은품선택 ::</option>
										</select>
									</div>
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
					<th class="freegift_sendmsg_wrap">요청사항</th>
					<td class="freegift_sendmsg_wrap">
						<textarea name="gift_msg" class="mobile_input freegift_sendmsg" maxlength="50" disabled="disabled" placeholder="사은품 관련 요청사항 입력(50자)" /></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?
		}
	?>
	<!-- 사은품 적용 사항 END -->
	<!-- 결제 방법 선택 START -->
	<?
		$arrpayinfo=explode("=",$_data->bank_account);
		//$arrcardcom=array("A"=>"[<font color=red>KCP.CO.KR</font>]","B"=>"[<font color=red>dacompay.net (데이콤전자상거래)</font>]","C"=>"[<font color=red>allthegate.com (올더게이트)</font>]","D"=>"[<font color=red>inicis.com (이니시스)</font>]");
		//$cardid_info=GetEscrowType($_data->card_id);
	?>
	<div class="order_title">
		<h4>결제방법 선택</h4>
	</div>
	<div class="payment_type_wrap">
		<table class="payment_wrap" cellpadding="0" cellspacing="0">
			<tr>
				<th>
				결제방법
				</th>
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
					<input type="radio" id="paytype_1" class="paytype" name="paymethod" value="B" onClick="showBankAccount('show')"><label for="paytype_1"><span class="btn_payment btn_paytype paytype_1" onClick="paymentControl('bankaccount');">&nbsp;무통장&nbsp;</span></label>
					<?
						}
						if(strlen($_data->card_id) > 0){
							if($paytype_row->use_creditcard == 'Y' && $usepg_row->pg_use == 'Y'){
					?>
					<input type="radio" id="paytype_2" class="paytype" name="paymethod" value="C" onClick="showBankAccount('hide')"><label for="paytype_2"><span class="btn_payment btn_paytype paytype_2" onClick="paymentControl('creditcard');">신용카드</span></label>
					<?
							}
						}
						if(strlen($_data->trans_id)>0) {
							if($paytype_row->use_transferaccount == 'Y' && $usepg_row->pg_use == 'Y'){
					?>
					<input type="radio" id="paytype_3" class="paytype" name="paymethod" value="V" onClick="showBankAccount('hide')"><label for="paytype_3"><span class="btn_payment btn_paytype paytype_3" onClick="paymentControl('transferaccount');">계좌이체</span></label>
					<?
							}
						}
						if(strlen($_data->virtual_id)>0) {
							if($paytype_row->use_virtualaccount == 'Y' && $usepg_row->pg_use == 'Y'){
					?>
					<input type="radio" id="paytype_4" class="paytype" name="paymethod" value="V" onClick="showBankAccount('hide')"><label for="paytype_4"><span class="btn_payment btn_paytype paytype_4" onClick="paymentControl('virtualaccount');">가상계좌</span></label>
					<?
							}
						}
					?>
				</td>
			</tr>
			<tr id="pay_account_list" style="display:none">
				<th class="borderline">
					입금계좌
				</th>
				<td class="borderline">
					<table class="pay_account_table" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td class="account_select_wrap">
								<select name="pay_data1" onChange="accountControl(this.value);">
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
														<option value="<?=$tok?>"><?=$account_info[0]?></option>
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
							</td>
						</tr>
						<tr id="account_info_list" style="display:none;">
							<td>
								<table cellpadding="0" width="100%" cellspacing="0" border="0" class="account_info_table">
									<tr>
										<td class="account_info_menu">
										계좌번호
										</td>
										<td class="separation">
										:
										</td>
										<td id="pay_account">
										</td>
									</tr>
									<tr>
										<td class="account_info_menu">
										예&nbsp;&nbsp;금&nbsp;&nbsp;주
										</td>
										<td class="separation">
										:
										</td>
										<td id="pay_holder">
										</td>
									</tr>
									<tr>
										<td class="account_info_menu">
											입금자명
										</td>
										<td class="separation">
										:
										</td>
										<td class="transfername_wrap">
											<input type="text" name="bankname" class="mobile_input transfetname" value="" placeholder="입금자명이 다를 경우 입력"/>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>

	<div class="totalpay_info_table">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<caption>총 결제 내역</caption>
			<tr>
				<th>합계</th>
				<td><?=number_format($sumprice+$sumpricevat)?></span>원</td>
			</tr>
			<? if(!_empty($_ShopInfo->getMemid())){ ?>
			<tr>
				<th>적립금 사용</th>
				<td><span id="disp_reserve">0</span>원</td>
			</tr>
			<? if($_data->coupon_ok =="Y" && $couponable){ ?>
			<tr>
				<th>할인쿠폰</th>
				<td><span id="disp_coupon">0</span>원</td>
			</tr>
			<? } ?>
			<tr>
				<th>등급할인</th>
				<td><span id="disp_groupdiscount">0</span>원</td>
			</tr>
			<? } ?>
			<tr>
				<th>배송비</th>
				<td>
					<span id="disp_deliprice"><?=number_format($basketItems['deli_price'])?></span>원
					<input type='hidden' name='disp_deliprice_temp' id='disp_deliprice_temp' value='0'>
				</td>
			</tr>
			<tr>
				<th>최종 결제금액</th>
				<td><span id="disp_last_price" style="font-size:18px; font-family:Tahoma;"><?=number_format($basketItems['sumprice']+$basketItems['deli_price']+$basketItems['sumpricevat'])?></span>원</td>
			</tr>
		</table>
	</div>
</div>
<!-- 주문정보 END -->
<br />
<!-- //
<!-- 버튼 -->
<section class="basic_btn_area btn_w1">

<? if($row_cfg[use_bank]!="Y" && $row_cfg[use_creditcard]!="Y" && $row_cfg[use_mobilephone]!="Y") { ?>
<a href="#" class="button black bigrounded" onClick="javascript:alert('결제방식이 설정되어있지 않습니다. 관리자에게 문의하세요~')">결제하기</a>
<? } else { ?>
<a href="#" class="button black bigrounded" onClick="javascript:CheckForm()">결제하기</a>
<? }?>
<a href="#" class="button white bigrounded" onClick="javascript:ordercancel('cancel')">주문취소</a>
</section>
<!-- //버튼 -->
<Script>
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
			$('.btn_paytype').removeClass('selected');
			$('.paytype_1').addClass('selected');
		break;
		case 'creditcard':
			$('.btn_paytype').removeClass('selected');
			$('.paytype_2').addClass('selected');			
		break;
		case 'transferaccount':
			$('.btn_paytype').removeClass('selected');
			$('.paytype_3').addClass('selected');
			$('#pay_account_list').css('display','none');
		break;
		case 'virtualaccount':
			$('.btn_paytype').removeClass('selected');
			$('.paytype_4').addClass('selected');
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
	gprice = parseInt(gprice);
	var noGift = ($('input[name=apply_gift]').val() == 'N');
	if(!noGift){
		if(isNaN(gprice)) gprice = parseInt($('input[name=gift01]').val());
		if(isNaN(gprice) || gprice < 1) gprice = 0;
	}else{
		gprice = 0;
	}


	$('input[name=gift01]').val(gprice);
	/*
	var $gift = $("select[name=giftval_seq] option:selected");
	var index =$("select[name=giftval_seq] option").index($gift);
	if(index > 0) alert('사은품 적용이 초기화 됩니다.');
	*/
	if(gprice >= mingiftprice){
		//$.post( '/json_order.php',{'act':'getGife','gift_price':gprice},function(response, textStatus, jqXHR){ console.log('data.length');},"json");
		if($('#giftSelectArea')) $('#giftSelectArea').css('display','');
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

function giftReset(items){
	$('select[name=giftval_seq]').find('option:gt(0)').remove();
	resetGiftOptions();

	if($(items).size() < 1){
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
			var str = '<table border="0" cellpadding="0" cellspacing="0" style="width:100%">';
			if($.isArray($items)){
				$($items).each(function(idx,itm){
					str += '<tr><td style="width:50px;">옵션 '+(idx+1)+' :</td>';
					str += '<td><select name="giftOpt'+(idx+1)+'" style="width:90%">';
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
						str += '<td><select name="giftOpt'+(p)+'" style="width:90%">';
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


/*

$( "select" )
  .change(function () {
    var str = "";

    $( "div" ).text( str );
  })
  .change();

onchange="secGift(this.value);"
*/
function addGiftSelSelect(itm){
	$('<option value="'+itm.gift_regdate+'">'+itm.gift_name+'</option>').data('imgsrc',itm.gift_image).data('options',itm.options).appendTo("select[name=giftval_seq]");

}
</script>

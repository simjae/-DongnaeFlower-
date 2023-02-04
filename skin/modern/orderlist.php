<?
//include_once('header.php');
?>

<div id="content">
	<div class="h_area2">
		<h2>주문내역</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<!-- 주문내역 -->
	<div class="orderlist">
		<div class="pr_navi">
			<h3 style="float:left;height:34px;line-height:32px">기간별 조회</h3>
			<span class="basic_select" style="float:right">
				<select name="search_period" onChange="GoSearch(this.value)">
					<option value="">선택</option>
					<option value="3MONTH" <? if($_POST[search_period]=="3MONTH") {echo "selected";}?>>3개월</option>
					<option value="6MONTH" <? if($_POST[search_period]=="6MONTH") {echo "selected";}?>>6개월</option>
					<option value="12MONTH" <? if($_POST[search_period]=="12MONTH") {echo "selected";}?>>1년</option>
				</select>
			</span>
			<div style="clear:both"></div>
		</div>

		<div class="tab_area">
			<ul class="tab_type2">
			<? if($_POST[ordgbn]=="" || $_POST[ordgbn]=="A") {?>
				<li class="active"><a href="javascript:GoOrdGbn('A')" rel="external">전체보기</a></li>
			<? } else { ?>
				<li><a href="javascript:GoOrdGbn('A')" rel="external">전체보기</a></li>
			<? } ?>

			<? if($_POST[ordgbn]=="S") {?>
				<li class="active"><a href="javascript:GoOrdGbn('S')" rel="external">주문내역</a></li>
			<? } else { ?>
				<li><a href="javascript:GoOrdGbn('S')" rel="external">주문내역</a></li>
			<? } ?>

			<? if($_POST[ordgbn]=="C") {?>
				<li class="active"><a href="javascript:GoOrdGbn('C')" rel="external">취소내역</a></li>
			<? } else { ?>
				<li><a href="javascript:GoOrdGbn('C')" rel="external">취소내역</a></li>
			<? } ?>

			<? if($_POST[ordgbn]=="R") {?>
				<li class="active"><a href="javascript:GoOrdGbn('R')" rel="external">반품,교환</a></li>
			<? } else { ?>
				<li><a href="javascript:GoOrdGbn('R')" rel="external">반품,교환</a></li>
			<? } ?>
			</ul>
		</div>

		<div class="orderlist_list">
<?
		$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
		$result=mysql_query($sql,get_db_conn());
		$delicomlist=array();
		while($row=mysql_fetch_object($result)) {
			$delicomlist[$row->code]=$row;
		}
		mysql_free_result($result);

		$s_curtime=mktime(0,0,0,$s_month,$s_day,$s_year);
		$s_curdate=date("Ymd",$s_curtime);
		$e_curtime=mktime(0,0,0,$e_month,$e_day,$e_year);
		$e_curdate=date("Ymd",$e_curtime)."999999999999";

		$sql = "SELECT COUNT(*) as t_count FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
		if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
		else if($ordgbn=="C") $sql.= " AND deli_gbn IN ('C','D') ";
		else if($ordgbn=="R") $sql.= " AND deli_gbn IN ('R','E') ";
		$sql.= " AND (del_gbn='N' OR del_gbn='A') ";
		//echo $sql;
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$t_count = (int)$row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT ordercode, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn ";
		$sql.= "FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
		if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
		else if($ordgbn=="C") $sql.= "AND deli_gbn IN ('C','D') ";
		else if($ordgbn=="R") $sql.= "AND deli_gbn IN ('R','E') ";
		$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
		$sql.= "ORDER BY ordercode DESC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		//echo $sql;
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {

			/*	$result_tmp = mysql_query("SELECT productname FROM tblorderproduct WHERE ordercode='$row->ordercode' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%')");
				$a = 0;
				while($row_tmp = mysql_fetch_array($result_tmp))
				{
					if($a==0){	$first_productname = $row_tmp[productname];	}
					$a++;
				}
				if($a > 1) {	$str = "(총" .$a."종)";	} else {	$str = "";}*/

			?>
				<div class="orderlist_detail_top">
					<div style="overflow:hidden">
						주문일 : <span class="orderlist_detail_date"><?=substr($row->ordercode,0,4)?>-<?=substr($row->ordercode,4,2)?>-<?=substr($row->ordercode,6,2)?></span>
						<a class="basic_button grayLineBtn" href="javascript:OrderDetailPop('<?=$row->ordercode?>')">주문상세정보</a>
					</div>
					결제금액 : <?=number_format($row->price)?>원 / 결제방법 : <?=getPaymethodStr($row->paymethod)?>
				</div>
				<div class="orderlist_detail_prlist">
<?
	$sql = "SELECT * FROM tblorderproduct WHERE ordercode='".$row->ordercode."' ";
	$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	//echo $sql;
	$result2=mysql_query($sql,get_db_conn());
	$jj=0;
	$numrows = mysql_num_rows($result2);
	$chkbox_count = 0;
	while($row2=mysql_fetch_object($result2)) {
?>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<col width="75%"></col>
						<col width=""></col>
						<tr>
							<td style="padding:5px 0px 5px 10px;">
							<?
								//if (isShowCancelCheckBox($_data->ordercancel, $row->deli_gbn) && !($row->pay_admin_proc=="C" && $row->pay_flag=="0000") && $numrows>1 && isShowCancelCheckBox($_data->ordercancel, $row2->deli_gbn) && $row2->status=='') {
								if (isShowCancelCheckBox($_data->ordercancel, $row->deli_gbn) && $numrows>1 && isShowCancelCheckBox($_data->ordercancel, $row2->deli_gbn) && $row2->status=='') {
							?>
								<input type="checkbox" name="chk_<?= $row->ordercode ?>" id="chk_<?= $row->ordercode ?>_<?= $jj ?>" value="<?=$row2->productcode?>"/>
								<input type="hidden" name="chk_uid_<?= $row->ordercode ?>" id="chk_uid_<?= $row->ordercode ?>_<?= $jj ?>" value="<?=$row2->uid?>"/>
							<?
								$chkbox_count++;
								}
								if(substr($row2->productcode,0,3) == "999"){
							?>
								<?=_strCut($row2->productname,20,5,$charset)?>
							<?
								}else{
							?>
								<a href="/m/productdetail_tab01.php?productcode=<?=$row2->productcode?>" rel="external"><?=_strCut($row2->productname,20,5,$charset)?></a>
							<?}?>
							</td>
							<td align="center" style="padding:5px 0px;">
								<? echo orderProductDeliStatusStr($row2,$row); ?>
								<?
								$deli_link="";
								$deli_url="";
								$trans_num="";
								$company_name="";
								if($row2->deli_gbn=="Y" AND strlen($row2->deli_num) > 0 ) {
									$deli_link = '';
									if($row2->deli_com>0 && $delicomlist[$row2->deli_com]) {
										$deli_url=$delicomlist[$row2->deli_com]->deli_url;
										$trans_num=$delicomlist[$row2->deli_com]->trans_num;
										$company_name=$delicomlist[$row2->deli_com]->company_name;
										//$deli_link .= $company_name."<br>".$row2->deli_num."<br>";
										if(strlen($row2->deli_num)>0 && strlen($deli_url)>0) {
											if(strlen($trans_num)>0) {
												$arrtransnum=explode(",",$trans_num);
												$pattern=array("(\[1\])","(\[2\])","(\[3\])","(\[4\])");
												$replace=array(substr($row2->deli_num,0,$arrtransnum[0]),substr($row2->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row2->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row2->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
												$deli_url=preg_replace($pattern,$replace,$deli_url);
											} else {
												$deli_url.=$row2->deli_num;
											}
											$deli_link .='<A HREF="javascript:DeliSearch(\''.$deli_url.'\')"><img src="'.$Dir.'images/common/btn_mypagedeliview.gif" border="0"></A>';
										}
									}
								}
								echo $deli_link;
								?>
								
								<? if(substr($row2->productcode,0,3) != "999"){ ?>
								<a class="basic_button grayBtnMini" href="javascript:reviewWrite('<?=$row2->productcode?>')">후기작성</a>
								<?}?>
							</td>
						</tr>
					</table>
<?
		$jj++;
	}
	mysql_free_result($result2);
?>

				<?
					//if (isShowCancelCheckBox($_data->ordercancel, $row->deli_gbn) && !($row->pay_admin_proc=="C" && $row->pay_flag=="0000") && $chkbox_count>0 && $numrows>1) {
					if (isShowCancelCheckBox($_data->ordercancel, $row->deli_gbn) && $chkbox_count>0 && $numrows>1) {
				?>
					<div class="orderlist_select_del">
						<input type="checkbox" name="chk_<?= $row->ordercode ?>_all" id="chk_<?= $row->ordercode ?>_all" value="all" onclick="productAll('chk_<?= $row->ordercode ?>')"/>
						<span style="cursor:pointer"
						<? if (strlen($row->bank_date)<12 && preg_match("/^(B|O|Q){1}/", $row->paymethod)) { ?>
							onclick="order_one_cancel('<?= $row->ordercode ?>', '', 'NO', '<?= $row->tempkey ?>')"
						<? }else{ ?>
							onclick="order_multi_cancel('<?=$row->ordercode?>')"
						<? } ?>>선택상품 주문취소</span>
					</div>
				<? } ?>

				<?php
					if (preg_match("/^(B|V){1}/", $row->paymethod)) {
						if (strlen($row->bank_date) > 12 && !preg_match("/^(D|E|C|R){1}/", $row->deli_gbn)) {
							$cash_sql = "SELECT * FROM tbltaxsavelist WHERE ordercode = '".$row->ordercode."'";
							$cash_res = mysql_query($cash_sql, get_db_conn());
							$cash_row = mysql_fetch_object($cash_res);
							mysql_free_result($cash_res);

							$cash_btn_name = ($cash_row->type == "Y") ? "현금영수증 발급완료" : "현금영수증 발급";
					?>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td class="mypage_list_cont">
								<style>
									.hiddenDiv{position:absolute;top:-200px;width:300px;padding:20px;background:#ffffff;border:2px solid #ebebeb;z-index:999999;}
									.hiddenDiv:before{position:absolute;top:20px;left:-23px;border-top:10px solid transparent;border-left:10px solid transparent;border-right:10px solid #999999;border-bottom:10px solid transparent;content:""}
									.mypageBaseTable{margin-bottom:10px;border-top:1px solid #ebebeb;}
									.mypageBaseTable caption{margin:13px;color:#333333;font-size:13px;font-weight:bold;text-align:center;letter-spacing:-0.5px;}
									.mypageBaseTable .th1{border-top:1px solid #ebebeb;border-bottom:1px solid #ebebeb;background:#f9f9f9;font-weight:normal}
									.mypageBaseTable th{border-bottom:1px solid #ebebeb;background:#f9f9f9;font-weight:normal}
									.mypageBaseTable td{text-align:left;padding:10px;border-bottom:1px solid #ebebeb;background:#ffffff;line-height:25px;}
									.mypageBaseTable .td1{text-align:left;padding:10px;border-top:1px solid #ebebeb;border-bottom:1px solid #ebebeb;background:#ffffff;line-height:25px;}
									.submitBtn{padding:10px;}
									.submitBtn a{display:block;width:60%;margin:0 auto;padding:5px 0px 3px 0px;background:#6a6a6a;color:#ffffff;font-size:12px;letter-spacing:-1px;text-align:center;text-decoration:none}
								</style>
								<a href="javascript:view_cash_receipt('<?php echo $row->ordercode?>')" class="basic_button grayLineBtn cashReceipt"  id="btn_<?php echo $row->ordercode?>" data-msg="<?php echo $cash_row->type?>"><?php echo $cash_btn_name?></a>
								<div id="view_<?php echo $row->ordercode?>" class="hiddenDiv" style="display:none;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mypageBaseTable">
										<caption>현금영수증 신청정보</caption>
										<colgroup>
											<col width="90" />
											<col width="" />
										</colgroup>
										<tr>
											<th class="th1">발급사유</th>
											<td class="td1">
												<label style="cursor:pointer"><input type="radio" name="useopt_<?php echo $row->ordercode?>" value="0" onclick="choice_type('<?php echo $row->ordercode?>',this.value)" style="vertical-align:middle" checked />개인소득공제용</label><br>
												<label style="cursor:pointer"><input type="radio" name="useopt_<?php echo $row->ordercode?>" value="1" onclick="choice_type('<?php echo $row->ordercode?>',this.value)" style="vertical-align:middle" />사업자증빙용</label>
											</td>
										</tr>

										<tr id="per_<?php echo $row->ordercode?>">
											<th>신청정보</th>
											<td>
												<div style="margin-bottom:6px;text-align:left;">
													<label style="cursor:pointer"><input type="radio" name="num_type1_<?php echo $row->ordercode?>" value="1" style="vertical-align:middle" checked />핸드폰번호</label><br>
													<label style="cursor:pointer"><input type="radio" name="num_type1_<?php echo $row->ordercode?>" value="2" style="vertical-align:middle" />현금영수증카드번호</label>
												</div>
												<input type="text" name="reg_num_<?php echo $row->ordercode?>" class="basic_input" style="width:100%" />
											</td>
										</tr>

										<tr id="com_<?php echo $row->ordercode?>" style="display:none;">
											<th>신청정보</th>
											<td>
												<div style="margin-bottom:4px">
													<label style="cursor:pointer"><input type="radio" name="num_type2_<?php echo $row->ordercode?>" value="1" style="vertical-align:middle" checked />사업자번호</label>
													<label style="cursor:pointer"><input type="radio" name="num_type2_<?php echo $row->ordercode?>" value="2" style="vertical-align:middle" />현금영수증카드번호</label>
												</div>
												<input type="text" name="com_num_<?php echo $row->ordercode?>" class="input" style="width:95%" />
											</td>
										</tr>

									</table>
									<div class="submitBtn"><a href="javascript:request_cash_receipt('<?php echo $row->ordercode?>')">현금영수증 신청하기</a></div>
								</div>
							</td>
						</tr>
					</table>
				<?php 
						}
					}
				?>
				</div>
<?
			$cnt++;
		}
		mysql_free_result($result);
?>
		</div>


<?

		$total_block = intval($pagecount / $setup[page_num]);

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
			//	$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {

				$a_prev_page = "<button type=\"button\" onClick='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' class=\"pg_btn pg_btn_prev\"><span></span></button>";
				//	$a_prev_page = $a_first_block.$a_prev_page;
				$a_prev_page = $a_prev_page;

			}
			else
			{
				$a_prev_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_prev\"><span></span></button>";

			}


			// 일반 블럭에서의 페이지 표시부분-시작
			if (intval($total_block) <> intval($nowblock)) {
				$print_page .= "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<span style=\"display:inline-block;width:28px;height:24px;line-height:24px; border:1px solid #000000;\">".(intval($nowblock*$setup[page_num]) + $gopage)."</span> "; //현재페이지
					} else {
						$print_page .= " <a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' class=\"pg_num\">".(intval($nowblock*$setup[page_num]) + $gopage)."</a>";
					}
				}
			} else {
				if (($pagecount % $setup[page_num]) == 0) {
					$lastpage = $setup[page_num];
				} else {
					$lastpage = $pagecount % $setup[page_num];
				}

				for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
					if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
						$print_page .= " <span class=\"pg_num pg_num_on\">".(intval($nowblock*$setup[page_num]) + $gopage)."</span>&nbsp;";
					} else {
						$print_page .= " <a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' class=\"pg_num\">".(intval($nowblock*$setup[page_num]) + $gopage)."</a>&nbsp;";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝

			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

			//	$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

				$next_page_exists = true;
			}
			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {

				//$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

				$a_next_page = "<button type=\"button\" onClick='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' class=\"pg_btn pg_btn_next\"><span></span></button>";

				//$a_next_page = $a_next_page.$a_last_block;
				$a_next_page = $a_next_page;
			}
			else
			{
				$a_next_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_next\"><span></span></button>";
			}
		} else {
			$a_prev_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_prev\"><span></span></button>";
			$print_page = "<span class=\"pg_num pg_num_on\">1</span>";
			$a_next_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_next\"><span></span></button>";
		}
?>

		<div class="pg pg_num_area3">
		<?=$a_prev_page?>
		<span class="pg_area"><?=$print_page?></span>
		<?=$a_next_page?>
		</div>


		</div>
	</div>
	<!-- //주문내역 -->

</div>

<hr>
<?
//include_once('footer.php');
?>
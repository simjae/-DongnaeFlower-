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
			<h3>기간별 조회</h3>
			<select class="basic_select" name="search_period" onChange="GoSearch(this.value)">
				<option value="">선택</option>
				<option value="3MONTH" <? if($_POST[search_period]=="3MONTH") {echo "selected";}?>>3개월</option>
				<option value="6MONTH" <? if($_POST[search_period]=="6MONTH") {echo "selected";}?>>6개월</option>
				<option value="12MONTH" <? if($_POST[search_period]=="12MONTH") {echo "selected";}?>>1년</option>
			</select>
		</div>
		
		<div class="tab_area">
			<ul class="tab_type1">
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
			<ul>				
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


				$result_tmp = mysql_query("SELECT productname FROM tblorderproduct WHERE ordercode='$row->ordercode' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%')");
				$a = 0;
				while($row_tmp = mysql_fetch_array($result_tmp))
				{
					if($a==0){	$first_productname = $row_tmp[productname];	}
					$a++;					
				}				
				if($a > 1) {	$str = "(총" .$a."종)";	} else {	$str = "";}
				
			?>
			<li>
					<div class="h_area3">
						<h4><!-- <A HREF="javascript:OrderDetailPop('<?=$row->ordercode?>')" onmouseover="window.status='주문내역조회';return true;" onmouseout="window.status='';return true;"> --><?=$first_productname?><?=$str?></h4>
					</div>
					<table class="basic_table tbl1">
					<tr>
							<th scope="row"><span>주문일자</span></th>
							<td><span><?=substr($row->ordercode,0,4)?>.<?=substr($row->ordercode,4,2)?>.<?=substr($row->ordercode,6,2)?></span></td>
					</tr>
			
					<?
					$sql = "SELECT * FROM tblorderproduct WHERE ordercode='".$row->ordercode."' ";
					$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
					//echo $sql;
					$result2=mysql_query($sql,get_db_conn());
					$jj=0;
					while($row2=mysql_fetch_object($result2)) {

						if($jj>0) { continue; }
?>

					
						<tr>
							<th scope="row"><span>배송상태</span></th>
							<td><span class="point1">							
							<?
							if ($row2->deli_gbn=="C") echo "주문취소";
							else if ($row2->deli_gbn=="D") echo "취소요청";
							else if ($row2->deli_gbn=="E") echo "환불대기";
							else if ($row2->deli_gbn=="X") echo "상품준비";
							else if ($row2->deli_gbn=="Y") echo "발송완료";
							else if ($row2->deli_gbn=="N") {
								if (strlen($row->bank_date)<12 && preg_match("/^(B|O|Q){1}/", $row->paymethod)) echo "입금확인중";
								else if ($row->pay_admin_proc=="C" && $row->pay_flag=="0000") echo "결제취소";
								else if (strlen($row->bank_date)>=12 || $row->pay_flag=="0000") echo "상품준비";
								else echo "결제확인중";
							} else if ($row2->deli_gbn=="S") {
								echo "상품준비";
							} else if ($row2->deli_gbn=="R") {
								echo "반송처리";
							} else if ($row2->deli_gbn=="H") {
								echo "발송완료 [정산보류]";
							}
							?>							
							</span></td>
						</tr>
					
					<?
			
				$jj++;
			}
			mysql_free_result($result2);
			?>
						
						<tr>
							<th scope="row"><span>결제방법</span></th>
							<td><span>
							<?
							if (preg_match("/^(B){1}/",$row->paymethod)) echo "무통장입금";
							else if (preg_match("/^(V){1}/",$row->paymethod)) echo "실시간계좌이체";
							else if (preg_match("/^(O){1}/",$row->paymethod)) echo "가상계좌";
							else if (preg_match("/^(Q){1}/",$row->paymethod)) echo "가상계좌-<FONT COLOR=\"#FF0000\">매매보호</FONT>";
							else if (preg_match("/^(C){1}/",$row->paymethod)) echo "신용카드";
							else if (preg_match("/^(P){1}/",$row->paymethod)) echo "신용카드-<FONT COLOR=\"#FF0000\">매매보호</FONT>";
							else if (preg_match("/^(M){1}/",$row->paymethod)) echo "휴대폰";
							else echo "";
							?>				
							</span></td>
						</tr>
						<tr>
							<th scope="row"><span>결제금액</span></th>
							<td><span><span class="point1"><?=number_format($row->price)?></span>원</span></td>
						</tr>
						</table>
					</li>
			<?				
			
			$cnt++;
		}
		mysql_free_result($result);
?>
				
		</ul>
		


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
			
				$a_prev_page = "<button type=\"button\" onClick='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' class=\"pg_btn pg_btn_prev\"><span>이전 페이지</span></button>";
				//	$a_prev_page = $a_first_block.$a_prev_page;
				$a_prev_page = $a_prev_page;

			}
			else
			{
				$a_prev_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_prev\"><span>이전 페이지</span></button>";
				
			}


			// 일반 블럭에서의 페이지 표시부분-시작			
			if (intval($total_block) <> intval($nowblock)) {
				$print_page .= "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= (intval($nowblock*$setup[page_num]) + $gopage)." ";
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

				$a_next_page = "<button type=\"button\" onClick='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' class=\"pg_btn pg_btn_next\"><span>다음 페이지</span></button>";

				//$a_next_page = $a_next_page.$a_last_block;
				$a_next_page = $a_next_page;
			}
			else
			{
				$a_next_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_next\"><span>다음 페이지</span></button>";
			}
		} else {
			$a_prev_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_prev\"><span>이전 페이지</span></button>";				
			$print_page = "<span class=\"pg_num pg_num_on\">1</span>";
			$a_next_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_next\"><span>다음 페이지</span></button>";
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
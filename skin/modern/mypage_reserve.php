<? 
include_once("./inc/function.php");
$setup[list_num] = 3;
?>

<div id="content">
	<div class="h_area2">
		<h2>적립금</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	
	<!-- 적립금 -->
	<div class="reserve">
		<div class="reserve_list">
			<div class="reserve_list_top">사용가능 적립금</div>
			<div class="reserve_list_value"><?=number_format($reserve)?>원</div>
		</div>

		<div class="reserve_prwrap">
			<h2>나의 적립금 내역</h2>
			<div class="reserve_pr_list">

<?
		$currentPage = $_REQUEST["page"];
		if(!$currentPage) $currentPage = 1; 
		$pagetype="board";
		$sql = "SELECT COUNT(*) as t_count FROM tblreserve ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$totalRecord = $row->t_count;

		mysql_free_result($result);
		
		$pagecount = (($totalRecord - 1) / $setup[list_num]) + 1;
		
		$recordPerPage = 3; // 페이지당 게시글 리스트 수
		$pagePerBlock = 5; // 블록 갯수

		$sql = "SELECT * FROM tblreserve WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		$sql.= "ORDER BY date DESC LIMIT " . ($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$date=substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);

			if($cnt>0) {
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"4\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
			}

			$ordercode="";
			$orderprice="";
			$orderdata=$row->orderdata;
			if(strlen($orderdata)>0) {
				$tmpstr=explode("=",$orderdata);
				$ordercode=$tmpstr[0];
				$orderprice=$tmpstr[1];
			}
?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="reserve_pr_table">
				<thead>
				<tr>
					<td colspan="2" class="reserve_pr_date"><?=$date?></td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th>적립내용</th>
					<td><!--<a href="#" rel="external">-->
						<em><?=$row->content?></em>
					</td>
				</tr>
				<tr>
					<th>결제금액</th>
					<td>
					<?
						if(strlen($orderprice)>0 && $orderprice>0) {
							echo number_format($orderprice);
						} else {
							echo "-";
						}
					?>원
					</td>
				</tr>
				<tr>
					<th class="lastTH">적립내역</th>
					<td class="lastTD"><span class="point6"><?=number_format($row->reserve)?>원</span></td>
				</tr>
				<!--</a>-->
				</tbody>
			</table>

			<?
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"reserve_pr_table\"><tr height=\"28\"><th align=\"center\">해당내역이 없습니다.</th></tr></table>";
		}
?>
		</ul>
		</div>
		</div>


		<div id="paging_container">
			<div id="paging_box">
				<ul>
					<?
						_getPage($totalRecord,$recordPerPage,$pagePerBlock,$currentPage,$pagetype); 
					?>
				</ul>
			</div>
		</div>


	</div>
	<!-- //적립금 -->
	
</div>

<hr>

<? 
//include_once('footer.php'); 
?>
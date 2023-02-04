
<div id="content">
	<div class="h_area2">
		<h2><?=$_GET[search]?>&nbsp;검색결과</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	
	<!-- 상품리스트 상단 -->
	<div class="pr_navi">
			<select class="basic_select" onChange="ChangeSort(this.value)">
				<option value="">최근등록순</option>
				<option value="price_desc" <?if($_GET[sort]=="price_desc") {echo "selected";}?>>높은가격순</option>
				<option value="price" <?if($_GET[sort]=="price") {echo "selected";}?>>낮은가격순</option>
				<option value="name" <?if($_GET[sort]=="name") {echo "selected";}?>>상품명 순</option>
				<option value="name_desc" <?if($_GET[sort]=="name_desc") {echo "selected";}?>>상품명 역순</option>
				<option value="reserve_desc" <?if($_GET[sort]=="reserve_desc") {echo "selected";}?>>적립금 높은순</option>
				<option value="reserve" <?if($_GET[sort]=="reserve") {echo "selected";}?>>적립금 낮은순</option>
				<option value="production_desc" <?if($_GET[sort]=="production_desc") {echo "selected";}?>>제조사 이름순</option>
				<option value="production" <?if($_GET[sort]=="production") {echo "selected";}?>>제조사 이름역순</option>
			</select>
			<button type="button" class="btn_list_type01 active"><span class="vc">갤러리형</span></button>
			<button type="button" class="btn_list_type02"><span class="vc">리스트형</span></button>
	</div>
	<!-- //상품리스트 상단 -->

	<!-- 상품리스트-타입1 -->
	<div class="">
		<ul class="pr_list pr_type1">	

<?
		if($search){
		$tag_0_count = 2; //전체상품 태그 출력 갯수
		//번호, 사진, 상품명, 제조사, 가격
		$tmp_sort=explode("_",$sort);
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
		}
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else $sql.= "ORDER BY a.productname ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
		

		
		$i=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			
	
?>
			<li><a href="productdetail_tab01.php?productcode=<?=$row->productcode?><?=$add_query?>&sort=<?=$sort?>" rel="external"><img src="../data/shopimages/product/<?=urlencode($row->tinyimage)?>" alt="상품명 이미지" class="pr_pt"><div class="pr_txt"><strong class="pr_name"><?=cutStr($row->productname,22)?></strong><em class="pr_price2"><?=number_format($row->consumerprice)?></em><em class="pr_price"><?=number_format($row->sellprice)?></em>원</div></a>
			
			<?
			if ($row->quantity=="0") echo soldout();			
			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
			if($reserveconv>0) {
				echo "<img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</td>\n";
			}
			echo "</li>";


			$i++;
		
		}
		mysql_free_result($result);
}
?>
		
		</ul>
	</div>



		<?
		if($search){
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
			$print_page = "<span class=\"pg_num pg_num_on\">1</span>";
		}
		}
?>

<div class="pg pg_num_area3">
<?=$a_prev_page?>
<span class="pg_area"><?=$print_page?></span>
<?=$a_next_page?>
</div>

	
	<!-- //페이지네비 -->
</div>

<hr>
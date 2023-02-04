<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$reviewimagedir = $Dir."data/shopimages/productreview/";

	//리스트 세팅
	$setup[page_num] = 10;
	$setup[list_num] = 10;

	$type=$_POST['type'];
	$productcode=$_POST['productcode'];

	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];

	if ($block != "") {
		$nowblock = $block;
		$curpage  = $block * $setup[page_num] + $gotopage;
	} else {
		$nowblock = 0;
	}

	if (($gotopage == "") || ($gotopage == 0)) {
		$gotopage = 1;
	}

	$addsql="";
	if(strlen($type)>0){
		switch($type){
			case "photo":
				$addsql.="AND img IS NOT NULL AND img != '' ";
				break;
			case "best":
				$addsql.="AND best = 'Y' ";
				break;
			case "basic":
				$addsql.="AND img IS NULL OR img = '' ";
				break;
			case "all":
			default:
				break;
		}
	}

	$qry="WHERE productcode='".$productcode."' ";
	if($_data->review_type=="A") $qry.= "AND display='Y' ";
	$sql="SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
	$sql.=$qry;
	$sql.=$addsql;
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);

	$t_count = (int)$row->t_count;
	$totmarks = (int)$row->totmarks;
	$marks=@ceil($totmarks/$t_count);
	mysql_free_result($result);

	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	//상품정보 호출
	$sql = "SELECT a.* FROM tblproduct AS a LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode='".$productcode."' ";
	$result=mysql_query($sql,get_db_conn());

	if($row=mysql_fetch_object($result)){
		$_pdata=$row;
	}
?>

<ul class="review_content">
	<?
		$sql = "SELECT * FROM tblproductreview ".$qry." ";
		$sql.= $addsql;
		$sql.= "ORDER BY date DESC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
		$j=0;
		while($row=mysql_fetch_object($result)){
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$j);

			$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
			$content=explode("=",$row->content);
			$attechfile=$row->img;

			#이미지 처리부분
			if(strlen($attechfile)>0){
				$imagearea = '<img src="'.$src.'" '.$size.' />';
				$viewtype ="<img src=\"skin/default/img/icon_photo.png\" alt=\"\" /> ";
			}else{
				$imagearea = $viewstar;
			}
	?>
	<li onclick="view_review('<?=$j?>')">
		<div style="overflow:hidden;">
			<p class="review_writer" style="float:left;"><?=$date?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$row->name?></p>
			<p class="review_point" style="float:right;">
				<?
					for($i=0;$i<$row->marks;$i++) {
						echo "<span>★</span>";
					}
					for($i=$row->marks;$i<5;$i++) {
						echo "★";
					}
				?>
			</p>
		</div>

		<p class='review_prname' style='color:#aaa;'><?=$_pdata->productname?></p>

		<p>
			<?
				echo "<p class=\"review_text\" style='position:relative;width:100%;'>";
				echo "<span style='width:90%;'>".$content[0]."</span>";
				echo "<span style='position:absolute;bottom:0px;right:0px;'>".$viewtype."</span>";
				echo "</p>";

				if(strlen($content[1])>0) echo "<img src=\"".$Dir."images/common/review/review_replyicn.gif\" border=0 align=absmiddle>";
			?>
		</p>
	</li>

	<li id="reviewspan" style="display:none;margin:0px;margin-top:-1px;">
		<?
			if(!empty($row->img) && file_exists($Dir.DataDir."shopimages/productreview/".$row->img)){
				echo "<img src=\"".$Dir.DataDir."shopimages/productreview/".$row->img."\" border=\"0\" style='max-width:100%;' />\n";
			}
			echo "<p>".nl2br($content[0]);
			if(strlen($content[1])>0) {
				echo "<br/><br/><img src=\"".$Dir."images/common/review/review_replyicn2.gif\" align=absmiddle border=0> ".nl2br($content[1]);
			}
			echo "</p><a href=\"javascript:view_review(".$j.")\"><div class=\"review_close\"></div></a>";
		?>
	</li>
	<?
			$j++;
		}
		mysql_free_result($result);

		if($j==0) {
			echo "<li style='text-align:center;'>등록된 사용후기가 없습니다.</li>\n";
		}
	?>
</ul>

<?
	if($j != 0) {
		$total_block = intval($pagecount / $setup[page_num]);

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(\"review\",0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(\"review\",".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(\"review\",".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
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
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(\"review\",".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(\"review\",".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(\"review\",".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"prlist\">1</FONT>";
		}
	}
?>
<div class="product_page" style="display:none;">
	<?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?>
</div>

<script type="text/javascript">
	function view_review(cnt,tp) {
		if(typeof(document.all.reviewspan)=="object" && typeof(document.all.reviewspan.length)!="undefined") {
			for(i=0;i<document.all.reviewspan.length;i++) {
				if(cnt==i) {
					if(document.all.reviewspan[i].style.display=="none") {
						document.all.reviewspan[i].style.display="";
					} else {
						document.all.reviewspan[i].style.display="none";
					}
				} else {
					document.all.reviewspan[i].style.display="none";
				}
			}
		} else {
			if(document.all.reviewspan.style.display=="none") {
				document.all.reviewspan.style.display="";
			} else {
				document.all.reviewspan.style.display="none";
			}
		}
	}
</script>
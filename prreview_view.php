<?
//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 15;

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

$colspan=3;
if($reviewdate!="N") $colspan=4;
$qry = "WHERE productcode='".$productcode."' ";
if($_data->review_type=="A") $qry.= "AND display='Y' ";
$sql = "SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
$sql.= $qry;

//echo $sql;

$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
$totmarks = (int)$row->totmarks;
$marks=@ceil($totmarks/$t_count);
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
?>

	
<section class="detail_03_view">
<?
	$sql = "SELECT * FROM tblproductreview ".$qry." and num = '$_GET[num]'";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$j);
		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$content=explode("=",$row->content);
?>

			<div class="view_type1">
				<div class="title_wrap">
					<span class="star_wrap">
						<?
						for($i=0;$i<$row->marks;$i++) {
							echo "<span class=\"on\"></span>";
						}
						?>
					</span>
					<em class="point1"><?=$row->name?></em> / <em><?=$date?></em>
					<strong><?=$content[0]?></strong>
				</div>
				<div class="btn_type2">
					<a href="javascript:history.back()" class="button white bigrounded" rel="external"><span>목록으로</span></a>
				</div>
			</div>
<?
	mysql_free_result($result);
?>
</section>
<!-- //TAB3-상품평 -->
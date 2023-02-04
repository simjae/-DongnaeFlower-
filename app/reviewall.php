<?php 
include_once("header.php");
include_once($Dir."app/inc/paging_inc.php");

if ($_data->ETCTYPE["REVIEW"]!="Y") {
	echo "<html><head><title></title></head><body onload=\"alert('사용후기 모음 게시판을 이용할 수 없습니다.');location.href='/m/';\"></body></html>";exit;
}

$tmp_filter=explode("#",$_data->filter);
$filter_array=explode("REVIEWROW",$tmp_filter[1]);
$reviewrow=(int)$filter_array[1];
if($reviewrow<8) $reviewrow=8;

$code=$_REQUEST["code"];

$codeA=(substr($code,0,3)!=""?substr($code,0,3):"000");
$codeB=(substr($code,3,3)!=""?substr($code,3,3):"000");
$codeC=(substr($code,6,3)!=""?substr($code,6,3):"000");
$codeD=(substr($code,9,3)!=""?substr($code,9,3):"000");

$sort=(int)$_POST["sort"];
$listnum=(int)$_POST["listnum"];
$reviewtype= !_empty($_POST['reviewtype'])?trim($_POST['reviewtype']):"";
if($sort>1) $sort=0;	//0:최근등록순, 1:높은평점순
if($listnum<=0) $listnum=$reviewrow;

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = $listnum;

$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1; 

$itemcount = 10; // 페이지당 게시글 리스트 수 

$rSql="SELECT * FROM tblproductreview";
$rResult=mysql_query($rSql,get_db_conn());
$rNums=mysql_num_rows($rResult);
$rowcount=$rNums;

if($imgwidth<10) $imgwidth=55;

$qry = "WHERE 1=1 ";
$qry.= "AND a.productcode=b.productcode ";
if($_data->review_type=="A") $qry.= "AND a.display='Y' ";
$qry.= "AND b.display='Y' ";

$sql = "SELECT a.num, a.id, a.name, a.marks, a.date, a.content, b.productcode, b.productname, b.tinyimage, b.quantity, b.selfcode ";
$sql.= "FROM tblproductreview a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
$sql.= $qry;
$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
if($sort==0) $sql.= "ORDER BY a.date DESC ";
else if($sort==1) $sql.= "ORDER BY marks DESC ";
$sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;
?>

<div id="content">
	<div class="h_area2">
		<h2>상품후기</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div class="reviewwrap">
		<!--<div class="reviewcount">** 후기를 클릭하시면 상세내용을 볼 수 있습니다.</div>-->
		<div class="review_list">
			<?
				if(false !== $listRes = mysql_query($sql,get_db_conn())){
					$listrowcount = mysql_num_rows($listRes);
					if($listrowcount>0){
						$cnt=0;
						while($listRow = mysql_fetch_object($listRes)){
							$date=substr($listRow->date,0,4)."-".substr($listRow->date,4,2)."-".substr($listRow->date,6,2);
							$content=explode("=",$listRow->content);
			?>
						<div style="padding:15px 0px;border-bottom: 1px solid #ebebeb;">
							<div style="overflow:hidden;" onclick="view_review(<?=$cnt?>);">
								<div style="float:left;width:22%;margin-left:10px;font-size:0px;text-align:center;">
									<?
										echo "<img src=\"".$Dir.DataDir."shopimages/product/".$listRow->tinyimage."\" border=0 width=\"100%\" alt=\"\" />";
									?>
								</div>
								<div style="float:right;width:72%;">
									<p class="prname"><?=$listRow->productname?><A HREF="productdetail_tab01.php?productcode=<?=$listRow->productcode?>"><img src="/m/images/btn_reviewprview.gif" border="0" alt="" /></a></p>
									<p class="title"><?=titleCut(30,$listRow->content)?></p>
									<p class="writer"><?=$date?> <span class="hline">|</span> <?=$listRow->name?></p>
									<p class="starpoint">
										<? //별점출력
											for($i=0;$i<$listRow->marks;$i++){
												echo "<FONT color=#000000>★</FONT>";
											}
											for($i=$listRow->marks;$i<5;$i++){
												echo "<FONT color=#DEDEDE>★</FONT>";
											}
										?>
									</p>
								</div>
							</div>

							<div id="reviewspan" style="display:none;">
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr>
										<?
											if(!empty($listRow->img) && file_exists($Dir.DataDir."shopimages/productreview/".$listRow->img)){
												echo "<td><img src=\"".$Dir.DataDir."shopimages/productreview/".$listRow->img."\" border=\"0\" width=\"100%\" /></td></tr><tr>\n";
											}
											echo "<td valign=\"top\" style=\"padding:10px;font-size:1em;\">".nl2br($content[0]);
											if(strlen($content[1])>0) {
												echo "<img src=\"".$Dir."images/common/review/review_replyicn2.gif\" align=absmiddle border=0> ".nl2br($content[1]);
											}
											echo "</td>\n";
										?>
									</tr>
								</table>
							</div>
						</div>
			<?
						$cnt++;
						}
					}else{
			?>
					<p class="err_td">등록된 후기가 없습니다.</p>
			<?
					}
				}
			?>
		</div>
	</div>

	<div id="page_wrap">
		<?
			$pageLink =$_SERVER['PHP_SELF']."?page=%u"; // 링크
			$pagePerBlock = ceil($rowcount/$listnum);
			$paging = new pages($pageparam);
			$paging->_init(array('page'=>$currentPage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
			echo $paging->_result('fulltext');
		?>
	</div>
</div>

<script type="text/javascript">
<!--
	function view_review(cnt) {
		if(typeof(document.all.reviewspan)=="object" && typeof(document.all.reviewspan.length)!="undefined") {
			for(i=0;i<document.all.reviewspan.length;i++) {
				if(cnt==i) {
					if(document.all.reviewspan[i].style.display=="none") {
						document.all.reviewspan[i].style.display="block";
					} else {
						document.all.reviewspan[i].style.display="none";
					}
				} else {
					document.all.reviewspan[i].style.display="none";
				}
			}
		} else {
			if(document.all.reviewspan.style.display=="none") {
				document.all.reviewspan.style.display="block";
			} else {
				document.all.reviewspan.style.display="none";
			}
		}
	}
//-->
</script>

<? include_once('footer.php'); ?>
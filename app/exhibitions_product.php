<?
include_once('header.php');
include_once($Dir."app/inc/paging_inc.php");

$currentPage=$_REQUEST["page"];

$event_category=$_REQUEST["event_category"];

if(!$currentPage){ $currentPage=1; }
?>

<div class="msExhibitionProduct">
	<div class="msExhibitionTitle">
		<h4>기획전</h4>
	</div>

	<div class="msExhibitionList">
		<!--
		<span class="basic_select">
			<select name="select">
				<option>최근등록순</option>
				<option>낮은가격순</option>
				<option>상품평많은순</option>
			</select>
		</span>
		-->
		<ul class="list">
			<?
				if($event_category){
					$sql_add = "	 and event_category = '".$event_category."' ";
				}

				$sql_add .= "AND ( status = '승인' and (sdate <= '".date("Y-m-d")."' and  edate >= '".date("Y-m-d")."' )) ";

				$path = $Dir."data/event/";
				$query = "SELECT * FROM tblevent WHERE go_start='1' $sql_add ORDER BY x_order ASC";
				$res = mysql_query($query);
				$cnt = 0;
				while( $eRow = mysql_fetch_object( $res ) ) {
					if($eRow->image){
						$sigm = $path.$eRow->image;
					}else{
						$sigm = $Dir."images/no_file.gif";
					}
			?>
			<li>
				<a href="exhibitions_product_list.php?eid=<?=$eRow->idx?>" target="_self">
					<div class="exhibitions_BannerBg" style="background:url('<?=$sigm?>') no-repeat;height:140px;background-size:cover;background-position:center;"></div>
					<div class="exhibitionsTable">
						<div class="exhibitionsCell">
							<? if($eRow->view_imgw == "1"){ ?>
							<h2><?=$eRow->title?></h2>
							<span class="explanation"><?=$eRow->title_memo?></span>
							<? } ?>
						</div>
					</div>
				</a>
			</li>
			<? } ?>
		</ul>

		<? /*
		<div class="product_page" id="page_wrap">
			<?
				$pageLink=$_SERVER['PHP_SELF']."?sort=".$sort."&page=%u";
				$pagePerBlock = ceil($rowcount/$itemcount);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$currentPage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
		</div>
		*/ ?>
	</div><!-- msVenderSaleProductList -->
</div>

<? include_once('footer.php'); ?>
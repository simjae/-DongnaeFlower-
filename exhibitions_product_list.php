<?
include_once('header.php');
include_once($Dir."app/inc/paging_inc.php");

$prsection_type=$_data->design_prspecial;

$sort=$_REQUEST["sort"];
$listnum=(int)$_REQUEST["listnum"];
$eid=$_REQUEST["eid"];
$cid=$_REQUEST["cid"];

$eidx=$_REQUEST["eidx"];




if(!$eid){
	$onviewQuery = " SELECT idx FROM tblevent WHERE 1=1 order by idx DESC LIMIT 1 ";
	$onviews = mysql_query($onviewQuery);
	$onviewss = mysql_fetch_array($onviews);
	if($onviewss[idx]){
		$eid = $onviewss[idx];
	}
}



if($listnum<=0) $listnum=16;

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = $listnum;

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


$t_count=0;

$sql = "SELECT COUNT( distinct a.productcode) as t_count ";
$sql.= "FROM tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.= "WHERE a.eventidx='{$eid}' AND a.display='Y' ";
if( $eidx != "" ) $sql .= " AND a.eventcateidx='{$eidx}' ";
//echo $sql;
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;


//이벤트 정보 구하기
$eQuery = "SELECT * FROM tblevent WHERE idx='{$eid}'";
$eRes = mysql_query($eQuery);
$eRow = mysql_fetch_array($eRes);

?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function ChangeSort(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	document.form2.submit();
}

function ChangeListnum(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.listnum.value=val;
	document.form2.submit();
}

function ChangeNum(obj) {
	document.form2.listnum.value=obj.value;
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
//-->
</SCRIPT>


<div class="msExhibitionProduct_list">
	<? /*
	<div class="msExhibitionTitle_list">
		<h4><?//=$eRow[title]?></h4>
		<p></p>
	</div>
	*/ ?>

	<div class="contsTAB"><?=$eRow[contents]?></div>

	<div class="msCategoryTable2">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><a href="./exhibitions_product_list.php?eid=<?=$eid?>">전체보기</a></td>
			<?
				$query = "SELECT * FROM tbleventcategory WHERE eidx='{$eid}' ORDER BY title ASC";
				$res = mysql_query($query);
				$z = 2;
				while( $eRow = mysql_fetch_array( $res ) ) {
					$notid = "";
						if($z%4 == 0){
							$notid = " class='lastTd' ";
							echo "</tr><tr>";
						}else{
							$notid = "";
						}
			?>
				<td <?=$notid?>><a href="./exhibitions_product_list.php?eid=<?=$eid?>&eidx=<?=$eRow[idx]?>"><?=$eRow[title]?></a></td>
			<?
				$z++;
				}
			?>			
				<!--<td class="lastTd"><a href="./exhibitions_product.php?event_category=<?=urlencode('가공식품')?>">가공식품</a></td>-->
			</tr>
		</table>
	</div>

	<?
		$_date="";
		$_best_desc="";
		$_price="";
		$_price_desc="";

		switch(trim($sort)){
			case "best_desc":
				$_best_desc=" selected ";
				break;

			case "price":
				$_price=" selected ";
				break;

			case "price_desc":
				$_price_desc=" selected ";
				break;

			case "reserve_desc":
				$_reserve_desc=" selected ";
				break;

			case "date":
				$_date=" selected ";
				break;

			case "event_desc":
			default:
				$_event_order=" selected ";
				break;
		}
	?>

	<div class="msExhibitionList">
		<span class="basic_select">
			<select name="select" onchange="ChangeSort(this.value);">
				<option value="" <?=$_event_order?>>추천순</option>
				<option value="date" <?=$_date?>>신규등록순</option>
				<option value="best_desc" <?=$_best_desc?>>인기상품순</option>
				<option value="price" <?=$_price?>>낮은가격순</option>
				<option value="price_desc" <?=$_price_desc?>>높은가격순</option>
			</select>
		</span>

		<ul class="list">
			<?
				$print_page="";
				$a_first_block="";
				$a_prev_page="";
				$a_next_page="";
				

				if($t_count<=0) {
					echo "<li style='width: 100%;text-align:center;'>등록된 상품이 없습니다.</li>";
				} else {
					$tag_0_count = 2; //전체상품 태그 출력 갯수
					if(!$sort){
						$sort = "event_desc";
					}
					$res = _getEventProductList($eid,$eidx,$gotopage,$setup["list_num"],$sort); //ext/product_func.php

					foreach($res as $i=>$row){
						// 도매 가격 적용 상품 아이콘
						$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

						// 할인율 표시
						$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."%</strong>↓" : "";

						$disRate = round( ( ($row->consumerprice - $row->sellprice) / $row->consumerprice )* 100 );
						
						$memberpriceValue = $row->sellprice;
						$strikeStart = $strikeEnd = '';
						$memberprice = 0;
						if($row->discountprices>0 AND isSeller() != 'Y' ){
							$memberprice = number_format($row->sellprice - $row->discountprices);
							$strikeStart = "<strike>";
							$strikeEnd = "</strike>";
							$memberpriceValue = ($row->sellprice - $row->discountprices);
						}

						$number = ($t_count-($setup["list_num"] * ($gotopage-1))-$i);
						$tableSize = $_data->primg_minisize + 12;
						
						if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
						 $imgMM = "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
							$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
							if($_data->ETCTYPE["IMGSERO"]=="Y") {
								if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $imgMM .= "height=\"".$_data->primg_minisize2."\" ";
								else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $imgMM .= "width=\"".$_data->primg_minisize."\" ";
							} else {
								if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $imgMM .= "width=\"".$_data->primg_minisize."\" ";
								else if ($width[1]>=$_data->primg_minisize) $imgMM .= "height=\"".$_data->primg_minisize."\" ";
							}
						} else {
							$imgMM = "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
						}
			?>
			
			<li>
				<div><a href="productdetail_tab01.php?productcode=<?=$row->productcode?>"><?=$imgMM?></a></div>
				<p><a href="productdetail_tab01.php?productcode=<?=$row->productcode?>"><span class="prname"><?=viewproductname($row->productname,$row->etctype,$row->selfcode)?></span></a></p>
				<p><?	if($row->consumerprice!=0) {
							echo "<strike>".number_format($row->consumerprice)."원</strike>&nbsp;&nbsp;";
						}
						
						if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)){
							echo $dicker;
						}else if(strlen($_data->proption_price)==0){
							echo $wholeSaleIcon.number_format($row->sellprice)."원";
							//if(strlen($row->option_price)!=0) echo "(기본가)";
						}else{
							if(strlen($row->option_price)==0){
								echo $wholeSaleIcon.number_format($row->sellprice)."원";
							}else{
								echo ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
							}
						}
						//if($row->discountRate>0) echo "<br /><span class=\"discount\">(".$discountRate.")</span>";
					?>
				</p>
			</li>
			<?
					}
			}
			?>
		</ul>

		<div class="product_page" id="page_wrap">
			<?
				$pageLink=$_SERVER['PHP_SELF']."?sort=".$sort."&eid=".$eid."&gotopage=%u";
				$pagePerBlock = ceil($rowcount/$itemcount);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$gotopage,'total_page'=>$pagecount,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
		</div>
	</div><!-- msVenderSaleProductList -->
</div>

<form name=form2 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=eid value="<?=$eid?>">
<input type=hidden name=cid value="<?=$cid?>">
</form>

<? include_once('footer.php'); ?>
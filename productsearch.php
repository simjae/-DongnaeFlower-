<?
include_once("./header.php");
include_once($Dir."app/inc/paging_inc.php");

$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1;
$recordPerPage = 12; // 페이지당 게시글 리스트 수 
$pagePerBlock = 5; // 블록 갯수

$prsearch=$_REQUEST[prsearch];

$terms = isset($_REQUEST[terms])? $_REQUEST[terms]:"";
$sc_text = isset($_REQUEST[sc_text])? $_REQUEST[sc_text]:$prsearch;
$mode = isset($_REQUEST[mode])? $_REQUEST[mode]:"";

if(!empty($terms) && !empty($sc_text) && !empty($mode)) {
	
	$sql = "SELECT  sellprice, consumerprice, reserve, reservetype, productname, tinyimage, maximage, quantity, productcode, youtube_url, youtube_prlist, youtube_prlist_imgtype FROM tblproduct WHERE ";
	
	switch($terms){
		case "productname":
			$sql.= "(UPPER(productname) LIKE UPPER('%".$sc_text."%')) ";
		break;
		case "keyword":
			$sql.= "(UPPER(keyword) LIKE UPPER('%".$sc_text."%')) ";
		break;
		case "production":
			$sql.= "(UPPER(production) LIKE UPPER('%".$sc_text."%')) ";
		break;
		default:
			$sql.= "1=1 ";
		break;
	}
	$sql.= "AND display='Y' ";
	$sql.="ORDER BY date DESC ";
	
	$cnt_result = mysql_query($sql,get_db_conn());
	$cnt = mysql_num_rows($cnt_result);	
	mysql_free_result($cnt_result);

	$sql.="LIMIT ".($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
	$result = mysql_query($sql, get_db_conn());
}

$pagetype = "product";
$variable = "mode=".$mode."&terms=".$terms."&sc_text=".$sc_text."&";

function len_title($title) {
	$string=mb_substr($title,0,9,'UTF-8');   
	
	if($string!=$title)
		$string.="..";

	return $string;
}

?>

<div id="content">
	<div class="h_area2">
		<h2>상품검색</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div class="sc_wrap">
		<div class="sc_terms">
			<form name="searchForm" method="get" action="<?=$_SERVER[PHP_SELF]?>">
				<span class="basic_select">
					<select name="terms" class="terms">
						<option value="productname">상품명</option>
						<option value="keyword">키워드</option>
						<option value="production">제조사</option>
					</select>
				</span>
				<? if($sellvidx){ ?>
				<input type="hidden" name="sellvidx" value="<?=$sellvidx?>" />
				<? } ?>
				<input type="text" name="sc_text" value="<?=$sc_text?>" class="basic_input" />
				<input type="button" name="btn_submit" id="btn_submit" class="basic_button" value="검색" />
				<input type="hidden" name="mode" value="search" />
			</form>
			<div style="clear:both"></div>
		</div>

		<div class="pr_list" role="main">
			<ul class="pr_type1 tiles-wrap animated" id="wookmark1">
			<?
				if($cnt >= 1){
					$k=0;
					while($row = mysql_fetch_object($result)){

						$wholeSaleIcon="";
						if($row->isdiscountprice == 1 AND isSeller()){
							$wholeSaleIcon='<img src="/images/common/wholeSaleIcon.gif" /> ';
							$row->sellprice=$row->productdisprice;
						}

						#####################상품별 회원할인율 적용 시작#######################################
						$discountprices = getProductDiscount($productcode);
						if($discountprices > 0 AND isSeller() != 'Y' ){
							$memberprice = $row->sellprice - $discountprices;
							$row->sellprice = $memberprice;
						}
						#####################상품별 회원할인율 적용 끝 #######################################

						$viewPrice="";
						$dicker=new_dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1);
						if($memberprice > 0) {	// 회원 로그인(및 등급 할인) 일 경우에만 
							$viewPrice.="<img src='/images/common/memsale_icon.gif' /> ";
						}

						if (count($dicker['memOpenData']) > 0) {
							if ($dicker['memOpenData']['type'] == "img") {
								$viewPrice.="<img src='".$dicker['memOpenData']['value']."' />";
							} else {
								$viewPrice.="<span class=''>".$dicker['memOpenData']['value']."</span>";
							}
						} else if (strlen($_data->proption_price) == 0) {
							$viewPrice.=$wholeSaleIcon.number_format($row->sellprice)."원";
						} else {
							if (strlen($row->option_price) == 0) {
								$viewPrice.=$wholeSaleIcon.number_format($row->sellprice)."원";
							} else {
								$viewPrice.=ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
							}
						}

						$img = "../data/shopimages/product/".urlencode($row->tinyimage);

						if(strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true){
							$background_url=$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage);
						}else{
							$background_url=$Dir."images/no_img.gif";
						}

						$prdetail_link="productdetail_tab01.php?productcode=".$row->productcode.($sellvidx?"&vidx=".$sellvidx:"");

						$youtube_url=$row->youtube_url;
						$youtube_prlist=$row->youtube_prlist;
						$youtube_prlist_imgtype=$row->youtube_prlist_imgtype;

						//동영상(유튜브) 등록일 때 상품이미지 교체
						if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='Y'){
							$youtube_code=str_replace("https://youtu.be/","",$youtube_url);
							$prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
							$background_image=str_replace("https://youtu.be/","",$youtube_url);
							$background_url="https://img.youtube.com/vi/".$background_image."/sddefault.jpg";

						}else if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='D'){
							$youtube_code=str_replace("https://youtu.be/","",$youtube_url);
							$prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
							$background_image=$youtube_prlist_file;
							$background_url=$Dir.DataDir."shopimages/product/".$background_image;
						}

						$width=getimagesize($background_url);
						if($width[1]>$width[0]){ //세로가 가로보다 길 때
							$background_size="100% auto";
						}else{ //가로가 세로보다 길 때
							$background_size="auto 100%";
						}
			?>
					<li>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td style="position:relative;border-bottom:1px solid #eeeeee;font-size:0px;line-height:0%">
									<div class="product_img">
									<?
										if(strlen($youtube_url)>0 && $youtube_prlist=='Y'){ //동영상(유튜브) 이미지는 퀵툴 미출력
											echo "<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;'><A HREF=".$prdetail_link."><img src='/images/movie_icon.png' alt='' /></a></div>";
										}
									?>
									<a href=<?=$prdetail_link?> rel="external" style="display:block;width:100%;height:100%;background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>;background-position:center;font-size:0px;">
										<img src="/images/common/trans.gif" width="100%" alt="상품 이미지" class="pr_pt" />
									</a>
								</td>
							</tr>
							<tr>
								<td valign="top" style="padding:0.5em;">
									<p class="p_productname"><?=strip_tags($row->productname)?></p>

									<? if($row->consumerprice != "0"){ ?>
										<p class="p_consumerprice"><?=number_format($row->consumerprice)?>원</p>
									<? } ?>
										<p class="p_sellprice"><?=$viewPrice?></p>
									<?
										if ($row->quantity=="0") echo "<p style=\"text-align:left\">".soldout()."</p>";

										$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
										if($reserveconv>0) {
									?>
										<p class="p_reserve"><img src="<?=$Dir?>images/common/reserve_icon.gif" border="0" align="absmiddle"> <?=number_format($reserveconv)?>원</p>
									<? } ?>
								</td>
							</tr>
						</table>
					</li>
			<?
					$k++;
					}
				}else{
					if($cnt === null){
			?>
						<li style="width:100%;height:30px;line-height:30px;">검색어를 입력해 주세요</li>
			<?
					}else{
			?>
						<li style="width:100%;height:30px;line-height:30px;">검색된 상품이 존재하지 않습니다</li>
			<?
					}
				}
			?>
		</div>
	</div>
</div>

<div id="paging_container">
	<div id="paging_box">
		<ul>
			<?
				_getPage($cnt,$recordPerPage,$pagePerBlock,$currentPage,$pagetype,$variable); 
			?>
		</ul>
	</div>
</div>

<!-- Include the plug-in -->
<script src="./js/wookmark.js"></script>

<!-- Once the page is loaded, initalize the plug-in. -->
<script type="text/javascript">
	window.onload = function () {
		var wookmark1 = new Wookmark('#wookmark1', {
			outerOffset: 0, // Optional, the distance to the containers border
			itemWidth:0 // Optional, the width of a grid item
		});
	};

	$("#btn_submit").click(function(){
		var _form = document.searchForm;

		if($("input[name=sc_text]").val() == "" || $("input[name=sc_text]").val() == ""){
			alert("검색어를 입력하세요.");
			$("input[name=sc_text]").focus();
			return false;
		}else{
			$("input[name=sc_text]").hide();
			_form.submit();
			return;
		}
	});
</script>

<? include "footer.php"; ?>
<?
//메인 기획상품
$imagesrc = $Dir."data/shopimages/product/";

if($pm_idx){

//섬네일 효과 설정값 가져오기
$preffect_sql="SELECT primg_effect, range_effect, primg_effect_section, radius_use, radius_value, radius_position FROM tblshopinfo";
$preffect_result=mysql_query($preffect_sql,get_db_conn());
$preffect_row=mysql_fetch_object($preffect_result);
$prradius_position=explode(",",$preffect_row->radius_position);

//이미지 라운드 효과 적용(모바일은 관리자 설정값/2로 적용)
if($preffect_row->radius_use=='Y' && $preffect_row->radius_value>0){
	$prradius1=($prradius_position[1]=="Y"?$preffect_row->radius_value/2:"0");
	$prradius2=($prradius_position[2]=="Y"?$preffect_row->radius_value/2:"0");
	$prradius3=($prradius_position[4]=="Y"?$preffect_row->radius_value/2:"0");
	$prradius4=($prradius_position[3]=="Y"?$preffect_row->radius_value/2:"0");
	$prradius="border-radius:".$prradius1."px ".$prradius2."px ".$prradius3."px ".$prradius4."px;overflow:hidden;";
}

$mainPRSQL = "SELECT * FROM tblmobileplanningmain WHERE display = 'Y' and pm_idx = '".$pm_idx."' ";
$mainPRSQL .= "ORDER BY pm_idx ASC ";
$webzinepage=0;
$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // 원본파일 경로
$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // 썸내일 저장 경로
$quality = 100;
$savewideloc = $Dir.DataDir."shopimages/wideimage/";
$saveminloc = $Dir.DataDir."shopimages/product/";

if(false !== $mainPRRes = mysql_query($mainPRSQL,get_db_conn())){
	$rowcount = mysql_num_rows($mainPRRes);
	if($rowcount>0){
		$origPRList=array();
		$mainPRList=$title=$dplimitcount=$limitcount=$dpPRcount=$maximage=$src=$productcode=	$sellprice=$vendername=$venderidx=$wideimage="";
		$prSQL = "SELECT p.productcode, p.productname, p.sellprice, p.consumerprice, p.discountRate, p.quantity, p.brand, p.prmsg, p.tinyimage, p.minimage, p.maximage, p.wideimage, p.vender, p.reservation, p.option1, p.option2, p.option_quantity, p.etctype, p.selfcode, p.youtube_url, p.youtube_prlist, p.youtube_prlist_imgtype, p.youtube_prlist_file, v.com_name ";
		$prSQL .= "FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ";
		$prSQL .= "ON(p.vender = v.vender) ";
		$prSQL .= "WHERE (p.pridx != '' OR p.pridx IS NOT NULL) ";

		$m = 1;
		while($mainPRRow = mysql_fetch_assoc($mainPRRes)){

			$origPRList = $mainPRRow['product_list']; //섹션별 진열된 상품 리스트
			$mainPRList = explode(",",$origPRList);
			$title = $mainPRRow['title']; // 섹션명 기본 HIT PRODUCT, MD PRODUCT, NEW PRODUCT, SPECIAL PRODUCT
			$dplimitcount = $mainPRRow['product_cnt']; //디스플레이 제한 카운터
			$realprSQL = "SELECT productcode FROM tblproduct WHERE productcode IN ('".implode("','",$mainPRList)."') AND display = 'Y' AND mobile_display = 'Y' ORDER BY FIELD(productcode,'".implode("','",$mainPRList)."') ";
	
			if(false !== $realprRes = mysql_query($realprSQL,get_db_conn())){
				$realprList = array();
				while($realrow = mysql_fetch_assoc($realprRes)){
					array_push($realprList,$realrow['productcode']);
				}
				@mysql_free_result($realprRes);
			}
			$dpPRcount = count($realprList); // 실제진열된 상품 수
			if($dpPRcount>=$dplimitcount){
				$limitcount = $dplimitcount;
			}else{
				$limitcount = $dpPRcount;
			}

			switch($mainPRRow['display_type']){
				case "gallery": //갤러리 타입
?>
					<div class="main_prlist_list">
						<div class="wrapper">
							<div >
								<h1 onclick="location.href='#'"><img src = "/m/images/skin/default/select_product.png" style="max-width: 100%; height: auto; cursor:pointer;"></h1>		
							</div>
							<? if(!empty($origPRList)){ ?>
								<div class="product_a" <?=($mainPRRow[gallery_type]=='B'?"style='margin:0px -15px;'":"")?>>
									<ul class="product_list" <?=($mainPRRow[gallery_type]=='B'?"style='display:flex;padding-left:10px;overflow-x:scroll;-webkit-overflow-scrolling:touch;'":"")?>>
										<?
											if($dpPRcount >0){
												for($i=0;$i<$limitcount;$i++){
													$gallerySQL = $prSQL."AND p.productcode = '".$realprList[$i]."' ";
													if(false !== $galleryRes = mysql_query($gallerySQL, get_db_conn())){
														$galleryRow = mysql_fetch_assoc($galleryRes);
														$maximage=$galleryRow['maximage'];
														$productcode=$galleryRow['productcode'];
														$productname=$galleryRow['productname'];
														$productmsg=$galleryRow['prmsg'];
														$prconsumerprice=number_format($galleryRow['consumerprice']);
														$sellprice=number_format($galleryRow['sellprice']);
														$discount=$galleryRow['discountRate'];
														$vendername=$galleryRow['com_name'];
														$venderidx=$galleryRow['vender'];
														$reservation=$galleryRow['reservation'];
														$option1=$galleryRow['option1'];
														$option2=$galleryRow['option2'];
														$optionquantity=$galleryRow['option_quantity'];

														if(strlen($reservation)>0 && $reservation != "0000-00-00"){
															$msgreservation="<span class=\"font-orange\">(예약)</span>";
															$datareservation="(".$reservation.")";
														}else{
															$msgreservation=$datareservation="";
														}

														$wholeSaleIcon="";
														if($galleryRow['isdiscountprice'] == 1 AND isSeller()){
															$wholeSaleIcon='<img src="/images/common/wholeSaleIcon.gif" /> ';
															$galleryRow['sellprice']=$galleryRow['productdisprice'];
														}

														#####################상품별 회원할인율 적용 시작#######################################
														$discountprices = getProductDiscount($productcode);
														if($discountprices > 0 AND isSeller() != 'Y' ){
															$memberprice = $galleryRow['sellprice'] - $discountprices;
															$galleryRow['sellprice'] = $memberprice;
														}
														#####################상품별 회원할인율 적용 끝 #######################################

														$viewPrice="";
														$dicker=new_dickerview($galleryRow['etctype'],$wholeSaleIcon.number_format($galleryRow['sellprice'])."원",1);
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
															$viewPrice.=$wholeSaleIcon.number_format($galleryRow['sellprice'])."원";
														} else {
															if (strlen($galleryRow['option_price']) == 0) {
																$viewPrice.=$wholeSaleIcon.number_format($galleryRow['sellprice'])."원";
															} else {
																$viewPrice.=ereg_replace("\[PRICE\]",number_format($galleryRow['sellprice']),$_data->proption_price);
															}
														}

														//상품평 수
														$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
														$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
														$row_cnt3=mysql_fetch_object($result_cnt3);
														$t_cnt3 = (int)$row_cnt3->t_count;


														if(strlen($galleryRow[tinyimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$galleryRow[tinyimage])==true){
															$background_url=$Dir.DataDir."shopimages/product/".urlencode($galleryRow[tinyimage]);
														}else{
															$background_url=$Dir."images/no_img.gif";
														}

														$prdetail_link="productdetail_tab01.php?productcode=".$productcode."";


														$youtube_url=$galleryRow['youtube_url'];
														$youtube_prlist=$galleryRow['youtube_prlist'];
														$youtube_prlist_imgtype=$galleryRow['youtube_prlist_imgtype'];
														$youtube_prlist_file=$galleryRow['youtube_prlist_file'];

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
										<li class="product_item" style="<?=($mainPRRow[gallery_type]=='B'?"flex:none;width:40%;margin-right:0px;padding-right:10px;padding-bottom:10px;'":"width:calc(".(100/$mainPRRow[gallery_nums])."% - 0px);")?>">
											<div class="product_view">
												<div class="product_img" style="<?=$prradius?>">
													<?
														if(strlen($youtube_url)>0 && $youtube_prlist=='Y'){ //동영상(유튜브) 이미지는 퀵툴 미출력
															echo "<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;'><A HREF=".$prdetail_link."><img src='/images/movie_icon.png' alt='' /></a></div>";
														}
													?>
													<a href=<?=$prdetail_link?> rel="external" style="display:block;width:100%;height:100%;background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>;background-position:center;font-size:0px;">
														<img src="/images/common/trans.gif" width="100%" alt="상품 이미지" class="pr_pt" />
													</a>
												</div>
												<? if($discount>0){ ?><div class="product_sale"><?=$discount?>%</div><? } ?>
											</div>

											<? if($productlist_basket=="Y"){ ?>
												<? $opti=0; ?>
												<form name="bfrm<?=$pm_idx.$productcode?>" id="bfrm<?=$pm_idx.$productcode?>" method="post" action="./basket.php">
													<? if(strlen($option1)>0){ ?>
														<input type="hidden" id="opt_idx_<?=$productcode?>" name="opt_idx[]" <?=$oneoption1?>/>
														<input type="hidden" id="opt_idx2_<?=$productcode?>" name="opt_idx2[]" <?=$oneoption2?>/>
														<input type="hidden" id="opt_quantity_<?=$productcode?>" name="opt_quantity[]" />
													<? } ?>
													<input type="hidden" name="price" />
													<input type="hidden" name="dollarprice" />
													<input type="hidden" name="code" />
													<input type="hidden" name="productcode" value="<?=$productcode?>" />
													<input type="hidden" name="ordertype" />
													<input type="hidden" name="opts" />

													<? if(!$dicker['memOpen']){ ?>
														<div class="product_amount">
															<div class="product_minus" onclick="change_quantity(bfrm<?=$pm_idx.$productcode?>.name,'dn');">-</div>
															<div class="product_quantity"><input type="text" name="quantity" value="1" /></div>
															<div class="product_plus" onclick="change_quantity(bfrm<?=$pm_idx.$productcode?>.name,'up');">+</div>

															<?
															//옵션유무 체크
															$optClass->setOptUse($productcode);
															$optClass->setOptType($productcode);

															if($optClass->optUse){ //옵션이 있는 상품일 때
																?>
																<div class="product_basket" onclick="optChecker('<?=$productcode?>','basket');">&nbsp;</div>
																<div class="product_buy" onclick="optChecker('<?=$productcode?>','ordernow');">&nbsp;</div>
															<? }else{ ?>
																<div class="product_basket" onclick="CheckForm(document.bfrm<?=$pm_idx.$productcode?>,'<?=$productcode?>','');">&nbsp;</div>
																<div class="product_buy" onclick="CheckForm(document.bfrm<?=$pm_idx.$productcode?>,'<?=$productcode?>','ordernow');">&nbsp;</div>
															<? } ?>
														</div>
													<? } ?>
												</form>
											<? } ?>

											<div class="product_info" <?=($productlist_quick=='Y'?"style=\"margin-bottom:65px;\"":"")?>>
												<div class="product_name"><a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external"><?=$productname?></a></div>
												<? if(strlen($productmsg)>0){ ?><div class="product_caption"><?=$productmsg?></div><? } ?>
												<? if($prconsumerprice>0){ ?><div class="product_discount"><?=$prconsumerprice?>원</div><? } ?>
												<div class="product_price">
													<?=$viewPrice?>
													<? if ($galleryRow['quantity']=="0") echo soldout(); ?>
												</div>
												<? if(strlen($reservation)>0 && $reservation != "0000-00-00"){ ?>
													<div class="product_reserve"><span><?=$msgreservation?></span> <?=$datareservation?></div>
												<? } ?>

											<div>

										<!--모바일 메인 아이콘-->
										<?
											$icoi = strpos(" ".$galleryRow['etctype'],"ICON=");
											if($icoi>0){
												$icon = substr($galleryRow['etctype'],strpos($galleryRow['etctype'],"ICON="));
												$icon = substr($icon,5,strpos($icon,"")-5);
												$num=strlen($icon);
												for($j=0;$j<$num;$j+=2){
													$temp=$icon[$j].$icon[$j+1];
										/*
													if($temp=='04'){
														$icon_name="NEW";
													}else if($temp=='13'){
														$icon_name="BEST";
													}else{
														$icon_name="HOT";
													}
										*/
													if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
														echo "<img src=\"http://".$_ShopInfo->shopurl.DataDir."shopimages/etc/icon".$temp.".gif\" align=\"absmiddle\" border=\"0\" />";
													} else if(strlen($temp)>0 && !preg_match("/^(U)[1-6]$/",$temp)) {
														echo "<span class='icon".$temp."'></span>";
													}
												}
											}

										?>
										<!--모바일 아이콘-->


											<div><? //echo viewproductname('',$galleryRow['etctype'],'');?></div>
											</div>

												
												<? if(strlen($vendername)>0){ ?>
													<div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');"><?=$vendername?></a></div>
												<? } ?>
											</div>

											<? if($productlist_quick == 'Y'){ //퀵메뉴 사용 설정 ?>
											<div class="product_communicate">
												<ul>
													<li><a href="productdetail_tab01.php?productcode=<?=$productcode?>&sort=#tapTop" rel="external"><span class="product_coment"><?=$t_cnt3?></span></a></li>

													<?
														$wish_chk = true;
														$wish_sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='".$productcode."' ";
														$wish_result = mysql_query($wish_sql, get_db_conn());
														$wish_row = mysql_fetch_object($wish_result);

														if($wish_row->cnt>0)
															$wish_chk = false;

														if(strlen($_ShopInfo->getMemid())>0){
															if($wish_chk){
													?>
															<li><a href="javascript:wishAjax('<?=$productcode?>', 'im<?=$i?>')" id="im<?=$i?>" class="btn_wishlist off"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
													<?		}else{ ?>
															<li><a href="javascript:wishAjax('<?=$productcode?>', 'im<?=$i?>')" id="im<?=$i?>" class="btn_wishlist on"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
													<?
															}
														}else{
													?>
														<li><a href="javascript:check_login()" id="im<?=$i?>" class="btn_wishlist off"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
													<? } ?>
													<li><a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external"><span class="product_more"></span></a></li>
												</ul>
											</div>
											<? } ?>

										</li>
										<?
													}
													mysql_free_result($galleryRes);

													if($mainPRRow[gallery_type]=='B'){ //갤러리+슬라이드형일 때는 줄바꿈 처리 해제
														echo "";
													}else{
														if(floor(($i+1)%$mainPRRow[gallery_nums])==0){
															echo '</ul><div style="height:40px;"></div><ul class="product_list">';
														}
													}
												}
											}
										?>
									</ul>
								</div>
							<? }else{?>
								<div style="width:100%;text-align:center;"><?=$title?>에 진열된 상품이 없습니다.</div>
							<? } ?>
						</div>
					</div>
<?
				break;

				case "webzine": // 웹진 타입
					//$pagecount = ceil($limitcount / 3);
					$pagecount = $limitcount;
?>
					<div class="main_prlist_list">
						<div class="wrapper">
							<h1><?=$title?></h1>
							<?
								if(!empty($realprList)){
									for($i=0;$i<$pagecount;$i++){
										//$startnum = $i * 3;
							?>
							<div class="product_c">
								<ul class="product_list" id="<?=$webzinepage?>_main_product_<?=$i?>" <? if($i!=0) {echo "style=\"display:none\"";}?>>
									<?
										//for($j=$startnum;$j <$startnum + 3;$j++){
										for($j=0;$j <$limitcount;$j++){
											if($realprList[$j]=="") {	continue;	}
												$webzineSQL = $prSQL."AND p.productcode = '".$realprList[$j]."' ";

												if(false !== $webzineRes = mysql_query($webzineSQL,get_db_conn())){
													$webzineRow = mysql_fetch_assoc($webzineRes);
													$maximage=$webzineRow['maximage'];
													$productcode=$webzineRow['productcode'];
													$productname = _strCut($webzineRow['productname'],24,4,$charset);
													$productmsg = _strCut($webzineRow['prmsg'],34,4,$charset);
													$sellprice=number_format($webzineRow['sellprice']);
													$prconsumerprice=number_format($webzineRow['consumerprice']);
													$discount=$webzineRow['discountRate'];
													$vendername=$webzineRow['com_name'];
													$venderidx = $webzineRow['vender'];
													$reservation = $webzineRow['reservation'];
													if(strlen($reservation)>0 && $reservation != "0000-00-00"){
														$msgreservation = "<span class=\"font-orange\">(예약)</span>";
														$datareservation = $reservation;
													}else{
														$msgreservation = $datareservation = "";
													}

													$wholeSaleIcon="";
													if($webzineRow['isdiscountprice'] == 1 AND isSeller()){
														$wholeSaleIcon='<img src="/images/common/wholeSaleIcon.gif" /> ';
														$webzineRow['sellprice']=$webzineRow['productdisprice'];
													}

													#####################상품별 회원할인율 적용 시작#######################################
													$discountprices = getProductDiscount($productcode);
													if($discountprices > 0 AND isSeller() != 'Y' ){
														$memberprice = $webzineRow['sellprice'] - $discountprices;
														$webzineRow['sellprice'] = $memberprice;
													}
													#####################상품별 회원할인율 적용 끝 #######################################

													$viewPrice="";
													$dicker=new_dickerview($webzineRow['etctype'],$wholeSaleIcon.number_format($webzineRow['sellprice'])."원",1);
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
														$viewPrice.=$wholeSaleIcon.number_format($webzineRow['sellprice'])."원";
													} else {
														if (strlen($webzineRow['option_price']) == 0) {
															$viewPrice.=$wholeSaleIcon.number_format($webzineRow['sellprice'])."원";
														} else {
															$viewPrice.=ereg_replace("\[PRICE\]",number_format($webzineRow['sellprice']),$_data->proption_price);
														}
													}

													//상품평 수
													$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
													$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
													$row_cnt3=mysql_fetch_object($result_cnt3);
													$t_cnt3 = (int)$row_cnt3->t_count;


													if(strlen($webzineRow[tinyimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$webzineRow[tinyimage])==true){
														$background_url=$Dir.DataDir."shopimages/product/".urlencode($webzineRow[tinyimage]);
													}else{
														$background_url=$Dir."images/no_img.gif";
													}

													$prdetail_link="productdetail_tab01.php?productcode=".$productcode."";


													$youtube_url=$webzineRow['youtube_url'];
													$youtube_prlist=$webzineRow['youtube_prlist'];
													$youtube_prlist_imgtype=$webzineRow['youtube_prlist_imgtype'];
													$youtube_prlist_file=$webzineRow['youtube_prlist_file'];

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
									<li class="product_item">
										<div class="product_view">
											<div class="product_img" style="<?=$prradius?>">
												<?
													if(strlen($youtube_url)>0 && $youtube_prlist=='Y'){ //동영상(유튜브) 이미지는 퀵툴 미출력
														echo "<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;'><A HREF=".$prdetail_link."><img src='/images/movie_icon.png' alt='' /></a></div>";
													}
												?>
												<a href=<?=$prdetail_link?> rel="external" style="display:block;width:100%;height:100%;background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>;background-position:center;font-size:0px;">
													<img src="/images/common/trans.gif" width="100%" alt="상품 이미지" class="pr_pt" />
												</a>
											</div>
											<? if($discount>0){ ?><div class="product_sale"><?=$discount?>%</div><? } ?>
										</div>
										<div class="product_info">
											<div class="product_name"><a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external"><?=$productname?></a></div>
											<? if(strlen($productmsg)>0){ ?><div class="product_caption"><?=$productmsg?></div><? } ?>
											<? if($prconsumerprice>0){ ?><div class="product_discount"><?=$prconsumerprice?>원</div><? } ?>
											<div class="product_price">
												<?=$viewPrice?>
												<? if ($webzineRow['quantity']=="0") echo soldout(); ?>
											</div>
											<? if(strlen($vendername)>0){ ?>
												<div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');"><?=$vendername?></a></div>
											<? } ?>
											<div><span class="product_coment"><?=$t_cnt3?></span></div>
										</div>
									</li>
									<?
												if($j>=$limitcount-1){
													break;
												}
											}
										}
									?>
								</ul>
							</div>
							<?
										mysql_free_result($webzineRes);
									}
								}else{
							?>
							<div style="text-align:center;padding:5px 0px;"><?=$title?>에 진열된 상품이 없습니다.</div>
							<?
								}
								$webzinepage++;
							?>
						</div>
					</div>
<?
				break;

				case "list": //리스트 타입
?>
					<div class="main_prlist_list">
						<div class="wrapper">
							<h1><?=$title?></h1>
							<? if(!empty($realprList)){ ?>
							<div class="product_b">
								<ul class="product_list">
									<?
										for($i=0;$i<$limitcount;$i++){
											$listSQL = $prSQL."AND p.productcode = '".$realprList[$i]."' ";
											if(false !== $listRes = mysql_query($listSQL, get_db_conn())){
												$listRow = mysql_fetch_assoc($listRes);
												$minimage=$listRow['minimage'];
												$wideimage=$listRow['wideimage'];
												$productcode = $listRow['productcode'];

												$productname = _strCut($listRow['productname'],32,4,$charset);
												$prmsg = $listRow['prmsg'];
												$consumerprice = number_format($listRow['consumerprice']);
												$sellprice = number_format($listRow['sellprice']);
												$discountRate = number_format($listRow['discountRate']);
												$vendername = $listRow['com_name'];
												$venderidx = $listRow['vender'];

												$reservation = $listRow['reservation'];
												if(strlen($reservation)>0 && $reservation != "0000-00-00"){
													$msgreservation = "<span class=\"font-orange\">(예약)</span>";
													$datareservation = "(".$reservation.")";
												}else{
													$msgreservation = $datareservation = "";
												}

												$wholeSaleIcon="";
												if($listRow['isdiscountprice'] == 1 AND isSeller()){
													$wholeSaleIcon='<img src="/images/common/wholeSaleIcon.gif" /> ';
													$listRow['sellprice']=$listRow['productdisprice'];
												}

												#####################상품별 회원할인율 적용 시작#######################################
												$discountprices = getProductDiscount($productcode);
												if($discountprices > 0 AND isSeller() != 'Y' ){
													$memberprice = $listRow['sellprice'] - $discountprices;
													$listRow['sellprice'] = $memberprice;
												}
												#####################상품별 회원할인율 적용 끝 #######################################

												$viewPrice="";
												$dicker=new_dickerview($listRow['etctype'],$wholeSaleIcon.number_format($listRow['sellprice'])."원",1);
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
													$viewPrice.=$wholeSaleIcon.number_format($listRow['sellprice'])."원";
												} else {
													if (strlen($listRow['option_price']) == 0) {
														$viewPrice.=$wholeSaleIcon.number_format($listRow['sellprice'])."원";
													} else {
														$viewPrice.=ereg_replace("\[PRICE\]",number_format($listRow['sellprice']),$_data->proption_price);
													}
												}

												//상품평 수
												$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
												$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
												$row_cnt3=mysql_fetch_object($result_cnt3);
												$t_cnt3 = (int)$row_cnt3->t_count;

												$background_url="";
												if(is_file($savewideloc.$wideimage)>0){
													$background_url=$savewideloc.$wideimage;
												}else if(strlen($listRow[minimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$listRow[minimage])==true){
													$background_url=$Dir.DataDir."shopimages/product/".urlencode($listRow[minimage]);
												}else{
													$background_url=$Dir."images/no_img.gif";
												}

												$prdetail_link="productdetail_tab01.php?productcode=".$productcode."";


												$youtube_url=$listRow['youtube_url'];
												$youtube_prlist=$listRow['youtube_prlist'];
												$youtube_prlist_imgtype=$listRow['youtube_prlist_imgtype'];
												$youtube_prlist_file=$listRow['youtube_prlist_file'];

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
									<li class="product_item">
										<div class="product_view">
											<div class="product_img" style="<?=$prradius?>">
												<?
													if(strlen($youtube_url)>0 && $youtube_prlist=='Y'){ //동영상(유튜브) 이미지는 퀵툴 미출력
														echo "<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;'><A HREF=".$prdetail_link."><img src='/images/movie_icon.png' alt='' /></a></div>";
													}
												?>
												<a href=<?=$prdetail_link?> rel="external" style="display:block;width:100%;height:100%;background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>;background-position:center;font-size:0px;" />
													<? if($discountRate>0){ ?><div class="product_sale"><?=$discountRate?>%</div><? } ?>
													<img src="/images/common/trans.gif" width="100%" alt="상품 이미지" class="pr_pt" />
												</a>
											</div>
										</div>
										<div class="product_info">
											<div class="product_name">
												<a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external"><?=$productname?></a>
											</div>
											<div class="product_caption"><?=$prmsg?></div>
											<div class="product_price">
												<?=$viewPrice?>
												<? if ($listRow['quantity']=="0") echo soldout(); ?>
											</div>
											<? if($consumerprice > 0){ ?><div class="product_discount"><?=$consumerprice?>원</div><? } ?>
											<? if(strlen($reservation)>0 && $reservation != "0000-00-00"){ ?>
												<div class="product_reserve"><span><?=$msgreservation?></span> <?=$datareservation?></div>
											<? } ?>
											<? if(strlen($vendername)>0){ ?>
												<div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');"><?=$vendername?></a></div>
											<? } ?>
											<div>

											<!--모바일 메인 아이콘-->
											<?
												$icoi = strpos(" ".$listRow['etctype'],"ICON=");
												if($icoi>0){
													$icon = substr($listRow['etctype'],strpos($listRow['etctype'],"ICON="));
													$icon = substr($icon,5,strpos($icon,"")-5);
													$num=strlen($icon);
													for($j=0;$j<$num;$j+=2){
														$temp=$icon[$j].$icon[$j+1];
														/*
														if($temp=='04'){
															$icon_name="NEW";
														}else if($temp=='13'){
															$icon_name="BEST";
														}else{
															$icon_name="HOT";
														}
														*/
														if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
															echo "<img src=\"http://".$_ShopInfo->shopurl.DataDir."shopimages/etc/icon".$temp.".gif\" align=\"absmiddle\" border=\"0\" />";
														} else if(strlen($temp)>0 && !preg_match("/^(U)[1-6]$/",$temp)) {
															echo "<span class='icon".$temp."'></span>";
														}
													}
												}
											?>
											<!--모바일 아이콘-->

											<? //echo viewproductname('',$listRow['etctype'],'');?>
											</div>
										</div>
										<div class="product_communicate">
											<ul>
												<li>
													<a href="productdetail_tab01.php?productcode=<?=$listRow['productcode']?>&sort=#tapTop"><span class="product_coment"><?=$t_cnt3?></span></a>
												</li>
											</ul>
										</div>
									</li>
									<?
											}
										}
									?>
								</ul>
							</div>
						<? }else{ ?>
							<div style="text-align:center;padding:5px 0px;"><?=$title?>에 진열된 상품이 없습니다.</div>
						<? } ?>
						</div>
					</div>
<?
				break;
			}
			$m++;
		}
	}else{
?>
	<div style="text-align:center;padding:5px 0px;">
		진열설정된 섹션 정보가 없습니다.
	</div>
<?
			}
			mysql_free_result($mainPRRes);
		}
	}
?>
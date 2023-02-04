<?
$src = "";
$src = $primgsrc.$_pdata->minimage;
$size = _getImageRateSize($src,298);

if(strlen($_pdata->minimage) == 0) $src = '/images/no_img.gif';

$strapline = _currentCategoryName($code);
//$productname =_strCut($_pdata->productname,20,5,"EUC-KR");
$productname =$_pdata->productname;
$prmsg=$_pdata->prmsg;
$reservation = $_pdata->reservation;
if(strlen($reservation)>0 &&$reservation != "0000-00-00"){
	$msgreservation = "<span class=\"iconYeyak\">예약상품</span>";
	$datareservation = $reservation;
}else{
	$msgreservation = $datareservation = "";
}
//_pr($categoryNavi);
?>
<script src="js/swiper.min.js"></script>
<style>
	.wrapperCon{display:none;}
	.getmallBottomBanner{display:none;}
	.md_comment .wrapper{line-height:1.5em;}
	.md_comment .wrapper img{max-width:100%;height:auto;}

	/* MD PICK 상품 전체목록 CSS */
	.selectBoxChage{position:relative;background:url('/app/skin/basic/img/icon_arrow_bottom01.png') no-repeat;background-position:96% 50%;}
	.selectBoxChage dt{padding:10px 30px 10px 13px;box-sizing:border-box;width:100%;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;}
	.selectBoxChage dd{position:relative;z-index:1;}
	.selectBoxChage dd ul{display:none;position:absolute;top:0px;left:0px;width:100%;height:200px;padding-bottom:10px;background:#fff;border-bottom:1px solid #ddd;box-shadow:0px 7px 10px rgba(0,0,0,0.05);text-align:left;overflow-y:scroll;}
	.selectBoxChage dd li{padding:10px 13px;box-sizing:border-box;width:100%;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;}
</style>

<div id="detail">
<?
	if($mid){ //MD 정보출력
		$sql="SELECT * FROM tblmdpick WHERE md_id='".$mid."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);

		$imagepath=$Dir.DataDir."shopimages/etc/";
?>

	<!-- MD PICK 상품 전체목록 -->
	<dl class="selectBoxChage">
		<dt><?=$productname?> (MD <?=$row->md_nickname?>)</dt>
		<dd>
			<ul>
				<?
					$mdpsql="SELECT a.md_id, a.productcode, a.mdpick_regdate, b.productname FROM tblmdpick_prinfo AS a LEFT OUTER JOIN tblproduct b ON a.productcode=b.productcode ORDER BY a.mdpick_regdate DESC ";
					$mdpresult=mysql_query($mdpsql,get_db_conn());

					while($mdprow=mysql_fetch_object($mdpresult)){
						$msql="SELECT md_nickname, deal_category FROM tblmdpick WHERE md_id='".$mdprow->md_id."' ";
						$mresult=mysql_query($msql,get_db_conn());
						$mrow=mysql_fetch_object($mresult);

						echo "<li><a href='/app/productdetail_tab01.php?productcode=".$mdprow->productcode."&mid=".$mdprow->md_id."'>".$mdprow->productname." (MD ".$mrow->md_nickname.")</a></li>";
					}
				?>
			</ul>
		</dd>
	</dl>

	<div class="md_infomation" style="padding-bottom:40px;overflow:hidden;">
		<div style="position:relative;height:180px;margin-bottom:40px;background:#aaa url('<?=$imagepath.$row->main_image?>') no-repeat;background-position:center;background-size:cover;color:#fff;font-weight:bold;text-align:center;">
			<!--대표 이미지-->
			<div style="position:absolute;bottom:-25px;left:50%;width:90px;height:90px;line-height:90px;margin-left:-40px;border-radius:50%;background:#fff url('<?=$imagepath.$row->profile_image?>') no-repeat;background-position:center;background-size:cover;color:<?=($row->profile_image?"#fff":"#666")?>;font-weight:bold;text-align:center;"><!--프로필 사진--></div>
		</div>
		<p style="margin-bottom:10px;color:#888;font-size:17px;font-weight:600;text-align:center;"><?=$row->deal_category?> 온라인 MD</p>
		<h4 style="margin:0px;color:#222;font-weight:600;text-align:center;"><?=$row->md_nickname?></h4>
		<p style="display:none;margin-top:15px;text-align:center;"><?=$row->md_greeting?></p>
		<p style="margin-top:25px;text-align:center;"><a href="mdpick_prlist.php?mid=<?=$row->md_id?>" style="display:inline-block;padding:10px 25px;box-sizing:border-box;background:#424242;color:#fff;">MD 추천상품 전체보기</a></p>
	</div><!-- md_infomation -->

	<div class="md_comment" style="padding-bottom:40px;overflow:hidden;">
		<div class="wrapper">
			<?
				$sql="SELECT * FROM tblmdpick_prinfo WHERE productcode='".$productcode."' ";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);

				echo stripslashes($row->mdpick_comment);
			?>
		</div>
	</div><!-- md_comment -->

<? }else{ ?>

	<div class="wrapperCon">
		<div class="swiper-container">
			<div class="swiper-wrapper">
			<?php
			if (($end = count($categoryNavi)) > 0) {
				for ($i=0; $i < $end; $i++) {
					$cate = $categoryNavi[$i]['cate'];
					// 하위 카테고리 없으면 출력 안함
					if (!$cate) continue;

					$curCateCode = subCategoryCode($categoryNavi[$i]['curCode']);

					$cend			= count($cate);
					$curCateName	= "선택";
					$cate_lis		= "";
					if ($categoryNavi[$i]['depth'] != 0) {
						$allCate = getSubAllCate($categoryNavi[$i]['curCode'], $categoryNavi[$i]['depth']);

						if ($categoryNavi[$i]['curCode'] == $allCate) {
							$curCateName = "전체보기";
						}

						$cate_lis = "<li><a href='/app/productlist.php?code=".$allCate."'><span>전체보기</span></a></li>";
					}

					if ($cend > 0) {
						for ($j=0; $j < $cend; $j++) {
							if ($cate[$j]->codeA == $curCateCode['codeA'] && $cate[$j]->codeB == $curCateCode['codeB'] && $cate[$j]->codeC == $curCateCode['codeC'] && $cate[$j]->codeD == $curCateCode['codeD']) {
								$curCateName = $cate[$j]->code_name;
							}

							$slctCateCode = "";
							for ($k=0,$e=4; $k < $e; $k++) {
								$cateCodes = $cate[$j]->{'code'.chr(65+$k)};
								if ($cateCodes != "000") {
									$slctCateCode .= $cateCodes;
								}
							}

							$cate_lis .= '
							<li class="selectCate"><a href="/app/productlist.php?code='.$slctCateCode.'"><span>'.$cate[$j]->code_name.'</span></a></li>';
						}
					}
			?>
				<div class="swiper-slide">
					<a class="fistCateName" id='prd_cate_<?=$i?>' onclick="javascript:toggle('prdCateLayer_<?=$i?>');"><span><?php echo $curCateName?></span></a>
					<ul style="display:none" id="prdCateLayer_<?=$i?>" name="prdCateLayer_<?=$i?>">
						<?php echo $cate_lis ?>
					</ul>
				</div>
			<?php
				}
			}
			?>
			</div>
		</div>

		<script language="javascript">
			<!--
			var swiper = new Swiper('.swiper-container', {
				slidesPerView:2.2,
				spaceBetween:0,
				freeMode: true
			});
			//-->
		</script>
	</div>
	<div class="product_view">
		<section id="banner_wrap" style="height: 100%;">
			<div class="banners" style="height: 100%;">

				<!-- slide-delay 속성으로 인터벌 조정 및 오토플레이 유무 설정가능 -->
				<div class="swiper-primage" style="height: 100%;">
					<div class="swiper-wrapper">
						<?
							$background_url=$src;

							$youtube_url=$_pdata->youtube_url;
							$youtube_prdetail=$_pdata->youtube_prdetail;
							$youtube_prdetail_file=$_pdata->youtube_prdetail_file;
							$youtube_prdetail_imgtype=$_pdata->youtube_prdetail_imgtype;

							//동영상(유튜브) 등록일 때 상품이미지 교체
							if(strlen($youtube_url)>0 && $youtube_prdetail=='Y' && $youtube_prdetail_imgtype=='Y'){
								$youtube_code=str_replace("https://youtu.be/","",$youtube_url);
								$prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
								$background_image=str_replace("https://youtu.be/","",$youtube_url);
								$background_url="https://img.youtube.com/vi/".$background_image."/sddefault.jpg";

							}else if(strlen($youtube_url)>0 && $youtube_prdetail=='Y' && $youtube_prdetail_imgtype=='D'){
								$youtube_code=str_replace("https://youtu.be/","",$youtube_url);
								$prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
								$background_image=$youtube_prdetail_file;
								$background_url=$Dir.DataDir."shopimages/product/".$background_image;
							}

							$width=getimagesize($background_url);
							if($width[1]>$width[0]){ //세로가 가로보다 길 때
								$background_size="100% auto";
							}else{ //가로가 세로보다 길 때
								$background_size="auto 100%";
							}
						?>

						<?
							//상품다중이미지 확인
							$imagepath=$Dir."data/shopimages/product/";

							$mult_sql="SELECT * FROM product_multicontents WHERE pridx='".$_pdata->pridx ."' ";
							$mult_result=mysql_query($mult_sql,get_db_conn());
							$mult_nums=mysql_num_rows($mult_result);
							$width=getimagesize($imagepath.$multi_imgs[$i]);
							if($width[1]>$width[0]){ //세로가 가로보다 길 때
								$background_size="100% auto";
							}else{ //가로가 세로보다 길 때
								$background_size="auto 100%";
							}
							$i=1;
							while($mult_row=mysql_fetch_object($mult_result)){
								$multi_imgs[$i] = $mult_row->cont;
								echo "<div class='swiper-slide' style=\"width:100%;height:auto;min-height:300px;border:none;padding:0px;background:url('".$imagepath.$multi_imgs[$i]."') no-repeat;background-position:center;background-size:".$background_size.";\"></div>\n";
								$i++;
							}
						?>
					</div>
					<div class="swiper-page" style="position:absolute;z-index:99;"></div>
				</div>

				<script>
					//메인 비주얼 기본 슬라이드
					var mySwiper = new Swiper('.swiper-primage', {
						loop: false,
						pagination: {
							el: '.swiper-page',
							clickable: true
						},
						autoplay: {
							delay: 5000,
							disableOnInteraction: false,
						}
					});
				</script>

			</div>
		</section>
	</div>

	<? } ?>

	<div class="wrapper">
		<? if($_data->sns_ok == "Y"){ ?>
			<div class="sns_share">
				<div style="display:none;">SNS로 소문내기</div>
				<?
					//echo $_data->sns_ok.$_pdata->sns_state;
					include_once("./sns.php");
				?>
			</div>
		<? } ?>

		<form name="form1" method="post" action="./basket.php">
		
		<div class="detail_info">
			<h1><?=$msgreservation?> <?=$productname?></h1>
			<p><?=$prmsg?></p>

			<?
			// 160624 회원공개 관련 가격 정보
			$dicker = "";
			if ($memberprice > 0) {
				$dicker = new_dickerview($_pdata->etctype,number_format($memberprice),1);
				$_pdata->sellprice = $memberprice;
			} else {
				$dicker = new_dickerview($_pdata->etctype,number_format($_pdata->sellprice),1);
			}
			?>
				<ul>
				<? if(!$dicker['memOpen'] && $_pdata->consumerprice>0){ ?>
					<li class="detail_discount">
						<h1>기존가격</h1>
						<p><em class="pr_price2"><?=number_format($_pdata->consumerprice)?>원</em></p>
					</li>
				<? } ?>

				<?
					$SellpriceValue=0;

					$prsellprice2 = "";

					if(count($dicker['memOpenData']) > 0) {
						
						$prsellprice ="<li class='detail_price'>\n";
						$prsellprice.="<h1>판매가격</h1>\n";
						$prsellprice.="<p><em class=\"pr_price\" id=\"idx_price\">".$dicker['memOpenData']['value']."</em></p>\n";

						$prsellprice2 ="<li class='detail_price'>\n";
						$prsellprice2.="<h1>판매가격</h1>\n";
						$prsellprice2.="<p><em class=\"pr_price\" id=\"idx_price\">".$dicker['memOpenData']['value']."</em></p>\n";

						$prdollarprice="";
						$priceindex=0;
					} else if(strlen($optcode) == 0 && strlen($_pdata->option_price) > 0) {
						$option_price = $_pdata->option_price;
						$pricetok=explode(",",$option_price);
						$priceindex = count($pricetok);
						for($tmp=0;$tmp<=$priceindex;$tmp++) {
							$pricetokdo[$tmp]=number_format($pricetok[$tmp]/$ardollar[1],2);
							$pricetok[$tmp]=number_format($pricetok[$tmp]);
						}
						$prsellprice ="<li class='detail_price'>\n";
						$prsellprice.="<h1>판매가격</h1>\n";
						$prsellprice.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</em></p>\n";
						$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";


						$prsellprice2 ="<li class='detail_price'>\n";
						$prsellprice2.="<h1>판매가격</h1>\n";
						$prsellprice2.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</em></p>\n";


						$prdollarprice.="<h1>해외화폐</h1>\n";
						$prdollarprice.="<p>".$ardollar[0]." ".number_format($_pdata->sellprice/$ardollar[1],2)." ".$ardollar[2]."</p>\n";
						$prdollarprice.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
						$SellpriceValue=str_replace(",","",$pricetok[0]);
					} else if(strlen($optcode) > 0) {
						$prsellprice ="<li class='detail_price'>\n";
						$prsellprice.="<h1>판매가격</h1>\n";
						$prsellprice.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</em></p>\n";
						$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
						$prsellprice.="</li>\n";

						$prsellprice2 ="<li class='detail_price'>\n";
						$prsellprice2.="<h1>판매가격</h1>\n";
						$prsellprice2.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</em></p>\n";
						$prsellprice2.="</li>\n";

						$prdollarprice.="<h1>해외화폐</h1>\n";
						$prdollarprice.="<p><em class=\"pr_price\">".$ardollar[0]." ".number_format($_pdata->sellprice/$ardollar[1],2)." ".$ardollar[2]."</em></[>\n";
						$prdollarprice.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
						$SellpriceValue=$_pdata->sellprice;
					} else if(strlen($_pdata->option_price) == 0) {
						if($_pdata->assembleuse=="Y") {
							$prsellprice ="<li class='detail_price'>\n";
							$prsellprice.="<h1>판매가격</h1>\n";
							$prsellprice.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."원</em></p>\n";
							$prsellprice.="<input type=hidden name=price value=\"".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."\">\n";
							$prsellprice.="</li>\n";

							$prsellprice2 ="<li class='detail_price'>\n";
							$prsellprice2.="<h1>판매가격</h1>\n";
							$prsellprice2.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."원</em></p>\n";
							$prsellprice2.="</li>\n";

							$prdollarprice.="<h1>해외화폐</h1>\n";
							$prdollarprice.="<p>".$ardollar[0]." ".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)/$ardollar[1],2)." ".$ardollar[2]."</em></p>\n";
							$prdollarprice.="<input type=hidden name=dollarprice value=\"".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)/$ardollar[1],2)."\">\n";
							$SellpriceValue=($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice);
						} else {
							$prsellprice ="<li class='detail_price'>\n";
							$prsellprice.="<h1>판매가격</h1>\n";

							$prsellprice2 ="<li class='detail_price'>\n";
							$prsellprice2.="<h1>판매가격</h1>\n";

							if($mempricestr > 0){
								$prsellprice.="<p><em class=\"pr_price\" id=\"idx_price\">(회원할인)".$mempricestr."원</em></p>\n";
								$prsellprice2.="<p><em class=\"pr_price\" id=\"idx_price\">(회원할인)".$mempricestr."원</em></p>\n";
							}else{
								$prsellprice.="<p align='right'><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</em></p>\n";
								$prsellprice2.="<p align='right'><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</em></p>\n";
							}
							$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
							$prsellprice.="</li>\n";

							$prsellprice2.="</li>\n";

							$prdollarprice.="<h1>해외화폐</h1>\n";
							$prdollarprice.="<p><em class=\"pr_price\">".$ardollar[0]." ".number_format($_pdata->sellprice/$ardollar[1],2)." ".$ardollar[2]."</em></p>\n";
							$prdollarprice.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
							$SellpriceValue=$_pdata->sellprice;
						}
						$priceindex=0;
					}
					

					if(isSeller() == 'Y' AND $_pdata->productdisprice > 0 ){
						$prsellprice ="<li class='detail_price'>\n";
						$prsellprice.="<h1>도매가격</h1>\n";
						$prsellprice.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->productdisprice)."원</em></p>\n";
						$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->productdisprice)."\">\n";
						$prsellprice.="</li>\n";

						$prsellprice2 ="<li class='detail_price'>\n";
						$prsellprice2.="<h1>도매가격</h1>\n";
						$prsellprice2.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->productdisprice)."원</em></p>\n";
						$prsellprice2.="</li>\n";
					}

					if($ao_cnt>0){
						$prsellprice ="<li class='detail_price'>\n";
						$prsellprice.="<h1>판매가격</h1>\n";
						$prsellprice.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format($ao_sellprice)."원</em></p>\n";

						$prsellprice2 ="<li class='detail_price'>\n";
						$prsellprice2.="<h1>판매가격</h1>\n";
						$prsellprice2.="<p><em class=\"pr_price\" id=\"idx_price\">".number_format($ao_sellprice)."원</em></p>\n";
					}
					
					//판매가
					echo $prsellprice2;


					$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $_pdata->sns_state == "Y" && $sell_memid !=""){
						$reserveconv = getReserveConversionSNS($reserveconv,$_pdata->sns_reserve2,$_pdata->sns_reserve2_type,$_pdata->sellprice,"Y");
					}

					$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");
					if($reserveconv>0) {
						$prreserve ="<li class='detail_point'>\n";
						$prreserve.="<h1>적립금</h1>\n";
						$prreserve.="<p id=\"idx_reserve\">".number_format($reserveconv)."원</p>\n";
						$prreserve.="</li>\n";

						echo $prreserve;
					}
					
					$infoSQL = "SELECT COUNT(p.pridx) AS prcount, v.com_name, v.com_owner, v.com_image, v.com_addr, v.deli_able_area ";
					$infoSQL .= "FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ON(p.vender = v.vender) ";
					$infoSQL .= "WHERE v.vender = '".$_pdata->vender."' AND p.display='Y' ";

					if(false !== $infoRes = mysql_query($infoSQL,get_db_conn())){
						$infoNumRows = mysql_num_rows($infoRes);
						if($infoNumRows > 0){
							$prcount = mysql_result($infoRes,0,0);
							$corpname = mysql_result($infoRes,0,1);
							$corprep = mysql_result($infoRes,0,2);
							$corpaddr = mysql_result($infoRes,0,4);
							$deliAbleArea = mysql_result($infoRes,0,5);

						}else{
							echo '<script>alert(\"등록된 입점사가 아닙니다.\");history.go(-1);</script>';exit;
						}
						
						mysql_free_result($infoRes);
					}else{
						echo '<script>alert(\"연결이 지연되었습니다.\n 잠시 후 다시 시도 해 주시기 바랍니다.\");history.go(-1);</script>';exit;
					}

				?>

				<? if(strlen($delipriceTxt)>0){ ?>
					<li class="detail_delivery">
						<h1>배송비</h1>
						<p><?=$delipriceTxt?></p>
					</li>
				<? } ?>
				<li class="detail_delivery">
					<div style="text-align:right;color:#0000ff;width:100%;font-size:0.8em">* 본 상품은 <?=$deliAbleArea?> 배송이 가능합니다</div>
				</li>

				<? if(strlen($_pdata->production)>0){ ?>
				<li class="detail_company">
					<h1>제조사</h1>
					<p><?=$_pdata->production?></p>
				</li>
				<? } ?>

				<? if(strlen($reservation)>0 &&$reservation != "0000-00-00"){ ?>
				<li class="detail_reservation">
					<h1>배송일</h1>
					<p><?=$datareservation?></p>
				</li>
				<? } ?>

				<?
					if(strlen($_pdata->brand)>0) {
						if($_data->ETCTYPE["BRANDPRO"]=="Y"){
							$prbrand = $_pdata->brand;
						}else{
							$prbrand =$_pdata->brand;
						}
				?>
				<li class="detail_brand">
					<h1>브랜드</h1>
					<p><?=$prbrand?></p>
				</li>
				<? } ?>

				<? if(strlen($_pdata->addcode)>0){ ?>
				<li class="detail_caution">
					<h1>특이사항</h1>
					<p><?=$_pdata->addcode?></p>
				</li>
				<? } ?>
			</ul>
		</div>

<?
	if($_pdata->vender){
?>
		<div style="position:relative;margin-top:20px;padding:15px 0px;border-top:1px solid #eee;border-bottom:1px solid #eee;overflow:hidden;">
			<h2 style="float:left;padding-left:40px;color:#666;font-weight:normal;background:url('/app/images/skin/default/write.png') no-repeat;background-position:0% 20%;background-size:30px auto;">
				<?=$corpname?>
				<p style="margin-top:5px;font-size:13px;">등록 상품수 : <?=$prcount?>개</p>
			</h2>
			<a href="/app/venderinfo.php?vidx=<?=$_pdata->vender?>" style="position:absolute;top:32%;right:0px;height:24px;line-height:26px;padding:0px 8px;color:#999;border:1px solid #ccc;border-radius:0px;">바로가기 </a>
		</div>
<? } ?>
	<?
		$times = mktime();
		//시작시간
		$startHour = 10;
		//종료시간
		$lastHour = 20;
		//주문가능 시간차
		$gapHour = 5;
		//카렌더 검색가중치
		$gapDay = 0;
		$todayDate = date("Y-m-d", $times+(3600*$gapHour)); 
		$realHour = date("H", $times);
		$nowHour = $realHour + $gapHour;
		if ($nowHour < $startHour){
			$nowHour = $startHour;
		}
		else if($nowHour  >= $lastHour){
			$nowHour = $startHour;
			$todayDate = date("Y-m-d", $times+(3600*24)); 
			$gapDay = 1;
		}
	?>
		<script>
			$(document).ready(function(){
				$('#div_btn').click(function(){
					$('#div_toggle').removeClass('on');
					$('#div_btn').css('display','none');
				});
				$("#receiveDate").datepicker({
					dateFormat: 'yy-mm-dd',
					prevText: '이전 달',
					nextText: '다음 달',
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNames: ['일','월','화','수','목','금','토'],
					dayNamesShort: ['일','월','화','수','목','금','토'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					showMonthAfterYear: true,
					changeMonth: true,
					changeYear: true,
					minDate: '+<?=$gapDay?>d',
					yearRange: "-100:+0"	
				});
			});
		</script>


		<div class="detail_button" id="div_toggle">
			<div class="option_select">
				<div class="option_button" id="div_btn">
					<div>&rang;</div>
				</div>

				<div class="option_list wrapper">
					<ul>
						<?=$prsellprice?>
						<li class="detail_delivery" style="display:none;" id="pick_addr_group">
							<h1>꽃집주소</h1>
							<p>
								<?=$corpaddr?>
							</p>
						</li>
						<? if(strlen($delipriceTxt)>0){ ?>
						<li class="detail_delivery delipriceTxt" style="display:none;">
							<h1>배송비</h1>
							<p>
								<?=$delipriceTxt?>
							</p>
						</li>
						<? } ?>
						<div id="addr_group">
						</div>
						<?if($ao_cnt==0){?>
							<li class="detail_delivery">
								<h1>배송방식</h1>
								<div class="order_input_group order_wrap">
									<div class="order_receiveType">
										<div class="typeBtn" id="deliveryBtn" onClick="ReceiverShow('<?=$deliAbleArea?>')">배송</div>
										<div class="typeBtn" id="pickupBtn"onclick="receiveTypeSel(this,'1')">픽업</div>
									</div>
								</div>
								<input type="hidden" class="receiveType" name="receiveType" value=""/>
							</li>
							<li class="detail_delivery">
								<div style="text-align:right;color:#0000ff;width:100%;font-size:0.8em">* 본 상품은 <?=$deliAbleArea?> 배송이 가능합니다</div>
							</li>
							<li class="detail_delivery">
								<h1>배송일시</h1>
								<div class="order_input_group order_wrap" style="width:180px">
									<div style="width:110px;float:left;">
										<INPUT type="text" name="receiveDate" id="receiveDate" maxLength="10" class="basic_input receiveDate" placeholder="날짜(예 <?=date($todayDate)?>)" value="<?=date($todayDate)?>" readonly/>
									</div>
									<div style="width:70px;float:left;">
										<select name="receiveTime" class="basic_input receiveTime">
											<?for( $i=$nowHour ; $i<20 ; $i++ ){
												$time_str01 = $i . ':00';
											?>
												<option value="<?=$time_str01?>"  <? if($i == $nowHour){ echo "selected"; } ?>><?=$time_str01?></option>
											<?}?>
										</select>
									</div>
								</div>
							</li>
						<? } ?>
					</ul>

					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<? 
						//옵션 사용여부 2016-10-04 Seul
						$optClass->setOptUse($_pdata->productcode);
						$optClass->setOptType($_pdata->productcode);

						if($optClass->optUse || strlen($_pdata->option1)>0 || strlen($_pdata->option2)>0) { 
					?>
						<caption style="padding:5px 0px;text-align:left">옵션선택</caption>
					<? 
						}

						//옵션 출력
						$proption1="";
						if(strlen($_pdata->option1)>0) {
							$temp = $_pdata->option1;
							$tok = explode(",",$temp);
							$count=count($tok);

							if($optionView == 'Y'){		//관리자 옵션출력 설정 체크하기
								$proption1.="<tr><td>\n";
							}else{
								$proption1.="<tr><td style=\"position:relative\">\n";
							}

							if ($priceindex!=0) {
								//레이어형태 옵션1 출력
								$proption1.="<a style=\"display:block;height:32px;line-height:32px;padding:0px 10px;border:1px solid #dddddd;background:#ffffff url('/app/skin/default/img/icon_arrow_bottom01.png') no-repeat;background-position:96% 50%;text-align:left\" onclick=\"javascript:toggle('prdOptionLayer_1');\" id='layer_opt1_name' data-name='".$tok[0]."'>".$tok[0]." 선택</a>";
								$proption1.="
									<ul id=\"prdOptionLayer_1\" class='selectOption' style=\"display:none;\">";

								if($optionView == 'Y'){		//관리자 옵션출력 설정 체크하기
									$proption1.="<li style=\"background:#f8f8f8\">".$tok[0]." 선택</li>\n";
								}

								for($i=1;$i<$count;$i++) {
									$pricetokTemp = 0;
									if( !empty($option_price) ) {
										$pricetok=explode(",",$option_price);
										if( $pricetok[$i-1] > 0 ){
											$pricetokTemp = ( $pricetok[$i-1]) - $_pdata->sellprice;
											$pricetokTempFlag = ($pricetokTemp>0) ? "+" : "";
										}
									}
									$priceView = ( $pricetokTemp == 0 ) ? "" : " (".$pricetokTempFlag.number_format($pricetokTemp)."원)";

									if(strlen($tok[$i])>0) {
										$proption1.="<li onclick=\"layerOptionSelected('option1',".$i.",document.form1.option2.selectedIndex-1,1)\">".$tok[$i].$priceView;
									}
									if(strlen($_pdata->option2)==0 && $optioncnt[$i-1]=="0") $proption1.=" (품절)";
									$proption1.="</li>\n";
								}
								$proption1.="	</ul>";

								$proption1.="<select name=\"option1\" id=\"option1\" style=\"display:none;\" ";
								if($_data->proption_size>0) $proption1.="style=\"width:".$_data->proption_size."px\" ";
								$proption1.="onchange=\"change_price(1,document.form1.option1.selectedIndex-1,";
								if(strlen($_pdata->option2)>0) $proption1.="document.form1.option2.selectedIndex-1";
								else $proption1.="''";
								$proption1.=")\">\n";
							} else {
								//레이어형태 옵션1 출력
								$proption1.="<a style=\"display:block;height:32px;line-height:32px;padding:0px 10px;border:1px solid #dddddd;background:#ffffff url('/app/skin/default/img/icon_arrow_bottom01.png') no-repeat;background-position:96% 50%;text-align:left\" onclick=\"javascript:toggle('prdOptionLayer_1');\" id='layer_opt1_name' data-name='".$tok[0]."'>".$tok[0]." 선택</a>";
								$proption1.="
									<ul id=\"prdOptionLayer_1\" class='selectOption' style=\"display:none;\">";

								if($optionView == 'Y'){		//관리자 옵션출력 설정 체크하기
									$proption1.="<li style=\"background:#f8f8f8\">".$tok[0]." 선택</li>\n";
								}

								for($i=1;$i<$count;$i++) {
									if(strlen($tok[$i])>0) {
										$proption1.="<li onclick=\"layerOptionSelected('option1',".$i.",document.form1.option2.selectedIndex-1,0)\">".$tok[$i];
									}
									if(strlen($_pdata->option2)==0 && $optioncnt[$i-1]=="0") $proption1.=" (품절)";
									$proption1.="</li>\n";
								}
								$proption1.="	</ul>";

								$proption1.="<select name=\"option1\" id=\"option1\" style=\"display:none;\" ";
								if($_data->proption_size>0) $proption1.="style=\"width : ".$_data->proption_size."px\" ";
								$proption1.="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
								if(strlen($_pdata->option2)>0) $proption1.="document.form1.option2.selectedIndex-1";
								else $proption1.="''";
								$proption1.=")\">\n";

							}

							$optioncnt = explode(",",substr($_pdata->option_quantity,1));
							$proption1.="<option value=\"\">".$tok[0]." 선택</option>\n";
							$proption1.="<option>---------------</option>\n";
							for($i=1;$i<$count;$i++) {
								if(strlen($tok[$i])>0) $proption1.="<option value=\"$i\">$tok[$i]\n";
								if(strlen($_pdata->option2)==0 && $optioncnt[$i-1]=="0") $proption1.=" (품절)";
								$proption1.="</option>\n";
							}
							$proption1.="</select>";
							$proption1.="</td>\n";
							$proption1.="</tr>\n";
						} else {
							$proption1.="<input type='hidden' name='option1' />";
						}

						$proption2="";
						if(strlen($_pdata->option2)>0) {
							$temp = $_pdata->option2;
							$tok = explode(",",$temp);
							$count2=count($tok);

							$proption2.="<tr><td height=\"5\"></td></tr>";

							if($optionView == 'Y'){		//관리자 옵션출력 설정 체크하기
								$proption2.="<tr><td>\n";
							}else{
								$proption2.="<tr><td style=\"position:relative\">\n";
							}


							//레이어형태 옵션2 출력
							$proption2.="<a style=\"display:block;height:32px;line-height:32px;padding:0px 10px;border:1px solid #dddddd;background:#ffffff url('/app/skin/default/img/icon_arrow_bottom01.png') no-repeat;background-position:96% 50%;text-align:left\" onclick=\"javascript:toggle('prdOptionLayer_2');\" id='layer_opt2_name' data-name='".$tok[0]."'>".$tok[0]." 선택</a>";
							$proption2.="
								<ul id=\"prdOptionLayer_2\" class='selectOption' style=\"display:none;\">";

							if($optionView == 'Y'){		//관리자 옵션출력 설정 체크하기
								$proption2.="<li style=\"background:#f8f8f8\">".$tok[0]." 선택</li>\n";
							}

							for($i=1;$i<$count2;$i++) {
								if(strlen($tok[$i])>0) {
									$proption2.="<li onclick=\"layerOptionSelected('option2',".$i.",document.form1.option1.selectedIndex-1,0)\">".$tok[$i]."</li>\n";
								}
							}
							$proption2.="	</ul>\n";

							$proption2.="<select name=\"option2\" id=\"option2\" style=\"display:none;\" ";
							if($_data->proption_size>0) $proption2.="style=\"width:".$_data->proption_size."px\" ";
							$proption2.="onchange=\"change_price(0,";
							if(strlen($_pdata->option1)>0) $proption2.="document.form1.option1.selectedIndex-1";
							else $proption2.="''";
							$proption2.=",document.form1.option2.selectedIndex-1)\">\n";
							$proption2.="<option value=\"\">".$tok[0]." 선택</option>\n";
							$proption2.="<option>---------------</option>\n";
							for($i=1;$i<$count2;$i++) if(strlen($tok[$i])>0) $proption2.="<option value=\"$i\">$tok[$i]</option>\n";
							$proption2.="</select>";
							$proption2.="</td>\n";
							$proption2.="</tr>\n";

						} else {
							$proption2.="<input type='hidden' name='option2' />";
						}

						if(strlen($optcode)>0) {
							$sql = "SELECT * FROM tblproductoption WHERE option_code='".$optcode."' ";
							$result = mysql_query($sql,get_db_conn());
							if($row = mysql_fetch_object($result)) {
								$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
								$opti=0;
								$option_choice = $row->option_choice;
								$exoption_choice = explode("",$option_choice);
								$proption3.="<tr>\n";
								$proption3.="	<td colspan=\"2\" align=\"right\">";
								while(strlen($optionadd[$opti])>0) {
									$opval = str_replace('"','',explode("",$optionadd[$opti]));
									$opcnt = count($opval);
									$optitle = $opval[0].($exoption_choice[$opti]==1 ? "(필수)" : "(선택)");

									$proption3.="[OPT]";

									//레이어형태 그룹옵션 출력
									$proption3.="<div class=\"basic_select\" style=\"position:relative;\">";
									$proption3.="<a style=\"display:block;height:32px;line-height:32px;padding:0px 10px;border:1px solid #dddddd;background:#ffffff url('/app/skin/default/img/icon_arrow_bottom01.png') no-repeat;background-position:96% 50%;text-align:left\" onclick=\"javascript:toggle('prdOptionLayer_".$opti."');\" id='grp_opt_name_".$opti."' data-name='".$optitle."'>".$optitle."</a>";
									$proption3.="
										<ul id=\"prdOptionLayer_".$opti."\" class='selectOption' style=\"display:none;\">";
									for($j=1;$j<$opcnt;$j++) {
										$exop = str_replace('"','',explode(",",$opval[$j]));
										$proption3.="<li onclick=\"GroupOptSelected('".$opval[$j]."',".$opti.")\">";
										if($exop[1]>0) $proption3.=$exop[0]."(+".$exop[1]."원)";
										else if($exop[1]==0) $proption3.=$exop[0];
										else $proption3.=$exop[0]."(".$exop[1]."원)";
										$proption3.="</li>\n";
									}
									$proption3.="	</ul>";
									$proption3.="</div>";

									$proption3.="<select name=\"mulopt\" onchange=\"chopprice('$opti')\" style=\"display:none;\" ";
									if($_data->proption_size>0) $proption3.=" style=\"width:".$_data->proption_size."px\"";
									$proption3.=">";
									$proption3.="<option value=\"0,0\">--- ".$optitle." ---";
									for($j=1;$j<$opcnt;$j++) {
										$exop = str_replace('"','',explode(",",$opval[$j]));
										$proption3.="<option value=\"".$opval[$j]."\">";
										if($exop[1]>0) $proption3.=$exop[0]."(+".$exop[1]."원)";
										else if($exop[1]==0) $proption3.=$exop[0];
										else $proption3.=$exop[0]."(".$exop[1]."원)";
										$proption3.="</option>\n";
									}
									$proption3.="</select><input type='hidden' name=\"opttype\" value=\"0\" /><input type='hidden' name=\"optselect\" value=\"".$exoption_choice[$opti]."\" />[OPTEND]";
									$opti++;
								}
								$proption3.="<input type='hidden' name=\"mulopt\" /><input type='hidden' name=\"opttype\" /><input type='hidden' name=\"optselect\" />";
								$proption3.="	</td>\n";
								$proption3.="</tr>\n";
							}
							mysql_free_result($result);
						}


						for($i=0;$i<$prcnt;$i++) {
							if(substr($arexcel[$i],0,1)=="O") {	//공백

							}else if($arexcel[$i]=="7"){	//옵션
								if(strlen($proption1)>0 || strlen($proption2)>0 || strlen($proption3)>0) {
									if(strlen($proption1)>0) {
										$proption.=$proption1;
									}
									if(strlen($proption2)>0) {
										$proption.=$proption2;
									}
									if(strlen($proption3)>0) {
										$pattern=array("[OPT]","[OPTEND]");
										$replace=array("<tr><td colspan='2' style='padding:5px 0px'>","</td></tr>");
										$proption.=str_replace($pattern,$replace,$proption3);
									}
									echo $arproduct[$arexcel[$i]];
								} else {
									$proption ="<input type='hidden' name='option1' />\n";
									$proption.="<input type='hidden' name='option2' />\n";
								}
							}
						}

						if(isSeller() == 'Y' AND $_pdata->productdisprice > 0 ){
							$_pdata->sellprice = $_pdata->productdisprice;
						}else{
							$_pdata->sellprice = ( $memberprice > 0 ) ? $memberprice : $_pdata->sellprice;
						}

						if($optClass->optUse) {
							if ($dicker['memOpen']==1) {
								$onlyMember = 1;
							} else {
								$onlyMember = 0;
							}

							echo "<tr><td colspan='2' style='padding:5px 0px'>";
							echo $optClass->createOptDetailForm($Dir, 1, $optClass->optType, $optClass->optNormalType, $onlyMember, "productdetail");
							echo "</td></tr>";
						} 
					?>
					</table>

					<div class="detail_amount">
						<ul>
							<li>
					<?if($ao_cnt>0){?>
						<table border="0" width="100%" cellpadding="0" cellspacing="0" class="detail_01">
							<tr>
								<th scope="row">구매수량</th>
								<td align="right">
									<div class="product_amount" style="float:right;">
										<div class="product_value"><input type="number" name="quantity" required min="1" value="<?=$ao_prodnum?>" readonly='readonly' style="width:100%"/></div>
									</div>
								</td>
							</tr>
						</table>
					<?
					}
					else{
							//상품 다중옵션 처리 (2016-01-20) Seul 옵션이 있으면 수량 안나오게
							if(!$optClass->optUse && strlen($_pdata->option1)<=0 && strlen($_pdata->option2)<=0){
						?>
							<table border="0" width="100%" cellpadding="0" cellspacing="0" class="detail_01">
								<tr>
									<th scope="row">구매수량</th>
									<td align="right">
										<div class="product_amount" style="float:right;">
											<div class="product_minus" onClick="javascript:change_quantity('dn')" />-</div>
											<div class="product_value"><input type="number" name="quantity" required min="1" value="1" readonly='readonly' /></div>
											<div class="product_plus" onClick="javascript:change_quantity('up')" />+</div>
										</div>
									</td>
								</tr>
							</table>
						<? }else{ ?>
							<input type="hidden" name="quantity" required value="1" />

							<!-- 상품 다중옵션 처리 (2016-01-20) Seul -->
							<div id="div_opts"><span class="vc">옵션목록 출력</span></div>
							<div class="optionTotalPrice detail_total" style="display:none;"><span id="multitotprice">0</span>원</div>
						<? } 
						}
						?>
							</li>
						</ul>
					</div>

                    <?
                        //네이버 API 함수 호출
                        if (function_exists('Naver_API_Product_detail')) {
                            Naver_API_Product_detail($productcode, $_ShopInfo->getMemid(), '', 'Mobile', $_ShopInfo->shopid);
                            Naver_API_Product_detail_mobile_cssjs(); //css+js 호출(상단에 한번만 호출)
                        }
                        //네이버 API 함수 호출
                    ?>
				</div>
			</div>
			</section>

			<ul>
				<? if($_ShopInfo->getMemid() != ""){ ?>
					<li id="wishChk" onClick="CheckForm('wishlist','<?=$opti?>')" />위시리스트</li>
				<? }else{ ?>
					<li onClick="check_login()" />위시리스트</li>
				<?
					}

					if(strlen($_pdata->quantity)>0 && $_pdata->quantity<=0){
						echo "<li onClick=\"javascript:alert('품절된 상품입니다.');\" />품절</li>";
					}else{
				?>
					<li id="cartChk" onClick="CheckForm('','<?=$opti?>')" />장바구니</li>
					<li id="baroChk" onClick="CheckForm('ordernow','<?=$opti?>')" />바로구매</li>
				<?
					}

					if(strlen($_pdata->quantity)>0 && $_pdata->quantity<=0){
						$sqlbb = "SELECT * FROM tblalarm_sms WHERE alarm_mbid = '".$_ShopInfo->getMemid()."' and alarm_productcode = '".$_pdata->productcode."' and alarm_send = 0 ";
						$resultbb = mysql_query($sqlbb,get_db_conn());
						$bblist_row=mysql_fetch_object($resultbb);
						mysql_free_result($resultbb);
						
						if($bblist_row){
							echo "<li class='reware_btn_on' onclick=\"alarm_productcode_add('".$_pdata->productcode."');\" id='showMask' />재입고 알림</li>";
						}else{
							echo "<li class='reware_btn' onclick=\"alarm_productcode_add('".$_pdata->productcode."');\" id='showMask' />재입고 알림</li>";
						}
					}
				?>
			</ul>
		</div>

        <?
            //네이버 API 함수 호출
            if (function_exists('Naver_API_Product_detail')) {
                Naver_API_Product_detail_btn('mobileDetail', $_ShopInfo->shopid); //네이버페이 버튼 출력
            }
            //네이버 API 함수 호출
        ?>

		<!-- //TAB1-기본정보 -->
		<input type="hidden" name="code" value="<?=$code?>" />
		<input type="hidden" name="productcode" value="<?=$productcode?>" />
		<input type="hidden" name="ordertype" />
		<input type="hidden" name="opts" />
		<input type="hidden" name="arropts" />
		<input type="hidden" name="aoidx" value="<?=$aoidx?>" />
		<input type="hidden" name="aopidx" value="<?=$aopidx?>" />
		<?=($brandcode>0 ? "<input type='hidden' name='brandcode' value=\"".$brandcode."\">\n" : "")?>
		</form>
	</div>
	<!-- //상품 DETAIL -->

	<a name="tapTop"></a>

	<!-- MD PICK 상품 전체목록 -->
	<script>
		$(".selectBoxChage dt").click(function(){
			$(".selectBoxChage dd ul").toggle();
		});

		$(".selectBoxChage dd ul li").click(function() {
			var text = $(this).html();
			$(".selectBoxChage dt").html(text);
			$(".selectBoxChage dd ul").hide();
		});

		$(document).bind('click', function(e) {
			var $clicked=$(e.target);
			if(!$clicked.parents().hasClass("selectBoxChage"))
			$(".selectBoxChage dd ul").hide();
		});
	</script>

	<? /*
	<script type="text/javascript" src="./js/jquery.touchSwipe-1.2.5.js"></script>
	<script type="text/javascript" src="./js/jquery.baramangSwipe-1.0.js"></script>
	<script type="text/javascript" src="./js/banner.js"></script>
	*/ ?>

	<script type="text/javascript">
	/*
		var bannerImages = BaramangSwipe.mainBanner("#banner_list", "#banner_navi",3000);
		bannerImages.load().bannerNavigator();
	*/

		// 카테고리 선택 및 옵션선택 관련
		function toggle(str){
			var selectorArr = str.split("_");
			jQuery("ul[id*="+selectorArr[0]+"]").each(function() {
				if (this.id == str) {
					if (this.style.display == "none") {
						this.style.display = "";
					} else {
						this.style.display = "none";
					}
				} else {
					this.style.display = "none";
				}
			});
		}

		/**
		 * 일반 옵션 혹은, 멀티 옵션
		 */
		function layerOptionSelected(opt,opt_idx,oth_idx,priceChk) {
			var curOpt       = jQuery("select[name='"+opt+"']").val(opt_idx).attr("selected", "selected"),
				selectedText = jQuery("select[name='"+opt+"'] option:selected").text(),
				opt1_idx     = -1,
				opt2_idx     = -1;

			// option2 가 없을 때.
			if (isNaN(oth_idx)) {
				oth_idx = "";
			}

			// option2 일 경우 idx 넘겨주는 순서 변경.
			if (opt == "option2") {
				opt1_idx = oth_idx;
				opt2_idx = opt_idx;

				// 선택된 옵션을 레이어에 표시 및 레이어 숨김
				jQuery("#layer_opt2_name").text(selectedText);
				jQuery("#prdOptionLayer_2").hide();
			} else {
				opt1_idx = opt_idx;
				opt2_idx = oth_idx;

				// 선택된 옵션을 레이어에 표시 및 레이어 숨김
				jQuery("#layer_opt1_name").text(selectedText);
				jQuery("#prdOptionLayer_1").hide();
			}

			if (opt1_idx != -1 && opt2_idx != -1) {
				// 솔루션 기존 함수에 값 넘김
				change_price(priceChk, opt1_idx, opt2_idx);

				// 선택되어졌던 레이어를 원래 상태로 되돌림
				jQuery("ul[id*=prdOptionLayer_]").each(function() {
					if (this.id == "prdOptionLayer_1") {
						jQuery("#layer_opt1_name").text(jQuery("#layer_opt1_name").data("name"));
						jQuery("[name='option1']").val("");
					} else {
						jQuery("#layer_opt2_name").text(jQuery("#layer_opt2_name").data("name"));
						jQuery("[name='option2']").val("");
					}
					this.style.display = "none";
				});
			}
		}

		/**
		 * 그룹 옵션
		 */
		function GroupOptSelected(opt_idx, select_idx) {

			jQuery("select[name='mulopt']").each(function(idx) {
				if (idx == select_idx) {
					jQuery(this).val(opt_idx).attr("selected", "selected");
					return false;
				}
			});
			if (!chopprice(select_idx)) {
				jQuery("#prdOptionLayer_"+select_idx).hide();
				return;
			}

			if (opt_idx != "" && !isNaN(select_idx)) {
				jQuery("#grp_opt_name_"+select_idx).text(opt_idx);
				jQuery("#prdOptionLayer_"+select_idx).hide();
			}
		}

		jQuery(document).ready(function() {
			// 그룹옵션일 경우 처음에 옵션 초기화, 바로구매 했을 때 뒤로 가기시 셀렉트 초기화.
			jQuery("select[name=mulopt]").each(function() {
				this.value = "0,0";
			});
		});


		function solvprice() {
			var totalprice = 0;
			$j('input[name="opt_price[]"]').each(function(index, item) {
				var idx = item.id.replace('opt_price_', '');
				totalprice += parseInt($j(item).val() * $j('#opt_quantity_'+idx).val());
			});
			$j('#multitotprice').html(number_format(totalprice));
		}

		//상단메뉴 스크롤시 숨김처리
		var pscrollTop=$(window).scrollTop();
		var nscrollTop=$(window).scrollTop();

		$(window).on('scroll touchmove', function(){
			nscrollTop=$(window).scrollTop();
			if(nscrollTop > pscrollTop && nscrollTop >= $('#top').height()){
				$('#top').addClass('hidden');
				$('.detail_button').addClass('hidden');
				$('.detail_tab').addClass('hidden');
				clearTimeout($.data(this,"scrollCheck"));
				$.data(this,"scrollCheck",setTimeout(function(){
					//$('#top').addClass('hidden');
					//$('.detail_tab').removeClass('hidden');
					$('.detail_button').removeClass('hidden');
				},500));
			}else{
				$('#top').removeClass('hidden');
				$('.detail_tab').removeClass('hidden');
			}
			setTimeout(function(){
				pscrollTop = nscrollTop;
			},1000);
		});


		$(function(){
			//스크롤 상단 탭메뉴 고정
			var jbOffset = $( '.detail_tab' ).offset();
			$( window ).scroll( function() {
				if ( $( document ).scrollTop() > jbOffset.top ) {
					$( '.detail_tab' ).addClass( 'fix_tab' );
					$('.detail_panel').css('padding-top','60px');
					$('#tab_move_panel').attr('class','tab_move_panel');
				} else {
					$( '.detail_tab' ).removeClass( 'fix_tab' );
					$('.detail_panel').css('padding-top','0px');
					$('#tab_move_panel').attr('class','tab_non_move_panel');
				}
			});
		});
		
		function receiveTypeSel(obj,val){
			var btnObjs = $(obj).parent().find(".typeBtn");
			var inputObj = $("input[name=receiveType]").val(val);
			if(val == "1"){
				$("#pick_addr_group").show();
				$(".delipriceTxt").hide();
				$("#addr_group").html("");
			}else{
				$(".delipriceTxt").show();
				$("#pick_addr_group").hide();
			}
			btnObjs.each(function(obj) {
				$(this).removeClass("select")
			})
			$(obj).addClass("select")
		}

		//주소록 팝업창
		function ReceiverShow(deliAbleArea){
			$("#delivery_popup").show();
			$("#delivery_content").attr("src","/app/mydeliveryModal.php?deliAbleArea="+deliAbleArea);
		}
		function ReceiverClose(){
			$("#delivery_popup").hide();
			$("#delivery_content").attr("src","about:blank");
		}
		
		function addAddr(name,tel1,tel2,post,addr1,addr2){
			var html = "<div class=\"addr_item\">";
			html += "		<div class=\"addrStr_group\">";
			html += "			<input type=\"hidden\" name=\"zip\" value=\"" + post + "\"/>";
			html += "			<input type=\"hidden\" name=\"addr1\" value=\"" + addr1 + "\"  />";
			html += "			<input type=\"hidden\" name=\"addr2\" value=\"" + addr2 + "\"  />";
			html += "			<input type=\"hidden\" name=\"tel\" value=\"" + tel1 + "\"  />";
			html += "			<input type=\"hidden\" name=\"rcvName\" value=\"" + name + "\"  />";
			html += "			<p class=\"addrStr\">";
			html += addr1;
			html += " ";
			html += addr2;
			html += "			</p>";
			html += "			<p class=\"nameTelStr\">";
			html += name + "  (" + tel1 + ")";
			html += "			</p>";
			html += "		</div>";
			html += "		<div style=\"clear:both;\"></div>";
			html += "	</div>";
			$("#addr_group").html(html);
			receiveTypeSel($("#deliveryBtn"),'0');
		}
		function resetAddr(){
			$(".addr_item").remove();
		}
		
	</script>
<div id="delivery_popup" style="display: none; position: fixed; padding: 15% 3%; box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 999; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="ReceiverClose()">
		<div style="position:absolute;top:3%;right:3%;color:#fff;font-size:4em;font-weight:500;">×</div>
	</div>
	<div style="position: relative; width: 100%; height: 100%; background-color: rgb(255, 255, 255); z-index: 0; overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;">
		<iframe frameborder="0" id="delivery_content" src="about:blank" style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
	</div>
</div>
<? include_once("./alarm_sms_basket.php"); ?>
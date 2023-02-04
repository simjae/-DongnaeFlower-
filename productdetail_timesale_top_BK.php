<?PHP
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
	#baroChk{
		background-color: #e61e6e;
		color: #fff;
		line-height:4em;
		text-align: center;
		margin: 50px 0;
	}
	.detail_info {
		margin-top: 20px;
	}
	.detail_info>ul {
		margin-top: 20px;
	}
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

		<form name="form1" method="post" action="./basket.php">
		<input type="hidden" class="receiveType" name="receiveType" value="1"/>
		<input type="hidden" name="quantity" value="1" />
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

		<div id="baroChk" onClick="CheckForm('ordernow','<?=$opti?>')" />구매하기</div>

		<!-- //TAB1-기본정보 -->
		<input type="hidden" name="code" value="<?=$code?>" />
		<input type="hidden" name="productcode" value="<?=$productcode?>" />
		<input type="hidden" name="ordertype" />
		<input type="hidden" name="opts" />
		<input type="hidden" name="arropts" />
		</form>
	</div>
	<!-- //상품 DETAIL -->

<?
	if($_pdata->vender){
		$vidx = $_pdata->vender;
		$pagetype = "include";
		include $skinPATH."venderinfo.php";
	} ;
?>
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

		

	function CheckForm(gbn,temp2) {

		var optMust = true;

		if(gbn!="wishlist") {
				if(document.form1.receiveType.value.length==0 || document.form1.receiveType.value=="") {
					alert("배송방식을 선택하세요.");
					return;
				}
			if(document.form1.quantity.value.length==0 || document.form1.quantity.value==0) {
				alert("주문수량을 입력하세요.");
				document.form1.quantity.focus();
				return;
			}
		}
		if(gbn=="ordernow") {
			document.form1.ordertype.value="ordernow";
		}
		else if(gbn=="ordernow2" || gbn=="ordernow3") {
			document.form1.ordertype.value=gbn;
			document.form1.action = "<?=$Dir.FrontDir?>basket2.php";
		}
		else if(gbn=="ordernow4" || gbn=="present" || gbn=="pester") {
			document.form1.ordertype.value=gbn;
			document.form1.action = "<?=$Dir.FrontDir?>basket3.php";
		} else {
			// 1606022 바로구매 클릭 시 옵션 미선택 경고 뜨고 장바구니로 담았을 때 바로구매로 가는 오류 수정.
			document.form1.ordertype.value="";
		}

		//무제한 옵션 사용 시 체크
		<?
			if($optClass->optUse) {
		?>
			$('input[name="optMustCnt[]"]').each(function(index, item) {
				if($(item).val()<=0) {
					if(document.getElementById("div_btn").style.display!='none' || document.getElementById("div_btn").style.display!='' ){
						alert('필수 옵션을 선택해주세요.');
						optMust = false;
						return false;
					}
					else {
						optMust = false;
						return false;
					}
				}
			});

			if(!optMust) {
				return;
			}
			
			if($("#div_opts:has(div)").length == 0) {
				alert('필수 옵션을 선택해주세요.');
				optMust = false;
				return false;
			}
		<?
			}
		?>

		if(temp2!="") {
			document.form1.opts.value="";
			try {
				for(i=0;i<temp2;i++) {
					if(document.form1.optselect[i].value==1 && document.form1.mulopt[i].selectedIndex==0) {
						alert('필수선택 항목입니다. 옵션을 반드시 선택하세요');
						document.form1.mulopt[i].focus();
						return;
					}
					document.form1.opts.value+=document.form1.mulopt[i].selectedIndex+",";
				}
			} catch (e) {}
		}
	<?
	if(eregi("S",$_cdata->type)) {
	?>
		if(typeof(document.form1.option)!="undefined" && document.form1.option.selectedIndex<2) {
			alert('해당 상품의 옵션을 선택하세요.');
			$('#div_toggle').show(250);
			document.form1.option.focus();
			return;
		}
		if(typeof(document.form1.option)!="undefined" && document.form1.option.selectedIndex>=2) {
			arselOpt=document.form1.option.value.split("_");
			arselOpt[1] = (arselOpt[1] > 0)? arselOpt[1] :1;
			seq = parseInt(10*(arselOpt[1]-1)) + parseInt(arselOpt[0]);
			if(num[seq-1]==0) {
				alert('해당 상품의 옵션은 품절되었습니다. 다른 옵션을 선택하세요');
				document.form1.option.focus();
				return;
			}
			document.form1.option1.value = arselOpt[0];
			document.form1.option2.value = arselOpt[1];
		}
	<?
	}else{
	?>
		if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex<2 && typeof(document.form1.opt_idx_1)=="undefined") {
			alert('해당 상품의 옵션을 선택하세요.');
			$('#div_toggle').show(250);
			document.form1.option1.focus();
			return;
		}
		if(typeof(document.form1.option2)!="undefined" && document.form1.option2.selectedIndex<2 && typeof(document.form1.opt_idx2_1)=="undefined") {
			alert('해당 상품의 옵션을 선택하세요.');
			$('#div_toggle').show(250);
			document.form1.option2.focus();
			return;
		}
		if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex>=2) {
			temp2=document.form1.option1.selectedIndex-1;
			if(typeof(document.form1.option2)=="undefined") temp3=1;
			else temp3=document.form1.option2.selectedIndex-1;
			if(num[(temp3-1)*10+(temp2-1)]==0) {
				alert('해당 상품의 옵션은 품절되었습니다. 다른 옵션을 선택하세요');
				document.form1.option1.focus();
				return;
			}
		}
	<?
	}
	?>
		if(typeof(document.form1.package_type)!="undefined" && typeof(document.form1.packagenum)!="undefined" && document.form1.package_type.value=="Y" && document.form1.packagenum.selectedIndex<2) {
			alert('해당 상품의 패키지를 선택하세요.');
			document.form1.packagenum.focus();
			return;
		}
		if(gbn!="wishlist") {
			<? if($_pdata->assembleuse=="Y") { ?>
			if(typeof(document.form1.assemble_type)=="undefined") {
				alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');
				return;
			} else {
				if(document.form1.assemble_type.value.length>0) {
					arracassembletype = document.form1.assemble_type.value.split("|");
					document.form1.assemble_list.value="";

					for(var i=1; i<=arracassembletype.length; i++) {
						if(arracassembletype[i]=="Y") {
							if(document.getElementById("acassemble"+i).options.length<2) {
								alert('필수 구성상품의 상품이 없어서 구매가 불가능합니다.');
								document.getElementById("acassemble"+i).focus();
								return;
							} else if(document.getElementById("acassemble"+i).value.length==0) {
								alert('필수 구성상품을 선택해 주세요.');
								document.getElementById("acassemble"+i).focus();
								return;
							}
						}

						if(document.getElementById("acassemble"+i)) {
							if(document.getElementById("acassemble"+i).value.length>0) {
								arracassemblelist = document.getElementById("acassemble"+i).value.split("|");
								document.form1.assemble_list.value += "|"+arracassemblelist[0];
							} else {
								document.form1.assemble_list.value += "|";
							}
						}
					}
				} else {
					alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');
					return;
				}
			}
			<? } ?>
			document.form1.submit();
		} else {
			document.form1.action = "confirm_wishlist.php";
			document.form1.submit();
			//document.wishform.opts.value=document.form1.opts.value;
			//if(typeof(document.form1.option1)!="undefined") document.wishform.option1.value=document.form1.option1.value;
			//if(typeof(document.form1.option2)!="undefined") document.wishform.option2.value=document.form1.option2.value;

			//window.open("about:blank","confirmwishlist","width=500,height=250,scrollbars=no");
			//document.wishform.submit();
		}
	}
		
	</script>
<? include_once("./alarm_sms_basket.php"); ?>
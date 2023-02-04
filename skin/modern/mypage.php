<?
	$profile_photo=($row->profile_photo?"background:url('/data/shopimages/member/".$row->profile_photo."') no-repeat;background-position:center;background-size:cover;":"");
?>

<style>
	.mypage_profile{position:relative;margin:0px;padding:20px 15px;color:#fff;overflow:hidden;}
	.mypage_profile .btn_mypage_modify{position:absolute;top:0px;right:0px;padding:5px 10px;border:1px solid #eee;border-radius:50px;color:#fff;}
	.mypage_profile h4{margin:0;padding:0;font-size:17px;font-weight:bold;}
	.mypage_menu{position:relative;margin:15px;padding-right:32px;box-sizing:border-box;overflow:hidden;}
	.mypage_menu li{width:auto;}
	.mypage_menu li a{display:block;padding:7px 5px;}
	.mypage_menu li.selected{border-bottom:3px solid #242424;box-sizing:border-box;}
	.mypage_menu .swiper-button-next, .swiper-container-rtl .swiper-button-prev{right:0px;background:#f2f2f2 url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%2027%2044'%3E%3Cpath%20d%3D'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z'%20fill%3D'%23848484'%2F%3E%3C%2Fsvg%3E") no-repeat;background-position:center;}
	.mypage_menu .swiper-button-next, .swiper-button-prev{top:5px;width:22px;height:22px;margin-top:0px;background-size:6px auto;border:1px solid #ccc;border-radius:3px;box-sizing:border-box;box-shadow:-15px 0px 5px rgba(255,255,255,1);}
	.mypage_meminfo{margin:10px 15px 40px 15px;padding:20px 25px;border:1px solid #eee;}
	.mypage_meminfo h4{margin:0;margin-bottom:5px;padding:0;color:#bbb;font-weight:normal;}
	.mypage_meminfo ul{overflow:hidden;}
	.mypage_meminfo li{float:left;width:45%;color:#242424;font-weight:bold;}
	.mypage_myreserve{position:relative;padding-right:20px;}
	.mypage_myreserve:after{content:'';position:absolute;top:1px;right:0px;width:13px;height:13px;background:url('/m/images/icon_arrow_right02_x2.png') no-repeat;background-size:auto 6px;background-position:65% 50%;border:1px solid #777;border-radius:50%;}
	.mypage_mycoupon{position:relative;padding-right:20px;}
	.mypage_mycoupon:after{content:'';position:absolute;top:1px;right:0px;width:13px;height:13px;background:url('/m/images/icon_arrow_right02_x2.png') no-repeat;background-size:auto 6px;background-position:65% 50%;border:1px solid #777;border-radius:50%;}

	.mypage_orderlist{position:relative;margin-bottom:40px;padding:0px 15px;box-sizing:border-box;overflow:hidden;}
	.mypage_orderlist h4{margin:0;margin-bottom:15px;padding:0;padding-bottom:10px;border-bottom:1px solid #efefef;color:#242424;font-size:1.4em;}
	.mypage_orderlist .btn_more_orderlist{position:absolute;top:5px;right:15px;padding-right:20px;}
	.mypage_orderlist .btn_more_orderlist:after{content:'';position:absolute;top:0px;right:0px;width:13px;height:13px;background:url('/m/images/icon_arrow_right02_x2.png') no-repeat;background-size:auto 6px;background-position:65% 50%;border:1px solid #444;border-radius:50%;}

	.mypage_myreivew{margin-bottom:40px;padding:0px 15px;box-sizing:border-box;overflow:hidden;}
	.mypage_myreivew h4{margin:0;margin-bottom:10px;padding:0;color:#242424;font-size:1.4em;}
	.mypage_myreivew li{border:1px solid #eee;box-sizing:border-box;}

	.mypage_wishlist{position:relative;margin-bottom:40px;padding:0px 15px;box-sizing:border-box;overflow:hidden;}
	.mypage_wishlist h4{margin:0;margin-bottom:10px;padding:0;color:#242424;font-size:1.4em;}
	.mypage_wishlist .btn_more_wishlist{position:absolute;top:5px;right:15px;padding-right:20px;}
	.mypage_wishlist .btn_more_wishlist:after{content:'';position:absolute;top:0px;right:0px;width:13px;height:13px;background:url('/m/images/icon_arrow_right02_x2.png') no-repeat;background-size:auto 6px;background-position:65% 50%;border:1px solid #444;border-radius:50%;}
	.mypage_wishlist li{border:1px solid #eee;box-sizing:border-box;}

	.mypage_viewproduct{margin-bottom:40px;padding:0px 15px;box-sizing:border-box;overflow:hidden;}
	.mypage_viewproduct h4{margin:0;margin-bottom:10px;padding:0;color:#242424;font-size:1.4em;}
	.mypage_viewproduct li{border:1px solid #eee;box-sizing:border-box;}
</style>

<div id="content">
	<div class="mypage_profile" style="<?=$profile_photo?>">
		<div style="position:absolute;top:0px;left:0px;width:100%;height:100%;background:rgba(0,0,0,0.2);z-index:1;"></div>
		<div style="position:relative;z-index:2;">
			<a href="mypage_usermodify.php" class="btn_mypage_modify">회원정보수정</a>
			<h4><?=$_ShopInfo->memname?> 님</h4>
			<?=($row->profile_photo?"":"프로필 이미지를 등록해 보세요.")?>
			<p>현재 <?=($_ShopInfo->memgroup?$_ShopInfo->memgroup:"미지정")?> 회원그룹</p>
		</div>
	</div>

	<div class="mypage_menu">
		<ul class="swiper-wrapper">
			<li class="swiper-slide selected"><a href="mypage.php" rel="external">마이페이지</a></li>
			<li class="swiper-slide"><a href="orderlist.php" rel="external">주문내역</a></li>
			<li class="swiper-slide"><a href="mypage_delivery.php" rel="external">배송지등록</a></li>
			<li class="swiper-slide"><a href="mypage_personal_list.php" rel="external">1:1 문의</a></li>
			<li class="swiper-slide"><a href="board_list.php?board=all" rel="external">내가 쓴 글 모아보기</a></li>
			<li class="swiper-slide"><a href="wishlist.php" rel="external">위시리스트</a></li>
			<li class="swiper-slide"><a href="mypage_reserve.php" rel="external">적립금</a></li>
			<li class="swiper-slide"><a href="mypage_coupon.php" rel="external">쿠폰사용내역</a></li>
			<li class="swiper-slide"><a href="mypage_coupon_down.php" rel="external">쿠폰다운로드</a></li>
			<? if($_data->memberout_type!="N"){ ?>
			<li class="swiper-slide"><a href="mypage_memberout.php" rel="external">회원탈퇴</a></li>
			<? } ?>
		</ul>
		<div class="swiper-button-next"></div>
	</div>

	<div class="mypage_meminfo">
		<ul>
			<li>
				<h4>적립금</h4>
				<span class="mypage_myreserve"><?=number_format($_ShopInfo->memreserve)?>원</span>
			</li>
			<li>
				<h4>쿠폰</h4>
				<?
					$cdate = date("YmdH");
					if($_data->coupon_ok=="Y") {
						$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='".$cdate."' OR date_end='') ";
						$result = mysql_query($sql,get_db_conn());
						$row = mysql_fetch_object($result);
						$coupon_cnt = $row->cnt;
						mysql_free_result($result);
					}else{
						$coupon_cnt=0;
					}
					echo "<span class='mypage_mycoupon'>".$coupon_cnt."장</span>";
				?>
			</li>
		</ul>
	</div>


	<div class="mypage_orderlist">
		<h4>구매내역</h4>
		<a href="orderlist.php" rel="external" class="btn_more_orderlist">더보기</a>

		<!-- 일반상품 주문(최근 주문내역) START -->
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<?
			$delicomlist=getDeliCompany();
			$orderlists=getMyOrderList(5);
			$returnableCnt=0;

			if($orderlists['total'] < 1){
		?>
		<tr>
			<td colspan=3 align=center bgcolor=#FFFFFF>최근 1개월 이내에 구매하신 내역이 없습니다.</td>
		</tr>
		<?
			}else{
				$idx=0;
				foreach($orderlists['orders'] as $row){
					$orderproducts = array();
					$orderproducts = getOrderProduct($row->ordercode);
		?>
		<tr>
			<td class="tdstyle" colspan="2">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding:10px;border:1px solid #efefef;box-sizing:border-box;background:#f8f8f8;">
					<tr>
						<td class="mypage_list_cont2">주문일 : <?=substr($row->ordercode,0,4)?>-<?=substr($row->ordercode,4,2)?>-<?=substr($row->ordercode,6,2)?></td>
						<td align="right"><A HREF="javascript:OrderDetailPop('<?=$row->ordercode?>')" style="display:inline-block;padding:2px 6px;border:1px solid #eee;background:#fff;font-size:0.9em;">주문상세정보</a></td>
					</tr>
					<tr><td class="mypage_list_cont2" colspan="2">결제금액 : <b><font color="#333333"><?=number_format($row->price)?></font></b>원 / 결제방법 : <?=getPaymethodStr($row->paymethod)?></td></tr>
				</table>

				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:30px;padding:5px 10px;border:1px solid #efefef;border-top:none;">
					<?
						$cnt = count($orderproducts);
						for($jj=0;$jj < $cnt;$jj++){
							$row2 = $orderproducts[$jj];
							if($jj>0) echo '<tr><td colspan=4 height=1 style="border-bottom:1px dashed #efefef;"></tr>';

							$optvalue="";
							if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row2->opt1_name)) {
								$optioncode=$row2->opt1_name;
								$row2->opt1_name="";
								$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$row->ordercode."' AND productcode='".$row2->productcode."' AND opt_idx='".$optioncode."' limit 1 ";
								$res=mysql_query($sql,get_db_conn());
								if($res && mysql_num_rows($res)){
									$optvalue= mysql_result($res,0,0);
								}
								mysql_free_result($res);
							}
					?>
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" width="100%" border="0" style="margin:5px 0px;">
								<tr>
									<?
										$reservation = "";
										if( $row2->reservation != "0000-00-00" && $row2->productcode!='99999990GIFT') {
											$reservation = "[예약배송상품(배송예정일:".$row2->reservation.")]<br />";
										}
									?>
									<td width="60" style="line-height:0%;">
										<a href="/m/productdetail_tab01.php?productcode=<?=$row2->productcode?>" rel="external"><img src="<?=(strlen($row2->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row2->tinyimage)==true)?$Dir.DataDir.'shopimages/product/'.urlencode($row2->tinyimage):$Dir."images/no_img.gif"?>" border="0" style="max-width:60px;margin-right:10px" /></a>
									</td>
									<td style="text-align:left;">
										<a href="/m/productdetail_tab01.php?productcode=<?=$row2->productcode?>" rel="external"><?=$reservation?><?=$row2->productname?></a>

										<?
											//옵션 텍스트 가져오기 2016-10-12 Seul
											$ordprd_comtext = $optClass->getOrdprdOptComtext($row2->ordprd_optidx);
											if(strlen($ordprd_comtext)>0) {
										?>
										<p style="margin-top:5px;color:#999999;font-size:11px"><?=" ".$ordprd_comtext?></p>
										<? } ?>

										<? if(strlen($row2->opt1_name)>0 || strlen($row2->opt2_name)>0){ ?>
											<p style="margin-top:5px;color:#999999;font-size:11px"><img src="/images/common/basket/001/basket_skin3_icon002.gif" border="0" align="absmiddle" /> <?=$row2->opt1_name?> <?=(strlen($row2->opt1_name)>0 ? " / " : "")?> <?=$row2->opt2_name?></p>
										<? } ?>

										<? if(!_empty($optvalue)){ echo $optvalue; } ?>
									</td>
									<td width="64" align="center">
										<?
											echo orderProductDeliStatusStr($row2,$row,$cnt);

											if($row2->deli_gbn=="Y" && $row2->status=="" && $row2->review_type =="N"){
												echo "<A HREF=\"javascript:reviewWrite('".$row2->productcode."')\"><span style='display:inline-block;margin-top:4px;padding:1px 6px;background:#666;border:1px solid #666;color:#fff;font-size:11px;'>후기작성</span></a>";
											}else{
												echo "<span style='display:inline-block;margin-top:4px;padding:1px 6px;border:1px solid #eee;border-radius:4px;color:#aaa;font-size:11px;'>후기작성</span>";
											}
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<?	} // end for $jj ?>
				</table>
			</td>
		</tr>
		<?
				$idx++;
				} // end foreach
			} // end if ?>
		</table>
		<!-- 일반상품 주문(최근 주문내역) END -->
	</div>

	<div class="mypage_myreivew">
		<h4>나의 리뷰</h4>
		<ul class="swiper-wrapper">
		<?
			$sql="SELECT * FROM tblproductreview WHERE id='".$_ShopInfo->memid."' ";
			$result=mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)){
				//상품정보 가져오기
				$psql="SELECT * FROM tblproduct WHERE productcode='".$row->productcode."' ";
				$presult=mysql_query($psql,get_db_conn());
				$prow=mysql_fetch_object($presult);
				$pnum=mysql_num_rows($result);

				if($prow->productcode){
					echo "<li class='swiper-slide'>";
					echo "<div style='font-size:0px;line-height:0%;'>";
					echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$prow->productcode."\">";
					echo "	<div style=\"background:url('/data/shopimages/product/".$prow->tinyimage."') no-repeat;background-size:cover;background-position:center;\"><img src='/images/common/trans.gif' width='100%' height='100%' alt='' /></div>";
					echo "</a></div>";
					echo "<p class='product_name' style='width:100%;padding:10px;box-sizing:border-box;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;'>".$prow->productname."</p>";
					echo "<div style='min-height:48px;padding:10px;padding-top:0px;box-sizing:border-box;'>";
					echo "	<p style='width:100%;box-sizing:border-box;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;'>".$row->content."</p>";
					echo "	<p>".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."</p>";
					echo "</div>";
					echo "</li>";
				}

				if($pnum == 0){
					echo "<li>작성된 나의 리뷰가 없습니다.</li>";
				}
			}
		?>
		</ul>
	</div>

	<div class="mypage_wishlist">
		<h4>좋아하는(찜) 상품</h4>
		<a href="wishlist.php" rel="external" class="btn_more_wishlist">더보기</a>
		<ul class="swiper-wrapper">
			<?
				$sql = "SELECT b.productcode, b.productname, b.sellprice, b.consumerprice, b.quantity, b.reserve, b.reservetype, b.tinyimage, b.discountRate, b.vender, ";
				$sql.= "b.option_price, b.option_quantity, b.selfcode, b.etctype FROM tblwishlist a, tblproduct b ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
				$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
				$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
				$sql.= "AND b.display='Y' LIMIT 8 ";
				$result=mysql_query($sql,get_db_conn());
				$cnt=0;

				while($row=mysql_fetch_object($result)) {
					// 할인율 표시
					$discountRate = ( $row->discountRate > 0 ) ? $row->discountRate : "";

					$memberpriceValue = $row->sellprice;
					$strikeStart = $strikeEnd = $memberprice = '';
					if($row->discountprices>0){
						$memberprice = number_format($row->sellprice - $row->discountprices);
						$strikeStart = "<strike>";
						$strikeEnd = "</strike>";
						$memberpriceValue = ($row->sellprice - $row->discountprices);
					}

					echo "<li class='swiper-slide'>\n";
					echo "<div style='font-size:0px;line-height:0%;'>";
					echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\">";
					if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
						echo "<div style=\"background:url('".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."') no-repeat;background-position:center;background-size:cover;\"><img src='/images/common/trans.gif' width='100%' height='100%' alt= ''/></div>";
					} else {
						echo "<div style=\"background:url('".$Dir."images/no_img.gif') no-repeat;background-size:cover;background-position:center;\"><img src='/images/common/trans.gif' width='100%' height='100%' alt= ''/></div>";
					}
					echo "'</A></div>";
					echo "<p class=\"product_name\" style='width:100%;padding:10px;box-sizing:border-box;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;'><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\">".viewproductname($row->productname,'',$row->selfcode)."</A></p>\n";

					//시중가 + 판매가 + 할인율 + 회원할인가
					echo "<div style='min-height:48px;padding:10px;padding-top:0px;box-sizing:border-box;'>";
					if($row->consumerprice!=0) {
						echo "<p class=\"product_discount\">".number_format($row->consumerprice)."원</p>\n";
					}

					echo "<p class=\"product_price\">";
					if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
						echo $strikeStart.$dicker.$strikeEnd;
					} else if(strlen($_data->proption_price)==0) {
						echo number_format($row->sellprice)."원";
						if (strlen($row->option_price)!=0) echo "<FONT color=\"#FF0000\">(옵션변동)</FONT>";
					} else {
						if (strlen($row->option_price)==0){
							echo number_format($row->sellprice)."원";
						}else{
							echo ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
						}
					}
					echo "</p>";
					echo "</div>";

					if($row->discountRate > 0){
						//echo "<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."%↓</td>";
					}

					//회원할인가 적용
					if( $memberprice > 0 ) {
						echo "<div><span class=\"prprice\">".dickerview($row->etctype,$memberprice)."원</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
					}

					if ($row->quantity=="0") echo soldout(1);

					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					if($reserveconv>0) {
						//echo "<p style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" align=\"absmiddle\" alt=\"\" /> <span class=\"mainreserve\">".number_format($reserveconv)."원</span></p>\n";
					}
					echo "</li>";

					$cnt++;
				}
				mysql_free_result($result);
				if ($cnt==0) {
					echo "<li>WishList에 담긴 상품이 없습니다.</li>";
				}
			?>
		</ul>
	</div>

	<div class="mypage_viewproduct">
		<h4>최근 본 상품</h4>
		<?
			$_prdt_list=substr($_COOKIE[ViewProduct],1,-1); //(,상품코드1,상품코드2,상품코드3,) 형식으로
			$prdt_list=explode(",",$_prdt_list);
			$prdt_no=count($prdt_list);
			if(strlen($prdt_no)==0) {
				$prdt_no=0;
			}

			$tmp_product="";
			for($i=0;$i<$prdt_no;$i++){
				$tmp_product.="'".$prdt_list[$i]."',";
			}

			unset($productall);
			$tmp_product=substr($tmp_product,0,-1);
			$sql = "SELECT productcode, productname, tinyimage, consumerprice, sellprice FROM tblproduct ";
			$sql.= "WHERE productcode IN (".$tmp_product.") ";
			$sql.= "ORDER BY FIELD(productcode,".$tmp_product.") ";
			$result=mysql_query($sql,get_db_conn());
			$jj=0;
			while($row=mysql_fetch_object($result)){
				$productall[$jj]["code"]=$row->productcode;
				$productall[$jj]["name"]=$row->productname;
				$productall[$jj]["image"]=$row->tinyimage;
				$productall[$jj]["consumerprice"]=$row->consumerprice;
				$productall[$jj]["sellprice"]=$row->sellprice;
				$jj++;
			}
			mysql_free_result($result);

			for($i=0;$i<count($productall);$i++) {
				$strlist1="<div style='font-size:0px;line-height:0%;'>";
				$strlist1.="<a href=\"".$Dir.FrontDir."productdetail.php?productcode=".$productall[$i]["code"]."\">";
				if (strlen($productall[$i]["image"])>0 && file_exists($Dir.DataDir."shopimages/product/".$productall[$i]["image"])) {
					$strlist1.="<div style=\"background:url('".$Dir.DataDir."shopimages/product/".$productall[$i]["image"]."') no-repeat;background-size:cover;background-position:center;\"><img src='/images/common/trans.gif' width='100%' height='100%' alt= ''/></div>";
				} else {
					$strlist1.="<div style=\"background:url('".$Dir."images/common/noimage.gif') no-repeat;background-size:cover;background-position:center;\"><img src='/images/common/trans.gif' width='100%' height='100%' alt= ''/></div>";
				}
				$strlist1.="</a></div>";
				$strlist1.="<p class='product_name' style='width:100%;padding:10px;box-sizing:border-box;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;'><a href=\"".$Dir.FrontDir."productdetail.php?productcode=".$productall[$i]["code"]."\">".$productall[$i]["name"]."</a></p>";
				$strlist1.="<div style='min-height:48px;padding:10px;padding-top:0px;box-sizing:border-box;'>\n";
				if($productall[$i]["consumerprice"]>0){
					$strlist1.="<p class='product_discount'>".number_format($productall[$i]["consumerprice"])."원</p>";
				}
				$strlist1.="<p class='product_price'>".number_format($productall[$i]["sellprice"])."원</p>";
				$strlist1.="</div>\n";

				if($prdt_no>$i){
					$strlist_content[$i]=$strlist1;
				}
			}

			$prdt_body.="<ul class=\"swiper-wrapper\">";
			for($j=0; $j<$prdt_no; $j++) {
				$prdt_body.="<li class='swiper-slide'>".$strlist_content[$j]."</li>\n";
			}
			$prdt_body.="</ul>\n";
			echo $prdt_body;
		?>
	</div>



	<? /*
	<div class="h_area2">
		<h2>마이페이지</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<!-- 카테고리 list -->
	<div class="category_list">
		<ul class="list_type04">
			<li><a href="orderlist.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon01.png"></span>주문내역</a></li>
			<li><a href="mypage_delivery.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon09.png"></span>배송지 등록</a></li>
			<li><a href="mypage_personal_list.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon02.png"></span>1:1 문의</a></li>
			<li><a href="board_list.php?board=all" rel="external"><span><img src="/m/images/skin/default/mypage_icon03.png"></span>내가 쓴 글 모아보기</a></li>
			<li><a href="basket.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon04.png"></span>장바구니</a></li>
			<li><a href="wishlist.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon05.png"></span>위시리스트</a></li>
			<li><a href="mypage_reserve.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon06.png"></span>적립금</a></li>
			<li><a href="mypage_coupon.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon07.png"></span>쿠폰사용내역</a></li>
			<li><a href="mypage_coupon_down.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon08.png"></span>쿠폰다운로드</a></li>
			<? if($_data->memberout_type!="N"){ ?>
			<li><a href="mypage_memberout.php" rel="external"><span><img src="/m/images/skin/default/mypage_icon10.png"></span>회원탈퇴</a></li>
			<? } ?>
		</ul>
	</div>
	<!-- //카테고리 list -->
	*/ ?>

</div>

<script>
	var swiper = new Swiper('.mypage_menu', {
		slidesPerView: 'auto',
		spaceBetween: 10,
		//freeMode: true
		navigation: {
			nextEl: '.swiper-button-next'
		}
	});

	//나의 리뷰
	var swiper = new Swiper('.mypage_myreivew', {
		slidesPerView: 2,
		spaceBetween: 10
	});

	//위시리스트
	var swiper = new Swiper('.mypage_wishlist', {
		slidesPerView: 2,
		spaceBetween: 10
	});

	//최근 본 상품
	var swiper = new Swiper('.mypage_viewproduct', {
		slidesPerView: 2,
		spaceBetween: 10
	});
</script>
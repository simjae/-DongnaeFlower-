<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	//include_once($Dir."lib/cache_product.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."lib/ext/product_func.php");
	include_once($Dir."lib/ext/member_func.php");
	include_once($Dir."lib/class/option.php");
	$optClass = new Option;
	
	$rcode=$_REQUEST["code"];

	$code = '';
	$likecode='';
	for($i=0;$i<4;$i++){
		$tcode = substr($rcode,$i*3,3);
		if(strlen($tcode) != 3 || $tcode == '000'){
			$tcode = '000';
		}else{
			$likecode.=$tcode;
		}
		${'code'.chr(65+$i)} = $tcode;
		$code.=$tcode;
	}

// search by alice [START]
	$search_bridx = $_REQUEST["search_bridx"];
	$search_price_s = $_REQUEST["search_price_s"];
	$search_price_e = $_REQUEST["search_price_e"];
	$search_color_idx = $_REQUEST["search_color_idx"];
	$searchkey = $_REQUEST["searchkey"];

	$_cdata="";
	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
	$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
	    $_cdata=$row;
	}
	mysql_free_result($result);
	
	$sort=$_REQUEST["sort"];
	$listnum=(int)$_REQUEST["listnum"];

	if($listnum<=0) $listnum=$_data->prlist_num;

	//리스트 세팅
	$setup[page_num] = 10;
	$setup[list_num] = $listnum;

	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];

	if ($block != "") {
		$nowblock = $block;
		$curpage = $block * $setup[page_num] + $gotopage;
	} else {
		$nowblock = 0;
	}

	if (($gotopage == "") || ($gotopage == 0)) {
		$gotopage = 1;
	}

	$sql = "SELECT codeA, codeB, codeC, codeD FROM tblproductcode ";
	if(strlen($_ShopInfo->getMemid())==0) {
		$sql.= "WHERE group_code!='' ";
	} else {
		//$sql.= "WHERE group_code!='".$_ShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
		$sql.= "WHERE group_code NOT LIKE '%".$_ShopInfo->getMemgroup()."%' AND group_code!='' ";
	}
	$result=mysql_query($sql,get_db_conn());
	$not_qry="";
	while($row=mysql_fetch_object($result)) {
		$tmpcode=$row->codeA;
		if($row->codeB!="000") $tmpcode.=$row->codeB;
		if($row->codeC!="000") $tmpcode.=$row->codeC;
		if($row->codeD!="000") $tmpcode.=$row->codeD;
		$not_qry.= "AND a.productcode NOT LIKE '".$tmpcode."%' ";
	}
	mysql_free_result($result);

	$qry = "WHERE 1=1 ";
	if(eregi("T",$_cdata->type)) {	//가상분류
		$sql = "SELECT productcode FROM tblproducttheme WHERE code LIKE '".$likecode."%' ";
		if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
			$sql.= "ORDER BY date DESC ";
		} else if($_cdata->sort=="date3") {
			//역순일 때 2016-08-26 Seul
			$sql.= "ORDER BY date ASC ";
		}
		$result=mysql_query($sql,get_db_conn());
		$t_prcode="";
		while($row=mysql_fetch_object($result)) {
			$t_prcode.=$row->productcode.",";
			$i++;
		}
		mysql_free_result($result);

		//추가 카테고리가 있는지 체크
		$sql = "SELECT productcode FROM tblcategorycode WHERE categorycode LIKE '".$likecode."%' ";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$t_prcode.=$row->productcode.",";
			$i++;
		}
		mysql_free_result($result);
		//# 추가 카테고리가 있는지 체크

		$t_prcode=substr($t_prcode,0,-1);
		$t_prcode=ereg_replace(',','\',\'',$t_prcode);
		$qry.= "AND a.productcode IN ('".$t_prcode."') ";

		$add_query="&code=".$code;
	} else {	//일반분류
		//$qry.= "AND a.productcode LIKE '".$likecode."%' ";

		//추가 카테고리가 있는지 체크
		/*
		$sql = "SELECT productcode FROM tblcategorycode WHERE categorycode LIKE '".$likecode."%' ";

		$result=mysql_query($sql,get_db_conn());
		$prcode="";
		while($row=mysql_fetch_object($result)) {
			$prcode.=$row->productcode.",";
			$i++;
		}
		mysql_free_result($result);
		$prcode=substr($prcode,0,-1);
		$prcode=ereg_replace(',','\',\'',$prcode);
		$qry.= "AND a.productcode IN ('".$prcode."') ";
		$add_query="&code=".$code;*/
		$qry.= "AND cc.categorycode LIKE '".$likecode."%' ";
	//	echo $qry;
		$add_query="&code=".$code;
	}
	$qry.="AND a.display='Y' ";
	//echo $qry;
	

	    //search by alice [START]
	    $search_sql = '';
	    //brand
	    if($search_bridx) {
	        $_bridx = str_replace(':','',$search_bridx);
	        $_bridx = str_replace('|',',',$_bridx);
	        if($search_bridx) { $search_brand_add = ','; }
	        else { $search_brand_add = ''; }
	        $search_brand = $search_brand_add.$_bridx;
	        
	        $search_sql.= "AND (a.brand IN (".$search_brand.")) ";
			
			$add_query.="&search_bridx=".$search_bridx;
	    }
	    //price
	    if($search_price_s || $search_price_e) {
	        if($search_price_s && $search_price_e) {
	            $search_sql.= "AND a.sellprice BETWEEN ".$search_price_s." AND ".$search_price_e." ";
	        }
	        else if($search_price_s && !$search_price_e) {
	            $search_sql.= "AND a.sellprice >= ".$search_price_s." ";
	        }
	        else if(!$search_price_s && $search_price_e) {
	            $search_sql.= "AND a.sellprice <= ".$search_price_e." ";
	        }
			$add_query.="&search_price_s=".$search_price_s."&search_price_e=".$search_price_e;
	    }
	    //color
	    if($search_color_idx) {
	        $arr_color_idx = explode('|',$search_color_idx);
	        $q = "";
	        for($i=0; $i<sizeof($arr_color_idx); $i++) {
	            if($i > 0) { $q = $q." OR "; }
	            $q = $q."(a.color_idx LIKE '%".$arr_color_idx[$i]."%')";
	        }
	        
	        $search_sql.= "AND (".$q.") ";
			$add_query.="&search_color_idx=".$search_color_idx;
	    }
		if($searchkey){
			$search_sql.= "AND a.productname like '%{$searchkey}%' ";
			$add_query.="&searchkey=".$searchkey;
		}
	    //search by alice [ END ]
	    
	    $sql = "SELECT COUNT(DISTINCT a.productcode) as t_count FROM tblproduct AS a left join tblcategorycode as cc on cc.productcode = a.productcode ";
	    $sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	    $sql.= $qry." ";
	    $sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	    if(strlen($not_qry)>0) {
	        $sql.= $not_qry." ";
	    }
	    $sql.= $search_sql." "; //search by alice
	    $result=mysql_query($sql,get_db_conn());
	    $row=mysql_fetch_object($result);
	    $t_count = (int)$row->t_count;
	    mysql_free_result($result);
	    $pagecount = (($t_count - 1) / $setup[list_num]) + 1;
					if($t_count == 0){
						echo "<tr><td align=\"center\" style='padding:30px;' colspan='7'>등록된 상품이 없습니다.</td></tr>";
					}

					$tmp_sort=explode("_",$sort);
					if($tmp_sort[0]=="reserve") {
						$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
					}
					$sql = "SELECT distinct a.productcode,a.productname,a.sellprice,a.quantity,a.consumerprice,a.reserve,a.reservetype,a.production,a.production,a.pridx, ";
					if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
					$sql.= "a.tag, a.tinyimage, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode,a.prmsg, a.discountRate, a.vender, a.sellcount, a.reservation ";
					$sql.= $addsortsql;
					$sql.= "FROM tblproduct AS a  left join tblcategorycode as cc on cc.productcode = a.productcode ";
					$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
					$sql.= $qry." ";
					$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
					if(strlen($not_qry)>0) {
						$sql.= $not_qry." ";
					}
					$sql.= $search_sql." "; //search by alice

					if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
					else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
					else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
					else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
					else if($tmp_sort[0]=="sellcount") $sql.= "ORDER BY sellcount ".$tmp_sort[1]." ";
					else {
						if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
							if(eregi("T",$_cdata->type) && strlen($t_prcode)>0) {
								$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
							} else {
								$sql.= "ORDER BY date DESC ";
							}
						} else if($_cdata->sort=="date3") {
							$sql.= "ORDER BY date ASC ";
						} else if($_cdata->sort=="productname") {
							$sql.= "ORDER BY a.productname ";
						} else if($_cdata->sort=="production") {
							$sql.= "ORDER BY a.production ";
						} else if($_cdata->sort=="price") {
							$sql.= "ORDER BY a.sellprice ";
						}
					}
					$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
					$result=mysql_query($sql,get_db_conn());

					$i=0;
					$optcnt = 0;
					$noptcnt = 0;
					while($row=mysql_fetch_object($result)) {

						// 예약상품 아이콘 추가
						$row->etctype = reservationEtcType($row->reservation,$row->etctype);

						// 도매 가격 적용 상품 아이콘
						$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

						// 할인율 표시
						$discountRate = ( $row->discountRate > 0 ) ? $row->discountRate : "";

						$memberpriceValue = $row->sellprice;
						$strikeStart = $strikeEnd = '';
						$memberprice = 0;
						if($row->discountprices>0 AND isSeller() != 'Y' ){
							$memberprice = number_format($row->sellprice - $row->discountprices);
							$strikeStart = "<strike>";
							$strikeEnd = "</strike>";
							$memberpriceValue = ($row->sellprice - $row->discountprices);
						}

						$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
						$_data->primg_minisize = 100;
						if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
						    $itemImg = "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
						    $width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
						    if($_data->ETCTYPE["IMGSERO"]=="Y") {
						        if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $itemImg .= "height=\"".$_data->primg_minisize2."\" ";
						        else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $itemImg .=  "width=\"".$_data->primg_minisize."\" ";
						    } else {
						        if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $itemImg .=  "width=\"".$_data->primg_minisize."\" ";
						        else if ($width[1]>=$_data->primg_minisize) $itemImg .=  "height=\"".$_data->primg_minisize."\" ";
						    }
						} else {
						    $itemImg = "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
						}
						$prPriceClass = (($memberprice > 0) ? "memprprice" : "prprice");
						$sellPrice = $strikeStart;
						if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)){
						    $sellPrice .=  $dicker;
						}else if(strlen($_data->proption_price)==0){
						    $sellPrice .= $wholeSaleIcon.number_format($row->sellprice)."원";
						    //if(strlen($row->option_price)!=0) echo "(기본가)";
						}else{
						    if(strlen($row->option_price)==0){
						        $sellPrice .= $wholeSaleIcon.number_format($row->sellprice)."원";
						    }else{
						        $sellPrice .= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
						    }
						}
						$sellPrice .= $strikeEnd;
						
						if($memberprice>0){
						   // $sellPrice .=  "<p class=\"prprice\"><img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" />".dickerview($row->etctype,$memberprice."원")."</p>\n";
						}
						$optClass->setOptUse($row->productcode);
						if($optClass->optUse) {
							$Ooptcnt++;
						    $optcnt = 0;
						    //옵션일 경우 전체 품절 확인
						    $optinfo = mysql_fetch_object(mysql_query("select sum(opt_quantity) as cnt from tblopt_combi where productcode='{$row->productcode}'"));
						    $optcnt = $optinfo->cnt;
						    
						}else $Onoptcnt++;
						?>
						<?if(!$optClass->optUse) {?>
						<input type="hidden" name='itemcode[]' value="<?=$row->productcode?>">
						<input type="hidden" name='itembuycnt[]' value="<?=is_null($row->quantity)?99999999:$row->quantity;?>">
						<input type="hidden" name='itemprice[]' value="<?=$row->sellprice?>">
						<?}else{?>
						<input type="hidden" name='optitemcode[]' value="<?=$row->productcode?>">
						<?php }?>
						<li>
							<input type="hidden" id="itemprice_<?=$row->productcode?>" value="<?=$row->sellprice?>"><input type="checkbox" id='check_item_<?php echo $row->productcode?>' value="<?=$row->pridx?>" onclick="checktoggle('<?php echo $row->productcode?>', '<?php if($optClass->optUse) { echo "1"; }else{ echo "2";}?>')" style="width:22px;height:22px;cursor:pointer;display:none">
							<table cellpadding="0" cellspacing="0" width="100%" class="pr_type_list_table">
								<tr>
								<td class="typelist_image_wrap" style="background:#ffffff url('<?=((strlen($row->tinyimage)>0) ? "/data/shopimages/product/".$row->tinyimage : "/images/no_img.gif")?>') no-repeat;background-size:120px auto;background-position:top" onclick="location.href='productdetail_tab01.php?productcode=<?=$row->productcode?><?=$add_query?>&sort=<?=$sort?>'"></td>
									<td class="typelist_text_wrap">
										<div class="pr_txt">
											<p class="p_productname"><?php echo viewproductname($row->productname,$row->etctype,$row->selfcode)?><?php echo (strlen($row->prmsg) ? "<p class=\"prmsg\">".$row->prmsg."</p>" : "")?></p>
											<p class="p_option">
												<?
												if($optClass->optUse) {
													if (strlen($dicker) > 0) {
														$onlyMember = 1;
													} else {
														$onlyMember = 0;
													}
													$optClass->setOptType($row->productcode);
													$optClass->createOptDetailForm($Dir, 0, 2, $optClass->optNormalType, $onlyMember, "optlist_{$row->pridx}");
												}
												?>
											</p>
											<p class="p_orderAmount"  style="margin:4px 0px;">
												<input type="number" maxlength="4" style="WIDTH:60px;HEIGHT:32px;line-height:32px;border:none;BORDER:#aaaaaa 1px solid;box-sizing:border-box;text-align:center;<?if($optClass->optUse) {?>background-color:#EFEFEF<?}?>" id="quantity_<?php echo $row->productcode?>" <?if($optClass->optUse) {?>disabled<?}else{?> onkeyup="pricecheck()" onclick="pricecheck()" name='quantity[]'<?}?>>
											</p>
											<p class="p_productAmount" style="margin:4px 0px;">
												재고 : <?if($optClass->optUse) {?>옵션상품<?}else if(is_null($row->quantity)){echo "무제한";}else{ echo number_format($row->quantity)."개"; }?>
											</p>
											<p class="p_prmsg"><?=$prmsg?></p>
											<p class="p_reviewCnt">
												<?if($optClass->optUse) {?>
													<?if($optcnt > 0){ echo "판매중";}else{ echo "품절";}?>
												<?}else{?>
													<?if($row->quantity==0 && !is_null($row->quantity)){ echo soldout();}else{ echo "판매중";}?>
												<?}?>
											</p>
											<div style="overflow:hidden">
												<span class="p_sellprice"<?if(!$optClass->optUse) {?> id='payprice[]'<?}?>>
													<?php echo number_format($row->sellprice)?>원
												</span>
											</div>

											<? if(strlen($listRow['com_name'])){ ?>
												<p><a href="javascript:venderInfo('<?=$listRow['vender']?>');" rel="external"><?=$listRow['com_name']?></a></p>
											<? } ?>
											<div>
												<table border="0" cellpadding="0" cellspacing="0" width="100%">
													<tbody id="optlist_<?=$row->productcode?>">
														
													</tbody>
												</table>
												
											</div>

											<div class="searchBtn" style="background:#c4c5c9;color:#fff;font-weight:700;padding:8px 10px;text-align:center;margin:7px 0px;">
												<a href="javascript:<?if($optClass->optUse) {?>OoBasket(<?=$Ooptcnt-1?>, '<?=$row->productcode?>');<?}else{?>NoBasket(<?=$Onoptcnt-1?>, '<?=$row->productcode?>');<?}?>" id="<?if($optClass->optUse) {?>Basketopt[]<?}else{?>Basketnopt[]<?}?>">장바구니</a>
											</div>
										</div>

										<?//=$datareservation?>
									</td>
								</tr>
							</table>
							</a>
						</li>
						<?php
						
						$i++;
					}

					mysql_free_result($result);
				?>
    		
    	
    <!-- 페이징 -->
	<div class="wrapPage">
		<?if ($pagecount > $gotopage+1) {?>
			<a href="javascript:ajaxitemlist('<?php echo $code?>', '<?php echo $listnum?>', '<?php echo $sort?>', '<?php echo $block?>', '<?php echo $gotopage+1?>', '<?php echo $search_bridx?>',
			 '<?php echo $search_price_s?>', '<?php echo $search_price_e?>', '<?php echo $search_color_idx?>', '<?php echo $searchkey?>')">더보기</a>
		<?}?>
	</div>

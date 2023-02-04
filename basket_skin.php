<?
	$receiveDateTime = $row->receiveDateTime;
	$receiveDateArr = explode("-",$row->receiveDate);
	$receiveDateTimeStr = $receiveDateArr[0]."년 "
	.$receiveDateArr[1]."월 "
	.$receiveDateArr[2]."일 "
	.$row->receiveTime;
	$addr = str_replace("=","",$row->addr);
	$prodNum = $row->prodNum;
	$receiveType = $row->receiveType;
	$receiveTypeText = $row->receiveTypeText;
	$purposeText = $row->purposeText;
	$productTypeText = $row->productTypeText;
	$priceText = $row->priceText;
	$styleText = $row->styleText;
	$aoidx = $row->aoidx;
	$rcvName = $row->rcvName;
	$tel = $row->tel;
?>
			<li>
				<div class="pr_info_area">
					<table cellpadding="0" cellspacing="0" width="100%" border="0" class="pr_infobox">
						<tr>
							<td valign="top">
							</td>
						</tr>
						<tr>
							<td>
								<div style="overflow:hidden">
									<div style="float:right;font-size:0em;line-height:0em;">
									<?
										if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
										?>
											<img src="<?=_getMobileThumbnail($origloc,$saveloc,$row->tinyimage,80,80,$quality)?>?>" />
										<?
											
										} else {
											echo "<img src=\"".$Dir."images/no_img.gif\" width=\"80\">";
										}
									?>
									</div>
									<div style="float:left;width:70%;">
										<div style="font-weight:bold;font-size: 1.2em;letter-spacing:-1px;line-height:25px;"><a href="./productdetail_tab01.php?productcode=<?=$row->productcode?>" rel="external"><?=cutStr($productname,60)?></a></div>
										<table cellpadding="0" cellspacing="0" width="100%" border="0" class="pr_info">
										<?
											$opt_change_btn = false;
											//옵션 사용여부 2016-10-04 Seul
											$optClass->setOptUse($row->productcode);
											$optClass->setOptType($row->productcode);
											if($optClass->optUse) {
												$opt_change_btn = true;
										?>
											<tr>
												<td><span class="sfont">옵션 : <?=$optClass->getOptComText($row->productcode, $row->com_idx)?></span>
													<!-- 옵션변경-->
													<?
													if ($opt_change_btn) {
														echo "<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/basket/".$_data->design_basket."/basket_skin3_btn9.gif\" style=\"cursor:pointer;\" onclick=\"optChange({$row->basketidx}, ".$row->com_idx.")\" alt=\"옵션변경\" /></div>";
													}
													?>
													<!-- 옵션변경-->
												</td>
											</tr>
										<?
											}
										?>

											<input type="hidden" name="opt_comidx" value="<?=$row->com_idx?>">

											<?if ($_data->reserve_maxuse>=0){?>
											<tr>
												<td class="pt_info_contents"><?=number_format($tempreserve)?>원 적립</td>
											</tr>
											<?}?>
											<tr>
												<td class="pt_info_contents"><span class="point4"><?=number_format($sellprice)?>원</span></td>
											</tr>
											<tr>
												<td class="basket_quantity">
													<?
														$sqlh = "SELECT a.* ";
														$sqlh.= "FROM tblproduct AS a ";
														$sqlh.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
														$sqlh.= "WHERE a.productcode='".$row->productcode."' AND a.display='Y' ";
														$resulth=mysql_query($sqlh,get_db_conn());
														if($_pdata=mysql_fetch_object($resulth)) {
														}
														if(strlen($_pdata->quantity)>0 && $_pdata->quantity<=0){
															echo "<a href=\"javascript:;\" onclick=\"alarm_productcode_add('".$row->productcode."');\" id='showMask'>재입고 알림</a>";
														}else{
													?>
														<div style="float:left;">
															<input type="button" value="-" class="basic_button" onClick="quantityControl('minus',<?=$formcount-1?>);" />
															<input class="basic_input" type="text" name="quantity" size="4" value="<? echo $row->quantity ?>" style="text-align:center;vertical-align:top;border-left:0px;border-right:0px;" />
															<input type="button" value="+" class="basic_button" onClick="quantityControl('plus',<?=$formcount-1?>);" />
														</div>
														<input type="button" value="적용" class="basic_button grayBtn" onClick="CheckForm('upd',<?=$formcount-1?>);" style="margin-left:3px;background:#eee;"  />
													<? } ?>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>			
						<tr>
							<td>
								<div style="background:#f8f8f8;padding:10px;">
									<div style="text-align:center;padding:3px;line-height:20px;">
										<div>
											<span class="receiveDateTimeConf"><?=$receiveDateTimeStr?>까지 </span>
											<span class="receiveTypeConf" style="margin-right:20px;">[<?=$receiveTypeText?>]</span>
										</div>
									</div>
									<?if($receiveType==0){?>
										<div style="margin:10px;">
											<p style="text-align:center;">
												<span><?=$addr?></span>
											</p>
											<div style="text-align:right;margin:0 10px 10px 10px;"><?=$rcvName?>(<?=$tel?>)
											</div>
										</div>
									<?}?>
								</div>
							</td>
						</tr>
						<tr>
							<td class="basket_prbtn_area">
								<?
									if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
								?>
									<button type="button" class="basic_button wish" onClick="go_wishlist('<?=$formcount-1?>')"><span >찜하기</span></button>
								<?
									} else {
								?>
									<button type="button" class="basic_button wish" onClick="check_login()"><span>찜하기</span></button>
								<?
									}
								?>
								<button type="button" class="basic_button delete" onClick="CheckForm('del',<?=$formcount-1?>)"><span>삭제</span></button>
							</td>
						</tr>
					</table>
				</div>
			</li>
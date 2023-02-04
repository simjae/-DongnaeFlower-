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

											<input type="hidden" name="opt_comidx" value="<?=$row->com_idx?>">

											<?if ($_data->reserve_maxuse>=0){?>
											<tr>
												<td class="pt_info_contents"><?=number_format($tempreserve)?>원 적립</td>
											</tr>
											<?}?>
											<tr>
												<td class="pt_info_contents"><span class="point4"><?=number_format($sellprice)?>원</span></td>
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
										수량 : <span class="prodNumConf"><?=$prodNum?></span>개
										<input type="hidden" name="quantity" value="<?=prodNum?>"/>
										
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
								<button type="button" class="basic_button delete" onClick="CheckForm('del',<?=$formcount-1?>)"><span>삭제</span></button>
							</td>
						</tr>
					</table>
				</div>
			</li>
<!--ui개선 20180723-->
<div class="wishlist_container">
	<div id="content">
		<div class="h_area2">
			<h2>위시리스트</h2>
			<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
			<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
		</div>
		
		<!-- 위시리스트 -->
		<div class="wishlist_wrap">
			<?
				$qry = "WHERE a.id='".$_ShopInfo->getMemid()."' ";
				$qry.= "AND a.productcode=b.productcode AND b.display='Y' ";
				$qry.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";

				$sql = "SELECT COUNT(*) as t_count ";
				$sql.= "FROM tblwishlist a, tblproduct b ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
				$sql.= $qry;
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$t_count = (int)$row->t_count;
				mysql_free_result($result);
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
			?>

			<h2>나의 Wishlist <span class="point3"><?=$t_count?></span>개</h2>
			<?
				$tmp_sort=explode("_",$sort);
				$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.com_idx,b.productcode,b.productname,b.sellprice,b.sellprice as realprice, ";
				$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
				$sql.= "b.etctype,a.wish_idx,a.marks,a.memo,b.selfcode,b.assembleuse,b.package_num FROM tblwishlist a, tblproduct b ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
				$sql.= $qry." ";
				if($tmp_sort[0]=="date") $sql.= "ORDER BY a.date ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="marks") $sql.= "ORDER BY a.marks ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="price") $sql.= "ORDER BY b.sellprice ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="name") $sql.= "ORDER BY b.productname ".$tmp_sort[1]." ";
				else $sql.= "ORDER BY a.date DESC ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result=mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$row->quantity=1;

					if(ereg("^(\[OPTG)([0-9]{4})(\])$",$row->option1)) {
						$optioncode = substr($row->option1,5,4);
						$row->option1="";
						$row->option_price="";
						if($row->optidxs!="") {
							$tempoptcode = substr($row->optidxs,0,-1);
							$exoptcode = explode(",",$tempoptcode);

							$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ";
							$resultopt = mysql_query($sqlopt,get_db_conn());
							if($rowopt = mysql_fetch_object($resultopt)){
								$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
								$opti=0;
								$optvalue="";
								$option_choice = $rowopt->option_choice;
								$exoption_choice = explode("",$option_choice);
								while(strlen($optionadd[$opti])>0){
									if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
										$delsql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
										$delsql.= "AND productcode='".$row->productcode."' ";
										$delsql.= "AND opt1_idx='".$row->opt1_idx."' AND opt2_idx='".$row->opt2_idx."' ";
										$delsql.= "AND optidxs='".$row->optidxs."' ";
										mysql_query($delsql,get_db_conn());
									}
									if($exoptcode[$opti]>0){
										$opval = str_replace('"','',explode("",$optionadd[$opti]));
										$optvalue.= ", ".$opval[0]." : ";
										$exop = str_replace('"','',explode(",",$opval[$exoptcode[$opti]]));
										if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=\"#FF3C00\">+".$exop[1]."원</font>)";
										else if($exop[1]==0) $optvalue.=$exop[0];
										else $optvalue.=$exop[0]."(<font color=\"#FF3C00\">".$exop[1]."원</font>)";
										$row->realprice+=($row->quantity*$exop[1]);
									}
									$opti++;
								}
								$optvalue = substr($optvalue,1);
							}
						}
					} else {
						$optvalue="";
					}

					if (strlen($row->option_price)==0) {
						$price = $row->realprice;
						$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
						$sellprice=$row->sellprice;
					} else if (strlen($row->opt1_idx)>0) {
						$option_price = $row->option_price;
						$pricetok=explode(",",$option_price);
						$priceindex = count($pricetok);
						$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
						$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
						$sellprice=$pricetok[$row->opt1_idx-1];
					}
					//옵션 사용여부 2016-10-17 Seul
					$optClass->setOptUse($row->productcode);

					//무제한 옵션이 있으면 옵션가격으로 조정 2016-10-11 Seul
					if($optClass->optUse) {
						$price = $price + $optClass->getOptPrice($row->com_idx);
					}

					$bankonly_html = ""; $setquota_html = "";
					
					if (strlen($row->etctype)>0) {
						$etctemp = explode("",$row->etctype);
						for ($i=0;$i<count($etctemp);$i++) {
							switch ($etctemp[$i]) {
								case "BANKONLY": $bankonly = "Y";
									$bankonly_html = " <img src=\"".$Dir."images/common/bankonly.gif\" border=\"0\"> ";
									break;
								case "SETQUOTA":
									if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
										$setquotacnt++;
										$setquota_html = " <img src=\"".$Dir."images/common/setquota.gif\" border=\"0\">";
										$setquota_html.= "</b><font color=\"#000000\" size=\"1\">(";
										$setquota_html.="3~";
										$setquota_html.= $_data->card_splitmonth.")</font>";
									}
									break;
							}
						}
					}



					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					if($cnt>0) {
				
					}
			?>

					<div class="wishlist_list">
						<a href="./productdetail_tab01.php?productcode=<?=$row->productcode?>" rel="external">
						<table cellpadding="0" cellspacing="0" border="0" class="wishlist_table">
							<tr>
								<td class="wishlist_check_wrap">
									<input type="checkbox" name="sels[]" value="<?=$row->wish_idx?>"  id="wish01" class="input_check" />
								</td>
								<td class="wishlist_image_wrap">
									<a href="productdetail.php?productcode=<?=$row->productcode?>" rel="external">
										<?
											if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
												$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
												if($file_size[0]>=$file_size[1]){
													$setSize = 'width="40"';
												}else{
													$setSize = 'height="40"';
												}
										?>
											<img src="<?=$Dir.DataDir?>shopimages/product/<?=$row->tinyimage?>" <?=$setSize?>/>
										<?
											} else {
										?>
											<img src="images/no_img.gif" width="50" />
										<?
											}
										?>
									</a>
								</td>
								<td>
									<table cellpadding="0" cellspacing="0" class="wishlist_info_table" border="0">
										<tr>
											<th>상품명</th>
											<td>
												<strong><?=_strCut($row->productname,22,2,$charset)?></strong>
												
												<?
													if($optClass->optUse) {
												?>
													<br/><img border=0 src="../images/common/basket/001/basket_skin3_icon002.gif"><?=$optClass->getOptComText($row->productcode, $row->com_idx)?>
												<?
													}
												?>
											</td>
										</tr>
										<tr>
											<th>판매가</th>
											<td><?=number_format($price)?>원</td>
										</tr>
										<?
											if($tempreserve>0){
										?>
										<tr>
											<th>적립금</th>
											<td><?=number_format($tempreserve)?>원</td>
										</tr>
										<?
											}
										?>
									</table>
								</td>
							</tr>
						</table>
						</a>
						<div class="wishlist_btn_wrap">
							<a href="#" class="basic_button" onClick="javascript:CheckForm('ordernow','<?=$row->wish_idx?>')"><span>바로구매</span></a>
							<a href="#" class="basic_button" onClick="javascript:CheckForm('','<?=$row->wish_idx?>')"><span>장바구니</span></a>
						</div>
					</div>
				</li>
				<?
					$miniq = 1; 
					if (strlen($row->etctype)>0) {
						$etctemp = explode("",$row->etctype);
						for ($i=0;$i<count($etctemp);$i++) {
							if (substr($etctemp[$i],0,6)=="MINIQ=") $miniq=substr($etctemp[$i],6);
						}
					}
					echo "<input type=hidden name=productcode_".$row->wish_idx." value=\"".$row->productcode."\">\n";
					echo "<input type=hidden name=option1_".$row->wish_idx." value=\"".$row->opt1_idx."\">\n";
					echo "<input type=hidden name=option2_".$row->wish_idx." value=\"".$row->opt2_idx."\">\n";
					echo "<input type=hidden name=opts_".$row->wish_idx." value=\"".$row->optidxs."\">\n";
					echo "<input type=hidden name=opt_comidx_".$row->wish_idx." value=\"".$row->com_idx."\">\n";
					echo "<input type=hidden name=quantity_".$row->wish_idx." value=\"".$miniq."\">\n";
					echo "<input type=hidden name=assembleuse_".$row->wish_idx." value=\"".$row->assembleuse."\">\n";
					echo "<input type=hidden name=packagenum_".$row->wish_idx." value=\"".((int)$row->package_num?$row->package_num:"")."\">\n";
					$cnt++;
				}
				mysql_free_result($result);

				if($cnt==0) {
					echo "<div style=\"text-align:center;padding-top:10px\">해당내역이 없습니다.</div>";
				}
			?>
			</ul>
			
			<section class="basic_btn_area">
				<a href="#" class="basic_button" onClick="CheckBoxAll()">전체상품선택</a>&nbsp;
				<a href="#" class="basic_button" onClick="GoDelete()">선택상품삭제</a>
			</section>
		</div>
	</div>
</div>
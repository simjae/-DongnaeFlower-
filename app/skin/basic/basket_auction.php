<div id="content">
	<div class="h_area2">
		<h2>장바구니</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<!-- 장바구니 -->
	<div class="basket">
		<ul class="my_pr_list basket">
		<!-- 상품리스트 -->
<?
	//$sql = "SELECT b.vender,(select IF(deli_super='S',NULL,b.vender) from tblvenderinfo where vender = b.vender) as deli_super FROM ".$tblbasket." a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql = "SELECT b.vender,(select IF(deli_super='S',NULL,b.vender) from tblvenderinfo where vender = b.vender) as deli_super FROM ".$tblbasket." a, tblproduct b WHERE a.".$basketWhere." ";
	$sql.= "AND a.productcode=b.productcode GROUP BY deli_super ";
	$res=mysql_query($sql,get_db_conn());

	$cnt=0;
	$sumprice = 0;
	$deli_price = 0;
	$reserve = 0;
	$formcount=0;
	while($vgrp=mysql_fetch_object($res)) {
		//1. vender가 0이 아니면 해당 입점업체의 배송비 추가 설정값을 가져온다.
		unset($_vender);
		if($vgrp->deli_super != NULL ) {
			$sql = "SELECT deli_super,deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='".$vgrp->deli_super."' ";
			$res2=mysql_query($sql,get_db_conn());
			if($_vender=mysql_fetch_object($res2)) {
				if($_vender->deli_price==-9) {
					$_vender->deli_price=0;
					$_vender->deli_after="Y";
				}
				if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;
			}
			mysql_free_result($res2);

		}

		$result = getBasketByResource($tblbasket,$vgrp->deli_super);
		
		
		$vender_sumprice = 0;	//해당 입점업체의 총 구매액
		$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
		$vender_deliprice = 0;
		$deli_productprice=0;
		$deli_init = false;
		
		while($row = mysql_fetch_object($result)) {
			//옵션 사용여부 2016-10-17 Seul
			$optClass->setOptUse($row->productcode);

			$arPresent[$formcount] = $row->present_state;
			$arPester[$formcount] = $row->pester_state;
			$sellChk = true;
			if($row->sell_startdate && $row->sell_enddate){
				$sellChk = false;
				if($row->sell_startdate<time() && time()<$row->sell_enddate){
					$sellChk = true;
				}
			}
			if (strlen($row->option_price)>0 && $row->opt1_idx==0) {
				$sql = "DELETE FROM ".$tblbasket." WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
				$sql.= "AND productcode='".$row->productcode."' AND opt1_idx='".$row->opt1_idx."' ";
				$sql.= "AND opt2_idx='".$row->opt2_idx."' AND optidxs='".$row->optidxs."' ";
				mysql_query($sql,get_db_conn());

				echo "<script>alert('필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir."app/productdetail.php?productcode=".$row->productcode."\";</script>";
				exit;
			}
			if(ereg("^(\[OPTG)([0-9]{4})(\])$",$row->option1)){
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
								$delsql = "DELETE FROM ".$tblbasket." WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
								$delsql.= "AND productcode='".$row->productcode."' ";
								$delsql.= "AND opt1_idx='".$row->opt1_idx."' AND opt2_idx='".$row->opt2_idx."' ";
								$delsql.= "AND optidxs='".$row->optidxs."' ";
								mysql_query($delsql,get_db_conn());
								echo "<script>alert('필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir."app/productdetail.php?productcode=".$row->productcode."\";</script>";
								exit;
							}
							if($exoptcode[$opti]>0){
								$opval = explode("",str_replace('"','',$optionadd[$opti]));
								$optvalue.= ", ".$opval[0]." : ";
								$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
								if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
								else if($exop[1]==0) $optvalue.=$exop[0];
								else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
								$row->sellprice+=$exop[1];
							}
							$opti++;
						}
						$optvalue = substr($optvalue,1);
					}
				}
			} else {
				$optvalue="";
			}

			$cnt++;

			echo "<form name=form_".$formcount." method=post action=\"basket.php\">\n"; $formcount++;
			echo "<input type=hidden name=mode value=\"\">\n";
			echo "<input type=hidden name=code value=\"".$code."\">\n";
			echo "<input type=hidden name=productcode value=\"".$row->productcode."\">\n";
			echo "<input type=hidden name=orgquantity value=\"".$row->quantity."\">\n";
			echo "<input type=hidden name=orgoption1 value=\"".$row->opt1_idx."\">\n";
			echo "<input type=hidden name=orgoption2 value=\"".$row->opt2_idx."\">\n";
			echo "<input type=hidden name='basketidx' value=\"".$row->basketidx."\">\n";
			echo "<input type=hidden name='productname' value=\"".strip_tags($row->productname)."\">\n";
			echo "<input type=hidden name=opts value=\"".$row->optidxs."\">\n";
			echo "<input type=hidden name=brandcode value=\"".$brandcode."\">\n";
			echo "<input type=hidden name=assemble_list value=\"".$row->assemble_list."\">\n";
			echo "<input type=hidden name=assemble_idx value=\"".$row->assemble_idx."\">\n";
			echo "<input type=hidden name=package_idx value=\"".$row->package_idx."\">\n";
			
			$assemble_str="";
			$package_str="";
			$packagelist_str="";
			
			#####################상품별 회원할인율 적용 시작#######################################
			$old_sellprice = $row->sellprice;
			$discountprices = getProductDiscount($row->productcode);
			if($discountprices>0 AND isSeller() != 'Y'){
				$row->sellprice = $row->sellprice - $discountprices;
				$row->realprice = $row->sellprice*$row->quantity;
			}
			#####################상품별 회원할인율 적용 끝 #######################################


			if($row->assemble_idx>0 && strlen(str_replace("","",$row->assemble_list))>0) {
				$assemble_list_proexp = explode("",$row->assemble_list);
				//$alprosql = "SELECT productcode,productname,sellprice FROM tblproduct ";
				$alprosql = "SELECT productcode,productname,".((isSeller()=='Y')?'if(productdisprice>0,productdisprice,sellprice) as sellprice':'sellprice')." FROM tblproduct ";

				$alprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_proexp)."') ";
				$alprosql.= "AND display = 'Y' ";
				$alprosql.= "ORDER BY FIELD(productcode,'".implode("','",$assemble_list_proexp)."') ";
				$alproresult=mysql_query($alprosql,get_db_conn());

				$assemble_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
				$assemble_str.="		<td width=\"100%\">\n";
				$assemble_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";

				$assemble_sellerprice=0;
				while($alprorow=@mysql_fetch_object($alproresult)) {
					$assemble_str.="		<tr>\n";
					$assemble_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
					$assemble_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
					$assemble_str.="			<col width=\"\"></col>\n";
					$assemble_str.="			<col width=\"80\"></col>\n";
					$assemble_str.="			<col width=\"120\"></col>\n";
					$assemble_str.="			<tr>\n";
					$assemble_str.="				<td style=\"padding:4px; word-break:break-all;\"><font color=\"#000000\">".$alprorow->productname."</font>&nbsp;</td>\n";
					$assemble_str.="				<td align=\"right\" style=\"padding:4px; border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\"><font color=\"#000000\">".number_format((int)$alprorow->sellprice)."원</font></td>\n";
					$assemble_str.="				<td align=\"center\" style=\"padding:4px;\">본 상품 1개당 수량1개</td>\n";
					$assemble_str.="			</tr>\n";
					$assemble_str.="			</table>\n";
					$assemble_str.="			</td>\n";
					$assemble_str.="		</tr>\n";
					$assemble_sellerprice+=$alprorow->sellprice;
				}
				@mysql_free_result($alproresult);
				$assemble_str.="		</table>\n";
				$assemble_str.="		</td>\n";

				//######### 코디/조립에 따른 가격 변동 체크 ###############
				$price = $assemble_sellerprice*$row->quantity;
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$assemble_sellerprice,"N");
				//상품홍보 적립금
				if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
					$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$assemble_sellerprice,"N");
				}
				$sellprice=$assemble_sellerprice;
			} else if($row->package_idx>0 && strlen($row->package_idx)>0) {
				$package_str ="<a href=\"javascript:setPackageShow('packageidx".$cnt."');\">".$title_package_listtmp[$row->productcode][$row->package_idx]."(<font color=#FF3C00>+".number_format($price_package_listtmp[$row->productcode][$row->package_idx])."원</font>)</a>";

				$productname_package_list_exp = $productname_package_list[$row->productcode][$row->package_idx];
				if(count($productname_package_list_exp)>0) {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";

					for($i=0; $i<count($productname_package_list_exp); $i++) {
						$packagelist_str.="		<tr>\n";
						$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
						$packagelist_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
						$packagelist_str.="			<col width=\"\"></col>\n";
						$packagelist_str.="			<col width=\"120\"></col>\n";
						$packagelist_str.="			<tr>\n";
						$packagelist_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$productname_package_list_exp[$i]."</font>&nbsp;</td>\n";
						$packagelist_str.="				<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;\">본 상품 1개당 수량1개</td>\n";
						$packagelist_str.="			</tr>\n";
						$packagelist_str.="			</table>\n";
						$packagelist_str.="			</td>\n";
						$packagelist_str.="		</tr>\n";
					}
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				} else {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
					$packagelist_str.="		<tr>\n";
					$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;padding:4px;word-break:break-all;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
					$packagelist_str.="		</tr>\n";
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				}

				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$sellprice=$row->sellprice+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					//상품홍보 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$sellprice,"N");
					}
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$sellprice=$pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
					if($price){
					$price = $sellprice*$row->quantity;
					}
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					//상품홍보 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$sellprice,"N");
					}
				}
			} else {
				//######### 옵션에 따른 가격 변동 체크 ###############
				if ($row->com_idx>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
					//상품홍보 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$pricetok[$row->opt1_idx-1],"N");
					}
				 	$sellprice = ($row->sellprice + $optClass->getOptPrice($row->com_idx))*$row->quantity;
					
					if(!$price){
						$price = $sellprice;
					}
				} else if (strlen($row->option_price)==0) {
					$price = $row->sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
					//상품홍보 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$row->sellprice,"N");
					}
					$sellprice = $row->sellprice*$row->quantity;
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
					//상품홍보 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$pricetok[$row->opt1_idx-1],"N");
					}
					$sellprice=$pricetok[$row->opt1_idx-1];
				}
			}

			#####################상품별 회원할인율 적용 시작#######################################
			if($discountprices>0){
				$strSellPrice = "<strike>".number_format($old_sellprice)."원</strike><br />".number_format($sellprice)."원";
			}else{
				$strSellPrice = number_format($old_sellprice)."원";
			}
			$discountSum += ($old_sellprice-$sellprice)*$row->quantity;
			#####################상품별 회원할인율 적용 끝 #######################################

			//######### 옵션에 따른 가격 변동 체크 끝 ############
			$sumprice += $price;
			$vender_sumprice += $price;

			
			//################ 개별 배송비 체크 #################
			$deli_str = "";
			if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
				if($row->deli=="Y") {
					$deli_productprice += $row->deli_price*$row->quantity;
				//	$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(구매수 대비 증가:".number_format($row->deli_price*$row->quantity)."원)</font></font>";
					$deli_str = "유료배송".number_format($row->deli_price*$row->quantity)."원";
				} else {
					$deli_productprice += $row->deli_price;
					//$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(".number_format($row->deli_price)."원)</font></font>";
					$deli_str = "유료배송".number_format($row->deli_price)."원";
				}
			} else if($row->deli=="F" || $row->deli=="G") {
				$deli_productprice += 0;
				if($row->deli=="F") {
					//$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#0000FF>(무료)</font></font>";
					$deli_str = '<font style="font-size:0.8em;color:#2F9D27;">무료배송</font>';
				} else {
					$deli_str = '<font style="font-size:0.8em;color:#2F9D27;">착불</font>';
				}
			} else {
				$deli_init=true;
				$vender_delisumprice += $price;
				if($row->vender == 0) {
					$deli_str = '<font style="font-size:0.8em;color:#2F9D27;">기본배송</font>';
				} else {
					$deli_str = '<font style="font-size:0.8em;color:#2F9D27;">입점몰기본배송</font>';
				}
			}

			//###################################################
			$productname=$row->productname;

			$reserve += $tempreserve*$row->quantity;

			//######## 특수값체크 : 현금결제상품//무이자상품 #####
			$bankonly_html = ""; $setquota_html = "";
			if (strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				for ($i=0;$i<count($etctemp);$i++) {
					switch ($etctemp[$i]) {
						case "BANKONLY": $bankonly = "Y";
							$bankonly_html = " <img src=".$Dir."images/common/bankonly.gif border=0 align=absmiddle> ";
							break;
						case "SETQUOTA":
							if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
								$setquotacnt++;
								$setquota_html = " <img src=".$Dir."images/common/setquota.gif border=0 align=absmiddle>";
								$setquota_html.= "</b><font color=black size=1>(";
								//if ($card_type=="IN" || $card_type=="BO") $setquota_html.="2~";
								//else                  $setquota_html.="3~";
								$setquota_html.="3~";
								$setquota_html.= $_data->card_splitmonth.")</font>";
							}
							break;
					}
				}
			}
?>

		<!-- 상품리스트 START -->
		<? include('basket_skin.php'); ?>
	
	<!-- 상품 리스트 END -->
<?
		echo '</form>';
		}
		mysql_free_result($result);

		$vender_deliprice=$deli_productprice;

		if($_vender) {
			if($_vender->deli_price>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_vender->deli_mini && $deli_init==true) {
					$vender_deliprice+=$_vender->deli_price;
				}
			} else if(strlen($_vender->deli_limit)>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}
				if($deli_init==true) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_vender->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		} else {
			if($_data->deli_basefee>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_data->deli_miniprice && $deli_init==true) {
					$vender_deliprice+=$_data->deli_basefee;
				}
			} else if(strlen($_data->deli_limit)>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if($deli_init==true) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		}
		$deli_price+=$vender_deliprice;
	}
	mysql_free_result($res);

	if($cnt==0){
?>
		</ul>
		<div class="iconshoppingBag">
			<span><img src="/m/skin/basic/img/icon_shoppingBag.png"></span>
			장바구니가 비어있습니다.
		</div>
<?
	}else{
?>
		<!-- 상품TOTAL START -->
		<table cellpadding="0" cellspacing="0" border="0" class="basket_total_info" width="100%">
			<tr>
				<th><span>상품 합계금액</span></th>
				<td><span><?=number_format($sumprice)?>원</span></td>
			</tr>

			<? if($_data->ETCTYPE["VATUSE"]=="Y"){
				$sumpricevat = return_vat($sumprice);
			?>
			<tr>
				<th><span>부가세(VAT) 합계금액</span></th>
				<td><span><?=number_format($sumpricevat)?>원</span></td>
			</tr>
			<? } ?>

			<? if($deli_price>0){ ?>
			<tr>
				<th><span>배송비 합계금액</span></th>
				<td><span>+ <?=number_format($deli_price)?>원</span></td>
			</tr>
			<? } ?>

			<tr>
				<th><span>총 결제금액</span></th>
				<td><span class="point3"><strong><?=number_format($sumprice+$deli_price+$sumpricevat)?></strong>원</span></span></td>
			</tr>

			<? if($reserve>0 && $_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0){ ?>
			<tr>
				<th><span>적립금</span></th>
				<td><span><strong><?=number_format($reserve)?></strong>원</span></td>
			</tr>
			<? } ?>

		</table>
		<!-- //상품TOTAL END -->

<?
	/*if($sumprice<$_data->deli_miniprice && $_data->deli_after!="Y" && $_data->deli_basefee>0) { 
		if($_data->deli_miniprice<1000000000) {
			echo "<tr><td height=\"30\" align=\"right\" valign=\"top\" style=\"padding-right:5px;\"><font color=\"#FF4C00\" style=\"font-size:11px;letter-spacing:-0.5pt;\">* ".number_format($_data->deli_miniprice)."원 미만의 주문은 배송료를 청구합니다.</font></td></tr>\n";
		} else {
			echo "<tr><td height=\"30\" align=\"right\" valign=\"top\" style=\"padding-right:5px;\"><font color=\"#FF4C00\" style=\"font-size:11px;letter-spacing:-0.5pt;\">* 주문에 배송료 ".number_format($_data->deli_basefee)."원을 청구합니다.</font></td></tr>\n";
		}
	} else if($_data->deli_after=="Y") {
		echo "<tr><td height=\"30\" align=\"right\" valign=\"top\" style=\"padding-right:5px;\"><font color=\"#FF4C00\" style=\"font-size:11px;letter-spacing:-0.5pt;\">* 배송료는 착불로 소비자 부담입니다.</font></td></tr>\n";
	}*/

	if(strlen($_ShopInfo->getMemid())>0 && strlen($_ShopInfo->getMemgroup())>0 && substr($_ShopInfo->getMemgroup(),0,1)!="M") {
		$arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"");
		$sql = "SELECT a.name,b.group_code,b.group_name,b.group_payment,b.group_usemoney,b.group_addmoney ";
		$sql.= "FROM tblmember a, tblmembergroup b WHERE a.id='".$_ShopInfo->getMemid()."' AND b.group_code=a.group_code ";
		$sql.= "AND MID(b.group_code,1,1)!='M' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="basket_group_wrap">
			<tr>
				<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td height="20"><B><?=$row->name?></B>님은 <B><FONT COLOR="#EE1A02">[<?=$row->group_name?>]</FONT></B> 회원입니다.</td>
						</tr>
						<tr>
							<td height="20"><FONT COLOR="#EE1A02"><B><?=number_format($row->group_usemoney)?>원</B></FONT> 이상 <?=$arr_dctype[$row->group_payment]?>구매시,
							<?
							$type=substr($row->group_code,0,2);
							if($type=="RW") echo "적립금에 ".number_format($row->group_addmoney)."원을 <font color=\"#EE1A02\"><B>추가 적립</B></font>해 드립니다.";
							else if($type=="RP") echo "구매 적립금의 ".number_format($row->group_addmoney)."배를 <font color=\"#EE1A02\"><B>적립</B></font>해 드립니다.";
							else if($type=="SW") echo "구매금액 ".number_format($row->group_addmoney)."원을 <font color=\"#EE1A02\"><B>추가 할인</B></font>해 드립니다.";
							else if($type=="SP") echo "구매금액의 ".number_format($row->group_addmoney)."%를 <font color=\"#EE1A02\"><B>추가 할인</B></font>해 드립니다.";
							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
<?
		}
		mysql_free_result($result);
	}
?>

		<!-- 버튼 -->
<?
		if(strlen($code)>0) {
			if($brandcode>0) {
				$shopping_url="productblist.php?code=".substr($code,0,12)."&brandcode=".$brandcode;
			} else {
				$shopping_url="productlist.php?code=".substr($code,0,12);
			}
		} else {
			$shopping_url= "main.php";
		}
?>

		<section class="basic_btn_area">
			<a href="#" class="basic_button" onClick="javascript:basket_clear()">장바구니비우기</a>
			<a href="#" class="basic_button" onClick="location.href='<?=$shopping_url?>'">계속쇼핑</a>
			<a href="#" class="basic_button orangeBtn" onClick="javascript:orderInfo()">주문하기</a>
            <?
                //네이버 API 함수 호출
                if (function_exists('Naver_API_Product_detail')) {
                    Naver_API_Product_detail_btn('mobileBasket', $_ShopInfo->shopid); //네이버페이 버튼 출력
                    Naver_API_Product_detail_mobile_cssjs(); //css+js 호출(상단에 한번만 호출)
                }
                //네이버 API 함수 호출
            ?>
		</section>
		<!-- //버튼 -->

        <?
            //네이버 API 함수 호출
            if (function_exists('Naver_API_Product_detail')) {
                Naver_API_Product_detail_basket($productcode, $_ShopInfo->getMemid(), $_ShopInfo->getTempkey(), 'Mobile', $_ShopInfo->shopid);
            }
            //네이버 API 함수 호출
        ?>

<? } ?>
	</div>
	<!-- //장바구니 -->
</div>
<hr>
	<div class="h_area2">
		<h2>주문서 작성</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
<div id="orderend_container">

<?
	if (preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) {
?>
	<div class="orderend_msg" style="display:table;background-color:#e3e3e3;width:100%;">
		<div class="" style="height:70px;background-color:#FFF;margin:10px;">
			<?
				if(preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) {
					if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
						echo "<font color=\"#FF6600\"><b>".$_ord->sender_name."님의 주문이 완료되었습니다.</b></font><br><br>\n";
						if ($totreserve>0) echo "귀하의 제품 구입에 따른 적립금 <font color=\"#FF6600\"><b>".number_format($totreserve)."원</b></font>은 배송과 함께 바로 적립됩니다.<br>\n";
					} else {
						echo "주문이 완료되었습니다.<br>\n";
						echo "귀하의 주문확인 번호는 <font color=0000a0><b>".substr($_ord->id,1,6)."</b></font>입니다.<br>\n";
					}
				} else if (preg_match("/^(V|O|Q|C|P|M)$/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")!=0 ) {
					echo "<font color=red size=3><b>주문이 실패되었습니다.</b></font><br>\n";
				}

				if(preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(O|Q){1}/", $_ord->paymethod) && $_ord->pay_flag=="0000")) {
					echo "입금방법이 무통장입금의 경우 계좌번호를 메모하세요.<br>저희가 입금확인 후 바로 보내드립니다.<br><br>\n";
				} else if(preg_match("/^(C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0) {
					echo "저희가 확인 후 바로 보내드립니다.<br><br>\n";
				}

				if ((preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && strlen($_data->orderend_msg)>0) {
					echo ereg_replace("\n","<br>",$_data->orderend_msg);
					echo "<br>\n";
				}
			?>
		</div>
	</div>
<?}?>
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin:0px;padding:0px;">
<tr>
	<td align=center>
	<table cellpadding="0" cellspacing="0" width="100%" align=center>
	<TR>
		<TD>주문상세내역</TD>
	</TR>
	<tr>
		<td height="2"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<col></col>
		<col width="30"></col>
		<col width="60"></col>
		<col width="80"></col>
		<tr>
			<td height="2" colspan="4" bgcolor="#000000"></td>
		</tr>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><font color="#333333"><b>상품명</b></font></td>
			<td><font color="#333333"><b>수량</b></font></td>
			<td><font color="#333333"><b>적립금</b></font></td>
			<td><font color="#333333"><b>주문금액</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="4" bgcolor="#DDDDDD"></td>
		</tr>
<?
	$sql = "SELECT productcode,productname,price,reserve,opt1_name,opt2_name,tempkey,addcode,quantity,order_prmsg,selfcode,package_idx,assemble_idx,assemble_info ";
	$sql.= "FROM tblorderproduct WHERE ordercode='".$ordercode."' ORDER BY productcode ASC ";
	$result=mysql_query($sql,get_db_conn());
	$sumprice=0;
	$sumreserve=0;
	$totprice=0;
	$totreserve=0;
	$totquantity=0;
	$cnt=0;
	unset($etcdata);
	unset($prdata);
	while($row=mysql_fetch_object($result)) {
		$optvalue="";
		if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row->opt1_name)) {
			$optioncode=$row->opt1_name;
			$row->opt1_name="";
			$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$ordercode."' AND productcode='".$row->productcode."' ";
			$sql.= "AND opt_idx='".$optioncode."' ";
			$result2=mysql_query($sql,get_db_conn());
			if($row2=mysql_fetch_object($result2)) {
				$optvalue=$row2->opt_name;
			}
			mysql_free_result($result2);
		}

		$isnot=false;
		if (substr($row->productcode,0,3)!="999" && substr($row->productcode,0,3)!="COU") {
			$no++;
			$sumreserve=$row->reserve*$row->quantity;
			$totreserve+=$sumreserve;
			$totquantity+=$row->quantity;
			$isnot=true;
		}
		if(ereg("^(COU)([0-9]{8})(X)$",$row->productcode)) {				#쿠폰
			$etcdata[]=$row;
			continue;
		} else if(ereg("^(9999999999)([0-9]{1})(X)$",$row->productcode)) {
			#99999999999X : 현금결제시 결제금액에서 추가적립/할인
			#99999999998X : 에스크로 결제시 수수료
			#99999999997X : 부가세(VAT)
			#99999999990X : 상품배송비
			$etcdata[]=$row;
			continue;
		} else {															#진짜상품
			$prdata[]=$row;
		}

		$sumprice=$row->price*$row->quantity;
		$totprice+=$sumprice;
		echo "<tr ".($sumprice<0?"height=\"22\" bgcolor=\"#F2FAFF\"":"height=\"26\"").">\n";
		echo "	<td ".($sumprice<0?"align=\"right\" style=\"word-break:break-all;\"":"style=\"padding-left:5px;word-break:break-all;\"").">";
		echo ($sumprice<0?"<font color=\"#0000FF\"><b>".viewselfcode($row->productname,$row->selfcode)."</B></font><b>&nbsp:&nbsp;</b>":"<font color=\"#000000\"><B>".viewselfcode($row->productname,$row->selfcode)."</B></font>");
		echo "	</td>\n";
		echo "	<td align=\"center\"><font color=\"#000000\">".($isnot==true?$row->quantity:"&nbsp;")."</font></td>\n";
		echo "	<td align=\"right\" style=\"padding-right:5px\"><font color=\"#000000\">".($isnot==true?number_format($sumreserve)."원":"&nbsp;")."</font></td>\n";
		echo "	<td align=\"right\" style=\"padding-right:5px\"><font color=\"#FF3C00\"><b>".number_format($sumprice)."원</b></font></td>\n";
		echo "</tr>\n";
		if(strlen($row->opt1_name)>0 || strlen($row->opt2_name)>0 || strlen($optvalue)>0 || strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) {
			if(strlen($row->opt1_name)>0 || strlen($row->opt2_name)>0 || strlen($optvalue)>0) {
				echo "<tr>\n";
				echo "	<td colspan=\"4\" style=\"padding:5px;padding-top:0px;word-break:break-all;\">";
				if(strlen($row->addcode)>0) echo "특징 : ".$row->addcode."&nbsp;&nbsp;";
				if(strlen($row->opt1_name)>0) echo " ".$row->opt1_name." ";
				if(strlen($row->opt2_name)>0) echo ", ".$row->opt2_name." ";
				if(strlen($optvalue)>0) echo $optvalue;
				echo "	</td>\n";
				echo "</tr>\n";
				$row->addcode="";
			}
			if(strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) {
				$assemble_infoall_exp = explode("=",$row->assemble_info);
				
				if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
					echo "<tr>\n";
					echo "	<td colspan=\"4\" style=\"padding:5px;padding-top:0px;word-break:break-all;\">";
					if(strlen($row->addcode)>0) echo "특징 : ".$row->addcode."&nbsp;&nbsp;";
					$package_info_exp = explode(":", $assemble_infoall_exp[0]);
					if(strlen($package_info_exp[3])>0) echo "패키지선택 : <a href=\"javascript:setPackageShow('packageidx".$cnt."');\">".$package_info_exp[3]."(<font color=#FF3C00>+".number_format($package_info_exp[2])."원</font>)</a>";
					$productname_package_list_exp = explode("",$package_info_exp[1]);
					echo "	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					echo "	<tr id=\"packageidx".$cnt."\" style=\"display:none;\">\n";
					if(count($productname_package_list_exp)>0 && strlen($productname_package_list_exp[0])>0) {
						echo "		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						echo "		<td width=\"100%\">\n";
						echo "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
						
						for($i=0; $i<count($productname_package_list_exp); $i++) {
							echo "		<tr>\n";
							echo "			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
							echo "			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
							echo "			<col width=\"\"></col>\n";
							echo "			<col width=\"120\"></col>\n";
							echo "			<tr>\n";
							echo "				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$productname_package_list_exp[$i]."</font>&nbsp;</td>\n";
							echo "				<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;\">본 상품 1개당 수량1개</td>\n";
							echo "			</tr>\n";
							echo "			</table>\n";
							echo "			</td>\n";
							echo "		</tr>\n";
						}
						echo "		</table>\n";
						echo "		</td>\n";
					} else {
						echo "		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						echo "		<td width=\"100%\">\n";
						echo "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
						echo "		<tr>\n";
						echo "			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;padding:4px;word-break:break-all;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
						echo "		</tr>\n";
						echo "		</table>\n";
						echo "		</td>\n";
					}
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</td>\n";
					echo "</tr>\n";
					$row->addcode="";
				}
				if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) { 
					echo "<tr>\n";
					echo "	<td colspan=\"4\" style=\"padding:5px;padding-top:0px;padding-left:5px;\"><nobr>";
					if(strlen($row->addcode)>0) echo "특징 : ".$row->addcode."<br>";
					if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
						echo "<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
						echo "<tr>\n";
						echo "	<td width=\"50\" valign=\"top\" style=\"padding-left:5px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						echo "	<td width=\"100%\">\n";
						echo "	<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
						
						$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

						if(count($assemble_info_exp)>2) {
							$assemble_productname_exp = explode("", $assemble_info_exp[1]);
							$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

							for($k=0; $k<count($assemble_productname_exp); $k++) {
								echo "	<tr>\n";
								echo "		<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
								echo "		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
								echo "		<col width=\"\"></col>\n";
								echo "		<col width=\"80\"></col>\n";
								echo "		<col width=\"120\"></col>\n";
								echo "		<tr>\n";
								echo "			<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$assemble_productname_exp[$k]."</font>&nbsp;</td>\n";
								echo "			<td align=\"right\" style=\"padding:4px;border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\"><font color=\"#000000\">".number_format((int)$assemble_sellprice_exp[$k])."원</font></td>\n";
								echo "			<td align=\"center\" style=\"padding:4px;\">본 상품 1개당 수량1개</td>\n";
								echo "		</tr>\n";
								echo "		</table>\n";
								echo "		</td>\n";
								echo "	</tr>\n";
							}
						}
						echo "	</table>\n";
						echo "	</td>\n";
						echo "</tr>\n";
						echo "</table>\n";
					}
					echo "	</td>\n";
					echo "</tr>\n";
					$row->addcode="";
				}
			}
		} else if(strlen($row->addcode)>0) {
			echo "<tr>\n";
			echo "	<td colspan=\"4\" style=\"padding-left:5px;word-break:break-all;\">";
			if(strlen($row->addcode)>0) echo "특징 : ".$row->addcode;
			echo "	</td>\n";
			echo "</tr>\n";
		}
		if($sumprice>=0) {
			echo "<tr><td colspan=\"4\" height=\"1\" bgcolor=\"#DDDDDD\"></td></tr>\n";
		}
	}
	mysql_free_result($result);
?>
		<tr height="26" bgcolor="#F9F9F9">
			<td colspan="4" align="right" style="padding-right:5px;"><font color="#000000"><B>합계&nbsp:&nbsp;</b></font><font color="#FF3C00"><b><?=number_format($totprice)?>원</b></font></td>
		</tr>
		<tr><td colspan="4" height="1" bgcolor="#DDDDDD"></td></tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<TR>
		<TD>추가비용/할인/적립내역</TD>
	</TR>
	<tr>
		<td height="2"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="90"></col>
		<col width="190"></col>
		<col width="60"></col>
		<col width="40"></col>
		<col></col>
		<tr>
			<td height="2" colspan="5" bgcolor="#000000"></td>
		</tr>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><font color="#333333"><b>항목</b></font></td>
			<td><font color="#333333"><b>내용</b></font></td>
			<td><font color="#333333"><b>금액</b></font></td>
			<td><font color="#333333"><b>적립금</b></font></td>
			<td><font color="#333333"><b>해당상품명</b></font></td>
		</tr>
<?
	$etcprice=0;
	$etcreserve=0;
	for($i=0;$i<count($etcdata);$i++) {
		echo "<tr><td colspan=\"5\" height=\"1\" bgcolor=\"#DDDDDD\"></td></tr>\n";
		if(ereg("^(COU)([0-9]{8})(X)$",$etcdata[$i]->productcode)) {				#쿠폰
			$etcprice+=$etcdata[$i]->price;
			$etcreserve+=$etcdata[$i]->reserve;
			echo "<tr height=\"25\">\n";
			echo "	<td align=\"center\"><b>쿠폰 사용</b></td>\n";
			echo "	<td style=\"padding-left:5px;word-break:break-all;\">".$etcdata[$i]->productname."</td>\n";
			echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#000000\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</font></td>\n";
			echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#FF3C00\"><b>".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</b></font></td>\n";
			echo "	<td style=\"padding-right:5px;word-break:break-all;\" align=\"right\"><font color=\"#000000\"><B>".$etcdata[$i]->order_prmsg."</b></font></td>\n";
			echo "</tr>\n";
		} else if(ereg("^(9999999999)([0-9]{1})(X)$",$etcdata[$i]->productcode)) {
			#99999999999X : 현금결제시 결제금액에서 추가적립/할인
			#99999999998X : 에스크로 결제시 수수료
			#99999999997X : 부가세(VAT)
			#99999999990X : 상품배송비
			
			if($etcdata[$i]->productcode=="99999999999X") {
				$etcprice+=$etcdata[$i]->price;
				$etcreserve+=$etcdata[$i]->reserve;
				echo "<tr height=\"25\">\n";
				echo "	<td align=\"center\"><b>결제 할인</b></td>\n";
				echo "	<td style=\"padding-left:5px;word-break:break-all;\">".$etcdata[$i]->productname."</td>\n";
				echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#000000\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</font></td>\n";
				echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#FF3C00\"><b>".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</b></font></td>\n";
				echo "	<td style=\"padding-right:5px;word-break:break-all;\" align=\"right\"><font color=\"#000000\"><B>주문서 전체적용</b></font></td>\n";
				echo "</tr>\n";
			} else if($etcdata[$i]->productcode=="99999999998X") {
				$etcprice+=$etcdata[$i]->price;
				$etcreserve+=$etcdata[$i]->reserve;
				echo "<tr height=\"25\">\n";
				echo "	<td align=\"center\"><b>결제 수수료</b></td>\n";
				echo "	<td style=\"padding-left:5px;word-break:break-all;\">".$etcdata[$i]->productname."</td>\n";
				echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#000000\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</font></td>\n";
				echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#FF3C00\"><b>".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</b></font></td>\n";
				echo "	<td style=\"padding-right:5px;word-break:break-all;\" align=\"right\"><font color=\"#000000\"><B>주문서 전체적용</b></font></td>\n";
				echo "</tr>\n";
			} else if($etcdata[$i]->productcode=="99999999990X") {
				echo "<tr height=\"25\">\n";
				echo "	<td align=\"center\"><b>배송료</b></td>\n";
				echo "	<td style=\"padding-left:5px;word-break:break-all;\">".$etcdata[$i]->productname."</td>\n";
				echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#000000\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</font></td>\n";
				echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#FF3C00\"><b>".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</b></font></td>\n";
				echo "	<td style=\"padding-right:5px;word-break:break-all;\" align=\"right\"><font color=\"#000000\"><B>".$etcdata[$i]->order_prmsg."</b></font></td>\n";
				echo "</tr>\n";
			} else if($etcdata[$i]->productcode=="99999999997X") {
				
				echo "<tr height=\"25\">\n";
				echo "	<td align=\"center\"><b>부가세(VAT)</b></td>\n";
				echo "	<td style=\"padding-left:5px;word-break:break-all;\">".$etcdata[$i]->productname."</td>\n";
				echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#000000\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</font></td>\n";
				echo "	<td align=\"right\" style=\"padding-right:5px;\"></td>\n";
				echo "	<td style=\"padding-right:5px;word-break:break-all;\" align=\"right\"><font color=\"#000000\"><B>주문서 전체 적용</b></font></td>\n";
				echo "</tr>\n";
				
			}
		}
	}
	
	$dc_price=(int)$_ord->dc_price;
	$salemoney=0;
	$salereserve=0;
	if($dc_price<>0) {
		if($dc_price>0) $salereserve=$dc_price;
		else $salemoney=-$dc_price;
		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
			$sql.= "WHERE a.id='".$_ord->id."' AND b.group_code=a.group_code AND MID(b.group_code,1,1)!='M' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$group_name=$row->group_name;
			}
			mysql_free_result($result);
		}
		echo "<tr><td colspan=\"5\" height=\"1\" bgcolor=\"#DDDDDD\"></td></tr>\n";
		echo "<tr height=\"25\">\n";
		echo "	<td align=\"center\"><b>그룹적립/할인</b></td>\n";
		echo "	<td style=\"padding-left:5px;\">그룹회원 적립/할인 : ".$group_name."</td>\n";
		echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#000000\">".($salemoney>0?"-".number_format($salemoney)."원":"&nbsp;")."</font></td>\n";
		echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#FF3C00\"><b>".($salereserve>0?"+ ".number_format($salereserve)."원":"&nbsp;")."</b></font></td>\n";
		echo "	<td style=\"padding-right:5px;\" align=\"right\"><font color=\"#000000\"><B>주문서 전체 적용</b></font></td>\n";
		echo "</tr>\n";
	}

	if($_ord->reserve>0) {
		echo "<tr><td colspan=\"5\" height=\"1\" bgcolor=\"#DDDDDD\"></td></tr>\n";
		echo "<tr height=\"25\">\n";
		echo "	<td align=\"center\"><b>적립금 사용</b></td>\n";
		echo "	<td style=\"padding-left:5px;\">결제시 적립금 ".number_format($_ord->reserve)."원 사용</td>\n";
		echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#000000\">-".number_format($_ord->reserve)."원</font></td>\n";
		echo "	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#FF3C00\"><b>&nbsp;</b></font></td>\n";
		echo "	<td style=\"padding-right:5px;\" align=\"right\"><font color=\"#000000\"><B>주문서 전체 적용</b></font></td>\n";
		echo "</tr>\n";
	}
	
	$totprice+=$_ord->deli_price-$salemoney-$_ord->reserve+$etcprice;
	$totreserve+=$salereserve+$etcreserve;

	echo "<tr><td colspan=\"5\" height=\"1\" bgcolor=\"#DDDDDD\"></td></tr>\n";
?>
		<tr height="26" bgcolor="#F9F9F9">
			<td colspan="5" align="right" style="padding-right:5px;"><font color="#000000"><B>합계&nbsp:&nbsp;</b></font><font color="#FF3C00"><b><?=number_format($_ord->deli_price-$salemoney-$_ord->reserve+$etcprice)?>원</b></font></td>
		</tr>
		<tr><td colspan="5" height="1" bgcolor="#DDDDDD"></td></tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td>


		<table cellpadding="0" cellspacing="6" width="100%" bgcolor=#F4F4F4>
		<tr>
			<td style="padding:10px;" bgcolor=#ffffff>


				<table cellpadding="0" cellspacing="0" width="100%">
				<col></col>
				<col width="100"></col>
				<tr height="30" >
					<td align="right" style="padding-right:5px;"><font color="#000000"><B>총 결제금액&nbsp:&nbsp;</b></font></td>
					<td align="right" style="padding-right:5px;"><font color="#FF3C00" style="font-size:12pt;"><b><?=number_format($_ord->price)?>원</b></font></td>
				</tr>

				<?if($totreserve>0 && substr($_ord->ordercode,-1)!="X") {?>

				<tr><td colspan="2" height="1" bgcolor="#DDDDDD"></td></tr>
				<tr height="30" >
					<td align="right" style="padding-right:5px;"><font color="#000000"><B>적립금액&nbsp:&nbsp;</b></font></td>
					<td align="right" style="padding-right:5px;"><font color="#0054A6">배송후 <b><?=number_format($totreserve)?>원</b></font></td>
				</tr>
				
				<?}?>
				</table>
			</td>
			</tr>
		</table>
			

		</td>
	</tr>
	<tr>
		<td height="30"></td>
	</tr>
	<TR>
		<TD><IMG src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/orderend_stext01.gif" border="0" vspace="3"></TD>
	</TR>
	<tr>
		<td height="2"></td>
	</tr>
	<TR>
		<TD>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td bgcolor=#DDDDDD height=2></td>
		</tr>
		<tr>
			<td style="padding:10px;">
<?
	if(preg_match("/^(V|C|P|M){1}/", $_ord->paymethod)) {
		$arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰");
		echo $arpm[substr($_ord->paymethod,0,1)];

		if ($_ord->pay_flag=="0000") {
			if(preg_match("/^(C|P){1}/", $_ord->paymethod)) {
				echo "&nbsp;결제성공 - 승인번호 : ".$_ord->pay_auth_no." ";
			} else {
				echo "&nbsp;결제가 <font color=blue>정상처리</font> 되었습니다.";
			}
		} else if(strlen($_ord->pay_flag)>0)
			echo "&nbsp;거래결과 : <font color=red><b><u>".$_ord->pay_data."</u></b></font>\n";
		else
			echo "&nbsp;\n<font color=red>(지불실패)</font>";

		if (preg_match("/^(C|P|M){1}/", $_ord->paymethod) && $_data->card_payfee>0) echo "<br>&nbsp\n".$arpm[substr($_ord->paymethod,0,1)]." 결제시 현금 할인가 적용이 안됩니다.";

	} else if (preg_match("/^(B|O|Q){1}/", $_ord->paymethod)) {
		if(preg_match("/^(B){1}/", $_ord->paymethod)) echo "무통장 입금 : <font color=#0054A6>".$_ord->pay_data."</font> <br>\n(입금확인후 배송이 됩니다.)";
		else {
			if($_ord->pay_flag=="0000") $msg = "&nbsp\n(입금확인후 배송이 됩니다.)";
			if(preg_match("/^(O){1}/", $_ord->paymethod)) echo "가상계좌 : <font color=#0054A6>".$_ord->pay_data."</font> <br>".$msg;
			else if(preg_match("/^(Q){1}/", $_ord->paymethod)) echo "매매보호 - 가상계좌 : <font color=#0054A6>".$_ord->pay_data."</font> <br>".$msg;
		}
	}
?>
			</td>
		</tr>
		<tr>
			<td bgcolor=#DDDDDD height=2></td>
		</tr>
		</table>
		</TD>
	</TR>
	<tr>
		<td height="20"></td>
	</tr>
	<TR>
		<TD><IMG src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/orderend_stext02.gif" border="0" vspace="3"></TD>
	</TR>
	<tr>
		<td height="2"></td>
	</tr>
	<TR>
		<TD>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td bgcolor=#DDDDDD height=2></td>
		</tr>
		<tr>
			<td style="padding:10px;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<col width="100"></col>
			<col></col>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>이름</b></font></td>
				<td><b><?=$_ord->sender_name?></b></td>
			</tr>
			<tr>
				<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>전화번호</b></font></td>
				<td><?=$_ord->sender_tel?></td>
			</tr>
			<tr>
				<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>이메일</b></font></td>
				<td><?=$_ord->sender_email?></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td bgcolor=#DDDDDD height=2></td>
		</tr>
		</table>
		</TD>
	</TR>
	<tr>
		<td height="30"></td>
	</tr>
	<TR>
		<TD><IMG src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/orderend_stext03.gif" border="0" vspace="3"></TD>
	</TR>
	<tr>
		<td height="2"></td>
	</tr>
	<TR>
		<TD>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td bgcolor=#DDDDDD height=2></td>
		</tr>
		<tr>
			<td style="padding:10px;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<col width="100"></col>
			<col></col>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>이름</b></font></td>
				<td><b><?=$_ord->receiver_name?></b></td>
			</tr>
			<tr>
				<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>전화번호</b></font></td>
				<td><?=$_ord->receiver_tel1?></td>
			</tr>
			<tr>
				<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>비상전화</b></font></td>
				<td><?=$_ord->receiver_tel2?></td>
			</tr>
			<tr>
				<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>주소</b></font></td>
				<td><?=$_ord->receiver_addr?></td>
			</tr>
			<tr>
				<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>주문요청사항</b></font></td>
				<td valign="top"><?=ereg_replace("\r\n","<br>",$_ord->order_msg)?></td>
			</tr>
<?
	for($i=0;$i<count($prdata);$i++) {
		if(strlen($prdata[$i]->order_prmsg)>0) {
			echo "<tr><td HEIGHT=\"10\" colspan=\"2\" background=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_line.gif\"></td></tr>";
			echo "<tr>\n";
			echo "	<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>주문메세지</b></font></td>\n";
			echo "	<td style=\"word-break:break-all;\">\n";
			echo "	<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> ".$prdata[$i]->productname."<BR>\n";
			echo nl2br($prdata[$i]->order_prmsg);
			echo "	</td>\n";
			echo "</tr>\n";
		}
	}
?>
			</table>
		</tr>
		<tr>
			<td bgcolor=#DDDDDD height=2></td>
		</tr>
		</table>
		</TD>
	</TR>
	<tr>
		<td height="30"></td>
	</tr>
	<tr>
		<td>
		
		</td>
	</tr>
	<TR>
		<TD height="10"></TD>
	</TR>
	<TR>
		<TD align="center"><a href="main.php" rel="external"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_orderend2.gif" border="0" hspace="10"></a></TD>
	</TR>
	<tr>
		<td height="40"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
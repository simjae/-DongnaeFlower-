<?
if($_data->sns_ok == "Y"){
	
	if(TWITTER_ID !="TWITTER_ID")
		echo "<input type=\"hidden\" name=\"tLoginBtnChk\" id=\"tLoginBtnChk\">";
	if(FACEBOOK_ID !="FACEBOOK_ID")
		echo "<input type=\"hidden\" name=\"fLoginBtnChk\" id=\"fLoginBtnChk\">";
	if(ME2DAY_ID !="ME2DAY_ID")
		echo "<input type=\"hidden\" name=\"mLoginBtnChk\" id=\"mLoginBtnChk\">";
}

if($_data->sns_ok == "Y" && $_pdata->sns_state == "Y") {

	$snsButton ="";
	//if($_data->recom_ok == "Y"){
	if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
		if(TWITTER_ID !="TWITTER_ID"){
			$snsButton .= "<td><INPUT type=\"checkbox\" name=\"send_chk\" id=\"send_chk_t\" value=\"t\" disabled><label for=\"send_chk_t\"><IMG SRC=\"../images/design/icon_twitter_off.gif\" width=\"17\" onclick=\"showDiv('snsSend');\" border=\"0\" id=\"tLoginBtn0\" style=\"cursor:pointer\" align=\"absmiddle\"></label></td>";
		}
		if(FACEBOOK_ID !="FACEBOOK_ID"){
			$snsButton .= "<td><INPUT type=\"checkbox\" name=\"send_chk\" id=\"send_chk_f\" value=\"f\" disabled><label for=\"send_chk_f\"> <IMG SRC=\"../images/design/icon_facebook_off.gif\" width=\"17\"  onclick=\"showDiv('snsSend');\" border=\"0\" id=\"fLoginBtn0\" style=\"cursor:pointer\" align=\"absmiddle\"></label></td>";
		}
		if(ME2DAY_ID !="ME2DAY_ID"){
			$snsButton .= "<td><INPUT type=\"checkbox\" name=\"send_chk\" id=\"send_chk_m\" value=\"m\" disabled><IMG SRC=\"../images/design/icon_me2day_off.gif\" width=\"17\"  border=\"0\" id=\"mLoginBtn0\" style=\"cursor:pointer\" align=\"absmiddle\"></td>";
		}
	}else{
		$snsButton .= "<td style=\"padding-right:3px;\"><a href=\"javascript:snsSendCheck('t');\"><IMG SRC=\"../images/design/icon_twitter_on.gif\" width=\"17\" border=\"0\" id=\"tLoginBtn0\"></a></td>";
		$snsButton .= "<td style=\"padding-right:3px;\"><a href=\"javascript:snsSendCheck('f');\"><IMG SRC=\"../images/design/icon_facebook_on.gif\" width=\"17\"  border=\"0\" id=\"fLoginBtn0\"></a></td>";
		$snsButton .= "<td><a href=\"javascript:snsSendCheck('m');\"><IMG SRC=\"../images/design/icon_me2day_on.gif\" width=\"17\"  border=\"0\" id=\"mLoginBtn0\"></a></td>";
	}
?>
									<table cellpadding="0" cellspacing="0" style="position:relative;">
										<tr>
											<?=$snsButton?>
											<td></td>
<?
	if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
?>
											<td><a href="#snsSepup" onclick="showDiv('snsSepup');"><img src="../images/design/icon_setup.gif" alt="sns자동연결설정" border="0" align="absmiddle"></a>
												<!--sns 자동연결 설정-->
												<div id="snsSepup" style="position:absolute;z-index:1000;background:#fff;left:35px;top:20px;visibility:hidden;">
												<table cellpadding="0" cellspacing="0" width="150">
												<tr>
													<td colspan="3"><IMG src="../images/design/speech_bubble_top.gif" width="150" height="7"></td>
												</tr>
												<tr>
													<td width="5" background="../images/design/speech_bubble_leftbg.gif"></td>
													<td width="140" class="table01_con">
														<table cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td class="speechbubble_title"><b>sns자동연결 설정</b></td>
															<td align="right" class="speechbubble_close"><a href="#snsSepup" onclick="showDiv('snsSepup');"><img src="../images/design/speech_bubble_close.gif"></a></td>
														</tr>
														<tr>
															<td colspan="2" height="10"><img src="../images/design/con_line02.gif" width="140" height="1"></td>
														</tr>
														<tr>
															<td colspan="2" class="speechbubble_con">
																<table cellpadding="0" cellspacing="0" width="100%">
																	<tr>
																		<td height=23><img src="../images/design/icon_sb_facebook_off.gif" id="fLoginBtn1"></td>
																		<td><a href="javascript:changeSnsInfo('f');"><img src="../images/design/btn_connection_off.gif" alt="" id="fLoginBtn2"></td>
																	</tr>
																	<tr>
																		<td height=23><img src="../images/design/icon_sb_twitter_off.gif"  id="tLoginBtn1"></td>
																		<td><a href="javascript:changeSnsInfo('t');"><img src="../images/design/btn_connection_off.gif" alt="" id="tLoginBtn2"></td>
																	</tr>
																	<tr>
																		<td height=23><img src="../images/design/icon_sb_me2day_off.gif" id="mLoginBtn1"></td>
																		<td><a href="javascript:changeSnsInfo('m');"><img src="../images/design/btn_connection_off.gif" alt="" id="mLoginBtn2"></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td colspan="2" height="10"><img src="../images/design/con_line02.gif" width="140" height="1"></td>
														</tr>
														<tr>
															<td colspan="2" class="speechbubble_con">버튼을 클릭하면 연결해제를 할수 있습니다.</td>
														</tr>
														</table>

													</td>
													<td width="5" background="../images/design/speech_bubble_rightbg.gif"></td>
												</tr>
												<tr>
													<td colspan="3"><IMG src="../images/design/speech_bubble_bottom.gif" width="150" height="7"></td>
												</tr>
												</table>
												</div>
												<!--sns 자동연결 설정-->
											</td>
											<td><a href="#snsHelp" onclick="showDiv('snsHelp');"><img src="../images/design/icon_help.gif" hspace="2" alt="도움말" border="0" align="absmiddle"></a>
												<!--sns보내기(도움말)-->
												<div id="snsHelp" style="position:absolute;z-index:1000;background:#fff;left:55px;top:20px;visibility:hidden;">
												<table cellpadding="0" cellspacing="0" width="150">
													<tr>
														<td colspan="3"><IMG src="../images/design/speech_bubble_top.gif" width="150" height="7"></td>
													</tr>
													<tr>
														<td width="5" background="../images/design/speech_bubble_leftbg.gif"></td>
														<td width="140">
															<table cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td class="speechbubble_title"><b>도움말</b></td>
																	<td align="right" class="speechbubble_close"><a href="#snsHelp" onclick="showDiv('snsHelp');"><img src="../images/design/speech_bubble_close.gif"></a></td>
																</tr>
																<tr>
																	<td colspan="2" class="speechbubble_con">해당 컨텐츠를 내 SNS로 보내 친구들과 공유해보세요.<br><font color="#F8752F">SNS 자동연결 설정 시 한번에 여러개의 SNS로 글을 올릴수 있습니다.</font></td>
																</tr>
															</table>
														</td>
														<td width="5" background="../images/design/speech_bubble_rightbg.gif"></td>
													</tr>
													<tr>
														<td colspan="3"><IMG src="../images/design/speech_bubble_bottom.gif" width="150" height="7"></td>
													</tr>
												</table>
												</div>
												<!--sns보내기(도움말)-->
											</td>
											<td><a href="#snsSend" onclick="showDiv('snsSend');"><img src="../images/design/icon_snssend.gif" border="0" align="absmiddle"></a>
												<!--sns 보내기-->
												<div id="snsSend" style="position:absolute;z-index:1000;background:#fff;left:0;top:-200px;visibility:hidden;">
												<table cellpadding="0" cellspacing="0" width="350">
													<tr>
														<td colspan="3"><IMG src="../images/design/speech_bubble_topa.gif" width="352" height="7"></td>
													</tr>
													<tr>
														<td width="5" background="../images/design/speech_bubble_leftbg.gif"></td>
														<td width="342" class="table01_con">


															<table cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td class="speechbubble_count"><b><font color="#F8752F" id="cmtByte">0</font></b>/80자</td>
																	<td align="right" class="speechbubble_close"><a href="#snsSend" onclick="showDiv('snsSend');"><img src="../images/design/speech_bubble_close.gif"></a></td>
																</tr>
																<tr>
																	<td class="speechbubble_con" colspan="2"><TEXTAREA rows="3" cols="50" name="comment0" id="comment0" class="textarea1" onChange="CheckStrLen('80',this,'top');" onKeyUp="CheckStrLen('80',this,'top');" onfocus="if(this.value == '내용을 입력하세요.' ){this.value='';}" onblur="if(this.value.length==0){this.value='내용을 입력하세요.'};">내용을 입력하세요.</TEXTAREA></td>
																</tr>
																<tr>
																	<td  align="center" colspan="2" style="padding-bottom:10px"><a href="#snsSend" id="m_snsSend" ><img src="../images/design/icon_snssend.gif"></a><a href="#snsSend" onclick="showDiv('snsSend');"><img src="../images/design/btn_cancel01.gif" hspace="4"></a></td>
																</tr>
															</table>

														</td>
														<td width="5" background="../images/design/speech_bubble_rightbg.gif"></td>
													</tr>
													<tr>
														<td colspan="3"><IMG src="../images/design/speech_bubble_bottoma.gif" width="352" height="7"></td>
													</tr>
												</table>
												</div>
											</td>
<?}?>
										</tr>
									</table>
<?
}
$sql="SELECT count(1) cnt FROM tblsmsinfo WHERE product_hongbo ='Y' limit 1 ";
$smsRs=mysql_query($sql,get_db_conn());
$rowsms=mysql_fetch_object($smsRs);
$smsChk = $rowsms->cnt;
mysql_free_result($smsRs);

// SMS 남은 카운트
$smsCount = smsCountValue ();

?>
								</td>
								<td></td>
								<td align="right">
<?
if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
	echo "<a href=\"../front/mail_send.php?pcode=".$productcode."\" onclick=\"window.open(this.href,'mailSend','width=420px,height=315px');return false;\">";
}else{
	echo "<a href=\"javascript:check_login();\" >";
}
?>
								<IMG SRC="../images/design/icon_email.gif" WIDTH=18 HEIGHT=18 ALT="" border="0" /></a>
<?
if($smsChk >0 AND $smsCount > 0){
	if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
		echo "<a href=\"../front/smssendFrm.php?pcode=".$productcode."\" onclick=\"window.open(this.href,'smsSendWin','width=420px,height=335px');return false;\">";
	}else{
		echo "<a href=\"javascript:check_login();\" >";
	}
?>
								<IMG SRC="../images/design/icon_phone.gif" WIDTH=18 HEIGHT=18 ALT="" hspace="2"></a>
<?}?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
<?
if($odrChk &&($_pdata->present_state == "Y" || $_pdata->pester_state == "Y"))
{
?>
				<tr>
					<td height="20"></td>
				</tr>
				<tr>
					<td align=right >
					<?if($_pdata->pester_state == "Y"){?><a href="javascript:CheckForm('<?=(eregi("S",$_cdata->type))? "pester":""?>','<?=$opti?>')"><img src="<?=$Dir?>images/design/productdetail_pester.gif" border="0"></a><?}?>
					<?if($_pdata->present_state == "Y"){?><a href="javascript:CheckForm('<?=(eregi("S",$_cdata->type))? "present":""?>','<?=$opti?>')"><img src="<?=$Dir?>images/design/productdetail_present.gif" border="0"></a><?}?></td>
				</tr>
<?
}
?>
</table>
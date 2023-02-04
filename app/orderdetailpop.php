<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/order_func.php");
require("./paygate/E_SMART/lib/NicepayLite.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

function getDeligbn($strdeli,$true=true) {
	global $_ShopInfo, $ordercode, $arrdeli;
	if(!is_array($arrdeli)) {
		$sql = "SELECT deli_gbn FROM tblorderproduct WHERE ordercode='".$ordercode."' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		$sql.= "GROUP BY deli_gbn ";
		$result=mysql_query($sql,get_db_conn());
		$arrdeli=array();
		while($row=mysql_fetch_object($result)) {
			$arrdeli[]=$row->deli_gbn;
		}
		mysql_free_result($result);
	}

	$res=true;
	for($i=0;$i<count($arrdeli);$i++) {
		if($true==true) {
			if(!preg_match("/^(".$strdeli.")$/", $arrdeli[$i])) {
				$res=false;
				break;
			}
		} else {
			if(preg_match("/^(".$strdeli.")$/", $arrdeli[$i])) {
				$res=false;
				break;
			}
		}
	}
	return $res;
}

$ordercode=$_REQUEST["ordercode"];	//로그인한 회원이 조회시
$ordername=$_REQUEST["ordername"]; //비회원 조회시 주문자명
$ordercodeid=$_REQUEST["ordercodeid"];	//비회원 조회시 주문번호 6자리
$print=$_REQUEST["print"];	//OK일 경우 프린트

if(strlen($ordercodeid)>0 && strlen($ordercodeid)!=6) {
	echo "<html><head><title></title></head><body onload=\"alert('주문번호 6자리를 정확히 입력하시기 바랍니다.');window.close();\"></body></html>";exit;
}

$sql = "SELECT gift FROM tblorderinfo WHERE ordercode='{$ordercode}'";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_array($result);

$row["gift"] = '0';

if($row["gift"]=='1'|| $row["gift"]=='2') {
	echo "<script>window.location.href='orderdetailpop2.php?ordercode={$ordercode}';</script>";
	exit;
}

$gift_type=explode("|",$_data->gift_type);

$type=$_REQUEST["type"];
$tempkey=$_REQUEST["tempkey"];
$rescode=$_REQUEST["rescode"];

####### 에스크로 구매결정 #######
if ($type=="okescrow" && strlen($ordercode)>0 && $rescode=="Y") {
	$sql = "UPDATE tblorderinfo SET escrow_result='Y' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$sql.= "AND (MID(paymethod,1,1)='Q' OR MID(paymethod,1,1)='P') ";
	$sql.= "AND deli_gbn='Y' ";
	$result = mysql_query($sql,get_db_conn());

	echo "<script>alert('구매결정 되었습니다.');self.close();</script>";
	exit;
}


####### 주문취소 (에스크로 포함) #######
if ($type=="cancel" || ($type=="okescrow" && $rescode=="C" && strlen($ordercode)>0)) { //매매보호 주문거절시
	$charset = "utf-8";

	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
	if($type=="cancel") $sql.= "AND tempkey='".$tempkey."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		
		$deliAfterCancelYN = TRUE;
		$closedate = date("Ymd", strtotime(substr($row->deli_date,0,8)." +11 days"));	// 주문일자 + 11일
		if ($row->deli_gbn == "Y" && date("Ymd") >= $closedate) {
			// 주문일자
			$deliAfterCancelYN = FALSE;
		}
		if (
		(preg_match("/^(Q|P){1}/", $row->paymethod) && !preg_match("/^(C|D|E|H)$/", $row->deli_gbn) && getDeligbn("C|D|E|H",false))
		|| ($_data->ordercancel=="3" && (($row->deli_gbn=="S" || $row->deli_gbn=="N" || $row->deli_gbn=="Y") && getDeligbn("N|S|Y",true)) && $deliAfterCancelYN) // 배송 후 7일 이내
		|| ($_data->ordercancel=="0" && ($row->deli_gbn=="S" || $row->deli_gbn=="N") && getDeligbn("N|S",true)) // 발송완료 전 :: tblorderproduct에 deli_gbn이 "S|N"만 있는지 확인한다.
		|| ($_data->ordercancel=="2" && $row->deli_gbn=="N" && getDeligbn("N",true)) // 상품준비 전 :: tblorderproduct에 deli_gbn이 "N"만 있는지 확인한다.
		|| ($_data->ordercancel=="1" && $row->paymethod=="B" && strlen($row->bank_date)<12 && $row->deli_gbn=="N" && getDeligbn("N",true)) // 결제 완료 전
		) {

			$bankYN = false;
			$refundResultCode = "";
			if ($row->paymethod=="B" && strlen($row->bank_date) >= 12) {
				$bankYN = true;
				$refundResultCode = "2001";
			}
			//무통장 결제가 아니면 나이스페이 취소
			else{
				$nicepay                  = new NicepayLite;
				$nicepay->m_NicepayHome   = "./paygate/E_SMART/log";               // 로그 디렉토리 설정
				$nicepay->m_ActionType    = "CLO";                  // ActionType
				$nicepay->m_charSet       = "UTF8";                 // 인코딩
				$nicepay->m_ssl           = "true";                 // 보안접속 여부
			    $nicepay->m_CancelAmt  = $row->price;                   // 취소 금액
			    $nicepay->m_CancelPwd   = "dongne202";               // 결제 취소 패스워드 설정  
				$nicepay->m_TID           = $row->pay_auth_no;                  // TID
				$nicepay->m_PartialCancelCode = "0";
				$nicepay->m_CancelMsg		="사용자 취소";
				
				$nicepay->startAction();
				$refundResultCode = $nicepay->m_ResultData["ResultCode"];
			}

			if ($refundResultCode=="2001" || $refundResultCode=="2211") {
				$deliok = "D";	// 취소요청
				$status = "";	// 환불요청.
				if ($_data->ordercancel=="1") {
					if ($bankYN) {
						$deliok = "D";
						$status = "RA";
					}
					else{
						$deliok = "C";
						$status = "C";
					}
				} else if ($_data->ordercancel=="0" || $_data->ordercancel=="2") {

					if ($bankYN) {
						$status = "RA";
					}
					else{
						$deliok = "C";
						$status = "C";
					}
				} else if ($_data->ordercancel=="3") {
					$deliok = "D";

					if ($bankYN) {
						$status = "RA";
					}
					else{
						$deliok = "C";
						$status = "C";
					}
					if ($bankYN && $row->deli_gbn == "Y") {
						$deliok = "TB";
					}
				}

				if($_REQUEST['bank_name'] != "" ){//환불 계좌 정보가 있을때
					$bankAccountInfo = "<br>환불계좌정보 : ".$_REQUEST['bank_name']." ".$_REQUEST['bank_num']. "(예금주:". $_REQUEST['bank_owner'].")";
					$banksql = ", pay_data = CONCAT(pay_data,'".$bankAccountInfo."') ";
				}
				//결제사 자동취소인 경우 반환금
				$realprice = "";
				if (!$bankYN) {
					$realprice =", realprice = ". ($row->price * -1);
				}
				$sql = "UPDATE tblorderinfo SET deli_gbn='".$deliok."'".$banksql.$realprice." WHERE ordercode='".$ordercode."' ";
				if($type=="cancel") $sql.= "AND tempkey='".$tempkey."' ";
				//echo $sql;exit;
				if(mysql_query($sql,get_db_conn())) {
					$sql = "UPDATE tblorderproduct SET deli_gbn='".$deliok."' ";
					if ($status != "") {
						$sql .= ", status=IF(productcode = '99999990GIFT','','".$status."')";
					}
					$sql.= "WHERE ordercode='".$ordercode."' ";
					$sql.= "AND (productcode NOT LIKE 'COU%' AND productcode NOT IN ('99999999990X','99999999995X')) ";
					$sql.= "AND status != 'RC' ";
					mysql_query($sql,get_db_conn());

					/////////////// 주문취소시 관리자에게 메일을 발송
					$maildata=$row->sender_name."고객님이 <font color=blue>".date("Y")."년 ".date("m")."월 ".date("d")."일</font>에 아래와 같이 주문을 취소하셨습니다.<br><br>";
					$maildata.="<li> 취소된 주문의 번호 : $ordercode<br><br>";
					$maildata.="취소된 주문은 관리자메뉴의 주문조회에서 확인하실 수 있습니다.";

					if (strlen($_data->shopname)>0) $mailshopname = "=?".$charset."?B?".base64_encode($_data->shopname)."?=";
					$header=getMailHeader($mailshopname,$_data->info_email);
					// 아웃룩 등에서 메일 제목 깨지는 것 관련 해서 처리
					$subject = '=?utf-8?B?'.base64_encode(strtr($_data->shopname." 주문취소 확인 메일입니다.","\r\n",'  ')).'?=';
					if(ismail($_data->info_email)) {
						sendmail($_data->info_email, $subject, $maildata, $header);
					}

					if(strlen($_data->okcancel_msg)==0)  $_data->okcancel_msg="정상적으로 주문이 취소요청 되었습니다!";
					if (preg_match("/^(Q){1}/", $row->paymethod) && strlen($row->bank_date)>=12) $_data->okcancel_msg.=" 최종적으로 상점에서 취소 후 환불처리됩니다.";
					if (preg_match("/^(P){1}/", $row->paymethod) && $row->pay_flag=="0000") $_data->okcancel_msg.=" 최종적으로 상점에서 취소 후 카드취소처리됩니다.";

					$sqlsms = "SELECT * FROM tblsmsinfo WHERE admin_cancel='Y' ";
					$resultsms= mysql_query($sqlsms,get_db_conn());
					if($rowsms=mysql_fetch_object($resultsms)) {
						if(strlen($ordercode)>0) {
							$sms_id=$rowsms->id;
							$sms_authkey=$rowsms->authkey;

							$pr_cancel_msg = $rowsms->pr_cancel_msg;
							$pattern = array("(\[NAME\])","(\[DATE\])");
							$replace = array($row->sender_name, substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2));
							$pr_cancel_msg=preg_replace($pattern, $replace, $pr_cancel_msg);
							$pr_cancel_msg=addslashes($pr_cancel_msg);

							$totellist=$rowsms->admin_tel;
							if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
							if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
							if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
							$fromtel=$rowsms->return_tel;

							//$smsmsg=$row->sender_name."님께서 ".substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2)."에 주문하신 주문을 취소하셨습니다.";
							$etcmsg="주문취소 메세지(관리자)";
							if($rowsms->sleep_time1!=$rowsms->sleep_time2) {
								$date="0";
								$time = date("Hi");
								if($rowsms->sleep_time2<"12" && $time<=substr("0".$rowsms->sleep_time2,-2)."59") $time+=2400;
								if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

								if($time<substr("0".$rowsms->sleep_time1,-2)."00" || $time>=substr("0".$rowsms->sleep_time2,-2)."59"){
									if($time<substr("0".$rowsms->sleep_time1,-2)."00") $day = date("d");
									else $day=date("d")+1;
									$date = date("Y-m-d H:i:s",mktime($rowsms->sleep_time1,0,0,date("m"),$day,date("Y")));
								}
							}
							$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $pr_cancel_msg, $etcmsg);
						}
					}
					mysql_free_result($resultsms);
					$onload="<script>alert('".$_data->okcancel_msg."');</script>";
				} else {
					$onload="<script>alert('요청하신 작업중 오류가 발생하였습니다.');</script>";
				}
			}
			else {
				$onload="<script>alert('결제사 취소처리중 오류가 발생하였습니다.');</script>";
			}
		} else if (preg_match("/^(Q|P){1}/", $row->paymethod) && preg_match("/^(D)$/", $row->deli_gbn)) {
			$onload="<script>alert('최종적으로 상점에서 취소 후 환불처리됩니다.');</script>";
		} else if($_data->ordercancel==0) {
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"이미 배송된 상품이 있습니다. 쇼핑몰로 연락주시기 바랍니다.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		} else if($_data->ordercancel==2) {
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"상품준비가 완료되어 택배회사에 전달된 상품이 있습니다. 쇼핑몰로 연락주시기 바랍니다.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		} else if($_data->ordercancel==3) {
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"상품 수령 후 7일 이내에만 교환/반품/환불이 가능합니다. 쇼핑몰로 연락주시기 바랍니다.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		} else {
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"결제대금의 환불/취소는 쇼핑몰로 연락주시기 바랍니다.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		}
	}
}

####### 주문서 삭제 #######
if($type=="delete" && strlen($ordercode)>0 && strlen($tempkey)>0) {
	$sql = "SELECT del_gbn FROM tblorderinfo WHERE ordercode='".$ordercode."' AND tempkey='".$tempkey."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	$del_gbn = $row->del_gbn;
	if($del_gbn=="N" || $del_gbn==NULL) $okdel="Y";
	else if($del_gbn=="A") $okdel="R";
	else {
		echo "<html><head><title></title></head><body onload=\"alert('해당 주문서는 이미 삭제처리가 되었습니다.');window.close();opener.location.reload();\"></body></html>";exit;
	}

	$sql = "UPDATE tblorderinfo SET del_gbn='".$okdel."' WHERE ordercode='".$ordercode."' AND tempkey='".$tempkey."' ";
	mysql_query($sql,get_db_conn());
	echo "<html><head><title></title></head><body onload=\"alert('해당 주문서를 삭제처리 하였습니다.');window.close();opener.location.reload();\"></body></html>";exit;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>주문내역 상세 조회</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=UTF8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="format-detection" content="telephone=no" />
<link rel="stylesheet" href="./css/common.css" />
<link rel="stylesheet" href="./css/skin/default.css" />
<SCRIPT LANGUAGE="JavaScript">
<!--
window.moveTo(10,10);
window.resizeTo(800,650);
window.name="orderpop";

function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function DeliSearch(url){
	window.open(url,'배송조회','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=550,height=500');
}

function view_product(productcode) {
	opener.location.href="<?=$Dir?>m/productdetail.php?productcode="+productcode;
}

function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function order_cancel(tempkey,ordercode,bankdate) {	//주문취소
	//alert("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다");
	if (confirm("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다")) {
		if(bankdate != "") {
			//document.getElementById("refundAccount").style.display="block";
			if(document.refundAccountForm.bank_name.value == "" || document.refundAccountForm.bank_owner.value == "" || document.refundAccountForm.bank_num.value == "") {
				alert("환불계좌 정보를 입력하세요.");
				document.refundAccountForm.bank_name.focus();
				return;
			}
			document.form1.bank_name.value=document.refundAccountForm.bank_name.value;
			document.form1.bank_owner.value=document.refundAccountForm.bank_owner.value;
			document.form1.bank_num.value=document.refundAccountForm.bank_num.value;
		}
		document.form1.tempkey.value=tempkey;
		document.form1.ordercode.value=ordercode;
		document.form1.type.value="cancel";
		document.form1.submit();
	}
}
function order_del(tempkey,ordercode) {	//주문서 삭제
	if(confirm("주문건에 대해서 취소는 되지 않고, 조회만 불가능합니다.\n\n주문서 내용만 삭제하시겠습니까?")) {
		document.form1.tempkey.value=tempkey;
		document.form1.ordercode.value=ordercode;
		document.form1.type.value="delete";
		document.form1.submit();
	}
}

function get_taxsave(ordercode) {	//현금영수증 요청
	window.open("about:blank","taxsavepop","width=400,height=500,scrollbars=no");
	document.taxsaveform.ordercode.value=ordercode;
	document.taxsaveform.submit();
}

function setPackageShow(packageid) {
	if(packageid.length>0 && document.getElementById(packageid)) {
		if(document.getElementById(packageid).style.display=="none") {
			document.getElementById(packageid).style.display="";
		} else {
			document.getElementById(packageid).style.display="none";
		}
	}
}

// 전자세금계산서 관련
function sendBillPop(ordercode) {
	document.billform.ordercode.value=ordercode;
	window.open("about:blank","billpop","width=610,height=500,scrollbars=yes");
	document.billform.submit();
}
function sendBill(ordercode) {
	document.billsendfrm.ordercode.value=ordercode;
	document.billsendfrm.submit();
}
function viewBill(bidx){
	document.billviewFrm.b_idx.value= bidx;
	window.open("","winBill","scrollbars=yes,width=700,height=600");
	document.billviewFrm.submit();
}

function rDeliUpdate2(cnt){
	f = eval("document.reForm2_"+cnt);
	if(!f.deli_com.value) {
		alert("배송업체를 선택하세요");
		f.deli_com.focus();
		return false;
	}
	if(!f.deli_num.value) {
		alert("송장번호를  입력하세요");
		f.deli_num.focus();
		return false;
	}

	f.type.value = "deli";
	f.action = "order_oks.php";
	f.submit();

}

function rDeliUpdate(cnt){
	f = eval("document.reForm_"+cnt);
	if(!f.deli_com.value) {
		alert("배송업체를 선택하세요");
		f.deli_com.focus();
		return false;
	}
	if(!f.deli_num.value) {
		alert("송장번호를  입력하세요");
		f.deli_num.focus();
		return false;
	}

	f.type.value = "deli";
	f.action = "order_oks.php";
	f.submit();

}


function order_one_cancel(ordercode, productcode, can, tempkey,uid) {
	
	if (can=="yes") {
		if (confirm("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다")) {
		window.open("<?=$Dir?>m/order_one_cancel_pop.php?ordercode="+ordercode+"&productcode="+productcode+"&uid="+uid,"one_cancel","width=610,height=500,scrollbars=yes");
		}
	}else{
		if (confirm("입금확인중 주문은 '전체취소'만 가능합니다. \n전체취소를 원하시는 경우 구매를 원하는 상품을 다시 주문해주세요.\n이주문을 지금 주문 전체취소하시겠습니까?")) {
			if(bankdate != "") {
				document.getElementById("refundAccount").style.display="block";
				if(document.refundAccountForm.bank_name.value == "" || document.refundAccountForm.bank_owner.value == "" || document.refundAccountForm.bank_num.value == "") {
					alert("환불계좌 정보를 입력하세요.");
					document.refundAccountForm.bank_name.focus();
					return;
				}
				document.form1.bank_name.value=document.refundAccountForm.bank_name.value;
				document.form1.bank_owner.value=document.refundAccountForm.bank_owner.value;
				document.form1.bank_num.value=document.refundAccountForm.bank_num.value;
			}
			document.form1.tempkey.value=tempkey;
			document.form1.ordercode.value=ordercode;
			document.form1.type.value="cancel";
			document.form1.submit();
		}
	}
}

//-->
</SCRIPT>
</head>
<!--디자인 ui개선 20180724-->
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<?=$onload?>
<div class="orderdetailwrap">
	<h1>
		나의 주문조회
	</h1>
	<!-- 주문내역이 없는 경우 START -->
	<?
		if (strlen($ordercodeid)>0 && strlen($ordername)>0) {	//비회원 주문조회
			$curdate = date("Ymd",mktime(0,0,0,date("m"),date("d")-90,date("Y")))."00000";
			$sql = "SELECT * FROM tblorderinfo WHERE ordercode > '".$curdate."' AND id LIKE 'X".$ordercodeid."%' ";
			$sql.= "AND sender_name='".$ordername."' ";
			$result=mysql_query($sql,get_db_conn());
			
			if($row=mysql_fetch_object($result)) {
				$_ord=$row;
				$ordercode=$row->ordercode;
				$gift_price=$row->price-$row->deli_price;
			} else {
	?>
				<div class="orderdetail_nodata">
					<p class="nodata_msg_top">조회하신 주문내역이 없습니다.</p>
					<p class="nodata_msg_bottom">비회원 주문건의 경우 90일 경과시 상점에 문의바랍니다.</p>
					<button type="button" onClick="window.close();">닫기</button>
				</div>
	<?
			}
			mysql_free_result($result);
		} else {
			$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$_ord=$row;
				$gift_price=$row->price-$row->deli_price;
			} else {
	?>
				<div class="orderdetail_nodata">
					<p class="nodata_msg_top">조회하신 주문내역이 없습니다.</p>
					<!-- <button class="nodata_close" type="button" onClick="window.close();">닫기</button> -->
				</div>
	<?
			}
		mysql_free_result($result);
		}
	?>
	<!-- 주문내역이 없는 경우 END -->

	<!-- 주문내역 안내메시지 START -->
	<?if (strlen($ordercodeid)>0 && strlen($ordername)>0) {?>
	<div class="orderdetail_infomsg">
		<FONT COLOR="#EE1A02"><B><?=$_ord->sender_name?></B></FONT>님께서 <FONT COLOR="#111682"><?=substr($_ord->ordercode,0,4)?>년 <?=substr($_ord->ordercode,4,2)?>월 <?=substr($_ord->ordercode,6,2)?>일</FONT> 주문한 내역입니다.
	</div>
	<?}?>
	<!-- 주문 내역 안내메시지 END -->
	<?
		if (strlen($ordercodeid)>0 && $_ord->deli_gbn=="Y") {
			/* 전자세금계산서 발행 설정 체크 */
			$sql = "SELECT COUNT(*) as cnt FROM tblshopbillinfo where bill_state ='Y' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$shopBill = (int)$row->cnt;
			mysql_free_result($result);
			if($shopBill>0){
				include_once($Dir."lib/cfg.php");
				$SBinfo = new Shop_Billinfo();
				$HB = new Hiworks_Bill( $SBinfo->domain, $SBinfo->license_id, $SBinfo->license_no, $SBinfo->partner_id );
				$sql = "SELECT COUNT(*) as cnt FROM tblmemcompany WHERE memid='".$_ord->ordercode."' ";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$companyinfo = (int)$row->cnt;
				mysql_free_result($result);

				$sql3 = "SELECT COUNT(*) as cnt, document_id, b_idx  FROM tblorderbill WHERE ordercode='".$_ord->ordercode."' ";
				$result3=mysql_query($sql3,get_db_conn());
				$row3=mysql_fetch_object($result3);
				$billcnt = (int)$row3->cnt;
				$document_id = $row3->document_id ;
				$b_idx = $row3->b_idx ;
				mysql_free_result($result3);
				echo "<span style=\"float:right\">";
				if($billcnt>0){//신청
					$HB->set_document_id($document_id);
					$documet_result_array = $HB->check_document( HB_SOAPSERVER_URL );
					echo "<A HREF=\"javascript:viewBill('".$b_idx."')\">".$document_status[$documet_result_array[0]["now_state"]]."</a>";
				}else{
					if($companyinfo>0){
						echo "<input type='button' value='세금계산서 신청' onclick=\"sendBill('".$_ord->ordercode."')\" onmouseover=\"window.status='세금계산서 신청';return true;\" onmouseout=\"window.status='';return true;\" style='cursor:pointer;'>";
					}else{
						echo "<A HREF=\"javascript:sendBillPop('".$_ord->ordercode."')\" onmouseover=\"window.status='세금계산서 신청';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/mypage_detailview.gif\" border=\"0\"></A>";
					}
				}
				echo "</span>";
			}
		}
	?>

	<div class="orderdetail_ct">
	<!-- 주문정보 START -->
		<div class="orderdetail_prwrap">
			<h2>주문정보</h2>
			<div class="orderdetail_pr_list">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
					<tbody>
						<?if(strlen($ordercode)==21 && substr($ordercode,-1)=="X"){?>
						<tr>
							<th>주문확인번호</th>
							<td><?=substr($_ord->id,1,6)?></td>
						</tr>
						<?}?>
						<tr>
							<th>주문번호</th>
							<td><?=$ordercode?></td>
						</tr>
						<tr>
							<th>주문일자</th>
							<td><?=substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)?></td>
						</tr>
						<tr>
							<th>결제방법</th>
							<td>
								<?
									if (preg_match("/^(B|O|Q){1}/",$_ord->paymethod)) {	//무통장, 가상계좌, 가상계좌 에스크로
										if($_ord->paymethod=="B") echo "<span class=\"font-orange\">무통장입금</span>\n";
										else if(substr($_ord->paymethod,0,1)=="O") echo "<span class=\"font-orange\">가상계좌</span>\n";
										else echo "매매보호 - 가상계좌";

										if(!preg_match("/^(C|D)$/",$_ord->deli_gbn) || $_ord->paymethod=="B") echo "[ ".$_ord->pay_data." ]";
										else echo "[ 계좌 취소 ]";

										if (strlen($_ord->bank_date)>=12) {
											echo "</td>\n</tr>\n";
											echo "<tr>\n";
											echo "	<th>입금확인</th>\n";
											echo "	<td>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")";
										} else if(strlen($_ord->bank_date)==9) {
											echo "</td>\n</tr>\n";
											echo "<tr>\n";
											echo "	<th>입금확인</th>\n";
											echo "	<td>환불";
										}
									} else if(substr($_ord->paymethod,0,1)=="M") {	//핸드폰 결제
										echo "핸드폰 결제[ ";
										if ($_ord->pay_flag=="0000") {
											if($_ord->pay_admin_proc=="C") echo "[ <span class=font-orange>결제취소 완료</span>]";
											else echo "<span class=font-orange>결제가 성공적으로 이루어졌습니다.</span>";
										}
										else echo "결제가 실패되었습니다.";
										echo " ]";
									} else if(substr($_ord->paymethod,0,1)=="P") {	//매매보호 신용카드
										echo "매매보호 - 신용카드";
										if($_ord->pay_flag=="0000") {
											if($_ord->pay_admin_proc=="C") echo "[ <span class=font-orange>카드결제 취소완료</span>]";
											else if($_ord->pay_admin_proc=="Y") echo "[ 카드 결제 완료 * 감사합니다. : 승인번호 ".$_ord->pay_auth_no."]";
										}
										else echo "[ ".$_ord->pay_data."]";
									} else if (substr($_ord->paymethod,0,1)=="C") {	//일반신용카드
										echo "<span class=\"font-orange\">신용카드</span>\n";
										if($_ord->pay_flag=="0000") {
											if($_ord->pay_admin_proc=="C") echo "[ <span class=font-orange>카드결제 취소완료</span>]";
											else if($_ord->pay_admin_proc=="Y") echo "[ 카드 결제 완료 * 감사합니다. : 승인번호 ".$_ord->pay_auth_no."]";
										}
										else echo "[ ".$_ord->pay_data."]";
									} else if (substr($_ord->paymethod,0,1)=="V") {
										echo "실시간 계좌이체 : ";
										if ($_ord->pay_flag=="0000") {
											if($_ord->pay_admin_proc=="C") echo "[ <span class=font-orange> [환불]</span>]";
											else echo "<span class=font-orange>".$_ord->pay_data."</span>";
										}
										else echo "결제가 실패되었습니다.";
									}
								?>
							</td>
						</tr>
						<tr>
							<th>결제금액</th>
							<td><span class="font-orange bold"><?=number_format($_ord->price)."원</span>".($_ord->reserve>0?"(적립금 ".number_format($_ord->reserve)."원 공제)":"")?></td>
						</tr>
						<?
							$order_msg=explode("[MEMO]",$_ord->order_msg);
							if(strlen($order_msg[0])>0) {
						?>
						<tr>
							<th>고객메모</th>
							<td><?=nl2br($order_msg[0])?></td>
						</tr>
						<?}?>
						<?if(strlen($order_msg[2])>0) {?>
						<tr>
							<th>상점메모</th>
							<td><?=nl2br($order_msg[2])?></td>
						</tr>
						<?}?>
						<?if( preg_match("/^(B){1}/", $_ord->paymethod) && strlen($_ord->bank_date)==14 && $_ord->deli_gbn=="N" && getDeligbn("N",true)){//무통장 입금 완료 , 미처리 상태 일때 출력
						?>
						<tr>
							<th>환불계좌</th>
							<td style="padding:0px;">
								<form name="refundAccountForm">
									<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_refund">
										<tr>
											<th>은행명</th>
											<td><input type="text" name="bank_name" maxlength="30" style="width:90%; border: 1px solid #ebebeb;" /></td>
										</tr>
										<tr>
											<th>예금주</th>
											<td><input type="text" name="bank_owner" maxlength="4" style="width:90%; border: 1px solid #ebebeb;" /></td>
										</tr>
										<tr>
											<th>계좌번호</th>
											<td><input type="text" name="bank_num" style="width:90%; border: 1px solid #ebebeb;" /></td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
							<?}?>
					</tbody>
				</table>
			</div>
		</div>
	<!-- 주문정보 END -->

	<!-- 주문 내역 SATART -->
		<div class="orderdetail_prwrap">
			<h2>주문상품 정보</h2>
			<div class="orderdetail_pr_list">
				<ul>
					<?
						$delicomlist=getDeliCompany();
						$orderproducts = getOrderProduct($row->ordercode);

						$cnt=0;
						$gift_check="N";
						$taxsaveprname="";
						$etcdata=array();
						$giftdata=array();
						$in_reserve=0;

						foreach($orderproducts as $row) {
							
							if (substr($row->productcode,0,3)=="999" || substr($row->productcode,0,3)=="COU") {
				
								if ($gift_check=="N" && strpos($row->productcode,"GIFT")!==false) $gift_check="Y";
								
								$etcdata[]=$row;

								if(strpos($row->productcode,"GIFT")!==false) {
									$giftdata[]=$row;
								}

								continue;
							}
							$gift_tempkey=$row->tempkey;
							$taxsaveprname.=$row->productname.",";
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

							if($row->status!='RC') $in_reserve+=$row->quantity*$row->reserve;
					?>
						<li>
							<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
								<tbody>
									<tr>
										<td colspan="2" class="orderdetail_pr_name">
											<!--<div style="float:left; width:60px; text-align:center;"><img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->minimage)?>" border=0 width=40 height=40 /></div>-->
											<b>상품명 : <?=$row->productname?></b><br />
											<?
												//옵션 텍스트 가져오기 2016-10-12 Seul
												$ordprd_comtext = $optClass->getOrdprdOptComtext($row->ordprd_optidx);

												if(strlen($ordprd_comtext)>0) {
													echo $ordprd_comtext;
												}
											?>
										</td>
									</tr>
									<tr>
										<th>상품금액</th>
										<td><span class="orderdetail_pr_price"><?=number_format($row->sumprice)?>원</span></td>
									</tr>
									<tr>
										<th>수량</th>
										<td><?=$row->quantity?>개</td>
									</tr>
									<tr>
										<th>배달주소</th>
										<td><?=ereg_replace("주소 :","<br>주소 :",$row->receiver_addr)?></td>
									</tr>
									<tr>
										<th>받는사람</th>
										<td><?=$row->receiver_name?></td>
									</tr>
									<tr>
										<th>주문상태</th>
										<td>
											<?if(strlen($row->order_prmsg)>0) {?>
											<p><?=nl2br(strip_tags($row->order_prmsg))?></p>
											<?}?>
											<p><?=orderProductDeliStatusStr($row,$_ord)?></p>
										</td>
									</tr>
									<tr>
										<th>배송상태</th>
										<td>
											<?
												$deli_url="";
												$trans_num="";
												$company_name="";
												if($row->deli_gbn=="Y") {
													if($row->deli_com>0 && $delicomlist[$row->deli_com]) {
														$deli_url=$delicomlist[$row->deli_com]->deli_url;
														$trans_num=$delicomlist[$row->deli_com]->trans_num;
														$company_name=$delicomlist[$row->deli_com]->company_name;
														//echo $company_name."<br>";
														echo "<div style=\"float:left; height:20px; line-height:20px;\">".$company_name."</div>";
														if(strlen($row->deli_num)>0 && strlen($deli_url)>0) {
															if(strlen($trans_num)>0) {
																$arrtransnum=explode(",",$trans_num);
																$pattern=array("(\[1\])","(\[2\])","(\[3\])","(\[4\])");
																$replace=array(substr($row->deli_num,0,$arrtransnum[0]),substr($row->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
																$deli_url=preg_replace($pattern,$replace,$deli_url);
															} else {
																$deli_url.=$row->deli_num;
															}
															echo "<div style=\"float:right; margin-right:6px;\"><A HREF=\"javascript:DeliSearch('".$deli_url."')\" class=\"button white small\">배송추적</A></div>";
														}
													} else {
														echo "-";
													}
												} else {
													echo "-";
												}
											?>
										</td>
									</tr>
								</tbody>
							</table>
						</li>
					<?
						}
					?>
				</ul>
			</div>
		</div>
	<!-- 주문 내역 END -->
	
	<!-- 사은품 내역 START -->
	<?if(count($giftdata)>0){?>
		<div class="orderdetail_prwrap">
			<h2>사은품 내역</h2>
			<div class="orderdetail_pr_list">
				<ul>
					<?for($i=0;$i<count($giftdata);$i++) {?>
					<li>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
							<thead>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>사은품명 : <?=$giftdata[$i]->productname?></b></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>선택사항1</th>
									<td><?=$giftdata[$i]->opt1_name?></td>
								</tr>
								<tr>
									<th>선택사항2</th>
									<td><?=$giftdata[$i]->opt2_name?></td>
								</tr>
								<tr>
									<th>선택사항3</th>
									<td><?=$giftdata[$i]->opt3_name?></td>
								</tr>
								<tr>
									<th>선택사항4</th>
									<td><?=$giftdata[$i]->opt4_name?></td>
								</tr>
								<tr>
									<th>수량</th>
									<td><?=$giftdata[$i]->quantity?></td>
								</tr>
								<tr>
									<th>요청사항</th>
									<td><?=$giftdata[$i]->assemble_info?></td>
								</tr>
							</tbody>
						</table>
					<li>
					<?}?>
				<ul>
			</div>
		</div>
	<?}?>
	<!-- 사은품 내역 END -->

	<!-- 추가 내역 START -->
	<? $etcdata = getOrderAddtional($row->ordercode); ?>
		<div class="orderdetail_prwrap">
			<h2>추가비용/할인/적립내역</h2>
			<div class="orderdetail_pr_list">
				<ul>
					<?for($i=0;$i<count($etcdata);$i++) {?>
					<li>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
							<tbody>
						<?
							$in_reserve+=$etcdata[$i]->reserve;
							if(ereg("^(COU)([0-9]{8,10})(X)$",$etcdata[$i]->productcode)) {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>항목 : 쿠폰사용</b></td>
								</tr>
								<tr>
									<th>내용</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>금액</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>적립액</th>
									<td><?=number_format($etcdata[$i]->reserve)?>원</td>
								</tr>
								<?}?>
								<tr>
									<th>해당 상품명</th>
									<td><?=$etcdata[$i]->order_prmsg?></td>
								</tr>
						<?
							}else if(ereg("^(9999999999)([0-9]{1})(X)$",$etcdata[$i]->productcode)){
								if($etcdata[$i]->productcode=="99999999999X") {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>항목 : 결제할인</b></td>
								</tr>
								<tr>
									<th>내용</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>금액</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>적립액</th>
									<td><?=number_format($etcdata[$i]->reserve)?>원</td>
								</tr>
								<?}?>
								<tr>
									<th>해당 상품명</th>
									<td>주문서 전체 적용</td>
								</tr>
						<?
								} else if($etcdata[$i]->productcode=="99999999998X") {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>항목 : 결제 수수료</b></td>
								</tr>
								<tr>
									<th>내용</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>금액</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>적립액</th>
									<td><?=number_format($etcdata[$i]->reserve)?>원</td>
								</tr>
								<?}?>
								<tr>
									<th>해당 상품명</th>
									<td>주문서 전체 적용</td>
								</tr>
						<?
								} else if($etcdata[$i]->productcode=="99999999990X") {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>항목 : 배송료</b></td>
								</tr>
								<tr>
									<th>내용</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>금액</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>적립액</th>
									<td><?=number_format($etcdata[$i]->reserve)?>원</td>
								</tr>
								<?}?>
								<tr>
									<th>해당 상품명</th>
									<td><?=$etcdata[$i]->order_prmsg?></td>
								</tr>
						<?
								} else if($etcdata[$i]->productcode=="99999999997X") {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>항목 : 부가세(VAT)</b></td>
								</tr>
								<tr>
									<th>내용</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>금액</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>적립액</th>
									<td><?=number_format($etcdata[$i]->reserve)?>원</td>
								</tr>
								<?}?>
								<tr>
									<th>해당 상품명</th>
									<td>주문서 전체적용</td>
								</tr>
						<?
								}
							}
						?>
							</tbody>
						</table>
					</li>
					<?
						} //end for
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
					?>
					<li>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
							<tbody>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>항목 : 그룹적립 / 할인</b></td>
								</tr>
								<tr>
									<th>내용</th>
									<td>그룹회원 적립/할인<?=$group_name?></td>
								</tr>
								<tr>
									<th>금액</th>
									<td><?=($salemoney>0?"-".number_format($salemoney)."원":"&nbsp;")?></td>
								</tr>
								<?if($salereserve>0){?>
								<tr>
									<th>적립액</th>
									<td><?=($salereserve>0?"+ ".number_format($salereserve)."원":"&nbsp;")?></td>
								</tr>
								<?}?>
								<tr>
									<th>해당 상품명</th>
									<td>주문서 전체적용</td>
								</tr>
							</tbody>
						</table>
					</li>

					<?
						}
						if($_ord->reserve>0) {
					?>
					<li>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
							<tbody>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>항목 : 적립금 사용</b></td>
								</tr>
								<tr>
									<th>내용</th>
									<td>결제시 적립금 <?=number_format($_ord->reserve)?>원 사용</td>
								</tr>
								<tr>
									<th>금액</th>
									<td>-<?=number_format($_ord->reserve)?>원</td>
								</tr>
								<tr>
									<th>해당 상품명</th>
									<td>주문서 전체적용</td>
								</tr>
								<?
									$sql = "SELECT * FROM part_cancel_reserve WHERE ordercode='".$ordercode."' order by reg_date asc";
									$result=mysql_query($sql,get_db_conn());

									while( $row=mysql_fetch_object($result)) {
								?>
									<tr>
										<th>항목</th>
										<td>적립금 환원</td>
									</tr>
									<tr>
										<th>내용</th>
										<td>적립금 <?=number_format($row->cancel_reserve)?>원 환원</td>
									</tr>
									<tr>
										<th>적립액</th>
										<td><?=number_format($row->cancel_reserve)?>원</td>
									</tr>
								<? } ?>
							</tbody>
						</table>
					</li>
					<? } ?>
				</ul>
			</div>
		</div>
	<!-- 추가내역 END -->

	<!-- 부분 취소 내역 -->
	<?
	$sql = "select * from tblorderproduct where ordercode='".$_ord->ordercode."' and tempkey='".$_ord->tempkey."' and productcode='99999999995X' order by opt1_name asc";
	//echo $sql;
	$pcancleitems = array();
	if(false !== $cres = mysql_query($sql,get_db_conn())){
		while($citem = mysql_fetch_assoc($cres)){
			array_push($pcancleitems, $citem);
		}
	}
	if(count($pcancleitems)){
	?>
		<div class="orderdetail_prwrap">
			<h2>부분 취소 내역</h2>
			<div class="orderdetail_pr_list">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
					<tbody>
						<?
							$sumcancle = 0;
							for($i=0;$i<count($pcancleitems);$i++) {
								$citem = $pcancleitems[$i];
								$sumcancle +=abs($citem['price']);
						?>
						<tr>
							<td colspan="2" class="orderdetail_pr_name"><b>항목 : <?=$citem['productname'].' #'.$citem['opt1_name']?></b></td>
						</tr>
						<tr>
							<th>금액</th>
							<td><?=number_format(abs($citem['price']))?>원</td>
						</tr>
						<tr>
							<th>일자</th>
							<td><? echo substr($citem['date'],0,4).'-'.substr($citem['date'],5,2).'-'.substr($citem['date'],7,2); ?></td>
						</tr>
						<?}?>
					</tbody>
				</table>
				취소 금액 합계 : <span><?=number_format($sumcancle)?></span>
			</div>
		</div>
	<?}?>
	</div>
	<!-- 부분 취소 내역 END -->

	<div style="text-align:center; margin:15px 0px;">
		<A HREF="javascript:parent.PopupClose();" class="basic_button grayLineBtn btnPaddingRadius">닫기</A>
		<?
		if($print!="OK") {
			$deliAfterCancelYN = TRUE;
			$closedate = date("Ymd", strtotime(substr($_ord->deli_date,0,8)." +11 days"));	// 주문일자 + 11일
			if ($_ord->deli_gbn == "Y" && date("Ymd") >= $closedate) {
				// 주문일자
				$deliAfterCancelYN = FALSE;
			}

			if (
			($_data->ordercancel==3 && (($_ord->deli_gbn=="S" || $_ord->deli_gbn=="N" || $_ord->deli_gbn=="Y") && getDeligbn("N|S|Y",true)) && $deliAfterCancelYN) // 배송 후 7일 이내
			|| ($_data->ordercancel==0 && ($_ord->deli_gbn=="S" || $_ord->deli_gbn=="N") && getDeligbn("S|N",true))/*주문배송완료 전에 취소가 가능하며 상품준비된 주문서 또는 미처리된 주문서일 경우 가능*/
			|| ($_data->ordercancel==2 && $_ord->deli_gbn=="N" && getDeligbn("N",true))/*주문배송준비 전에만 취소가 가능하며 미처리된 주문서일 경우 가능*/
			|| ($_data->ordercancel==1 && preg_match("/^(B){1}/", $_ord->paymethod) && strlen($_ord->bank_date)<12 && $_ord->deli_gbn=="N" && getDeligbn("N",true)) /*주문결제완료 전에만 취소가 가능하며 무통장입금으로 입금전 미처리된 주문서일 경우 가능*/
			) {
				if(!preg_match("/^(Q){1}/", $_ord->paymethod)) {
					echo "<a href=\"javascript:order_cancel('".$_ord->tempkey."', '".$_ord->ordercode."','".$_ord->bank_date."')\" onMouseOver=\"window.status='주문취소';return true;\" class=\"basic_button\">전체주문취소</a>\n";
				}
			} else if($_data->ordercancel==1 && (($_ord->paymethod=="B" && strlen($_ord->bank_date)>=12) || ( preg_match("/^(C|P){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && getDeligbn("N",true)){
				if(strlen($_data->nocancel_msg)==0) $_data->nocancel_msg="주문취소가 되지 않습니다.\\n쇼핑몰에 문의하세요.";
				echo "<a href=\"javascript:alert('".$_data->nocancel_msg."')\"><img src=\"".$Dir."images/common/orderdetailpop_ordercancel.gif\" align=absmiddle border=0></a>\n";
			}

			if($_ord->del_gbn!="A" && $_ord->del_gbn!="Y" && getDeligbn("A|Y",false)
			&& !(substr($_ord->paymethod,0,1)=="Q" && strlen($_ord->bank_date)>=12 && $_ord->deli_gbn!="C")  //매매보호 가상계좌이고 입금확인되고 주문취소가 아닌경우
			&& !(substr($_ord->paymethod,0,1)=="P" && $_ord->pay_flag=="0000" && $_ord->deli_gbn!="C")      //매매보호 신용카드이고 카드성공 주문취소가 아닌경우
			&& strlen($_ShopInfo->getMemid())>0 /* 비회원은 내용삭제안되게 */) {
				echo "<a href=\"javascript:order_del('".$_ord->tempkey."', '".$_ord->ordercode."')\" onMouseOver=\"window.status='내용삭제';return true;\" class=\"basic_button grayLineBtn btnPaddingRadius\">내용삭제</a>\n";
			}

			/*
			if(preg_match("/^(B|O|Q){1}/", $_ord->paymethod) && $_ord->deli_gbn!="C") {
				if($_data->tax_type!="N" && $_ord->price>=1) {
					echo "<a href=\"javascript:get_taxsave('".$_ord->ordercode."')\" onMouseOver=\"window.status='현금영수증';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_cashbill.gif\" align=absmiddle border=0></a>\n";
				}
			}
			*/


			// 기존 현금 영수증 발행 관련 기능에 추가 하여 세금 계산서 영역 처리
			if($_data->tax_type!="N" && $_ord->price>=1) {
				if(preg_match("/^(B|O|Q){1}/", $_ord->paymethod) && $_ord->deli_gbn!="C"){
					$reqItem = '';
					if(false !== $cres = mysql_query("select count(*) from tbltaxsavelist WHERE ordercode='".$ordercode."'",get_db_conn())){
						if(mysql_result($cres,0,0)) $reqItem = 'taxsave';
					}
					if( !_empty($reqItem) && $_ord->deli_gbn == 'Y'){
						if(false !== $cres = mysql_query("select bill_idx from bill_basic WHERE ordercode='".$ordercode."'",get_db_conn())){
							if(mysql_num_rows($cres)){
								$reqItem = 'bill';
								$bill_idx = mysql_result($cres,0,0);
							}
						}
					}
					if($reqItem != 'bill'){
						echo "<a href=\"javascript:get_taxsave('".$_ord->ordercode."')\" onMouseOver=\"window.status='현금영수증';return true;\" class=\"button white small\">현금영수증신청</A>\n";
					}
					if($reqItem != 'taxsave'  && $_ord->deli_gbn == 'Y'){
						if(_isInt($bill_idx)){
							echo "<A HREF=\"javascript:viewBill('".$bill_idx."')\" onmouseover=\"window.status='세금계산서 신청';return true;\" onmouseout=\"window.status='';return true;\" class=\"button white small\">세금계산서신청</A>";

						}else{
							echo "<A HREF=\"javascript:sendBillPop('".$_ord->ordercode."')\" onmouseover=\"window.status='세금계산서 신청';return true;\" onmouseout=\"window.status='';return true;\" class=\"button white small\">세금계산서신청</A>";
						}
					}
				}
			}


			if(((substr($_ord->paymethod,0,1)=="P" && $_ord->pay_admin_proc=="Y") || (substr($_ord->paymethod,0,1)=="Q" && $_ord->pay_flag=="0000")) && !preg_match("/^(Y|C)$/",$_ord->escrow_result) && $_ord->deli_gbn!="C") {
				/*
				에스크로 정보를 가지고 온다.
				*/
				$pgid_info="";
				$pg_type="";
				switch (substr($_ord->paymethod,0,1)) {
					case "B":
						break;
					case "V":
						$pgid_info=GetEscrowType($_data->trans_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "O":
						$pgid_info=GetEscrowType($_data->virtual_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "Q":
						$pgid_info=GetEscrowType($_data->escrow_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "C":
						$pgid_info=GetEscrowType($_data->card_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "P":
						$pgid_info=GetEscrowType($_data->card_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "M":
						$pgid_info=GetEscrowType($_data->mobile_id);
						$pg_type=$pgid_info["PG"];
						break;
				}
				$pg_type=trim($pg_type);

				// 배송처리가 되어야만 매매보호
				if ($_ord->deli_gbn=="Y") {
					echo "<a href=\"javascript:escrow_ok('".$_ord->ordercode."')\" onMouseOver=\"window.status='구매확인';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_okorder.gif\" align=absmiddle border=0></a>\n";
				} else if (substr($_ord->paymethod,0,1)=="Q" && !preg_match("/^(D|E|H)$/", $_ord->deli_gbn) && getDeligbn("D|E|H",false)) {
					#<!--- 취소 ( 취소 & 환불 한꺼번에 처리) -->
					echo "<a href=\"javascript:escrow_cancel('".$_ord->tempkey."','".$_ord->ordercode."','".$_ord->bank_date."')\" onMouseOver=\"window.status='구매취소';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_ordercancel.gif\" align=absmiddle border=0></a>\n";
				}
			}

			// ######### 사은품을 선택하지 않은 주문의 경우 사은품을 선택할 수 있도록 해줌
			if (($_ord->paymethod=="B" || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && getDeligbn("N",true) && $gift_check=="N" && $gift_type[3]=="Y") {
				if ($gift_type[2]=="A" || strlen($gift_type[2])==0 || ($gift_type[2]=="B" && $_ord->paymethod=="B")) {
					if (($gift_type[0]=="M" && strlen($_ShopInfo->getMemid())>0) || $gift_type[0]=="C") { // 회원전용, 비회원+회원
						$sql = "SELECT COUNT(*) as gift_cnt FROM tblgiftinfo ";
						if($gift_type[1]=="N") {
							$sql.= "WHERE gift_startprice<=".$gift_price." AND gift_endprice>".$gift_price." ";
						} else  {
							$sql.= "WHERE gift_startprice<=".$gift_price." ";
						}
						$sql.= "AND (gift_quantity is NULL OR gift_quantity>0) ";
						$result=mysql_query($sql,get_db_conn());
						$row=mysql_fetch_object($result);
						$gift_cnt=$row->gift_cnt;
						mysql_free_result($result);
						if ($gift_cnt>0) {
							$gift_body = "<a href=\"javascript:getGift()\"><img src='".$Dir."images/common/orderdetailpop_gift.gif' border=0 align=absmiddle></a>\n";
							$gift_body.= "<form name=giftform method=post action=\"".$Dir.FrontDir."gift_choice.php\" target=\"gift_popwin\">\n";
							$gift_body.= "<input type=hidden name=gift_price value=\"".$gift_price."\">\n";
							$gift_body.= "<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">\n";
							$gift_body.= "<input type=hidden name=gift_mode value=\"orderdetailpop\">\n";
							$gift_body.= "<input type=hidden name=gift_tempkey value=\"".$gift_tempkey."\">\n";
							$gift_body.= "</form>\n";
							$gift_body.= "<script language='javascript'>\n";
							$gift_body.= "function getGift() {\n";
							$gift_body.= "	gift_popwin = window.open('about:blank','gift_popwin','width=700,height=600,scrollbars=yes');\n";
							$gift_body.= "	document.giftform.target='gift_popwin';\n";
							$gift_body.= "	document.giftform.submit();\n";
							$gift_body.= "	gift_popwin.focus();\n";
							$gift_body.= "}\n";
							$gift_body.= "</script>\n";
							echo $gift_body;
						}
					}
				}
			}
		}
?>
	</div>

<form name=form1 action="orderdetailpop.php" method=post>
	<input type=hidden name=tempkey>
	<input type=hidden name=ordercode>
	<input type=hidden name=type>
	<input type=hidden name=ordercodeid value="<?=$ordercodeid?>">
	<input type=hidden name=ordername value="<?=$ordername?>">
	<input type=hidden name=bank_name value="">
	<input type=hidden name=bank_owner value="">
	<input type=hidden name=bank_num value="">
</form>
<form name=taxsaveform method=post action="<?=$Dir?>m/taxsave.php" target=taxsavepop>
	<input type=hidden name=ordercode>
	<input type=hidden name=productname value="<?=urlencode(titleCut(30,htmlspecialchars(strip_tags($taxsaveprname),ENT_QUOTES)))?>">
</form>
<form name=escrowform action="<?=$Dir?>paygate/okescrow.php" method=post>
	<input type=hidden name=ordercode value="">
	<?if($pg_type=="D") {?>
	<input type=hidden name=sendtype value="">
	<? } else { ?>
	<input type=hidden name=sitecd value="<?=urlencode($pgid_info["ID"])?>">
	<input type=hidden name=sitekey value="<?=urlencode($pgid_info["KEY"])?>">
	<? } ?>
	<input type=hidden name=return_host value="<?=urlencode(getenv("HTTP_HOST"))?>">
	<input type=hidden name=return_script value="<?=urlencode(str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."app/orderdetailpop.php")?>">
	<input type=hidden name=return_data value="<?=urlencode("type=okescrow&ordercode=".$ordercode)?>">
</form>

<form name=vform action="<?=$Dir?>paygate/set_bank_account.php" method=post target="baccountpop">
	<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<form name=form3 method=post>
	<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<form name=billform method=post action="<?=$Dir.FrontDir?>orderbillpop.php" target="billpop">
	<input type=hidden name=ordercode>
</form>

<form name=billsendfrm action="orderbillsend.php" method=post target="hiddenFrame">
	<input type=hidden name="ordercode">
	<input type=hidden name="member" value="<?=(strlen($_ShopInfo->getMemid())==0)? "guest":$_ShopInfo->getMemid()?>">
</form>

<iframe id="hiddenFrame" name="hiddenFrame" style="width:0;height:0; position:absolute; visibility:hidden;"></iframe>

<form name=billviewFrm method=post action="orderbillview.php" target="winBill">
	<input type=hidden name=b_idx value="">
</form>

</body>
</html>
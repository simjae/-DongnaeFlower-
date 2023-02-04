<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/shopdata.php");

//솔루션타입 확인
//$shopconfig = shopconfig();

if(strlen($_REQUEST["PayMethod"]) > 0){
	$ordercode=$_REQUEST["Moid"];
	if(strlen($_REQUEST["ResultCode"])>0 && $_REQUEST["ResultCode"] == "3001"){
		$deli_gbn="N";
	}else{
		$deli_gbn="C";
	}
}else{
	if(strlen($ordercode)==0) $ordercode=$_REQUEST["ordercode"];
	$deli_gbn=$_REQUEST["deli_gbn"];
}


if(strlen($ordercode)>0) {

	mysql_query("INSERT INTO tblorderinfo SELECT * FROM tblorderinfotemp WHERE ordercode='".$ordercode."'",get_db_conn());
	mysql_query("INSERT INTO tblorderproduct SELECT * FROM tblorderproducttemp WHERE ordercode='".$ordercode."'",get_db_conn());
	mysql_query("INSERT INTO tblorderoption SELECT * FROM tblorderoptiontemp WHERE ordercode='".$ordercode."'",get_db_conn());

	mysql_query("DELETE FROM tblorderinfotemp WHERE ordercode='".$ordercode."'",get_db_conn());
	mysql_query("DELETE FROM tblorderproducttemp WHERE ordercode='".$ordercode."'",get_db_conn());
	mysql_query("DELETE FROM tblorderoptiontemp WHERE ordercode='".$ordercode."'",get_db_conn());

	snsPromoteAccessOrder( $_ShopInfo->authkey, $ordercode );

	/* 장바구니 비우기 */
	$sql = sprintf("SELECT basketidx FROM tblorderproduct WHERE ordercode = '%s'", $ordercode);
	$result = mysql_query($sql,get_db_conn());
	while($row = mysql_fetch_object($result)) {
		if ($ordertype == "ordernow") {
			$del_tbl = "tblbasket_ordernow";
		}else if($ordertype == "orderquick"){
			$del_tbl = "tblbasket_quick";
		} elseif ($ordertype == "") {
			$del_tbl = "tblbasket";
		}
		$del_sql = sprintf("DELETE FROM %s WHERE basketidx='%s'", $del_tbl, $row->basketidx);
		mysql_query($del_sql,get_db_conn());
	}
	mysql_free_result($result);

	$oldtempkey=$_ShopInfo->getTempkey();
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
	$_ShopInfo->setGifttempkey($oldtempkey);
	$_ShopInfo->setOldtempkey($oldtempkey);
	$_ShopInfo->setOkpayment("");
	$_ShopInfo->Save();

	//$okmail="YES";
}


$sql="SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$paymethod=substr($row->paymethod,0,1);
	$pay_flag=$row->pay_flag;
	$pay_flag_check=$row->pay_flag;
	$pay_auth_no=$row->pay_auth_no;
	$bank_date=$row->bank_date;
	$deli_flag=$row->deli_gbn;
	$user_reserve=$row->reserve;
	$last_price=$row->price;
	$pay_data=$row->pay_data;
	$delflag=$row->del_gbn;
	$sender_name=$row->sender_name;
	$sender_email=$row->sender_email;
	$sender_tel=$row->sender_tel;
	//선물 메일발송관련추가
	$order_type = $row->order_type;
	$receiver_name = $row->receiver_name;
	$receiver_email=$row->receiver_email;
	$receiver_tel1=$row->receiver_tel1;
	$receiver_message=$row->receiver_message;
}
mysql_free_result($result);

//exit( $paymethod." / ".$pay_flag." / ".$okmail );

if (preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && $deli_gbn=="C") {
	$pay_data = "결제 중 주문취소";
}

if(preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && $last_price>0) {
	if(strlen($_ShopInfo->getOkpayment())==0) {
		$_ShopInfo->setOkpayment("result");
		$_ShopInfo->Save();
		$_ShopInfo->setOkpayment("");
	}
}

//카드실패시 장바구니 복구
if (preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && $pay_flag!="0000") {
	mysql_query("UPDATE tblbasket SET tempkey='".$_ShopInfo->getTempkey()."' WHERE tempkey='".$_ShopInfo->getGifttempkey()."'",get_db_conn());
} else {
	mysql_query("DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getGifttempkey()."'",get_db_conn());
}

//새로고침 방지
if ($paymethod!="B" && $_ShopInfo->getOkpayment()=="result") {
	echo "<html></head><body onload=\"location.href='/app/main.php'\"></body></html>";
	exit;
}



//결제성공 처리
if (($paymethod=="B" || (preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")==0)) ) { //&& $okmail!="YES"

	//상품 구매 완료 시간 등록
	$orderproductSQL = "SELECT `vender`,`productcode` FROM `tblorderproduct` WHERE `ordercode` = '".$ordercode."' ";
	$orderproductResult=mysql_query($orderproductSQL,get_db_conn());
	$venderProductList = array();
	while($orderproductROW=mysql_fetch_object($orderproductResult)) {
		if( $orderproductROW->vender > 0 ) {
			${$orderproductROW->vender}++;
			$venderProductList[ $orderproductROW->vender ] = ${$orderproductROW->vender};
		}
		
		//결제성공시에만 재고처리하도록 수정한다.  -> payresult
		// 2021-10-14 김성식
		// 상품 DB 수량 수정 시작 -----------------------------------------------------------------------
	
		$selldateSQL= "UPDATE 
							tblproduct 
						SET
							selldate = NOW()
							,sellcount = sellcount+1
							,quantity = quantity-1
						WHERE productcode = '".$orderproductROW->productcode."' LIMIT 1; ";
		mysql_query($selldateSQL,get_db_conn());
	}


	//  쿠폰 사용
	if( strlen($_ShopInfo->getMemid()) > 0 ) {
		$couponOrderSql = "SELECT productcode FROM tblorderproduct WHERE ordercode='".$ordercode."' AND productcode LIKE 'COU%' ";
		$couponOrderResult=mysql_query($couponOrderSql,get_db_conn());
		while($couponOrderRow=mysql_fetch_object($couponOrderResult)){

			$coupon_code=substr($couponOrderRow->productcode,3,-2);

			$couponSql = "SELECT repeat_ok FROM tblcouponinfo WHERE coupon_code='".$coupon_code."' ";
			$couponResult = mysql_query($couponSql,get_db_conn());
			$couponRow=mysql_fetch_object($couponResult);

			if ( $couponRow->repeat_ok == 'Y' ) {
				$couponUsedState = "N";
			} else {
				$couponUsedState = "Y";
			}
			mysql_query("UPDATE tblcouponissue SET used='".$couponUsedState."' WHERE id='".$_ShopInfo->getMemid()."' AND coupon_code='".$coupon_code."' LIMIT 1;",get_db_conn());
		}
	}


	$thankmsg="<hr size=1 width=100%>\n";
	if (strlen($_data->orderend_msg)>0) {
		$orderend_msg=ereg_replace("\n","<br>",$orderend_msg);
		$thankmsg.="<table cellpadding=0 cellspacing=0 border=0 width=100%>\n";
		$thankmsg.="<tr><td align=center>\n";
		$thankmsg.=ereg_replace("\"","",$orderend_msg);
		$thankmsg.="</td></tr>\n";
		$thankmsg.="</table>\n";
	} else {
		$thankmsg.="<br><h3>구매해주셔서 감사합니다!</h3><br>";
	}

	if (strlen($sender_email)>0) $oksendmail="Y"; //메일이 있으면 주문메일 발송
	if (strlen($_data->info_email)>0) $okadminmail="Y"; //쇼핑몰 메일이 있으면 해당 주문내역서를 발송

	//관리자/입점업체/고객 주문완료 메일 발송
	SendOrderMail($_data->shopname, $_ShopInfo->getShopurl(), $_data->design_mail, $_data->info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg, "");
	$arpay=array("B"=>"현금","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

	//주문완료시 회원/관리자/입금내역을 sms로 발송함
	$sqlsms = "SELECT * FROM tblsmsinfo LIMIT 1 ; ";
	/*
	WHERE (mem_order='Y' OR admin_order='Y' ";
	if($paymethod=="B") $sqlsms.="OR mem_bank='Y' ";
	if($order_type=="p") $sqlsms.="OR mem_present='Y' ";
	$sqlsms.=")";
	*/
	$resultsms= mysql_query($sqlsms,get_db_conn());
	if($rowsms=mysql_fetch_object($resultsms)){
		if(strlen($ordercode)>0) {
			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;

			$admin_order=$rowsms->admin_order;
			$vender_order=$rowsms->vender_order;
			$mem_order=$rowsms->mem_order;
			$mem_bank=$rowsms->mem_bank;
			$totellist=$rowsms->admin_tel;
			if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
			if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
			if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
			$fromtel=$rowsms->return_tel;

			$msg_mem_order=$rowsms->msg_mem_order;
			$msg_mem_bank=$rowsms->msg_mem_bank;

			$mem_present=$rowsms->mem_present;
			$msg_mem_present=$rowsms->msg_mem_present;
			$use_mms =($rowsms->use_mms=='Y')? $rowsms->use_mms:"";

			$pr_buy_msg = $rowsms->pr_buy_msg;

			if(strlen($msg_mem_bank)==0) $msg_mem_bank="[NAME]님! [PRICE]원 [ACCOUNT] 입금바랍니다. [".$_data->shopname."]";
			if(strlen($pr_buy_msg)==0) $pr_buy_msg="[NAME]님이 [PRODUCT]를 [PAYTYPE] 구입하셨습니다.";
			$patten=array("(\[NAME\])","(\[PRODUCT\])");
			$replace=array($sender_name,substr($smsproductname,1));
			$msg_mem_order=preg_replace($patten,$replace,$msg_mem_order);
			$msg_mem_order=AddSlashes($msg_mem_order);
			//$smsmsg=$sender_name."님이 ".substr($smsproductname,1)."를 ".$arpay[$paymethod]." 구입하셨습니다.";
			$patten=array("(\[NAME\])","(\[PRICE\])","(\[ACCOUNT\])");
			$replace=array($sender_name,number_format($last_price),$pay_data);
			$msg_mem_bank=preg_replace($patten,$replace,$msg_mem_bank);

			$patten=array("(\[NAME\])","(\[PRODUCT\])","(\[PAYTYPE\])");
			$replace=array($sender_name,substr($smsproductname,1), $arpay[$paymethod]);
			$smsmsg=preg_replace($patten,$replace,$pr_buy_msg);
			$smsmsg=addslashes($smsmsg);
			mysql_free_result($resultsms);
			$etcmsg="상품주문 안내메세지(회원)";
			$date="0";
			if($mem_order=="Y") {
				$temp=SendSMS($sms_id, $sms_authkey, $sender_tel, "", $fromtel, $date, $msg_mem_order, $etcmsg);
			}
			$etcmsg="무통장입금 안내메세지(회원)";
			if(preg_match("/^(B|O|Q)$/", $paymethod) && $mem_bank=="Y") {
				$temp=SendSMS($sms_id, $sms_authkey, $sender_tel, "", $fromtel, $date, $msg_mem_bank, $etcmsg);
			}
			$etcmsg="상품주문 안내메세지(관리자)";
			if($admin_order=="Y" && $rowsms->sleep_time1!=$rowsms->sleep_time2){
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

			if($admin_order=="Y") {
				$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
			}

			$etcmsg="상품주문 안내메세지(입점업체)";
			if($vender_order=="Y") {
				//exit(_pr($venderProductList));
				foreach ( $venderProductList as $key => $no ) {
					$venderInfo = mysql_fetch_object( mysql_query( "SELECT `p_mobile` FROM `tblvenderinfo` WHERE `vender` = ".$key." LIMIT 1; " ) );
					$vender_product;
					
					for($ii=0; $ii<count($vender_smsproductname[$key]); $ii++){
						if($ii==0)
							$vender_product = $vender_smsproductname[$key][$ii];
						else
							$vender_product .= ", " .$vender_smsproductname[$key][$ii];}
					$replace=array($sender_name,$vender_product,$arpay[$paymethod]);
					$vender_smsmsg=preg_replace($patten,$replace,$pr_buy_msg);
					$vender_smsmsg=addslashes($vender_smsmsg);
					if( strlen( $venderInfo->p_mobile ) > 0 ) {
						$temp=SendSMS($sms_id, $sms_authkey, $venderInfo->p_mobile, "", $fromtel, $date, $vender_smsmsg, $etcmsg);
					}
				}
			}
		}
	}

	if(preg_match("/^(O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")==0){
		//선물하기 메일보내기
		if($order_type == "p"){
			$cnt = 1;
			while($cnt > 0){
				$tmpcode = rand(10000,999999);
				$sql = "SELECT count(1) cnt FROM tblpresentcode WHERE code='".$tmpcode."'";
				$result = mysql_query($sql,get_db_conn());
				if($row = mysql_fetch_object($result)) {
					$cnt = (int)$row->cnt;
				}
				mysql_free_result($result);
			}
			$sql = "INSERT tblpresentcode SET code = '".$tmpcode."', ordercode = '".$ordercode."'";
			$insert=mysql_query($sql,get_db_conn());
			if (mysql_errno()==0) {
				if(strlen($receiver_email)>0 && strlen($receiver_message)>0){
					SendPresentMail($_data->shopname, $_data->shopurl, $_data->design_mail, $receiver_message, $sender_email, $sender_name, $receiver_email, $receiver_name, $tmpcode);
				}
				if($mem_present == "Y"){
					if(strlen($msg_mem_present)==0) $msg_mem_present="[".strip_tags($_shopdata->shopname)."] [URL] [NAME]님이 선물하셨습니다.";
					$patten=array("(\[URL\])","(\[NAME\])");
					$replace=array("http://".$_ShopInfo->getShopurl()."?gft_cd=".$tmpcode,$sender_name);
					$msg_mem_present=preg_replace($patten,$replace,$msg_mem_present);
					$msg_mem_present=addslashes($msg_mem_present);

					$date=0;
					$etcmsg="선물하기메세지(회원)";
					$temp=SendSMS2($sms_id, $sms_authkey, $receiver_tel1, "", $fromtel, $date, $msg_mem_present, $etcmsg, $use_mms);
				}
			}
		}
	}
	/**
	* Erpia 연동 관련 추가시 실시간 호출 프로세스
	*/
	if(file_exists($Dir."erpia/erpia.class.php")){
		include_once ($Dir."erpia/erpia.class.php");
		$erpia = new erpia();
		$erpia->_realTimeSync('order',$ordercode);
	}

	//주문서 주문 주문완료 처리
	$sql="UPDATE auction_order SET status = 3, updateDate = NOW() WHERE aoidx in (SELECT aoidx FROM tblorderproduct WHERE ordercode = '".$ordercode."' AND aoidx > 0)";
	mysql_query($sql,get_db_conn());
	
	//tblorderlog 처리구분 변경시 로그 등록
	$log_sql = "INSERT INTO tblorderlog (vender,aoidx,ordercode,productcode,pay_admin_proc,deli_gbn,deli_com,deli_num,deli_date,createDate)
				SELECT
					op.vender,op.aoidx,op.ordercode,op.productcode,oi.pay_admin_proc,op.deli_gbn,op.deli_com,op.deli_num,op.deli_date,now()
				FROM
					tblorderproduct op
					LEFT JOIN tblorderinfo oi ON
					op.ordercode = oi.ordercode
				WHERE
					op.productcode NOT LIKE '99%' AND
					op.ordercode = '".$ordercode."'";
	mysql_query($log_sql,get_db_conn());
	
}


//주문중 주문취소 데이터 처리
if ((preg_match("/^(O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")!=0 && strlen($ordercode)>0 && strlen($pay_auth_no)==0 && $pay_flag_check=="N" && $deli_gbn=="C" && $deli_flag=="N") || ($paymethod=="V" && strlen($ordercode)>0 && $deli_gbn=="C" && $deli_flag=="N" && strlen($bank_date)==0) && strlen($delflag)==0) {
	//옵션별 상품의 수량복구를 위해서 해당 주문의 옵션을 찾는다.

	$sql = "SELECT a.option_quantity,b.productcode,b.opt1_idx,b.opt2_idx,b.quantity ";
	$sql.= "FROM tblproduct a, tblbasket b WHERE b.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode ";
	$result = mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		if(strlen($row->option_quantity)>0){
			if(strlen($option_quantity[$row->productcode])==0) {
				$option_quantity[$row->productcode]=$row->option_quantity;
			}
			$option1num=$row->opt1_idx;
			$option2num=($row->opt2_idx>0?$row->opt2_idx:1);
			$optioncnt2 = explode(",",substr($option_quantity[$row->productcode],1));
			if($optioncnt2[($option2num-1)*10+($option1num-1)]!="") {
				$optioncnt2[($option2num-1)*10+($option1num-1)]+=$row->quantity;
			}
			$tempoption_quantity="";
			for($j=0;$j<5;$j++){
				for($i=0;$i<10;$i++){
					$tempoption_quantity.=",".$optioncnt2[$j*10+$i];
				}
			}
			if(strlen($tempoption_quantity)>0){
				$option_quantity[$row->productcode]=$tempoption_quantity.",";
			}
		}
	}
	mysql_free_result($result);

	//상품 수량 복구
	//결제성공시에만 재고를 변경하므로 사용하지 않는다 2021-10-14 김성식
	/*
	$sql = "SELECT quantity, productcode, package_idx, assemble_idx, assemble_info FROM tblorderproduct WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		// 상품DB에서 수량을 더한다.
		if(strlen($option_quantity[$row->productcode])>0) {
			$sql2 = "UPDATE tblproduct SET ";
			$sql2.= "quantity		= quantity+".$row->quantity.", ";
			$sql2.= "option_quantity='".$option_quantity[$row->productcode]."' ";
			$sql2.= "WHERE productcode='".$row->productcode."' ";
		} else {
			$sql2 = "UPDATE tblproduct SET ";
			$sql2.= "quantity		= quantity+".$row->quantity." ";
			$sql2.= "WHERE productcode='".$row->productcode."' ";
		}
		mysql_query($sql2,get_db_conn());

		if(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info)))) {
			$assemble_infoall_exp = explode("=",$row->assemble_info);

			if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
				$package_info_exp = explode(":",$assemble_infoall_exp[0]);
				if(strlen($package_info_exp[0])>0) {
					$package_productcode_exp = explode("",$package_info_exp[0]);
					for($k=0; $k<count($package_productcode_exp); $k++) {
						$sql2 = "UPDATE tblproduct SET ";
						$sql2.= "quantity		= quantity+".$row->quantity." ";
						$sql2.= "WHERE productcode='".$package_productcode_exp[$k]."' ";
						mysql_query($sql2,get_db_conn());
					}
				}
			}

			if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
				$assemble_info_exp = explode(":",$assemble_infoall_exp[1]);
				if(strlen($assemble_info_exp[0])>0) {
					$assemble_productcode_exp = explode("",$assemble_info_exp[0]);
					for($k=0; $k<count($assemble_productcode_exp); $k++) {
						$sql2 = "UPDATE tblproduct SET ";
						$sql2.= "quantity		= quantity+".$row->quantity." ";
						$sql2.= "WHERE productcode='".$assemble_productcode_exp[$k]."' ";
						mysql_query($sql2,get_db_conn());
					}
				}
			}
		}
	}
	mysql_free_result($result);
*/
	// 적립금 환원
	if ($_data->reserve_maxuse>=0 && strlen($user_reserve)>0 && $user_reserve>0 && strlen($delflag)==0){
		$sql = "UPDATE tblmember SET reserve = reserve + ".abs($user_reserve)." ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		mysql_query($sql,get_db_conn());
	}


	// 사용 쿠폰 복원
	if( strlen($_ShopInfo->getMemid()) > 0 ) {
		$sql = "SELECT productcode FROM tblorderproduct WHERE ordercode='".$ordercode."' AND productcode LIKE 'COU%' ";
		$result=mysql_query($sql,get_db_conn());
		while ($row=mysql_fetch_object($result)){
			$coupon_code=substr($row->productcode,3,-2);
			mysql_query("UPDATE tblcouponissue SET used='N' WHERE id='".$_ShopInfo->getMemid()."' AND coupon_code='".$coupon_code."'",get_db_conn());
		}
	}

	// 주문서 주문취소,적립금환원상태로 돌려놓음.
	$sql = "UPDATE tblorderinfo SET ";
	$sql.= "pay_data		= '고객이 결제창에서 주문취소를 하였습니다.', ";
	$sql.= "deli_gbn	= 'C', ";
	$sql.= "del_gbn		= 'R' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$sql.= "AND paymethod='".$paymethod."' AND pay_flag='N' ";
	if(mysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
		mysql_query($sql,get_db_conn());
	}

	echo "<script type=\"text/javascript\"> <!--  alert('x2chi');  //--> </script>";

} else if(((preg_match("/^(O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")==0 && strlen($ordercode)>0 && $deli_gbn!="C" && $deli_flag=="N") || $paymethod=="B" || ($paymethod=="V" && strlen($ordercode)>0 && $deli_gbn!="C" && $deli_flag=="N" && strlen($bank_date)>0)) && strlen($delflag)==0) {
	// 주문성공시 적립금차감/쿠폰 차감.
	if($_data->reserve_maxuse>=0 && strlen($user_reserve)>0 && $user_reserve>0) {
		$sql = "INSERT tblreserve SET ";
		$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
		$sql.= "reserve		= -$user_reserve, ";
		$sql.= "reserve_yn	= 'N', ";
		$sql.= "content		= '물품 주문시 적립금 사용', ";
		$sql.= "orderdata	= '".$ordercode."=".$last_price."', ";
		$sql.= "date		= '".date("YmdHis")."' ";
		mysql_query($sql,get_db_conn());
	}
	$sql="UPDATE tblorderinfo SET del_gbn='N' WHERE ordercode='".$ordercode."'";
	mysql_query($sql,get_db_conn());
	//주문 상품 품절시 관리자에서 sms 통보
	$sqlsms="SELECT * FROM tblsmsinfo WHERE admin_soldout='Y' ";
	$resultsms= mysql_query($sqlsms,get_db_conn());
	if($rowsms=mysql_fetch_object($resultsms)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;

		$totellist=$rowsms->admin_tel;
		if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
		if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
		if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
		$fromtel=$rowsms->return_tel;
		mysql_free_result($resultsms);
		$etcmsg="상품품절 알림 메세지(관리자)";
		$date="0";
		//관리자가 sms를 원하지 않는 시간 체크하여 그외시간에 보내도록 한다.
		if($rowsms->sleep_time1!=$rowsms->sleep_time2){
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
		$sql = "SELECT a.productname FROM tblproduct a, tblorderproduct b ";
		$sql.= "WHERE b.ordercode='".$ordercode."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$sql.= "AND (a.quantity<=0 && a.quantity is NOT NULL) ";
		$result = mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$smsmsg="[".addslashes($row->productname)."]이 ".$sender_name."님 주문에 의해서 품절되었습니다.";
			$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
		}
		mysql_free_result($result);
	}
}

?>
<html>
<head>
<title>결제</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onLoad="document.form1.submit()">
<form name=form1 action="./orderend.php" method=post>
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>
</body>
</html>
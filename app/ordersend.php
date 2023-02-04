<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/venderlib.php");

$tTable = "tblmember";
$_VenderInfo = new _VenderInfo($_COOKIE[_vinfo]);
if($_ShopInfo->getMemid()==$_VenderInfo->getId()){
	$Vender = 1;
	$tTable = "tblvenderinfo";
}
$ordertype		= $_REQUEST["ordertype"];
$receiveDate		= $_POST["receiveDate"];
$receiveTime		= $_POST["receiveTime"];
$basket = basketTable($ordertype);

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

//tblorderinfotemp 050번호 등록용 파라미터
$oi_aoidx = "";
$oi_productcode = "";

function gobackError($ordercode,$msg){
	if(!_empty($ordercode)){
		@mysql_query("DELETE FROM tblorderinfotemp WHERE ordercode='".$ordercode."'",get_db_conn());
		@mysql_query("DELETE FROM tblorderproducttemp WHERE ordercode='".$ordercode."'",get_db_conn());
		@mysql_query("DELETE FROM tblorderoptiontemp WHERE ordercode='".$ordercode."'",get_db_conn());
		echo "<html></head><body onload=\"alert('".$msg."');parent.location.replace='".$Dir.MobileDir."basket.php'\"></body></html>";
	}
	exit;
}

$ip = getenv("REMOTE_ADDR");

$sslchecktype="";
if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
	$sslchecktype="ssl";
}
if($sslchecktype=="ssl") {
	$secure_data=getSecureKeyData($_POST["sessid"]);
	if(!is_array($secure_data)) {
		echo "<html><head><title></title></head><body onload=\"alert('보안인증 정보가 잘못되었습니다.');history.go(-2);\"></body></html>";exit;
	}
	foreach($secure_data as $key=>$val) {
		${$key}=$val;
	}
} else {
	foreach($_POST as $key=>$val) {
		${$key}=$val;
	}
}

if (!_empty($_POST['ord_info_save']) && $_POST['ord_info_save'] == "Y") {
	$addInfo = array();
	$addInfo['id']		= $_ShopInfo->getMemid();
	$addInfo['name']	= trim($sender_name);
	$addInfo['email']	= trim($sender_email);
	$addInfo['tel']		= trim($sender_tel);
	$addInfo['phone']	= trim($sender_tel);
	$addInfo['post']	= trim($rpost1);
	$addInfo['addr']	= trim($raddr1)."=".trim($raddr2);

	setAutoAddMemInfo($addInfo);
}

$raddr = $_POST['raddr1'];

if(strlen($_ShopInfo->getMemid())==0) {	//비회원
	$basketWhere = "tempkey='".$_ShopInfo->getTempkey()."'";
}else{
	$basketWhere = "id='".$_ShopInfo->getMemid()."'";
}

// 주문타입별 장바구니 테이블
$basket = basketTable($ordertype);



/**************************************************************************
주문자 정보
***************************************************************************/

$sender_name		= ereg_replace(" ","",$sender_name);
$sender_email		= ereg_replace("'","",$sender_email);
$receiver_name		= ereg_replace(" ","",$receiver_name);
$order_msg			= ereg_replace("'","",$order_msg);
$sender_tel			= ereg_replace("'","",$sender_tel);
$receiver_tel1		= ereg_replace("'","",$receiver_tel1);
$receiver_tel2		= ereg_replace("'","",$receiver_tel2);
$receiver_addr		= ereg_replace("'","",$receiver_addr);
//$rpost				= $rpost1.$rpost2;
$rpost				= $rpost1;

//주소 앞칸 자르기
$cutaddr = explode(' ', $raddr1);
$loc				= $cutaddr[0];
$receiver_email		= ereg_replace("'","",$receiver_email);
$receiver_message	= ereg_replace("'","",$receiver_message);
$usereserve			= ereg_replace(",","",$usereserve);

$orderpatten		= array("(')","(\\\\)");
$orderreplace		= array("","");

/*---------------------------------------------------------------------------*/
if (strlen($paymethod)==0) {
	echo "<html></head><body onload=\"alert('결제방법이 선택되지 않았습니다.');parent.document.form1.process.value='N';parent.ProcessWait('hidden');\"></body></html>";
	exit;
}

if (strlen($usereserve)>0 && !IsNumeric($usereserve)) {
	echo "<html></head><body onload=\"alert('적립금은 숫자만 입력하시기 바랍니다.');parent.document.form1.process.value='N';parent.ProcessWait('hidden');\"></body></html>";
	exit;
}

if(strlen($_data->escrow_id)==0 && $paymethod=="Q") {
	echo "
		<html>
			<body onload=\"
				alert('에스크로 정보가 존재하지 않습니다.');
				parent.document.form1.process.value='N';
				parent.ProcessWait('hidden');
				parent.document.all.paybuttonlayer.style.display='block';
				parent.document.all.payinglayer.style.display='none';
			\"></body>
		</html>
	";
	exit;
}

$escrow_info = GetEscrowType($_data->escrow_info);
$escrow=$escrow_info['escrow'];
if(strlen($_data->escrow_id)>0 && ($escrow_info["escrowcash"]=="Y" || $escrow_info["escrowcash"]=="A")) {
	$escrowok="Y";
} else {
	$escrowok="N";
	$escrow_info["escrowcash"]="";
	if($escrow_info["onlycash"]!="Y" && (strlen($escrow_info["onlycard"])==0 && strlen($escrow_info["nopayment"])==0)) $escrow_info["onlycash"]="Y";
}

$pg_type="";
switch ($paymethod) {
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

$pg_type = trim($pg_type);

/**********************
* 결제사 임시 강제 적용 (테스트후 주석 하기)
* A : KCP
* B : LG U+
* C : 올더게이트
* D : 이니시스
* E : 나이스페이
***********************/
$pg_type = "E";

$pmethod=$paymethod.$pg_type;

if ($paymethod!="B" && strlen($pg_type)==0) {
	echo "
		<html>
				<body onload=\"
					alert('선택하신 결제방법은 이용하실 수 없습니다.');
					parent.document.form1.process.value='N';
					parent.ProcessWait('hidden');
					parent.document.all.paybuttonlayer.style.display='block';
					parent.document.all.payinglayer.style.display='none';
				\">
				</body>
		</html>";
	exit;
}

$card_splittype		= $_data->card_splittype;
$card_splitmonth	= $_data->card_splitmonth;
$card_splitprice	= $_data->card_splitprice;

$coupon_ok			= $_data->coupon_ok;			//쿠폰 기능 사용 여부
$card_miniprice		= $_data->card_miniprice;		//카드결제 최소 금액
$reserve_limit		= $_data->reserve_limit;		//적립금 최대 사용 금액
$reserve_maxprice	= $_data->reserve_maxprice;		//적립금 결제 최대 금액

if( $reserve_limit < 1 ) $reserve_limit=1000000000000;

if($_data->rcall_type=="Y") {
	$rcall_type = $_data->rcall_type;
	$bankreserve="Y";
} else if($_data->rcall_type=="N") {
	$rcall_type = $_data->rcall_type;
	$bankreserve="Y";
} else if($_data->rcall_type=="M") {
	$rcall_type="Y";
	$bankreserve="N";
} else {
	$rcall_type="N";
	$bankreserve="N";
}

if($_data->reserve_useadd==-1) $reserve_useadd="N";
else if($_data->reserve_useadd==-2) $reserve_useadd="U";
else $reserve_useadd=$_data->reserve_useadd;

$etcmessage=explode("=",$_data->order_msg);

#적립금이 현금결제시에만 사용가능하고 현금결제를 선택안했을때
if($bankreserve=="N" && !preg_match("/^(B|V|O|Q)$/",$paymethod)) {
	$usereserve=0;
}

$user_reserve=0;

// 회원 / 비회원
if(strlen($_ShopInfo->getMemid())>0) {
	// 회원 정보
	$sql = "SELECT * FROM {$tTable} WHERE id='".$_ShopInfo->getMemid()."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$ordercode=unique_id();
		$user_reserve = $row->reserve;
		$group_code=$row->group_code;
		$id=$_ShopInfo->getMemid();
		mysql_free_result($result);

		if(strlen($group_code)>0 && $group_code!=NULL) {
			$sql = "SELECT * FROM tblmembergroup WHERE group_code='".$group_code."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$group_code=$row->group_code;
				$group_name=$row->group_name;
				$group_type=substr($row->group_code,0,2);
				$group_usemoney=$row->group_usemoney;
				$group_addmoney=$row->group_addmoney;
				$group_payment=$row->group_payment;
			}
			mysql_free_result($result);

		}
	} else {
		$_ShopInfo->SetMemNULL();
		// 등록된 회원이 없을때 - 비회원
		$ordercode=unique_id()."X";
		$id="X".date("iHs").$sender_name;
	}
} else {
	//비회원 구매의 경우 주문번호 뒤에 X를 붙힘
	$ordercode=unique_id()."X";
	$id="X".date("iHs").$sender_name;
}










/****************************************************
재고 수량 파악 START
*****************************************************/
	$errmsg="";
	$sql = "SELECT a.quantity as sumquantity,a.com_idx,b.productcode,b.productname,b.display,b.quantity,b.group_check,b.social_chk, ";
	$sql.= "b.option_quantity,b.etctype,b.assembleuse,a.assemble_list AS basketassemble_list ";
	$sql.= ", c.assemble_list,a.package_idx ";
	$sql.= "FROM {$basket} a, tblproduct b ";
	$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
	$sql.= "WHERE a.".$basketWhere." ";
	$sql.= "AND a.productcode=b.productcode ";
	$result=mysql_query($sql,get_db_conn());
	$assemble_proquantity_cnt=0;
	
	while($row=mysql_fetch_object($result)) {
		if($row->display!="Y") {
			$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 판매가 되지 않는 상품입니다.\\n";
		}
	
	
	
	
	
	
		// today sale 판매 시간 관련 check
		if(preg_match('/^899[0-9]{15}$/',$row->productcode)){
			$tsql = "select unix_timestamp(t.end) -unix_timestamp() as remain, t.salecnt+t.addquantity as sellcnt from tblproduct a inner join todaysale t using(pridx) WHERE a.productcode='".$row->productcode."' limit 1";
	
			if(false === $tres = mysql_query($tsql,get_db_conn())){
				$errmsg="[".ereg_replace("'","",$row->productname)."]의 정보를 DB 에서 확인 하는중 오류가 발생했습니다..\\n";
			}else{
				if(mysql_num_rows($tres) < 1){
					$errmsg="[".ereg_replace("'","",$row->productname)."]의 정보를 찾을수 없습니다.\\n";
				}else{
					$trow = mysql_fetch_assoc($tres);
					if($trow['remain'] < 1){
						$errmsg="[".ereg_replace("'","",$row->productname)."]은 판매 마감된 상품 입니다.\\n";
						mysql_query("delete from {$basket} where a.".$basketWhere." and productcode='".$row->productcode."'",get_db_conn()); // 삭제 처리
	
	
	
	
					}
				}
			}
		}
	
	
		if($row->social_chk =="Y") {	//소셜상품
			$sql2 = "SELECT count(1) as cnt FROM tblproduct_social WHERE pcode='".$row->productcode."' AND '".time()."' between sell_startdate and sell_enddate ";
			$result2=mysql_query($sql2,get_db_conn());
			if($row2=mysql_fetch_object($result2)) {
				if(strlen($errmsg)==0 && $row2->cnt == 0){
					$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 판매가 종료된 상품입니다.\\n";
	
				}
			}
		}
		if($row->group_check!="N") {
			if(strlen($_ShopInfo->getMemid())>0) {
				$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
				$sqlgc.= "WHERE productcode='".$row->productcode."' ";
				$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
				$resultgc=mysql_query($sqlgc,get_db_conn());
				if($rowgc=@mysql_fetch_object($resultgc)) {
					if($rowgc->groupcheck_count<1) {
	
	
	
						$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 지정 등급 전용 상품입니다.\\n";
					}
					@mysql_free_result($resultgc);
				} else {
					$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 지정 등급 전용 상품입니다.\\n";
				}
			} else {
				$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 회원 전용 상품입니다.\\n";
			}
		}
		$assemble_list_exp = array();
		if(strlen($errmsg)==0 && $row->assembleuse=="Y") { // 조립/코디 상품 등록에 따른 구성상품 체크
			if(strlen($row->assemble_list)==0) {
				$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 구성상품이 미등록된 상품입니다. 다른 상품을 주문해 주세요.\\n";
			} else {
				$assemble_list_exp = explode("",$row->basketassemble_list);
			}
		}
		if(strlen($errmsg)==0) {
			$miniq=1;
			$maxq="?";
			if(strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				for($i=0;$i<count($etctemp);$i++) {
					if(substr($etctemp[$i],0,6)=="MINIQ=")     $miniq=substr($etctemp[$i],6);
					if(substr($etctemp[$i],0,5)=="MAXQ=")      $maxq=substr($etctemp[$i],5);
				}
			}
	
	
	
	
	
	
	
	
	
	
	
			if(strlen(dickerview($row->etctype,0,1))>0) {
				$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\\n";
	
			}
		}
	
		$package_productcode_tmp = array();
		$package_quantity_tmp = array();
		$package_productname_tmp = array();
		if(strlen($errmsg)==0 && $row->package_idx>0) { // 패키지 상품 등록에 따른 구성상품 체크
			if(count($productcode_package_list[$row->productcode][$row->package_idx])>0) {
				$package_productcode_tmp = $productcode_package_list[$row->productcode][$row->package_idx];
				$package_quantity_tmp = $quantity_package_list[$row->productcode][$row->package_idx];
				$package_productname_tmp = $productname_package_list[$row->productcode][$row->package_idx];
	
			}
		}
	
		if(strlen($errmsg)==0) {
			if ($miniq!=1 && $miniq>1 && $row->sumquantity<$miniq)
				$errmsg.="[".ereg_replace("'","",$row->productname)."]상품은 최소 ".$miniq."개 이상 주문하셔야 합니다.\\n";
	
			if ($maxq!="?" && $maxq>0 && $row->sumquantity>$maxq)
				$errmsg.="[".ereg_replace("'","",$row->productname)."]상품은 최대 ".$maxq."개 이하로 주문하셔야 합니다.\\n";
	
			if(strlen($row->quantity)>0) {
				if ($row->sumquantity>$row->quantity) {
					if ($row->quantity>0)
						$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$row->quantity." 개 입니다.")."\\n";
					else
						$errmsg.= "[".ereg_replace("'","",$row->productname)."]상품의 재고가 다른고객 주문등의 이유로 장바구니 수량보다 작습니다.\\n";
	
				}
			}
			if($assemble_proquantity_cnt==0) { //일반 및 구성상품들의 재고량 가져오기
				///////////////////////////////// 코디/조립 기능으로 인한 재고량 체크 ///////////////////////////////////////////////
				$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM {$basket} ";
				$basketsql.= "WHERE ".$basketWhere;
				$basketresult =mysql_query($basketsql,get_db_conn());
				while($basketrow=@mysql_fetch_object($basketresult)) {
					if($basketrow->assemble_idx>0) {
						if(strlen($basketrow->assemble_list)>0) {
							$assembleprolistexp = explode("",$basketrow->assemble_list);
							for($i=0; $i<count($assembleprolistexp); $i++) {
								if(strlen($assembleprolistexp[$i])>0) {
									$assemble_proquantity[$assembleprolistexp[$i]]+=$basketrow->quantity;
								}
							}
	
	
						}
					} else {
						$assemble_proquantity[$basketrow->productcode]+=$basketrow->quantity;
					}
	
	
				}
				@mysql_free_result($basketresult);
				$assemble_proquantity_cnt++;
			}
			if(count($assemble_list_exp)>0) { // 구성상품의 재고 체크
				$assemprosql = "SELECT productcode,quantity,productname FROM tblproduct ";
				$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
				$assemprosql.= "AND display = 'Y' ";
				$assemproresult=mysql_query($assemprosql,get_db_conn());
				while($assemprorow=@mysql_fetch_object($assemproresult)) {
					if(strlen($assemprorow->quantity)>0) {
						if($assemble_proquantity[$assemprorow->productcode]>$assemprorow->quantity) {
							if($assemprorow->quantity>0) {
								$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 구성상품 [".ereg_replace("'","",$assemprorow->productname)."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$assemprorow->quantity." 개 입니다.")."\\n";
							} else {
								$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 구성상품 [".ereg_replace("'","",$assemprorow->productname)."] 다른 고객의 주문으로 품절되었습니다.\\n";
							}
						}
					}
				}
			} else if(strlen($package_productcode_tmp)>0) { // 패키지 구성상품의 재고 체크
				$package_productcode_tmpexp = explode("",$package_productcode_tmp);
				$package_quantity_tmpexp = explode("",$package_quantity_tmp);
				$package_productname_tmpexp = explode("",$package_productname_tmp);
				for($i=0; $i<count($package_productcode_tmpexp); $i++) {
					if(strlen($package_productcode_tmpexp[$i])>0) {
						if(strlen($package_quantity_tmpexp[$i])>0) {
							if($assemble_proquantity[$package_productcode_tmpexp[$i]] > $package_quantity_tmpexp[$i]) {
								if($package_quantity_tmpexp[$i]>0) {
									$errmsg.="해당 상품의 패키지 [".ereg_replace("'","",$package_productname_tmpexp[$i])."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$package_quantity_tmpexp[$i]." 개 입니다.")."\\n";
								} else {
									$errmsg.="해당 상품의 패키지 [".ereg_replace("'","",$package_productname_tmpexp[$i])."] 다른 고객의 주문으로 품절되었습니다.\\n";
								}
							}
						}
					}
				}
			} else { // 일반상품의 재고 체크
				if(strlen($row->quantity)>0) {
					if($assemble_proquantity[$assemprorow->productcode]>$row->quantity) {
						if ($row->quantity>0) {
							$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$row->quantity." 개 입니다.")."\\n";
						} else {
							$errmsg.= "[".ereg_replace("'","",$row->productname)."]상품의 재고가 다른고객 주문등의 이유로 장바구니 수량보다 작습니다.\\n";
						}
					}
				}
			}
			if(strlen($row->option_quantity)>0) {
				$sql = "SELECT opt1_idx, opt2_idx, quantity FROM {$basket} ";
				$sql.= "WHERE ".$basketWhere." ";
				$sql.= "AND productcode='".$row->productcode."' ";
				$result2=mysql_query($sql,get_db_conn());
				while($row2=mysql_fetch_object($result2)) {
					$optioncnt = explode(",",substr($row->option_quantity,1));
					$optionvalue=$optioncnt[($row2->opt2_idx==0?0:($row2->opt2_idx-1))*10+($row2->opt1_idx-1)];
	
					if($optionvalue<=0 && $optionvalue!="") {
						$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
					} else if($optionvalue<$row2->quantity && $optionvalue!="") {
						$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
					}
	
				}
				mysql_free_result($result2);
			}
		}
	
	
		//옵션 무제한 재고 확인 2016-10-11 Seul
		//옵션 사용여부 2016-10-04 Seul
		$optClass->setOptUse($row->productcode);
		if($optClass->optUse) {
			$optClassQuantity = $optClass->getOptQuantity($row->com_idx);
			if($optClassQuantity<$row->sumquantity) {
				$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optClassQuantity 개 입니다.")."\\n";
			} else if($optClassQuantity<=0) {
				$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
			}
		}
	}
	mysql_free_result($result);
	
	if(strlen($errmsg)>0) {
		echo "<html></head><body onload=\"alert('".$errmsg."');parent.location.href='".$Dir.MobileDir."basket.php?ordertype=".$ordertype."'\"></body></html>";
		exit;
	}
/****************************************************
재고 수량 파악 END
*****************************************************/



// 사은품 재고 확인 시작 ----------------------------------------------------------------------------------------------------------------------------------------
$giftinfo = array();
if(!_empty($giftval_seq) && $_REQUEST['apply_gift'] != 'N'){
	$gift_msg = addslashes($gift_msg);
	$sql = "SELECT * FROM tblgiftinfo WHERE gift_regdate='".$giftval_seq."' limit 1";

	if(false !== $result=mysql_query($sql,get_db_conn())){
		if($giftinfo=mysql_fetch_assoc($result)){
			$giftinfo['options'] = array();

			for($g=1;$g<=4;$g++){
				if(!_empty($giftinfo['gift_option'.$g])){
					$tmp = explode(',',$giftinfo['gift_option'.$g]);
					$giftinfo['options'][$g][0] = $tmp[0];
					$giftinfo['options'][$g][1] = array();

					for($gi=1;$gi<count($tmp);$gi++){
						if(strpos($tmp[$gi],':')){
							$tmp2 = explode(':',$tmp[$gi]);
							$giftinfo['options'][$g][1][$tmp2[0]] = $tmp2[1];
						}else{
							$giftinfo['options'][$g][1][$tmp[$gi]] = true;
						}
					}


					if(!_empty($_REQUEST['giftOpt'.$g])){
						if(isset($giftinfo['options'][$g][1][$_REQUEST['giftOpt'.$g]])){
							if($giftinfo['options'][$g][1][$_REQUEST['giftOpt'.$g]] !== true){
								$chkq = $giftinfo['options'][$g][1][$_REQUEST['giftOpt'.$g]]--;
								if($chkq < 1){
									echo "<html></head><body onload=\"alert('사은품의 해당 옵션이 다른 고객에 의해 품절 되었습니다. 사은품을 다시 선택해 주세요.');parent.location.href='".$Dir.MobileDir."basket.php'\"></body></html>";
									exit;
								}
							}
							$giftinfo['selopt'][$g] = $giftinfo['options'][$g][0].' : '.$_REQUEST['giftOpt'.$g];
						}
					}
				}
			}

			if(_isInt($giftinfo['quantity'])){
				if($giftinfo['quantity'] < 0){
					echo "<html></head><body onload=\"alert('사은품이 다른 고객에 의해 품절 되었습니다. 사은품을 다시 선택해 주세요.');parent.location.href='".$Dir.MobileDir."basket.php'\"></body></html>";
					exit;
				}
			}
			$giftinfo['gift_quantity'] = 1;
		}
	}
}
// 사은품 재고 확인 끝 ----------------------------------------------------------------------------------------------------------------------------------------//




	// 장바구니 호출
	$basketItems = getBasketByArray($basket); // 쿠폰 적용안됨

	/*
	echo "<div style=\" height:500px; overflow:scroll;  border:2px solid #ff0000 ;  text-align:left;\">";
	_pr($basketItems);
	echo "</div>";
	*/



	/*
	echo "<div style=\" height:500px; width:100%; overflow:scroll;  border:2px solid #ff0000 ;  text-align:left;\">";
	_pr($_POST);
	echo "</div>";
	*/




	// 장바구니 상품 입력 시작 ==================================================================
	$bankonly = "N";
	$goodname = "";
	
	$product_solvquantity = array();
	$optqnantityprlist = array();

	$totalProductPrice = 0;
	foreach($basketItems['vender'] as $vender=>$vendervalue) {
		

		// 상품 리스트 시작 ---------------------------------------------------------------------------------------------------------------------------

		foreach( $vendervalue['products'] as $productKey=>$product ) {

			// 옵션 그룹 사용 처리 시작 -----------------------------------------
			$optvalue="";
			$optvalue2="";
			if( ereg("^(\[OPTG)([0-9]{4})(\])$",$product['optionGroup']) ) {
				$optioncode = substr($product['optionGroup'],5,4);
				$product['option_price']="";
				if( strlen($product['optidxs']) > 0 ) {
					$tempoptcode = substr($row->optidxs,0,-1);
					$exoptcode = explode(",",$tempoptcode);
					$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ";
					$resultopt =mysql_query($sqlopt,get_db_conn());
					if($rowopt = mysql_fetch_object($resultopt)){
						$optionadd = array ( &$rowopt->option_value01, &$rowopt->option_value02, &$rowopt->option_value03, &$rowopt->option_value04, &$rowopt->option_value05, &$rowopt->option_value06, &$rowopt->option_value07, &$rowopt->option_value08, &$rowopt->option_value09, &$rowopt->option_value10 );
						$opti=0;
						$optvalue="";
						while(strlen($optionadd[$opti])>0) {
							if($exoptcode[$opti]>0) {
								$opval = explode("",str_replace('"','',$optionadd[$opti]));
								$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
								$optvalue.= ", ".$opval[0]." : ";
								if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".$exop[1]."원</font>)";
								else if($exop[1]==0) $optvalue.=$exop[0];
								else $optvalue.=$exop[0]."(<font color=#FF3C00>".$exop[1]."원</font>)";
								$row->sellprice+=$exop[1];
							}
							$opti++;
						}
						$optvalue = substr($optvalue,1);
						$optcnt++;

						$optvalue2 = "[OPTG".substr("00".$optcnt,-3)."]";
					}
				}
			}

			if(strlen($optvalue2)>0){
				$optvalue2=str_replace("'","\'",$optvalue2);
				$optvalue=str_replace("'","\'",$optvalue);
				$optionGroupSQL = "
					INSERT
						tblorderoptiontemp
					SET
						ordercode = '".$ordercode."',
						productcode = '".$product['productcode']."',
						opt_idx = '".$optvalue2."',
						opt_name = '".trim( $product['optvalue'] )."'
					;
				";
				mysql_query($optionGroupSQL,get_db_conn());
				//echo "<hr>옵션 그룹 사용<br> ".$optionGroupSQL;
			}
			// 옵션 그룹 사용 처리 끝 ----------------------------------------- //


//결제성공시에만 재고처리하도록 수정한다.  -> payresult
// 2021-10-14 김성식
			// 상품 DB 수량 수정 시작 -----------------------------------------------------------------------
			$tempoptcnt="";
			if(_array($product['option_quantity'])) {
				if (!$product_solvquantity[$product['productcode']]) {
					$product_solvquantity[$product['productcode']] = array();
				} else {
					// 비어있지 않다면, 다른 옵션으로 구매된 상품. 변경된 옵션으로 수정해야 함.
					$product['option_quantity'] = $product_solvquantity[$product['productcode']];
				}
				for($j=0;$j<10;$j++) {
					for($i=0;$i<10;$i++) {
						$selNum = (($product['opt2_idx'])*10+($product['opt1_idx']))-10; // 수량 검증 셀
						$thisNum = $j*10+$i;
						$thisprcnt=0;
						if($selNum == $thisNum){
							if(!_empty($product['option_quantity'][$selNum]) && $product['option_quantity'][$selNum] >0) {
								$product['option_quantity'][$selNum] -=$product['quantity'];
							}	
						}
					}
				}
				// 수정된 옵션 수량을 저장해둠.
				$product_solvquantity[$product['productcode']] = $product['option_quantity'];

				$tempoptcnt = " , option_quantity='".implode(",",$product['option_quantity'])."' ";
				if(!in_array($product['productcode'],$optqnantityprlist)){
					@array_push($optqnantityprlist,$product['productcode']);
				}
			} else{
				$tempoptcnt=" , quantity = quantity-".$product['quantity']." ";
			}

			$productUpdateSQL = "
				UPDATE
					tblproduct
				SET
					sellcount = sellcount+1
					".$tempoptcnt."
				WHERE
					productcode='".$product['productcode']."'
			";
//			mysql_query($productUpdateSQL,get_db_conn());
			//echo "<hr>상품 DB 수량 수정<br> ".$productUpdateSQL;

			//옵션 사용여부 2016-10-04 Seul
			$optClass->setOptUse($product['productcode']);
			//무제한 옵션 상품 수량 수정 2016-10-11 Seul
			if($optClass->optUse) {
				$optUpdateSQL = "UPDATE tblopt_combi SET opt_quantity = opt_quantity-".$product['quantity']." WHERE com_idx='".$product['com_idx']."'";
				mysql_query($optUpdateSQL,get_db_conn());
			}
			// 상품 DB 수량 수정 끝 ----------------------------------------------------------------------- //

			// 적립금 사용시 추가 적립 조건
			if($reserve_useadd!="N" && $usereserve>=$reserve_useadd && $usereserve!=0) {
				$product['reserve']=0;
			} else if($reserve_useadd=="U" && $usereserve!=0) {
				$reservepercent = 100 * ($sumprice-$usereserve) / $sumprice;
				$product['reserve']=round($product['reserve']*($reservepercent/100),-1);
			}

			//상품 등록 시작 ----------------------------------------------------------------------
			//무제한 옵션 상품 등록 2016-10-11 Seul
			if($optClass->optUse) {
				$optProductSQL = "INSERT tblopt_orderprd SET ";
				$optProductSQL.= "ordercode = '".$ordercode."', ";
				$optProductSQL.= "productcode = '".$product['productcode']."', ";
				$optProductSQL.= "opt_comtext = '".$optClass->getOptComText($product['productcode'], $product['com_idx'])."', ";
				$optProductSQL.= "com_idx = '".$product['com_idx']."', ";
				$optProductSQL.= "quantity = '".$product['quantity']."' ";
				mysql_query($optProductSQL,get_db_conn());
			}

			if( strlen($optvalue2) > 0 ) { // 옵션그룹 상품의 경우
					$orderProductOptValue[0] = $optvalue2;
					$orderProductOptValue[1] = '';
			} else{
				if( strlen($product['optvalue']) > 0 ) {
					$orderProductOptValue = explode( ",",  $product['optvalue'] );
				} else{
					$orderProductOptValue[0] = ( strlen($product['option1'][$product['opt1_idx']]) > 0 ) ? $product['option1'][0].":".$product['option1'][$product['opt1_idx']] : '';
					$orderProductOptValue[1] = ( strlen($product['option2'][$product['opt2_idx']]) > 0 ) ? $product['option2'][0].":".$product['option2'][$product['opt2_idx']] : '';
				}
			}
			// 160226 :: 판매 상품 총 금액 합계 => 검증을 위한 값. //
			$totalProductPrice = $totalProductPrice + ($product['quantity'] * $product['sellprice']);
						
			$orderProductSQL = "INSERT tblorderproducttemp SET ";
			$orderProductSQL.= "vender = ".$product['vender'].", ";
			$orderProductSQL.= "ordercode = '".$ordercode."', ";
			$orderProductSQL.= "tempkey = '".$_ShopInfo->getTempkey()."', ";
			$orderProductSQL.= "productcode = '".$product['productcode']."', ";
			$orderProductSQL.= "productname = '".preg_replace($orderpatten,$orderreplace,$product['productname'])."', ";
			$orderProductSQL.= "opt1_name = '".trim( $orderProductOptValue[0] )."', ";
			$orderProductSQL.= "opt2_name = '".trim( $orderProductOptValue[1] )."', ";
			$orderProductSQL.= "addcode = '".$product['addcode']."', ";
			$orderProductSQL.= "quantity = ".$product['quantity'].", ";
			$orderProductSQL.= "price = ".$product['sellprice'].", ";
			$orderProductSQL.= "reserve = ".round($product['reserve']/$product['quantity']).", ";
			$orderProductSQL.= "date = '".$product['date']."', ";
			$orderProductSQL.= "selfcode = '".$product['selfcode']."', ";
			$orderProductSQL.= "sell_memid = '".$product['sell_memid']."', ";
			$orderProductSQL.= "review_type = 'N', ";
			$orderProductSQL.= "basketidx = ".$product['basketidx'].", ";
			
			//주문서 주문의 상품별 주문
			if($product['aoidx']>0){
				$orderProductSQL.= "aoidx = ".$product['aoidx'].", ";
			}
			$orderProductSQL.= "receiver_addr = '".$product['addr']."', ";
			$orderProductSQL.= "receiver_name = '".$product['rcvName']."', ";
			$orderProductSQL.= "receiver_tel = '".$product['tel']."', ";
			$orderProductSQL.= "receiveTypeText = '".$product['receiveTypeText']."', ";
			if(strlen($receiveDate) > 0 && strlen($receiveTime)> 0){
				$orderProductSQL.= "receiveDate = '".$receiveDate."', ";
				$orderProductSQL.= "receiveTime = '".$receiveTime."', ";
			}else{
				$orderProductSQL.= "receiveDate = '".$product['receiveDate']."', ";
				$orderProductSQL.= "receiveTime = '".$product['receiveTime']."', ";
			}
			
		
			//옵션 무제한 상품 인덱스 삽입 2016-10-11 Seul
			if($optClass->optUse) {
				$ordprd_optidx = $optClass->getOrdprdOptidx($ordercode, $product['productcode'], $product['com_idx']);
				if($ordprd_optidx > 0) {
					$orderProductSQL.= "ordprd_optidx = ".$ordprd_optidx." ";
				} else {
					$orderProductSQL.= "ordprd_optidx = 0 ";
				}
			} else {
				$orderProductSQL.= "ordprd_optidx = 0 ";
			}
			mysql_query($orderProductSQL,get_db_conn());
			$orderProduct_UID = mysql_insert_id (get_db_conn()); //주문 상품 고유 번호
			//echo "<hr>상품 등록 <br> ".$orderProductSQL;
			
			// 쿠폰 사용 상품 매칭 주문 상품 고유 번호 -------------------------------------------------
			$orderProductKey = $product['productcode']."_".$product['opt1_idx']."_".$product['opt2_idx']."_".$product['optidxs']."_".$product['com_idx'];
			$orderProductKey = str_replace(",","",$orderProductKey);
			$orderProductUid[ $orderProductKey ] = $orderProduct_UID; // 주문 상품 idx
			//echo "<hr>쿠폰 사용 상품 매칭 키 주문 상품 고유 번호 <br> ".$orderProductKey." => ".$orderProductUid[ $orderProductKey ];
			// 상품 등록 끝 -----------------------------------------------------------------------//
			
			//tblorderinfotemp 050번호 등록용 파라미터값 설정
			$oi_aoidx = $product['aoidx'];
			$oi_productcode = $product['productcode'];

			// today sale 판매 시간 관련 check 시작 -----------------------------------------------------------------------
			if(preg_match('/^899[0-9]{15}$/',$product['productcode'])){
				$tsql = "
					update
						todaysale T,
						tblproduct P
					set
						T.salecnt=T.salecnt+".intval($product['quantity'])."
					WHERE
						P.productcode = '".$product['productcode']."'
						AND
						T.pridx=P.pridx
					limit 1 ;
				";
				mysql_query($tsql,get_db_conn());
				//echo "<hr>today sale 판매 시간 관련 check<br> ".$tsql;
			}
			// today sale 판매 시간 관련 check 끝 ----------------------------------------------------------------------- //



			// 현금결제 전용 상품 ------------------------------------------------------
			if( $product['bankonly'] == "Y" ) $bankonly = "Y";


			// PG용 상품 명
			if (strlen($goodname)>0) $goodname = preg_replace($orderpatten,$orderreplace,$product['productname'])." 등.."; else $goodname = preg_replace($orderpatten,$orderreplace,$product['productname']);

		}
		// 상품 리스트 끝 -----------------------------------------------------------------------------------------------------------------------------//
	}
	// 장바구니 상품 입력 끝 ===================================================================//
	
	if(_array($optqnantityprlist)){
		setOptQuantityCheck($optqnantityprlist);
	}

	// 현금결제상품이 있는데 카드결제선택시
	if ($bankonly=="Y" && !preg_match("/^(B|V|O|Q)$/",$paymethod)) {
		echo "<html></head><body onload=\"alert('현금결제 상품이 있기 때문에 무통장 입금 결제만 선택하실 수 있습니다.');parent.location.href='./basket.php'\"></body></html>";
		exit;
	}



	// 쿠폰 사용 정보 등록 시작 -------------------------------------------------------
	if( !_empty($_ShopInfo->getMemid())
		AND
		$_data->coupon_ok=="Y"
		AND
		!_empty($_REQUEST['couponproduct'])
		AND
		(
			!_empty($_REQUEST['coupon_price'])
			OR
			!_empty($_REQUEST['coupon_reserve'])
		)
	) {

		$couponitems = array();

		$dcpricelist = explode("|",$_REQUEST['dcpricelist']); // 할인금액리스트
		$drpricelist = explode("|",$_REQUEST['drpricelist']); // 적립금액 리스트
		$couponproduct = explode("|",$_REQUEST['couponproduct']); // 쿠폰적용상품 리스트

		$tmpcoupon = array();

		// 쿠폰리스트
		for($qq=1,$end=count($couponproduct);$qq<$end;$qq++){

			$tmpProductcode = explode("_",$couponproduct[$qq]); // 쿠폰적용 상품 정보 분석 ( 쿠폰코드_상품코드_옵션1_옵션2 )
			$tmpCouponCode = $tmpProductcode[0];

			// 쿠폰 사용 상품 매칭-------------------------------------------------
			$orderProductUidKey = $tmpProductcode[1]."_".$tmpProductcode[2]."_".$tmpProductcode[3]."_".$tmpProductcode[4]."_".$tmpProductcode[5];
			$orderProductUidKey = str_replace(",","",$orderProductUidKey);
			$orderUid = $orderProductUid[ $orderProductUidKey ];
			//echo "<hr>쿠폰 사용 상품 매칭 키<br> ".$orderProductUidKey." =>".$orderUid;
			if( $couponproduct[$qq] > 0 ) {
				$orderCouponMatching = "
					INSERT
						tblordercoupon
					SET
						ordercode = '".$ordercode."',
						orderPuid = ".$orderUid.",
						couponcode = '".$tmpCouponCode."' ,
						dcPrice = ".intval($dcpricelist[$qq]).",
						reserve = ".intval($drpricelist[$qq])."
					;
				";
				mysql_query($orderCouponMatching,get_db_conn());
				//echo "<hr>쿠폰 사용 상품 매칭<br> ".$orderCouponMatching;
			}
			// 쿠폰 사용 상품 매칭------------------------------------------------- //


			if( _array($couponitems[$tmpCouponCode]) ) {
				$couponitems[$tmpCouponCode]['coupon_price'] -= intval($dcpricelist[$qq]); // 쿠폰별 할인
				$couponitems[$tmpCouponCode]['coupon_reserve'] += intval($drpricelist[$qq]); // 쿠폰별 적립
				$couponitems[$tmpCouponCode]['couponmsg'] .= ','.$basketItems['arr_prlist'][$tmpProductcode[1]]; // 쿠폰 사용 상품 명들..
			}else{

				if(!isset($tmpcoupon[$tmpCouponCode]) || !is_object($tmpcoupon[$tmpCouponCode])){
					$sql = "SELECT * FROM tblcouponinfo  WHERE coupon_code ='".$tmpCouponCode."'  limit 1";
					$resultcou =mysql_query($sql,get_db_conn());
					if($rowcou=mysql_fetch_object($resultcou)){
						$tmpcoupon[$rowcou->coupon_code] = $rowcou;
					}
				}

				if(!isset($tmpcoupon[$tmpCouponCode]) || !is_object($tmpcoupon[$tmpCouponCode])){
					continue;
				}
				$rowcou = $tmpcoupon[$tmpCouponCode];

				$tmp = array();
				$tmp['coupon_code'] = $rowcou->coupon_code;
				$tmp['vender']  = $rowcou->vender;
				$tmp['use_point'] = $rowcou->use_point;

				$tmp['coupon_name']=titleCut(50,$rowcou->coupon_name)." - ".number_format($rowcou->sale_money).($rowcou->sale_type<=2?"%":"원").($rowcou->sale_type%2==0?"할인":"적립")."쿠폰";
				$tmp['coupon_name'] = addslashes($tmp['coupon_name']);
				$tmp['coupon_price'] = intval($dcpricelist[$qq])*-1;
				$tmp['coupon_reserve'] = intval($drpricelist[$qq]);

				$tmp['couponmsg'] = $basketItems['arr_prlist'][$tmpProductcode[1]];
				$couponitems[$tmp['coupon_code']] = $tmp;
			}
		}


		if(_array($couponitems)){
			foreach($couponitems as $citem){
				if (isSeller() == 'Y') $citem['coupon_reserve'] = 0; // 도매회원 적립 안됨
				$couponSQL = "
					INSERT
						tblorderproducttemp
					SET
						vender = ".$citem['vender'].",
						ordercode = '".$ordercode."',
						tempkey = '".$_ShopInfo->getTempkey()."',
						productcode = 'COU".$citem['coupon_code']."0X',
						productname = '".preg_replace($orderpatten,$orderreplace,$citem['coupon_name'])."',
						quantity = 1,
						price = ".$citem['coupon_price'].",
						reserve = ".$citem['coupon_reserve'].",
						date = '".date("Ymd")."',
						order_prmsg = '".preg_replace($orderpatten,$orderreplace,$citem['couponmsg'])."'
					;
				";
				mysql_query($couponSQL,get_db_conn());
				//echo "<hr>쿠폰 정보 등록<br> ".$couponSQL;
			}
		}

	}
	// 쿠폰 사용 정보 등록 끝 ------------------------------------------------------- //




	//echo "<hr>초기 총결제금액 : ".number_format($sumprice);


	// 적립금 사용  --------------------------------------------------------------------------------------------------------------------------------------
	if( $usereserve > 0 ) {
		//$usereserve = ( $usereserve > $sumprice ) ? $sumprice : $usereserve;// 적립금이 배송비 정책 변경으로 제거
		$sumprice -= $usereserve;
		$sumprice = ( $sumprice < 0 ) ? 0 : $sumprice;

		// 남은 적립금은 다시 넣어 주거나 없앤다.
		/*
		if($sumprice == 0 AND $sumprice<=$usereserve) {
			$remain_reserve = $user_reserve - $sumprice;
			$usereserve = $sumprice;
		} else {
		*/
			$remain_reserve=$user_reserve-$usereserve;
		//}

		if( $remain_reserve < 0 ) $remain_reserve = 0;

		if(strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0) {
			$remainReserveSQL = "UPDATE {$tTable} SET reserve=".abs($remain_reserve)." WHERE id='".$_ShopInfo->getMemid()."' ";
			mysql_query($remainReserveSQL,get_db_conn());
			//echo "<hr>남은 적립금은 다시 넣어 주거나 없앤다<br> ".$remainReserveSQL;
		}

	}

	if($sumprice==0) {
		$pay_data="총 구매금액 ".number_format($usereserve)."원을 적립금으로 구매";
	}

	//echo "<hr>적립금 사용 총결제금액 (-".number_format($usereserve).") : ".number_format($sumprice);


	// 할인 쿠폰 사용   --------------------------------------------------------------------------------------------------------------------------------------
	if( $coupon_price > 0 ) {
		$coupon_price = ( $coupon_price > $sumprice ) ? $sumprice : $coupon_price;
		$sumprice -= $coupon_price;
		$sumprice = ( $sumprice < 0 ) ? 0 : $sumprice;
	}

	//echo "<hr> 할인 쿠폰 사용 총결제금액 (-".number_format($coupon_price).") : ".number_format($sumprice);


	// 회원그룹(추가)할인   --------------------------------------------------------------------------------------------------------------------------------------
	/*
	if( $groupdiscount < 0 ) {
		$sumprice += $groupdiscount;
		$sumprice = ( $sumprice < 0 ) ? 0 : $sumprice;
	}
*/
	//echo "<hr> 회원그룹(추가)할인 총결제금액 (-".number_format($groupdiscount).") : ".number_format($sumprice);








	// 배송료 추가  --------------------------------------------------------------------------------------------------------------------------------------
	// 쿠폰 사용 할인 적용
	$basketTempList = explode("-",$_POST['basketTempList']);
	foreach ( $basketTempList as $val ){
		$sub = explode("|",$val);
		if( strlen($sub[0]) > 0 ) $basketTempArray[$sub[0]] += $sub[1];
	}
	$basketItemsCoupon = getBasketByArray($basket, $raddr, $basketTempArray); // 쿠폰 적용됨

	/*
	echo "<div style=\" height:500px; overflow:scroll;  border:2px solid #ff0000 ;  text-align:left;\">";
	_pr($basketItemsCoupon);
	echo "</div>";
	*/
	// 배송비 정보 저장 시작 ------------------------------------------
	foreach($basketItemsCoupon['vender'] as $venderCoupon=>$vendervalueCoupon) {
		// 상품 리스트 시작 ---------------------------------------------------------------

		$deliPrdName	= array();	// 기본배송 상품명
		$deliPrdUid		= array();	// 기본배송 상품 UID
		$deliPrt		= "";	// 배송 처리 메세지
		$order_prmsg	= "";	// 주문 메세지
		$deliAreaName	= array();//지역배송 상품명

		foreach( $vendervalueCoupon['products'] as $productCoupon ) {

			$productKEY = $productCoupon['productcode']."_".$productCoupon['opt1_idx']."_".$productCoupon['opt2_idx']."_".$productCoupon['optidxs']."_".$productCoupon['com_idx'];
			$productKEY = str_replace(",","",$productKEY);
			$ordPrdUID = $orderProductUid[$productKEY];

			$order_prmsg = preg_replace($orderpatten,$orderreplace,$productCoupon['productname']);


			// 개별 배송료 정보 시작 ------------------------------------------------------
			if($basketItemsCoupon['deli_individual_fee'][$venderCoupon][$productCoupon['productcode']]['deli_price']>0){
				$tempDeliPrice = $basketItemsCoupon['deli_individual_fee'][$venderCoupon][$productCoupon['productcode']]['deli_price'];

				$deliPrt = "유료배송(". ( $tempDeliPrice > 0 ? number_format($tempDeliPrice)."원" : "무료" ) .", ".$order_prmsg.")  ";

				if($productCompare != $productCoupon['productcode']){
					$deliSQL = "
						INSERT
							tblorderproducttemp
						SET
							vender = ".$venderCoupon.",
							ordercode = '".$ordercode."',
							tempkey = '".$_ShopInfo->getTempkey()."',
							productcode = '99999999991X',
							productname = '배송료(".$deliPrt.")',
							quantity = 1,
							price = ".$tempDeliPrice.",
							reserve = 0,
							date = '".date("Ymd")."',
							order_prmsg = '".$order_prmsg."'
						;
					";
					if (mysql_query($deliSQL,get_db_conn())) {
						$deliUID = mysql_insert_id (get_db_conn());

						$ordPrdUpdate = "UPDATE tblorderproducttemp SET deli_idx = ".$deliUID." WHERE productcode = '".$productCoupon['productcode']."' and ordercode = '".$ordercode."'";
						mysql_query($ordPrdUpdate, get_db_conn());
					}
				}
				
				$productCompare = $productCoupon['productcode'];

				// 개별 배송료 정보 끝 ------------------------------------------------------//
			} else {
				$deliPrdName[] = $order_prmsg;
				$deliPrdUid[] = $ordPrdUID;
			}

			$deliAreaName[] = $order_prmsg;

		}
		//_pr($deliPrdUid);
		$defaultDeliPrice = $basketItemsCoupon['deli_vender_price'][$venderCoupon];
		// 기본 배송비 (무료 제외) 처리 부분.
		if($defaultDeliPrice > 0 ) {

			// 기본 배송비 정보
			$deliPrt = ( $venderCoupon > 0 ? "입점사 " : "" )."기본배송(". ( $basketItemsCoupon['deli_vender_price'][$venderCoupon] > 0 ? number_format($basketItemsCoupon['deli_vender_price'][$venderCoupon])."원" : "무료" ) .")";

			if (count($deliPrdName) > 0) {
				$order_prmsg = implode(",", array_unique($deliPrdName));
			}

			$deliSQL = "
				INSERT
					tblorderproducttemp
				SET
					vender = ".$venderCoupon.",
					ordercode = '".$ordercode."',
					tempkey = '".$_ShopInfo->getTempkey()."',
					productcode = '99999999990X',
					productname = '배송료(".$deliPrt.")',
					quantity = 1,
					price = ".$defaultDeliPrice.",
					reserve = 0,
					date = '".date("Ymd")."',
					order_prmsg = '".$order_prmsg."'
				;
			";

			if (mysql_query($deliSQL,get_db_conn())) {
				$deliUID = mysql_insert_id (get_db_conn());

				$ordPrdUpdate = "UPDATE tblorderproducttemp SET deli_idx = ".$deliUID." WHERE uid in (".implode(",",$deliPrdUid).")";
				mysql_query($ordPrdUpdate, get_db_conn());
			}
			//echo "<hr>배송비 정보 저장 <br> ".$deliSQL;
		}
		if($basketItemsCoupon['deli_area_price'][$venderCoupon]>0){

			if (count($deliAreaName) > 0) {
				$order_prmsg = implode(",", array_unique($deliAreaName));
			}

			$deliPrt = "지역배송(".number_format($basketItemsCoupon['deli_area_price'][$venderCoupon])."원)";

			$deliprice += $basketItemsCoupon['deli_area_price'][$venderCoupon];

			$deliSQL = "
				INSERT
					tblorderproducttemp
				SET
					vender = ".$venderCoupon.",
					ordercode = '".$ordercode."',
					tempkey = '".$_ShopInfo->getTempkey()."',
					productcode = '99999999992X',
					productname = '배송료(".$deliPrt.")',
					quantity = 1,
					price = ".$basketItemsCoupon['deli_area_price'][$venderCoupon].",
					reserve = 0,
					date = '".date("Ymd")."',
					order_prmsg = '".$order_prmsg."'
				;
			";
			mysql_query($deliSQL,get_db_conn());
		}
	}
	// 배송비 정보 저장 끝 --------------------------------------------//

	//echo "<hr>배송료 추가 총결제금액 (+".number_format($basketItemsCoupon['deli_price']).") : ".number_format($sumprice);




	// 주문 마이너스 결제 오류 검토
	if( $sumprice < 1 ) {
		if( $sumprice < 0 OR ( $sumprice == 0 AND $usereserve == 0 AND $coupon_price == 0 ) ) {
			echo "<html><head><title></title></head><body onload=\"alert('죄송합니다.\\n결제 연산이 잘못 되었습니다.\\n재시도 하시기 바랍니다.');history.go(-2);\"></body></html>";
			exit;
		}
	}



	// 임시 주문서 등록 =============================================================================
	if ($paymethod=="B") {
		// 계좌이체시 입금 은행 정보
		$pay_data = $pay_data1;
	} else if (preg_match("/^(C|P)$/", $paymethod)) {
		//
		$pay_data = $pay_data2;
	} else if ($paymethod=="V") {
		$pay_data = "실시간 계좌이체 결제중";
	}
	
	// 회원 그룹 적립/할인관련
	$groupdiscount = 0;
	$groupCheckInfo = &$basketItems['groupMemberSale'];
	if(!_empty($groupCheckInfo['groupCode']) && _isInt($groupCheckInfo['addMoney']) && ($sumprice-$basketItemsCoupon['excp_group_discount'] >= $groupCheckInfo['useMoney']) &&  ($groupCheckInfo['payTypeCode'] == 'N' || ($groupCheckInfo['payTypeCode'] == 'B' && $paymethod == 'B') || ($groupCheckInfo['payTypeCode'] != 'B' && $paymethod == 'C'))){
		$arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"현금/카드");
		$prefix = (substr($groupCheckInfo['groupCode'],0,1) == 'S')?-1:1;
		$dcprice = (substr($groupCheckInfo['groupCode'],1,1) == 'P')?round($sumprice*$groupCheckInfo['addMoney']/100):$groupCheckInfo['addMoney'];
		$groupdiscount = $prefix*$dcprice;
		if($groupdiscount < 0){
			$sumprice+= $groupdiscount;
		}
	}

	// 총배송비를 합계에 더함
	if( $basketItemsCoupon['deli_price'] > 0 ) {
		$sumprice += $basketItemsCoupon['deli_price'];
		$sumprice = ( $sumprice < 0 ) ? 0 : $sumprice;
	}
	
	//211108 :: 배송인 050번호 추가
	$vender_tel = "";
	$sender_tel = "";
	$receiver_tel = "";
	$vender_050_tel="";
	$sender_tel = "";
	$receiver_050_tel = "";
	
	$order_flg = substr($oi_productcode,0,2);
	$sd_tel_sql="SELECT
						mb.mobile AS sender_tel
					FROM
						auction_order ao
						LEFT JOIN tblmember mb ON
						ao.userid = mb.id
					WHERE
						ao.aoidx = ".$oi_aoidx;
	$sd_result = mysql_query($sd_tel_sql,get_db_conn());
	$sd_row = mysql_fetch_object($sd_result);
	$sender_tel = $sd_row->sender_tel;
	
	if ($order_flg == "90") {
		$sp_tel_sql = "SELECT
							vi.com_tel AS vender_tel,
							ao.tel AS receiver_tel
						FROM
							tblvenderinfo vi
							LEFT JOIN auction_order_proposal aop ON
							vi.vender = aop.vender
							LEFT JOIN auction_order ao ON
							aop.aoidx = ao.aoidx
							LEFT JOIN tblproduct prd ON
							aop.pridx = prd.pridx
						WHERE
							ao.aoidx=".$oi_aoidx." AND
							prd.productcode='".$oi_productcode."'";
		$sp_tel_result = mysql_query($sp_tel_sql,get_db_conn());
		$sp_tel_row = mysql_fetch_object($sp_tel_result);
		
		$vender_tel = $sp_tel_row->vender_tel;
		$receiver_tel = $sp_tel_row->receiver_tel;
	} else {
		$ts_v_tel_sql = "SELECT
								vi.com_tel AS vender_tel
							FROM
								todaysale ts
								LEFT JOIN tblvenderinfo vi ON
								ts.vender = vi.vender
								LEFT JOIN tblproduct prd ON
								ts.pridx = prd.pridx
							WHERE
								prd.productcode = '".$oi_productcode."'";
		$ts_v_result = mysql_query($ts_v_tel_sql,get_db_conn());
		$ts_v_row = mysql_fetch_object($ts_v_result);
		$vender_tel = $ts_v_row->vender_tel;

		$ts_m_sql = "SELECT mobile AS receiver_tel FROM tblmember WHERE id = '".$id."'";
		$ts_m_result = mysql_query($ts_m_sql,get_db_conn());
		$ts_m_row = mysql_fetch_object($ts_m_result);
		$receiver_tel = $ts_m_row->receiver_tel;
	}
	
	//050번호 등록
	if (strlen($vender_tel) > 0) {
		$response = set050tel("make",$vender_tel, "");
		$vender_050_tel = $response->vn;
	}
	
	if (strlen($sender_tel) > 0) {
		$response = set050tel("make",$sender_tel, "");
		$sender_050_tel = $response->vn;
	}
	
	if (strlen($receiver_tel) > 0) {
		$response = set050tel("make",$receiver_tel, "");
		$receiver_050_tel = $response->vn;
	}
	
	$tblorderinfotempSQL = "
		INSERT
			tblorderinfotemp
		SET
			ordercode = '".$ordercode."',
			tempkey = '".$_ShopInfo->getTempkey()."',
			id = '".$id."',
			price = ".$sumprice.",
			deli_price = ".$deliprice.",
			dc_price = '".$groupdiscount."',
			reserve = ".$usereserve.",
			paymethod = '".$pmethod."',
			pay_data = '".$pay_data."',
			sender_name = '".$sender_name."',
			sender_email = '".$sender_email."',
			sender_tel = '".$sender_tel."',
			vender_050_tel = '".$vender_050_tel."',
			sender_050_tel = '".$sender_050_tel."',
			receiver_050_tel = '".$receiver_050_tel."',
			receiver_name = '".$receiver_name."',
			receiver_tel1 = '".$receiver_tel1."',
			receiver_tel2 = '".$receiver_tel2."',
			receiver_addr = '".$receiver_addr."',
			order_msg = '".$_POST['order_prmsg']."',
			ip = '".$ip."',
			del_gbn = '',
			partner_id = '".$_ShopInfo->getRefurl()."',
			loc = '".$loc."',
			order_type = '".$ordertype."',
			receiver_email = '".$receiver_email."',
			receiver_message = '".$receiver_message."',

			HP1 = '".$HP1."',
			HP2 = '".$HP2."',
			HP3 = '".$HP3."',
			Account1 = '".$Account1."',
			Account2 = '".$Account2."',
			Account3 = '".$Account3."',

			device = 'M',
			bankname = '".$_REQUEST['bankname']."'
	";
	if($sumprice==0) {
		$tblorderinfotempSQL.= ", bank_date = '".date("YmdHis")."' ";
		if(preg_match("/^(O|Q)$/", $paymethod)) $tblorderinfotempSQL.= ", pay_flag = '0000', ";	//가상계좌만,,,
	}
	
	$res = mysql_query($tblorderinfotempSQL,get_db_conn()) or die(mysql_error());

	//echo "<hr>주문 정보 등록<br> ".$tblorderinfotempSQL;

	// 사은품 insert 처리		2015-03-20
	//$resultsumprice = $sumprice - $usereserve;
	$sumgiftprice = $basketItems['gift_price'];
	if(_array($giftinfo)){
	
		//금액에 맞지 않는 사은품 지금 제외 적용
		if(strlen($coupon_code)>0 && $coupon_ok !="A"){
			$sumgiftprice -= $coupon_price;
		}
		if(strlen($usereserve) > 0 && $usereserve > 0){
			$sumgiftprice -= $usereserve;
		}
		if(intval($giftinfo['gift_startprice']) > $sumgiftprice){
			gobackError($ordercode,'사은품 선택가능한 금액이 아닙니다.');
			exit;
		}
		// 160517 주석처리 $sumprice 주문 총 가격은 적립금 뺀 금액인데 또 빼서 체크하면 오류남.
		//금액에 맞지 않는 사은품 지금 제외 적용
		//if(intval($giftinfo['gift_startprice']) > $resultsumprice){
		//	gobackError($ordercode,'사은품 선택 가능금액과 선택 사은품간에 오류가 있습니다.');
		//	exit;
		//}

		$sql = "INSERT tblorderproducttemp SET ";
		$sql.= "ordercode	= '".$ordercode."', ";
		$sql.= "tempkey		= '".$_ShopInfo->getTempkey()."', ";
		$sql.= "productcode	= '99999990GIFT', ";
		$sql.= "productname	= '사은품 - ".addslashes($giftinfo['gift_name'])."', ";
		$sql.= "opt1_name	= '".$giftinfo['selopt'][1]."', ";
		$sql.= "opt2_name	= '".$giftinfo['selopt'][2]."', ";
		$sql.= "opt3_name	= '".$giftinfo['selopt'][3]."', ";
		$sql.= "opt4_name	= '".$giftinfo['selopt'][4]."', ";
		$sql.= "quantity	= ".intval($giftinfo['gift_quantity']).", ";
		$sql.= "selfcode	= '".$giftinfo['gift_regdate']."', ";
		$sql.= "price		= 0, ";
		$sql.= "order_prmsg	= '".$gift_msg."', ";
		$sql.= "date		= '".date("Ymd")."' ";
		mysql_query($sql,get_db_conn());

		if(intval($giftinfo['gift_quantity'])>0) mysql_query("UPDATE tblgiftinfo SET gift_quantity=gift_quantity-".intval($giftinfo['gift_quantity'])." WHERE gift_regdate='".$giftinfo['gift_regdate']."'",get_db_conn());

		$upStr = array();
		for($g=1;$g<=4;$g++){
			if(_array($giftinfo['options'][$g])){
				$tmparr = array();
				foreach($giftinfo['options'][$g][1] as $gk=>$gv) array_push($tmparr,$gk.':'.$gv);
				$giftinfo['options'][$g][1] = implode(',',$tmparr);
				array_push($upStr,'gift_option'.$g."='".implode(',',$giftinfo['options'][$g]));
			}
		}
		if(_array($upStr)) mysql_query("UPDATE tblgiftinfo SET ".implode(',',$upStr)." WHERE gift_regdate='".$giftinfo['regdate']."'",get_db_conn());
		unset($giftinfo);
	}

	// PG 결제금액 관련 변수
	$SupplyAmt = $vat = 0;
	$last_price = $sumprice; // 결제액
	$groupdiscount_Percent = round ( 100 - ( 100 * ( $sumprice / $basketItems['sumprice'] ) ) ); //회원그룹(추가)할인 비율 %
	$taxfree_groupdiscount = round($basketItems['tax_free']*($groupdiscount_Percent/100)); // 면세 회원그룹(추가)할인
	$tax_free = round(($basketItems['tax_free'] - $taxfree_groupdiscount)/100)*100; // 면세액
	$SupplyAmtTmp = $sumprice - $tax_free;
	if( $SupplyAmtTmp > 0 ) {
		$vat = round( $SupplyAmtTmp / 11 ); // 과세 세액
		$SupplyAmt = $SupplyAmtTmp - $vat; // 과세결제액(세액을 제외한 금액)
	}

	// 전체 면세 상품일 경우 결제 액과 면세상품액이 다르면 동기화
	if( $SupplyAmt == 0 AND $vat == 0 AND $last_price != $tax_free ){
		$tax_free = $last_price;
	}

	if($paymethod!="B") {

		$martid = "";
		$martkey = "";
		switch($pg_type){
			case "A":
				$includefile = "./paylist.php";
			break;
			case "B":
				$includefile = "./paygate/B/payreq.php";
			break;
			case "D":
				$includefile = "./paygate/D/charge.php";
			break;
			case "E":
//				$includefile = "./paygate/E/charge.php";
//				$martid = $pgid_info['ID'];
//				$martkey = $pgid_info['KEY
				$includefile = "./paygate/E_SMART/payRequest.php";
				$martid = "dongne202m";
				$martkey = "NWkaNWJ9rqaAF0lQlzmersm2g9YL+/gCdH/RCHkNYe0udaVEa9crTN7Dkw2the2YBGjQP1RG8fgNk6ZUpZ9L8A==";
			break;
			case "G":
				$includefile = "./paygate/G/charge.php";
			break;
		}
		########### 결제시스템 연결 시작 ##########
		include($includefile);
		exit;
		########### 결제시스템 연결 끝   ##########

		// 무통장 입금이 아닌 경우 결제 모듈로 연동
		//include($Dir.MobileDir."paylist.php");
		//exit;

	}


// 무통장 입금인 경우는 바로 주문 처리 완료
include($Dir."/app/payresult.php");

?>

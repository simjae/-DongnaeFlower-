<?php
$Dir="../../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include "./allatutil.php";

$ordercode		= !_empty($_POST['ordercode'])		? trim($_POST['ordercode'])		: "";
$return_host	= !_empty($_POST['return_host'])	? trim($_POST['return_host'])	: "";
$return_script	= !_empty($_POST['return_script'])	? trim($_POST['return_script'])	: "";
$return_data	= !_empty($_POST['return_data'])	? trim($_POST['return_data'])	: "";
$return_type	= !_empty($_POST['return_type'])	? trim($_POST['return_type'])	: "";
$CancelAmt		= !_empty($_POST['CancelAmt'])		? trim($_POST['CancelAmt'])		: 0;

$returnurl = $return_script."?".$return_data;

$orderlogSQL = "SELECT paymethod FROM tblpordercode WHERE ordercode = '".$ordercode."' ";
$orderlogcount=0;
if(false !== $orderlogRes = mysql_query($orderlogSQL,get_db_conn())){
	$orderlogcount= mysql_num_rows($orderlogRes);
	if($orderlogcount>0){
		$paymethod = mysql_result($orderlogRes,0,0);
	}else{
		echo '<script>alert("해당 거래정보가 없어 거부 되었습니다.");location.replace("'.$returnurl.'");</script>';exit;
	}
	mysql_free_result($orderlogRes);
}else{
	echo '<script>alert("주문 정보가 없어 처리가 거부 되었습니다.");location.replace("'.$returnurl.'");</script>';exit;
}


$_ShopInfo->getPgdata();
$pginfo=array();
if(strlen($_data->trans_id)>0){
	$pginfo = GetEscrowType($_data->trans_id);
	$at_shop_id = trim($pginfo['ID']);
	$at_cross_key= trim($pginfo['KEY']);
}else{
	echo '<script>alert("PG가 연동되지 않아 사용할 수 없습니다.");location.replace("'.$returnurl.'");</script>';exit;
}

switch($paymethod){
	case "C":
	case "P":
		$tablename = "tblpcardlog";
		$at_pay_type = "CARD";
	break;
	case "M":
		$tablename = "tblpmobilelog";
		$at_pay_type = "HP";
	break;
	default:
		echo '<script>alert("취소가능 거래가 아닙니다.\n취소가능 거래 : 신용카드,휴대폰");location.replace("'.$returnurl.'");</script>';exit;
	break;
}
//데이터 설정
$at_enc=setValue($at_enc,"allat_shop_id",$at_shop_id);
$at_enc=setValue($at_enc,"allat_order_no",$ordercode);
$at_enc=setValue($at_enc,"allat_amt",$CancelAmt);
$at_enc=setValue($at_enc,"allat_pay_type",$at_pay_type);

$at_data =	"allat_shop_id=".$at_shop_id.
			"&allat_enc_data=".$at_enc.
			"&allat_cross_key=".$at_cross_key;

$at_txt = CancelReq($at_data,"SSL");
$repcode =getValue("reply_cd",$at_txt);
$repmsg =getValue("reply_msg",$at_txt);

if(!strcmp($repcode,"0000")){
	$logwriteSQL = "UPDATE ".$tablename." SET ";
	$logwriteSQL.= "ok			= 'C', ";
	$logwriteSQL.= "canceldate	= '".date("YmdHis")."' ";
	$logwriteSQL.= "WHERE ordercode='".$ordercode."' ";

	if(false !== mysql_query($logwriteSQL,get_db_conn())){

		// 160617 원래 관리자 주문 상세 페이지에 있는 구문을 모바일에서는 여기서 처리
		//카드승인/취소,  핸드폰결제 취소 처리 (결제서버에서 호출)
		$sql = "UPDATE tblorderinfo SET pay_admin_proc='C' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());

		if ($paymethod == "P") {
			$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='".$ordercode."' AND MID(paymethod,1,1)='P'";
			if(mysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
				$sql.= "WHERE ordercode='".$ordercode."' ";
				$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
				mysql_query($sql,get_db_conn());
			}
		}

		echo '<script>alert("취소처리가 정상적으로 처리되었습니다.\n올앳 관리자 페이지에서 취소여부를 확인하시기 바랍니다.");location.replace("'.$returnurl.'");</script>';

	}else{
		echo '<script>alert("취소처리는 정상적으로 되었으나, 쇼핑몰에 반영되지 않았습니다.\n 관리자에게 문의 하시기 발바니다.");location.replace("'.$returnurl.'");</script>';exit;
	}

}else{
	echo '<script>alert("취소처리가 거부되었습니다.\n잠시후 다시 시도해 주세요.");location.replace("'.$returnurl.'");</script>';exit;
}

?>
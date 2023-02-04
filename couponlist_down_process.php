<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$mode=$_REQUEST["mode"];
	$coupon_code=$_REQUEST["coupon_code"];
	$download_flg=$_REQUEST["download_flg"];
	$download_date=$_REQUEST["download_date"];

	$code=$_REQUEST["code"];
	$productcode=$_REQUEST["productcode"];

	/*if($mode=="coupon" && strlen($coupon_code)==8 ) {	//쿠폰 발급
		if(strlen($_ShopInfo->getMemid())==0) {	//비회원
			echo "<html></head><body onload=\"alert('로그인 후 쿠폰 다운로드가 가능합니다.'); parent.location.href='./login.php?chUrl=/front/couponlist.php';\"></body></html>";exit;
		} else {
				if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
					echo "<script>alert(\"모든 쿠폰이 발급되었습니다.\"); location.href='about:blank';</script>";
				} else {
					$date=date("YmdHis");
					if($row->date_start>0) {
						$date_start=$row->date_start;
						$date_end=$row->date_end;
					} else {
						$date_start = substr($date,0,10);
						$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
					}
					$sql = "INSERT tblcouponissue SET ";
					$sql.= "coupon_code	= '".$coupon_code."', ";
					$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
					$sql.= "date_start	= '".$date_start."', ";
					$sql.= "date_end	= '".$date_end."', ";
					$sql.= "date		= '".$date."' ";
					mysql_query($sql,get_db_conn());
					if(!mysql_errno()) {
						$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
						$sql.= "WHERE coupon_code = '".$coupon_code."'";
						mysql_query($sql,get_db_conn());

						echo "<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\"); location.href='about:blank';</script>";
					} else {
						if($row->repeat_id=="Y") {	//동일인 재발급이 가능하다면,,,,
							$sql = "UPDATE tblcouponissue SET ";
							if($row->date_start<=0) {
								$sql.= "date_start	= '".$date_start."', ";
								$sql.= "date_end	= '".$date_end."', ";
							}
							$sql.= "used		= 'N' ";
							$sql.= "WHERE coupon_code='".$coupon_code."' ";
							$sql.= "AND id='".$_ShopInfo->getMemid()."' ";
							mysql_query($sql,get_db_conn());
							echo "<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\"); location.href='about:blank';</script>";
						} else {
							echo "<script>alert(\"이미 쿠폰을 발급받으셨습니다.\\n\\n해당 쿠폰은 재발급이 불가능합니다.\"); location.href='about:blank';</script>";
						}
					}
				}
			mysql_free_result($result);
		}
	}*/
	if($mode=="coupon" && strlen($coupon_code)==8 ) {	//쿠폰 발급
		if(strlen($_ShopInfo->getMemid())==0) {	//비회원
			echo "<html></head><body onload=\"alert('로그인 후 쿠폰 다운로드가 가능합니다.'); parent.location.href='./login.php?chUrl=/front/couponlist.php';\"></body></html>";exit;
		} else {
				$cInfoSql = "SELECT * FROM tblcouponinfo WHERE coupon_code = '".$coupon_code."' ";
				if(false !== $cRes = mysql_query($cInfoSql, get_db_conn())){
					$row = mysql_fetch_object($cRes);
					if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
						echo "<script>alert(\"모든 쿠폰이 발급되었습니다.\"); parent.href='mypage_coupon.php?pageType=myCoupon';</script>";
					} else {
						$date=date("YmdHis");
						if ($download_flg == 'N') {
							if($row->date_start>0) {
								$date_start=$row->date_start;
								$date_end=$row->date_end;
							} else {
								$date_start = substr($date,0,10);
								$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
							}
						} else {
							$date_start = substr($date,0,10);
							$date_end = date("Ymd", mktime(0,0,0,substr($date,4,2), substr($date,6,2) + $download_date, substr($date,0,4)) )."23";
						}
						$sql = "INSERT tblcouponissue SET ";
						$sql.= "coupon_code	= '".$coupon_code."', ";
						$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
						$sql.= "date_start	= '".$date_start."', ";
						$sql.= "date_end	= '".$date_end."', ";
						$sql.= "date		= '".$date."' ";
						mysql_query($sql,get_db_conn());
						if(!mysql_errno()) {
							$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
							$sql.= "WHERE coupon_code = '".$coupon_code."'";
							mysql_query($sql,get_db_conn());
							
							echo "<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\"); parent.location.reload();</script>";
						} else {
							if($row->repeat_id=="Y") {	//동일인 재발급이 가능하다면,,,,
								$sql = "UPDATE tblcouponissue SET ";
								if($row->date_start<=0) {
									$sql.= "date_start	= '".$date_start."', ";
									$sql.= "date_end	= '".$date_end."', ";
								}
								$sql.= "used		= 'N' ";
								$sql.= "WHERE coupon_code='".$coupon_code."' ";
								$sql.= "AND id='".$_ShopInfo->getMemid()."' ";
								mysql_query($sql,get_db_conn());
								echo "<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\"); parent.location.reload();</script>";
							} else {
								echo "<script>alert(\"이미 쿠폰을 발급받으셨습니다.\\n\\n해당 쿠폰은 재발급이 불가능합니다.\"); location.href='about:blank';</script>";
							}
						}
					}
				mysql_free_result($cRes);
			}
		}
	}


?>
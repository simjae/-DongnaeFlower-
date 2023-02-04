<?
//if(getenv("SERVER_ADDR")!=getenv("REMOTE_ADDR")) exit;


$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata2.php");

//print_r($_POST);

$ordercode=$_POST["ordr_idxx"];
//kcp res_cd
//$pay_flag=$_POST["pay_flag"];
$pay_flag=$_POST["res_cd"];
$pay_auth_no=$_POST["pay_auth_no"];
$pay_data=$_POST["pay_data"];
$real_price=$_POST["real_price"];
$message=$_POST["message"];
$deli_gbn=$_POST["deli_gbn"];
if (strlen($deli_gbn)==0) $deli_gbn="N";


if(strlen($ordercode)>0) {
	mysql_query("INSERT INTO tblorderinfo SELECT * FROM tblorderinfotemp WHERE ordercode='".$ordercode."'",get_db_conn());

	if (mysql_errno()!=1062) $mysql_errno+=mysql_errno();
	if(!mysql_errno()) mysql_query("DELETE FROM tblorderinfotemp WHERE ordercode='".$ordercode."'",get_db_conn());

	mysql_query("INSERT INTO tblorderproduct SELECT * FROM tblorderproducttemp WHERE ordercode='".$ordercode."'",get_db_conn());
	if (mysql_errno()!=1062) $mysql_errno+=mysql_errno();
	if(!mysql_errno()) mysql_query("DELETE FROM tblorderproducttemp WHERE ordercode='".$ordercode."'",get_db_conn());

	mysql_query("INSERT INTO tblorderoption SELECT * FROM tblorderoptiontemp WHERE ordercode='".$ordercode."'",get_db_conn());
	if (mysql_errno()!=1062) $mysql_errno+=mysql_errno();
	if(!mysql_errno()) mysql_query("DELETE FROM tblorderoptiontemp WHERE ordercode='".$ordercode."'",get_db_conn());

	if ($mysql_errno!=0) { echo "no"; exit; }
}

$sql="SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$tempkey=$row->tempkey;
	$id=$row->id;
	$pay_flag_check = $row->pay_flag;
	$paymethod=substr($row->paymethod,0,1);
	$pg_type=substr($row->paymethod,1,1);
	$last_price = $row->price;
	$user_reserve=$row->reserve;
	$delflag=$row->del_gbn;
}
mysql_free_result($result);


if (!preg_match("/^(V|O|Q|C|P|M)$/", $paymethod)) { echo "no";exit; }

//############ 카드 승인/실패 업데이트 ############
if (strcmp($pay_flag_check,"0000")!=0 && strlen($pay_flag)>0) {
	#추후 여러 PG사 이용할 경우 PG사에 따라 결과코드를 구분하기 위하여
	if (strcmp($pay_flag,"0000")==0) {
		$auto_pay_admin_proc="N";
		if(preg_match("/^(A|B|C|D)$/", $pg_type)) {	//KCP/DACOM/ALLTHEGATE/INIPAY 경우 자동매입이기 때문에 아래 세팅
			$auto_pay_admin_proc="Y";
		}
	} else {

	}
	if ($pay_flag=="0000" && $last_price!=$real_price) {
		sendmail(AdminMail,"[결제] 승인금액이 맞지않음 (".$paymethod.")","주문금액 : $last_price\n승인금액 : $real_price\n\nordercode=$ordercode\npay_data=$pay_data","Content-Type: text/plain\r\n");
	}

	if(preg_match("/^(C|P)$/", $paymethod)) {	//신용카드
		$sql = "UPDATE tblorderinfo SET pay_flag='".$pay_flag."', pay_auth_no='".$pay_auth_no."', ";
		$sql.= "pay_data='".$pay_data."', deli_gbn='".$deli_gbn."' ";
		if($auto_pay_admin_proc=="Y") $sql.= ", pay_admin_proc='Y' ";
	} else if(preg_match("/^(O|Q)$/", $paymethod)) {	//가상계좌
		$sql = "UPDATE tblorderinfo SET pay_flag='".$pay_flag."', ";
		$sql.= "pay_data='".$pay_data."', deli_gbn='".$deli_gbn."' ";
	} else if(preg_match("/^(M)$/", $paymethod)) {	//휴대폰
		$sql = "UPDATE tblorderinfo SET pay_flag='".$pay_flag."', ";
		$sql.= "pay_data='".$pay_data."', deli_gbn='".$deli_gbn."' ";
	} else if(preg_match("/^(V)$/", $paymethod)) {	//계좌이체
		$sql = "UPDATE tblorderinfo SET pay_flag='".$pay_flag."', ";
		$sql.= "pay_data='".$pay_data."', deli_gbn='".$deli_gbn."' ";
	}
	if($deli_gbn=="C") $sql.= ",del_gbn='R' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	if(mysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='".$deli_gbn."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		$sql.= "AND NOT (productcode LIKE 'COU%' AND productcode LIKE '999999%') ";
		mysql_query($sql,get_db_conn());
	}
	$mysql_errno+=mysql_errno();

} else if (strcmp($pay_flag_check,"0000")!=0 && strlen($ordercode)>0 && $deli_gbn=="C") {
	$sql = "UPDATE tblorderinfo SET ";
	$sql.= "deli_gbn	= '".$deli_gbn."', ";
	$sql.= "pay_data		= '결제정보 작성 중 주문취소' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	if(mysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='".$deli_gbn."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		$sql.= "AND NOT (productcode LIKE 'COU%' AND productcode LIKE '999999%') ";
		mysql_query($sql,get_db_conn());
	}
	$mysql_errno += mysql_errno();
}



if (preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")!=0 && strlen($delflag)==0) {	//주문실패
	$sql = "SELECT a.option_quantity,b.productcode,b.opt1_idx,b.opt2_idx,b.quantity ";
	$sql.= "FROM tblproduct a, tblbasket b WHERE b.tempkey='".$tempkey."' ";
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
	$sql = "SELECT quantity, productcode, package_idx, assemble_idx, assemble_info FROM tblorderproduct ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
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

	// 적립금 환원
	if (strlen($user_reserve)>0 && $user_reserve>0 && strlen($delflag)==0) {
		$sql = "UPDATE tblmember SET reserve = reserve + ".abs($user_reserve)." ";
		$sql.= "WHERE id='".$id."' ";
		mysql_query($sql,get_db_conn());
	}
	// 주문서의 쿠폰을 다시 사용가능하게 변경함
	$sql = "SELECT productcode FROM tblorderproduct WHERE ordercode='".$ordercode."' AND productcode LIKE 'COU%' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)){
		$coupon_code=substr($row->productcode,3,-2);
		mysql_query("UPDATE tblcouponissue SET used='N' WHERE id='".$id."' AND coupon_code='".$coupon_code."'",get_db_conn());
	}

} else if((preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")==0 && strlen($ordercode)>0) && (strlen($delflag)==0 || $delflag=="R")) {	//주문성공
	// 주문성공시 적립금차감/쿠폰 차감.
	if($_data->reserve_maxuse>=0 && strlen($user_reserve)>0 && $user_reserve>0) {
		$sql = "INSERT tblreserve SET ";
		$sql.= "id			= '".$id."', ";
		$sql.= "reserve		= -$user_reserve, ";
		$sql.= "reserve_yn	= 'N', ";
		$sql.= "content		= '물품 주문시 적립금 사용', ";
		$sql.= "orderdata	= '".$ordercode."=".$last_price."', ";
		$sql.= "date		= '".date("YmdHis")."' ";
		mysql_query($sql,get_db_conn());
	}

	if($delflag=="R") {	//배치프로그램에 의해 수량이 복구가 된 경우
		if (strlen($user_reserve)>0 && $user_reserve>0) {
			$sql = "UPDATE tblmember SET reserve=if(reserve<".abs($user_reserve).",0,reserve-".abs($user_reserve).") ";
			$sql.= "WHERE id='".$id."' ";
			mysql_query($sql,get_db_conn());
		}
		// 주문서의 쿠폰을 다시 사용가능하게 변경함
		$sql = "SELECT productcode FROM tblorderproduct WHERE ordercode='".$ordercode."' AND productcode LIKE 'COU%' ";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)){
			$coupon_code=substr($row->productcode,3,-2);
			mysql_query("UPDATE tblcouponissue SET used='Y' WHERE id='".$id."' AND coupon_code='".$coupon_code."'",get_db_conn());
		}

		$sql = "SELECT a.productname,a.productcode,a.quantity,a.opt1_name,a.opt2_name,b.option_quantity, ";
		$sql.= "b.option1,b.option2,a.package_idx,a.assemble_idx,a.assemble_info FROM tblorderproduct a, tblproduct b ";
		$sql.= "WHERE a.ordercode='".$ordercode."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$result = mysql_query($sql,get_db_conn());
		$mess="";
		while($row=mysql_fetch_object($result)) {
			$tempoption_quantity="";
			if(strlen($artempoption_quantity[$row->productcode])>0) $srcoption_quantity=$artempoption_quantity[$row->productcode];
			else $srcoption_quantity=$row->option_quantity;

			if(strlen($srcoption_quantity)>51 && substr($row->opt1_name,0,5)!="[OPTG"){
				$tempopt1_name=explode(" : ",$row->opt1_name);
				$tempopt2_name=explode(" : ",$row->opt2_name);
				$tempoption1=explode(",",$row->option1);
				$tempoption2=explode(",",$row->option2);
				$cnt=1;
				$maxoption_quantity = count($tempoption1);
				while ($tempoption1[$cnt]!=$tempopt1_name[1] && $cnt<$maxoption_quantity) {
					$cnt++;
				}
				$option1num=$cnt;
				$cnt=1;
				$maxoption_quantity2 = count($tempoption2);
				while ($tempoption2[$cnt]!=$tempopt2_name[1] && $cnt<$maxoption_quantity2) {
					$cnt++;
				}
				$option2num=$cnt;
				$optioncnt = explode(",",substr($srcoption_quantity,1));
				if($optioncnt[($option2num-1)*10+($option1num-1)]!="") $optioncnt[($option2num-1)*10+($option1num-1)]-=$row->quantity;
				for($j=0;$j<5;$j++){
					for($i=0;$i<10;$i++){
						$tempoption_quantity.=",".$optioncnt[$j*10+$i];
					}
				}
				if(strlen($tempoption_quantity)>0 && $tempoption_quantity.","!=$srcoption_quantity){
					$artempoption_quantity[$row->productcode]=$tempoption_quantity;
					$tempoption_quantity=",option_quantity='".$tempoption_quantity.",'";
				}else{
					$tempoption_quantity="";
					$mess .="[".$row->productname." - ".$row->opt1_name.$row->opt2_name."]\\n";
				}
			}
			$sql2 = "UPDATE tblproduct SET quantity=quantity-".$row->quantity.$tempoption_quantity." ";
			$sql2.= "WHERE productcode='".$row->productcode."'";
			$result2 = mysql_query($sql2,get_db_conn());

			if(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info)))) {
				$assemble_infoall_exp = explode("=",$row->assemble_info);

				if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
					$package_info_exp = explode(":",$assemble_infoall_exp[0]);
					if(strlen($package_info_exp[0])>0) {
						$package_productcode_exp = explode("",$package_info_exp[0]);
						for($k=0; $k<count($package_productcode_exp); $k++) {
							$sql2 = "UPDATE tblproduct SET ";
							$sql2.= "quantity		= quantity-".$row->quantity." ";
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
							$sql2.= "quantity		= quantity-".$row->quantity." ";
							$sql2.= "WHERE productcode='".$assemble_productcode_exp[$k]."' ";
							mysql_query($sql2,get_db_conn());
						}
					}
				}
			}
		}
	}

	$sql="UPDATE tblorderinfo SET del_gbn='N' WHERE ordercode='".$ordercode."'";
	mysql_query($sql,get_db_conn());
}

if ($mysql_errno!=0) { echo $mysql_errno;exit; }
?>
<script>
alert("결제가 완료되었습니다");
location.href="./";
</script>
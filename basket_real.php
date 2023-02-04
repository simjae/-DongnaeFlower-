<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;

/// POST INPUT
$mode=$_POST["mode"];
$code=$_POST["code"];
$ordertype=$_REQUEST["ordertype"];	//바로구매 구분 (바로구매시 => ordernow)
$opts=$_POST["opts"];	//옵션그룹 선택된 항목 (예:1,1,2,)
$option1=$_POST["option1"];	//옵션1
$option2=$_POST["option2"];	//옵션2
$optidxs=$_POST["opt_idx"];
$optidxs2=$_POST["opt_idx2"];
$opt_quantity=$_POST["opt_quantity"];

$quantity=(int)$_REQUEST["quantity"];	//구매수량
if($quantity==0) $quantity=1;
$productcode=$_REQUEST["productcode"];

//옵션 사용여부 2016-10-04 Seul
$optClass->setOptUse($productcode);
$opt_comidx = (isset($_POST['opt_comidx']))?$_POST['opt_comidx']:0;

$orgquantity=$_POST["orgquantity"];
$orgoption1=$_POST["orgoption1"];
$orgoption2=$_POST["orgoption2"];

$assemble_type=$_POST["assemble_type"];
$assemble_list=@str_replace("|","",$_POST["assemble_list"]);
$assembleuse=$_POST["assembleuse"];
$assemble_idx=(int)$_POST["assemble_idx"];

$package_idx=(int)$_POST["package_idx"];

$sell_memid = $_POST["sell_memid"];

//위시리스트에서 배열로 넘어온 경우
$sels=(array)$_POST["sels"];
$wish_idx=$_POST["wish_idx"];

// 주문타입별 장바구니 테이블
$basket = basketTable($ordertype);

if(strlen($_ShopInfo->getMemid())==0) {	//비회원
	$basketWhere = "tempkey='".$_ShopInfo->getTempkey()."'";
}else{
	$basketWhere = "id='".$_ShopInfo->getMemid()."'";
}

if( $ordertype != "" ){
	// 160129 장바구니 제외 바스켓 디비 정리용 쿼리. 회원일 경우 회원 아이디 또는, 템프키로 모두 삭제처리.
	$where = "";
	if (strlen($_ShopInfo->getMemid()) > 0) {
		$where .= "id='{$_ShopInfo->getMemid()}' or ";
	}
	$where .= "tempkey='{$_ShopInfo->getTempkey()}'";
	$sql = "DELETE FROM ".$basket." WHERE ".$where;
	mysql_query($sql,get_db_conn());
}

if($assemble_idx==0) {
	if($assembleuse=="Y") {
		$assemble_idx="99999";
	}
} else {
	$assembleuse="Y";
}


//장바구니 인증키 확인
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}

//sns 홍보 본인체크
if(strlen($_ShopInfo->getMemid()) > 0){
	$sql ="UPDATE ".$basket." SET sell_memid ='' WHERE ".$basketWhere." AND sell_memid='".$_ShopInfo->getMemid()."'";
	mysql_query($sql,get_db_conn());
}

if(strlen($productcode)==18) {
	if(strlen($code)==0) {
		$code=substr($productcode,0,12);
	}
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);
	if(strlen($codeA)!=3) $codeA="000";
	if(strlen($codeB)!=3) $codeB="000";
	if(strlen($codeC)!=3) $codeC="000";
	if(strlen($codeD)!=3) $codeD="000";

	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->group_code=="NO") {	//숨김 분류
			echo json_encode(array(
				"result" => "err",
				"code" => 1000,
				"msg" => "판매가 종료된 상품입니다."
			));
			exit;
		} else if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			echo json_encode(array(
				"result" => "err",
				"code" => 1001,
				"msg" => "로그인 하셔야 장바구니에 담으실 수 있습니다."
			));
			exit;
		} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			echo json_encode(array(
				"result" => "err",
				"code" => 1002,
				"msg" => "해당 분류의 접근 권한이 없습니다."
			));
			exit;
		}

		//Wishlist 담기
		if($mode=="wishlist") {
			if(strlen($_ShopInfo->getMemid())==0) {	//비회원
				echo json_encode(array(
					"result" => "err",
					"code" => 2000,
					"msg" => "로그인 하셔야 장바구니에 담으실 수 있습니다."
				));
				exit;
			}
			$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$result2=mysql_query($sql,get_db_conn());
			$row2=mysql_fetch_object($result2);
			$totcnt=$row2->totcnt;
			mysql_free_result($result2);
			$maxcnt=20;
			if($totcnt>=$maxcnt) {
				$sql = "SELECT b.productcode FROM tblwishlist a, tblproduct b ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
				$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
				$sql.= "AND b.display='Y' ";
				$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
				$sql.= "GROUP BY b.productcode ";
				$result2=mysql_query($sql,get_db_conn());
				$i=0;
				$wishprcode="";
				while($row2=mysql_fetch_object($result2)) {
					$wishprcode.="'".$row2->productcode."',";
					$i++;
				}
				mysql_free_result($result2);
				$totcnt=$i;
				$wishprcode=substr($wishprcode,0,-1);
				if(strlen($wishprcode)>0) {
					$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode NOT IN (".$wishprcode.") ";
					mysql_query($sql,get_db_conn());
				}
			}
			if($totcnt<$maxcnt) {
				//다중옵션선택시
				$optidxs_count = sizeof( $opt_comidx );
				$wishlistCnt = 0;
				if($optidxs_count>0 && is_array($opt_comidx)){
					for( $i = 0; $i < $optidxs_count; $i++ ){

						$sql = "SELECT * FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."'";
						$sql.= " AND productcode='".$productcode."'";
						$sql.= " AND com_idx=".$opt_comidx[$i]."";
						$result = mysql_query($sql,get_db_conn());
						$wishlist_row=mysql_fetch_object($result);
						mysql_free_result($result);

						if($wishlist_row){
							$wishlistCnt++;
						} else {
							$sql = "INSERT tblwishlist SET ";
							$sql.= "productcode		= '".$productcode."', ";
							$sql.= "com_idx			= ".$opt_comidx[$i].", ";
							$sql.= "id				= '".$_ShopInfo->getMemid()."' ";
							mysql_query($sql,get_db_conn());
						}
					}
					if($wishlistCnt > 0){
						echo json_encode(array(
							"result" => "err",
							"code" => 2001,
							"msg" => "이미 위시리스트에 ".$wishlistCnt."개의 상품이 담겨있습니다."
						));
						exit;
					}
				} else {
					$sql = "SELECT * FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."'";
					$sql.= " AND productcode='".$productcode."'";
					$sql.= " AND com_idx=".$opt_comidx."";
					$result = mysql_query($sql,get_db_conn());
					$wishlist_row=mysql_fetch_object($result);
					mysql_free_result($result);

					if($wishlist_row){
						echo json_encode(array(
							"result" => "err",
							"code" => 2002,
							"msg" => "이미 위시리스트에 상품이 담겨있습니다."
						));
						exit;
					} else {
						$sql = "INSERT tblwishlist SET ";
						$sql.= "productcode		= '".$productcode."', ";
						//찜하기 기능 수정함 (옵션없는 상품도 등록되도록) 2016-07-21 Seul
						if(strlen($opt_comidx)>0) {
							$sql.= "com_idx		= ".$opt_comidx.", ";
						}
						$sql.= "id				= '".$_ShopInfo->getMemid()."' ";
						mysql_query($sql,get_db_conn());
					}
				}
				echo json_encode(array(
					"result" => "ok_wishlist",
					"code" => 2003,
					"msg" => $sql
				));
				exit;
			} else {
				echo json_encode(array(
					"result" => "err",
					"code" => 2004,
					"msg" => "WishList에는 ".$maxcnt."개 까지만 등록이 가능합니다.\n\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다."
				));
				exit;
			}
		}
	} else {
		echo json_encode(array(
			"result" => "err",
			"code" => 2005,
			"msg" => "해당 분류가 존재하지 않습니다."
		));
		exit;
	}
	mysql_free_result($result);
}


$errmsg="";
if(count($sels)>0) {//wishlist에서 배열로 넘어온 경우
	
	$sellist="";
	for($i=0;$i<count($sels);$i++) {
		$sellist.=$sels[$i].",";
	}
	$sellist=substr($sellist,0,-1);

	
	if(strlen($sellist)>0) {

		$sql = "SELECT * FROM tblwishlist WHERE id='".$_ShopInfo->getmemid()."' AND wish_idx IN (".$sellist.") ";
		
		$result = mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)){
			
			// 이미 장바구니에 담긴 상품인지 검사하여 있으면 카운트 증가.
			

			$sql2 = "SELECT * FROM ".$basket." WHERE ".$basketWhere;
			$sql2.= " AND productcode='".$row->productcode."' ";
			$sql2.= " AND com_idx=".$row->com_idx." ";
			$sql2.= " AND assemble_idx = ".$assemble_idx." ";
			$sql2.= " AND package_idx = ".$package_idx." ";

			$res = mysql_query($sql2,get_db_conn());
			$rw=mysql_fetch_object($res);
					
			if ( $rw ) {
				$cnt++;
			}
	
			$sql = "INSERT tblbasket SET ";
			$sql.= "tempkey			= '".$_ShopInfo->getTempkey()."', ";
			$sql.= "productcode		= '".$row->productcode."', ";
			$sql.= "com_idx			= ".$row->com_idx.", ";
			$sql.= "quantity		= ".$quantity.", ";
			$sql.= "package_idx		= ".$package_idx.", ";
			$sql.= "assemble_idx	= '".$assemble_idx_max."', ";
			$sql.= "assemble_list	= '".$assemble_list."', ";
			$sql.= "date			= '".$vdate."', ";
			$sql.= "id				= '".$_ShopInfo->getMemid()."', ";
			$sql.= "sell_memid		= '".$sell_memid."' ";
			mysql_query($sql,get_db_conn());
		}

	}

	if($cnt>0){
		echo json_encode(array(
			"result" => "err",
			"code" => 3000,
			"msg" => "이미 장바구니에 담겨있는 상품이 있습니다."
		));
		exit;
	}

	echo json_encode(array(
		"result" => "ok_basket",
		"code" => 3001,
		"msg" => ""
	));
	exit;

}else{

	if($mode!="clear" && $mode!="seldel" && $mode!="wishlist" && strlen($productcode)==18) {
		//해당상품삭제, 장바구니담기, 바로구매, 수량 업데이트, 원샷구매시에...
		if($mode!="del" && strlen($quantity)>0 && $quantity<=0 && strlen($productcode)==18) {
			echo json_encode(array(
				"result" => "err",
				"code" => 4000,
				"msg" => "구매수량이 잘못되었습니다."
			));
			exit;
		}

		//장바구니 담기 또는 수량/옵션 업데이트
		if($mode!="del" && strlen($quantity)>0 && strlen($productcode)==18) {
			$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check,assembleuse,package_num FROM tblproduct ";
			$sql.= "WHERE productcode='".$productcode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				if($row->display!="Y") {
					$errmsg="해당 상품은 판매가 되지 않는 상품입니다.";
				}

				$proassembleuse = $row->assembleuse;

				if($mode=="upd") {
					$sql2 = "SELECT SUM(quantity) as quantity FROM ".$basket." WHERE ".$basketWhere;
					$sql2.= "AND productcode='".$productcode."' ";
					$sql2.= "GROUP BY productcode ";
					$result2 = mysql_query($sql2,get_db_conn());
					if($row2 = mysql_fetch_object($result2)) {
						$rowcnt=$row2->quantity;
					} else {
						$rowcnt=0;
					}
					mysql_free_result($result2);

					$charge_quantity = -($orgquantity-$quantity);
					$rowcnt=$rowcnt+$charge_quantity;
				} else {
					$rowcnt=$quantity;
					$charge_quantity=$quantity;
				}

				if($row->group_check!="N") {
					if(strlen($_ShopInfo->getMemid())>0) {
						$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
						$sqlgc.= "WHERE productcode='".$productcode."' ";
						$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
						$resultgc=mysql_query($sqlgc,get_db_conn());
						if($rowgc=@mysql_fetch_object($resultgc)) {
							if($rowgc->groupcheck_count<1) {
								$errmsg="해당 상품은 지정 등급 전용 상품입니다.\n";
							}
							@mysql_free_result($resultgc);
						} else {
							$errmsg="해당 상품은 지정 등급 전용 상품입니다.\n";
						}
					} else {
						$errmsg="해당 상품은 회원 전용 상품입니다.\n";
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
						$errmsg="해당 상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\n";
					}
				}
				if(strlen($errmsg)==0) {
					if ($miniq!=1 && $miniq>1 && $rowcnt<$miniq)
						$errmsg="해당 상품은 최소 ".$miniq."개 이상 주문하셔야 합니다.\n";
					if ($maxq!="?" && $maxq>0 && $rowcnt>$maxq)
						$errmsg.="해당 상품은 최대 ".$maxq."개 이하로 주문하셔야 합니다.\n";

					if(empty($option1) && strlen($row->option1)>0)  $option1=1;
					if(empty($option2) && strlen($row->option2)>0)  $option2=1;
					if(strlen($row->quantity)>0) {
						if ($rowcnt>$row->quantity) {
							if ($row->quantity>0)
								$errmsg.="해당 상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$row->quantity." 개 입니다.")."\n";
							else
								$errmsg.= "해당 상품이 다른 고객의 주문으로 품절되었습니다.\n";
						}
					}

					if(count($assemble_list_exp)>0) {
						for($i=0; $i<count($assemble_list_exp); $i++) {
							if(strlen($assemble_list_exp[$i])>0) {
								$assemble_proquantity[$assemble_list_exp[$i]]+=$charge_quantity;
							}
						}
						$assemprosql = "SELECT productcode,quantity,productname FROM tblproduct ";
						$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
						$assemprosql.= "AND display = 'Y' ";
						$assemproresult=mysql_query($assemprosql,get_db_conn());
						while($assemprorow=@mysql_fetch_object($assemproresult)) {
							if(strlen($assemprorow->quantity)>0) {
								if($assemble_proquantity[$assemprorow->productcode] > $assemprorow->quantity) {
									if($assemprorow->quantity>0) {
										$errmsg.="해당 상품의 구성상품 [".ereg_replace("'","",$assemprorow->productname)."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$assemprorow->quantity." 개 입니다.")."\n";
									} else {
										$errmsg.="해당 상품의 구성상품 [".ereg_replace("'","",$assemprorow->productname)."] 다른 고객의 주문으로 품절되었습니다.\n";
									}
								}
							}
						}
						@mysql_free_result($assemproresult);
					} else if(strlen($package_productcode_tmp)>0) {
						$assemble_proquantity[$productcode]+=$charge_quantity;
						$package_productcode_tmpexp = explode("",$package_productcode_tmp);
						$package_quantity_tmpexp = explode("",$package_quantity_tmp);
						$package_productname_tmpexp = explode("",$package_productname_tmp);
						for($i=0; $i<count($package_productcode_tmpexp); $i++) {
							if(strlen($package_productcode_tmpexp[$i])>0) {
								$assemble_proquantity[$package_productcode_tmpexp[$i]]+=$charge_quantity;

								if(strlen($package_quantity_tmpexp[$i])>0) {
									if($assemble_proquantity[$package_productcode_tmpexp[$i]] > $package_quantity_tmpexp[$i]) {
										if($package_quantity_tmpexp[$i]>0) {
											$errmsg.="해당 상품의 패키지 [".ereg_replace("'","",$package_productname_tmpexp[$i])."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$package_quantity_tmpexp[$i]." 개 입니다.")."\n";
										} else {
											$errmsg.="해당 상품의 패키지 [".ereg_replace("'","",$package_productname_tmpexp[$i])."] 다른 고객의 주문으로 품절되었습니다.\n";
										}
									}
								}
							}
						}
					} else if(strpos($errmsg,"재고")==false) { //재고부족 출력문구 반복 하지 않음 2016-05-02 Seul
						$assemble_proquantity[$productcode]+=$charge_quantity;
						if(strlen($row->quantity)>0) {
							if ($assemble_proquantity[$productcode] > $row->quantity) {
								if ($row->quantity>0)
									$errmsg.="해당 상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$row->quantity." 개 입니다.")."\n";
								else
									$errmsg.= "해당 상품이 다른 고객의 주문으로 품절되었습니다.\n";
							}
						}
					}

					if(strlen($row->option_quantity)>0) {
						$optioncnt = explode(",",substr($row->option_quantity,1));
						if($option2==0) $tmoption2=1;
						else $tmoption2=$option2;

						//멀티옵션 재고 체크 2016-04-28 Seul
						$optionvalue=0;
						$optvalidx=0;
						
						for($i=0, $end=count($optidxs); $i<$end; $i++){
							$optionvalue=$optioncnt[(($optidxs2[$i]-1)*10)+($optidxs[$i]-1)];

							if($optionvalue<=0 && $optionvalue!="") {
								//품절일 때
								$optvalidx=1;
								break;
							}
							else if($optionvalue<$opt_quantity[$i] && $optionvalue!="") {
								//재고 부족일 때
								$optvalidx=2;
								break;
							}
						}
						
						if($optvalidx==1) {
							$errmsg.="해당 상품의 선택된 옵션은 다른 고객의 주문으로 품절되었습니다.\n";
						} else if($optvalidx==2) {
							$errmsg.="해당 상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\n";
						} else {
							if($mode=="upd") {
								if (empty($option1))  $option1=0;
								if (empty($option2))  $option2=0;
								if (empty($opts))  $opts="0";
								if (empty($assemble_idx))  $assemble_idx=0;

								$samesql = "SELECT * FROM ".$basket." WHERE ".$basketWhere;
								$samesql.= "AND productcode='".$productcode."' ";
								$samesql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
								$samesql.= "AND assemble_idx = '".$assemble_idx."' ";
								$sameresult = mysql_query($samesql,get_db_conn());
								$samerow=mysql_fetch_object($sameresult);
								mysql_free_result($sameresult);
								if($samerow && ($option1!=$orgoption1 || $option2!=$orgoption2)) {
									if($optionvalue<($samerow->quantity + $quantity) && $optionvalue!="") {
										$errmsg.="해당 상품의 선택된 옵션과 중복상품의 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\n";
									}
								}
							}
						}
					}
				}
			} else {
				$errmsg="해당 상품이 존재하지 않습니다.\n";
			}
			mysql_free_result($result);

			if(strlen($errmsg)>0) {
				echo json_encode(array(
					"result" => "err",
					"code" => 4001,
					"msg" => $errmsg
				));
				exit;
			}
		}

		// 이미 장바구니에 담긴 상품인지 검사하여 있으면 카운트만 증가.
		if (empty($option1))  $option1=0;
		if (empty($option2))  $option2=0;
		if (empty($opts))  $opts=0;
		if (empty($assemble_idx))  $assemble_idx=0;

		if($proassembleuse=="Y") {
			$assemaxsql = "SELECT MAX(assemble_idx) AS assemble_idx_max FROM ".$basket." WHERE ".$basketWhere;
			$assemaxsql.= " AND productcode='".$productcode."'";
			$assemaxsql.= " AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."'";
			$assemaxsql.= " AND assemble_idx > 0 ";
			$assemaxresult = mysql_query($assemaxsql,get_db_conn());
			$assemaxrow=@mysql_fetch_object($assemaxresult);
			@mysql_free_result($assemaxresult);
			$assemble_idx_max = $assemaxrow->assemble_idx_max+1;
		} else {
			$assemble_idx_max = 0;
		}
		

		if ($mode=="del") {
			$sql = "DELETE FROM ".$basket." WHERE ".$basketWhere." AND productcode='".$productcode."' ";
			$sql.= "AND opt1_idx='".$orgoption1."' AND opt2_idx='".$orgoption2."' AND optidxs='".$opts."' ";
			$sql.= "AND assemble_idx = '".$assemble_idx."' ";
			$sql.= "AND package_idx = '".$package_idx."' ";
			mysql_query($sql,get_db_conn());
		} else if ($mode=="upd") {
			if (($option1==$orgoption1 && $option2==$orgoption2) || !($row)) {
				// 확인결과 : 그룹옵션일 때 여기서 처리
				$sql = "UPDATE ".$basket." SET ";
				$sql.= "quantity		= '".$quantity."', ";
				$sql.= "opt1_idx		= '".$option1."', ";
				$sql.= "opt2_idx		= '".$option2."' ";
				$sql.= "WHERE ".$basketWhere;
				$sql.= "AND productcode	='".$productcode."' AND opt1_idx='".$orgoption1."' ";
				$sql.= "AND opt2_idx	='".$orgoption2."' AND optidxs='".$opts."' ";
				$sql.= "AND assemble_idx = '".$assemble_idx."' ";
				$sql.= "AND package_idx = '".$package_idx."' ";
				mysql_query($sql,get_db_conn());
			} else {
				// 그룹옵션이 아닐 때 여기서 처리
				$sql = "UPDATE ".$basket." SET quantity=".$quantity;
				$sql.= " WHERE ".$basketWhere;
				$sql.= " AND productcode='".$productcode."' AND opt1_idx='".$orgoption1."' ";
				$sql.= " AND opt2_idx='".$orgoption2."' AND optidxs='".$opts."' ";
				mysql_query($sql,get_db_conn());
			}
		} else {
			if (strlen($productcode)==18) {
				$vdate = date("YmdHis");
				$sql = "SELECT COUNT(*) as cnt, ordertype FROM ".$basket." WHERE ".$basketWhere;
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				mysql_free_result($result);
				if($row->cnt>=200) {
					echo json_encode(array(
						"result" => "err",
						"code" => 5000,
						"msg" => "장바구니에는 총 200개까지만 담을수 있습니다."
					));
					exit;
				} else {
					//다중옵션선택시
					$optidxs_count = sizeof( $opt_comidx );
					$basketCnt = 0;
					if($optidxs_count>0 && is_array($opt_comidx)) {
						for( $i = 0; $i < $optidxs_count; $i++ ){

							$sql = "SELECT * FROM ".$basket." WHERE ".$basketWhere;
							$sql.= " AND productcode='".$productcode."'";
							$sql.= " AND com_idx=".$opt_comidx[$i]."";
							$result = mysql_query($sql,get_db_conn());
							$basket_row=mysql_fetch_object($result);
							mysql_free_result($result);

							if($basket_row){
								$basketCnt++;
							} else {
								$sql = "INSERT ".$basket." SET ";
								$sql.= "tempkey			= '".$_ShopInfo->getTempkey()."', ";
								$sql.= "productcode		= '".$productcode."', ";
								$sql.= "com_idx			= '".$opt_comidx[$i]."', ";
								$sql.= "quantity		= '".$opt_quantity[$i]."', ";
								$sql.= "package_idx		= '".$package_idx."', ";
								$sql.= "assemble_idx	= '".$assemble_idx_max."', ";
								$sql.= "assemble_list	= '".$assemble_list."', ";
								$sql.= "date			= '".$vdate."', ";
								$sql.= "sell_memid		= '".$sell_memid."', ";
								$sql.= "id				= '".$_ShopInfo->getMemid()."', ";
								$sql.= "ordertype		= '".$ordertype."' ";
								mysql_query($sql,get_db_conn());
							}
						}
						if($basketCnt > 0){
							echo json_encode(array(
								"result" => "err",
								"code" => 5001,
								"msg" => "이미 장바구니에 ".$basketCnt."개의 상품이 담겨있습니다."
							));
							exit;
						}
					} else {
						$sql = "SELECT * FROM ".$basket." WHERE ".$basketWhere;
						$sql.= " AND productcode='".$productcode."'";
						$sql.= " AND com_idx=".$opt_comidx."";
						$result = mysql_query($sql,get_db_conn());
						$basket_row=mysql_fetch_object($result);
						mysql_free_result($result);

						if($basket_row){
							echo json_encode(array(
								"result" => "err",
								"code" => 5002,
								"msg" => "이미 장바구니에 상품이 담겨있습니다."
							));
							exit;
						} else {
							$sql = "INSERT ".$basket." SET ";
							$sql.= "tempkey			= '".$_ShopInfo->getTempkey()."', ";
							$sql.= "productcode		= '".$productcode."', ";
							$sql.= "com_idx			= ".$opt_comidx.", ";
							$sql.= "quantity		= '".$quantity."', ";
							$sql.= "package_idx		= '".$package_idx."', ";
							$sql.= "assemble_idx	= '".$assemble_idx_max."', ";
							$sql.= "assemble_list	= '".$assemble_list."', ";
							$sql.= "date			= '".$vdate."', ";
							$sql.= "sell_memid		= '".$sell_memid."', ";
							$sql.= "id				= '".$_ShopInfo->getMemid()."', ";
							$sql.= "ordertype		= '".$ordertype."' ";

							mysql_query($sql,get_db_conn());
						}
					}

					if( $ordertype != "" ){
						echo json_encode(array(
							"result" => "ok_now",
							"code" => 6000,
							"url" => $Dir.FrontDir."login.php?chUrl=".urlencode( $Dir.FrontDir."order.php?ordertype=".$ordertype )
						));
						exit;
					}

					echo json_encode(array(
						"result" => "ok_basket",
						"code" => 7000,
						"msg" => ""
					));
					exit;
				}
			}
		}
	}
}//wishlist 배열값 end if
<?
// 상품가격 0원 오류 관련 수정함 2016-04-07 Seul
include "header.php";
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");
include_once($Dir."lib/lib.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;

$refURL = $_REQUEST["refURL"];
$productcode = $_REQUEST["productcode"];
$vidx = $_REQUEST["vidx"];

$ordertype=$_REQUEST["ordertype"];////(empty($_GET["ordertype"])?"ordernow":$_GET["ordertype"]);
$aoidx=$_REQUEST["aoidx"];
$aopidx=$_REQUEST["aopidx"];
$receiveTime=$_REQUEST["receiveTime"];
$receiveDate=$_REQUEST["receiveDate"];
$receiveDateFomet= date( 'Y년 m월 d일', strtotime($receiveDate));
// 주문타입별 장바구니 테이블
$basket = basketTable($ordertype);

//회원전용일 경우 로긴페이지로...
if($_data->member_buygrant=="Y" && strlen($_ShopInfo->getMemid())==0) {
    //Header("Location:./login.php?chUrl=".getUrl());
    echo "<script>location.href='./login.php?chUrl=".getUrl()."'</script>";
    exit;
    
}

$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // 원본파일 경로
$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // 썸내일 저장 경로
$quality = 100;

//장바구니 인증키 확인
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
    $_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}
if(strlen($_ShopInfo->getMemid()) > 0){
    //sns 홍보 본인체크
    $sql ="UPDATE tblbasket SET sell_memid ='' WHERE tempkey='".$_ShopInfo->getTempkey()."' AND sell_memid='".$_ShopInfo->getMemid()."'";
    mysql_query($sql,get_db_conn());
    
    if ($ordertype == "ordernow") {
        $sql = sprintf("DELETE FROM %s WHERE id='%s' AND tempkey!='%s'",$basket,$_ShopInfo->getMemid(),$_ShopInfo->getTempkey());
        mysql_query($sql,get_db_conn());
    }
    
    // 160202 장바구니에 템프키로 담겨 있을 경우 회원 로그인 후 장바구니 접근시 해당 상품에 회원 아이디 삽입처리.
    $sql = sprintf("UPDATE %s SET id='%s' WHERE tempkey='%s' AND (id IS NULL or id = '')",$basket,$_ShopInfo->getMemid(),$_ShopInfo->getTempkey());
    mysql_query($sql,get_db_conn());
    
    $basketWhere = "id='".$_ShopInfo->getMemid()."'";
}else{
    $basketWhere = "tempkey='".$_ShopInfo->getTempkey()."'";
}


// 장바구니 데이터 (Array) ==================================================
// 상품가격 0원 오류 관련 수정함 2016-04-07 Seul
$basketItems = getBasketByArray($basket);

/*
 회원 등급 할인 메세지 ============
 RW : 금액 추가 적립
 RP  : % 추가 적립
 SW : 금액 추가 할인
 SP  : % 추가 할인
 */

$groupMemberSale = "";
if( $basketItems['groupMemberSale'] ) {
    $groupMemberSale .= "
		<font style=\"letter-spacing:0px;\"><b>".$basketItems['groupMemberSale']['name']."</b></font>님(".$basketItems['groupMemberSale']['group'].")은 회원 등급 할인
		<font color=\"#ee0a02\" style=\"letter-spacing:0px;\">".number_format($basketItems['groupMemberSale']['useMoney'])."</font>원 이상
		<font  color=\"#ee0a02\">".$basketItems['groupMemberSale']['payType']."</font> 결제시
	";
    if($basketItems['groupMemberSale']['groupCode']=="RW") {
        $groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>".number_format($basketItems['groupMemberSale']['addMoney'])."</b>원</font>의 적립금을 추가로 적립해 드립니다.";
    } else if($basketItems['groupMemberSale']['groupCode']=="RP") {
        $groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>구매금액의 ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>%</font>를 적립해 드립니다.";
    } else if($basketItems['groupMemberSale']['groupCode']=="SW") {
        $groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>구매금액 ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>원</font>을 추가로 할인 됩니다.";
    } else if($basketItems['groupMemberSale']['groupCode']=="SP") {
        $groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>구매금액의 ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>%</font>를 추가로 할인 됩니다.";
    }
    $groupMemberSale .= "<span id=\"couponEventMsg\"></span>";
}
#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

//////  결제 수단 선택 start  ////////////////////////////////////////////////

// 결제 현금결제 전용 포함 체크
$bankonlyCHK = "N";
foreach ( $basketItems['vender'] as $venderKey => $venderValue ) {
    foreach ( $venderValue['products'] as $productsKey=> $productsValue ){
        if( $productsValue['bankonly'] ) {
            $bankonlyCHK = "Y";
        }
    }
}

$escrow_info = GetEscrowType($_data->escrow_info);
$pgInfo=GetEscrowType($_data->card_id); //사용PG사 확인

$payType = "";

//무통장
/*if( preg_match("/^(Y|N)$/", $_data->payment_type) && $escrow_info["onlycard"]!="Y" ) {
 $payType .= "<input type='radio' onclick=\"change_paymethod(1);\" name='sel_paymethod' value='B' id=\"sel_paymethod1\"><label for=\"sel_paymethod1\" style=\"cursor:pointer;\">무통장 입금</label>&nbsp;&nbsp;";
 }
 
 //2:신용카드: 현금결제시 비활성
 if(preg_match("/^(Y|C)$/", $_data->payment_type) && strlen($_data->card_id)>0 AND $bankonlyCHK == "N" ) {
 $payType .= "<input type='radio' onclick=\"change_paymethod(2);\" name='sel_paymethod' value='C' id=\"sel_paymethod2\"><label for=\"sel_paymethod2\" style=\"cursor:pointer;\">신용카드</label>&nbsp;&nbsp;";
 }
 
 //2:실시간계좌이체
 if($escrow_info["onlycard"]!="Y" ) {
 if(strlen($_data->trans_id)>0) {
 $payType .= "<input type='radio' onclick=\"change_paymethod(3);\" name='sel_paymethod' value='V' id=\"sel_paymethod3\"><label for=\"sel_paymethod3\" style=\"cursor:pointer;\">실시간계좌이체</label>&nbsp;&nbsp;";
 }
 }
 
 //3:가상계좌
 if($escrow_info["onlycard"]!="Y" ) {
 if(strlen($_data->virtual_id)>0) {
 //$payType .= "<input type='radio' onclick=\"change_paymethod(4);\" name='sel_paymethod' value='O' id=\"sel_paymethod4\"><label for=\"sel_paymethod4\" style=\"cursor:pointer;\">가상계좌</label>&nbsp;&nbsp;";
 }
 }
 
 //4:에스크로
 if(($escrow_info["escrowcash"]=="A" || $escrow_info["escrowcash"]=="Y") && strlen($_data->escrow_id)>0) {
 $pgid_info="";
 $pg_type="";
 $pgid_info=GetEscrowType($_data->escrow_id);
 $pg_type=trim($pgid_info["PG"]);
 
 if(preg_match("/^(A|B|C|D)$/",$pg_type)) {
 //KCP/데이콤/올더게이트/이니시스 가상계좌 에스크로 코딩
 $payType .= "<input type='radio' onclick=\"change_paymethod(5);\" name='sel_paymethod' value='Q' id=\"sel_paymethod5\"><label for=\"sel_paymethod5\" style=\"cursor:pointer;\">결제대금예치제(에스크로)</label>&nbsp;&nbsp;";
 }
 }
 
 //5:핸드폰 : 현금결제시 비활성
 if(strlen($_data->mobile_id)>0 AND $bankonlyCHK == "N" ) {
 //$payType .= "<input type='radio' onclick=\"change_paymethod(6);\" name='sel_paymethod' value='M' id=\"sel_paymethod6\"><label for=\"sel_paymethod6\" style=\"cursor:pointer;\">핸드폰 결제</label>";
 }
 */
//현금결제 전용 상품 포함시 메세지
if( $bankonlyCHK == "Y" ) {
    $payType .= "&nbsp;&nbsp;&nbsp;<font color='#FF0000'>(*주문 상품에 [현금결제] 상품이 포함되어 신용카드결제가 불가능합니다.)</font>";
}

//////  결제 수단 선택 end  ////////////////////////////////////////////////




// 오프라인 쿠폰 링크
$offlineCouponInputButton = "<img src='/images/common/order/T01/offlineCouponInputButton.gif' align='absmiddle' style='cursor:pointer;' alt='오프라인 쿠폰 등록' onclick=\" coupon_check( 'offlinecoupon' );\">";


// shopinfo 사은품 활성화 정보 호출
$giftInfoRow = @mysql_fetch_object( mysql_query("SELECT `gift_type` FROM `tblshopinfo` LIMIT 1;",get_db_conn()) );
$giftInfoSetArray = explode("|",$giftInfoRow->gift_type);

#수량재고파악
$errmsg="";
$sql = "SELECT a.quantity as sumquantity,b.productcode,b.productname,b.display,b.quantity, ";
$sql.= "b.option_quantity,b.etctype,b.group_check,b.assembleuse,a.assemble_list AS basketassemble_list ";
$sql.= ", c.assemble_list,a.package_idx ";
$sql.= "FROM ".$basket." a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
$sql.= "WHERE a.".$basketWhere." ";
$sql.= "AND a.productcode=b.productcode ";
$result=mysql_query($sql,get_db_conn());
$assemble_proquantity_cnt=0;

// 160809 재고 체크 못해서 할 수 있도록 수정 처리.
if (mysql_num_rows($result) > 0) {
    while($row=mysql_fetch_object($result)) {
        if($row->display!="Y") {
//            $errmsg="[".ereg_replace("'","",$row->productname)."]상품은 판매가 되지 않는 상품입니다.\\n";
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
                        mysql_query("delete from tblbasket where a.".$basketWhere." and productcode='".$row->productcode."'",get_db_conn()); // 삭제 처리
                    }
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
                        $basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket WHERE ".$basketWhere;
                        $basketresult = mysql_query($basketsql,get_db_conn());
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
                        @mysql_free_result($assemproresult);
                    } else if(strlen($package_productcode_tmp)>0) {
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
                        $sql = "SELECT opt1_idx, opt2_idx, quantity FROM tblbasket ";
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
                        @mysql_free_result($result2);
                    }
        }
    }
} else {
    $errmsg = "주문하신 상품이 존재하지 않습니다. 다시 시도해주십시오.";
}
@mysql_free_result($result);

if(strlen($errmsg)>0) {
    echo "<html></head><body onload=\"alert('".$errmsg."');location.href='/app/main.php';\"></body></html>";
    exit;
}







//쿠폰 발행이 있을 경우
if($_REQUEST['mode']=="coupon" && strlen($_REQUEST['coupon_code'])==8){
    $onload = '';
    if(strlen($_ShopInfo->getMemid())==0) {	//비회원
        echo "<html></head><body onload=\"alert('로그인 후 쿠폰 다운로드가 가능합니다.');location.href='./login.php?chUrl=".getUrl()."';\"></body></html>";exit;
    }else{
        $sql = "SELECT * FROM tblcouponinfo where coupon_code = '".$_REQUEST['coupon_code']."'";
        
        
        $result=mysql_query($sql,get_db_conn());
        if($row=mysql_fetch_object($result)) {
            if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
                $onload="<script>alert(\"모든 쿠폰이 발급되었습니다.\");</script>";
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
                $sql.= "coupon_code	= '".$_REQUEST['coupon_code']."', ";
                $sql.= "id			= '".$_ShopInfo->getMemid()."', ";
                $sql.= "date_start	= '".$date_start."', ";
                $sql.= "date_end	= '".$date_end."', ";
                $sql.= "date		= '".$date."' ";
                
                mysql_query($sql,get_db_conn());
                if(!mysql_errno()) {
                    $sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
                    $sql.= "WHERE coupon_code = '".$_REQUEST['coupon_code']."'";
                    mysql_query($sql,get_db_conn());
                    
                    $onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
                } else {
                    if($row->repeat_id=="Y") {	//동일인 재발급이 가능하다면,,,,
                        $sql = "UPDATE tblcouponissue SET ";
                        if($row->date_start<=0) {
                            $sql.= "date_start	= '".$date_start."', ";
                            $sql.= "date_end	= '".$date_end."', ";
                        }
                        $sql.= "used		= 'N' ";
                        $sql.= "WHERE coupon_code='".$_REQUEST['coupon_code']."' ";
                        $sql.= "AND id='".$_ShopInfo->getMemid()."' ";
                        mysql_query($sql,get_db_conn());
                        $onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
                    } else {
                        $onload="<script>alert(\"이미 쿠폰을 발급받으셨습니다.\\n\\n해당 쿠폰은 재발급이 불가능합니다.\");</script>";
                    }
                }
            }
        }
        mysql_free_result($result);
        
    }
    
    if(_empty($onload)){
        echo $onload;
    }
    ?>
	<script language="javascript" type="text/javascript">
		document.location.replace('./order.php');
	</script>
	<?
	exit;

}


// 보유 쿠폰 리스트
$mycoupon_codes = getMyCouponList('',true);







$card_miniprice=$_data->card_miniprice;
$deli_area=$_data->deli_area;
$admin_message = $_data->order_msg;
$reserve_limit = $_data->reserve_limit;
$reserve_maxprice = $_data->reserve_maxprice;
if($reserve_limit==0) $reserve_limit=1000000000000;

if($reserve_limit<0) $reserve_limit = round($basketItems['sumprice']*(-1*$reserve_limit/100), -1);	//2015-06-02 적립금 제한이 %일때 -로 나오는 부분 %로 계산해서 나오도록 수정

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
$etcmessage=explode("=",$admin_message);



$user_reserve=0;
if(strlen($_ShopInfo->getMemid())>0) {
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = mysql_query($sql);
	if($row = mysql_fetch_object($result)) {
		$user_reserve = $row->reserve;
		if($user_reserve>$reserve_limit) {
			$okreserve=$reserve_limit;
			$remainreserve=$user_reserve-$reserve_limit;
		} else {
			$okreserve=$user_reserve;
			$remainreserve=0;
		}
		$home_addr="";
		/*if(strlen($row->home_post)==6) {
			$home_post1=substr($row->home_post,0,3);
			$home_post2=substr($row->home_post,3,3);
		}*/
		$home_post1=$row->home_post;

		$row->home_addr = ereg_replace("\"","",$row->home_addr);
		$home_addr = explode("=",$row->home_addr);
		$home_addr1 = $home_addr[0];
		$home_addr2 = $home_addr[1];

		$office_addr="";
		/*if(strlen($row->office_post)==6) {
			$office_post1=substr($row->office_post,0,3);
			$office_post2=substr($row->office_post,3,3);
		}*/
		$office_post1=$row->office_post;
		
		$row->office_addr = ereg_replace("\"","",$row->office_addr);
		$office_addr = explode("=",$row->office_addr);
		$office_addr1 = $office_addr[0];
		$office_addr2 = $office_addr[1];

		$name = $row->name;
		$email = $row->email;
		if (strlen($row->mobile)>0) $mobile = $row->mobile;
		if (strlen($row->home_tel)>0) $home_tel = $row->home_tel;
		if (strlen($row->office_tel)>0) $office_tel = $row->office_tel;
		//$mobile=explode("-",replace_tel(check_num($mobile)));
		//$home_tel=explode("-",replace_tel(check_num($row->home_tel)));

		$group_code=$row->group_code;
		@mysql_free_result($result);
		if(strlen($group_code)>0 && $group_code!=NULL) {
			$sql = "SELECT * FROM tblmembergroup WHERE group_code='".$group_code."' AND MID(group_code,1,1)!='M' ";
			$result=mysql_query($sql);
			if($row=mysql_fetch_object($result)){

				//그룹 이미지 출력처리 20131025 J.Bum
				if(file_exists($Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif")) {
					$royal_img="<img src=\"".$Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif\" border=0>";
				} else {
					$royal_img="<img src=\"".$Dir."images/common/group_img.gif\" border=0>\n";
				}

				$group_code = $row->group_code;
				$org_group_name=$row->group_name;  //그룹정보로 인해 추가
				$group_name=$row->group_name;
				$group_type=substr($row->group_code,0,2); // 그룹 타입 					RW : 금액 추가 적립 / RP  : % 추가 적립 / SW : 금액 추가 할인 / SP  : % 추가 할인
				$group_usemoney=$row->group_usemoney; // 그룹할인 기준 금액
				$group_addmoney=$row->group_addmoney; // 그룹할인금액
				$group_payment=$row->group_payment; // 결제 방식					"B"=>"현금","C"=>"카드","N"=>"현금/카드"
					if($group_payment=="B") {
						$group_name.=" (현금결제시)";
					} else if($group_payment=="C") {
						$group_name.=" (카드결제시)";
					}
			}
			@mysql_free_result($result);
		}
	} else {
		$_ShopInfo->setMemid("");
	}
}




// 비회원 동의
if( strlen($_ShopInfo->getMemid()) == 0 ){
	$sql = "SELECT privercy FROM tbldesign ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$privercy_exp = @explode("=", $row->privercy);
		$privercybody=$privercy_exp[1];
	}
	@mysql_free_result($result);

	if(strlen($privercybody)==0) {
		$buffer="";
		$fp=fopen($Dir.AdminDir."privercy2.txt","r");
		if($fp) {
			while (!feof($fp)) {
				$buffer.= fgets($fp, 1024);
			}
		}
		fclose($fp);
		$privercybody=$buffer;
	}

	$pattern=array("(\[SHOP\])","(\[NAME\])","(\[EMAIL\])","(\[TEL\])");
	$replace=array($_data->shopname,$_data->privercyname,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyemail."</a>",$_data->info_tel);
	$privercybody = preg_replace($pattern,$replace,$privercybody);
}



$sumprice = $basketItems['sumprice'];


?>
<SCRIPT LANGUAGE="JavaScript">
<!--
var coupon_limit = "<?=$_data->coupon_limit_ok?>";
function change_message(gbn) {
	if(gbn==1) {
		document.all["msg_idx2"].style.display="none";
		document.all["msg_idx1"].style.display="";
		document.form1.msg_type.value=gbn;
	} else if(gbn==2) {
		document.all["msg_idx2"].style.display="";
		document.all["msg_idx1"].style.display="none";
		document.form1.msg_type.value=gbn;
	}
}
function SameCheck() {
	document.form1.receiver_name.value=document.form1.sender_name.value;
	document.form1.receiver_tel1.value=document.form1.sender_tel.value;
//	document.form1.receiver_tel2.value=document.form1.sender_hp.value;
	//document.form1.receiver_tel11.value=document.form1.sender_tel1.value;
	//document.form1.receiver_tel12.value=document.form1.sender_tel2.value;
	//document.form1.receiver_tel13.value=document.form1.sender_tel3.value;
	//document.form1.receiver_tel21.value=document.form1.sender_hp1.value;
	//document.form1.receiver_tel22.value=document.form1.sender_hp2.value;
	//document.form1.receiver_tel23.value=document.form1.sender_hp3.value;
	document.form1.rpost1.value="<?=$home_post1?>";
	//document.form1.rpost2.value="<?=$home_post2?>";
	document.form1.raddr1.value="<?=$home_addr1?>";
	document.form1.raddr2.value="<?=$home_addr2?>";
}
<?if(strlen($_ShopInfo->getMemid())>0){?>
/*function addrchoice() {
	if(document.form1.addrtype[0].checked==true) {
		document.form1.rpost1.value="<?=$home_post1?>";
		document.form1.rpost2.value="<?=$home_post2?>";
		document.form1.raddr1.value="<?=$home_addr1?>";
		document.form1.raddr2.value="<?=$home_addr2?>";
	} else if(document.form1.addrtype[1].checked==true) {
		document.form1.rpost1.value="<?=$office_post1?>";
		document.form1.rpost2.value="<?=$office_post2?>";
		document.form1.raddr1.value="<?=$office_addr1?>";
		document.form1.raddr2.value="<?=$office_addr2?>";
	} else if(document.form1.addrtype[2].checked==true) {
		window.open("./addrbygone.php","addrbygone","width=100,height=100,toolbar=no,menubar=no,scrollbars=yes,status=no");
	}
}*/
function reserve_check(temp) {
	temp=parseInt(temp);
	if(isNaN(document.form1.usereserve.value)) {
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('숫자만 입력하셔야 합니다.');
		return;
	}
	if(parseInt(document.form1.usereserve.value)>temp) {
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('사용가능 적립금 보다 적거나 똑같이 입력하셔야 합니다.');
		return;
	}
	document.form1.okreserve.value=parseInt(temp-document.form1.usereserve.value);
	document.form1.usereserve.value=temp-document.form1.okreserve.value;
}
<?}?>
function get_post() {
	window.open("./addr_search.php?form=form1&post=rpost&addr=raddr1&gbn=2","f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");		
}

// 쿠폰다운로드
function issue_coupon(coupon_code,productcode){
	location.href="?mode=coupon&coupon_code="+coupon_code+"&productcode="+productcode;
}


// 쿠폰선택 ( offlinecoupon : 오프라인쿠폰등록 )
function coupon_check( offlinecoupon ){
	resetCoupon();

	var offlinecouponURL = "";
	offlinecouponURL = "?ordertype=<?=$ordertype?>";		//바로구매시 쿠폰적용이 불가능한 부분 수정 2016-07-18 Seul
	if( offlinecoupon == "offlinecoupon" ) {
		offlinecouponURL = "?offlinecoupon=popup";
	}
	iframePopupOpen("/app/couponpop.php"+offlinecouponURL)
//	window.open("/app/couponpop.php"+offlinecouponURL,"couponpopup","width=720,height=750,toolbar=no,menubar=no,scrollbars=yes,status=no");
}
/*<?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y"){?>
	var isreserveinit=false;
function coupon_cancel() {
	if(document.form1.coupon_code.value.length>0) {
		if(confirm("선택된 쿠폰을 취소하시겠습니까?")==true) {
			document.form1.coupon_code.value="";
		}
	}
	if(isreserveinit==true) {
		if(typeof(document.form1.usereserve)=="object") {
			document.form1.usereserve.readOnly=false;
		}
	}
}
function issue_coupon(coupon_code,productcode){
	location.href="?mode=coupon&coupon_code="+coupon_code+"&productcode="+productcode;
}

function coupon_check( offlinecoupon ){
	resetCoupon();

	var offlinecouponURL = "";
	if( offlinecoupon == "offlinecoupon" ) {
		offlinecouponURL = "?offlinecoupon=popup";
	}
	window.open("/app/couponpop.php"+offlinecouponURL,"couponpopup","toolbar=no,menubar=no,scrollbars=yes,status=no");
}

function coupon_default(){
	resetCoupon();
}
<?}?>*/
function number_format(input){
	var input = String(input);
    var reg = /(\-?\d+)(\d{3})($|\.\d+)/;

    if(reg.test(input)){
        return input.replace(reg, function(str, p1,p2,p3){
                return number_format(p1) + "," + p2 + "" + p3;
            }
        );
    }else{
        return input;
    }
}


/*function ProcessWait(display) {
	var PAYWAIT_IFRAME = document.all.PAYWAIT_IFRAME;

	document.paywait.src = "<?=$Dir?>images/paywait.gif";
	var _x = document.body.clientWidth/2 + document.body.scrollLeft - 250;
	var _y = document.body.clientHeight/2 + document.body.scrollTop - 120;

	PAYWAIT_IFRAME.style.visibility=display;
	PAYWAIT_IFRAME.style.posLeft=_x;
	PAYWAIT_IFRAME.style.posTop=_y;

	PAYWAIT_LAYER.style.posLeft=_x;
	PAYWAIT_LAYER.style.posTop=_y;
	PAYWAIT_LAYER.style.visibility=display;
}

function ProcessWaitPayment() {
	var PAYWAIT_IFRAME = document.all.PAYWAIT_IFRAME;

	document.paywait.src = "<?=$Dir?>images/paywait2.gif";
	var _x = document.body.clientWidth/2 + document.body.scrollLeft - 250;
	var _y = document.body.clientHeight/2 + document.body.scrollTop - 120;

	PAYWAIT_IFRAME.style.visibility='visible';
	PAYWAIT_IFRAME.style.posLeft=_x;
	PAYWAIT_IFRAME.style.posTop=_y;

	PAYWAIT_LAYER.style.visibility='visible';
	PAYWAIT_LAYER.style.posLeft=_x;
	PAYWAIT_LAYER.style.posTop=_y;
}

function PaymentOpen() {
	PROCESS_IFRAME.PaymentOpen();
	ProcessWait('visible');
}*/

//-->
</SCRIPT>
<?
$mingiftprice = 0;
if(false !== $gres = mysql_query("select min(gift_startprice) from tblgiftinfo",get_db_conn())){
	if(mysql_num_rows($gres)) $mingiftprice = mysql_result($gres,0,0);
}
?>

<script>
	var deli_basefee	= parseInt(<?=$_data->deli_basefee?>); //쇼핑몰 설정 배송료
	var deli_miniprice	= parseInt(<?=$_data->deli_miniprice?>); //쇼핑몰 설정 배송무료 최소 상품가
	var deli_price = parseInt(<?=$basketItems['deli_price']?>);
	var excp_group_discount = parseInt(<?=$basketItems['excp_group_discount']?>);
	var mingiftprice = parseInt(<?=$giftprice?>);

	var setprice;

	//등급할인 정보
	var groupDiscMoney = parseInt("<?=$basketItems['groupMemberSale']['addMoney']?>"); // 적립/할인금액 또는 %
	var groupDiscUseMoney = parseInt("<?=$basketItems['groupMemberSale']['useMoney']?>"); // 기준 금액
	var groupDiscPayTypeCode = "<?=$basketItems['groupMemberSale']['payTypeCode']?>"; // 기준 결제 방법
	var groupCode = "<?=$basketItems['groupMemberSale']['groupCode']?>"; // 그룹코드

	if(isNaN(mingiftprice) || mingiftprice <1) mingiftprice = 0;

	$(document).ready(function() {
		$('.btn_prev').click(function() {
			var refURL = "<?=$refURL?>";
			var refPage = "<?=$refPage?>";
			var productcode = "<?=$productcode?>";
			var vidx = "<?=$vidx?>";
			if (refURL == "productdetail_today_sale") {
				location.href = "productdetail_today_flower.php?productcode=" + productcode + "&vidx=" + vidx + "&refURL=" + refURL + "&refPage=" + refPage;
			} else if (refURL == "productdetail_today_flower") {
				location.href = "productdetail_timesale.php?productcode=" + productcode + "&vidx=" + vidx + "&refURL=" + refURL + "&refPage=" + refPage;
			} else {
				history.back();
			}
		});
		
		// 적립금
		$("#usereserve").keyup(function(){
			var possibleMileage = parseInt($("#okreserve").val());//해당 주문에서 사용가능한 적립금
			var defaultprice	= parseInt($("#sumprice").val());	//기본 총 결제금액

			repstr = $(this).val().replace(/[^0-9]/g,'');
			userMileage = parseInt(repstr);
			if(isNaN(userMileage)) userMileage = 0;
			$(this).val(userMileage.toString());

			if(userMileage > possibleMileage){
				alert("해당 주문의 적립금 적용 가능한 금액은 "+possibleMileage + "원 입니다.");
				$("#usereserve").val(possibleMileage.toString());
			}else{

			}
			//resetCoupon();

			solvPrice();
		});

		/*
		$("input[name=saddr2],input[name=raddr2]").focus(function(){
			if($(this).val() == '나머지 주소') $(this).val('');
		});

		$("input[name=saddr2],input[name=raddr2]").blur(function(){
			if($.trim($(this).val()).length < 1) $(this).val('나머지 주소');
		});
		*/

		solvPrice();
	});

	function reserdeli(total_price){
		if(total_price > 0 && deli_miniprice > total_price) {
			alert("최종 결제금액이 " + number_format(deli_miniprice) + " 원 이하인 경우 기본 배송료 " +number_format(deli_basefee)+ "원이 추가됩니다");
			$("#disp_last_price").text(number_format(total_price+deli_basefee));	// 최종결제금액 UI 표시
		}
	}

	function resetCoupon(){
		var coupon = parseInt($("#coupon_price").val()); //쿠폰 할인액
		var limitLength = $('.limitedCouponGroup').length;
		var unlimitLength = $('.unlimitedCouponGroup').length;
		if(!isNaN(coupon) && coupon > 0){
			alert('쿠폰 사용 설정이 초기화 됩니다.');
		}
		if(limitLength > 0 || unlimitLength > 0){
			$('.limitedCouponGroup').remove();	
			$('.unlimitedCouponGroup').remove();	
		}
	
		$('#couponlist').val('');
		$('#dcpricelist').val('');
		$('#couponproduct').val('');
		$('#coupon_price').val('0');
		$('#bank_only').val('N');
		$("#possible_gift_price_used").val("Y");
		$("#possible_group_dis_used").val("Y");

		solvPrice();
	}

	function change_paymethod(val){
		solvPrice();
	}


	// 재 계산기 ******************************************************************************************
	function solvPrice(){

		var possibleMileage = parseInt($("#okreserve").val());//해당 주문에서 사용가능한 적립금
		var userMileage = parseInt($("#usereserve").val()); // 사용한 적립금
		var gift = parseInt($("#possible_gift_price").val()); // 사은품 지급가능 구매금액
		var coupon = parseInt($("#coupon_price").val()); //쿠폰 사용한 값
		var defaultprice = parseInt($("#sumprice").val()); //총 결제금액
		var deli_price = parseInt($("#deliprice").val()); // 배송비

		if(isNaN(possibleMileage)) possibleMileage = 0;
		if(isNaN(userMileage)) userMileage = 0;
		if(isNaN(gift)) gift = 0;
		if(isNaN(coupon)) coupon = 0;
		if(isNaN(defaultprice)) defaultprice = 0;
		if(isNaN(deli_price)) deli_price = 0;
		var gdiscount = 0;

		setprice = parseInt((defaultprice+deli_price)-userMileage-coupon); // 결제 금액

		// 적립금 사용
		if(setprice< 0 ) {
			userMileage = parseInt( userMileage - ( 0 - setprice ) );
			alert("적립금사용은 "+userMileage+"까지 사용가능합니다.\n\n* 쿠폰 사용 및 할인정책에 의하여 변경 또는 원가 변동의 의한것입니다.");
			setprice = 0;
		}
		if(setprice==0 ) {
			document.getElementById("orderPaySelt").style.display = "none";
			// document.getElementById("orderPaySel").style.display = "none";
			document.getElementById("paytype_1").checked = true;
			document.getElementById("pay_data1").selectedIndex  = 1;
		}
		else{
			document.getElementById("orderPaySelt").style.display = "block";
			// document.getElementById("orderPaySel").style.display = "block";
		}
		$("#usereserve").val(userMileage);


		//등급 할인
		var gdiscount = 0;
		var ispaymentcheck=false;
		for(i=0;i<document.form1.sel_paymethod.length;i++) {
			if(document.form1.sel_paymethod[i].checked==true) {
				document.form1.paymethod.value=document.form1.sel_paymethod[i].value;
				ispaymentcheck=true;
				break;
			}
		}
		if( isNaN(groupCode) && ispaymentcheck==true && $("#possible_group_dis_used").val() == "Y" && setprice >= groupDiscMoney && setprice >= groupDiscUseMoney ) {
			if ( groupCode == 'SW' ) {
				gdiscount=groupDiscMoney;
            } else if(groupCode == 'SP'){
                gdiscount= Math.floor(((setprice*(groupDiscMoney/100))/100)*100);
			}
			// 결제 방식에 따른 처리
			// "B"=>"현금","C"=>"카드","N"=>"현금/카드"
			if( groupDiscPayTypeCode != "N" ) {
				var paymethodList = ( groupDiscPayTypeCode == "B" ) ? "B|V|O" : "C|M";
				var paymethod = $("#paymethod").val();
				if( paymethodList.indexOf(paymethod) < 0 ) {
					gdiscount = 0;
				}
			}
		}

		// 등급할인 적용 안됨 쿠폰 사용 메세지
		if ($("#possible_group_dis_used").val() == "N") {
			$("#couponEventMsg").html("<br><font color='blue'>사용하신 쿠폰 중 등급할인 혜택을 받을 수 없는 쿠폰이 포함되었습니다.</font>");
		} else {
			$("#couponEventMsg").html("");
		}

		setprice -= gdiscount;

		gdiscount = 0-gdiscount;
		$("#groupdiscount").val(gdiscount);

		// 사은품 적용
		if(setprice < gift) gift = setprice; // 사은품 사용가능 금액
		giftchoices(gift);

		// 총결제금액
		var total_price =  parseInt( setprice );

		// 디스플레이 ( UI 표시 )
		$("#disp_coupon").text(number_format(0-coupon)); // 할인쿠폰 사용금액
		$("#disp_reserve").text(number_format(0-userMileage)); // 적립금 사용액
		$("#disp_groupdiscount").text(number_format(gdiscount));	// 등급할인
		$("#disp_deliprice").text(number_format(deli_price));	// 배송금액
		$("#disp_last_price").text(number_format(total_price));	// 최종결제금액
		var pmBtn = $("#pmBtn").text(number_format(total_price));	// 최종결제금액
	}
	// 재 계산기 끝 **************************************************************************************
</script>
<?
#무이자 상품과 일반 상품이 주문할 경우
if($basketItems['productcnt']!=$basketItems['productcnt'] && $basketItems['productcnt']>0 && $_data->card_splittype=="O") {
	echo "<script> alert('[안내] 무이자적용상품과 일반상품을 같이 주문시 무이자할부적용이 안됩니다.');</script>";
}

if($basketItems['sumprice']<$_data->bank_miniprice) {
	echo "<script>alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');location.href='./main.php';</script>";
	exit;
} else if($basketItems['sumprice']<=0) {
	echo "
		<script>
			alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다.');
			location.href='./main.php';
		</script>
	";
	exit;
}

?>

<form name="form1" action="ordersend.php" method=post>
<input type="hidden" name="sumprice" id="sumprice" value="<?=$basketItems['sumprice']?>" />

<!-- 쿠폰적용 값 ( 구분기호 : | )) -->
<!-- 사용쿠폰리스트 -->
<input type="hidden" name="couponlist" id="couponlist" value="" />
<!-- 사용쿠폰 할인액 리스트 -->
<input type="hidden" name="dcpricelist" id="dcpricelist" value="" />
<!-- 사용쿠폰 적립액 리스트 -->
<input type="hidden" name="drpricelist" id="drpricelist" value="" /><!-- -->
<!-- 사용쿠폰상품리스트 --><!--  (쿠폰코드_상품코드_옵션1idx_옵션2idx) -->
<input type="hidden" name="couponproduct" id="couponproduct" value="" />
<!-- 현금 사용시 가능한 쿠폰이 선택된 경우 --><!-- if (현금 사용시 가능한 쿠폰이 선택된 경우 ) Y else N -->
<input type="hidden" name="bank_only" id="couponBankOnly" value="N" />
<!-- 배송비 -->
<input type='hidden' name='deliprice' id='deliprice' value='<?=$basketItems['deli_price']?>'>
<!-- 쿠폰적립총액 -->
<input type="hidden" name="coupon_reserve" id="coupon_reserve" value="0" />
<!-- 결제방식 -->
<input type="hidden" name="paymethod" id="paymethod" value="0" />
<!-- 적립금 적용 불가 상품 제외한 적용가능한 적립금 금액 , 사용 적립금이 okreserve 보다 작아야 함 -->
<input type="hidden" name="okreserve" id="okreserve" value="<?=$okreserve?>" />
<!-- 결제 타입(?) 선물하기 일경우 (?) -->
<input type=hidden name="ordertype" value="<?=$ordertype?>" />
<!-- 면세? 이건 어떻게 활용?? -->
<input type="hidden" name="tax_free" value="<?=$basketItems['tax_free']?>" />
<!-- 사은품 사용가능 금액 -->
<input type="hidden" name="possible_gift_price" id="possible_gift_price" value="<?=$basketItems['gift_price']?>" />
<!-- 사은품 사용가능 여부 (Y/N) -->
<input type="hidden" name="possible_gift_price_used" id="possible_gift_price_used" value="Y" />
<!-- 회원 등급 할인 혜택 여부 (Y/N) -->
<input type="hidden" name="possible_group_dis_used" id="possible_group_dis_used" value="Y" />

<!-- 주문메세지 타입 -->
<input type="hidden" name="msg_type" value="1" />
<!-- 지역별 추가 배송료..???? -->
<input type="hidden" name="addorder_msg" value="" />

<!-- 쿠폰 할인 정보 -->
<input type="hidden" name="basketTempList" id="basketTempList" value="" />

<!-- 회원그룹(추가)할인 -->
<input type="hidden" name="groupdiscount" id="groupdiscount" value="0" />

<input type="hidden" name="process" value="N" />
<!-- <input type=hidden name=paymethod> --><!-- 결제를 현재페이지에서 하기위해 주석처리 -->
<!-- <input type=hidden name=pay_data1> --><!-- 결제를 현재창 페이지서 하기위해 주석처리 팝업에서 opener로 넘기는 값이었음 -->
<input type="hidden" name="pay_data2" />
<input type="hidden" name="sender_resno" />
<input type="hidden" name="receiveDate" value="<?=$receiveDate?>"/>
<input type="hidden" name="receiveTime" value="<?=$receiveTime?>"/>
<? /*
<input type="hidden" name="sender_tel" />
<input type="hidden" name="receiver_tel1" />
<input type="hidden" name="receiver_tel2" />
*/ ?>
<input type="hidden" name="receiver_addr" />
<input type="hidden" name="order_msg" />
<!-- <input type=hidden name=gift_price value="<?//=$basketItems['gift_price']?>"> -->
<?
	if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {
?>
	<input type="hidden" name="shopurl" value="<?=getenv("HTTP_HOST")?>" />
<?
	}
?>
<? include $skinPATH."order.php"; ?>
</form>


<!-- <form name="couponform" action="<?=$Dir.FrontDir?>couponpop_new.php" method=post target=couponpopup>
<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>">
<input type="hidden" name="giftprice" value="<?=$basketItems['gift_price']?>">
<input type="hidden" name="usereserve" value="0">
<input type="hidden" name="total_sumprice" id="ctotal_sumprice" value="" />
</form> -->

<!-- <form name=couponissueform method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode value="">
<input type=hidden name=coupon_code value="">
<input type=hidden name=productcode value="">
</form> -->

<!-- <form name=couponform2 action="<?=$Dir.FrontDir?>coupon.php" method=post target=couponpopup>
<input type=hidden name=sumprice value="<?=$basketItems['sumprice']?>">
<input type="hidden" name="giftprice" value="<?=$basketItems['gift_price']?>">
<input type="hidden" name="usereserve">
<input type="hidden" name="total_sumprice" id="ctotal_sumprice" value="" />
</form> -->

<!-- <form name=orderpayform method=post action="<?=$Dir.FrontDir?>orderpay.php" target=orderpaypop>
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<input type=hidden name=coupon_code>
<input type=hidden name=couponlist>
<input type=hidden name=coupon_price>
<input type=hidden name=couponproduct>
<input type=hidden name=usereserve>
<input type=hidden name=email>
<input type=hidden name=mobile_num1>
<input type=hidden name=mobile_num>
<input type=hidden name=address>
</form> -->

<!-- <form name="reserve_check_form">
<input type="hidden" name="possible_total_price" id="possible_total_price" value="<?=$basketItems['sumprice']//$sumprice+$sumpricevat-$salemoney?>" />

<input type="hidden" name="possible_reserve_price" id="possible_reserve_price" value="<?=$okreserve?>" />

<input type="hidden" name="possible_gift_price" id="possible_gift_price" value="<?=$basketItems['gift_price']?>" />
<input type="hidden" name="possible_gift_price_used" id="possible_gift_price_used" value="Y" />
</form> -->
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if(document.form1.sender_name.type=="text") {
		if(document.form1.sender_name.value.length==0) {
			alert("주문자 성함을 입력하세요.");
			document.form1.sender_name.focus();
			return;
		}
		if(!chkNoChar(document.form1.sender_name.value)) {
			alert("주문자 성함에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표)는 입력하실 수 없습니다.");
			document.form1.sender_name.focus();
			return;
		}
	}

	if(document.form1.sender_tel.value.length==0) {
		alert("주문자 전화번호를 입력하세요.");
		document.form1.sender_tel.focus();
		return;
	}
	/*
	if(!IsNumeric(document.form1.sender_tel.value)) {
		alert("주문자 전화번호는 숫자만 입력하세요.");
		document.form1.sender_tel.focus();
		return;
	}
	if(document.form1.sender_tel1.value.length==0) {
		alert("주문자 전화번호를 입력하세요.");
		document.form1.sender_tel1.focus();
		return;
	}
	if(document.form1.sender_tel2.value.length==0) {
		alert("주문자 전화번호를 입력하세요.");
		document.form1.sender_tel2.focus();
		return;
	}
	if(document.form1.sender_tel3.value.length==0) {
		alert("주문자 전화번호를 입력하세요.");
		document.form1.sender_tel3.focus();
		return;
	}

	if(!IsNumeric(document.form1.sender_tel1.value)) {
		alert("주문자 전화번호 입력은 숫자만 입력하세요.");
		document.form1.sender_tel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_tel2.value)) {
		alert("주문자 전화번호 입력은 숫자만 입력하세요.");
		document.form2.sender_tel2.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_tel3.value)) {
		alert("주문자 전화번호 입력은 숫자만 입력하세요.");
		document.form3.sender_tel3.focus();
		return;
	}
	document.form1.sender_tel.value=document.form1.sender_tel1.value+"-"+document.form1.sender_tel2.value+"-"+document.form1.sender_tel3.value;
	*/

	if(document.form1.sender_email.value.length>0) {
		if(!IsMailCheck(document.form1.sender_email.value)) {
			alert("주문자 이메일 형식이 잘못되었습니다.");
			document.form1.sender_email.focus();
			return;
		}
	}

	/*
	if(document.form1.receiver_name.value.length==0) {
		alert("받는 분 성함을 입력하세요.");
		document.form1.receiver_name.focus();
		return;
	}
	if(!chkNoChar(document.form1.receiver_name.value)) {
		alert("받는 분 성함에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표)는 입력하실 수 없습니다.");
		document.form1.receiver_name.focus();
		return;
	}
	if(document.form1.receiver_tel1.value.length==0) {
		alert("받는 분 연락처를 입력하세요.");
		document.form1.receiver_tel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel1.value)) {
		alert("받는 분 전화번호는 숫자만 입력하세요.");
		document.form1.receiver_tel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel2.value)) {
		alert("받는 분 휴대폰 번호는 숫자만 입력하세요.");
		document.form1.receiver_tel2.focus();
		return;
	}

	if(document.form1.receiver_tel11.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_tel11.focus();
		return;
	}
	if(document.form1.receiver_tel12.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_tel12.focus();
		return;
	}
	if(document.form1.receiver_tel13.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_tel13.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel11.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel11.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel12.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel12.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel13.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel13.focus();
		return;
	}
	document.form1.receiver_tel1.value=document.form1.receiver_tel11.value+"-"+document.form1.receiver_tel12.value+"-"+document.form1.receiver_tel13.value;

	if(document.form1.receiver_tel21.value.length==0) {
		alert("받는분 비상전화번호를 입력하세요.");
		document.form1.receiver_tel21.focus();
		return;
	}
	if(document.form1.receiver_tel22.value.length==0) {
		alert("받는분 비상전화번호를 입력하세요.");
		document.form1.receiver_tel22.focus();
		return;
	}
	if(document.form1.receiver_tel23.value.length==0) {
		alert("받는분 비상전화번호를 입력하세요.");
		document.form1.receiver_tel23.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel21.value)) {
		alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel21.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel22.value)) {
		alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel22.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel23.value)) {
		alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel23.focus();
		return;
	}
	document.form1.receiver_tel2.value=document.form1.receiver_tel21.value+"-"+document.form1.receiver_tel22.value+"-"+document.form1.receiver_tel23.value;
	*/

	/*if(document.form1.rpost1.value.length==0 || document.form1.rpost2.value.length==0) {
		alert("우편번호를 선택하세요.");
		get_post('r');
		return;

	if(document.form1.rpost1.value.length <=0 || document.form1.rpost1.value.length>=6) {
		alert("우편번호 입력이 옳바르지 않습니다.");
		addr_search_for_daumapi("rpost1", "raddr1", "raddr2");
		return;
	}
	if(document.form1.raddr1.value.length==0) {
		alert("주소를 입력하세요.");
		document.form1.raddr1.focus();
		return;
	}
	if(document.form1.raddr2.value.length==0) {
		alert("상세주소를 입력하세요.");
		document.form1.raddr2.focus();
		return;
	}
	if(!chkNoChar(document.form1.raddr2.value)) {
		alert("상세주소에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표)는 입력하실 수 없습니다.");
		document.form1.raddr2.focus();
		return;
	}
	}*/
	<? if(strlen($_ShopInfo->getMemid())==0) { ?>
	if(document.form1.dongi[0].checked!=true) {
		alert("개인정보보호정책에 동의하셔야 비회원 주문이 가능합니다.");
		document.form1.dongi[0].focus();
		return;
	}
	<?}?>
	<? if(strlen($_ShopInfo->getMemid())>0) { ?>
		<? if($_data->reserve_maxuse>=0 && strlen($okreserve)>0 && $okreserve>0) { ?>
		if(document.form1.usereserve.value > <?=$okreserve?>) {
			alert("적립금 사용가능금액보다 큽니다.");
			document.form1.usereserve.focus();
			return;
		} else if(document.form1.usereserve.value < 0) {
			alert("적립금은 0원보다 크게 사용하셔야 합니다.");
			document.form1.usereserve.focus();
			return;
		}

		if(document.form1.usereserve.value > 0) {
			if(document.form1.usereserve.value < <?=$_data->reserve_maxuse?>) {
				alert("적립금은 "+"<?=number_format($_data->reserve_maxuse)?>"+"원 이상 사용하셔야 합니다.");
				document.form1.usereserve.value="<?=$_data->reserve_maxuse?>";
				document.form1.usereserve.focus();
				return;
			}
		}
		<? } ?>
		
		<? if($_data->reserve_maxuse>=0 && strlen($okreserve)>0 && $okreserve>0 && $_data->coupon_ok=="Y" && $rcall_type=="N") { ?>
		//if(document.form1.usereserve.value>0 && document.form1.coupon_code.value.length==8){
			if(document.form1.usereserve.value>0 && document.form1.couponlist.value.length>8){
			alert('적립금과 쿠폰을 동시에 사용이 불가능합니다.\n둘중에 하나만 사용하시기 바랍니다.');
			document.form1.usereserve.focus();
			return;
		}
		<? } ?>

		<? if($_data->reserve_maxuse>=0 && $bankreserve=="N") { ?>
		if (document.form1.usereserve.value>0) {
			var paymethod = document.form1.paymethod.value;
			if(paymethod!="B") {
				alert('적립금은 현금결제시에만 사용이 가능합니다.\n현금결제로 선택해 주세요');
				document.form1.paymethod.value="";
				return;
			}
		}
		<? } ?>
	<? } ?>

	//sks 추가 	

	//결제방법 선택에서 무통장, 신용카드...
	var is_paymethod;
	var selected_paymethod;

	if ($('#orderPaySelt').css('display') == "block") {
		if(document.form1.sel_paymethod.length == 1) {//라디오 박스가 1개라면
			if(document.form1.sel_paymethod.checked != true){
				alert("결제방법이 선택 되지 않았습니다.");
				return;
			}else{
				document.form1.paymethod.value = document.form1.sel_paymethod.value;
				is_paymethod = true;
			}
			document.form1.paymethod.checked = true;
			selected_paymethod = document.form1.paymethod.value;
			//is_paymethod = true;
		} else if (document.form1.sel_paymethod.length > 1) {//1개 이상
			var is_paymethod = false;
			var selected_paymethod = "";
			for (i=0;i<document.form1.sel_paymethod.length;i++) {//결제방식을 선택했는지 여부
				if(document.form1.sel_paymethod[i].checked==true){	
					if (document.form1.sel_paymethod[i].value == "B") {
						var lastPrice = $('#disp_last_price').text();
						lastPrice = parseInt(lastPrice.replace(/,/g,""));

						if (lastPrice == 0) {
							is_paymethod = true;
							selected_paymethod = document.form1.sel_paymethod[i].value;
						} else {
							alert('결제방식을 선택하세요'); return;
						}
					} else {
						is_paymethod = true;
						selected_paymethod = document.form1.sel_paymethod[i].value;
					}
				}
			}
			if(is_paymethod==false) {	
				alert("결제방식을 선택하세요");	return;
			}		
		}	
	}

	//무통장 입급을 선택했다면
	var is_pay_data1;
	var selected_pay_data1;
	if(selected_paymethod=="B"){
		var _account = document.getElementById("pay_data1");
		var option_value = _account.options[_account.selectedIndex].value;
		if(option_value == 'dont'){
			alert("입금계좌를 선택하세요");
			//_account.focus();
			return;
		}
	}
	
//	document.form1.receiver_addr.value = "우편번호 : " + document.form1.rpost1.value + "-" + document.form1.rpost2.value + "\n주소 : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;
//	document.form1.receiver_addr.value = "우편번호 : " + document.form1.rpost1.value + "\n주소 : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;

	<? if($_data->coupon_ok=="Y" && strlen($_ShopInfo->getMemid())>0) { ?>
		if (document.form1.bank_only.value=="Y") {
			var paymethod = document.form1.paymethod.value;
			if(paymethod!="B") {
				alert("선택하신 쿠폰은 현금결제만 가능합니다.\n현금결제로 선택해 주세요");
				document.form1.paymethod.value="";
				return;
			}
		}
	<? } ?>
		document.form1.order_msg.value="";
		if(document.form1.process.value=="N") {
		<? if(strlen($etcmessage[1])>0) {?>
			if(document.form1.nowdelivery.checked==true) {
				document.form1.order_msg.value+="<font color=red>희망배송일 : 가능한 빨리배송</font>";
			} else {
				document.form1.order_msg.value+="<font color=red>희망배송일 : "+document.form1.year.value+"년 "+document.form1.mon.value+"월 "+document.form1.day.value+"일";
				<? if(strlen($etcmessage[1])==6) { ?>
				document.form1.order_msg.value+=" "+document.form1.time.value;
				<? } ?>
				document.form1.order_msg.value+="</font>";
			}
		<? } ?>
			/*

		<? if($etcmessage[2]=="Y") { ?>
			if(document.form1.bankname.value.length>1 && (document.form1.paymethod.length==null && paymethod=="B")) {
				if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
				document.form1.order_msg.value+="입금자 : "+document.form1.bankname.value;
			}
		<? } ?>
	*/
			//지역별 추가배송료 확인
		<?
	/*
			echo "address = \" \"+document.form1.raddr1.value;\n";
			$array_deli = explode("|",$_data->deli_area);
			$cnt= floor(count($array_deli)/2);
			for($i=0;$i<$cnt;$i++){
				$subdeli=explode(",",$array_deli[$i*2]);
				$subcnt=count($subdeli);
				echo "if(";
				for($j=0;$j<$subcnt;$j++){
					if($j!=0) echo " || ";
					echo "address.indexOf(\"".$subdeli[$j]."\")>0";
				}
				echo "){ if(!confirm('";
				if($array_deli[$i*2+1]>0) echo "해당 지역은 배송료 ".number_format($array_deli[$i*2+1])."원이 추가됩니다.";
				else echo "해당 지역은 배송료 ".number_format(abs($array_deli[$i*2+1]))."원이 할인됩니다.";
				echo "')) return;}\n";
			}
	*/
		?>
		if(document.form1.addorder_msg=="[object]") {
			if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
			document.form1.order_msg.value+=document.form1.addorder_msg.value;
		}
		//document.form1.process.value="Y";
		// document.form1.target = "PROCESS_IFRAME"; //sks

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
		//document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>order.php';
<?}?>	
		if (confirm('꽃/식물 주문시 생화의 특성상 결제 후 취소가 어려운 점 양해 부탁 드립니다.')) {
			document.form1.submit();
		}
		//document.all.paybuttonlayer.style.display="none";
		//document.all.payinglayer.style.display="block";

		//if(paymethod!="B") ProcessWait("visible");

	} else {
		ordercancel();
	}
}
//-->
</SCRIPT>

<script>

	function ordercancel(gbn) {
		if(gbn=="cancel" && document.form1.process.value=="N") {
		<?
			if($aoidx==""){
		?>
				callTimesale();
		<?
			}
			else{
		?>
				document.location.href="proposalList.php";
		<?
			}
		?>
		} else {
			if (PROCESS_IFRAME.chargepop) {
				if (gbn=="cancel") alert("결제창과 연결중입니다. 취소하시려면 결제창에서 취소하기를 누르세요.");
			} else {
				PROCESS_IFRAME.PaymentOpen();
			}
		}
	}
	function showBankAccount(str){
		if(str=="show"){
			document.getElementById('pay_account_list').style.display = '';
		}else{
			document.getElementById('pay_account_list').style.display = 'none';
		}
	}
</script>


<? include "footer.php"; ?>

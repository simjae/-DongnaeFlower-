<?
//쿠폰관련 배송비 오류 수정 2016-07-18 Seul
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");
include_once($Dir."app/inc/function.php");

if(strlen($_ShopInfo->getMemid())==0) {
    exit;
}

if(true !== checkGroupUseCoupon($groupname)) _alert($groupname.' 회원 등급은 쿠폰 사용이 불가능합니다.','0');


if( $_REQUEST['offlinecoupon'] == "popup" ) {
    $onloadOfflinecouponAuthPop = " onload=\"offlinecoupon_auth();\"";
}

//쿠폰 발행이 있을 경우
if($_REQUEST['mode']=="coupon" && strlen($_REQUEST['coupon_code'])==8){
    $onload = '';
    $sql = "SELECT * FROM tblcouponinfo ";
    $sql.= "WHERE coupon_code = '".$_REQUEST['coupon_code']."'";
    
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
            //echo $sql;
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
                    //echo $sql;
                    mysql_query($sql,get_db_conn());
                    $onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
                } else {
                    $onload="<script>alert(\"이미 쿠폰을 발급받으셨습니다.\\n\\n해당 쿠폰은 재발급이 불가능합니다.\");</script>";
                }
            }
        }
    }
    mysql_free_result($result);
    
    if(_empty($onload)){
        echo $onload;
    }
    ?>
	<script language="javascript" type="text/javascript">
		document.location.replace('/m/couponpop.php');
	</script>
	<?
	exit;
}


$productitems = array();
// 주문타입별 장바구니 테이블
$basket = basketTable($_REQUEST['ordertype']);

if($_REQUEST['otype'] == 'scheduled'){	
	include_once $Dir.'scheduled_delivery/config.php';
	$basket = getScheduledBasket();
	if(!_empty($basket['msg']) && $basket['msg'] != 'success'){
		_alert($basket['msg'],'0');
		exit;
	}
	foreach($basket['items'] as $idx=>$pd){
		if($pd['cateAuth']['coupon'] != 'Y') continue;
		$basket['items'][$idx]['realprice'] = $pd['sumprice'];
		$productitems[$idx] = &$basket['items'][$idx];
	}
	$basketItems['sumprice'] = $basket['info']['sumprice'];
}else{
	$basketItems = getBasketByArray($basket);
	
	
	foreach($basketItems['vender'] as $vd=>$val){
		foreach($val['products'] as $idx=>$pd){
			if($pd['cateAuth']['coupon'] != 'Y') continue;
			//if(!_array($productitems[$pd['productcode']])) $productitems[$pd['productcode']] = array();
			$productitems[] = &$basketItems['vender'][$vd]['products'][$idx];
		}
	}
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-cache" />
<title>쿠폰 특시 할인 적용</title>

<link rel="stylesheet" href="./css/common.css" />
<link rel="stylesheet" href="./css/skin/default.css" />

<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery-1.10.2.min.js"></script>


</head>
<style>
.coupon{
	width: 100%;
}
.coupon_wrap_title{
	text-align: center;
    font-size: 1.5em;
    color: #282828;
    font-weight: 900;
    padding: 25px 0;
    border-bottom: 1px solid #9e9e9e36;
}
.couponContainer{
	margin: 0 20px;
}
.couponWrap {
    border-radius: 14px;
    background: #f5f5f5;
    margin: 15px 0 15px 0;
	width: 100%;
    height: 100px;
	text-align: left;
}	
.couponGroup{
	padding: 20px;
}
.couponTitle {
	display: flex;
    justify-content: space-between;
    font-size: 16px;
    color: #282828;
    font-weight: 600;
}
.couponPrice {
    width: 80px;
    text-align: right;
    float: right;
}
.couponTitle{
    margin-bottom: 5px;
    font-size: 14px;
    color: #454545;
}
.couponSubTitle{
    margin-bottom: 10px;
}

.couponCount{
	color: #df196b;
    font-weight: 500;
}
.couponEndDate{
	display: flex;
	justify-content: space-between;
}
.selected{
	background-color:#ffe8e8 ;
}
.grayOut{
	background-color:#f5f5f5 ;
	opacity: 0.4;
}
.couponAppltBtn{
	color: #ffffff;
    font-size: 1.4em;
    font-weight: 900;
    text-align: center;
    height: 40px;
    padding: 20px;
    position: fixed;
    bottom: 122px;
    z-index: 910;
    width: calc(100vw - 40px);
    bottom: 0;
    height: calc(env(safe-area-inset-bottom) + 65px);
    height: calc(constant(safe-area-inset-bottom) + 65px);
    background-color: #e51e6e;
}

</style>
<body class="coupon" <?=$onloadOfflinecouponAuthPop?>>
	<article class="coupon_wrap">
		<form name="frm" action="">
			<div class="coupon_wrap_title">내 쿠폰 목록</div>
			<div class="couponAppltBtn" onclick="couponApplyBtn()">쿠폰 적용하기</div>
			<div class="couponContainer">
				<input type="hidden" class="unlimitedCouponCode">
				<input type="hidden" class="unlimitedCouponName">
				<input type="hidden" class="unlimitedCouponPrice">
				<input type="hidden" class="limitedCouponCode">
				<input type="hidden" class="limitedCouponName">
				<input type="hidden" class="limitedCouponPrice">

		<?
			$sumprice =$p_cnt = $reserveprice =0;
			$sumprice -= $usereserve;
			//_pr($productitems);

			$chkcouponcode = array();
			$ablecoupons = array();
			$mycoupons = array();
			

			foreach($productitems as $idx=>$product){
				//_pr($product);
				$coupons = array();
				$coupons = getMyCouponList($product['productcode']);
				$dan = "";
				$sale = "";
				//_pr($coupons);

				$p_cnt = $idx+1;


				$limitcoupons = array();
				$unlimitcoupons = array();

				if( $product['cateAuth']['coupon'] == "Y") {
					if(_array($coupons)){

						foreach($coupons as $coupon){
							
							//echo $coupon['etcapply_gift'].", ";
							if(!in_array($coupon['coupon_code'],$chkcouponcode)) {
								array_push($chkcouponcode,$coupon['coupon_code']);
								array_push($mycoupons,$coupon); // 적용가능 쿠폰 리스트
							}

							if($coupon['use_con_type2'] != "N"){
								if($coupon['mini_price'] > 0 && ($coupon['mini_price'] > $product['realprice'] || $product['realprice'] < 100)) continue;
								$coupon['etcapply_gift'] = ($coupon['etcapply_gift'] == "A" && $product['cateAuth']['gift'] == "Y")?'A':'';
							}
							//echo "[".$product['cateAuth']['gift']."], "."(".$coupon['etcapply_gift']."), ";

							if($coupon['vender'] > 0 && $product['vender']  != $coupon['vender']) continue;

							if($coupon['order_limit']=="N") {
								array_push($unlimitcoupons,$coupon);
							} else {
								array_push($limitcoupons,$coupon);
							}
						}
						unset($coupons);
					}
				}
				$_size = _getImageSize($product['tinyimage']['src']);
				$_wdith = $_size[width];
				$_height = $_size[height];

				if($_wdith >= $_height){
					$set_size = "width=60";
				}else{
					$set_size = "height=60";
				}

		?>
			<!-- 기존 option hidden value -->
			<input type="hidden" name="step3_<?=$p_cnt?>_price" id="step3_<?=$p_cnt?>_price" value="<?=$product['realprice']?>"/>
			<input type="hidden" name="step3_<?=$p_cnt?>_price_limit" id="step3_<?=$p_cnt?>_price_limit" value="0"/>
			<input type="hidden" name="step3_<?=$p_cnt?>_product" id="step3_<?=$p_cnt?>_product" opt1="<?=$product['opt1_idx']?>" opt2="<?=$product['opt2_idx']?>" optidxs="<?=$product['optidxs']?>" com_idx="<?=$product['com_idx']?>"  value="<?=$product['productcode']?>"/>
			
			<!-- 기존 결제  hidden value -->
			<input type="hidden" name="step3_orgprice" id="step3_orgprice" value="<?=$sumprice?>"/>
			<input type="hidden" name="step3_discount" id="step3_discount" value="0"/>
			<input type="hidden" name="total_discount" value="0" />
		<?
				
				$objIdx = 0;
			if(_array($unlimitcoupons)){

				$s_time=mktime((int)substr($coupon['date_start'],8,2),0,0,(int)substr($coupon['date_start'],4,2),(int)substr($coupon['date_start'],6,2),(int)substr($coupon['date_start'],0,4));
				$e_time=mktime((int)substr($coupon['date_end'],8,2),0,0,(int)substr($coupon['date_end'],4,2),(int)substr($coupon['date_end'],6,2),(int)substr($coupon['date_end'],0,4));
				$date=date("Y-m-d H:i:s",$s_time)."시 ~ ".date("Y.m.d H:i:s",$e_time)."시";
				$enddateN = date("Y-m-d H:i:s",$e_time);
				$enddateN = date('D M d Y H:i:s O', strtotime($enddateN));
				foreach($unlimitcoupons as $coupon){
					if($coupon['sale_type'] <= 2) {
						$dan="%";
					} else {
						$dan="원";
					}
					if($coupon['sale_type'] == 0) {
						$sale = "할인";
					} else {
						$sale = "적립";
					}
			
		?>
				<div class="couponWrap unlimit"seq="<?=$p_cnt?>"setstamp="dcsm_<?=$objIdx?>"endstamp="<?=$enddateN?>"limitFlg="N" couponIdx="<?=$objIdx?>"couponCode="<?=$coupon['coupon_code']?>"sale_type="<?=$coupon['sale_type']?>"sale_money="<?=$coupon['sale_money']?>"amount_floor="<?=$coupon['amount_floor']?>"discount=""etcapply_gift="<?=$coupon['etcapply_gift']?>"bank_only="<?=$coupon['bank_only']?>"order_limit="<?=$coupon['order_limit']?>"se_point="<?=$coupon['use_point']?>">
					<div class="couponGroup">
						<div class="couponTitle">
							<div class="couponName">[쿠폰]<?=$coupon['coupon_name']?></div>
							<div class="couponPrice"><?=number_format($coupon['sale_money']).$dan?></div>
						</div>
						<div class="couponSubTitle"><?=($coupon['mini_price']=="0"?"구매금액 제한 없이 사용 가능":number_format($coupon['mini_price']).'원 이상')?></div>
						<div class="couponEndDate">
							<div class="couponCount" id="dcsm_<?=$objIdx?>"></div>
							<div class="couponCount" style="margin-left: 45%;">중복가능쿠폰</div>
						</div>
					</div>
				</div>
		<?
				$objIdx++;
					}
				} 
			if(_array($limitcoupons)){

				$s_time=mktime((int)substr($coupon['date_start'],8,2),0,0,(int)substr($coupon['date_start'],4,2),(int)substr($coupon['date_start'],6,2),(int)substr($coupon['date_start'],0,4));
				$e_time=mktime((int)substr($coupon['date_end'],8,2),0,0,(int)substr($coupon['date_end'],4,2),(int)substr($coupon['date_end'],6,2),(int)substr($coupon['date_end'],0,4));
				$date=date("Y-m-d H:i:s",$s_time)."시 ~ ".date("Y.m.d H:i:s",$e_time)."시";
				$enddateN = date("Y-m-d H:i:s",$e_time);
				$enddateN = date('D M d Y H:i:s O', strtotime($enddateN));

				foreach($limitcoupons as $coupon){
					if($coupon['sale_type'] <= 2) {
						$dan="%";
					} else {
						$dan="원";
					}
					if($coupon['sale_type'] == 0) {
						$sale = "할인";
					} else {
						$sale = "적립";
					}
		?>
				<div class="couponWrap limit" seq="<?=$p_cnt?>"setstamp="dcsm_<?=$objIdx?>"endstamp="<?=$enddateN?>"limitFlg="Y" couponIdx="<?=$objIdx?>"couponCode="<?=$coupon['coupon_code']?>"sale_type="<?=$coupon['sale_type']?>"sale_money="<?=$coupon['sale_money']?>"amount_floor="<?=$coupon['amount_floor']?>"discount=""etcapply_gift="<?=$coupon['etcapply_gift']?>"bank_only="<?=$coupon['bank_only']?>"order_limit="<?=$coupon['order_limit']?>"se_point="<?=$coupon['use_point']?>">
					<div class="couponGroup">
						<div class="couponTitle">
							<div class="couponName">[쿠폰]<?=$coupon['coupon_name']?></div>
							<div class="couponPrice"><?=number_format($coupon['sale_money']).$dan?></div>
						</div>
						<div class="couponSubTitle"><?=($coupon['mini_price']=="0"?"구매금액 제한 없이 사용 가능":number_format($coupon['mini_price']).'원 이상')?></div>
						<div class="couponEndDate">
							<span class="couponCount" id="dcsm_<?=$objIdx?>"></span>
						</div>
					</div>
				</div>
		<?	
					$objIdx++;
					}
				} 
			
			} 
		?>
			</div>
			<!-- 쿠폰적용 할인된 정보 -->
			<input type="hidden" name="basketTempReturn" id="basketTempReturn" value="<?=$basketItems['deli_price']?>">
			<input type="hidden" name="default_deli_sumprice_org" id="default_deli_sumprice_org" value="<?=$basketItems['deli_price']?>">		
		</form>
	</article>
	<div style="text-align:center;"><a href="javascript:parent.iframePopupClose();" class="basic_button" style="padding:0px 10px;margin-bottom:15px;">닫기</a></div>

<form name=couponissueform method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode value="">
<input type=hidden name=coupon_code value="">
</form>

<input type="hidden" name="basketTempList" id="basketTempList" value="">

</body>
</html>

<script type="text/javascript">
var $j = jQuery.noConflict();

//window.moveTo(10,10);
window.resizeTo(750,600);

var orgSumPrice = parseInt("<?=$basketItems['sumprice']?>");
var totalpay = orgSumPrice;

var coupondata  = '<?=$coupon_json?>';
var coupon_limit = '<?=$_data->coupon_limit_ok?>';

var giftprice		= 0;
var discount		= 0;
var reserve		= 0;
var arrobj			= [];
var bank_only	= "N"; //현금 사용시 가능한 쿠폰이 선택된 경우 결제는 현금 및 가상계좌로만 가능해야 한다.
var giftUnUsed	= false; //사은품 불가 쿠폰 사용 여부
var GroupDisUnUsed = false; // 회원등급할인 불가 쿠폰 사용 여부

$j(document).ready(function(){
	intCountdown();
	couponClickEvent();
	// 중복사용쿠폰 선택
	$j('.unlimitcouponselect').change(function(){
		calprice();
	});
	

	// 단일사용쿠폰 선택
	$j('.limitcouponselect').change(function(){
		$this = $j(this);
		$j('.limitcouponselect').each(function(idx,el){
			if($j.trim($j($this).val()) != ''){
				if($j($this).attr('seq') != $j(el).attr('seq')){
					if($j(el).val() ==  $j($this).val()){
						alert('기존 동일 쿠폰 사용 항목이 초기화 됩니다.');
						$j(el).val('');
					}
				}
			}
		})
		calprice();
	});

	// 초기화 선택
	$j(".reset").click(function(){
		document.frm.reset();
		arrobj = [];
		//////////////////////
		$j("#total_discount_txt").html('0');
		$j("#total_payprice").html(number_format(orgSumPrice));
		basketTemp( 'default' );// 배송비 초기화
	});

	$j('#total_sumprice').html(number_format(totalpay));
	$j('#total_payprice').html(number_format(totalpay));
});

// 계산처리
function calprice(){
	// 초기화
	discount = 0;
	reserve =0;
	arrobj = [];
	giftUnUsed = false;
	GroupDisUnUsed = false;
	var deli_price = $j('#default_deli_sumprice_org').val();
	var unUsedGiftcouponList='';
	var unUsedGroupDisCouponList='';

	var basketTempList = ''; // 배송비 재 계산 리스트


	$j("#moreMsg").html(""); //사은품 불가 쿠폰 메세지
	var etcapply_gift_temp = ''; // 사은품 불가 쿠폰 메세지 적용쿠폰리스트 중복 체크

	$j("#moreMsg1").html(""); // 회원등급할인 불가 쿠폰 메세지
	var use_point_temp = ''; // 회원등급할인 불가 쿠폰 메세지 적용쿠폰리스트 중복 체크

	// 쿠폰 할인금 초기화
	$j('input[name$="_price_limit"]').each(function(idx, el) {
		el.value = 0;
	});

	// 쿠폰선택 리스트
	//$j('.couponWrap').each(function(idx,el){
	$j('.couponWrap.unlimit.selected,.couponWrap.limit.selected').each(function(idx,el){
	//$j('.couponWrap.unlimit.selected, .couponWrap.limit.selected').each(function(idx,el){
	//$j('.unlimitcouponselect option:selected, .limitcouponselect option:selected').each(function(idx,el){
		var tmp = dr = dc = 0;
		//var seq = $j(el).parent().attr('seq');
		var seq = $j(this).attr('seq');
		var oripay = parseInt($j("#step3_"+seq+"_price").val()); // 상품 원래 가격
		var limitpay = parseInt($j("#step3_"+seq+"_price_limit").val()); // 쿠폰 제한 가격 할인쿠폰적욧ㅇ
		var saletype = parseInt($j(this).attr('sale_type')); // 할인/적립 타입
		var salemoney = parseInt($j(this).attr('sale_money')); // 할인/적립 금액/%
		var amount_floor = parseInt($j(this).attr('amount_floor')); // 금액절사 1:일원/2:10원/3:백원

		/*
		saletype
		1 : + % : 적립 %
		2 : - % :  할인 %
		3 : + 원 : 적립 원
		4 : - 원 :  할인 원
		*/
		if(saletype < 3 && salemoney >= 100){
			alert('연산 오류 입니다 관리자에게 문의 하세요.');
			return false;
		}
		if(saletype < 3) {
			// % 비율
			po = 0;
			if( !isNaN(amount_floor) && amount_floor > 0 && amount_floor < 4) po += amount_floor;
			tmp = Math.floor(oripay*(salemoney/ 100) / Math.pow(10,po))*Math.pow(10,po);
		} else {
			// 금액
			tmp = salemoney;
		}
		if(saletype%2 == 1) {
			dr = tmp; // 적립
		} else {
			if (tmp > oripay) {
				dc = oripay;
			} else {
				dc = tmp; // 할인
			}
			limitpay = limitpay + dc;

			if (limitpay > oripay) {
				dc = oripay;
				limitpay = oripay;
			}
			$j("#step3_"+seq+"_price_limit").val(limitpay)
		}

		$j(this).attr('dr',dr);
		$j(this).attr('dc',dc);

		reserve += dr; // 총적립

		var couponCode = $j(this).attr('couponCode');
		var couponName = $j(this).find('.couponName').text();

		//사은품 불가 쿠폰
		if($j(this).attr('etcapply_gift') == "A"){
			if ( etcapply_gift_temp != couponCode) {
				etcapply_gift_temp = couponCode;
				//unUsedGiftcouponList += "["+$j(el).val()+"] ";
				unUsedGiftcouponList +=  (couponName+ " ");
			}
			$j("#moreMsg").html("<br><font color='red'>"+unUsedGiftcouponList+" 쿠폰사용시 사은품을 받을 수 없습니다.</font>");
			giftUnUsed = true;
		}

		// 회원등급할인 불가 쿠폰
		if( $j(this).attr('use_point') == 'A' ) {
			if ( use_point_temp != couponCode) {
				use_point_temp = couponCode;
				//unUsedGroupDisCouponList += "["+$j(el).val()+"] ";
				unUsedGroupDisCouponList +=  (couponName+ " ");
			}
			$j("#moreMsg1").html("<br><font color='blue'>"+unUsedGroupDisCouponList+" 쿠폰사용시 등급할인 혜택을 받을 수 없습니다.</font>");
			GroupDisUnUsed = true;
		}

		$j(this).attr('product',$j("#step3_"+seq+"_product").val()); //상품코드
		$j(this).attr('opt1',$j("#step3_"+seq+"_product").attr('opt1')); //상품 옵션 1 인덱스 코드
		$j(this).attr('opt2',$j("#step3_"+seq+"_product").attr('opt2')); //상품 옵션 2 인덱스 코드
		$j(this).attr('optidxs',$j("#step3_"+seq+"_product").attr('optidxs')); //상품 옵션s 인덱스 코드
		$j(this).attr('com_idx',$j("#step3_"+seq+"_product").attr('com_idx')); //상품 옵션s 인덱스 코드
		arrobj.push($j(this));

		basketTempList += $j("#step3_"+seq+"_product").val()+"_"+$j("#step3_"+seq+"_product").attr('opt1')+"_"+$j("#step3_"+seq+"_product").attr('opt2')+'_'+$j(this).attr('optidxs')+'_'+$j(this).attr('com_idx')+"|"+dc+"-";
	});

	// 배송비 재 계산
	//basketTemp( basketTempList );

	$j('input[name$="_price_limit"]').each(function(idx, el) {
		discount += parseInt(el.value); // 총할인
	});

	// 쿠폰이 결제액 보다 클 경우
	if ( orgSumPrice < discount ) discount = orgSumPrice;

	$j("#basketTempList").val(basketTempList);

	$j("#total_discount_txt").html(number_format(discount));
	$j("#total_reserve_txt").html(number_format(reserve));
	$j("#total_payprice").html(number_format(orgSumPrice - discount));
}


// 쿠폰 적용 하기
function checkCoupon(){
	var couponlist = ""; // 쿠폰 리스트
	var dcpricelist = ""; // 할인액 리스트
	var drpricelist = ""; // 적립액 리스트
	var couponproduct = ""; // 쿠폰사용 상품 리스트 (쿠폰코드_상품코드_옵션1idx_옵션2idx)
	var couponBankOnly = ""; // if (현금 사용시 가능한 쿠폰이 선택된 경우 ) Y else N


	$j(arrobj).each(function(idx,el){
		couponlist += "|"+$j(el).attr('couponcode');
		dcpricelist += "|"+$j(el).attr('dc');
		drpricelist += "|"+$j(el).attr('dr');
		couponproduct += "|"+$j(el).attr('couponcode')+'_'+$j(el).attr('product')+'_'+$j(el).attr('opt1')+'_'+$j(el).attr('opt2')+'_'+$j(el).attr('optidxs')+'_'+$j(el).attr('com_idx');
		if($j(el).attr('bank_only') == "Y") couponBankOnly = "Y";

	});


	parent.document.getElementById("couponlist").value = couponlist;
	parent.document.getElementById("dcpricelist").value = dcpricelist;
	parent.document.getElementById("drpricelist").value = drpricelist;
	parent.document.getElementById("couponproduct").value = couponproduct;
	parent.document.getElementById("couponBankOnly").value = couponBankOnly;

	if(parent.document.getElementById("possible_gift_price_used")) parent.document.getElementById("possible_gift_price_used").value = ( giftUnUsed ) ? "N" : "Y"; // 사은품 불가 쿠폰사용
	if(parent.document.getElementById("possible_group_dis_used")) parent.document.getElementById("possible_group_dis_used").value = ( GroupDisUnUsed ) ? "N" : "Y";  // 회원등급 할인 중복 불가 쿠폰사용
	if(parent.document.getElementById("deliprice")) parent.document.getElementById("deliprice").value = $j('#basketTempReturn').val(); // 배송비

	parent.document.getElementById("coupon_price").value = discount; // 총할인
	parent.document.getElementById("coupon_reserve").value = reserve; // 총적립

	if(parent.document.getElementById("basketTempList")) parent.document.getElementById("basketTempList").value = $j("#basketTempList").val(); // 할인 정보

//	alert(parent.document.getElementById("coupon_price").value + "/" + discount)
	parent.solvPrice();

//	window.close();
	parent.iframePopupClose();
}

// 취소
function cancelCoupon(){
	parent.resetCoupon();
//	window.close();
	parent.iframePopupClose();
}

// 쿠폰 다운로드
function issue_coupon(coupon_code,productcode){
	document.couponissueform.mode.value="coupon";
	document.couponissueform.coupon_code.value=coupon_code;
	document.couponissueform.submit();
}



// 오프라인 쿠폰 등록
function offlinecoupon_auth () {
	window.open('/front/offlinecoupon_auth.php?reloadchk=no','OffLineCoupon','width=300,height=200');
}


// 쿠폰 할인 적용 배송비 재계산
// ex ) basketTemp( '002001000000000003_0_0|5000-002002000000000002_2_3|5000' );
//	상품코드_옵션1인덱스_옵션2인덱스|할인가격-상품코드_옵션1인덱스_옵션2인덱스|할인가격
// "-" : 상품 리스트 구분 , "|" : 상품키|할인가격 구분
function basketTemp( code ) {
	console.log( code );
	if( code == 'default' ) {
		var result = <?=!_empty($basketItems['deli_price'])?$basketItems['deli_price']:0?>;
		$j('#basketTempReturn').val(result);
		$j('#total_deli_price').html(number_format(parseInt(result)));
	} else {
		$j.post(
			"basket.temp.php",
			{ code:code },
			function(result){
				$j('#basketTempReturn').val(result);
				$j('#total_deli_price').html(number_format(parseInt(result)));
				//alert("총 배송료 : "+result);
				//return result;
			}
		);
	}
}
function reverse_counter(va1, va2){
	today = new Date();
	d_day = new Date(va2);
	// alert(d_day);
	days = (d_day - today) / 1000 / 60 / 60 / 24;
	daysRound = Math.floor(days);
	hours = (d_day - today) / 1000 / 60 / 60 - (24 * daysRound);
	hoursRound = Math.floor(hours);
	minutes = (d_day - today) / 1000 /60 - (24 * 60 * daysRound) - (60 * hoursRound);
	minutesRound = Math.floor(minutes);
	seconds = (d_day - today) / 1000 - (24 * 60 * 60 * daysRound) - (60 * 60 * hoursRound) -
	(60 * minutesRound);
	secondsRound = Math.round(seconds);


		sec = "초 ";
		min = "분 ";
		hr = "시간 ";
		dy = "일 ";

		if (hoursRound < 10) {
			hoursRound = "0" + hoursRound;
		}
		
		if (minutesRound < 10) {
			minutesRound = "0" + minutesRound;
		}
		
		if (secondsRound < 10) {
			secondsRound = "0" + secondsRound;
		}

		// $j("#"+va1).html('<font class="fc_dgry fw_bold">' + hoursRound + '<font class="fc_dgry fw_bold">:</font>' + '<font class="fc_pink fw_bold">' + minutesRound + '</font>' + '<font class="fc_dgry fw_bold">:</font>' + '<font class="fc_pink fw_bold">' + secondsRound + '</font>' );
		$j("#"+va1).html(daysRound + dy + hoursRound + hr + minutesRound + min + " 남음");
}
function intCountdown(){
	$j('.couponWrap').each(function(idx,el){
		dday = $j(el).attr('endstamp');
		sid = $j(el).attr('setstamp');
		reverse_counter(sid, dday);

	});
	setTimeout("intCountdown()", 1000);
}
//단일쿠폰이 클릭시 나머지 단일쿠폰은 회색처리 
function couponClickEvent(){
	$j('.couponWrap').click(function(){
		var limitFlg = $j(this).attr('limitFlg');
		var couponIdx = $j(this).attr('couponIdx');
		var couponCode = $j(this).attr('couponCode');
		var couponName = $j(this).find('.couponName').text();
		var couponPrice = $j(this).find('.couponPrice').text();

		if ($j(this).hasClass('selected')) {
			$j(this).removeClass('selected');
			$j(this).removeClass('grayOut');
		} else {
			$j(this).addClass('selected');
			$j(this).removeClass('grayOut');
		}

		var couponLength = $j('.couponWrap').length;
		for (var i=0; i<couponLength; i++) {
			var couponNum = 0;
			if (couponIdx != i) {
				var couponWrap = $j('.couponWrap').eq(i);
				if (limitFlg == couponWrap.attr('limitFlg')) {
					couponWrap.removeClass('selected');
					couponWrap.addClass('grayOut');
				}
			}
		}
		
		var selectedCouponCode = null;
		var selectedCouponName = null;
		var selectedCouponPrice = null;
		var resetCoupon = null;
		if (limitFlg == "Y") {
			selectedCouponCode = $j('.limitedCouponCode');
			selectedCouponName = $j('.limitedCouponName');
			selectedCouponPrice = $j('.limitedCouponPrice');
			resetCoupon = $j('.limit');
		} else {
			selectedCouponCode = $j('.unlimitedCouponCode');
			selectedCouponName = $j('.unlimitedCouponName');
			selectedCouponPrice = $j('.unlimitedCouponPrice');
			resetCoupon = $j('.unlimit');
		}

		if (couponCode == selectedCouponCode.val()) {
			resetCoupon.removeClass('grayOut');
			selectedCouponCode.val('');
		} else {
			selectedCouponCode.val(couponCode);
			selectedCouponName.val(couponName);
			selectedCouponPrice.val(couponPrice);
		}

		console.log('LIMITED : ' + $j('.limitedCouponCode').val() + ' UNLIMITED : ' + $j('.unlimitedCouponCode').val());
		console.log('LIMITED : ' + $j('.limitedCouponName').val() + ' UNLIMITED : ' + $j('.unlimitedCouponName').val());
		console.log('LIMITED : ' + $j('.limitedCouponPrice').val() + ' UNLIMITED : ' + $j('.unlimitedCouponPrice').val());

		calprice();
	
	});
}

function couponApplyBtn(){
	checkCoupon();
	
	var couponlist = ""; // 쿠폰 리스트
	var dcpricelist = ""; // 할인액 리스트
	var drpricelist = ""; // 적립액 리스트
	var couponproduct = ""; // 쿠폰사용 상품 리스트 (쿠폰코드_상품코드_옵션1idx_옵션2idx)
	var couponBankOnly = ""; // if (현금 사용시 가능한 쿠폰이 선택된 경우 ) Y else N

	$j(arrobj).each(function(idx,el){
		var limitFlg = $j(el).attr('limitFlg');
		couponlist = $j(el).attr('couponcode');
		dcpricelist = $j(el).attr('dc');
		drpricelist = $j(el).attr('dr');
		couponproduct = $j(el).attr('couponcode') +'_'+$j(el).attr('product')+'_'+$j(el).attr('opt1')+'_'+$j(el).attr('opt2')+'_'+$j(el).attr('optidxs')+'_'+$j(el).attr('com_idx');
		if($j(el).attr('bank_only') == "Y") couponBankOnly = "Y";
		
		var className = "";
		var couponName = "";
		var couponPrice = "";
		var couponParent = "";

		if (limitFlg == "Y") {
			className = "limitedCouponGroup";
			couponName = $j('.limitedCouponName').val();
			couponPrice = $j('.limitedCouponPrice').val();
			dcpricelist = Math.floor(dcpricelist/100) * 100;
			couponParent = $j('#limitedCouponName', parent.document);
			btnflg = "Y"
		} else {
			className = "unlimitedCouponGroup";
			couponName = $j('.unlimitedCouponName').val();
			couponPrice = $j('.unlimitedCouponPrice').val();
			dcpricelist = Math.floor(dcpricelist/100) * 100;
			couponParent = $j('#unlimitedCouponName', parent.document);
			btnflg = "N"
		}

		var strDiv = "";
		strDiv+= '<div class="parentCouponWrap ' + className + '" style="display: flex;justify-content: space-between;padding: 5px;"couponlist="' + couponlist + '" dcpricelist="' + dcpricelist + '" drpricelist="' + drpricelist + '" couponproduct="' + couponproduct + '" couponBankOnly="' + couponBankOnly + '">';
		strDiv+= '	<div style="display: flex; width:70%;">';
		strDiv+= '		<div class="couponRemoveBtn" btnFlag='+btnflg+'><img style="width: 8px;" src="/app/skin/basic/svg/close_small_x.svg"></div>';
		strDiv+= '		<div style="width:100%;"><div class="coutponList">' + couponName + '</div></div>';
		strDiv+= '	</div>';
		strDiv+= '	<div>' + couponPrice + '</div>';
		strDiv+= '	<input class="couponPrice" type="hidden" value="' + couponPrice + '">';
		strDiv+= '	<input class="couponList" type="hidden" value="' + $j(el).attr('couponcode') + '">';
		strDiv+= '</div>';
		couponParent.html(strDiv);
	});
	

	parent.couponRemoveBtnEvent();
	parent.iframePopupClose();

	
}

//-->
</script>
</body>
</html>

<?
include_once("header.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."app/inc/paging_inc.php");
include_once($Dir."lib/class/class.category.php");

// 카테고리 클래스
$categoryClass = new category;
$category      = json_decode($categoryClass->getCategory());
$_active = 'active';

# 썸네일 관련 파라메터 #
$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // 원본파일 경로
$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // 썸내일 저장 경로
$quality = 100;

#썸네일 관련 파라메터 끝 #
// search by alice [START]
$search_bridx = $_REQUEST["search_bridx"];
$search_price_s = $_REQUEST["search_price_s"];
$search_price_e = $_REQUEST["search_price_e"];
$search_color_idx = $_REQUEST["search_color_idx"];
$searchkey = $_REQUEST["searchkey"];


//섬네일 효과 설정값 가져오기
$preffect_sql="SELECT primg_effect, range_effect, primg_effect_section, radius_use, radius_value, radius_position FROM tblshopinfo";
$preffect_result=mysql_query($preffect_sql,get_db_conn());
$preffect_row=mysql_fetch_object($preffect_result);
$prradius_position=explode(",",$preffect_row->radius_position);

//이미지 라운드 효과 적용(모바일은 관리자 설정값/2로 적용)
if($preffect_row->radius_use=='Y' && $preffect_row->radius_value>0){
	$prradius1=($prradius_position[1]=="Y"?$preffect_row->radius_value/2:"0");
	$prradius2=($prradius_position[2]=="Y"?$preffect_row->radius_value/2:"0");
	$prradius3=($prradius_position[4]=="Y"?$preffect_row->radius_value/2:"0");
	$prradius4=($prradius_position[3]=="Y"?$preffect_row->radius_value/2:"0");
	$prradius="border-radius:".$prradius1."px ".$prradius2."px ".$prradius3."px ".$prradius4."px;overflow:hidden;";
}


//상품색상
$color_sql = "SELECT color_idx, color_name FROM tblproductcolor WHERE enabled = 'Y' ORDER BY color_idx ";
$color_result = mysql_query($color_sql,get_db_conn());
// search by alice [ END ]

$code=isset($_GET['code']) ? trim($_GET['code']) : "";
$displaymode = isset($_GET['list_type']) ? trim($_GET['list_type']) : "gallery";

#화면 모드 관련 파라메터#
$displaygallery=$displaywebzine=$displaylist="";

switch($displaymode){
	case "list":;
		$displaylist="on";
	break;
	case "webzine":
		$displaywebzine="on";
	break;
	case "gallery":
	default:
		$displaygallery="on";
	break;
}

if(strlen($code)==0) {
	Header("Location:./main.php");
	exit;
}

$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$code=$codeA.$codeB.$codeC.$codeD;

$likecode=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;


$_cdata="";
$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	//접근가능권한그룹 체크
	if($row->group_code=="NO") {
		echo "<html></head><body onload=\"location.href='./main.php'\"></body></html>";exit;
	}
	if(strlen($_ShopInfo->getMemid())==0) {
		if(strlen($row->group_code)>0) {
			echo "<html></head><body onload=\"location.href='./login.php?chUrl=".getUrl()."'\"></body></html>";exit;
		}
	} else {
		if(strlen($row->group_code)>0 && strpos($row->group_code,$_ShopInfo->getMemgroup())===false) {	//그룹회원만 접근
			echo "<html></head><body onload=\"alert('해당 카테고리 접근권한이 없습니다.');location.href='./main.php'\"></body></html>";exit;
		}
	}
	$_cdata=$row;

	// 미리보기
	if( @!preg_match( 'U', $_cdata->list_type ) AND $preview===true ) {
		$_cdata->list_type = $_cdata->list_type."U";
	}

} else {
	echo "<html></head><body onload=\"location.href='./main.php'\"></body></html>";exit;
}
mysql_free_result($result);

$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1; 

$vidx=$_REQUEST["vidx"];
$sort=$_REQUEST["sort"];
$listnum=(int)$_REQUEST["listnum"];
if($listnum<=0) $listnum=$_data->prlist_num;

$sql = "SELECT codeA, codeB, codeC, codeD FROM tblproductcode ";
if(strlen($_ShopInfo->getMemid())==0) {
	$sql.= "WHERE group_code!='' ";
} else {
	//$sql.= "WHERE group_code!='".$_ShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
	$sql.= "WHERE group_code NOT LIKE '%".$_ShopInfo->getMemgroup()."%' AND group_code!='' ";
}
$result=mysql_query($sql,get_db_conn());
$not_qry="";
while($row=mysql_fetch_object($result)) {
	$tmpcode=$row->codeA;
	if($row->codeB!="000") $tmpcode.=$row->codeB;
	if($row->codeC!="000") $tmpcode.=$row->codeC;
	if($row->codeD!="000") $tmpcode.=$row->codeD;
	$not_qry.= "AND a.productcode NOT LIKE '".$tmpcode."%' ";
}
mysql_free_result($result);


$qry = "WHERE 1=1 ";
if(eregi("T",$_cdata->type)) {	//가상분류
	$sql = "SELECT productcode FROM tblproducttheme WHERE code LIKE '".$likecode."%' ";
	if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
		$sql.= "ORDER BY date DESC ";
	}
	$result=mysql_query($sql,get_db_conn());
	$t_prcode="";
	while($row=mysql_fetch_object($result)) {
		$t_prcode.=$row->productcode.",";
		$i++;
	}
	mysql_free_result($result);

	//추가 카테고리가 있는지 체크
	$sql = "SELECT productcode FROM tblcategorycode WHERE categorycode LIKE '".$likecode."%' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$t_prcode.=$row->productcode.",";
		$i++;
	}
	mysql_free_result($result);
	//# 추가 카테고리가 있는지 체크

	$t_prcode=substr($t_prcode,0,-1);
	$t_prcode=ereg_replace(',','\',\'',$t_prcode);
	$qry.= "AND a.productcode IN ('".$t_prcode."') ";

	$add_query="&code=".$code;
} else {	//일반분류
	$qry.= "AND cc.categorycode LIKE '".$likecode."%' ";
	$add_query="&code=".$code;
}
$qry.="AND a.display='Y' ";



function getCodeLoc($code) {
	global $_ShopInfo, $Dir,$code, $vidx;
	$naviitem = array();
	
	for($i=0;$i<4;$i++){
		$tmp = array();
		
		$getsub = ($GLOBALS['code'.chr(65+$i)] == '000');
		$tmp = getCategoryItems_vender(substr($code,0,$i*3),true, $vidx);
		if(is_array($tmp) && count($tmp) > 0 && count($tmp['items']) > 0){

			if($i != '0'){
				$curCateName= '전체보기';
			}
			$sel = "";
			$cate_lis = "";
			foreach($tmp['items'] as $item){
				if($sel != 'ok'){
					for($j=0;$j<=$i;$j++){
						if($j >0 && $sel != 'selected') break;
					
						if($item['code'.chr(65+$j)] == $GLOBALS['code'.chr(65+$j)]) $sel = 'selected';
						else $sel = '';
					}
				}
				if($sel == 'selected'){
					$cate_lis .= '<li class="selectCate"><a href="/m/productlist.php?vidx='.$vidx.'&code='.$item['codeA'].$item['codeB'].$item['codeC'].$item['codeD'].'"><span>'.$item['code_name'].'</span></a></li>';
					$sel = 'ok';
					$curCateName= $item['code_name'];
				}else{
					$cate_lis .= '<li class="selectCate"><a href="/m/productlist.php?vidx='.$vidx.'&code='.$item['codeA'].$item['codeB'].$item['codeC'].$item['codeD'].'"><span>'.$item['code_name'].'</span></a></li>';
				}
			}
			array_push($naviitem,$str);
		}
		$slideM .= "
		<div class='swiper-slide'>
			<a class='fistCateName' id='prd_cate_".$i."' onclick=\"javascript:toggle('prdCateLayer_".$i."');\"><span>".$curCateName."</span></a>
			<ul style='display:none' id='prdCateLayer_".$i."' name='prdCateLayer_".$i."'>
				".$cate_lis."
			</ul>
		</div>
		";
		if($getsub) break;
	}
	return $slideM;
}

$codenavi=getCodeLoc($code);


$categoryNavi = getCategoryMobile($code, $_ShopInfo->getMemgroup(), $_ShopInfo->getMemid());


include $skinPATH."productlist.php";
?>
<script src="../js/jquery.cookie.js"></script>
<form name="form2" method="get" action="<?=$_SERVER[PHP_SELF]?>">
	<input type="hidden" name="code" value="<?=$code?>" />
	<input type="hidden" name="sort" value="<?=$sort?>" />
	<input type="hidden" name="vidx" value="<?=$vidx?>" />
	<input type="hidden" name="search_bridx" value="<?=$search_bridx?>" />
	<input type="hidden" name="search_price_s" value="<?=$search_price_s?>" />
	<input type="hidden" name="search_price_e" value="<?=$search_price_e?>" />
	<input type="hidden" name="search_color_idx" value="<?=$search_color_idx?>" />
	<input type="hidden" name="searchkey" value="<?=$searchkey?>" />
	<input type="hidden" name="list_type" value="<?=$displaymode?>" />
</form>

<script type="text/javascript" src="./js/cate_ajax.js"></script>
<script type="text/javascript" src="./js/wishlist_ajax.js"></script>

<script type="text/javascript">
	<!--
	function ClipCopy(url) {
		var tmp;
		tmp = window.clipboardData.setData('Text', url);
		if(tmp) {
			alert('주소가 복사되었습니다.');
		}
	}

	function ChangeSort(val, displaytype) {
		document.form2.sort.value=val;
		document.form2.list_type.value = displaytype;
		document.form2.submit();
	}

	function chgListType(str){
		<? if($vidx){ ?>
			location.href="productlist.php?vidx=<?=$vidx?>&code=<?=$code?>&codeA=<?=$codeA?>&codeB=<?=$codeB?>&codeC=<?=$codeC?>&codeD=<?=$codeD?>&sort=<?=$_GET[sort]?>&list_type="+str;
		<? }else{ ?>
			location.href="productlist.php?code=<?=$code?>&codeA=<?=$codeA?>&codeB=<?=$codeB?>&codeC=<?=$codeC?>&codeD=<?=$codeD?>&sort=<?=$_GET[sort]?>&list_type="+str;
		<? } ?>
	}

	function changeDisplayMode(displaymode,code,sort){
		<? if($vidx){ ?>
			location.href="productlist.php?vidx=<?=$vidx?>&code="+code+"&list_type="+displaymode+"&sort="+sort+"&search_bridx=<?=$search_bridx?>&search_price_s=<?=$search_price_s?>&search_price_e=<?=$search_price_e?>&search_color_idx=<?=$search_color_idx?>&searchkey=<?=$searchkey?>";
		<? }else{ ?>
			location.href="productlist.php?code="+code+"&list_type="+displaymode+"&sort="+sort+"&search_bridx=<?=$search_bridx?>&search_price_s=<?=$search_price_s?>&search_price_e=<?=$search_price_e?>&search_color_idx=<?=$search_color_idx?>&searchkey=<?=$searchkey?>";
		<? } ?>
		return;
	}
	//-->
</SCRIPT>

<script language="javascript">
<!--
function displayUL(target,page_cnt,k){
	kk = k -1;
	for(i=0;i<page_cnt;i++){
		document.getElementById(target+"_main_product_"+i).style.display = 'none';
		document.getElementById(target+"_page_"+i).style.display = 'none';
	}
	document.getElementById(target+"_main_product_"+kk).style.display = '';
	document.getElementById(target+"_page_"+kk).style.display = '';
}


function change_quantity(theform,gbn) {
	var frm = document.getElementById(theform);

	tmp=frm.quantity.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	if(frm.quantity.value!=tmp) {
	<? if($_pdata->assembleuse=="Y") { ?>
		if(getQuantityCheck(tmp)) {
			if(frm.assemblequantity) {
				frm.assemblequantity.value=tmp;
			}
			frm.quantity.value=tmp;
			setTotalPrice(tmp);
		} else {
			alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
			return;
		}
	<? } else { ?>
		frm.quantity.value=tmp;
	<? } ?>
	}
}

function CheckForm(theform, productcode) {
	if(theform.quantity.value.length==0 || theform.quantity.value==0) {
		alert("주문수량을 입력하세요.");
		theform.quantity.focus();
		return;
	}
	if(isNaN(theform.quantity.value)) {
		alert("주문수량은 숫자만 입력하세요.");
		theform.quantity.focus();
		return;
	}
	if(typeof(theform.option1)!="undefined" && theform.option1.selectedIndex<1) {
		alert('해당 상품의 옵션을 선택하세요.');
		theform.option1.focus();
		return;
	}
	if(typeof(theform.option2)!="undefined" && theform.option2.selectedIndex<1) {
		alert('해당 상품의 옵션을 선택하세요.');
		theform.option2.focus();
		return;
	}

	if(typeof(theform.option1)!="undefined") {
		document.getElementById("opt_idx_"+productcode).value = theform.option1.value;
		document.getElementById("opt_quantity_"+productcode).value = theform.quantity.value;
		theform.option1.value = "";
	}

	if(typeof(theform.option2)!="undefined") {
		document.getElementById("opt_idx2_"+productcode).value = theform.option2.value;
	} else if(typeof(theform.option1)!="undefined" && typeof(theform.option2)=="undefined") {
		document.getElementById("opt_idx2_"+productcode).value = 1;
	}
	
	theform.submit();
}

function mulOptChange(form, selectBox, essential) {
	var select   = form.mulopt[selectBox],
		optValue = select.options[select.selectedIndex].value,
		optPrice = select.options[select.selectedIndex].getAttribute("data-price");
}

function check_login() {
	if(confirm("로그인이 필요한 서비스입니다. 로그인을 하시겠습니까?")) {
		document.location.href="login.php?chUrl=<?=getUrl()?>";
	}
}
//-->
</script>

<? include "footer.php"; ?>
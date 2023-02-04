<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;

$basket_idx   = !_empty($_GET['idx']) ? $_GET['idx'] : $_POST['idx'];
$com_idx   = !_empty($_GET['com_idx']) ? $_GET['com_idx'] : $_POST['com_idx'];
$ordertype		= !_empty($_POST["ordertype"]) ? $_POST["ordertype"] : $_GET["ordertype"];

$basket = basketTable($ordertype);

$sql = "SELECT * FROM  ".$basket." WHERE basketidx='".$basket_idx."' ";

$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
mysql_free_result($result);

$prd_sql = "SELECT * FROM tblproduct WHERE productcode = '".$row->productcode."' ";
$prd_result = mysql_query($prd_sql,get_db_conn());
$prd_row = mysql_fetch_object($prd_result);
mysql_free_result($prd_result);

//옵션 사용여부 2016-10-04 Seul
$optClass->setOptUse($row->productcode);
$optClass->setOptType($row->productcode);


// 상품 가격
$dis_sellprice = $prd_row->sellprice + $optClass->getOptPrice($com_idx);

// 적립금 표시
$dis_reserve = $prd_row->reserve;
if ($prd_row->reservetype == "Y") {	// Y 면 %, N 이면 원
    $dis_reserve = $prd_row->sellprice * ( $prd_row->reserve / 100 );
}

$PAGE_TITLE = "장바구니 상품 옵션 변경";
?>

<link rel='stylesheet' type='text/css' href="<?=$Dir?>css/common.css" />
<link rel="stylesheet" type="text/css" href="<?=$Dir?>css/basket.css" />

<div class="newWinTitle">장바구니 상품옵션 변경</div>
	<div id="wrapOptChange">
		<div class="prInfoTable">
			<form name="optForm" id="optForm" method="POST">
			<input type="hidden" name="prdcode" value="<?=$row->productcode?>" />
			<input type="hidden" name="ordertype" value="<?=$ordertype?>" />
			<input type="hidden" name="basket_idx" value="<?=$basket_idx?>" />
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<colgroup>
					<col width="100" />
					<col width="" />
				</colgroup>
				<tr>
					<td valign="top"><img src="/data/shopimages/product/<?=$prd_row->tinyimage;?>" width="80" border="0" alt="" /></td>
					<td>
						<p class="prname"><?=$prd_row->productname?></p>
						<p class="prmsg"><?=$prd_row->prmsg?></p>
						<p>
						<?php if (count($opt_price) > 0) { // 옵션 가격이 있을 경우 원래 가격과 옵션 가격 표시 ?>
							<span class="prprice oldprice"><?=number_format($prd_row->sellprice).'원'?></span>
							<span class="optprice"><?=number_format($dis_sellprice)?></span>원&nbsp;&nbsp;
						<?php } else { ?>
							<span class="prprice"><?=number_format($dis_sellprice)?></span>원&nbsp;&nbsp;
						<?php } ?>
							<span class="savedMoney">적</span><span class="prreserve"><?=number_format($dis_reserve)?></span>원
						</p>
					<div style="width:90%;">
					<?
					echo	$optClass->createOptDetailForm($Dir, 1, $optClass->optType, $optClass->optNormalType, 0, "basketoptchange", $optClass->getOptAtt($com_idx));
					?>
					</div>
					</td>
				</tr>
			</table>
			</form>
		</div>
		<div class="saveBtnArea">
			<a href="javascript:optSave();"><span class="btn_m_gray">옵션저장</span></a>&nbsp;
			<a href="javascript:optclose();"><span class="btn_m_line">취소</span></a>
		</div>

	</div>

<script type="text/javascript">
function optSave() {

	var params = $('#optForm').serialize();

	jQuery.ajax({
		url: './basket_opt_change_ok.php',
		type: 'POST',
		data:params,
		contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
		dataType: 'html',
		success: function (result) {
			if (result){
				if(result == "1"){
					alert("이미 장바구니에 존재하는 옵션입니다.");
				}else if(result == "2"){
					alert("옵션이 수정되었습니다.");
					$("#wrap_layer_popup").dialog("close");
					//부모창 리플레쉬
						location.replace("basket.php");
				}else{
					alert("해당 옵션은 품절입니다.");
				}
			}else{
				alert("옵션을 선택해주세요.");
			}
		}
	});
}

function optclose() {
	$("#wrap_layer_popup").dialog("close");
}
</script>
<?=$onload;?>
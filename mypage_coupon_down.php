<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once("./inc/function.php");
if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:./login.php?chUrl=".getUrl());
	exit;
}

include "header.php";

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:./login.php?chUrl=".getUrl());
	exit;
}

if($_data->coupon_ok!="Y") {
	echo "<html><head><title></title></head><body onload=\"alert('본 쇼핑몰에서는 쿠폰 기능을 지원하지 않습니다.');location.href='./mypage.php'\"></body></html>";exit;
}

$cdate = date("YmdH");
if($_data->coupon_ok=="Y") {
	$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='".$cdate."' OR date_end='') ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$coupon_cnt = $row->cnt;
	mysql_free_result($result);
} else {
	$coupon_cnt=0;
}

// 정열
$sort = ( empty($_GET['sort']) )?"new":$_GET['sort'];
	if( $sort == 'end' ) {
		$SQL_SORT = "if(date_start<0,DATE_FORMAT(SYSDATE(date)+INTERVAL ABS(date_start) DAY,'%Y%m%d%H'),date_end) ASC";
	}
	if( $sort == 'new' ) {
		$SQL_SORT = "date DESC";
	}
	$sortB[$sort][0] = "<b>";
	$sortB[$sort][1] = "</b>";
	

	$limitcouponarray = array();

	$limitcpSQL = "SELECT co.coupon_code FROM tblcouponinfo AS co LEFT JOIN tblcouponissue AS ce ON co.coupon_code = ce.coupon_code WHERE ce.id = '".$_ShopInfo->getMemid()."' AND co.repeat_id = 'N' AND ce.coupon_code IS NOT NULL ORDER BY co.date DESC";
	if(false !== $limitcpRes = mysql_query($limitcpSQL,get_db_conn())){
		$limitcprowcount= mysql_num_rows($limitcpRes);

		if($limitcprowcount>0){
			while($limitcprow = mysql_fetch_assoc($limitcpRes)){
				array_push($limitcouponarray,$limitcprow['coupon_code']);
			}
		}
	}
	
	$reusedcpSQL = "SELECT co.coupon_code FROM tblcouponinfo AS co LEFT JOIN tblcouponissue AS ce ON co.coupon_code = ce.coupon_code WHERE ce.id = '".$_ShopInfo->getMemid()."' AND co.repeat_id = 'Y' AND ce.used='N' ORDER BY co.date DESC";

	if(false !== $recpRes = mysql_query($reusedcpSQL,get_db_conn())){
		$recprowcount= mysql_num_rows($recpRes);

		if($recprowcount>0){
			while($recprow = mysql_fetch_assoc($recpRes)){
				array_push($limitcouponarray,$recprow['coupon_code']);
			}
		}
	}

?>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	//탭 처리
	function DisplayMenu(index) {
		for (i=1; i<=8; i++)
		if (index == i) {
			thisMenu = eval("themeShopTab" + index + ".style");
			thisMenu.display = "";
		}else{
			otherMenu = eval("themeShopTab" + i + ".style"); 
			otherMenu.display = "none"; 
		}
	}


	// 쿠폰 발급
	<?if($_data->coupon_ok=="Y") {?>
	function issue_coupon(coupon_code){
		document.couponform.mode.value="coupon";
		document.couponform.coupon_code.value=coupon_code;
		document.couponform.submit();
	}
	<?}?>
	//-->
</script>
<div id="content">
	<div class="h_area2">
		<h2>쿠폰 다운로드</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<div class="coupon_down_wrap">
		<h2>다양한 쿠폰혜택으로 알뜰한 쇼핑하세요!</h2>
		<div class="coupon_down_list">
		<?
			// 쿠폰 불러오기
			$i=1;
			$couponSQL = "
						SELECT
							`coupon_name`,
							`coupon_code`,
							`date_start`,
							`date_end`,
							`date`,
							`productcode`,
							`sale_money`,
							`sale_type`
						FROM
							`tblcouponinfo`
						WHERE
							`issue_type` = 'Y' ";
				
					if(count($limitcouponarray)>0){
						$couponSQL .= " AND coupon_code NOT IN(".implode(",",$limitcouponarray).") ";
					}
					$couponSQL .= " ORDER BY ".$SQL_SORT;

			$couponResult=mysql_query($couponSQL,get_db_conn());

			$couponCNT = mysql_num_rows($couponResult);
			while($couponRow=mysql_fetch_assoc($couponResult)) {

				// 쿠폰 기간 확인
				if( $couponRow['date_start'] < 0 ) {
					$startTime = strtotime($couponRow['date']);
					$endTime = strtotime( abs( $couponRow['date_start'] )." day", strtotime($couponRow['date']) );
				} else {
					$startTime = strtotime($couponRow['date_start']."00");
					$endTime = strtotime($couponRow['date_end']."00");
				}

				$saleType = ( $couponRow['sale_type'] > 2 )? "적립쿠폰" : "할인쿠폰";

				// 적용 상품
				$productcode = explode( ",", $couponRow['productcode'] );

				$saleType = ( $couponRow['sale_type'] > 2 )? "적립쿠폰" : "할인쿠폰";

				$productCoupon = false;

				// 적용 카테고리 수대로
				$cate_i = 0;
				$CategoryList = "";
				foreach ( $productcode as $var ) {

					$CategoryListName = "";

					// 카테고리
					if( strlen($var) == 12 ) {

						$codeA = substr($var,0,3);
						$codeA_SQL = "SELECT `code_name` FROM `tblproductcode` WHERE `codeA` = '".$codeA."' AND `type`='L' ";
						$codeA_Result=mysql_query($codeA_SQL,get_db_conn());
						$codeA_Row=mysql_fetch_assoc($codeA_Result);
						$CategoryListName .= $codeA_Row['code_name'];

						$codeB = substr($var,3,3);
						if( $codeB > 0 ) {
							$codeB_SQL = "SELECT `code_name` FROM `tblproductcode` WHERE `codeA` = '".$codeA."' AND `codeB` = '".$codeB."' AND `type`!='L' ";
							$codeB_Result=mysql_query($codeB_SQL,get_db_conn());
							$codeB_Row=mysql_fetch_assoc($codeB_Result);
							$CategoryListName .= ">".$codeB_Row['code_name'];
						}

						$codeC = substr($var,6,3);
						if( $codeC > 0 ) {
							$codeC_SQL = "SELECT `code_name` FROM `tblproductcode` WHERE `codeA` = '".$codeA."' AND `codeB` = '".$codeB."' AND `codeC` = '".$codeC."' AND `type`!='L' ";
							$codeC_Result=mysql_query($codeC_SQL,get_db_conn());
							$codeC_Row=mysql_fetch_assoc($codeC_Result);
							$CategoryListName .= ">".$codeC_Row['code_name'];
						}

						$codeD = substr($var,9,3);
						if( $codeD > 0 ) {
							$codeD_SQL = "SELECT `code_name` FROM `tblproductcode` WHERE `codeA` = '".$codeA."' AND `codeB` = '".$codeB."' AND `codeC` = '".$codeC."' AND `codeD` = '".$codeD."' AND `type`!='L' ";
							$codeD_Result=mysql_query($codeD_SQL,get_db_conn());
							$codeD_Row=mysql_fetch_assoc($codeD_Result);
							$CategoryListName .= ">".$codeD_Row['code_name'];
						}

						$CategoryList .="<a href='./productlist.php?code=".$var."'>".$CategoryListName."</a>";

						$cate_i++;
						if( $cate_i > 0 ) $CategoryList .= ", ";

					}

					// 상품
					if( strlen($var) > 12 ) {

						$productSQL = "SELECT * FROM `tblproduct` WHERE `productcode` = '".$var."' LIMIT 1 ; ";
						$productResult=mysql_query($productSQL,get_db_conn());
						$productRow=mysql_fetch_assoc($productResult);

						// 제품 코드가 있다면 출력
						if( $productRow['pridx'] ) {

							$sellPrice = $productRow['sellprice'];

							// 쿠폰 적용 금액
							$couponPrice = abs( $sellPrice - $couponRow['sale_money'] );
							$productCoupon = true;
						}
					}
				}

				//상품
				if( $productCoupon == true ) {
		?>
			<div class="coupon_down">
				<table cellpadding="0" cellspacing="0" width="100%" class="coupon_down_table">
					<colgroup>
						<col width="25%" />
						<col width="" />
					</colgroup>
					<tr>
						<td class="coupon_down_image_wrap">
							<?
								$img_loc = "../data/shopimages/product/".$productRow['tinyimage'];
								if(!is_file($img_loc)){
									$img_loc = "../images/no_img.gif";
								}
								$getSize = _getImageSize($img_loc);
								//print_r($getSize);
								if($getSize['error'] == "false"){
									//echo "A";
									if($getSize['width']>=$getSize['height']){
										$setSize = 'width="50"';
									}else{
										$setSize = 'height="50"';
									}
								}
								
							?>
							<div class="coupon_down_image">
								<img src="<?=$img_loc?>" <?=$setSize?> />
							</div>
						</td>
						<td class="coupon_down_info_wrap">
							<table cellpadding="0" cellspacing="0" width="100%" class="coupon_down_info_table">
								<tr>
									<th>
										<b><?=$couponRow['coupon_name']?></b> [<?=$couponRow['coupon_code']?>]<br />
										<?=number_format($couponRow['sale_money'])?>원 할인
									</th>
								</tr>
								<tr>
									<td>상품판매가 : <?=number_format($sellPrice)?>원</td>
								</tr>
								<tr>
									<td>상품적용가 : <?=number_format($couponPrice)?>원</td>
								</tr>
								<tr>
									<td>
										상품명 : <a href=".productdetail_tab01.php?productcode=<?=$productRow['productcode']?>">
										<?
											if(strlen($productRow['productname'])>24){
												echo substr($productRow['productname'],0,24).'..';
											}else{
												echo $productRow['productname'];
											}
										?>
										</a>
									</td>
								</tr>
								<tr>
									<td>유효기간 : <?=date("Y/m/d",$startTime)?> ~ <?=date("Y/m/d",$endTime)?></td>
								</tr>
							</table>
							<div class="coupon_down_btn_wrap">
								<a href="#" class="basic_button" onClick="issue_coupon('<?=$couponRow['coupon_code']?>');">쿠폰받기</a>
							</div>
						</td>
					</tr>
				</table>
			</div>
		<?
				}
				// 카테고리
				if( $cate_i > 0 ) {
		?>
			<div class="coupon_down">
				<table cellpadding="0" cellspacing="0" width="100%" class="coupon_down_table">
					<colgroup>
						<col width="25%" />
						<col width="" />
					</colgroup>
					<tr>
						<td class="coupon_down_image_wrap">
							<div class="coupon_down_image">
								<img src="/data/shopimages/product/" width="50" onerror="this.src='/images/coupon_c_img.gif';"/>
							</div>
						</td>
						<td class="coupon_down_info_wrap">
							<table cellpadding="0" cellspacing="0" width="100%" class="coupon_down_info_table">
								<tr>
									<th>
										<b>
										<?
											if(strlen($couponRow['coupon_name'])>26){
												echo substr($couponRow['coupon_name'],0,26).'..';
											}else{
												echo $couponRow['coupon_name'];
											}
										?>
										</b> [<?=$couponRow['coupon_code']?>]
									</th>
								</tr>
								<!--
								<tr>
									<td>
										쿠폰번호 : <?//=$couponRow['coupon_code']?>
									</td>
								</tr>
								<tr>
									<td><?//=$saleType?></td>
								</tr>
								-->
								<tr>
									<td>
										<?=number_format($couponRow['sale_money'])?><?=($couponRow['sale_type']<3?'%':'원')?> <?=$saleType?>
									</td>
								</tr>
								<tr>
									<td>
									<?=$CategoryList?>
									</td>
								</tr>
								<tr>
									<td>
										유효기간 : <?=date("Y/m/d",$startTime)?> ~ <?=date("Y/m/d",$endTime)?>
									</td>
								</tr>
							</table>
							<div class="coupon_down_btn_wrap">
								<a href="#" class="basic_button" onClick="issue_coupon('<?=$couponRow['coupon_code']?>');">쿠폰받기</a>
							</div>
						</td>
					</tr>
				</table>
			</div>
		<?
				}
				//전체 사용
				if( $couponRow['productcode'] == "ALL" ){
		?>
			<div class="coupon_down">
				<table cellpadding="0" cellspacing="0" width="100%" border="0" class="coupon_down_table">
					<colgroup>
						<col width="25%" />
						<col width="" />
					</colgroup>
					<tr>
						<td class="coupon_down_image_wrap">
							<div class="coupon_down_image">
								<img src="/data/shopimages/product/" width="50" onerror="this.src='/images/coupon_t_img.gif';"/>
							</div>
						</td>
						<td class="coupon_down_info_wrap">
							<table cellpadding="0" cellspacing="0" class="coupon_down_info_table">
								<tr>
									<th>
										<b>
										<?
											if(strlen($couponRow['coupon_name'])>26){
												echo substr($couponRow['coupon_name'],0,264).'..';
											}else{
												echo $couponRow['coupon_name'];
											}
										?>
										</b> [<?=$couponRow['coupon_code']?>]
									</th>
								</tr>
								<!--
								<tr>
									<td>
										쿠폰번호 : <?//=$couponRow['coupon_code']?>
									</td>
								</tr>
								<tr>
									<td>
										<?//=$saleType?>
									</td>
								</tr>
								-->
								<tr>
									<td>
										<?=number_format($couponRow['sale_money'])?><?=($couponRow['sale_type']<3?'%':'원')?> <?=$saleType?>
									</td>
								</tr>
								<tr>
									<td>
										유효기간 : <?=date("Y/m/d",$startTime)?> ~ <?=date("Y/m/d",$endTime)?>
									</td>
								</tr>
							</table>
							<div class="coupon_down_btn_wrap">
								<a href="#" class="basic_button" onClick="issue_coupon('<?=$couponRow['coupon_code']?>');">쿠폰받기</a>
							</div>
						</td>
					</tr>
				</table>
			</div>
		<?
				}
			}
		?>
		</div>
	</div>
</div>

<form name="couponform" method="POST" action="couponlist_down_process.php" target="couponlistProcessFrame">
	<input type=hidden name="mode" value="">
	<input type=hidden name="coupon_code" value="">
</form>

<iframe name="couponlistProcessFrame" id="couponlistProcessFrame" style="display:none;"></iframe>
<? include "footer.php";?>

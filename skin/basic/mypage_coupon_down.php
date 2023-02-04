<? 

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
		var download_flg = $('#download_flg').val();
		var download_date = $('#download_date').val();
		document.couponform.mode.value="coupon";
		document.couponform.coupon_code.value=coupon_code;
		document.couponform.download_flg.value=download_flg;
		document.couponform.download_date.value=download_date;
		document.couponform.submit();
	}
	<?}?>
	//-->
</script>
<style>
.coupon_down_image .couponCate{
	width: 40px;
	height: 40px;
	margin-top: 10px;

}
.couponTitle{
	font-size: 16px; 
	color:#282828; 
	font-weight:600;
}
.couponSub{
	font-size: 16px; 
	color:#464646;
}
.couponDate{
	color:#969696; 
	line-height: 35px;
}
.couponDownImg{
	width: 20px;
	height: 20px; 
	margin-right:15px;
}

</style>
<div class="coupon_down_wrap">
	<div class="coupon_down_list">
	<?
		// 쿠폰 불러오기
		$i=1;
		$couponSQL = "SELECT
								cif.coupon_name,
								cif.coupon_code,
								cif.date_start,
								cif.date_end,
								cif.date,
								cif.productcode,
								cif.sale_money,
								cif.sale_type,
								cif.download_flg,
								cif.download_date,
								cd.cont
							FROM
								tblcouponinfo cif
								LEFT JOIN tblcouponDesign cd ON
								cif.img_idx = cd.idx
							WHERE
								cif.issue_type = 'Y'";
			
				if(count($limitcouponarray)>0){
					$couponSQL .= " AND cif.coupon_code NOT IN(".implode(",",$limitcouponarray).") ";
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

			//$saleType = ( $couponRow['sale_type'] > 2 )? "적립쿠폰" : "할인쿠폰";
			//$saleType = ( $couponRow['sale_type'] == 1 || $couponRow['sale_type'] == 3 )? "적립쿠폰" : "할인쿠폰";
			if ( $couponRow['sale_type'] == 1 || $couponRow['sale_type'] == 3 ) {
				$saleType = "적립쿠폰";
			}

			// 적용 상품
			$productcode = explode( ",", $couponRow['productcode'] );

			//$saleType = ( $couponRow['sale_type'] > 2 )? "적립쿠폰" : "할인쿠폰";
			//$saleType = ( $couponRow['sale_type'] == 2 || $couponRow['sale_type'] == 4 )? "적립쿠폰" : "할인쿠폰";
			if ( $couponRow['sale_type'] == 2 || $couponRow['sale_type'] == 4 ) {
				$saleType = "할인쿠폰";
			}

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
	?>
	<input type="hidden" id="download_flg" value="<?=$couponRow['download_flg']?>">
	<input type="hidden" id="download_date" value="<?=$couponRow['download_date']?>">
	<?
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
									<?=number_format($couponRow['sale_money'])?>원
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
							<img class="couponCate" src="/data/shopimages/coupon/<?=$couponRow['cont']?>" width="50" onerror="this.src='/images/coupon_t_img.gif';"/>
						</div>
					</td>
					<td class="coupon_down_info_wrap">
						<table cellpadding="0" cellspacing="0" class="coupon_down_info_table">
							<tr>
								<th>
									<span class="couponTitle">
									<?
										if(strlen($couponRow['coupon_name'])>26){
											echo substr($couponRow['coupon_name'],0,264);
										}else{
											echo $couponRow['coupon_name'];
										}
									?>
									</span> 
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
									<span class="couponSub"><?=number_format($couponRow['sale_money'])?><?=($couponRow['sale_type']<3?'%':'원')?> <?=$saleType?></span>
								</td>
								<td>
									<a href="#" onClick="issue_coupon('<?=$couponRow['coupon_code']?>');">
										<img class="couponDownImg"src="/app/skin/basic/svg/coupon_down.svg">
									</a>
								</td>
							</tr>
							<tr>
								<td>
									<span class="couponDate">
										<?if ($couponRow['download_flg'] == 'N') {?>
											<?=date("Y년 m월 d일",$startTime)?> ~ <?=date("Y년 m월 d일",$endTime)?>
										<?} else {?>
											다운로드 후 <?=$couponRow['download_date']?>일 까지 사용 가능합니다.
										<?}?>
									</span>
								</td>
							</tr>
						</table>
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

<form name="couponform" method="POST" action="couponlist_down_process.php" target="couponlistProcessFrame">
	<input type=hidden name="mode" value="">
	<input type=hidden name="coupon_code" value="">
	<input type=hidden name="download_flg" value="">
	<input type=hidden name="download_date" value="">
</form>

<iframe name="couponlistProcessFrame" id="couponlistProcessFrame" style="display:none;"></iframe>
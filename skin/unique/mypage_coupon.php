<? 
//include_once('header.php'); 
?>

<div id="content">
	<div class="h_area2">
		<h2>쿠폰내역</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	
	<!-- 쿠폰내역 -->
	<div class="coupon">
		<div class="pr_navi">
			<h3>사용가능 쿠폰 : <strong><?=$coupon_cnt?>장</strong></h3>
		</div>
		
		<div class="coupon_list">
			<ul>



<?
		$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode, ";
		$sql.= "a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, b.date_start, b.date_end ";
		$sql.= "FROM tblcouponinfo a, tblcouponissue b ";
		$sql.= "WHERE b.id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND a.coupon_code=b.coupon_code AND b.date_start<='".date("YmdH")."' ";
		$sql.= "AND (b.date_end>='".date("YmdH")."' OR b.date_end='') ";
		$sql.= "AND b.used='N' ";
		$result = mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$codeA=substr($row->productcode,0,3);
			$codeB=substr($row->productcode,3,3);
			$codeC=substr($row->productcode,6,3);
			$codeD=substr($row->productcode,9,3);

			$prleng=strlen($row->productcode);

			$likecode=$codeA;
			if($codeB!="000") $likecode.=$codeB;
			if($codeC!="000") $likecode.=$codeC;
			if($codeD!="000") $likecode.=$codeD;

			if($prleng==18) $productcode[$cnt]=$row->productcode;
			else $productcode[$cnt]=$likecode;

			if($row->sale_type<=2) {
				$dan="%";
			} else {
				$dan="원";
			}
			if($row->sale_type%2==0) {
				$sale = "할인";
			} else {
				$sale = "적립";
			}
			
			if($row->productcode=="ALL") {
				$product="전체상품";
			} else {
				$product = "";
				$sql2 = "SELECT code_name FROM tblproductcode WHERE codeA='".substr($row->productcode,0,3)."' ";
				if(substr($row->productcode,3,3)!="000") {
					$sql2.= "AND (codeB='".substr($row->productcode,3,3)."' OR codeB='000') ";
					if(substr($row->productcode,6,3)!="000") {
						$sql2.= "AND (codeC='".substr($row->productcode,6,3)."' OR codeC='000') ";
						if(substr($row->productcode,9,3)!="000") {
							$sql2.= "AND (codeD='".substr($row->productcode,9,3)."' OR codeD='000') ";
						} else {
							$sql2.= "AND codeD='000' ";
						}
					} else {
						$sql2.= "AND codeC='000' ";
					}
				} else {
					$sql2.= "AND codeB='000' AND codeC='000' ";
				}
				$sql2.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
				$result2=mysql_query($sql2,get_db_conn());
				$i=0;
				while($row2=mysql_fetch_object($result2)) {
					if($i>0) $product.= " > ";
					$product.= $row2->code_name;
					$i++;
				}
				mysql_free_result($result2);

				if($prleng==18) {
					$sql2 = "SELECT productname as product FROM tblproduct ";
					$sql2.= "WHERE productcode='".$row->productcode."' ";
					$result2 = mysql_query($sql2,get_db_conn());
					if($row2 = mysql_fetch_object($result2)) {
						$product.= " > ".$row2->product;
					}
					mysql_free_result($result2);
				}
				if($row->use_con_type2=="N") $product="[".$product."] 제외";
			}

			if($cnt>0) {
				
			}

			$s_time=mktime((int)substr($row->date_start,8,2),0,0,(int)substr($row->date_start,4,2),(int)substr($row->date_start,6,2),(int)substr($row->date_start,0,4));
			$e_time=mktime((int)substr($row->date_end,8,2),0,0,(int)substr($row->date_end,4,2),(int)substr($row->date_end,6,2),(int)substr($row->date_end,0,4));


			$date=date("Y.m.d H",$s_time)."시~".date("Y.m.d H",$e_time)."시";

?>
				<li>
					<div class="h_area3">
						<h4><?=$row->coupon_name?></h4>
					</div>
					<table class="basic_table">
						<tr>
							<th scope="row"><span>쿠폰번호</span></th>
							<td><span class="point1"><?=$row->coupon_code?></span></td>
						</tr>
						<tr>
							<th scope="row"><span>사용기간</span></th>
							<td><span><?=$date?></span></td>
						</tr>
						<tr>
							<th scope="row"><span>남은기간</span></th>
							<td><span><?=ceil(($e_time-$s_time)/(60*60*24))?>일</span></td>
						</tr>
						<tr>
							<th scope="row"><span>쿠폰 적용상품</span></th>
							<td><span><?=$product?></span></td>
						</tr>
						<tr>
							<th scope="row"><span>제한사항</span></th>
							<td><span><?=($row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상")?></span></td>
						</tr>
						<tr>
							<th scope="row"><span>혜 택</span></th>
							<td><span class="point2"><?=number_format($row->sale_money).$dan.$sale?></span></td>
						</tr>
					</table>
				</li>




<?

			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<li>쿠폰내역이 없습니다.</li>";
		}
?>
		


				
			</ul>
		
		</div>
	</div>
	<!-- //쿠폰내역 -->
	
</div>

<hr>

<? 
//include_once('footer.php'); 
?>
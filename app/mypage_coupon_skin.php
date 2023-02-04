<div id="content">
	<div class="h_area2">
		<h2>쿠폰 사용내역</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	
	<!-- 쿠폰내역 -->
	<div class="coupon">
		<div class="coupon_list">
			<div class="coupon_list_top">사용가능 쿠폰 <span class="coupon_list_value"><?=$coupon_cnt?></span>장</div>
		</div>

		<div class="coupon_prwrap">
			<h2>나의 쿠폰 목록</h2>
			<div class="coupon_pr_list">
			<?
				$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode, ";
				$sql.= "a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, b.date_start, b.date_end ";
				$sql.= "FROM tblcouponinfo a, tblcouponissue b ";
				$sql.= "WHERE b.id='".$_ShopInfo->getMemid()."' ";
				$sql.= "AND a.coupon_code=b.coupon_code AND b.date_start<='".date("YmdH")."' ";
				$sql.= "AND (b.date_end>='".date("YmdH")."' OR b.date_end='') ";
				$sql.= "AND b.used='N' LIMIT ".($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;

				$total_count = mysql_num_rows($result);
				
				if($total_count>0){
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

						if(strlen($row->coupon_name) > 26){
							$coupon_name = substr($row->coupon_name,0,26).'..';
						}else{
							$coupon_name = $row->coupon_name;
						}
						$s_time=mktime((int)substr($row->date_start,8,2),0,0,(int)substr($row->date_start,4,2),(int)substr($row->date_start,6,2),(int)substr($row->date_start,0,4));
						$e_time=mktime((int)substr($row->date_end,8,2),0,0,(int)substr($row->date_end,4,2),(int)substr($row->date_end,6,2),(int)substr($row->date_end,0,4));

						$date=date("Y.m.d H",$s_time)."시 ~ ".date("Y.m.d H",$e_time)."시";

			?>
					<table cellpadding="0" cellspacing="0" width="100%" class="coupon_pr_table">
						<thead>
						<tr>
							<td colspan="2" class="coupon_pr_num">쿠폰번호 : <?=$row->coupon_code?></td>
						</tr>
						</thead>
						<tbody>
						<tr>
							<th>쿠폰명</th>
							<td><?=$coupon_name?></td>
						</tr>
						<tr>
							<th>혜택</th>
							<td><span class="point3"><?=number_format($row->sale_money).$dan.$sale?></span></td>
						</tr>
						<tr>
							<th>적용대상</th>
							<td><?=$product?></td>
						</tr>
						<tr>
							<th>기간</th>
							<td>
								<?=$date?><br/>
								<img style="margin:0px;padding:0px;border:0px;vertical-align:text-top;margin-right:2px;" src="../images/common/mycoupon/design_mycoupon_skin_btn2.gif"/><?=ceil(($e_time-$s_time)/(60*60*24))?>일
							</td>
						</tr>
						<tr>
							<th class="lastTH">제한사항</th>
							<td class="lastTD"><?=($row->mini_price=="0"?"제한 없음":number_format($row->mini_price).'원 이상')?></td>
						</tr>
						</tbody>
					</table>
			<?
					}
					mysql_free_result($result);
				}else{
			?>
				<div>
					쿠폰 내역이 없습니다.
				</div>
			<?
				}
			?>
		</div>
		<div id="paging_container">
			<div id="paging_box">
				<ul>
					<?
						_getPage($totalRecord,$recordPerPage,$pagePerBlock,$currentPage,$pagetype); 
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
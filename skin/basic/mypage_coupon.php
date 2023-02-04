<?
	
	$currentPage = $_REQUEST["page"];
	if(!$currentPage) $currentPage = 1; 

	$recordPerPage = 3; // 페이지당 게시글 리스트 수 
	$pagePerBlock = 2; // 블록 갯수

	$pagetype="board";
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

	$totalRecord = ($row->cnt > 0)? $row->cnt:0;
?>
<style>
	.coupon_prwrap{
		margin-top: 25px;
	}
	.couponWrap{
		border-radius: 14px;
		background: #f5f5f5;
		padding: 30px;
		margin: 15px 0 15px 0;
	}
	.couponWrap .couponList .titleGroup{
		font-size: 16px;
		margin-bottom: 6px;
		font-weight: 600;
		color: #282828;
		overflow:hidden;
		justify-content: space-between;
		line-height: 24px;
	}
	
	.couponWrap .couponList .titleGroup .couponTitle{
		width: calc(100vw - 180px);
		float:left;
	}
	.couponWrap .couponList .titleGroup .couponPrice{
		width: 80px;
		text-align: right;
		float:right;
	}
	.couponWrap .couponList .supGroup{
		margin-bottom: 10px;
		font-size: 14px;
		color:#454545;
	}
	.couponWrap .couponList .countGroup{
		font-size: 14px;
		margin-top: 14px;
		display: flex;
		justify-content: space-between;
	}
	.couponWrap .couponList .countGroup .couponCount{
		color: #df196b;
		font-weight: 500;
	}
	.couponWrap .couponList .countGroup .couponUse{
		color: #959595;
	}
	.codeWrap{border-bottom: solid 1px #9e9e9e36;}
	.codeGroup{margin: 20px 20px 15px 20px;}
	.codeRow{margin-bottom: 10px;}
	.codeInfoGroup{margin-bottom: 10px;}
	.codeInfo{margin:5px 0 0 13px; color: #c4c4c4;}
	#couponcode{
		border-radius: 8px;
		border: solid 1px#e3e3e3;
		height: 40px;
		line-height: 40px;
		padding-left: 0.9em;
		box-sizing: border-box;
		background: #ffffff;
		color: #000000;
		font-size: 14px;
		width: calc(100vw - 140px);
	}
	.codeRow .codeButton{
		color: white;
		border-radius: 5px;
		background-color: #ed4380;
		padding: 11px 17px;
		margin-left: 5px;
		font-size: 13px;
	}
</style>		
	<form name="authCouponForm" method="post" onSubmit="javascript:return ()">
		<div class="codeWrap">
			<div class="codeGroup">
				<div class="codeRow" >
					<input name="couponcode" id="couponcode" type="text" placeholder="쿠폰코드를 등록해주세요" maxlength="16">
					<a class="codeButton" onclick="CheckRequestCouponForm()">쿠폰등록</a>
				</div>
				<div class="codeInfoGroup">
					<div class="codeInfo">*쿠폰번호의 [-]은 빼고 입력해 주세요.</div>	
					<div class="codeInfo">*쿠폰은 유효기간 동안만 등록,사용 가능합니다.</div>							
					<div class="codeInfo">*쿠폰은 종류에 따라 제한 조건이 있을 수 있습니다</div>							
				</div>
			</div>
		</div>
	</form>
	<div class="coupon">
		<div style="display: none;" class="coupon_list">
			<div class="coupon_list_top">사용가능 쿠폰 <span class="coupon_list_value"><?=$coupon_cnt?></span>장</div>
		</div> 
		<!-- 카운트 none처리  -->

		<div class="coupon_prwrap">
			<div class="coupon_pr_list" style="width:calc(100vw - 40px);">
			
			<!-- 쿠폰내역 -->
			<?
			$sql = "SELECT
						ci.coupon_code,
						ci.coupon_name,
						ci.sale_type,
						ci.sale_money,
						ci.bank_only,
						ci.productcode,
						ci.mini_price,
						ci.use_con_type1,
						ci.use_con_type2,
						ci.use_point,
						cis.date_start,
						cis.date_end 
					FROM
						tblcouponinfo ci
						LEFT JOIN tblcouponissue cis ON
						ci.coupon_code = cis.coupon_code
					WHERE
						cis.id='".$_ShopInfo->getMemid()."' AND
						cis.date_start<='".date("YmdH")."' AND
						(cis.date_end>='".date("YmdH")."' OR cis.date_end='') AND
						cis.used='N'";
			// echo $sql;
			$result = mysql_query($sql,get_db_conn());
			$cnt=0;

			$total_count = mysql_num_rows($result);

			if($total_count>0){
			$objIdx = 0;
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

				$coupon_name = $row->coupon_name;
				
				$s_time=mktime((int)substr($row->date_start,8,2),0,0,(int)substr($row->date_start,4,2),(int)substr($row->date_start,6,2),(int)substr($row->date_start,0,4));
				$e_time=mktime((int)substr($row->date_end,8,2),0,0,(int)substr($row->date_end,4,2),(int)substr($row->date_end,6,2),(int)substr($row->date_end,0,4));

				$date=date("Y-m-d H:i:s",$s_time)."시 ~ ".date("Y.m.d H:i:s",$e_time)."시";
				$enddateN = date("Y-m-d H:i:s",$e_time);
				$enddateN = date('D M d Y H:i:s O', strtotime($enddateN));

			?>
					<div class="couponWrap" setstamp="dcsm_<?=$objIdx?>" endstamp="<?=$enddateN?>">
						<div class="couponList">
							<div class="titleGroup">
								<div class="couponTitle">[쿠폰]<?=$coupon_name?></div>
								<div class="couponPrice"><?=number_format($row->sale_money).$dan?></div>
							</div>
							<div class="supGroup"><span><?=($row->mini_price=="0"?"구매금액 제한 없이 사용 가능":number_format($row->mini_price).'원 이상')?></span></div>
							<div class="countGroup">  
								<span class="couponCount" id="dcsm_<?=$objIdx?>"></span>
								<span class="couponUse">사용가능</span>
							</div>
						</div>
					</div>
			<?
					$objIdx++;
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
		</div>
	</div>
	<script>
	
	function reverse_counter(va1, va2){
		today = new Date();
		d_day = new Date(va2);
		days = (d_day - today) / 1000 / 60 / 60 / 24;
		daysRound = Math.floor(days);
		hours = (d_day - today) / 1000 / 60 / 60 - (24 * daysRound);
		hoursRound = Math.floor(hours);
		minutes = (d_day - today) / 1000 /60 - (24 * 60 * daysRound) - (60 * hoursRound);
		minutesRound = Math.floor(minutes);
		seconds = (d_day - today) / 1000 - (24 * 60 * 60 * daysRound) - (60 * 60 * hoursRound) - (60 * minutesRound);
		secondsRound = Math.round(seconds);

		sec = "";
		min = "분 ";
		hr = "시간 ";
		dy = "일 ";

		hoursRound = hoursRound < 10 ? "0" + hoursRound : hoursRound;
		minutesRound = minutesRound < 10 ? "0" + minutesRound : minutesRound;
		secondsRound = secondsRound < 10 ? "0" + secondsRound : secondsRound;
		
		hoursRound = hoursRound == "00" ? "<span>" + hoursRound + "</span>" : hoursRound;
		minutesRound = minutesRound == "00" ? "<span>" + minutesRound + "</span>" : minutesRound;
		
		$("#"+va1).html(daysRound + dy + hoursRound + hr + minutesRound + min + " 남음");
	}
	function intCountdown(){
		$('.couponWrap').each(function(idx,el){
			dday = $(el).attr('endstamp');
			sid = $(el).attr('setstamp');
			reverse_counter(sid, dday);

		});
		setTimeout("intCountdown()", 1000);
	}

	$(function(){
	});
	$(document).ready(function() {
		intCountdown();
		$('#couponcode').keyup(function(e){
			$(this).val($(this).val().toUpperCase());
			$(this).val($(this).val().replace(/[^0-9A-Z]/g, ''));
		});	
	});
	function CheckRequestCouponForm() {
		var f = document.authCouponForm;
		if(f.couponcode.value.length == 0){
			alert('쿠폰 번호를 입력하세요.');
			return false;
		}
		if(f.couponcode.value.length < 5){
			alert('쿠폰 번호가 맞지 않습니다. 정확히 입력해주세요!');
			return false;
		}
		
		var formData = $("form[name=authCouponForm]").serialize() ;
		$.ajax({
			type : 'post',
			url : '/api/auth_coupon_update.php',
			data : formData,
			dataType : 'json',
			error: function(xhr, status, error){
				alert("데이터 통신중에 오류가 발생했습니다.");
			},
			success : function(json){
				if(json["result"] == "Y"){
					alert('쿠폰이 등록되었습니다.');
					location.replace("/app/mypage_coupon.php?pageType=myCoupon");
				}
				else if(json["result"] == "E"){
					alert(json["message"]);
				}
			}
		});
	}
	
	</script>
<style>
.order_wrap .order_group{background:#f4f4f4;padding:20px;margin-top:8px;border-radius:4px;}
.order_wrap h2{padding-bottom:10px;margin-bottom:10px;border-bottom:solid 1px #e0e0e0;}
</style>
<?
$sql="SELECT 
			addr
			,addr1
			,addr2
			,addr3
			,addr4
			,addr5
			,aoidx
			,comment
			,createDate
			,creater
			,del_flag
			,maxPrice
			,minPrice
			,orderType
			,priceText
			,prodNum
			,productType
			,productTypeText
			,purpose
			,purposeText
			,rcvName
			,receiveDate
			,receiveTime
			,closeDate
			,closeTime
			,receiveType
			,receiveTypeText
			,status
			,style
			,styleText
			,talkOrder
			,tel
			,updateDate
			,updater
			,userid
			,zip
			,ao_addr_pointx
			,ao_addr_pointy
			,DATE_FORMAT( CONCAT( receiveDate, ' ', receivetime ) , '%Y-%m-%d %H:%i' ) AS receiveDateTime
			,DATE_FORMAT( CONCAT( closeDate, ' ', closetime ) , '%Y-%m-%d %H:%i' ) AS closeDateTime
			,DATE_FORMAT( CONCAT( closeDate, ' ', closetime ) , '%Y-%m-%d %H:%i:%s' ) >= NOW() AS orderStatus
			,(SELECT COUNT(bk.aopidx)>0 FROM tblbasket AS bk WHERE bk.aoidx = '".$aoidx."' AND bk.id = '".$_ShopInfo->getMemid()."') AS basketStatus
			,(SELECT COUNT(aopidx) FROM auction_order_proposal WHERE aoidx = '".$aoidx."' AND userid = '".$_ShopInfo->getMemid()."' AND del_flag=false) AS proposalCount
			,(SELECT COUNT(DISTINCT vender) FROM auction_order_proposal WHERE aoidx = '".$aoidx."' AND userid = '".$_ShopInfo->getMemid()."' AND del_flag=false) AS proposalShopCount

		FROM
			auction_order 
		WHERE
			userid = '".$_ShopInfo->getMemid()."'
			AND aoidx = '".$aoidx."'";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	
	
	$basketStatus = $row->basketStatus;
	$status = $row->status;
	$orderStatus = $row->orderStatus;
	
	if(!$orderStatus){
		echo "<html></head><body onload=\"alert('주문가능시간이 지난 주문서입니다. ');location.href='./proposalList.php';\"></body></html>";exit;
	} else if($basketStatus){
		echo "<html></head><body onload=\"alert('주문서에 해당되는 상품이 장바구니에 담겨있습니다.\\n변경을 원하시면 장바구니에서 상품을 삭제 후 진행해 주세요');location.href='./basket.php';\"></body></html>";exit;
	} else if ($status == 2) {
		echo "<html></head><body onload=\"alert('사용자의 요청에 의해 취소된 주문서입니다. ');location.href='./proposalList.php';\"></body></html>";exit;
	}
	
	$receiveDateTime = $row->receiveDateTime;
	$pickupArr = explode(" ",$receiveDateTime);
	$receiveDate = $pickupArr[0];
	$receiveTime = $pickupArr[1];



	$receiveDateArr = explode("-",$row->receiveDate);
	$receiveTimeArr = explode(":",$row->receiveTime);
	$receiveHour = getHourText((int)$receiveTimeArr[0]);
	$receiveDateTimeStr = $receiveDateArr[0]."년 "
	.((int)$receiveDateArr[1])."월 "
	.((int)$receiveDateArr[2])."일 "
	.$receiveHour;
	
	$closeDateArr = explode("-",$row->closeDate);
	$closeDateTimeStr = $closeDateArr[0]."년 "
	.$closeDateArr[1]."월 "
	.$closeDateArr[2]."일 "
	.$row->closeTime;
	
	$proposalShopCount = $row->proposalShopCount;
	$proposalCount = $row->proposalCount;
	$addr = str_replace("="," ",$row->addr);
	$prodNum = $row->prodNum;
	$receiveTypeText = $row->receiveTypeText;
	$purposeText = $row->purposeText;
	$productTypeText = $row->productTypeText;
	$priceText = $row->priceText;
	$styleText = $row->styleText;
	$aoidx = $row->aoidx;
	$ao_addr_pointx = $row->ao_addr_pointx;
	$ao_addr_pointy = $row->ao_addr_pointy;
	
	if($proposalShopCount > 0){
		$orderScript = $proposalShopCount."개의 꽃집에서 총 ".$proposalCount."건의 제안이 도착했어요.";
	}
	else{
		$orderScript = "플로리스트들이 주문서를 확인중입니다.";
	}
?>
<div id="content">
	<div class="h_area2">
		<h2><?=$receiveDateTimeStr?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<!-- 주문결제 -->
	<input type="hidden" name="msg_type" value="1" />
	<input type="hidden" name="addorder_msg" value="" />
	

	<div class="proposals_wrap">
		
		<input type="hidden" name='quantity' value="<?=$prodNum?>">
		<input type="hidden" name='productcode' value="">
		<input type="hidden" name="ordertype" value="" />
		<input type="hidden" name="aoidx" value="<?=$aoidx?>" />
		<input type="hidden" name="receiveDate" value="<?=$receiveDate?>" />
		<input type="hidden" name="receiveTime" value="<?=$receiveTime?>" />
		<input type="hidden" name="aopidx" value="" />
		<input type="hidden" name="receiveType" value="" />
		
		<div style="width:100%;text-align:center;margin-top:10px;overflow:hidden;"> 
		<?
			$sql="SELECT 
					aop.vender
					,vs.brand_name
					,vi.com_addr
					,vi.badge
					,MAX(aop.deli_price) AS max_deli_price
					,MAX(aop.sellprice) AS max_sellprice
					,MIN(aop.sellprice) AS min_sellprice
					,vi.banner_file
					,(SELECT cont FROM vender_multicontents AS vm WHERE vm.vender = aop.vender LIMIT 1) AS banner_milti_file
					,ROUND(
						0
					) AS distance 
					, IFNULL(
						(SELECT 
							ROUND(AVG(marks),1)
						FROM 
							tblproductreview AS tpr
						LEFT JOIN
							tblproduct AS tp
						ON
							tpr.productcode = tp.productcode
						WHERE
							tp.vender = vi.vender
						GROUP BY tp.vender)
						,0) AS avg_marks
					, IFNULL(
						(SELECT 
							count(tp.vender)
						FROM 
							tblproductreview AS tpr
						LEFT JOIN
							tblproduct AS tp
						ON
							tpr.productcode = tp.productcode
						WHERE
							tp.vender = vi.vender
						GROUP BY tp.vender)
						,0) AS marks_count 
					,count(if(aop.deli_type=0, aop.deli_type, null)) AS deli_type_01
					,count(if(aop.deli_type=1, aop.deli_type, null)) AS deli_type_02
					,count(if(aop.deli_type=2, aop.deli_type, null)) AS deli_type_03
				FROM
					auction_order_proposal AS aop
				LEFT JOIN
					tblvenderinfo AS vi
				ON
					aop.vender = vi.vender
				LEFT JOIN
					tblvenderstore AS vs
				ON
					aop.vender = vs.vender
				WHERE
					userid = '".$_ShopInfo->getMemid()."'
					AND aoidx = '".$aoidx."'
				GROUP BY
					aop.vender
				ORDER BY aop.aopidx DESC ";
			$result1=mysql_query($sql,get_db_conn());
			$numRows = mysql_num_rows($result1);
			if($numRows > 0){
		?>
				<div class="search_group">
					<h2 style="float:left;">
						<?=$orderScript?>
					</h2>
					<div style="float:right;line-height: 3em;">
						<select name="sortType" class="basic_input purpose">
						<?
							$sql = "SELECT * FROM item_mst WHERE keyText='sortType' ";
							//echo $sql;
							$result2=mysql_query($sql,get_db_conn());
							while($row1=mysql_fetch_object($result2)) {
						?>
								<option value="<?=$row1->seq?>"><?=$row1->valText?></option>
						<?	}
							mysql_free_result($result2);?>
						</select>
					</div>
				</div>
		<?
			}
			else{
		?>
				<h2>
					<?=$orderScript?>
				</h2>
		<?
			}
		?>
		</div>
		<?
			while($row=mysql_fetch_object($result1)) {
				$brand_name = $row->brand_name;
				$com_addr = $row->com_addr;
				$com_addr_arr = explode(" ",$com_addr);
				if(count($com_addr_arr) >= 3){
					$com_addr = $com_addr_arr[1]." ".$com_addr_arr[2];
				}
				$deli_price = number_format($row->max_deli_price);
				$vender = $row->vender;
				$badge1 = substr($row->badge,0,1);
				$badge2 = substr($row->badge,1,1);
				$badge3 = substr($row->badge,2,1);
				$badge4 = substr($row->badge,3,1);
				$badge5 = substr($row->badge,4,1);
				$badge6 = substr($row->badge,5,1);
				$badge7 = substr($row->badge,6,1);
				$distance = $row->distance;
				$avg_marks = $row->avg_marks;
				$marks_count = number_format($row->marks_count);
				$deli_type_01 = $row->deli_type_01;
				$deli_type_02 = $row->deli_type_02;
				$deli_type_03 = $row->deli_type_03;
				
				if($row->min_sellprice == $row->max_sellprice){
					$priceText = number_format($row->min_sellprice)."원";
				}
				else{
					$priceText = number_format($row->min_sellprice)."원"."<br>~ ".number_format($row->max_sellprice)."원";
				}
				
				if($row->max_deli_price == 0){
					$deliPriceText = "배송비 무료";
				}
				else{
					$deliPriceText = "배송비 +".number_format($row->max_deli_price)."원";
				}
				
		?>
				<div class="proposals_group" id="proposalsGroup<?=$vender?>">
					<div class="venderInfoGroup">
						<div class="infoRow">
							<div class="bellIcon" onclick="showProducts('proposalsGroup<?=$vender?>')">
								<img src="/app/skin/basic/svg/question_main01.svg">
							</div>
							<div class="dateText" onclick="showProducts('proposalsGroup<?=$vender?>')"><?=$brand_name?></div>
							<div class="btnWrap">
								<div class="shopBtn" value="꽃집 정보" onclick="iframePopupOpen('/app/venderinfo.php?vidx=<?=$vender?>&pagetype=pop')">
								</div>
							</div>
						</div>
						<div class="addrRow" onclick="showProducts('proposalsGroup<?=$vender?>')">
							<div class="addr"><?=$com_addr?></div>
							<div class="starIcon"><img src="/app/skin/basic/svg/review_star_on.svg" alt=""></div>
							<div class="reviewAverage"><?=$avg_marks?> (<?=$marks_count?>)</div>
						</div>
						<div class="priceRow" onclick="showProducts('proposalsGroup<?=$vender?>')">
							<div class="pick"><?=$deliPriceText?></div>
							<?
								if($deli_type_01 > 0 || ($deli_type_02 > 0 && $deli_type_03 > 0 ) ){?>
									<div class="pick">둘 다 가능</div>
							<?	}
								else{
									if($deli_type_02>0){?>
										<div class="pick">배송만 가능</div>
								<?	}
									if($deli_type_03>0){?>
										<div class="pick">픽업만 가능</div>
								<?	}
								}?>
							<div class="dateText">
									<?=$priceText?>
							</div>
						</div>
					</div>
					<div class="products">
					<?
					$sql="SELECT
							aop.aopidx
							,aop.aoidx
							,aop.userid
							,aop.pridx
							,aop.vender
							,aop.prodNum
							,aop.sellprice
							,aop.deli_price
							,pr.productname
							,pr.productcode
							,pr.minimage
							,pr.tinyimage
							,pr.maximage
							,pr.prmsg
							,pr.product_comment
							,aop.deli_type
							,aop.createDate
							,aop.creater
							,aop.updateDate
							,aop.updater
						FROM 
							auction_order_proposal AS aop
						LEFT JOIN
							tblproduct AS pr
						ON
							aop.pridx = pr.pridx
						WHERE
							aop.vender = '".$vender."'
							AND aoidx = '".$aoidx."'
							AND del_flag = false
						ORDER BY aop.aopidx DESC ";
					$result2=mysql_query($sql,get_db_conn());
					while($prodRow=mysql_fetch_object($result2)) {
					?>
						<input type="hidden" name="itemcode[]" id="itemcode_<?=$prodRow->productcode?>" value="<?=$prodRow->productcode?>">
						<div class="pr_type_list_table">
							<div id="img<?=$prodRow->aopidx?>" class="swiper-primage typelist_image_wrap">
								<div class="swiper-wrapper">
									<?
									$imageSQL="SELECT cont FROM product_multicontents WHERE pridx='".$prodRow->pridx."' ORDER BY midx";
									$imageRes = mysql_query($imageSQL,get_db_conn());
									
									while($imageRow = mysql_fetch_object($imageRes)){
										$background_url = "/data/shopimages/product/".$imageRow->cont;
										echo "<div class='swiper-slide' style=\"width:100%;height:auto;min-height:100px;border:none;padding:0px;background:url('".$background_url."') no-repeat;background-position:center;background-size:cover;\" onclick=\"ImageShow('".((strlen($imageRow->cont)>0) ? "/data/shopimages/product/".$imageRow->cont : "/images/no_img.gif")."')\"></div>\n";

									}
									?>
								</div>
								<div class="swiperPageWrap">
									<div class="swiper-page-num"></div>
								</div>
							</div>
							<div class="typelist_text_wrap">
								<div class="typelist_text">
									<?=$prodRow->product_comment?>
								</div>
								<div class="pr_txt">
									<div style="overflow:hidden">
									<!--
										<div class="p_productname"><?php echo viewproductname($prodRow->productname,$prodRow->etctype,$prodRow->selfcode)?><?php echo (strlen($prodRow->prmsg) ? "<p class=\"prmsg\">".$prodRow->prmsg."</p>" : "")?></div>
									-->
										<div class="p_sellprice" style="margin-top:10px;">
											<?php echo number_format($prodRow->sellprice)?>원
										</div>
										<div class="p_sellprice" style="font-size:0.9rem; color:#979ba1;">배송비 + <?php echo number_format($prodRow->deli_price)?>원</div>
									</div>
									<div>
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tbody id="optlist_<?=$prodRow->productcode?>">
												
											</tbody>
										</table>
										
									</div>
									<div class="button_group">
										<ul>
										<!--
											<li id="cartChk" onclick="BasketAction('','<?=$prodRow->productcode?>','<?=$prodRow->aopidx?>')">장바구니</li>
										-->
											<?
											if($prodRow->deli_type == 0 || $prodRow->deli_type == 1 ){
											?>
												<li id="baroChk" onclick="BasketAction('ordernow','<?=$prodRow->productcode?>','<?=$prodRow->aopidx?>','0')">배송하기</li>
											<?
											}
											if($prodRow->deli_type == 0 || $prodRow->deli_type == 2 ){
											?>
												<li id="baroChk" onclick="BasketAction('ordernow','<?=$prodRow->productcode?>','<?=$prodRow->aopidx?>','1')">픽업하기</li>
											<?
											}
											?>
										</ul>
									</div>
								</div>
									<?//=$datareservation?>
							</div>
						</div>
					<?	
						}
					?>
				</div>
			</div>
		<?	
		}
		?>
		
	</div>
	<!-- 주문정보 END -->

	<Script>
		$(document).ready(function() {
			$(".order_select_table tr").click(function(){
				var selTrObj = $(this);
				$(selTrObj).parent().find("td").each(function(obj) {
					$(this).removeClass("select")
				})
				$(selTrObj).find("td").each(function(obj) {
					$(this).addClass("select")
				})
				$(selTrObj).find("input:radio").prop("checked", true);
			});
			

		});
		$(function(){
			$('select[name=giftval_seq]').change( function(){ resetGiftOptions();});
		});

	
	</script>

	<script>
		//주소록 팝업창
		function ReceiverCheck(){
			window.open("mydelivery.php","addrbygone","width=100,height=100,toolbar=no,menubar=no,scrollbars=yes,status=no");
		}
	</script>
	
	<script type="text/javascript">

	//생년월일 달력 처리
	$(function() {
		$("#receiveDate").datepicker({
			dateFormat: 'yy-mm-dd',
			prevText: '이전 달',
			nextText: '다음 달',
			monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNames: ['일','월','화','수','목','금','토'],
			dayNamesShort: ['일','월','화','수','목','금','토'],
			dayNamesMin: ['일','월','화','수','목','금','토'],
			showMonthAfterYear: true,
			changeMonth: true,
			changeYear: true,
			minDate: '+0d',
			yearRange: "-100:+0"	
		});
	});
	
	function BasketAction(gbn,productcode,aopidx,receiveType){
		var form = document.form1;
		if($("input[name='quantity'").val()){
			form.productcode.value = productcode;
			new Date(form.receiveDate.value);
			new Date(form.receiveTime.value );
			form.quantity.value = $("input[name='quantity'").val();
			$("input[name='receiveType'").val(receiveType);
			form.ordertype.value = gbn;
			form.aopidx.value = aopidx;
			
			CheckForm(gbn,'');
//			real_form_submit(form);
			//초기화
			form.productcode.value = "";
			form.quantity.value = "";
			form.ordertype.value = "";
		}else{
			alert("수량을 입력하여 주세요.");
		}
	}
	function real_form_submit(form, type) {
		var linkpage="";
		linkpage = "basket_auction.php";
		$.ajax({
			type : "POST",
			url: linkpage,
			data : $(form).serialize(),
			dataType : "json",
			success : function (rs) {
				console.log(rs);
				if (rs.result == "err") {
					alert(rs.msg);
				} else if (rs.result == "ok_basket") {
					$(".basketBox > h4 > span").text("장바구니");
					$(".basketMessage > h5 > span").text("장바구니");
					$(".basketBtnBox .goBasketBtn").attr("href", "basket.php");
					$("#insertBasket").show();
				} else if (rs.result == "ok_quick") {
					location.href='basket.php?ordertype=orderquick';
				} else {
					alert("오류입니다. 다시 시도해주십시오.");
				}
			}
		});
	}
	
	function CheckForm(gbn,temp2) {


		var optMust = true;

		if(gbn!="wishlist") {
			if(document.form1.quantity.value.length==0 || document.form1.quantity.value==0) {
				alert("주문수량을 입력하세요.");
				document.form1.quantity.focus();
				return;
			}
		}
		if(gbn=="ordernow") {
			document.form1.ordertype.value="ordernow";
		}
		else if(gbn=="ordernow2" || gbn=="ordernow3") {
			document.form1.ordertype.value=gbn;
			document.form1.action = "<?=$Dir.FrontDir?>basket2.php";
		}
		else if(gbn=="ordernow4" || gbn=="present" || gbn=="pester") {
			document.form1.ordertype.value=gbn;
			document.form1.action = "<?=$Dir.FrontDir?>basket3.php";
		} else {
			// 1606022 바로구매 클릭 시 옵션 미선택 경고 뜨고 장바구니로 담았을 때 바로구매로 가는 오류 수정.
			document.form1.ordertype.value="";
		}


		if(temp2!="") {
			document.form1.opts.value="";
			try {
				for(i=0;i<temp2;i++) {
					if(document.form1.optselect[i].value==1 && document.form1.mulopt[i].selectedIndex==0) {
						alert('필수선택 항목입니다. 옵션을 반드시 선택하세요');
						document.form1.mulopt[i].focus();
						return;
					}
					document.form1.opts.value+=document.form1.mulopt[i].selectedIndex+",";
				}
			} catch (e) {}
		}

		if(gbn!="wishlist") {
			document.form1.action = "basket.php";
			document.form1.submit();
		} else {
			document.form1.action = "confirm_wishlist.php";
			document.form1.submit();
			//document.wishform.opts.value=document.form1.opts.value;
			//if(typeof(document.form1.option1)!="undefined") document.wishform.option1.value=document.form1.option1.value;
			//if(typeof(document.form1.option2)!="undefined") document.wishform.option2.value=document.form1.option2.value;

			//window.open("about:blank","confirmwishlist","width=500,height=250,scrollbars=no");
			//document.wishform.submit();
		}
	}
	var swiperMap = new Array();
	var settings = {
						loop: true,
						pagination: {
							el: '.swiper-page-num',
							type: 'fraction'
						}
					}
	function showProducts(objId){
		if(!$("#"+objId).find(".products").is(':visible')){
			$("#"+objId).find(".products").slideDown(200,function(){
				var productObj = $("#"+objId).find(".pr_type_list_table");
				for(var i = 0 ; i < productObj.length ; i++){
					var imageObj = productObj.eq(i).find(".swiper-slide");
					if(imageObj.length > 1){
						var swiperObj = productObj.eq(i).find(".typelist_image_wrap");
						var swiperId = swiperObj.attr("id");
						if(swiperMap[swiperId] == null){
							swiperMap[swiperId] = new Swiper('#' + swiperId, settings);
						}
					}
					else{
						productObj.eq(i).find(".swiperPageWrap").hide();
					}
				}
			});
		}
		else{
			$("#"+objId).find(".products").slideUp(200);
		}
	}
	$(document).ready(function(){
		showProposals($(".proposals_group").first());
	});
	
	function showProposals(obj){
		var nextObj = $(obj).next();
		if (typeof nextObj != "undefined"){
			$(obj).fadeIn(400,function(){showProposals(nextObj)});
		}
	}
	
	function getLocation() {
	  if (navigator.geolocation) { // GPS를 지원하면
		navigator.geolocation.getCurrentPosition(function(position) {
		  alert(position.coords.latitude + ' ' + position.coords.longitude);
		}, function(error) {
		  console.error(error);
		}, {
		  enableHighAccuracy: false,
		  maximumAge: 0,
		  timeout: Infinity
		});
	  } else {
		alert('GPS를 지원하지 않습니다');
	  }
	}
	

//	getLocation();
	//-->
	
	</script>
	
</div>

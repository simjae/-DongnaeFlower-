<div id="list">
	
	<script src="js/swiper.min.js"></script>
	<script language="javascript">
		<!--
		var swiper = new Swiper('.mypage_menu', {
			slidesPerView: 'auto',
			spaceBetween: 10,
			freeMode: true
		});
		
		var settings = {
			loop: true,
			pagination: {
				el: '.swiper-page-num',
				type: 'fraction'
			}
		}
		
		function callOrderNowEvent(paramData) {
			$.ajax({
				url: "/api/order_now.php",
				type: "get",
				data: paramData,
				dataType: "json",
				success : function(data) {
								if (data.length > 0) {
									orderNowSplitEvent(data);	
								} else {
									$('.product_list').remove();
									var strDiv = "<div class='product_list' style='margin-top:10px;color:#e61e6e;font-size:1.1rem;'>*조회 결과가 없습니다. 필터를 다시 적용 해 주세요.</div>";
									$('.product_a').append(strDiv);
									$('.product_list_loader').hide();
								}
							}
			});
		}
		
		function orderNowSplitEvent(data){
			for(var i = 0; i < data.length; i++){
				productAppendEvent(data[i],i);
			}
			var timeSaleImageSwiper = new Swiper('.productImage', settings);
			setProductViewClickEvent();
			$('.product_list_loader').hide();
		}
		
		function setProductViewClickEvent() {
			$(".product_view").click(function(){
				var quantity = $(this).attr("quantity");
				if(quantity == "" || quantity > 0){
					$(".shopInfo").fadeOut(200);
					$(this).find(".shopInfo").fadeIn(200);
				}
			});
		}
		
		function productAppendEvent(data,num){
			var prdetail_link="";
			var urlVender = "&vidx=";
			if (data.vender.length > 0) {
				urlVender += data.vender;
			}
			
			var strDiv="";
			var category = data.category;
			if(category== "TS"){
				prdetail_link = 'location.href="productdetail_timesale.php?productcode=' + data.productcode + urlVender + '&refURL=timesale_product' + '"' ;
			}else{
				prdetail_link = 'location.href="productdetail_today_flower.php?productcode=' + data.productcode + urlVender + '&refURL=timesale_product' + '"' ;
			}
			var  backgroundUrl = "/data/shopimages/product/";
			var imgArray = new Array();
			if (data.product_img.length > 0) {
				imgArray = data.product_img.split(",");	
			}
			strDiv +=	"<ul class='product_list' style='width:45%;float: left;margin-left: 13px;margin-top: 10px;'>";
			strDiv +=		"<li class='product_item remainTimeBox'setstamp='dcsm_"+num+"' endstamp='" +data.end+"'>";
			strDiv +=			"<div class='product_view' quantity=" + data.quantity + " style='border-radius: 10px;isolation: isolate;'>";
			strDiv +=				"<div class='product_img'>";
			strDiv +=					"<div class='productImage' style='height:100%;''>";
			strDiv +=						"<div class='swiper-wrapper'>";
			for(var i = 0; i < imgArray.length; i++){
			strDiv +=							"<div class='swiper-slide' style='width:100%;height: 200px;min-height:100px;border:none;padding:0px;background:url(" + imgArray[i]+") no-repeat;background-position:center;background-size:cover;'></div>";
			}
			strDiv +=						"</div>";
			strDiv +=						"<div class='swiperPageWrap' style='bottom:10px;'>";
			strDiv +=							"<div class='swiper-page-num'></div>";
			strDiv +=						"</div>";
			strDiv +=					"</div>";
			switch(category){
				case 'TS' : 
					if(data.discount>0){
						strDiv +=	"<div class='product_sale' style='background-color:#ffdc6e;padding: 2px 5px;color:#282828;border: 1px solid #282828;'>-"+ data.discount +"%</div>";
					} 
				break;
				case 'FP':
					strDiv +=		"<div class='product_category'>" + data.productTypeText + "</div>";
				break;
				case 'TF':
					strDiv +=		"<div class='product_productTypeText'>"+data.productTypeText+"</div>";
				break;				
			}
			strDiv +=				"</div>";
			strDiv +=				"<div class='shopInfo' onclick='" + prdetail_link + "'>";
			strDiv +=					"<div class='shop_title'>" + data.productname +"</div> ";
			strDiv +=					"<div class='shop_content'>";
			strDiv +=						"<img src='/app/skin/basic/svg/review_star_on.svg' style='height:16px;' alt='star'>";
			strDiv +=						"<span class='count1'>" + data.avg_marks + "</span>";
			strDiv +=						"<span class='count2'>(" + data.marks_count + ")</span>";
			strDiv +=					"</div>";
			strDiv +=					"<div class='btnWrap'>";
			strDiv +=						"<div class='distBtn black' value='나와의 거리 " + data.distance + "Km'>";
			strDiv +=						"</div>";
			strDiv +=						"<div class='distBtn white' value='상세 정보 보기'>";
			strDiv +=						"</div>";
			strDiv +=					"</div>";
			strDiv +=				"</div>";
			if(data.quantity != "" && data.quantity < 1){
			strDiv +=				"<div class='soldout'>"
			strDiv +=					"<div class='textWrap'>판매완료</div>"
			strDiv +=				"</div>"
			}
			strDiv +=			"</div>";
			strDiv +=			"<div class='product_info'>";
			strDiv +=				"<div class='prodContentWrap'>";
			strDiv +=					"<div class='prodName' style ='font-size:1.0em;color: #8c8c8c;'>"+ data.com_name +"</div>";
			strDiv +=					"<div class='prodName' style ='font-size:1.0em;color: #282828;'>"+ data.productname +"</div>";
			strDiv +=				"</div>";
			var margin_bottom = "";
			if (category != "TS") {
				margin_bottom = "margin-bottom:34px;";
			}
			strDiv +=				"<div class='priceText' style='margin-top: 0px;padding:0px 7px;" + margin_bottom + "'>";
			strDiv +=					"<span style='font-size:1.0em;font-weight: 800;margin-right: 10px;color:#282828'>"+data.sellprice+"원";
			if (data.sellprice != data.consumerprice) {
			strDiv += 					"</span><span style='font-size:1.0em;color: #03031d;font-weight: 300;color:#464646;text-decoration: line-through;'>"+data.consumerprice+"원</span>"
			}
			strDiv +=				"</div>";
			var margin = "";
			if (category == "TS") {
			strDiv +=				"<div class='productTimeWrap'>";
			strDiv +=					"<div class='prodContentWrap'>";
			strDiv +=						"<div class='timeimg' id='dcsm_"+num+"' style='padding:2px 0px 20px 17px;background-position: 0px 4px;'></div>";
			strDiv +=					"</div>";
			strDiv +=				"</div>";
			}
			strDiv +=			"</div>";
			strDiv +=		"</li>";
			strDiv +=	"</ul>";
			strDiv +="</div>";
			$('.product_a').unbind();
			$('.product_a').append(strDiv);
			intCountdown();
		}
		
		function intCountdown(){
			$('li.remainTimeBox').each(function(idx,el){
				dday = $(el).attr('endstamp');
				dday = dday.replace(/-/gi,"/");
				sid = $(el).attr('setstamp');
				sid = sid.replace(/-/gi,"/");
				reverse_counter(sid, dday);

			});
			setTimeout("intCountdown()", 1000);
		}
		//-->
	</script>
	<div class="wrapper">
		<?
		
		$search_sql = '';

		$where = array();
		
		array_push($where,"start <= '".date('Y-m-d H:i')."'");

		if($_REQUEST['ordby'] == 'end'){
			array_push($where,"end < '".date('Y-m-d H:i')."'");
		}else{
			array_push($where,"end >= '".date('Y-m-d H:i')."'");	
		}

		$where = _array($where)?' where '.implode(' and ',$where):'';
		
		$dfVal = $_REQUEST["dfVal"];	//거리 필터값
		$ptfVal = $_REQUEST["ptfVal"];	//프로덕트  타입 필터값
		$pfVal = $_REQUEST["pfVal"];	//가격 필터값
		
		if (strlen($dfVal) > 0 && $dfVal != "N") {
			$where .= " distance <= ".$dfVal." AND ";
		}
		
		if (strlen($ptfVal) > 0 && $ptfVal != "N") {
			$where .= " tpr.productType=".$prfVal." AND ";
		}
		
		if (strlen($pfVal) > 0 && $pfVal != "N") {
			$price = 0;
			switch ($pfVal) {
				case "51" :
					$where .= " tpr.sellprice <=30000";
					break;
				case "52" :
					$where .= " (tpr.sellprice BETWEEN 30000 AND 50000)";
					break;
				case "53" :
					$where .= " (tpr.sellprice BETWEEN 50000 AND 70000)";
					break;
				case "54" :
					$where .= " (tpr.sellprice BETWEEN 70000 AND 100000)";
					break;
				case "55" :
					$where .= " (tpr.sellprice BETWEEN 100000 AND 150000)";
					break;
				case "56" :
					$where .= " (tpr.sellprice BETWEEN 150000 AND 200000)";
					break;
				case "57" :
					$where .= " (tpr.sellprice BETWEEN 200000 AND 300000)";
					break;
				case "58" :
					$where .= " tpr.sellprice >= 300000";
					break;
			}
		}
		
		$sql = "select count(pridx) as t_count from tblproduct a inner join todaysale t using(pridx) LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ".$where;
		$sql.= $qry." ";

		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$sql.= $search_sql." "; //search by alice
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$rowcount = (int)$row->t_count;
		mysql_free_result($result);

		$tmp_sort=explode("_",$sort);
		$sql = "select 
					a.*
					,TVS.brand_name
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
							tp.vender = a.vender
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
							tp.vender = a.vender
						GROUP BY tp.vender)
						,0) AS marks_count
					,ROUND(
						(5671 
							* acos(
								cos(radians(".$pointx.")) 
								* cos(radians(CAST(VI.com_addr_pointx AS DECIMAL(18,15)))) 
								* cos(radians(CAST(VI.com_addr_pointy AS DECIMAL(18,15))) - radians(".$pointy."))
								+ sin(radians(".$pointx.")) 
								* sin(radians(CAST(VI.com_addr_pointx AS DECIMAL(18,15))))
							)
						),1
					) AS distance
					,t.start
					,t.end
					,t.addquantity
					,t.salecnt
					,unix_timestamp(end) -unix_timestamp() as remain
					, a.sellcount+addquantity as sellcnt
				from 
					tblproduct a 
				inner join 
					todaysale t using(pridx) 
				LEFT OUTER JOIN 
					tblproductgroupcode b 
				ON 
					a.productcode=b.productcode 
				LEFT JOIN 
					tblvenderinfo VI 
				ON 
					a.vender=VI.vender 
				LEFT JOIN 
					tblvenderstore TVS
				ON 
					a.vender=TVS.vender   "
				.$where.$ordby.$limit;		//2015-04-16 salecnt->a.sellcount

		$sql.= $search_sql." "; //search by alice
		$sql.= $qry." ";

		echo $ordby;

		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else {
			if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
				if(eregi("T",$_cdata->type) && strlen($t_prcode)>0) {
					$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),a.date DESC ";
				} else {
					$sql.= "ORDER BY a.date DESC ";
				}
			} else if($_cdata->sort=="productname") {
				$sql.= "ORDER BY a.productname ";
			} else if($_cdata->sort=="production") {
				$sql.= "ORDER BY a.production ";
			} else if($_cdata->sort=="price") {
				$sql.= "ORDER BY a.sellprice ";
			}
		}

		$pagePerBlock = 5; // 블록 갯수

		?>
		<div class="product_a">
			<div class="product_list_loader" style="display:none;-webkit-box-shadow:none;border:none;height:150px;padding-top:50px;">
				<img src="/app/images/order_now_loading.gif" style="width:40px;height:50px;padding-left:45%;padding-right:45%;">
				<div style="margin-top:10px;font-size:1.0rem;color:rgba(0,0,0,.87);text-align:center;">꽃을 불러오는 중입니다</div>
				<div style="margin-top:5px;font-size:1.0rem;color:rgba(0,0,0,.87);text-align:center;">조금만 기다려 주세요 :)</div>
			</div>
			<!--<ul class="product_list">
			<?
				$itemcount = 12; // 페이지당 게시글 리스트 수 
				$sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;

				if(false !== $gelleryRes = mysql_query($sql,get_db_conn())){
					$gelleryNumRows = mysql_num_rows($gelleryRes);

					$i=0;
					if($gelleryNumRows > 0){
						while($gelleryRow=mysql_fetch_assoc($gelleryRes)){
							$wholeSaleIcon="";
							if($gelleryRow['isdiscountprice'] == 1 AND isSeller()){
								$wholeSaleIcon='<img src="/images/common/wholeSaleIcon.gif" /> ';
								$gelleryRow['sellprice']=$gelleryRow['productdisprice'];
							}
							$memberprice = 0;
							$brand_name = $gelleryRow['brand_name'];
							$avg_marks = $gelleryRow['avg_marks'];
							$marks_count = $gelleryRow['marks_count'];
							$reservation=$gelleryRow['reservation'];
							$productname=$gelleryRow['productname'];
							$productcode = $gelleryRow['productcode'];
							$productmsg=$gelleryRow['prmsg'];
							$prconsumerprice=number_format($gelleryRow['consumerprice']);
							$sellprice=number_format($gelleryRow['sellprice']);
							$discountRate = 100 - round($gelleryRow['sellprice'] / $gelleryRow['consumerprice'] ,2) * 100;
							$option1 = $gelleryRow['option1'];
							$option2 = $gelleryRow['option2'];
							$optionquantity = $gelleryRow['option_quantity'];
							$quantity = $gelleryRow['quantity'];
							$vendername = $gelleryRow['com_name'];
							$venderidx=$gelleryRow['vender'];
							$distance=$gelleryRow['distance'];

							if(strlen($reservation)>0 && $reservation != "0000-00-00"){
								$msgreservation="<span class=\"font-orange\">(예약)</span>";
								$datareservation="(".$reservation.")";
							}else{
								$msgreservation=$datareservation="";
							}

							#####################상품별 회원할인율 적용 시작#######################################
							$discountprices = getProductDiscount($productcode);
							if($discountprices > 0 AND isSeller() != 'Y' ){
								$memberprice = $gelleryRow['sellprice'] - $discountprices;
								$gelleryRow['sellprice'] = $memberprice;
							}
							#####################상품별 회원할인율 적용 끝 #######################################


							if(strlen($gelleryRow[tinyimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$gelleryRow[tinyimage])==true){
								$background_url=$Dir.DataDir."shopimages/product/".urlencode($gelleryRow[tinyimage]);
							}else{
								$background_url=$Dir."images/no_img.gif";
							}

							$prdetail_link="productdetail_timesale.php?productcode=".$productcode.($vidx?"&vidx=".$vidx:"");


							$youtube_url=$gelleryRow['youtube_url'];
							$youtube_prlist=$gelleryRow['youtube_prlist'];
							$youtube_prlist_imgtype=$gelleryRow['youtube_prlist_imgtype'];
							$youtube_prlist_file=$gelleryRow['youtube_prlist_file'];

							//동영상(유튜브) 등록일 때 상품이미지 교체
							if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='Y'){
								$youtube_code=str_replace("https://youtu.be/","",$youtube_url);
								$prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
								$background_image=str_replace("https://youtu.be/","",$youtube_url);
								$background_url="https://img.youtube.com/vi/".$background_image."/sddefault.jpg";

							}else if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='D'){
								$youtube_code=str_replace("https://youtu.be/","",$youtube_url);
								$prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
								$background_image=$youtube_prlist_file;
								$background_url=$Dir.DataDir."shopimages/product/".$background_image;
							}

							$width=getimagesize($background_url);
							if($width[1]>$width[0]){ //세로가 가로보다 길 때
								$background_size="auto 100%";
							}else{ //가로가 세로보다 길 때
								$background_size="100% auto";
							}

							$prradius="border-radius:15px;overflow:hidden;";
							//시간변환		
							$enddateN = date('D M d Y H:i:s O', strtotime($gelleryRow['end'])); 
						?>
							<li class="product_item remainTimeBox" setstamp="dcsm_<?=$i?>" endstamp='<?=$enddateN?>'>
								<div class="product_view" style="border-radius: 10px;" quantity="<?=$quantity?>">
									<div class="product_img" style="<?=$prradius?>">
										<div class="timeSaleImage" style="height:100%;">
											<div class="swiper-wrapper">
												<?
												$imageSQL="SELECT cont FROM product_multicontents WHERE pridx='".$gelleryRow[pridx]."' ORDER BY midx";
												$imageRes = mysql_query($imageSQL,get_db_conn());
												while($imageRow = mysql_fetch_object($imageRes)){
													$background_url = "/data/shopimages/product/".$imageRow->cont;
													echo "<div class='swiper-slide' style=\"width:100%;height:auto;min-height:100px;border:none;padding:0px;background:url('".$background_url."') no-repeat;background-position:center;background-size:cover;\")\"></div>\n";

												}
												?>
											</div>
											<div class="swiperPageWrap">
												<div class="swiper-page-num"></div>
											</div>
										</div>
										<? if($discountRate>0){ ?><div class="product_sale">-<?=$discountRate?>%</div><? } ?>
									</div>
									<div class="shopInfo" onclick="location.href='<?=$prdetail_link?>'">
										<div class="shop_title"><?=$brand_name?></div> 
										<div class="shop_content">
											<img src="/app/skin/basic/svg/review_star_on.svg" style="height:16px;" alt="star"> 
											<span class="count1"><?=$avg_marks?></span>
											<span class="count2">(<?=$marks_count?>)</span>
										</div>
										<div class="btnWrap">
											<div class="distBtn black" value="나와의 거리 <?=$distance?>Km">
											</div>
											<div class="distBtn white" value="상세 정보 보기">
											</div>
										</div>
									</div>
									<?if($quantity != "" && $quantity < 1){
									?>
										<div class="soldout">
											<div class="textWrap">
												판매완료
											</div>
										</div>
									<?}?>
								</div>
								
								<div class="product_info" <?=($productlist_quick=='Y'?"style=\"margin-bottom:65px;\"":"")?>>
									<div class="prodContentWrap">
										<div class="prodName">
											<?=$productname?>
										</div>
									</div>
									<div class="prodContentWrap">
										<div class="timeimg" id="dcsm_<?=$i?>"></div>
										<div class="priceText"><?=$sellprice?><span style="font-weight: 400;">원</span></div>
									</div>

									<p><span class="prname"><a href="productdetail_timesale.php?productcode=<?=$productcode.($vidx?"&vidx=".$vidx:"")?>" rel="external"><?=$msgreservation?></a></span></p>
									
								</div>

								
							</li>
						<?
							if($i>$gelleryNumRows-2 AND ($i+1)%2 != 0) {	//상품 전체 갯수가 홀수이면 비어있는 li 추가하기
								echo "<li class='product_item'></li>";
							}

							if($i>0 && $i%2){	//가로 2개 줄바꿈 처리
								echo "</ul><div style='height:40px;'></div><ul class='product_list'>";
							}

							$i++;
						}
					}
					mysql_free_result($gelleryRes);
				}
					?>
			</ul>-->
		</div>
	</div>
</div>

<!-- Once the page is loaded, initalize the plug-in. -->
<script type="text/javascript">


	function reverse_counter(va1, va2){
		today = new Date();
		d_day = new Date(va2);
		
		days = (d_day - today) / 1000 / 60 / 60 / 24;
		daysRound = Math.floor(days);
		hours = (d_day - today) / 1000 / 60 / 60 - (24 * daysRound);
		hoursRound = Math.floor(hours);
		minutes = (d_day - today) / 1000 /60 - (24 * 60 * daysRound) - (60 * hoursRound);
		minutesRound = Math.floor(minutes);
		seconds = (d_day - today) / 1000 - (24 * 60 * 60 * daysRound) - (60 * 60 * hoursRound) -
		(60 * minutesRound);
		secondsRound = Math.round(seconds);


			sec = "";
			min = "<span>:</span>";
			hr = "<span>:</span>";
			dy = "<span>일</span> ";

			hoursRound = hoursRound < 10 ? "0" + hoursRound : hoursRound;
			minutesRound = minutesRound < 10 ? "0" + minutesRound : minutesRound;
			secondsRound = secondsRound < 10 ? "0" + secondsRound : secondsRound;
			
			daysRound = "<span>" + parseInt(daysRound) + "</span>";
			hoursRound = "<span>" + parseInt(hoursRound) + "</span>";
			minutesRound = minutesRound == "00" ? "<span>" + parseInt(minutesRound) + "</span>" : parseInt(minutesRound);
			
			$("#"+va1).html(hoursRound + hr + minutesRound + min + secondsRound + sec);
	}

	function prStateView(val){
		//alert(document.getElementById("tbl_prStage_"+val).style.display);
		//$(".tbl_prStage").slideDown();
		//$(".btn_close_prState span").text('재고확인');
		if(document.getElementById("tbl_prStage_"+val).style.display=='none'){
			$("#btn_close_prState"+val+" span").text('× 닫기');
			$("#tbl_prStage_"+val).slideDown();
		}else{
			$("#btn_close_prState"+val+" span").text('재고확인');
			$("#tbl_prStage_"+val).slideUp();
		}
	}
</script>
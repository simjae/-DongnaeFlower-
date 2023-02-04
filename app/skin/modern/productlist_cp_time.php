<div id="list">
	
	<script src="js/swiper.min.js"></script>
	<script language="javascript">
		<!--
		var swiper = new Swiper('.swiper-container', {
			slidesPerView:2.2,
			spaceBetween:6,
			freeMode: true
		});
		//-->
	</script>

	<?
		$codeA = substr($code,0,3);
		$codeB = substr($code,3,3);
		$codeC = substr($code,6,3);
		$codeD = substr($code,9,3);

		$_cdata="";
		$sql="SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD='".$codeD."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$_cdata=$row;
	?>

	<h1 class="list_title"><?=$_cdata->code_name?></h1>

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
			$sql = "select a.*,t.start,t.end,t.addquantity,t.salecnt,unix_timestamp(end) -unix_timestamp() as remain, a.sellcount+addquantity as sellcnt from tblproduct a inner join todaysale t using(pridx) LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ".$where.$ordby.$limit;		//2015-04-16 salecnt->a.sellcount

			$sql.= $search_sql." "; //search by alice
			$sql.= $qry." ";

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

			//타입별 상품목록 출력

			$displaymode="gallery";

			switch($displaymode){
				case "gallery":
		?>
				<div class="product_a">
					<ul class="product_list">
					<?
						$itemcount = 200; // 페이지당 게시글 리스트 수 
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
									$reservation=$gelleryRow['reservation'];
									$productname=_strCut($gelleryRow['productname'],28,4,$charset);
									$productcode = $gelleryRow['productcode'];
									$productmsg=$gelleryRow['prmsg'];
									$prconsumerprice=number_format($gelleryRow['consumerprice']);
									$sellprice=number_format($gelleryRow['sellprice']);
									$discountRate=$gelleryRow['discountRate'];
									$option1 = $gelleryRow['option1'];
									$option2 = $gelleryRow['option2'];
									$optionquantity = $gelleryRow['option_quantity'];
									$vendername = $gelleryRow['com_name'];
									$venderidx=$gelleryRow['vender'];

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

									$prdetail_link="productdetail_tab01.php?productcode=".$productcode.($vidx?"&vidx=".$vidx:"");

									$sellprice = $gelleryRow['sellprice'];


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
										$background_size="100% auto";
									}else{ //가로가 세로보다 길 때
										$background_size="auto 100%";
									}

									$prradius="border-radius:5px;overflow:hidden;";
						?>
						<?
							//시간변환		
							 $enddateN = date('D M d Y H:i:s O', strtotime($gelleryRow['end'])); 
						?>
						<li class="product_item remainTimeBox" setstamp="dcsm_<?=$i?>" endstamp='<?=$enddateN?>'>
							<div class="product_view">
								<div class="product_img" style="<?=$prradius?>">
									<?
										if(strlen($youtube_url)>0 && $youtube_prlist=='Y'){ //동영상(유튜브) 이미지는 퀵툴 미출력
											echo "<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;'><A HREF=".$prdetail_link."><img src='/images/movie_icon.png' alt='' /></a></div>";
										}
									?>
									<a href=<?=$prdetail_link?> rel="external" style="display:block;width:100%;height:100%;background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>;background-position:center;font-size:0px;">
										<img src="/images/common/trans.gif" width="100%" alt="상품 이미지" class="pr_pt" />
									</a>
								</div>
								<? if($discountRate>0){ ?><div class="product_sale"><?=$discountRate?>%</div><? } ?>
							</div>
							
							<div class="product_info" <?=($productlist_quick=='Y'?"style=\"margin-bottom:65px;\"":"")?>>

								<p class="timeimg" id="dcsm_<?=$i?>" style="padding: 10px 0px 10px 20px;background:url('/m/images/icon_time.png') no-repeat;background-position:left;background-size:18px;color:#ee6b1b;"></p>

								<p><span class="prname"><a href="productdetail_tab01.php?productcode=<?=$productcode.($vidx?"&vidx=".$vidx:"")?>" rel="external"><?=$msgreservation?><?=$productname?></a></span></p>
								
								<p><? if($prconsumerprice>0){ ?><strike><?=$prconsumerprice?>원</strike><? } ?> <span class="prprice"><?=number_format($sellprice)?>원</span></p>
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
							}else{
								}
								mysql_free_result($gelleryRes);
							}else{
							}
							?>
					</ul>
				</div>
		<?
				break;
			}
		?>

		<? if($displaymode != "webzine"){ ?>
			<div class="product_page" id="page_wrap">
			<?
				$pageLink=$_SERVER['PHP_SELF']."?code=".$code."&sort=".$sort."&search_bridx=".$search_bridx."&search_price_s=".$search_price_s."&search_price_e=".$search_price_e."&search_color_idx=".$search_color_idx."&searchkey=".$searchkey."&list_type=".$displaymode."&page=%u";
				$pagePerBlock = ceil($rowcount/$itemcount);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$currentPage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
			</div>
		<? } ?>

	</div>
</div>

<!-- Once the page is loaded, initalize the plug-in. -->
<script type="text/javascript">
	//카테고리 선택 관련
	function toggle(str){
		var selectorArr = str.split("_");
		jQuery("ul[id*="+selectorArr[0]+"]").each(function() {
			if (this.id == str) {
				if (this.style.display == "none") {
					this.style.display = "";
				} else {
					this.style.display = "none";
				}
			} else {
				this.style.display = "none";
			}
		});
	}


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


		sec = " 초 ";
		min = " 분 ";
		hr = " 시간 ";
		dy = " 일 : ";

		$("#"+va1).html(daysRound + dy + hoursRound + hr + minutesRound + min + secondsRound + sec);
}



function intCountdown(){
	$('li.remainTimeBox').each(function(idx,el){
		dday = $(el).attr('endstamp');
		sid = $(el).attr('setstamp');
		reverse_counter(sid, dday);

	});
	setTimeout("intCountdown()", 1000);
}

$(function(){
	intCountdown();
});

</script>


<!-- Once the page is loaded, initalize the plug-in. -->
<script type="text/javascript">
	//카테고리 선택 관련
	function toggle(str){
		var selectorArr = str.split("_");
		jQuery("ul[id*="+selectorArr[0]+"]").each(function() {
			if (this.id == str) {
				if (this.style.display == "none") {
					this.style.display = "";
				} else {
					this.style.display = "none";
				}
			} else {
				this.style.display = "none";
			}
		});
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
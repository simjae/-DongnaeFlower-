<?
	$main_check=1;

	include_once('header_de.php'); 
	include_once($Dir."lib/mobile_eventpopup.php");
?>
<style>
	.swiper-slide{border:none;}
</style>

<!-- 내용 -->
<div id="main">
	<!-- 메인 비주얼 -->
	<div id="main_visual">
		<!-- slide-delay 속성으로 인터벌 조정 및 오토플레이 유무 설정가능 -->
		<div class="swiper-container" slide-delay="5000">
			<div class="swiper-wrapper">
				<?
					$bannerSQL="SELECT * FROM tblmainbanner WHERE position='T' and device = 'M' ORDER BY date DESC";

				//	$bannerSQL="SELECT image,url,target FROM tblmobilebanner WHERE position='visiul' ORDER BY date DESC LIMIT 5";

					$rowcount=0;
					if(false !== $bannerRes = mysql_query($bannerSQL,get_db_conn())){
						$rowcount = mysql_num_rows($bannerRes);
						if($rowcount>0){
							while($bannerRow = mysql_fetch_assoc($bannerRes)){
				?>
							<div class="swiper-slide" style="font-size:0px;line-height:0%;">
							<?
								if($bannerRow['type']=="M"){
									echo "
										<div style=\"position:relative;\">
											<a href='http://www.youtube.com/watch?v=".$bannerRow['movie_url']."' class='modal_movie''>
												<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;background:url(/images/movie_icon.png') no-repeat;background-size:auto;></div>
												<img src='https://img.youtube.com/vi/".$bannerRow['movie_url']."/sddefault.jpg' alt= ''/>
											</a>
										</div>
									";
								}else{
									//링크주소 정보가 있을 경우
									if(strlen($bannerRow['link_url'])>0){
										echo '<a href="http://'.$bannerRow['link_url'].'" target="'.$bannerRow['target'].'">';
									}

									echo $bannerRow[bannerText_modal]=='Y'?"<div style='position:absolute;top:0px;left:0px;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1;'><!--modal bg--></div>":"<div style='position:absolute;top:0px;left:0px;width:100%;height:100%;background:rgba(0,0,0,0);z-index:1;'><!--modal bg--></div>";

									if(strlen($bannerRow[title])>0 || strlen($bannerRow[contents])>0){
							?>
									<div style="position:absolute;top:0px;left:0px;width:100%;height:100%;padding:7%;box-sizing:border-box;z-index:2;">
										<div style="display:table;width:100%;height:100%;">
											<div style="z-index:2;display:table-cell;text-align:<?=$bannerRow[contents_position_x]?>;vertical-align:<?=$bannerRow[contents_position_y]?>;">
												<? if(strlen($bannerRow[title])>0){ ?>
													<h2 style="margin:10px 0px;color:<?=$bannerRow[title_color]?>;font-size:<?=$bannerRow[title_size]?>;font-weight:<?=$bannerRow[title_weight]=='B'?"bold":""?>;font-family:<?=$bannerRow[title_fonts]?>;letter-spacing:-1px;line-height:120%;<?=($bannerRow[bannerText_shadow]=='Y'?"text-shadow:0px 0px 15px rgba(0,0,0,0.65);":"")?>"><?=$bannerRow[title]?></h2>
												<? } ?>
												<? if(strlen($bannerRow[contents])>0){ ?>
													<span style="display:inline-block;width:75%;color:<?=$bannerRow[contents_color]?>;font-size:<?=$bannerRow[contents_size]?>;font-weight:<?=$bannerRow[contents_weight]=='B'?"bold":""?>;font-family:<?=$bannerRow[contents_fonts]?>;letter-spacing:-1px;line-height:120%;<?=($bannerRow[bannerText_shadow]=='Y'?"text-shadow:0px 0px 15px rgba(0,0,0,0.65);":"")?>"><?=$bannerRow[contents]?></span>
												<? } ?>
											</div>
										</div>
									</div>
							<?
									}
									echo '<img src="'.$configPATH.$bannerRow['files'].'" border="0" alt="" width="100%" />';
									//링크주소 정보가 있을 경우
									if(strlen($bannerRow['link_url'])>0){
										echo "</a>";
									}
								}
							?>
							</div>
				<?
							}
						}else{
				?>
				<div><img src="<?=$configPATH?>@main_banner.png" alt="배너를 등록하세요~" /></div>
				<?
						}
					}
				?>
			</div>
			<div class="swiper-pagination swiper-pagination-white"></div>
		</div>
		<script>
			//메인 비주얼 기본 슬라이드
			if($('.swiper-container').attr('slide-delay')==undefined){
				slide_delay = false;
			}else{
				slide_delay = {delay: $('.swiper-container').attr('slide-delay'), disableOnInteraction: false}
			}
			var mySwiper = new Swiper('.swiper-container', {
				loop: true,
				pagination: {
					el: '.swiper-pagination',
					clickable: true
				},
				autoplay : slide_delay
			});
		</script>
	</div>

	<?
		//배열값
		$orderlist = array();
		$ton = 1;

		//정렬값 추출
		//기존 순서 가져와서 최대값 등록
		$bgASql2=" SELECT max(s_order) as s_orders FROM tblmainbanner WHERE 1=1 ";
		$bgAResult2=mysql_query($bgASql2,get_db_conn());
		$bgARow2=mysql_fetch_object($bgAResult2);
		$bannerAorder2=$bgARow2->s_orders+4;	


		$order1_sql=" select * from tblmobileplanningmain where display = 'Y' order by pm_idx ASC ";
		$order1_result=mysql_query($order1_sql,get_db_conn());
		while($order1_row=mysql_fetch_object($order1_result)){
				if($order1_row->s_order){
					$orderlist[$ton][order]	 = $order1_row->s_order;
				}else{
					$orderlist[$ton][order]	 = $bannerAorder2;
					$bannerAorder2++;
				}
				$orderlist[$ton][ng]	 = "S";
				$orderlist[$ton][ck]	 = $order1_row->s_use;
				$orderlist[$ton][name]	 = $order1_row->title;
				$orderlist[$ton][id]	 = $order1_row->pm_idx;	
			$ton++;
		}
		

		//정렬
		$sor = 1;
		$order2_sql=" SELECT * FROM tblmainbanner WHERE position = 'B' and device = 'M' group by banner_group order by banner_group ASC ";
		$order2_result=mysql_query($order2_sql,get_db_conn());
		while($order2_row=mysql_fetch_object($order2_result)){
				if($order2_row->s_order){
					$orderlist[$ton][order]	 = $order2_row->s_order;
				}else{
					$orderlist[$ton][order]	 = $bannerAorder2;
					$bannerAorder2++;
				}
				$orderlist[$ton][name]	 = "콘텐츠배너/동영상 영역".$sor;
				$orderlist[$ton][ng]	 = "B";
				$orderlist[$ton][ck]	 = $order2_row->s_use;
				$orderlist[$ton][id]	 = $order2_row->banner_group;	
		$order_not++;
		$ton++;
		$sor++;
		}

		 function array_sort($arr, $dimension) {
				if($dimension)
				{
					for($i = 0; $i < sizeof($arr); $i++) {
						array_unshift($arr[$i], $arr[$i][$dimension]);
					}
						@sort($arr);
						for($i = 0; $i < sizeof($arr); $i++) {
							array_shift($arr[$i]);
						}
				} else {
						@sort($arr);
				}
		 
				return $arr;
		 }
		 $orderlist2 = array_sort($orderlist, "order");

		 $utt = 0;
		for($tt=1; $tt<count($orderlist2); $tt++){
			//사용유무
			if($orderlist2[$tt][ck] == "1"){
				if($orderlist2[$tt][ng] == "S"){

					$pm_idx = $orderlist2[$tt][id];
					include $mobilePATH."main_display_product.php";

				}else if($orderlist2[$tt][ng] == "B"){
					//배너 풀사이즈 사용유무 확인
					$visiul_sql3="SELECT * FROM tblmainbanner WHERE position='B' AND banner_group='".$orderlist2[$tt][id]."' and device = 'M'";
					$visiul_result3=mysql_query($visiul_sql3);
					$visiul_row3=mysql_fetch_object($visiul_result3);
					//배너 풀사이즈 사용유무 확인

					echo "<style>
								.mainBanner .loop_banner{position:relative;float:left;z-index:2;".($visiul_count>1?"height:auto;":"")."overflow:hidden;}
							</style>
					";

					echo "<div class='".($visiul_row3->banner_group_fullsize=='Y'?"":"wrapper tableposition ")."mainBanner' style='padding-bottom:0px;overflow:hidden;width: 100%;'>";
					echo "<div class='tableposition_absolute'></div>";
					$visiul_sql2="SELECT * FROM tblmainbanner WHERE position='B' AND banner_group='".$orderlist2[$tt][id]."' and device = 'M' ORDER BY date DESC";
					$visiul_result2=mysql_query($visiul_sql2);
					$visiul_count=mysql_num_rows($visiul_result2);

					$i=1;
					while($visiul_row2=mysql_fetch_object($visiul_result2)){
						$marginRight=($visiul_row2->banner_group_marginright*($visiul_count-1))/$visiul_count;

						echo "<div class='loop_banner' style='width:calc(".(100/$visiul_count)."% - ".$marginRight."px);".($visiul_count==$i?"margin-right:0px":"margin-right:".($visiul_row2->banner_group_marginright)."px").";margin-top:".$visiul_row2->banner_group_margintop."px;margin-bottom:".$visiul_row2->banner_group_marginbottom."px;'>";

						if($visiul_row2->type=="M"){ //동영상 배너일 때
							$youtube_addr=str_replace("https://youtu.be/","",$visiul_row2->movie_url); //유튜브 코드만 추출
							echo "<a class='modal_movie' href='http://www.youtube.com/watch?v=".$youtube_addr."'>";
							echo "<div style=\"height:".$visiul_row2->banner_group_height."px;background:url('https://img.youtube.com/vi/".$youtube_addr."/sddefault.jpg') no-repeat;background-size:cover;background-position:center;\">";
							echo "<div style=\"position:absolute;top:50%;left:50%;width:64px;height:64px;margin-top:-32px;margin-left:-32px;background:url('/board/images/icon_play.png') no-repeat;background-position:center;\"><!--동영상버튼--></div>";
							echo "</div>";
							echo "</a>";

						}else{ //이미지 배너일 때
							echo '<a href="'.$visiul_row2->link_url.'" target="'.$visiul_row2->target.'" style="position:relative;display:block;">';
							echo '<div style="height:'.$visiul_row2->banner_group_height.'px;background:url(\'/m/upload/'.$visiul_row2->files.'\') no-repeat;background-size:cover;background-position:center;"></div>';

							if(strlen($visiul_row2->title)>0 || strlen($visiul_row2->contents)>0){ //배너 타이틀 출력
								if($visiul_row2->contents_position=='top'){ //텍스트가 이미지 위 출력일 대
									echo "<div style=\"display:table;position:absolute;top:0px;left:0px;width:100%;height:".$visiul_row2->banner_group_height."px;padding:7%;box-sizing:border-box;\">";
									echo "<div style=\"display:table-cell;height:100%;text-align:".$visiul_row2->contents_position_x.";vertical-align:".$visiul_row2->contents_position_y.";\">";
									if($visiul_row2->bannerText_modal=='Y'){
										echo "<div style='position:absolute;top:0px;left:0px;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1;'><!--modal bg--></div>";
									}

								}else if($visiul_row2->contents_position=='overTop'){ //텍스트가 이미지 오버시 위 출력일 때(미구현)
									echo "<div style='padding:10px 5px;box-sizing:border-box;text-align:".$visiul_row2->contents_position_x.";'>";

								}else{ //텍스트가 이미지 하단 출력일 때
									echo "<div style='padding:10px 5px;box-sizing:border-box;text-align:".$visiul_row2->contents_position_x.";'>";
								}

								if(strlen($visiul_row2->title)>0){ //배너 타이틀이 있을 때
									echo "<h2 style=\"position:relative;z-index:2;margin-bottom:5px;line-height:120%;color:".$visiul_row2->title_color.";font-size:".$visiul_row2->title_size.";font-weight:".($visiul_row2->title_weight=='B'?"bold":"").";font-family:".$visiul_row2->title_fonts.";letter-spacing:-1px;".($visiul_row2->bannerText_shadow=='Y'?"text-shadow:2px 2px 6px rgba(0,0,0,0.6);":"")."\">".$visiul_row2->title."</h2>";
								}

								if(strlen($visiul_row2->contents)>0){ //배너 설명문구가 있을 때
									echo "<span style=\"position:relative;z-index:2;display:inline-block;line-height:120%;color:".$visiul_row2->contents_color.";font-size:".$visiul_row2->contents_size.";font-weight:".($visiul_row2->contents_weight=='B'?"bold":"").";letter-spacing:-1px;".($visiul_row2->bannerText_shadow=='Y'?"text-shadow:2px 2px 6px rgba(0,0,0,0.6);":"")."\">".$visiul_row2->contents."</span>";
								}

								if($visiul_row2->contents_position=='top'){
									echo "</div>";
									echo "</div>";
								}else{
									echo "</div>";
								}
							}
							echo "</a>";
						}
						echo "</div>"; // loop_banner
						$i++;
					}
					echo "</div>";
				}
			}
		}


		include $mobilePATH."main_notice_list.php";
		//include $mobilePATH."main_pavorite_menu.php";
	?>
</div>

<script type="text/javascript" src="./js/wishlist_ajax.js"></script>

<script language="javascript">
	<!--
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


	function CheckForm(theform, productcode, type) {
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

		theform.ordertype.value=type;
		theform.submit();
	}


	function check_login() {
		if(confirm("로그인이 필요한 서비스입니다. 로그인을 하시겠습니까?")) {
			document.location.href="login.php?chUrl=<?=getUrl()?>";
		}
	}
	//-->
</script>

<!-- 옵션바로담기 관련 테스트 -->
<script>
	function optChecker(prcode,type){
		$('#show_contents').html("");
		$.post('productlist_opt_checker.php?productcode='+prcode+'&buy_type='+type, function(data){
			$('#show_contents').html(data);
		});

		setTimeout(function(){
			$('#wrap_layer_popup').dialog({
				create:function(){
					$(this).parent().css({position:"fixed"});
				},
				title: '상품 옵션선택',
				modal: true,
				width: '90%',
				height: 'auto'
			});
		},200);
	}
</script>

<?
	echo $onload;
	include_once($Dir."lib/mobile_eventlayer.php");
	include_once('footer.php');
?>
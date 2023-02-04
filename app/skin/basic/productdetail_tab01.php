<?
	setcookie("prd_cd", $productcode, time() + 3600,"/");
	/*<script>
		function openBicImage(){
			window.open("productdetail_image_popup.php?productcode=<?=$_GET[productcode]?>","","");
		}
	</script>*/
?>

<style>
	.deliInfo img{max-width:100%;}
</style>

<!-- view탭 -->
<script type="text/javascript">
	$(function(){
		$("ul.detail_panel .panel_li:not("+$("ul.detail_tab li.on").attr("rel")+")").hide();
		$("ul.detail_tab li").on("click",function(){
			$("ul.detail_tab li").removeClass("on");
			$(this).addClass("on");
			$("ul.detail_panel .panel_li").hide();
			$($(this).attr("rel")).show();
			return false;
		});
	});

	//탭메뉴 클릭시 해당 패널로 이동
	function move_Panel(val){
		if($('.tab_move_panel').length){
			var offset=$('.tab_move_panel').offset();
			$('html, body').animate({scrollTop : offset.top-$('#top').height()}, 100); //상단메뉴 높이값만큼 아래로 이동
		}
	}
</script>

<div id="tab_move_panel" class="tab_non_move_panel"><? /* 탭메뉴 클릭시 상단좌표를 여기에 맞춤 */ ?></div>

<div class="wrapper">
	<!-- view탭 -->
	<ul id="detail_tab" class="detail_tab">
		<li rel="#prdetail_panel" onclick="move_Panel(1)" class="on">기본정보</li>
		<li rel="#prreview_panel" onclick="move_Panel(2)">상품평<?=($t_cnt3>0?"<span style='color:#ff6600;'>(".$t_cnt3.")</span>":"(".$t_cnt3.")")?></li>
		<li rel="#prqna_panel" onclick="move_Panel(3)">상품문의<?=($t_cnt4>0?"<span style='color:#ff6600;'>(".$t_cnt4.")</span>":"(".$t_cnt4.")")?></li>
	</ul>

	<ul class="detail_panel">
		<li id="prdetail_panel" class="panel_li">
			<!-- //view탭 -->
			<div class="detail_more" style="text-align:center;">
				<div onClick="prdetailView();" style="display: inline-block;padding: 0 2em;border: 1px solid #6a6a6a;line-height: 4em;font-size: 15px;color: #848484;"><img src="/app/skin/basic/img/search.png">상품정보 확대보기</div>
			</div>

			<div class="detail_content ckeditor_view_css">
				<?
					if(strlen($detail_filter)>0) {
						$_pdata->content = preg_replace($filterpattern,$filterreplace,$_pdata->content);
					}

					if (strpos($_pdata->content,"table>")!=false || strpos($_pdata->content,"TABLE>")!=false){
						echo "<pre>".$_pdata->content."</pre>";

					}else if(strpos($_pdata->content,"</")!=false){
						//echo ereg_replace("\n","<br>",$_pdata->content);
						echo stripslashes($_pdata->content);

					}else if(strpos($_pdata->content,"img")!=false || strpos($_pdata->content,"IMG")!=false){
						//echo ereg_replace("\n","<br>",$_pdata->content);
						echo stripslashes($_pdata->content);

					}else{
						//echo ereg_replace(" ","&nbsp;",ereg_replace("\n","<br>",$_pdata->content));
						echo stripslashes($_pdata->content);
					}
echo $_pdata->content;
					//상품정보고시
					$ditems = _getProductDetails($_pdata->pridx);
					if(_array($ditems) && count($ditems) > 0){
				?>
				<table border="0" cellpadding="0" cellspacing="0" class="productInfoGosi">
					<caption>상품정보제공 고시</caption>
					<? foreach($ditems as $ditem){ ?>
					<tr>
						<th><?=$ditem['dtitle']?></th>
						<td><?=nl2br($ditem['dcontent'])?></td>
					</tr>
					<? }// end foreach ?>
				</table>
				<? } // end if ?>
			</div>

		<?
			if(nameTechUse($_pdata->vender)){

				$sql = "SELECT * FROM tblvenderstore WHERE vender='".$_pdata->vender."'";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);

				$venderinfoSQL = "SELECT COUNT(p.pridx) AS prcount, v.com_name, v.com_owner, v.com_image ";
				$venderinfoSQL .= "FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ON(p.vender = v.vender) ";
				$venderinfoSQL .= "WHERE v.vender = '".$_pdata->vender."' AND p.display='Y' ";

				$venderimagedir = file_exists($Dir."data/shopimages/vender/top_".$_pdata->vender.".gif");
				if($venderimagedir){
					$venderimage = $Dir."data/shopimages/vender/top_".$_pdata->vender.".gif";
				}else{
					$venderimage='';
				}

				$venderprcount=$vendername=$ownername=$image=$src=$vendersize="";
				if(false !==$venderinfoRes = mysql_query($venderinfoSQL,get_db_conn())){
					$venderprcount=mysql_result($venderinfoRes,0,0);
					$vendername=mysql_result($venderinfoRes,0,1);
					$ownername=mysql_result($venderinfoRes,0,2);
					$image=mysql_result($venderinfoRes,0,3);
					$src = $venderimagedir.$image;

					$vendersize = _getImageRateSize($src,80);
		?>
			<section>
				<div style="position:relative;margin:40px 0px;background:url('<?=$venderimage?>') no-repeat;background-position:center;background-size:auto 100%;">
					<div style="position:absolute;top:0px;left:0px;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:1;"></div>
					<div style="position:relative;padding:40px;color:#fff;text-align:justify;z-index:10;">
						<h2 style="color:#fff;"><?=$row->brand_name?></h2>
						<?=(strlen($row->brand_description)>0?"<p style='margin-top:15px;color:#fff;'>".$row->brand_description."</p>":"")?>
					</div>
				</div>

				<?
					//입점업체 상품 출력
					if( $_pdata->vender > 0 ) {
						$venderproductSQL = "SELECT productcode, tinyimage, sellprice, consumerprice, reserve, reservetype, productname FROM tblproduct ";
						$venderproductSQL .= "WHERE vender ='".$_pdata->vender."' ";
						$venderproductSQL .= "AND productcode !='".$productcode."' ";
						$venderproductSQL .= "AND (maximage != '' || maximage is null) ";
						$venderproductSQL .= "AND display= 'Y' ";
						$venderproductSQL .= "ORDER BY regdate";

						$venderproduct="<ul style=\"display:flex;margin:15px 0px;padding-bottom:15px;border-bottom:1px solid #eee;overflow-x:scroll;-webkit-overflow-scrolling:touch;\">\n";

						if(false !== $venderproductRes = mysql_query($venderproductSQL, get_db_conn())){
							$venderproductNum = mysql_num_rows($venderproductRes);

							if($venderproductNum <= 0){
								$venderproduct .= "	<li>등록된 상품이 없습니다.</li>\n";
							}else{
								while($venderproductRow = mysql_fetch_assoc($venderproductRes)){
									$reserveconv=getReserveConversion($venderproductRow['reserve'],$venderproductRow['reservetype'],$venderproductRow['sellprice'],"Y");
									$src=$Dir."data/shopimages/product/".$venderproductRow['tinyimage'];

									//이미지 사이즈 체크해서 CSS추가
									$imgsize=getimagesize($src);
									if($imgsize[0]>$imgsize[1]){
										$addstyle="background-size:auto 100%;";
									}else{
										$addstyle="background-size:100% auto;";
									}

									if(strlen($venderproductRow['tinyimage'])>0){
										$size = '100%'; //상품이미지 가로 사이즈
									}

									$venderproduct .= "
										<li style=\"flex:none;float:left;width:40%;margin:0px 1%\">
											<a href='/app/productdetail_tab01.php?productcode=".$venderproductRow['productcode']."'><div>
												<div style=\"margin-bottom:10px;line-height:0%;background:url('".$src."') no-repeat;background-position:center;".$addstyle."\"><img src='/images/common/trans.gif' style='width:100%;' alt='' /></div>
												<p style='margin-bottom:10px;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;'>".$venderproductRow['productname']."</p>
												".($venderproductRow['consumerprice']>0 ? "<p style=\"text-decoration:line-through\">".number_format($venderproductRow['consumerprice'])."원</p>":"")."
												<p>".number_format($venderproductRow['sellprice'])."원</p>
											</div></a>
										</li>
									";
								}
							}

						}else{
							$venderproduct .= "<li>DB 와 연결중에 오류가 발생하였습니다.\n다시 시도해 주시기 바랍니다.</li>\n";
						}
						$venderproduct .= "</ul>\n";
					}
					echo "<h2 style='font-weight:normal;letter-spacing:-1px;'>미니샵 다른 상품보기</h2>";
					echo $venderproduct;
				?>
			</section>
		<?
				}
			}

			//배송/교환/환불정보
			$deli_info="";
			if($deliinfono!="Y") {	//개별상품별 배송/교환/환불정보 노출일 경우
				$deli_info_data="";
				if($_pdata->vender>0 && strlen($_vdata->deli_info)>0) {		//입점업체 상품이면서 배송/교환/환불정보가 있을경우 입점업체 배송/교환/환불정보 누출
					$deli_info_data=$_vdata->deli_info;
					$aboutdeliinfofile=$Dir.DataDir."shopimages/vender/aboutdeliinfo_".$_vdata->vender.".gif";
				} else {
					$deli_info_data=$_data->deli_info;
					$aboutdeliinfofile=$Dir.DataDir."shopimages/etc/aboutdeliinfo.gif";
				}

				if(strlen($deli_info_data)>0) {
					$tempdeli_info=explode("=",$deli_info_data);
					if($tempdeli_info[0]=="Y") {
						if($tempdeli_info[1]=="TEXT") {	//텍스트형
							$allowedTags = "<h1><b><i><a><ul><li><pre><hr><blockquote><u><img><br><font>";

							if(strlen($tempdeli_info[2])>0 || strlen($tempdeli_info[3])>0) {
								if(strlen($tempdeli_info[2])>0) {	//배송정보 텍스트
									$deli_info.= "<div class='detail_info2'>";
									$deli_info.= "<h1>배송안내</h1>\n";
									$deli_info.= "<p>".nl2br(strip_tags($tempdeli_info[2],$allowedTags))."</p>\n";
									$deli_info.= "</div>";
								}
								if(strlen($tempdeli_info[3])>0) {	//교환/환불정보 텍스트
									$deli_info.= "<div class='detail_info2'>";
									$deli_info.= "<h1>교환/반품/환불 안내</h1>\n";
									$deli_info.= "<p>".nl2br(strip_tags($tempdeli_info[3],$allowedTags))."</p>\n";
									$deli_info.= "</div>\n";
								}
							}
						} else if($tempdeli_info[1]=="IMAGE") {	//이미지형
							if(file_exists($aboutdeliinfofile)) {
								$deli_info.= "<div class='detail_info2'><img src=\"".$aboutdeliinfofile."\" border=\"0\" alt=\"\" /></div>\n";
							}
						} else if($tempdeli_info[1]=="HTML") {	//HTML로 입력
							if(strlen($tempdeli_info[2])>0) {
								$deli_info.= "<div class='detail_info2'>".$tempdeli_info[2]."</div>\n";
							}
						}
					}
				}
			}

			if(strlen($deli_info)>0) {
				echo $deli_info;
			}

			/*
			<p>상품정보고시와 배송/AS/환불 안내 규정은 PC버전에서 확인할 수 있습니다.</p>
			<a href="../front/productdetail.php?productcode=<?=$_pdata->productcode?>#2" class="basic_button" >확인하기</a>
			*/
		?>
		</li>

		<li id="prreview_panel" class="panel_li">
			<!-- 상품평 -->
			<? include "prreview.php"; ?>
		</li>

		<li id="prqna_panel" class="panel_li">
			<!-- 상품문의 -->
			<? include ("prqna.php"); ?>
		</li>
	</ul>

</div>
<!-- TAB1-기본정보 -->
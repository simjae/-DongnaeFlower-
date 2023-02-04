<div id="list">
	<!-- 상단 카테고리 그룹 -->
	<div class="swiper-container">
		<div class="swiper-wrapper">
			<?php
			if(!$vidx){
				if (($end = count($categoryNavi)) > 0) {
					for ($i=0; $i < $end; $i++) {
						$cate = $categoryNavi[$i]['cate'];
						// 하위 카테고리 없으면 출력 안함
						if (!$cate) { continue; }
						$curCateCode = subCategoryCode($categoryNavi[$i]['curCode']);
						$cend=count($cate);
						$curCateName="선택";
						$cate_lis="";
						if ($categoryNavi[$i]['depth'] != 0) {
							$allCate = getSubAllCate($categoryNavi[$i]['curCode'], $categoryNavi[$i]['depth']);

							if ($categoryNavi[$i]['curCode'] == $allCate) {
								$curCateName = "전체보기";
							}

							$cate_lis = "<li><a href='/m/productlist.php?code=".$allCate."'><span>전체보기</span></a></li>";
						}

						if ($cend > 0) {
							for ($j=0; $j < $cend; $j++) {
								if ($cate[$j]->codeA == $curCateCode['codeA'] && $cate[$j]->codeB == $curCateCode['codeB'] && $cate[$j]->codeC == $curCateCode['codeC'] && $cate[$j]->codeD == $curCateCode['codeD']) {
									$curCateName = $cate[$j]->code_name;
								}

								$slctCateCode = "";
								for ($k=0,$e=4; $k < $e; $k++) {
									$cateCodes = $cate[$j]->{'code'.chr(65+$k)};
									if ($cateCodes != "000") {
										$slctCateCode .= $cateCodes;
									}
								}

								$cate_lis .= '
								<li class="selectCate"><a href="/m/productlist.php?code='.$slctCateCode.'"><span>'.$cate[$j]->code_name.'</span></a></li>';
							}
						}
				?>
				<div class="swiper-slide">
					<a class="fistCateName" id='prd_cate_<?=$i?>' onclick="javascript:toggle('prdCateLayer_<?=$i?>');"><span><?php echo $curCateName?></span></a>
					<ul style="display:none" id="prdCateLayer_<?=$i?>" name="prdCateLayer_<?=$i?>">
						<?php echo $cate_lis ?>
					</ul>
				</div>
			<?php
				}
			}
		}else{
			echo $codenavi;
		}
		?>
		</div>
	</div>
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

		<div class="product_opt active">
			<h1 class="<?if($_COOKIE['searchmore']==1) echo $_active?>">Search More<span><img src="/images/tri.png"></span></h1>
			<ul class="search_more" style="<?if($_COOKIE['searchmore']==2){?>height: 0px; margin-top: 0px;<?}?>">
				<!--Quick Search More-->
				<div class="quickSearchMoreWrap">
					<div class="colorSelect">
						<ul class="search_color">
						<?
						$codegroup = "";
						while($color_row = mysql_fetch_object($color_result)){
							$codegroup .= '<li><label for="color_idx'.$color_cnt.'"><input type="checkbox" id="color_idx'.$color_cnt.'" name="search_color_idx[]" value="'.$color_row->color_idx.'"';
							if(sizeof(explode(':'.$color_row->color_idx.':',$search_color_idx)) > 1) { $codegroup .= ' checked'; }
							$codegroup .= ' /><div><span>'.$color_row->color_name.'</span></div></label></li>';
							$color_cnt++;
						}
						echo $codegroup;
						?>
						</ul>
					<!--<img src="/data/design/etc/test_color.gif">-->
					
					</div>
					<ul class="categorySelect">
						<li class="categorySelectLi">
							<p class="categoryTtile">1차카테고리</p>
							<div>
								<select name=ca_1 onchange="getSubCategory(this.value, 2)">
									<?
									$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
									$sql .= "WHERE codeB='000' AND codeC='000' ";
									$sql .= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
									$result = mysql_query($sql, get_db_conn());
									while ($row = mysql_fetch_object($result)) {
										echo "<option value=\"" . $row->codeA . "\"";
										if ($row->codeA == substr($code, 0, 3))
											echo " selected";
										echo ">" . $row->code_name . "</option>\n";
									}
									mysql_free_result($result);
									?>						
								</select>
							</div>
						</li>
						<li class="categorySelectLi">
							<p class="categoryTtile">2차카테고리</p>
							<div>
								<select name=ca_2 onchange="getSubCategory(this.value, 3)">
									<option value="">전체보기</option>
									<?
									if(strlen($code) >= 3){
										$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
										$sql .= "WHERE codeA='{$codeA}' and codeB!='000' and codeC='000' ";
										$sql .= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
										$result = mysql_query($sql, get_db_conn());
										while ($row = mysql_fetch_object($result)) {
											echo "<option value=\"" . $row->codeA.$row->codeB . "\"";
											if ($row->codeA.$row->codeB == substr($code, 0, 6))
												echo " selected";
											echo ">" . $row->code_name . "</option>\n";
										}
										mysql_free_result($result);
									}
									?>
								</select>
							</div>
						</li>
						<li class="categorySelectLi">
							<p class="categoryTtile">3차카테고리</p>
							<div>
								<select name=ca_3 onchange="getSubCategory(this.value, 4)">
									<option value="">전체보기</option>
									<?
									if(strlen($code) >= 6){
										$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
										$sql .= "WHERE codeA='{$codeA}' AND codeB='{$codeB}' AND codeC!='000'";
										$sql .= " AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
										$result = mysql_query($sql, get_db_conn());
										while ($row = mysql_fetch_object($result)) {
											echo "<option value=\"" . $row->codeA.$row->codeB.$row->codeC . "\"";
											if ($row->codeA.$row->codeB.$row->codeC == substr($code, 0, 9))
												echo " selected";
											echo ">" . $row->code_name . "</option>\n";
										}
										mysql_free_result($result);
									}
									?>
								</select>
							</div>
						</li>
						<li class="categorySelectLi">
							<p class="categoryTtile">4차카테고리(코드)</p>
							<div>
								<select name=ca_4 onchange="getSubCategory(this.value, 5)">
									<option value="">전체보기</option>
									<?
									if(strlen($code) >= 9){
										$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
										$sql .= "WHERE codeA='{$codeA}' AND codeB='{$codeB}' AND codeC='{$codeC}'";
										$sql .= " AND codeD!='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
										$result = mysql_query($sql, get_db_conn());
										while ($row = mysql_fetch_object($result)) {
											echo "<option value=\"" . $row->codeA.$row->codeB.$row->codeC.$row->codeD . "\"";
											if ($row->codeA.$row->codeB.$row->codeC.$row->codeD == substr($code, 0, 12))
												echo " selected";
											echo ">" . $row->code_name . "</option>\n";
										}
										mysql_free_result($result);
									}
									?>
								</select>
							</div>
						</li>
					</ul>
					<p style="clear:both;"></p>
					<div class="priceSearch">
						<div class="price">
							<span style="padding-right:10px;">가격</span>
							<input type="text" name="search_price_s" value="<?=$search_price_s?>">&nbsp;~&nbsp;
							<input type="text" name="search_price_e" value="<?=$search_price_e?>">
						</div>
						<div class="searchWord">
							<span>검색어</span>
							<input type="text" name="searchkey" value="<?=$searchkey?>">
							<div class="searchBtn button btn_search_price" style="cursor:pointer;">검색하기</div>
						</div>
					</div>
					<div class="categoryAllClose"><a href="javascript:interceptclose()"><span>X</span></a></div>
				</div>
			</ul>
		</div>

		<!--Quick Search More-->
		<script type="text/javascript">
			//<![CDATA[
			$('.btn_search_price').click(function() {
				var code="";
				if($("select[name=ca_1]").val()) code = $("select[name=ca_1]").val();
				if($("select[name=ca_2]").val()) code = $("select[name=ca_2]").val();
				if($("select[name=ca_3]").val()) code = $("select[name=ca_3]").val();
				if($("select[name=ca_4]").val()) code = $("select[name=ca_4]").val();
				

				var sprice = $(".price input[name=search_price_s]").val();
				var eprice = $(".price input[name=search_price_e]").val();
				var searchkey = $(".searchWord input[name=searchkey]").val();
				$('form[name="form2"] input[name=search_price_s]').val(sprice);
				$('form[name="form2"] input[name=search_price_e]').val(eprice);
				$('form[name="form2"] input[name=searchkey]').val(searchkey);
				$('form[name="form2"] input[name=code]').val(code);
				$('form[name="form2"]').submit();
			});

			$('.search_color input').click(function() {
				var cidx = '';
				$('.search_color input[type=checkbox]:checked').each(function (i) {
					if(cidx != '') { cidx += '|'; }
					if(this.checked){ cidx += ':'+$(this).val()+':'; }
				});
				$('form[name="form2"] input[name=search_color_idx]').val(cidx);
				$('form[name="form2"]').submit();
			});

			function interceptclose() {
				var modal1 = $('.product_opt h1', window.document); 
				modal1.click(); 
			}

			function getSubCategory(code, depth) {
				ajaxpage("prdtlist.ctgr.php?code="+code+"&depth="+depth);
			}

			function ajaxpage(url){
				$.getScript(url, function ( data, textStatus, jqxhr ) {
					jsloding();
				});
			}
			//]]>
		</script>
		<script src="/js/product_m_option.js"></script>


		<div class="list_sort">
			<div class="options">
				<span class="basic_select">
					<select onChange="ChangeSort(this.value, '<?=$displaymode?>')">
						<option value="">최근등록순</option>
						<option value="price_desc" <?if($_GET[sort]=="price_desc") {echo "selected";}?>>높은가격순</option>
						<option value="price" <?if($_GET[sort]=="price") {echo "selected";}?>>낮은가격순</option>
						<option value="name" <?if($_GET[sort]=="name") {echo "selected";}?>>상품명 순</option>
						<option value="name_desc" <?if($_GET[sort]=="name_desc") {echo "selected";}?>>상품명 역순</option>
						<option value="reserve_desc" <?if($_GET[sort]=="reserve_desc") {echo "selected";}?>>적립금 높은순</option>
						<option value="reserve" <?if($_GET[sort]=="reserve") {echo "selected";}?>>적립금 낮은순</option>
						<option value="production_desc" <?if($_GET[sort]=="production_desc") {echo "selected";}?>>제조사 이름순</option>
						<option value="production" <?if($_GET[sort]=="production") {echo "selected";}?>>제조사 이름역순</option>
					</select>
				</span>
			</div>
			<div class="sort_view">
				<ul>
					<li class="sort_gallery <?=$displaygallery?>" onClick="changeDisplayMode('gallery','<?=$code?>','<?=$sort?>')">
						<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
							<rect width="9" height="9"/>
							<rect x="11" width="9" height="9"/>
							<rect x="11" y="11" width="9" height="9"/>
							<rect y="11" width="9" height="9"/>
						</svg>
					</li>
					<li class="sort_webzine <?=$displaywebzine?>" onClick="changeDisplayMode('webzine','<?=$code?>','<?=$sort?>')">
						<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
							<rect class="st0" width="4" height="2"/>
							<rect x="7" y="0" width="13" height="2"/>
							<rect x="7" y="8" width="13" height="2"/>
							<rect y="8" width="4" height="2"/>
							<rect y="16" width="4" height="2"/>
							<rect x="7" y="16" width="13" height="2"/>
						</svg>
					</li>
					<li class="sort_list <?=$displaylist?>" onClick="changeDisplayMode('list','<?=$code?>','<?=$sort?>')">
						<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="24px" height="20px" viewBox="0 0 24 20" style="enable-background:new 0 0 24 20;" xml:space="preserve">
							<rect width="20" height="9"/>
							<rect y="12"  width="20" height="10"/>
						</svg>
					</li>
				</ul>
			</div>
		</div>

		<?
			//search by alice [START]
			$search_sql = '';

			//brand
			if($search_bridx) {
				$_bridx = str_replace(':','',$search_bridx);
				$_bridx = str_replace('|',',',$_bridx);
				if($search_bridx) { $search_brand_add = ','; }
				else { $search_brand_add = ''; }
				$search_brand = $search_brand_add.$_bridx;
				
				$search_sql.= "AND (a.brand IN (".$search_brand.")) ";
				
				$add_query.="&search_bridx=".$search_bridx;
			}

			//price
			if($search_price_s || $search_price_e) {
				if($search_price_s && $search_price_e) {
					$search_sql.= "AND a.sellprice BETWEEN ".$search_price_s." AND ".$search_price_e." ";
				}
				else if($search_price_s && !$search_price_e) {
					$search_sql.= "AND a.sellprice >= ".$search_price_s." ";
				}
				else if(!$search_price_s && $search_price_e) {
					$search_sql.= "AND a.sellprice <= ".$search_price_e." ";
				}
				$add_query.="&search_price_s=".$search_price_s."&search_price_e=".$search_price_e;
			}

			//color
			if($search_color_idx) {
				$arr_color_idx = explode('|',$search_color_idx);
				$q = "";
				for($i=0; $i<sizeof($arr_color_idx); $i++) {
					if($i > 0) { $q = $q." OR "; }
					$q = $q."(a.color_idx LIKE '%".$arr_color_idx[$i]."%')";
				}
				
				$search_sql.= "AND (".$q.") ";
				$add_query.="&search_color_idx=".$search_color_idx;
			}

			if($searchkey){
				$search_sql.= "AND a.productname like '%{$searchkey}%' ";
				$add_query.="&searchkey=".$searchkey;
			}
			//search by alice [ END ]

			$sql = "SELECT COUNT(distinct a.productcode) as t_count ";
			$sql.= "FROM tblproduct AS a left join tblcategorycode as cc on cc.productcode = a.productcode ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= $qry." ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			if(strlen($not_qry)>0) {
				$sql.= $not_qry." ";
			}
			$sql.= $search_sql." "; //search by alice
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$rowcount = (int)$row->t_count;
			mysql_free_result($result);

			$tmp_sort=explode("_",$sort);
			if($tmp_sort[0]=="reserve") {
				$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
			}
			$sql = "SELECT distinct a.productcode, a.productname, a.sellprice, a.discountRate, a.quantity, a.consumerprice, a.reserve, a.reservetype, a.production, ";
			if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
			$sql.= "a.tag, a.tinyimage, a.minimage, a.maximage, a.wideimage, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode, a.prmsg, a.option1, a.option2, a.option_quantity, a.productdisprice, a.youtube_url, a.youtube_prlist, a.youtube_prlist_imgtype, a.youtube_prlist_file, if(a.productdisprice>0,1,0) as isdiscountprice ";
			$sql.= $addsortsql;

			$sql.= ", v.com_name, a.vender, a.reservation ";
			$sql.= "FROM tblproduct AS a LEFT JOIN tblcategorycode as cc on cc.productcode = a.productcode ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "LEFT OUTER JOIN tblvenderinfo AS v ON(a.vender = v.vender) "; 

			$sql.= $qry." ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			if(strlen($not_qry)>0) {
				$sql.= $not_qry." ";
			}
			$sql.= $search_sql." "; //search by alice

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
			switch($displaymode){
				case "gallery":
		?>
				<div class="product_a">
					<ul class="product_list">
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

									$viewPrice="";
									$dicker=new_dickerview($gelleryRow['etctype'],$wholeSaleIcon.number_format($gelleryRow['sellprice'])."원",1);
									if($memberprice > 0) {	// 회원 로그인(및 등급 할인) 일 경우에만 
										$viewPrice.="<img src='/images/common/memsale_icon.gif' /> ";
									}

									if (count($dicker['memOpenData']) > 0) {
										if ($dicker['memOpenData']['type'] == "img") {
											$viewPrice.="<img src='".$dicker['memOpenData']['value']."' />";
										} else {
											$viewPrice.="<span class=''>".$dicker['memOpenData']['value']."</span>";
										}
									} else if (strlen($_data->proption_price) == 0) {
										$viewPrice.=$wholeSaleIcon.number_format($gelleryRow['sellprice'])."원";
									} else {
										if (strlen($gelleryRow['option_price']) == 0) {
											$viewPrice.=$wholeSaleIcon.number_format($gelleryRow['sellprice'])."원";
										} else {
											$viewPrice.=ereg_replace("\[PRICE\]",number_format($gelleryRow['sellprice']),$_data->proption_price);
										}
									}

									//상품평 수
									$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
									$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
									$row_cnt3=mysql_fetch_object($result_cnt3);
									$t_cnt3 = (int)$row_cnt3->t_count;


									if(strlen($gelleryRow[tinyimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$gelleryRow[tinyimage])==true){
										$background_url=$Dir.DataDir."shopimages/product/".urlencode($gelleryRow[tinyimage]);
									}else{
										$background_url=$Dir."images/no_img.gif";
									}

									$prdetail_link="productdetail_tab01.php?productcode=".$productcode.($vidx?"&vidx=".$vidx:"");


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
						?>
						<li class="product_item">
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

								<?
									if($productlist_basket=="Y"){

										########## 옵션체계 바껴서 아래꺼 사용 못할듯 ;; ###########
										$proption1="";
										$oneoption1="";
										if(strlen($option1)>0) {
											$temp = $option1;
											$tok = explode(",",$temp);
											$count=count($tok);

											$optioncnt = explode(",",substr($optionquantity,1));

											// count == 2 는 옵션이 1개 일 때.
											if($count==2){
												$oneoption1="value=\"1\" ";
												$proption1.="<span class=\"basic_select\"><select name=\"option1\" style=\"width:100%\">\n";
												$proption1.="	<option>".$tok[1];
												if(strlen($option2)==0 && $optioncnt[0]=="0") $proption1.=" (품절)";
												$proption1.="	</option>\n";
												$proption1.="</select></span>\n";
											}else{
												$proption1.="<span class=\"basic_select\"><select name=\"option1\" style=\"width:100%\">\n";
												$proption1.="<option>".$tok[0]." 선택</option>\n";
												//$proption1.="<option>------------------</option>\n";

												for($k=1;$k<$count;$k++) {
													if(strlen($tok[$k])>0) $proption1.="<option value=\"".$k."\">".$tok[$k];
													if(strlen($option2)==0 && $optioncnt[$k-1]=="0") $proption1.=" (품절)";
													$proption1.="</option>\n";
												}
												$proption1.="	</select></span>\n";
											}
										}

										$proption2="";
										$oneoption2="";
										if(strlen($option2)>0) {
											$temp = $option2;
											$tok = explode(",",$temp);
											$count2=count($tok);

											// count2 == 2는 옵션이 하나일 경우.
											if ($count2 == 2) {
												$oneoption2="value=\"1\" ";
												$proption2.="<span class=\"basic_select\"><select name=\"option2\" style=\"width:100%\"> ";
												$proption2.="	<option>".$tok[1]."</option>\n";
												$proption2.="</select>\n";
											} else {
												$proption2.="<span class=\"basic_select\"><select name=\"option2\" class=\"main_select\" style=\"width:100%\"> ";
												$proption2.= "<option>".$tok[0]." 선택</option>\n";
												//$proption2.="<option>------------------</option>\n";

												for($k=1;$k<$count2;$k++) {
													if(strlen($tok[$k])>0) $proption2.="<option value=\"".$k."\">".$tok[$k]."</option>";
												}
												$proption2.="</select></span>\n";
											}
										}else{
											$proption2.="";
										}

										$proption3 = "";
										if(strlen($optcode)>0) {
											$sql = "SELECT * FROM tblproductoption WHERE option_code='".$optcode."' ";
											$result = mysql_query($sql,get_db_conn());
											if($row = mysql_fetch_object($result)) {
												$optionadd = array ($row->option_value01,$row->option_value02,$row->option_value03,$row->option_value04,$row->option_value05,$row->option_value06,$row->option_value07,$row->option_value08,$row->option_value09,$row->option_value10);
												$opti=0;
												$option_choice = $row->option_choice;
												$exoption_choice = explode("",$option_choice);
												while(strlen($optionadd[$opti])>0) {
													$opval = str_replace('"','',explode("",$optionadd[$opti]));
													$opcnt = count($opval);
													$optitle = $opval[0].($exoption_choice[$opti]==1 ? "(필수)" : "(선택)");

													$proption3.="<span class=\"basic_select\">";
													$proption3.="<select name=\"mulopt\" style=\"width:100%\" onchange=\"mulOptChange(this.form,".$opti.",".$exoption_choice[$opti].")\">";
													$proption3.="<option value=\"0,0\">--- ".$optitle." ---";
													for($j=1;$j<$opcnt;$j++) {
														$exop = str_replace('"','',explode(",",$opval[$j]));
														$proption3.="<option value=\"".$j."\" data-price=\"".$exop[1]."\">";
														if($exop[1]>0) $proption3.=$exop[0]."(+".$exop[1]."원)";
														else if($exop[1]==0) $proption3.=$exop[0];
														else $proption3.=$exop[0]."(".$exop[1]."원)";
														$proption3.="</option>\n";
													}
													$proption3.="</select></sapn>";
													$opti++;
												}
												$proption3.="<input type='hidden' name=\"mulopt\" />";
											}
											mysql_free_result($result);
										}
										########## 옵션 ###########
								?>

								<? $opti=0; ?>
								<form name="bfrm<?=$productcode?>" id="bfrm<?=$productcode?>" method="post" action="./basket.php">
									<? if(strlen($option1)>0){ ?>
										<input type="hidden" id="opt_idx_<?=$productcode?>" name="opt_idx[]" <?=$oneoption1?>/>
										<input type="hidden" id="opt_idx2_<?=$productcode?>" name="opt_idx2[]" <?=$oneoption2?>/>
										<input type="hidden" id="opt_quantity_<?=$productcode?>" name="opt_quantity[]" />
									<? } ?>
									<input type="hidden" name="price" />
									<input type="hidden" name="dollarprice" />
									<input type="hidden" name="code" />
									<input type="hidden" name="productcode" value="<?=$productcode?>" />
									<input type="hidden" name="ordertype" />
									<input type="hidden" name="opts" />

									<?
									if (! $dicker['memOpen']) {
										echo $proption1.$proption2.$proption3;
									?>
									<div class="product_amount">
										<div class="product_minus" onclick="javascript:change_quantity(bfrm<?=$productcode?>.name,'dn');">-</div>
										<div class="product_value"><input type="text" name="quantity" value="1" /></div>
										<div class="product_plus" onclick="javascript:change_quantity(bfrm<?=$productcode?>.name,'up');">+</div>
										<div class="product_buy" onclick="javascript:CheckForm(document.bfrm<?=$productcode?>, '<?=$productcode?>');"></div>
									</div>
									<? } ?>
								</form>
								<? } ?>
							</div>

							<div class="product_info" <?=($productlist_quick=='Y'?"style=\"margin-bottom:65px;\"":"")?>>
								<div class="product_name"><a href="productdetail_tab01.php?productcode=<?=$productcode.($vidx?"&vidx=".$vidx:"")?>" rel="external"><?=$msgreservation?><?=$productname?></a></div>
								<? if(strlen($productmsg)>0){ ?><div class="product_caption"><?=$productmsg?></div><? } ?>
								<? if($prconsumerprice>0){ ?><div class="product_discount"><?=$prconsumerprice?>원</div><? } ?>
								<div class="product_price">
									<?
										echo $viewPrice;
										if($gelleryRow['quantity']=="0") echo soldout();
									?>
								</div>
								<? if(strlen($reservation)>0 && $reservation != "0000-00-00"){ ?>
								<div class="product_reserve"><span><?=$msgreservation?></span> <?=$datareservation?></div>
								<? } ?>
								<? if(strlen($vendername)>0){ ?>
									<div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');"><?=$vendername?></a></div>
								<? } ?>
								<? if($gelleryRow['etctype']){ ?><div>
<!--모바일 메인 아이콘-->
<?
	$icoi = strpos(" ".$gelleryRow['etctype'],"ICON=");
	if($icoi>0){
		$icon = substr($gelleryRow['etctype'],strpos($gelleryRow['etctype'],"ICON="));
		$icon = substr($icon,5,strpos($icon,"")-5);
		$num=strlen($icon);
		for($j=0;$j<$num;$j+=2){
			$temp=$icon[$j].$icon[$j+1];
/*
			if($temp=='04'){
				$icon_name="NEW";
			}else if($temp=='13'){
				$icon_name="BEST";
			}else{
				$icon_name="HOT";
			}
*/
			if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
				echo "<img src=\"http://".$_ShopInfo->shopurl.DataDir."shopimages/etc/icon".$temp.".gif\" align=\"absmiddle\" border=\"0\" />";
			} else if(strlen($temp)>0 && !preg_match("/^(U)[1-6]$/",$temp)) {
				echo "<span class='icon".$temp."'></span>";
			}
		}
	}

?>
<!--모바일 아이콘-->
								<?// echo viewproductname('',$gelleryRow['etctype'],'');?>
								
								</div><? } ?>
							</div>

							<? if($productlist_quick=="Y") { ?>
							<div class="product_communicate">
								<ul>
									<li><a href="productdetail_tab03.php?productcode=<?=$productcode?>&sort=#tapTop" rel="external"><span class="product_coment"><?=$t_cnt3?></span></a></li>
									<?
										$wish_chk = true;
										$wish_sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='".$productcode."' ";
										$wish_result = mysql_query($wish_sql, get_db_conn());
										$wish_row = mysql_fetch_object($wish_result);

										if($wish_row->cnt>0)
											$wish_chk = false;

										if(strlen($_ShopInfo->getMemid())>0){
											if($wish_chk){
									?>
									<li><a href="javascript:wishAjax('<?=$productcode?>', 'im<?=$i?>')" id="im<?=$i?>" class="btn_wishlist off"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
									<? }else{ ?>
									<li><a href="javascript:wishAjax('<?=$productcode?>', 'im<?=$i?>')" id="im<?=$i?>" class="btn_wishlist on"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
									<?
											}
										}else{
									?>
										<li><a href="javascript:check_login()" id="im<?=$i?>" class="btn_wishlist off"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
									<? } ?>
									<li><a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external"><span class="product_more"></span></a></li>
								</ul>
							</div>
							<? } ?>
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
						?>
							<li style="text-align:center;width:100%;">진열된 상품이 없습니다.</li>
						<?
								}
								mysql_free_result($gelleryRes);
							}else{
						?>
							<li style="text-align:center;width:100%;">연결이 지연되었습니다 다시 시도해주세요.</li>
						<? } ?>
					</ul>
				</div>
		<?
				break;

				case "webzine":
				if(!$_ShopInfo->getMemid()){
		?>
				<div class="product_c">
					<ul class="product_list">
					<?
						$itemcount=12; // 페이지당 게시글 리스트 수 
						$sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;
						if(false !== $listRes = mysql_query($sql,get_db_conn())){
							$listNumRows = mysql_num_rows($listRes);
							if($listNumRows > 0){
								while($listRow = mysql_fetch_assoc($listRes)){
									$productcode = $listRow['productcode'];
									$productname = $listRow['productname'];
									$productmsg=$listRow['prmsg'];
									$prconsumerprice = number_format($listRow['consumerprice']);
									$discountRate=$listRow['discountRate'];
									$vendername=$listRow['com_name'];
									$venderidx=$listRow['vender'];

									$wholeSaleIcon = "";
									if ($listRow['isdiscountprice'] == 1 AND isSeller()) {
										$wholeSaleIcon = '<img src="/images/common/wholeSaleIcon.gif" /> ';
										$listRow['sellprice'] = $listRow['productdisprice'];
									}
									$memberprice = 0;
									$reservation = $listRow['reservation'];

									if(strlen($reservation)>0 && $reservation != "0000-00-00"){
										$msgreservation = "<span class=\"font-orange\">(예약)</span> ";
										$datareservation = $reservation;
									}else{
										$msgreservation = $datareservation = "";
									}

									#####################상품별 회원할인율 적용 시작#######################################
									$discountprices = getProductDiscount($productcode);
									if($discountprices > 0 AND isSeller() != 'Y' ){
										$memberprice = $listRow['sellprice'] - $discountprices;
										$listRow['sellprice'] = $memberprice;
									}
									#####################상품별 회원할인율 적용 끝 #######################################

									$viewPrice="";
									$dicker=new_dickerview($listRow['etctype'],$wholeSaleIcon.number_format($listRow['sellprice'])."원",1);
									if($memberprice > 0) {	// 회원 로그인(및 등급 할인) 일 경우에만 
										$viewPrice.="<img src='/images/common/memsale_icon.gif' /> ";
									}

									if (count($dicker['memOpenData']) > 0) {
										if ($dicker['memOpenData']['type'] == "img") {
											$viewPrice.="<img src='".$dicker['memOpenData']['value']."' />";
										} else {
											$viewPrice.="<span class=''>".$dicker['memOpenData']['value']."</span>";
										}
									} else if (strlen($_data->proption_price) == 0) {
										$viewPrice.=$wholeSaleIcon.number_format($listRow['sellprice'])."원";
									} else {
										if (strlen($listRow['option_price']) == 0) {
											$viewPrice.=$wholeSaleIcon.number_format($listRow['sellprice'])."원";
										} else {
											$viewPrice.=ereg_replace("\[PRICE\]",number_format($listRow['sellprice']),$_data->proption_price);
										}
									}

									//상품평 수
									$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
									$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
									$row_cnt3=mysql_fetch_object($result_cnt3);
									$t_cnt3 = (int)$row_cnt3->t_count;


									if(strlen($listRow[tinyimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$listRow[tinyimage])==true){
										$background_url=$Dir.DataDir."shopimages/product/".urlencode($listRow[tinyimage]);
									}else{
										$background_url=$Dir."images/no_img.gif";
									}

									$prdetail_link="productdetail_tab01.php?productcode=".$productcode.($vidx?"&vidx=".$vidx:"");


									$youtube_url=$listRow['youtube_url'];
									$youtube_prlist=$listRow['youtube_prlist'];
									$youtube_prlist_imgtype=$listRow['youtube_prlist_imgtype'];
									$youtube_prlist_file=$listRow['youtube_prlist_file'];

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
					?>
						<li class="product_item">
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
							<div class="product_info">
								<div class="product_name"><?=$msgreservation?><?=$productname?></div>
								<? if(strlen($productmsg)>0){ ?><div class="product_caption"><?=$productmsg?></div><? } ?>
								<div class="product_price">
									<?
										echo $viewPrice;
										if($listRow['quantity']=="0") echo soldout();
									?>
								</div>
								<? if (!$dicker['memOpen']) { ?>
								<? if($prconsumerprice>0){ ?><div class="product_discount"><?=$prconsumerprice?>원</div><? } ?>
								<? } ?>
								<div class="product_reserve"><span><?=$msgreservation?></span> <?=$datareservation?></div>
								<? if(strlen($vendername)>0){ ?>
									<div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');"><?=$vendername?></a></div>
								<? } ?>
								<? if($listRow['etctype']){ ?><div>
<!--모바일 메인 아이콘-->
<?
	$icoi = strpos(" ".$listRow['etctype'],"ICON=");
	if($icoi>0){
		$icon = substr($listRow['etctype'],strpos($listRow['etctype'],"ICON="));
		$icon = substr($icon,5,strpos($icon,"")-5);
		$num=strlen($icon);
		for($j=0;$j<$num;$j+=2){
			$temp=$icon[$j].$icon[$j+1];
/*
			if($temp=='04'){
				$icon_name="NEW";
			}else if($temp=='13'){
				$icon_name="BEST";
			}else{
				$icon_name="HOT";
			}
*/
			if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
				echo "<img src=\"http://".$_ShopInfo->shopurl.DataDir."shopimages/etc/icon".$temp.".gif\" align=\"absmiddle\" border=\"0\" />";
			} else if(strlen($temp)>0 && !preg_match("/^(U)[1-6]$/",$temp)) {
				echo "<span class='icon".$temp."'></span>";
			}
		}
	}

?>
<!--모바일 아이콘-->
								<?// echo viewproductname('',$listRow['etctype'],'');?>
								
								</div><? } ?>
								<div><span class="product_coment"><?=$t_cnt3?></span></div>
							</div>
						</li>
						<?
								}
							}else{
						?>
							<li>진열된 상품이 없습니다.</li>
						<?
								}
								mysql_free_result($listRes);
							}else{
						?>
							<li>연결이 지연되었습니다 다시 시도해주세요.</li>
						<? } ?>
					</ul>
				</div>

			<? }else{ //회원 로그인되어 있다면... ?>

				<script type="text/javascript">
					function ajaxitemlist(code, listnum, sort, block, gotopage, search_bridx, search_price_s, search_price_e, search_color_idx, searchkey){
						$.ajax({
							type: "GET",
							url: "productlist_ajax.php",
							data: "code="+ code +"&listnum="+listnum+"&sort="+sort+"&block="+block+"&gotopage="+gotopage+"&search_bridx="+search_bridx+"&search_price_s="+search_price_s+"&search_price_e="+search_price_e+"&search_color_idx="+search_color_idx+"&searchkey="+searchkey, 
							cache: false,
							success: function(html)
							{
								$("#isotope_container").append(html);
								var el = $('[class=wrapPage]').toArray();
								for(i=0;i<el.length-1;i++){
									$(el[i]).css('display','none');
								}
							}
						});
					}

					$( document ).ready(function() {
						ajaxitemlist('<?php echo $code?>', '<?php echo $listnum?>', '<?php echo $sort?>', '<?php echo $block?>', '<?php echo $gotopage?>', '<?php echo $search_bridx?>', '<?php echo $search_price_s?>', '<?php echo $search_price_e?>', '<?php echo $search_color_idx?>', '<?php echo $searchkey?>');
					});
				</script>

				<form name="quickbuyform" id="quickbuyform" method="post">
					<input name="otherbasket" type="hidden" value="1">
					<div>
						<ul class="pr_type_list_wrap" id="isotope_container">
						</ul>
					</div>
					<div style="padding:35px 10px;">
						<p style="text-align:center;">합계 : <span style="color:#444444;font-weight:900;font-size:20px;" id='totalprice'>0원</span><p>
						<a href="javascript:allsubmit()" style="display: block;text-align: center;height: 20px;margin: 10px 0px 20px;padding: 20px 0px;background: #e83618;color: #ffffff;font-size: 1.2em;text-decoration: none;font-weight: 800;">일괄 주문하기</a>
					</div>
				</form>

				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

				<!-- 장바구니 바로가기/쇼핑계속하기 레이어 팝업 START -->
				<div id="insertBasket" class="insertBasket" style="display:none;">
					<div class="basketBox">
						<h4>
							<span>장바구니</span> 담기
							<a href="javascript:closeInsBasket();" class="closeBtn">X</a>
						</h4>
						<div class="basketMessage">
							<h5><span>장바구니</span>에 상품이 담겼습니다.</h5>
							지금 확인하시겠습니까?
						</div>
						<div class="basketBtnBox">
							<a href="#" class="goBasketBtn">예</a>
							<a href="javascript:closeInsBasket();" class="closeBasketBtn">아니오</a>
						</div>
					</div>
				</div>
				<!-- 장바구니 바로가기/쇼핑계속하기 레이어 팝업 END -->

				<form name="form1" method="post" action="<?=$Dir.FrontDir?>basket.php" id="submitForm">
					<input type="hidden" name="quantity" />
					<input type="hidden" name="productcode" />
				</form>

				<form name="form9" method="post" action="<?=$Dir.FrontDir?>basket.php">
					<input type="hidden" name="mode" />
					<input type="hidden" name="price" />
					<input type="hidden" name="quantity" />
					<input type="hidden" name="code" />
					<input type="hidden" name="productcode" />
					<input type="hidden" name="ordertype" />
					<input type="hidden" name="opts" />
					<input type="hidden" name="arropts" />
					<input type="hidden" name="sell_memid" />
				</form>

				<!-- 옵션관련 스크립트 시작 -->
				<script type="text/javascript">
					function allsubmit(){
						if($("span[id='totalprice'").text() != "0원"){
							var choose = confirm( '주문 하시겠습니까?');
							if(choose){
								real_form_submit(document.quickbuyform, 2);
							}else{
								return;
							}
						}else{
							alert('주문 상품을 선택하신 후 이용하여 주세요.');
						}
					}

					function closeInsBasket(){
						$("#insertBasket").hide();
					}

					/* 160302 디테일 페이지에서 바로 장바구니 및 바로구매, 위시리스트 담기 테스트 버전 */
					function real_form_submit(form, type) {
						var linkpage="";
						if(type==1) linkpage = "basket_real.php";
						else linkpage = "basket_quick.php";
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

					function OoBasket(num, productcode){
						var form = document.form1;
						var innerText = "";
						if($("#opt_productcode_"+productcode).length){
							form.productcode.value = productcode;
							var oOpt = $('input[name="opt_productcode[]"').length;
							for(i=0;i<oOpt; i++) {
								if($('input[name="opt_quantity[]"')[i].value > 0){
									innerText+= '<input name="opt_price[]" type="hidden" value="'+$("input[name='opt_price[]'")[i].value+'">';
									innerText+= '<input name="opt_comidx[]" type="hidden" value="'+$("input[name='opt_comidx[]'")[i].value+'">';
									innerText+= '<input name="opt_quantity[]" type="hidden" value="'+$("input[name='opt_quantity[]'")[i].value+'">';
								}
							}
							$('#submitForm').append(innerText);
							real_form_submit(form, 1);

							//초기화
							var innerText = "";
							innerText+= '<input type="hidden" name="productcode" />';
							innerText+= '<input type="hidden" name="quantity" />';
							$('#submitForm').append(innerText);
						}else{
							alert("옵션을 선택하여 주세요.");
						}
					}

					function NoBasket(num, productcode){
						var form = document.form9;
						if($("input[name='quantity[]'")[num].value){
							form.productcode.value = productcode;
							form.quantity.value = $("input[name='quantity[]'")[num].value;
							real_form_submit(form);
							//초기화
							form.productcode.value = "";
							form.quantity.value = "";
						}else{
							alert("수량을 입력하여 주세요.");
						}
					}

					function norSepOptAdd(productcode, com_idx, opt_combi_text, opt_price, opt_quantity) {
						var innerText = "",
						optPrice = document.getElementById('itemprice_'+productcode).value.replace(/,/gi,"");
						optPrice = (Number(optPrice)+Number(opt_price));
						innerText = '<tr id="optbox_'+com_idx+'">';
						innerText+= '	<td align="left" width=27><a class="delOpts" href="javascript:remove_optbox(\'optbox_'+com_idx+'\');"></a></td>';
						innerText+= '	<td align="left">'+opt_combi_text;
						if(opt_price > 0) {
							innerText+= '	 +'+commify(opt_price);
						}
						innerText+= '<input name="opt_productcode[]" id="opt_productcode_'+productcode+'" type="hidden" value="'+productcode+'">';
						innerText+= '<input name="opt_price[]" id="opt_price_'+com_idx+'" type="hidden" value="'+optPrice+'">';
						innerText+= '<input name="opt_comidx[]" id="opt_comidx_'+com_idx+'" type="hidden" value="'+com_idx+'">';
						innerText+= '<input name="opt_buycnt[]" id="opt_buycnt_'+com_idx+'" type="hidden" value="'+opt_quantity+'">';
						innerText+= '	</td>';
						innerText+= '	<td align="right" id="opt_price[]">'+commify(optPrice)+'원</td>';
						innerText+= '	</tr><tr id="optbox_'+com_idx+'1">';
						innerText+= '	<td></td>';
						innerText+= '	<td align="left" id="opt_cnt_'+com_idx+'">수량:';
						if(opt_quantity > 7999999){
							innerText+= '	무제한';
						}else{
							innerText+= commify(opt_quantity);
						}
						innerText+= '   </td>';
						innerText+= '	<td align="right">';
						innerText+= '		<table cellpadding="0" cellspacing="0">';
						innerText+= '			<tr>';
						innerText+= '				<td><input type="number" name="opt_quantity[]" onkeyup="pricecheck()" onclick="pricecheck()" value="1" maxlength="4" style="WIDTH:50px;HEIGHT:25px;line-height:25px;border:none;BORDER:#aaaaaa 1px solid;box-sizing:border-box;text-align:center"></td>';
						innerText+= '			</tr>';
						innerText+= '		</table>';
						innerText+= '	</td>';
						innerText+= '</tr>';

						$('#optlist_'+productcode).append(innerText);
						pricecheck();
					}

					function norSepOptChange(productcode, att_idx, end_att_idx, optCnt, onlyMember) {
						var optSelect = document.getElementById(productcode+'option'+(att_idx+1)),
							i = 0,
							firstOpt = end_att_idx-optCnt+1,
							opt_combi = "";

						if(onlyMember==1) {
							alert('회원 전용입니다.\n회원 로그인을 하셔야 합니다.');
							return;
						}

						if(att_idx<end_att_idx){
							//마지막 옵션에 재고수량 출력하는 부분 주석화 2016-10-26 Seul
							/*
							if(att_idx==(end_att_idx-1)) {
								opt_combi = "";
								for(i=firstOpt; i<end_att_idx; i++) {
									opt_combi += document.getElementById('option'+i).value+",";
								}
								norSepOptComInfo(productcode, opt_combi, end_att_idx);
							}
							*/

							if(optSelect.disabled == 1) {
								optSelect.options[0] = null;
								optSelect.disabled = 0;
							} else {
								norSepOptInitialize(productcode, "changed", att_idx, end_att_idx);
							}
						} else {
							opt_combi = "";
							for(i=firstOpt; i<=end_att_idx; i++) {
								if(i>firstOpt) {
									opt_combi += ",";
								}
								opt_combi += document.getElementById(productcode+'option'+i).value;
							}
							norSepOptCheck(productcode, opt_combi, att_idx);
							norSepOptInitialize(productcode, "allSelected", firstOpt, end_att_idx);
						}
					}

					function norSepOptInitialize(productcode, type, start_idx, end_att_idx) {
						var optSelect = "",
							objOption = "",
							init_term = 0;

						if(type=="allSelected") {
							init_term = start_idx+1;
						} else if(type=="changed") {
							init_term = start_idx+2;
						} else {
							alert("초기화 중 오류가 발생했습니다. (타입 오류)");
							return false;
						}

						////////////////////초기화 시작////////////////////
						for(i=start_idx; i<=end_att_idx; i++) {
							optSelect = document.getElementById(productcode+'option'+i);

							if(i>=init_term && optSelect.options[0].value!="checkOpt") {
								objOption = document.createElement("option");        
								objOption.text = "이전 옵션을 선택해주세요";
								objOption.value = "checkOpt";
								
								optSelect.options.add(objOption, 0);
								optSelect.disabled = 1;
							}
							
							if(type=="allSelected") {
								optSelect.options[0].selected = true;
							} else if(type=="changed") {
								if(i!=start_idx) {
									optSelect.options[0].selected = true;
								}
							}
						}
						////////////////////초기화 끝////////////////////
					}

					function norSepOptComInfo(productcode, opt_combi, end_att_idx) {
						var optSelect = document.getElementById(productcode+'option'+end_att_idx),
							i = 0,
							optText = "";

						$.ajax({
							type : "POST",
							url: "../templet/option/opt_combination_info.php",
							data : {
								"type" : "info",
								"opt_combi" : opt_combi,
								"productcode" : productcode
							},
							dataType : "json",
							success : function (rs) {
								if(rs.err) {
									//매칭되는 조합 없음
									alert("예기치않은 오류가 발생하였습니다.");
								} else {
									for(i=0; i<rs.opt_count; i++) {
										optText = lastOptVal[i];
										optText+= "　(";
										if(rs.opt_price[i]>0) {
											optText+= "+";
										}
										optText+= rs.opt_price[i]+"원)";
										optText+= "　재고:"+rs.opt_quantity[i]+"개";
										optSelect.options[i+2].text = optText;
									}
								}
							}
						});
					}

					function norSepOptCheck(productcode, opt_combi, att_idx) {
						var isOverlap = false,
							optMustCnt = document.getElementById('optMustCnt'+att_idx);

						$.ajax({
							type : "POST",
							url: "../templet/option/opt_combination_info.php",
							data : {
								"type" : "check",
								"opt_combi" : opt_combi,
								"productcode" : productcode
							},
							dataType : "json",
							success : function (rs) {
								if(rs.err) {
									//매칭되는 조합 없음
									alert("예기치않은 오류가 발생하였습니다.");
								} else {
									if(rs.opt_quantity==0) {
										alert("해당 옵션은 품절되었습니다.");
									} else {
										$('input[name="opt_comidx[]"]').each(function(index, item) {
											if($(item).val()==rs.com_idx) {
												alert('이미 선택된 옵션입니다.');
												isOverlap = true;
												return false;
											}
										});

										if(!isOverlap) {
											norSepOptAdd(productcode, rs.com_idx, rs.opt_combi_text, rs.opt_price, rs.opt_quantity);
										}
									}
								}
							}
						});
					}

					function commify(n) {
						var reg = /(^[+-]?\d+)(\d{3})/; // 정규식
						n += ''; // 숫자를 문자열로 변환

						while (reg.test(n))
							n = n.replace(reg, '$1' + ',' + '$2');
							return n;
					}

					//체크박스
					function checktoggle(productcode, opt){
						if($("input[id='check_item_"+productcode+"'").is(":checked")==false){
							if(opt==1){
								$('#optlist_'+productcode).html('');
							}else{
								$('#quantity_'+productcode).val('');
							}			
						}else{
							if(opt!=1){
								$('#quantity_'+productcode).val(1);
							}
						}
						pricecheck();
					}

					//금액
					function pricecheck(){
						var form=document.quickbuyform;
						var totalprice=0;
						//옵션없는거
						var noOpt = $("input[name='itemcode[]'").length;
						for(i=0;i<noOpt; i++) {
							var noOptprice=0;

							//비옵션 가격설정
							if($("input[name='quantity[]'")[i].value > 0){
								if(Number($("input[name='quantity[]'")[i].value) > Number($("input[name='itembuycnt[]'")[i].value)){
									alert("재고량이 부족합니다.");
									if($("input[name='itembuycnt[]'")[i].value==0){
										document.getElementById('check_item_'+$("input[name='itemcode[]'")[i].value).checked = false;
										$("input[name='quantity[]'")[i].value = "";
										$("[id='payprice[]'")[i].innerHTML = commify(Number($("input[name='itemprice[]'")[i].value))+"원";
									}else{
										document.getElementById('check_item_'+$("input[name='itemcode[]'")[i].value).checked = true;
										$("input[name='quantity[]'")[i].value = $("input[name='itembuycnt[]'")[i].value;
										noOptprice = Number($("input[name='itembuycnt[]'")[i].value) * Number($("input[name='itemprice[]'")[i].value); 
										$("[id='payprice[]'")[i].innerHTML = commify(noOptprice)+"원";
									}
								}else{
									document.getElementById('check_item_'+$("input[name='itemcode[]'")[i].value).checked = true;
									noOptprice = Number($("input[name='quantity[]'")[i].value) * Number($("input[name='itemprice[]'")[i].value); 
									$("[id='payprice[]'")[i].innerHTML = commify(noOptprice)+"원";
								}
								
								//$("input[id='check_item_"+$("input[name='itemcode[]'")[i].value+"'").attr("checked", true);
							}else{
								document.getElementById('check_item_'+$("input[name='itemcode[]'")[i].value).checked = false;
								//$("input[id='check_item_"+$("input[name='itemcode[]'")[i].value+"'").attr("checked", false);
								$("input[name='quantity[]'")[i].value = '';
								$("[id='payprice[]'")[i].innerHTML = commify($("input[name='itemprice[]'")[i].value)+"원";
							}
							totalprice = totalprice + noOptprice;
							//alert($("input[name='itemcode[]'")[i].value);
						}

						//옵션
						var oOpt = $('input[name="opt_productcode[]"').length;
						for(i=0;i<oOpt; i++) {
							var oOptprice=0;
							if($('input[name="opt_quantity[]"')[i].value > 0){
								if(Number($('input[name="opt_quantity[]"')[i].value) > Number($('input[name="opt_buycnt[]"')[i].value)){
									alert("재고량이 부족합니다.");
									oOptprice = Number($('input[name="opt_price[]"')[i].value) * Number($('input[name="opt_buycnt[]"')[i].value); 
									$('input[name="opt_quantity[]"')[i].value = $('input[name="opt_buycnt[]"')[i].value;
									$('[id="opt_price[]"')[i].innerHTML = commify(oOptprice)+"원";
								}else{
									oOptprice = Number($('input[name="opt_quantity[]"')[i].value) * Number($('input[name="opt_price[]"')[i].value); 
									$('[id="opt_price[]"')[i].innerHTML = commify(oOptprice)+"원";
								}
								document.getElementById('check_item_'+$("input[name='opt_productcode[]'")[i].value).checked = true;
								//$("input[id='check_item_"+$("input[name='opt_productcode[]'")[i].value+"'").attr("checked", true);
							}else{
								remove_optbox('optbox_'+$('input[name="opt_comidx[]"')[i].value);
							}
							totalprice = totalprice +oOptprice;
						}

						//옵션 체크박스확인
						var oOptCh = $('input[name="optitemcode[]"').length;
						for(i=0;i<oOptCh; i++) {
							//비었을때 체크박스 해제
							if($("#optlist_"+$('input[name="optitemcode[]"')[i].value).html().length < 30){
								document.getElementById('check_item_'+$("input[name='optitemcode[]'")[i].value).checked = false;
								//$("input[name='check_item_"+$("input[name='optitemcode[]'")[i].value+"'").attr("checked", false);
							}else{
								document.getElementById('check_item_'+$("input[name='optitemcode[]'")[i].value).checked = true;
								//$("input[name='check_item_"+$("input[name='optitemcode[]'")[i].value+"'").attr("checked", true);
							}
						}
						$("span[id='totalprice'")[0].innerHTML = commify(totalprice)+"원";
					}

					function remove_optbox(obj_name) {
						$('#'+obj_name).remove();
						$('#'+obj_name+'1').remove();
						pricecheck();
					}
				</script>
				<!-- 옵션관련 스크립트 끝 -->
		<?
				}
				break;

				case "list":
		?>
				<div class="product_b">
					<ul class="product_list">
					<?
						$itemcount = 5; // 페이지당 게시글 리스트 수
						$sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;
						if(false !== $listRes = mysql_query($sql,get_db_conn())){
							$listNumRows = mysql_num_rows($listRes);
							if($listNumRows > 0){
								while($listRow = mysql_fetch_assoc($listRes)){
									$productcode   = $listRow['productcode'];
									$productname   = $listRow['productname'];
									$productmsg         = $listRow['prmsg'];
									$consumerprice = number_format($listRow['consumerprice']);
									$discountRate  = $listRow['discountRate'];
									$vendername = $listRow['com_name'];
									$venderidx=$listRow['vender'];

									$wholeSaleIcon = "";
									if ($listRow['isdiscountprice'] == 1 AND isSeller()) {
										$wholeSaleIcon = '<img src="/images/common/wholeSaleIcon.gif" /> ';
										$listRow['sellprice'] = $listRow['productdisprice'];
									}
									$memberprice = 0;
									$reservation = $listRow['reservation'];

									if(strlen($reservation)>0 && $reservation != "0000-00-00"){
										$msgreservation = "<span class=\"font-orange\">(예약)</span> ";
										$datareservation = $reservation;
									}else{
										$msgreservation = $datareservation = "";
									}

									#####################상품별 회원할인율 적용 시작#######################################
									$discountprices = getProductDiscount($productcode);
									if($discountprices > 0 AND isSeller() != 'Y' ){
										$memberprice = $listRow['sellprice'] - $discountprices;
										$listRow['sellprice'] = $memberprice;
									}
									#####################상품별 회원할인율 적용 끝 #######################################

									$viewPrice = "";
									$dicker = new_dickerview($listRow['etctype'],$wholeSaleIcon.number_format($listRow['sellprice'])."원",1);
									if($memberprice > 0) {	// 회원 로그인(및 등급 할인) 일 경우에만 
										$viewPrice .= "<img src='/images/common/memsale_icon.gif' /> ";
									}

									if (count($dicker['memOpenData']) > 0) {
										if ($dicker['memOpenData']['type'] == "img") {
											$viewPrice .= "<img src='".$dicker['memOpenData']['value']."' />";
										} else {
											$viewPrice .= "<span class=''>".$dicker['memOpenData']['value']."</span>";
										}
									} else if (strlen($_data->proption_price) == 0) {
										$viewPrice .= $wholeSaleIcon.number_format($listRow['sellprice'])."원";
									} else {
										if (strlen($listRow['option_price']) == 0) {
											$viewPrice .= $wholeSaleIcon.number_format($listRow['sellprice'])."원";
										} else {
											$viewPrice .= ereg_replace("\[PRICE\]",number_format($listRow['sellprice']),$_data->proption_price);
										}
									}

									//상품평 수
									$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
									$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
									$row_cnt3=mysql_fetch_object($result_cnt3);
									$t_cnt3 = (int)$row_cnt3->t_count;


									if(strlen($listRow[tinyimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$listRow[tinyimage])==true){
										$background_url=$Dir.DataDir."shopimages/product/".urlencode($listRow[tinyimage]);
									}else{
										$background_url=$Dir."images/no_img.gif";
									}

									$prdetail_link="productdetail_tab01.php?productcode=".$productcode.($vidx?"&vidx=".$vidx:"");


									$youtube_url=$listRow['youtube_url'];
									$youtube_prlist=$listRow['youtube_prlist'];
									$youtube_prlist_imgtype=$listRow['youtube_prlist_imgtype'];
									$youtube_prlist_file=$listRow['youtube_prlist_file'];

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
					?>
						<li class="product_item">
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
							<div class="product_info">
								<div class="product_name"><a href="productdetail_tab01.php?productcode=<?=$listRow['productcode']?><?=$add_query?>&sort=<?=$sort?>" rel="external"><?=$msgreservation?><?=$productname?></a></div>
								<? if(strlen($productmsg)>0){ ?><div class="product_caption"><?=$productmsg?></div><? } ?>
								<div class="product_price">
									<?
										echo $viewPrice;
										if ($listRow['quantity']=="0") echo soldout();
									?>
								</div>
								<? if($consumerprice>0){ ?><div class="product_discount"><?=$consumerprice?>원</div><? } ?>
								<? if(strlen($reservation)>0 && $reservation != "0000-00-00"){ ?>
								<div class="product_reserve"><span><?=$msgreservation?></span> <?=$datareservation?></div>
								<? } ?>
								<? if(strlen($vendername)>0){ ?>
									<div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');"><?=$vendername?></a></div>
								<? } ?>
								<? if($listRow['etctype']){ ?><div>
<!--모바일 메인 아이콘-->
<?
	$icoi = strpos(" ".$listRow['etctype'],"ICON=");
	if($icoi>0){
		$icon = substr($listRow['etctype'],strpos($listRow['etctype'],"ICON="));
		$icon = substr($icon,5,strpos($icon,"")-5);
		$num=strlen($icon);
		for($j=0;$j<$num;$j+=2){
			$temp=$icon[$j].$icon[$j+1];
/*
			if($temp=='04'){
				$icon_name="NEW";
			}else if($temp=='13'){
				$icon_name="BEST";
			}else{
				$icon_name="HOT";
			}
*/
			if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
				echo "<img src=\"http://".$_ShopInfo->shopurl.DataDir."shopimages/etc/icon".$temp.".gif\" align=\"absmiddle\" border=\"0\" />";
			} else if(strlen($temp)>0 && !preg_match("/^(U)[1-6]$/",$temp)) {
				echo "<span class='icon".$temp."'></span>";
			}
		}
	}

?>
<!--모바일 아이콘-->
								<?// echo viewproductname('',$listRow['etctype'],'');?>
								
								</div><? } ?>
							</div>

							<div class="product_communicate">
								<ul>
									<li>
										<a href="#"><span class="product_coment"><?=$t_cnt3?></span></a>
									</li>
								</ul>
							</div>
						</li>
					<?
								}
							}else{
					?>
						<li>진열된 상품이 없습니다.</li>
					<?
							}
							mysql_free_result($listRes);
						}else{
					?>
						<li>연결이 지연되었습니다 다시 시도해주세요.</li>
					<? } ?>
					</ul>
				</div>
		<?
				break;
			}
		?>

		<? if($displaymode != "webzine"){ ?>
			<div class="product_page" id="page_wrap">
			<?
				if($vidx){
					$pageLink=$_SERVER['PHP_SELF']."?vidx=".$vidx."&code=".$code."&sort=".$sort."&search_bridx=".$search_bridx."&search_price_s=".$search_price_s."&search_price_e=".$search_price_e."&search_color_idx=".$search_color_idx."&searchkey=".$searchkey."&list_type=".$displaymode."&page=%u";
				}else{
					$pageLink=$_SERVER['PHP_SELF']."?code=".$code."&sort=".$sort."&search_bridx=".$search_bridx."&search_price_s=".$search_price_s."&search_price_e=".$search_price_e."&search_color_idx=".$search_color_idx."&searchkey=".$searchkey."&list_type=".$displaymode."&page=%u";
				}

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
</script>
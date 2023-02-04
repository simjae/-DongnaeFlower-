<script>
function openDetail()
{
	//alert();
	window.open("productdetail_tab02_popup.php?productcode=<?=$_GET[productcode]?>","","");
}
</script>
<?
//상품평 수
$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$_GET[productcode]'";
$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
$row_cnt3=mysql_fetch_object($result_cnt3);
$t_cnt3 = (int)$row_cnt3->t_count;

//상품문의 수
$pridx=$_pdata->pridx;
$sql_cnt4 = "SELECT COUNT(*) as t_count FROM tblboard WHERE board='$prqnaboard' and pridx = '$pridx'";
$result_cnt4=mysql_query($sql_cnt4,get_db_conn());
$row_cnt4=mysql_fetch_object($result_cnt4);
$t_cnt4 = (int)$row_cnt4->t_count;
?>

		<section class="tab_area"> 
			<ul class="tab_type1 tab01">
				<li><a href="productdetail_tab01.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">기본정보</a></li>
				<!-- <li class="active"><a href="productdetail_tab02.php?productcode=<?=$productcode?>&sort=<?=$sort?>" rel="external">상세정보</a></li> -->
				<li><a href="productdetail_tab03.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">상품평(<?=$t_cnt3?>)</a></li>
				<li><a href="productdetail_tab04.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">상품문의(<?=$t_cnt4?>)</a></li>
			</ul>
		</section>
		<!-- //view탭 -->
		
<!-- ////////////////////////////////////////// -->
		<!-- <div><a href="javascript:openDetail()" rel="external">새창으로 보기</a></div> -->
<!-- ////////////////////////////////////////// -->
		<!-- TAB2-상세정보 -->
		<section class="detail_02">
			<?
				if(strlen($detail_filter)>0) {
					$_pdata->content = preg_replace($filterpattern,$filterreplace,$_pdata->content);
				}

				if (strpos($_pdata->content,"table>")!=false || strpos($_pdata->content,"TABLE>")!=false)
					echo "<pre>".$_pdata->content."</pre>";
				else if(strpos($_pdata->content,"</")!=false)
					echo ereg_replace("\n","<br>",$_pdata->content);
				else if(strpos($_pdata->content,"img")!=false || strpos($_pdata->content,"IMG")!=false)
					echo ereg_replace("\n","<br>",$_pdata->content);
				else
					echo ereg_replace(" ","&nbsp;",ereg_replace("\n","<br>",$_pdata->content));
			?>
		</section>

		<section>
			<div class="information">
				<div style="float:left; width:70%; margin:4px 5px;">상품 정보고시와 배송/AS/환불 안내 규정은 PC버전에서 확인할 수 있습니다.</div>
				<div style="float:right; margin:7px 5px;"><a href="../front/productdetail.php?productcode=<?=$_pdata->productcode?>#2" class="button white small">확인하기</a><!--<button class="btn_information">확인하러 가기</button>--></div>
			</div>
		</section>
		<!--
		<script>
			jQuery(function($){
				$(".btn_information").click(function(){
					document.location.href="../front/productdetail.php?productcode=<?=$_pdata->productcode?>"
					
				});
			});
		</script>
		-->
		<!-- //TAB2-상세정보 -->
		
		<!-- 버튼 -->
		<!-- <section class="basic_btn_area btn_w1">
			<button type="button" class="basic_btn c1"><span>바로구매</span></button>
			<button type="button" class="basic_btn"><span>장바구니</span></button>
			<button type="button" class="basic_btn"><span>위시리스트</span></button>
		</section> -->
		<!-- //버튼 -->

	</div>
	<!-- //상품 DETAIL -->
</div>


<? 
//include_once('footer.php'); 
?>
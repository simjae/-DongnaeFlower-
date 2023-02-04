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
				<li class="active"><a href="productdetail_tab03.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">상품평(<?=$t_cnt3?>)</a></li>
				<li><a href="productdetail_tab04.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">상품문의(<?=$t_cnt4?>)</a></li>
			</ul>
		</section>
		<!-- //view탭 -->
	
		<!-- TAB3-상품평 -->
		<section class="detail_03_view">
			<? include "prreview_view.php"; ?>
		</section>
		<!-- //TAB3-상품평 -->
	</div>
	<!-- //상품 DETAIL -->
</div>

<hr>
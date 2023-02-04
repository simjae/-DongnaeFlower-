<!DOCTYPE HTML>
<html>
<head>
<!-- 쇼핑몰태그 -->
<meta name="description" content="">
<meta name="keywords" content="">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<!-- 바로가기 아이콘 -->
<link rel="apple-touch-icon-precomposed" href="" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<title><?=$row_shop[shopname]?> 쇼핑몰 - 모바일</title>
<link rel="stylesheet" href="../../css/common.css" />
<link rel="stylesheet" href="../../css/skin/default.css" />
<link rel="stylesheet" href="../../css/user.css" />

</head>


<div id="content">
	
	
	<!-- 상품 DETAIL -->
	<div class="pr_detail">
		
				
		
<!-- view탭 -->


		
		<!-- TAB2-상세정보 -->
		<section class="detail_02">
			<!-- <a href="#" target="_blank"><img src="img/@detail_sample01.png"></a>
			<a href="#" target="_blank"><img src="img/@detail_sample02.png"></a>	 -->
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
		<!-- //TAB2-상세정보 -->
	</div>
	<!-- //상품 DETAIL -->
</div>

<hr>

</body>
</html>
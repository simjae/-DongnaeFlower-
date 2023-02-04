<?
	include_once('header.php');

	//회사소개
	if($crow=mysql_fetch_object(mysql_query("select * from tbldesign"))){
	}else{
		$crow->brandstorytype="C";
	}
?>
<div id="content">
	<div class="h_area2">
		<h2>Brand Story</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<!-- 브랜드 스토리 -->
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<?
			if ($crow->brandstorytype=="A") {
				if (strlen($crow->brandstoryimage)>0 && $crow->brandstoryalign=="top" || strlen($crow->brandstoryimage)>0 && $crow->brandstoryalign=="left") {
					echo "<tr>\n";
					echo "	<td align=\"center\" style=\"padding-bottom:20px;font-size:0px;line-height:0%;\"><img src=\"".$Dir.DataDir."shopimages/etc/".$crow->brandstoryimage."\" border=\"0\" style=\"max-width:100%;\" /></td>\n";
					echo "</tr>\n";
				}

				echo "<tr>\n";
				echo "	<td style=\"padding:30px 20px;text-align:center;\">".$crow->brandstory."</td>\n";
				echo "</tr>\n";

				if (strlen($crow->brandstoryimage)>0 && $crow->brandstoryalign=="bottom" || strlen($crow->brandstoryimage)>0 && $crow->brandstoryalign=="right") {
					echo "<tr>\n";
					echo "	<td align=\"center\" style=\"font-size:0px;line-height:0%;\"><img src=\"".$Dir.DataDir."shopimages/etc/".$crow->brandstoryimage."\" border=\"0\" style=\"max-width:100%;\" /></td>\n";
					echo "</tr>\n";
				}

			} else if ($crow->brandstorytype=="B") {
				echo "<tr>\n";
				echo "	<td style=\"padding:20px;\">".$crow->brandstory."</td>\n";
				echo "</tr>\n";
			}

			if(!$crow->brandstory){
				echo "<tr><td style=\"padding:20px;\">Brand Story를 등록해 주세요.</td></tr>";
			}
		?>
	</table>
	<!-- //회사소개 -->

</div>

<? include_once('footer.php'); ?>
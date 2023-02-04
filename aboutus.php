<?
	include_once('header.php');

	//about us
	if($crow=mysql_fetch_object(mysql_query("select * from tbldesign"))){
	}else{
		$crow->aboutustype="C";
	}
?>
<div id="content">
	<div class="h_area2">
		<h2>About Us</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<!-- 브랜드 스토리 -->
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<?
			if ($crow->aboutustype=="A") {
				if (strlen($crow->aboutusimage)>0 && $crow->aboutusalign=="top" || strlen($crow->aboutusimage)>0 && $crow->aboutusalign=="left") {
					echo "<tr>\n";
					echo "	<td align=\"center\" style=\"padding-bottom:20px;font-size:0px;line-height:0%;\"><img src=\"".$Dir.DataDir."shopimages/etc/".$crow->aboutusimage."\" border=\"0\" style=\"max-width:100%;\" /></td>\n";
					echo "</tr>\n";
				}

				echo "<tr>\n";
				echo "	<td style=\"padding:30px 20px;text-align:center;\">".$crow->aboutus."</td>\n";
				echo "</tr>\n";

				if (strlen($crow->aboutusimage)>0 && $crow->aboutusalign=="bottom" || strlen($crow->aboutusimage)>0 && $crow->aboutusalign=="right") {
					echo "<tr>\n";
					echo "	<td align=\"center\" style=\"font-size:0px;line-height:0%;\"><img src=\"".$Dir.DataDir."shopimages/etc/".$crow->aboutusimage."\" border=\"0\" style=\"max-width:100%;\" /></td>\n";
					echo "</tr>\n";
				}

			} else if ($crow->aboutustype=="B") {
				echo "<tr>\n";
				echo "	<td style=\"padding:20px;\">".$crow->aboutus."</td>\n";
				echo "</tr>\n";
			}

			if(!$crow->aboutus){
				echo "<tr><td style=\"padding:20px;\">About Us를 등록해 주세요.</td></tr>";
			}
		?>
	</table>
	<!-- //회사소개 -->

</div>

<? include_once('footer.php'); ?>
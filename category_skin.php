<?
	$exceptSql = "AND type not like 'X%' AND type not like 'S%' AND group_code!='NO' AND mobile_display != 'N' ORDER BY sequence DESC";
?>

<div id="content">
	<div class="h_area2">
		<h2>카테고리</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<!-- 카테고리 리스트 -->
	<div class="category_list">
		<ul>
		<?
			$categorySql = "SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode WHERE codeA != '000' AND codeB = '000' AND codeC = '000' AND codeD ='000'".$exceptSql;
			if(false !== $categoryRes = mysql_query($categorySql,get_db_conn())){
				$ci = 0;
				$prcode="";
				while($categoryRow = mysql_fetch_assoc($categoryRes)){
					$prcode = $categoryRow['codeA'].$categoryRow['codeB'].$categoryRow['codeC'].$categoryRow['codeD'];
		?>
				<li>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="category_list_table">
						<tr>
							<th><a href="#" onClick="_toggle('<?=$ci?>');"><?=$categoryRow['code_name']?></a></th>
							<td align="right">
								<?
									$categorySubSql = "SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode WHERE codeA = '".$categoryRow['codeA']."' AND codeB != '000' AND codeC = '000' AND codeD ='000'".$exceptSql;
									if(false !== $categorySubRes = mysql_query($categorySubSql,get_db_conn())){
										$categorySubNum = mysql_num_rows($categorySubRes);
										if($categorySubNum > 0){
								?>
										<a href="#" id="btn_plus_<?=$ci?>" onClick="openSubCate('<?=$ci?>');" class="button white smallTH"><b>+</b></a><a href="#" id="btn_minus_<?=$ci?>" onClick="closeSubCate('<?=$ci?>');" class="button white smallTH" style="display:none;"><b>-</b></a>
								<?
										}
								?>
								<a href="./productlist.php?code=<?=$prcode?>" class="button white small">상품보기</a>
								<?
									}
								?>
							</td>
						</tr>
					</table>

					<?if($categorySubNum > 0){?>
						<div id="subCatelist_<?=$ci?>" class="category_list_se">
							<ul>
								<?
									$sprcode= "";
									while($categorySubRow = mysql_fetch_assoc($categorySubRes)){
										$sprcode = $categorySubRow['codeA'].$categorySubRow['codeB'].$categorySubRow['codeC'].$categorySubRow['codeD'];
								?>
									<li><a style="display:block;" href="./productlist.php?code=<?=$sprcode?>">- <?=$categorySubRow['code_name']?></a></li>
								<?}?>
							</ul>
						</div>
					<?}?>
				</li>

		<?
				$ci++;
				}

			}
		?>
		</ul>
	</div>
	<!-- //카테고리 리스트 -->
	
	<!-- 자주가는 서비스 -->
	<ul class="svc_list">

<?
	$query_t = "SELECT * FROM tblmobiledirectmenu ORDER BY date DESC";
	$result_t = mysql_query($query_t,get_db_conn());
	while($row_t=mysql_fetch_array($result_t))
	{

		
		?>
		<li><a href="http://<?=$row_t[url]?>" rel="external"><div class="icon_area"><img src="<?=$configPATH.$row_t[image]?>" class="img_large"></div><div class="txt_area"><?=$row_t[title]?></div></a></li>
		<?	
	}
?>

	</ul>
	<!-- //자주가는 서비스 -->
</div>

<hr>

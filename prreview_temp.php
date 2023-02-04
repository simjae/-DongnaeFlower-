<table cellpadding="0" cellspacing="0" border="1" width="100%">
			<caption>포토 상품평 <a href="./prreview_list.php?prcode=<?=$productcode?>&mode=photo">더보기</a></caption>
		<?

			$photoReviewSQL = "SELECT * FROM tblproductreview ".$qry;
			$photoReviewSQL .= " AND img is not null AND img != ''";
			$photoReviewSQL .= " ORDER BY date DESC LIMIT 3";
			
			if(false !== $photoReviewRes = mysql_query($photoReviewSQL, get_db_conn())){
				$photoReviewNum = mysql_num_rows($photoReviewRes);
				
				if($photoReviewNum > 0){
					while($photoReviewRow = mysql_fetch_assoc($photoReviewRes)){
						$imgsrc = $imageLocation.$photoReviewRow['img'];
						$date=substr($photoReviewRow['date'],0,4)."-".substr($photoReviewRow['date'],4,2)."-".substr($photoReviewRow['date'],6,2);
						$content=explode("=",$photoReviewRow['content']);

						switch($photoReviewRow['device']){
							case 'M':
								$_device = "M";
							break;
							default:
								$_device = "P";
							break;
						}
		?>
			<tr>
				<td width="80">
					<img src="<?=$imgsrc?>" <?=_getImageRateSize($imgsrc,80)?>/>
				</td>
				<td>
					<a class="review_list_link" href="productdetail_tab03_view.php?productcode=<?=$productcode?>&sort=<?=$sort?>&num=<?=$photoReviewRow['num']?>" rel="external">
						<p class="view_line"><?=$_device?> <b><?=titleCut(50,$content[0])?></b></p>
						<p class="view_line"><span class="review_writer"><?=$photoReviewRow['name']?> / <?=$date?></span></p>
					</a>
				</td>
			</tr>
		<?
					}
				mysql_free_result($photoReviewRes);
				
				}else{
		?>
			<tr>
				<td>
					등록된 상품평이 없습니다.
				</td>
			</tr>
		<?

				}	
			}else{
		?>
			<tr>
				<td>
					네트워크 오류가 발생하였습니다.<br/>
					잠시후 다시 시도해 주세요.
				</td>
			</tr>
		<?
				
			}
		?>
		</table>
		<table cellpadding="0" cellspacing="0" border="1" width="100%">
			<caption>일반 상품평 <a href="./prreview_list.php?prcode=<?=$productcode?>&mode=basic">더보기</a></caption>
		<?

			$basicReviewSQL = "SELECT * FROM tblproductreview ".$qry;
			$basicReviewSQL .= " AND (img = '' OR img is null)";
			$basicReviewSQL .= " ORDER BY date DESC LIMIT 3";

			if(false !== $basicReviewRes = mysql_query($basicReviewSQL, get_db_conn())){
				$basicReviewNum = mysql_num_rows($basicReviewRes);
				
				if($basicReviewNum > 0){
					while($basicReviewRow = mysql_fetch_assoc($basicReviewRes)){
						$imgsrc = $imageLocation.$basicReviewRow['img'];
						$date=substr($basicReviewRow['date'],0,4)."-".substr($basicReviewRow['date'],4,2)."-".substr($basicReviewRow['date'],6,2);
						$content=explode("=",$basicReviewRow['content']);

						switch($basicReviewRow['device']){
							case 'M':
								$_device = "M";
							break;
							default:
								$_device = "P";
							break;
						}
		?>
			<tr>
				<td width="80">
					<span class="star_wrap">
						<?
							for($i=0;$i<$basicReviewRow['marks'];$i++){
								echo "<span class=\"on\"></span>";
							}
						?>
						</span>
				</td>
				<td>
					<a class="review_list_link" href="productdetail_tab03_view.php?productcode=<?=$productcode?>&sort=<?=$sort?>&num=<?=$basicReviewRow['num']?>" rel="external">
						<p class="view_line"><?=$_device?> <b><?=titleCut(50,$content[0])?></b></p>
						<p class="view_line"><span class="review_writer"><?=$basicReviewRow['name']?> / <?=$date?></span></p>
					</a>
				</td>
			</tr>
		<?
					}
				mysql_free_result($basicReviewRes);
				
				}else{
		?>
			<tr>
				<td>
					등록된 상품평이 없습니다.
				</td>
			</tr>
		<?

				}	
			}else{
		?>
			<tr>
				<td>
					네트워크 오류가 발생하였습니다.<br/>
					잠시후 다시 시도해 주세요.
				</td>
			</tr>
		<?
				
			}
		?>
		</table>
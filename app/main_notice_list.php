<!-- 공지사항 -->
<div id="main_notice">
	<div class="wrapper">
	<h1><a href="board_list.php?board=notice" rel="external">NOTICE</a></h1>
		<ul>
		<?
			$noticelistSQL = "SELECT writetime, title, num, thread, writetime FROM tblboard WHERE board='notice' ORDER BY writetime DESC LIMIT 5 ";

			if(false !== $noticelistRes = mysql_query($noticelistSQL,get_db_conn())){
				$noticelistrowcount = mysql_num_rows($noticelistRes);
				if($noticelistrowcount>0){
					while($noticelistRow = mysql_fetch_assoc($noticelistRes)){
						$writetime = date('Y.m.d',$noticelistRow['writetime']);
						$subject = _strCut($noticelistRow['title'],24,6,$charset);
						$num = $noticelistRow['num'];
						$thread = $noticelistRow['thread'];
		?>
			<li>
				<!--<a href="javascript:noticeView('<?=$num?>','<?=$thread?>','notice');">-->
				<div><a href="board_view.php?num=<?=$num?>&board=notice"><?=$subject?></a></div>
				<div><?=$writetime?></div>
			</li>
		<?
					}
				}else{
		?>
			<li class="title"><div style="width:100%;text-align:center">등록된 공지사항이 없습니다.</div></li>
		<?
				}
				mysql_free_result($noticelistRes);
			}
		?>
		</ul>
	</div>
</div>
<!-- //공지사항 -->
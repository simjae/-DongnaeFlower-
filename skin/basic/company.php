<? include_once('header.php'); ?>
<div id="content">
	<div class="h_area2">
		<h2>회사 소개</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<!-- 회사소개 -->
	<div class="company">
		<? if($crow[introtype]=="A"){ //회사개요, 회사연혁, 소비자 센터 ?>

			<?=$crow->content?>

			<div class="title1">회사개요</div>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>· 회사명</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[companyname])>0) ? $crow[companyname]:"-")?></td>
				</tr>
				<tr>
					<td >· 상점명</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[shopname])>0) ? $crow[shopname]:"-")?></td>
				</tr>
				<tr>
					<td>· 대표이사</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[ownername])>0) ? $crow[ownername]:"-")?></td>
				</tr>
				<tr>
					<td>· 이메일</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[owneremail])>0) ? $crow[owneremail]:"-")?></td>
				</tr>
			</table>

			<div class="title">회사연혁</div>
			<?=$crow[history]?>

			<div class="title">소비자 센터</div>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>· 전화번호</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[info_tel])>0) ? $crow[info_tel]:"-")?></td>
				</tr>
				<tr>
					<td>· 팩스</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[info_fax])>0) ? $crow[info_fax]:"-")?></td>
				</tr>
				<tr>
					<td>· 상담시간</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[info_counsel])>0) ? $crow[info_counsel]:"-")?></td>
				</tr>
				<tr>
					<td>· 이메일</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[info_email])>0) ? $crow[info_email]:"-")?></A></td>
				</tr>
				<tr>
					<td>· 정보담당</td>
					<td>&nbsp;:&nbsp;</td>
					<td><?=((strlen($crow[privercyname])>0) ? $crow[privercyname]:"-")?> / <?=((strlen($crow[privercyemail])>0) ? $crow[privercyemail]:"-")?></td>
				</tr>
			</table>

		<? }else if($crow[introtype]=="B"){ ?>

		<? }else if($crow[introtype]=="C"){ ?>
			
		<? } ?>
		<!-- 관리자에서 입력한 내용 들어가게... -->
		<p class="companyContents"><?=$crow[content]?></p>
	</div>
	<!-- //회사소개 -->

</div>

<hr>

<? include_once('footer.php'); ?>
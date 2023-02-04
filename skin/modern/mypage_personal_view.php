<? 
//include_once('header.php'); 
?>

<div id="content">
	<div class="h_area2">
		<h2>1:1 문의</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<!-- 1:1문의내역 -->
	<div class="mtom">
		<!--<h2>빠른시간 내에 답변드리도록 하겠습니다.<br>궁금한 사항은 언제든지 문의주세요.</h2>-->
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mtomView">
			<tr>
				<th>문의제목</th>
				<td>
				<?=$_pdata->subject?>
				<div class="state">
				<span class="point5">
				<?
					if(strlen($row->re_date)==14) {
						echo "답변완료";
					} else {
						echo "답변대기중";
					}
				?>
				</span>
				<em> / <?=$date = substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);?></em>
				</div>
				</td>
			</tr>
		</table>

		<div class="mtomQnA">
			<div class="mtomQ">
				<div><?=$_pdata->subject?><br /><?=nl2br($_pdata->content)?></div>
			</div>
			<div class="mtomA">
				<span class="black small">답변내용</span>
				<div class="mtomAcontent"><?=nl2br($_pdata->re_content)?></div>
			</div>
		</div>

		<div class="mtomButton">
			<a href="mypage_personal_list.php" rel="external" class="basic_button"><span>목록보기</span></a>
		</div>
	</div>
	<!-- //1:1문의내역 -->

</div>

<hr>

<? 
//include_once('footer.php'); 
?>
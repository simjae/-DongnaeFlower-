<?
	
	$cateCode = $_REQUEST['code'];
	$categoryList = _getSubCateName($cateCode,0,4);
	
	//print_r($categoryList);
?>
<?
	if($categoryList['state'] != 'null'){
?>
<div id="catelistwrap" style="display:block">
	<ul id="catelist">
		<?	
			if($categoryList['state'] == 'list'){

				foreach($categoryList['list'] as $key){
					$tempName = explode("|",$key);
		?>
				<li><a href="./productlist.php?code=<?=$tempName[1]?>"><?=$tempName[0]?></a></li>
		<?
				}
			}
		?>
	</ul>
</div>
	<div id="btn_box_p" class="catebtn_box">
		<a href="javascript:cateAjax('p','<?=$cateCode?>');" id="more" class="btn_box_p">▼더보기</a>
		<a href="javascript:cateAjax('m','<?=$cateCode?>');" class="btn_box_m" style="display:none">▲닫기</a>
		<a href="javascript:cateAll();" id="allList">접기</a>
	</div>
<?}?>
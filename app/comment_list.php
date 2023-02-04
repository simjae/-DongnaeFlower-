<div class="loopReview">
	<div style="padding:5px 10px;background:#f9f9f9;overflow:hidden">
		<p style="float:left;height:20px;line-height:20px;font-weight:bold"><?=$c_name?><?=$c_id?></p>
		<p style="float:right">
			<?=$c_writetime?>
			<? if( $setup["onlyCmt"] == "N" OR strlen($_ShopInfo->id) > 0 ){ ?>&nbsp;<input type="button" class="basic_button" value="삭제" onclick="javascript:comment_delete('<?=$view_num?>','<?=$c_num?>','<?=$actionurl?>')"><? } ?>
		</p>
	</div>

	<div style="padding:5px 10px;overflow:hidden">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<? if($c_comment_file) { ?>
				<td style="width:90px;"><a href="javascript:zoomImage('<?=$filesname?>');"><?=$c_comment_file?></a></td>
				<? } ?>
				<td valign="top"><?=$c_comment?></td>
			</tr>
			<tr>
				<td <?=(strlen($c_comment_file)>0 ? "colspan='2'" : "")?>><?=$adminComment?></td>
			</tr>
		</table>
	</div>
</div>
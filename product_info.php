<?
	$img = "../data/shopimages/product/".$_pdata->minimage;
	$size = _getImageSize($img);
	$_width = $size[width];
	$_height = $size[height];

	if($_width >= $_height){
		$img_size="width=80";
	}else{
		$img_size="height=80";
	}
?>
<div class="detail_product_wrap">
	<table cellpadding="0" cellspacing="0" border="0" class="detail_product_table">
		<tr>
			<td class="detail_img_wrap"><a href="./productdetail_tab01.php?productcode=<?=$_pdata->productcode?>"><img <?=$img_size?> src="<?=$img?>" /></a></td>
			<td class="detail_pr_info">
				<a href="./productdetail_tab01.php?productcode=<?=$_pdata->productcode?>"><b><?=$_pdata->productname?></b></a><br/>
				판매가 : <span class="sellprice"><?=number_format($_pdata->sellprice);?>원</span><br />
				<?if($_pdata->consumerprice > 0){?>
					시중가 : <strike><?=number_format($_pdata->consumerprice);?>원</strike>
				<?}?>
			</td>
		</tr>
	</table>
</div>
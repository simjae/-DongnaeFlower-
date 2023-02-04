<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	header("Content-Type: text/html; charset=UTF-8");


	$sql = "SELECT * FROM tblproductreview WHERE productcode='".$_POST["productcode"]."' AND num = '".$_POST["num"]."'";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$j);
		$date=substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);
		$content=explode("=",$row->content);
?>

			<div class="view_type1">
				<div class="title_wrap">
					<span class="star_wrap">
						<?
						for($i=0;$i<$row->marks;$i++) {
							echo "<span class=\"on\"></span>";
						}
						?>
					</span>
					<em><?=$row->name?></em> / <em><?=$date?></em>
					<strong><?=nl2br($content[0])?></strong>
					<?if(strlen($content[1])){?>
					<strong style="border-top:1px solid #727272; padding-top:15px;"><img src="<?=$Dir?>m/images/review_replyicn.png"><?=nl2br($content[1])?></strong>
					<?}?>
				</div>
			</div>
<?
	mysql_free_result($result);
?>
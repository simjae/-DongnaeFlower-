<?
include_once('header.php');
include_once($Dir."app/inc/paging_inc.php");

$mid=$_REQUEST["mid"];
$sort=$_REQUEST["sort"];

if(!$mid){
	echo "<html><head></head><body onload=\"alert('해당 MD가 존재하지 않습니다.');history.go(-1);\"></body></html>";exit;
}

$imagepath=$Dir.DataDir."shopimages/etc/";

//MD 정보 확인
$mdsql="SELECT * from tblmdpick WHERE md_id='".$mid."' ";
$mdresult=mysql_query($mdsql,get_db_conn());
$mdrow=mysql_fetch_object($mdresult);
?>

<style>
	.mdPickTitle{padding:50px 20px;box-sizing:border-box;text-align:center;vertical-align:middle;}
	.mdPickTitle h4{margin-bottom:10px;color:#fff;font-size:1.7em;text-shadow:1px 1px 1px rgba(0,0,0,0.4);}
	.mdPickTitle p{color:#fff;text-shadow:1px 1px 1px rgba(0,0,0,0.4);}
	.mdPickList{padding:15px;box-sizing:border-box;}
	.mdPickList .list{margin-top:10px;}
	.mdPickList .list li{margin-bottom:10px;}
	.mdPickList .loopContents{position:relative;border:1px solid #eee;box-sizing:border-box;}
</style>

<div class="mdPickProduct">
	<div class="mdPickTitle" style="background:#eee url('<?=$imagepath.$mdrow->main_image?>') no-repeat;background-size:cover;background-position:center;">
		<h4>MD <?=$mdrow->md_nickname?></h4>
		<p><?=$mdrow->md_greeting?></p>
	</div>

	<div class="mdPickList">
		<span class="basic_select">
			<select onChange="ChangeSort(this.value)">
				<option value="regdate" <?if($_GET[sort]=="regdate") {echo "selected";}?>>최근등록순</option>
				<option value="price" <?if($_GET[sort]=="price") {echo "selected";}?>>낮은가격순</option>
				<option value="sellcount" <?if($_GET[sort]=="sellcount") {echo "selected";}?>>판매량순</option>
			</select>

		</span>

		<ul class="list">
		<?
			$currentPage=$_REQUEST["page"];
			if(!$currentPage){
				$currentPage=1;
			}

			$imagepath = $Dir.DataDir."shopimages/etc/";
			$itemcount = 4; // 페이지당 게시글 리스트 수

			$rcsql="SELECT a.idx, a.md_id, a.productcode, a.mdpick_banner, a.mdpick_title, a.mdpick_regdate, b.sellprice, b.sellcount FROM tblmdpick_prinfo AS a LEFT OUTER JOIN tblproduct b ON a.productcode=b.productcode WHERE a.md_id='".$mid."' ";
			$rcresult=mysql_query($rcsql,get_db_conn());
			$rowcount=mysql_num_rows($rcresult);

			$sql="SELECT a.idx, a.md_id, a.productcode, a.mdpick_banner, a.mdpick_title, a.mdpick_regdate, b.sellprice, b.sellcount, b.display, b.tinyimage, b.productname, b.group_check, c.group_code ";
			$sql.="FROM tblmdpick_prinfo AS a LEFT OUTER JOIN tblproduct b ON a.productcode=b.productcode ";
			$sql.="LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
			$sql.="WHERE a.md_id='".$mid."' AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";

			if($sort=="price"){
				$sql.="ORDER BY b.sellprice ";
			}else if($sort=="sellcount"){
				$sql.="ORDER BY b.sellcount DESC ";
			}else if($sort=="regdate"){
				$sql.="ORDER BY a.mdpick_regdate DESC ";
			}else{
				$sql.="";
			}

			$sql.="LIMIT ".($itemcount * ($currentPage - 1)).", ".$itemcount;
			$result=mysql_query($sql,get_db_conn());

			$i=0;
			while($row=mysql_fetch_object($result)){

				if(($i+2)%5==0 && $i < 5){
					$bgcolor="#e9f3f5";
				}else if(($i+2)%4==0 && $i < 4){
					$bgcolor="#feeecc";
				}else if(($i+2)%3==0 && $i < 3){
					$bgcolor="#e3ecd9";
				}else if(($i+2)%2==0 && $i < 2){
					$bgcolor="#e9f3f5";
				}

				echo "<li><!-- loop contents -->";
		?>
				<a href="productdetail_tab01.php?productcode=<?=$row->productcode?>&mid=<?=$row->md_id?>" rel="external">
				<div class="loopContents">
					<div style="position:absolute;top:0px;left:0px;width:100%;height:100%;background:<?=$bgcolor?> url('<?=($row->mdpick_banner?$imagepath.$row->mdpick_banner:"")?>') no-repeat;background-position:center;background-size:cover;z-index:1;">
						<? if($row->mdpick_banner){ ?><div style="position:absolute;top:0px;left:0px;width:100%;height:100%;background:#fff;opacity:0.8;"></div><? } ?>
					</div>
					<div style="position:relative;padding:20px 10px;box-sizing:border-box;overflow:hidden;z-index:2;">
						<div style="float:left;width:16%;"><div style="border-radius:50px;background:url('<?=($mdrow->profile_image?$imagepath.$mdrow->profile_image:"/images/common/no_img.gif")?>') no-repeat;background-size:cover;background-position:center;font-size:0px;line-height:0%;overflow:hidden;"><img src="/images/common/trans.gif" width="100%" alt="" /></div></div>
						<div style="float:left;width:58%;margin:0% 4%;overflow:hidden;">
							<div style="position:relative;margin-bottom:10px;padding-bottom:10px;color:#666;">
								<div style="position:absolute;bottom:0px;left:0px;width:35px;height:1px;background:#999;"></div>
								<?=$mdrow->deal_category?><span style="padding:0px 5px;">|</span>MD <?=$mdrow->md_nickname?>
							</div>
							<p style="color:#888;"><?=($row->mdpick_title?$row->mdpick_title:"배너 타이틀을 입력해 주세요.")?></p>
							<h4 style="margin:0;padding:0;color:#444;font-size:1.2em;font-weight:bold;"><?=$row->productname?></h4>
							<?//=($row->mdpick_regdate." / ".number_format($row->sellprice)."원 / ".number_format($row->sellcount)."건")?>
						</div>
						<div style="float:right;width:18%;">
							<img src="/data/shopimages/product/<?=$row->tinyimage?>" style="max-width:100%;" alt="" />
						</div>
					</div>
				</div>
				</a>
		<?
				echo "</li><!-- loop contents -->";
				$i++;
			}
		?>
		</ul>


		<div class="product_page" id="page_wrap">
			<?
				$pageLink=$_SERVER['PHP_SELF']."?sort=".$sort."&page=%u&mid=".$mid;
				$pagePerBlock = ceil($rowcount/$itemcount);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$currentPage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
		</div>
	</div><!-- msVenderSaleProductList -->
</div>

<form name="form1" method="get" action="<?=$_SERVER[PHP_SELF]?>">
	<input type="hidden" name="mid" value="<?=$mid?>" />
	<input type="hidden" name="sort" value="<?=$sort?>" />
</form>

<script language="javascript">
	<!--
	function ChangeSort(val) {
		document.form1.sort.value=val;
		document.form1.submit();
	}
	//-->
</script>

<? include_once('footer.php'); ?>
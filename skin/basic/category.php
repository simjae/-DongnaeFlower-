<?
//현재 카테고리명 구하기



if($_GET[codeD]!="000" && $_GET[codeD])
{
	$query_cg_name = mysql_query("SELECT code_name FROM tblproductcode WHERE codeA = '$_GET[codeA]' AND codeB = '$_GET[codeB]' AND codeC = '$_GET[codeC]' AND codeD = '$_GET[codeD]'");
	$depth = "D";
}
else if($_GET[codeC]!="000" && $_GET[codeC])
{
	$query_cg_name = mysql_query("SELECT code_name FROM tblproductcode WHERE codeA = '$_GET[codeA]' AND codeB = '$_GET[codeB]' AND codeC = '$_GET[codeC]'");
	$depth = "C";
	
}
else if($_GET[codeB]!="000" && $_GET[codeB])
{
	$query_cg_name = mysql_query("SELECT code_name FROM tblproductcode WHERE codeA = '$_GET[codeA]' AND codeB = '$_GET[codeB]'");
	$depth = "B";
	
}
else if($_GET[codeA]!="000" && $_GET[codeA])
{
	$query_cg_name = mysql_query("SELECT code_name FROM tblproductcode WHERE codeA = '$_GET[codeA]'");
	$depth = "A";
}

if($depth)
{
	$row_cg_name = mysql_fetch_array($query_cg_name);
	$cg_name = $row_cg_name[0];
}
else {	$cg_name = "카테고리"; }

?>



<div id="content">
	<div class="h_area2">
		<h2><?=$cg_name?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<!-- 카테고리 리스트 -->
	<div class="category_list">
		<ul class="list_type02">
<?
	
	if($depth=="D")
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' and codeA = '$_GET[codeA]' and codeB = '$_GET[codeB]' and codeC = '$_GET[codeC]' and codeD = '$_GET[codeD]' $qry_mobile_display ORDER BY sequence DESC ";
	}
	else if($depth=="C")
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' and codeA = '$_GET[codeA]' and codeB = '$_GET[codeB]' and codeC = '$_GET[codeC]'  $qry_mobile_display ORDER BY sequence DESC ";
	}	
	else if($depth=="B")
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' and codeA = '$_GET[codeA]' and codeB = '$_GET[codeB]'  $qry_mobile_display ORDER BY sequence DESC ";
	}
	else if($depth=="A")
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' and codeA = '$_GET[codeA]' $qry_mobile_display ORDER BY sequence DESC ";
	}
	else
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' AND (type='L' OR type='T' OR type='LX' OR type='TX')  $qry_mobile_display ORDER BY sequence DESC ";		
	}
	
	//echo $query;
	$result = mysql_query($query);
	$i = 0;
	while($row_cg = mysql_fetch_array($result))
	{
		$i++;
		//서브카테고리가 있으면 카테고리로		
		$code = $row_cg[codeA].$row_cg[codeB].$row_cg[codeC].$row_cg[codeD];

		//서브카테고리가 없는 카테고리라면
		/*if( strstr($row_cg[type],"X")) 	{		$str_page = "productlist.php";		}
		else	{		$str_page = "category.php";		}	*/
		$str_page = "productlist.php";	
		//상위 카테고리명은 하위목록에 출력하지 않는다
		if($depth=="C")
		{ 
			//depth 가 C 라면 D단계만 출력
			if($row_cg[codeD]!="000") 
			{	
				?>
				<li><a href="<?=$str_page?>?code=<?=$code?>&codeA=<?=$row_cg[codeA]?>&codeB=<?=$row_cg[codeB]?>&codeC=<?=$row_cg[codeC]?>&codeD=<?=$row_cg[codeD]?>" rel="external"><?=$row_cg[code_name]?></a></li>
				<?		
			}			
		}
		else if($depth=="B")
		{ 
			//depth 가 B 라면 D단계만 출력
			if($row_cg[codeC]!="000" && $row_cg[codeD]=="000") 
			{	
				?>
				<li><a href="<?=$str_page?>?code=<?=$code?>&codeA=<?=$row_cg[codeA]?>&codeB=<?=$row_cg[codeB]?>&codeC=<?=$row_cg[codeC]?>&codeD=<?=$row_cg[codeD]?>" rel="external"><?=$row_cg[code_name]?></a></li>
				<?		
			}			
		}
		else if($depth=="A")
		{ 
			//depth 가 A 라면 B단계만 출력
			if($row_cg[codeB]!="000" && $row_cg[codeC]=="000" && $row_cg[codeD]=="000") 
			{	
				?>
				<li><a href="<?=$str_page?>?code=<?=$code?>&codeA=<?=$row_cg[codeA]?>&codeB=<?=$row_cg[codeB]?>&codeC=<?=$row_cg[codeC]?>&codeD=<?=$row_cg[codeD]?>" rel="external"><?=$row_cg[code_name]?></a></li>
				<?		
			}			
		}
		else
		{
				?>
				<li><a href="<?=$str_page?>?code=<?=$code?>&codeA=<?=$row_cg[codeA]?>&codeB=<?=$row_cg[codeB]?>&codeC=<?=$row_cg[codeC]?>&codeD=<?=$row_cg[codeD]?>" rel="external"><?=$row_cg[code_name]?></a></li>
				<?	
		
		}
				
		?>
		

		
		<!-- <li><a href="productlist.php?code=<?=$row_cg[codeA]?>"><?=$row_cg[code_name]?></a></li> -->
		<!-- <li><a href="<?=$_SERVER[PHP_SELF]?>?code=<?=$row_cg[codeA]?>"><?=$row_cg[code_name]?></a></li> -->
		<!-- <li><a href="productlist_type01.php"><?=$row_cg[code_name]?></a></li> -->
		<?
	}

	if($i==1)
	{
		?><li style="height:30px;text-align:center;padding-top:15px">하위카테고리가 설정되어 있지 않습니다.</li><?
	
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
		<li><a href="http://<?=$row_t[url]?>" rel="external"><div class="icon_area"><img src="<?=$upload_path.$row_t[image]?>" class="img_large"></div><div class="txt_area"><?=$row_t[title]?></div></a></li>
		<?	
	}
?>

	</ul>
	<!-- //자주가는 서비스 -->
</div>
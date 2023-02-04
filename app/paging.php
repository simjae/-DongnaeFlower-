<?
	function handlePage($totalRecord,$recordPerPage,$pagePerBlock,$currentPage){ 

		$totalNumOfPage = ceil($totalRecord/$recordPerPage); 
		$totalNumOfBlock = ceil($totalNumOfPage/$pagePerBlock); 
		$currentBlock = ceil($currentPage/$pagePerBlock); 

		$startPage = ($currentBlock-1)*$pagePerBlock+1;   
		$endPage = $startPage+$pagePerBlock -1;  
		if($endPage > $totalNumOfPage) $endPage = $totalNumOfPage; 

		 
		$isNext = false; 
		$isPrev = false; 

		if($currentBlock < $totalNumOfBlock)    $isNext = true; 
		if($currentBlock > 1)                     $isPrev = true; 

		if($totalNumOfBlock == 1){ 
		$isNext = false; 
		$isPrev = false; 
		}  
	
		if($isPrev){ 
			$goPrevPage = $startPage-$pagePerBlock;  
			echo "<a rel=\"external\" href=\"$PHP_SELF?page=$goPrevPage\"><li><span class=\"arrow_left\"></span></li></a>";         
		}     
		for($i=$startPage;$i<=$endPage;$i++){ 
			if($i == $currentPage){
				echo "<a rel=\"external\" href=\"$PHP_SELF?page=$i\"><li class=\"cur_page\">".$i."</li></a>";
			}else{
				echo "<a rel=\"external\" href=\"$PHP_SELF?page=$i\"><li>".$i."</li></a>";
			}
		} 
		if($isNext){ 
				$goNextPage = $startPage+$pagePerBlock; 
				echo "<a rel=\"external\" href=\"$PHP_SELF?page=$goNextPage\"><li><span class=\"arrow_right\"></span></li></a>"; 
		}   
		
	} 
?>
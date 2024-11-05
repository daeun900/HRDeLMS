<?
include "../../include/include_function.php"; //DB연결 및 각종 함수 정의

$ID = $_SESSION['LoginMemberID'];

$RedHeart = "/cyber/img/educourse/red heart.png";
$WhiteHeart = "/cyber/img/educourse/white heart.png";

//컨텐츠좋아요 list
$Like_list = array();
$SQLB = "SELECT * from CourseLike WHERE ID = '$ID'";
$QUERYB = mysqli_query($connect, $SQLB);
if($QUERYB && mysqli_num_rows($QUERYB)){
    while($ROWB = mysqli_fetch_array($QUERYB)){
        extract($ROWB);
        $Like_list[$LectureCode] = $LectureCode;
    }
}

$pageStart = Replace_Check_XSS2($pageStart);
$sw  = Replace_Check_XSS2($sw);
$keyChkVal = Replace_Check_XSS2($keyChkVal);
$Category =  Replace_Check_XSS2($Category);
$CategoryB =  Replace_Check_XSS2($CategoryB);

if($sw){
    $where[] = "a.ContentsName LIKE '%$sw%'";
}
if($keyChkVal){
    $where[] = "a.Keyword LIKE '%$keyChkVal%'";
}
if($Category != "all"){
    $where[] = "a.Category1 LIKE '%$Category%'";
}
if($CategoryB){
    $where[] = "a.Category2 LIKE '%$CategoryB%'";
}
$where[] = "a.PackageYN='N'";
$where[] = "a.Del='N'";
$where[] = "a.UseYN='Y'";
$where[] = "a.ctype='B'";

$where = implode(" AND ",$where);
if($where) $where = "WHERE $where";

$SQL = "SELECT a.* FROM CourseCyber a $where  ORDER BY a.RegDate DESC, a.idx DESC  LIMIT $pageStart, 8";
//echo $SQL;
$QUERY = mysqli_query($connect, $SQL);
if($QUERY && mysqli_num_rows($QUERY)){
    while($ROW = mysqli_fetch_array($QUERY)){
        extract($ROW);
        $Keyword = str_replace(' ', '', $Keyword);
        $Keyword_array = explode('#',$Keyword);
        $Keyword_arrayA = array_slice($Keyword_array, 1, 3);
        $ImgUrl = "/upload/Course/".$PreviewImage;
?>
<li>
	<div class="course_img" style="background-image: url(<?=$ImgUrl?>); background-color: #e8ece7;" onclick="window.open('../edudetail/edudetail.html?LectureCode=<?=$LectureCode?>')" onclick="window.open('../edudetail/edudetail.html')">
		<span><?=$Keyword_array[1]?></span>					
		<button class="course_save_btn">
			<img onclick="CourseLike(this,'<?=$LectureCode?>', '<?=$ID?>')" name="courseLike" id="like_<?=$idx?>" <? if($Like_list[$LectureCode] == $LectureCode){ ?> src="<?=$RedHeart?>" <?}else{?> src="<?=$WhiteHeart?>" <?}?>" >
		</button>
	</div>
	<div class="course_title"><?=$ContentsName?></div>
	<div class="course_tag">
    	<? 
    	while (list($key,$value)=each($Keyword_arrayA)){
    	?>
    	 	<span><?=$value?></span>
    	<?
    	}
    	?>
	</div>
</li>
<?
	}
}

mysqli_close($connect);
?>
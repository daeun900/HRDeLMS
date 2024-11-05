<?
include "../../include/include_function.php"; //DB연결 및 각종 함수 정의

$pageStart = Replace_Check_XSS2($pageStart);
$PCategory =  Replace_Check_XSS2($PCategory);
$Category =  Replace_Check_XSS2($Category);

$where[] = "Del='N'";
$where[] = "UseYN='Y'";
$where[] = "PackageYN='N'";
$where[] = "Category1=$PCategory";

if($Category) {
    if($Category != "0"){
        $where[] = "Category2=$Category";
    }
}

$where = implode(" AND ",$where);
if($where) $where = "WHERE $where";

##-- 정렬조건
if($orderby=="") {
    $str_orderby = "ORDER BY RegDate DESC, idx DESC";
}else{
    $str_orderby = "ORDER BY $orderby";
}

$SQL = "SELECT * FROM Course $where  $str_orderby  LIMIT $pageStart, 8";
//echo $SQL;
$QUERY = mysqli_query($connect, $SQL);
if($QUERY && mysqli_num_rows($QUERY)){
    while($ROW = mysqli_fetch_array($QUERY)){
        extract($ROW);
        $ImgUrl = "/upload/Course/".$PreviewImage;
?>
<li>
    <div class="course_img" style="background-image: url(<?=$ImgUrl?>);" onclick="window.open('../edudetail/edudetail.html?LectureCode=<?=$LectureCode?>')"></div>
    <div class="course_title"><?=$ContentsName?></div>
    <div class="course_tag">내용전문가 : <?=$Professor?> | <?=$Chapter?>차시<em> / </em><?=$ContentsTime?>시간</div>
</li>
<?
	}
}

mysqli_close($connect);
?>
<?
include "../../include/include_function.php"; //DB연결 및 각종 함수 정의

$ID = $_SESSION['LoginMemberID'];

$pageStart = Replace_Check_XSS2($pageStart);

$where[] = "b.ID = '$ID'";
$where[] = "a.PackageYN='N'";
$where[] = "a.Del='N'";
$where[] = "a.UseYN='Y'";
$where[] = "a.ctype='B'";

$where = implode(" AND ",$where);
if($where) $where = "WHERE $where";

$SQL = "SELECT b.idx , b.LectureCode  , a.ContentsName , a.Keyword , a.PreviewImage , a.Price , a.Price01
		FROM CourseCyber a
		LEFT JOIN CourseLike b on a.LectureCode = b.LectureCode
		$where  ORDER BY a.RegDate DESC, a.idx DESC Limit $pageStart, 4";
//echo $SQL;
$QUERY = mysqli_query($connect, $SQL);
if($QUERY && mysqli_num_rows($QUERY)){
    while($ROW = mysqli_fetch_array($QUERY)){
        extract($ROW);
        $Keyword = str_replace(' ', '', $Keyword);
        $Keyword_array = explode('#',$Keyword);
        $Keyword_arrayA = array_slice($Keyword_array, 1, 3);
        $ImgUrl = "/upload/Course/".$PreviewImage;
        $RealPrice = $Price - $Price01; //실제금액
?>
<li>
    <input type="checkbox" name="chk" value="<?=$idx?>">
    <div class="left">
        <div class="lecture_img"><img src="<?=$ImgUrl?>" alt="과정이미지"></div>
        <button>강의맛보기</button>
    </div>
    <div class="right">
        <div class="top">
            <strong class="lecture_ttl"><?=$ContentsName?></strong>
            <div class="tag_box">
            <? while (list($key,$value)=each($Keyword_arrayA)){?>
			 	<span>#<?=$value?></span>
			<?}?>
            </div>
        </div>
        <div class="bottom">
            <div class="price">
                <strong class="fcred"><?=number_format($RealPrice,0)?> </strong>원
            </div>
            <button class="enrolment" onclick="location.href='/cyber/payment/card_payment.html'">수강신청<i class="ph ph-caret-right"></i></button>
        </div>
    </div>
</li>
<?
	}
}

mysqli_close($connect);
?>
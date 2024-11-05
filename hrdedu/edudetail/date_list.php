<?
include "../../include/include_function.php"; //DB연결 및 각종 함수 정의

$LectureCode = Replace_Check_XSS2($LectureCode);
$Price = Replace_Check_XSS2($Price);
$ID = Replace_Check_XSS2($ID);

$NowDate = date("Y-m-d");
$SQL = "SELECT DISTINCT LectureStart, LectureEnd FROM LectureTerme WHERE LectureCode='$LectureCode' AND LectureStart>'$NowDate' ORDER BY LectureStart ASC, LectureEnd ASC LIMIT 0,3";
$QUERY = mysqli_query($connect, $SQL);
if($QUERY && mysqli_num_rows($QUERY)){
    while($ROW = mysqli_fetch_array($QUERY)){
        extract($ROW);
?>
<b>
	<input type="radio" name="LectureDate" id="LectureDate<?=$i?>" value="<?=$LectureStart?>|<?=$LectureEnd?>">
	<label for="LectureDate<?=$i?>"><?=$LectureStart?> ~ <?=$LectureEnd?></label>
</b>
<br>
<?
    }
}else{
?>
<b>등록된 교육기간이 존재하지 않아 학습신청을 할수 없습니다.</b>
<?
}
mysqli_close($connect);
?>
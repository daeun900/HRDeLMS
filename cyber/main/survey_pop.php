<?
include "../../include/include_function.php"; //DB연결 및 각종 함수 정의

$idx = Replace_Check($idx);

//조회수 증가
$Sql = "UPDATE Review SET ViewCount=ViewCount+1 WHERE idx=$idx AND Del='N'";
$Row = mysqli_query($connect, $Sql);

//해당 데이터 조회
$Sql = "SELECT REGEXP_REPLACE(a.ID , '(?<=.{3}).', '*') AS MaskingID , a.* FROM Review a WHERE idx=$idx";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);

if($Row) {
    $Title = $Row['Title']; //후기제목
    $RegDate = $Row['RegDate']; //등록일
    $ViewCount = $Row['ViewCount']; //조회수
    $ContentsName = $Row['ContentsName']; //과정명
    $StarPoint = $Row['StarPoint']; //별점
    $Contents = nl2br(stripslashes($Row['Contents']));//후기내용
    $Status = $Row['Status']; //답변상태(A:대기/B:답변완료)
    $MaskingID = $Row['MaskingID']; //마스킹된 아이디
    
    $Star = StarPointViewC($StarPoint);
}else{
?>
<scirpt>
	alert('해당 수강후기글이 없습니다.');
	history.back();
</scirpt>
<?}?>
<style> 
    .review-modal {position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;z-index:1000;  padding: 50px;border-radius: 30px; width: 620px;}
    .close-btn{position: absolute;top: 20px; right: 20px;cursor: pointer;}
    .close-btn i{font-size: 25px;}
    .modal-wrap p{padding: 10px 0;}
</style>
<div class="review-modal">
    <div class="close-btn" onclick="Javascript:DataResultClose();">
        <i class="ph-thin ph-x"></i>
    </div>
    <div class="star"><?=$Star?></div>
    <span><?=$Title?></span>
    <div class="lecturename"><?=$ContentsName?></div>
    <div class="info">
        <span class="user"><i class="ph-light ph-user"></i><?=$MaskingID?></span>
        <span class="bar"></span>
        <span class="date"><?=substr($RegDate,0,10)?></span>
    </div>
    <p><?=$Contents?></p>
</div>
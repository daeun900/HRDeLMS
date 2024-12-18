<?
include "../include/include_function.php"; //DB연결 및 각종 함수 정의

include "../include/login_check.php";

$Chapter_Number = Replace_Check_XSS2($Chapter_Number);
$LectureCode = Replace_Check_XSS2($LectureCode);
$Study_Seq = Replace_Check_XSS2($Study_Seq);
$Chapter_Seq = Replace_Check_XSS2($Chapter_Seq);
$Contents_idx = Replace_Check_XSS2($Contents_idx);
$ContentsDetail_Seq = Replace_Check_XSS2($ContentsDetail_Seq);
$ProgressTime = Replace_Check_XSS2($ProgressTime);
$LastStudy = Replace_Check2($LastStudy);
$CompleteTime = Replace_Check_XSS2($CompleteTime);
$ProgressStep = Replace_Check_XSS2($ProgressStep);

//차시 진도율 
$ChapterProgress = floor($ProgressTime / $CompleteTime * 100);

if($ChapterProgress>=100) {
	$ChapterProgress = 100;
}

//이몬에 전송할 트리거 설정

//최초 진도 시작인 경우
if($ProgressStep=="Start") {
	if($_SESSION["EndTrigger"]=="N") {
		$TriggerYN = "Y";
	}else{
		$TriggerYN = "N";
	}
}

//중간에 1분마다 진도체크시 진도가 100%이고 세션 EndTrigger가 N인 경우만 트리거 전송
if($ProgressStep=="Middle") {
	if($ChapterProgress == 100 && $_SESSION["EndTrigger"]=="N") {
		$TriggerYN = "Y";
		$_SESSION["EndTrigger"] = "Y";
		// Brad (2021.11.28) : IsPlaying Session 초기화
		//$_SESSION["IsPlaying"] = "N";
	}else{
		$TriggerYN = "N";
	}
}

//학습종료 클릭시 이미 트리거를 전송했으면(세션 EndTrigger가 Y인 경우는 트리거 전송하지 않고 N인 경우만 트리거 전송)
if($ProgressStep=="End") {
	if($_SESSION["EndTrigger"]=="N") {
		$TriggerYN = "Y";
		$_SESSION["EndTrigger"] = "Y";
	}else{
		$TriggerYN = "N";
	}

	// Brad (2021.11.28) : IsPlaying Session 초기화
	$_SESSION["IsPlaying"] = "N";
}

$TriggerYN = "Y";

//수강한 전체 차시의 진도율 합
$Sql = "SELECT SUM(IF(Progress>100,100,Progress)) FROM Progress WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);
$SUM_Progress = $Row[0];

//수강한 차시수
$Sql = "SELECT COUNT(idx) FROM Progress WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);
$ProgressCount = $Row[0];


if(!$SUM_Progress) {
	$SUM_Progress = 0;
}
if(!$ProgressCount) {
	$ProgressCount = 0;
}

//전체 진도율
$Sql = "SELECT a.ServiceType, a.Progress AS StudyProgress, b.Chapter, b.PassProgress, p.Progress
			FROM Study AS a 
			LEFT OUTER JOIN Course AS b ON a.LectureCode=b.LectureCode 
            LEFT OUTER JOIN Progress AS p ON p.Study_Seq = $Study_Seq AND p.Chapter_Seq =$Chapter_Seq
			WHERE a.ID='$LoginMemberID' AND a.LectureCode='$LectureCode' AND a.Seq=$Study_Seq";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);

if($Row) {
	$Chapter = $Row['Chapter']; //차시수
	$ServiceType = $Row['ServiceType']; //강의 구분
	$PassProgress = $Row['PassProgress']; //수료기준 진도율
	$prvStudyProgress = $Row['StudyProgress']; //전체 진도율
	$prvProgressProgress = $Row['Progress']; //차시 진도율
}

$Total_Progress = floor($SUM_Progress / ($Chapter * 100) * 100);

if($Total_Progress>=100) {
	$Total_Progress = 100;
}

$Total_Progress = (int)$Total_Progress;

if($ServiceType=="3" && $Total_Progress >= $PassProgress) {
	$PassOk_Query = ", PassOK='Y'";
	$PassOk = "Y";
}

if($prvStudyProgress < $Total_Progress){
    $Sql3 = "UPDATE Study SET Progress=$Total_Progress, StudyIP='$UserIP' $PassOk_Query WHERE Seq=$Study_Seq";
    mysqli_query($connect, $Sql3);
}

if($prvProgressProgress < $ChapterProgress){ //Progress테이블의 Progress와 비교한 것. 
    //수강현황 로그 작성
    $Sql_log = "INSERT INTO ProgressLog(ID, LectureCode, Study_Seq, Chapter_Seq, Contents_idx, ContentsDetail_Seq, LastStudy, 
                                        Progress, StudyTime, UserIP, RegDate, TriggerYN, Chapter_Number, TotalProgress) 
                VALUES('$LoginMemberID', '$LectureCode', $Study_Seq, $Chapter_Seq, $Contents_idx, $ContentsDetail_Seq, '$LastStudy', 
                        $ChapterProgress, $ProgressTime, '$UserIP', NOW(), '$TriggerYN', '$Chapter_Number', $Total_Progress)";
    mysqli_query($connect, $Sql_log);
}

//현재 수강중인 차시가 존재하는지 체크
$Sql = "SELECT * FROM Progress WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq AND Chapter_Seq=$Chapter_Seq AND Contents_idx=$Contents_idx";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);

if($Row) {
	if($ChapterProgress>=100) {
		$Sql2 = "UPDATE Progress 
                    SET ContentsDetail_Seq=$ContentsDetail_Seq, LastStudy='$LastStudy', StudyTime=$ProgressTime, UserIP='$UserIP', 
                        Progress=$ChapterProgress, TriggerYN='$TriggerYN', Chapter_Number='$Chapter_Number' 
                    WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq 
                        AND Chapter_Seq=$Chapter_Seq AND Contents_idx=$Contents_idx";
		mysqli_query($connect, $Sql2);
	} else {
	    if($prvProgressProgress < $ChapterProgress){
    		$Sql2 = "UPDATE Progress 
                        SET ContentsDetail_Seq=$ContentsDetail_Seq, LastStudy='$LastStudy', StudyTime=$ProgressTime, UserIP='$UserIP', 
                            RegDate=NOW(), Progress=$ChapterProgress, TriggerYN='$TriggerYN', Chapter_Number='$Chapter_Number' 
                        WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq 
                            AND Chapter_Seq=$Chapter_Seq AND Contents_idx=$Contents_idx";
    		mysqli_query($connect, $Sql2);
	    }
	}
} else { 
	$Sql2 = "INSERT INTO Progress(ID, LectureCode, Study_Seq, Chapter_Seq, Contents_idx, ContentsDetail_Seq, LastStudy,
                                 Progress, StudyTime, UserIP, RegDate, TriggerYN, Chapter_Number) 
                VALUES('$LoginMemberID', '$LectureCode', $Study_Seq, $Chapter_Seq, $Contents_idx, $ContentsDetail_Seq, '$LastStudy', 
                        $ChapterProgress, $ProgressTime, '$UserIP', NOW(), '$TriggerYN', '$Chapter_Number')";
    mysqli_query($connect, $Sql2);
}

$studyapi = array();
$studyapi['Chapter_Number'] = $Chapter_Number-1;
$studyapi['Study_Seq'] = $Study_Seq;
$studyapi['ProgressCount'] = sprintf("%02d", $ProgressCount);
$studyapi['Total_Progress'] = $Total_Progress;
$studyapi['ChapterProgress'] = $ChapterProgress;
$studyapi['PassOk'] = $PassOk;

$json_encoded = json_encode($studyapi);
print_r($json_encoded);

mysqli_close($connect);

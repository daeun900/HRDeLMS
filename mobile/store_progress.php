<?php 
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = Replace_Check_XSS2($data['id']);
$chapterNumber = Replace_Check_XSS2($data['chapterNumber']);
$lectureCode = Replace_Check_XSS2($data['lectureCode']);
$studySeq = Replace_Check_XSS2($data['studySeq']);
$chapterSeq = Replace_Check_XSS2($data['chapterSeq']);
$contentsIdx = Replace_Check_XSS2($data['contentsIdx']);
$contentsDetailSeq = Replace_Check_XSS2($data['contentsDetailSeq']);
$progressTime = Replace_Check_XSS2($data['progressTime']); //지금까지 들은 수강 시간 (플레이어 하단 기록 시간)
$lastStudy = Replace_Check2($data['lastStudy']);
$completeTime = Replace_Check_XSS2($data['completeTime']);
$progressStep = Replace_Check_XSS2($data['progressStep']);

$response = [];

//차시 진도율
$chapterProgress = floor($progressTime / $completeTime * 100);

if($chapterProgress >= 100){
    $chapterProgress = 100;
    $set4RegDate = "";
}else{
    $set4RegDate = "RegDate=NOW(),"; //혹시 다시 듣는 경우에 마지막으로 수강했던 날짜가 변경될까봐인지, 기존 코드에 이 부분이 구분되어 있었음. 
}

//이몬 전송 트리거 설정 -----------------------------------------------------------------------------------------------------------
/*
//최소 진도 시작인 경우
if($progressStep == 'Start'){
    $triggerYN = ($_SESSION["EndTrigger"] == "N") ? "Y" : "N";
}
*/
//중간에 1분마다 진도체크시 진도가 100%이고 세션 EndTrigger가 N인 경우만 트리거 전송
if($progressStep=="Middle") {
    if($ChapterProgress == 100 && $_SESSION["EndTrigger"]=="N") {
//         $TriggerYN = "Y";
        $_SESSION["EndTrigger"] = "Y";
    }
}


//학습종료 클릭시 이미 트리거를 전송했으면(세션 EndTrigger가 Y인 경우는 트리거 전송하지 않고 N인 경우만 트리거 전송)
if($progressStep=='End') {
//     $triggerYN = ($_SESSION["EndTrigger"] == 'N')? 'Y' : 'N'; //어차피 아래에서 전부 Y로 변경됌.
    $_SESSION["IsPlaying"] = 'N';
}
$triggerYN = 'Y'; 
//----------------------------------------------------------------------------------------------------------------------------

/*기존 쿼리(아래의 $sql4Select로 합침) ---------------------------------------------------------------------------------------------
"SELECT SUM(IF(Progress>100,100,Progress)) FROM Progress WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq";

"SELECT a.ServiceType, a.Progress AS StudyProgress, b.Chapter, b.PassProgress, p.Progress
			FROM Study AS a 
			LEFT OUTER JOIN Course AS b ON a.LectureCode=b.LectureCode 
            LEFT OUTER JOIN Progress AS p ON p.Study_Seq = $Study_Seq AND p.Chapter_Seq =$Chapter_Seq
			WHERE a.ID='$LoginMemberID' AND a.LectureCode='$LectureCode' AND a.Seq=$Study_Seq";

"SELECT * FROM Progress WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq AND Chapter_Seq=$Chapter_Seq AND Contents_idx=$Contents_idx";
------------------------------------------------------------------------------------------------------------------------------*/
$sql4Select = "SELECT a.ServiceType, a.Progress AS prvStudyProgress, b.Chapter, b.PassProgress, p1.Progress AS prvProgressProgress, 
                    IFNULL(SUM(LEAST(p2.Progress, 100)), 0) AS sumProgress, p3.p3Cnt
                FROM Study AS a
                    LEFT OUTER JOIN Course AS b ON a.LectureCode = b.LectureCode
                    LEFT OUTER JOIN Progress AS p1 ON p1.Study_Seq = $studySeq AND p1.Chapter_Seq = $chapterSeq
                    LEFT OUTER JOIN Progress AS p2 ON p2.ID = a.ID AND p2.LectureCode = a.LectureCode AND p2.Study_Seq = a.Seq
                    LEFT OUTER JOIN (
                        SELECT count(idx) AS p3Cnt FROM Progress 
                        WHERE ID = '$id' AND LectureCode = '$lectureCode' AND Study_Seq = $studySeq 
                            AND Chapter_Seq = $chapterSeq AND Contents_idx = $contentsIdx
                    ) AS p3 ON 1=1
                WHERE a.ID = '$id' AND a.LectureCode = '$lectureCode' AND a.Seq = $studySeq";
$query4Select = mysqli_query($connect, $sql4Select);
$row = mysqli_fetch_assoc($query4Select);

if($row){
    extract($row);
    //기존 코드에서는 $prgPct = $Total_Progress; 이 변수가 100을 초과하지 않게, 그리고 integer 데이터형을 가지도록.
    $prgPct = (int)min(floor($sumProgress / ($Chapter * 100) * 100), 100); 
    $response['$sumProgress'] = $sumProgress; // "16"
    $response['$Chapter'] = $Chapter; // "4"
    
    if($ServiceType=="3" && $prgPct >= $PassProgress){
        $setQuery = ", PassOK='Y'";
    }
    $response['$prvStudyProgress'] = $prvStudyProgress;
    $response['$prgPct'] = $prgPct;
    if($prvStudyProgress <= $prgPct){// 기존의 전체 진도율 < 현재 계산된 진도율
        $sql4Update = "UPDATE Study SET Progress=$prgPct, StudyIP='$UserIP' $setQuery WHERE Seq=$studySeq";
        $row4Study = mysqli_query($connect, $sql4Update);
        $response['test'] = 'IN : '.$sql4Update;
    }else{
        $response['alert'] .= 'Error : Previous Data is bigger (L90)';
        $response['test'] = 'out';
    }
    
//     if($prvProgressProgress < $chapterProgress){ //Progress테이블의 Progress와 비교한 것. 
        //수강현황 로그 작성
        $sql4Log = "INSERT INTO ProgressLog(ID, LectureCode, Study_Seq, Chapter_Seq, Contents_idx, ContentsDetail_Seq, LastStudy, 
                                            Progress, StudyTime, UserIP, RegDate, TriggerYN, Chapter_Number, TotalProgress) 
                    VALUES('$id', '$lectureCode', $studySeq, $chapterSeq, $contentsIdx, $contentsDetailSeq, '$lastStudy', 
                            $chapterProgress, $progressTime, '$UserIP', NOW(), '$triggerYN', '$chapterNumber', $prgPct)";
        $row4ProgressLog = mysqli_query($connect, $sql4Log);
//     }else{
//         $response['alert'] .= 'Error : Previous Data is bigger (L101)';
//     }
    
    if($p3Cnt > 0){ //현재 수강중인 차시가 존재하는지 (update)
//         if($prvProgressProgress < $chapterProgress){//기존에 저장되어 있는 Progress테이블의 Progress가 현재 넣으려 하는 chapterProgress보다 작을 때.
            $sql4Update = "UPDATE Progress
                            SET ContentsDetail_Seq=$contentsDetailSeq, LastStudy='$lastStudy', StudyTime=$progressTime, UserIP='$UserIP',
                                $set4RegDate Progress=$chapterProgress, TriggerYN='$triggerYN', Chapter_Number='$chapterNumber'
                            WHERE ID='$id' AND LectureCode='$lectureCode' AND Study_Seq=$studySeq
                                AND Chapter_Seq=$chapterSeq AND Contents_idx=$contentsIdx";
            $row4Progress = mysqli_query($connect, $sql4Update);
//         }else{
//             $response['alert'] .= 'Error : Previous Data is bigger (L113)';
//         }
    }else{ // (insert)
        $sql4Insert = "INSERT INTO Progress(ID, LectureCode, Study_Seq, Chapter_Seq, Contents_idx, ContentsDetail_Seq, LastStudy,
                                            Progress, StudyTime, UserIP, RegDate, TriggerYN, Chapter_Number)
                        VALUES('$id', '$lectureCode', $studySeq, $chapterSeq, $contentsIdx, $contentsDetailSeq, '$lastStudy',
                                $chapterProgress, $progressTime, '$UserIP', NOW(), '$triggerYN', '$chapterNumber')";
        $row4Progress = mysqli_query($connect, $sql4Insert);
    }
    
    if($row4Study && $row4ProgressLog && $row4Progress){
        $response['alert'] = 'Y';        
    }
  
}else{
     $response['alert'] = 'Error : No Data (L123)';
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>
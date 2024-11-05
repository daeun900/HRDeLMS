<?php 
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = Replace_Check_XSS2($data['id']);
$studySeq = Replace_Check_XSS2($data['seq']);

$response = [];

// 상단의 Course 데이터 ---------------------------------------------------------------------------------------------
$sql4course = "SELECT s.LectureStart, s.LectureEnd, DATEDIFF(s.LectureEnd, CURDATE()) AS DaysLeft, s.ServiceType, s.LectureCode, s.Progress, 
                        c.ContentsName, c.Chapter, c.Professor, c.attachFile, COUNT(p.idx) AS ProgressNum
                FROM Study s 
                INNER JOIN Course c ON s.LectureCode = c.LectureCode  
                LEFT JOIN Progress p ON p.Study_Seq = s.Seq
                WHERE s.Seq =$studySeq";
$query4course = mysqli_query($connect, $sql4course);
$row = mysqli_fetch_array($query4course);

if($row){
    extract($row);
        
    $studyProgressStatus = StudyProgressStatus($ServiceType,$LectureCode,$studySeq);
    $studyProgressStatus_Array = explode("|",$studyProgressStatus);
    $progressStep = $studyProgressStatus_Array[0];
    $progressStatus = $studyProgressStatus_Array[1];

    $response['title'] = $ContentsName;
    $response['lectureStart'] = $LectureStart;
    $response['lectureEnd'] = $LectureEnd;
    $response['daysLeft'] = $DaysLeft;
    $response['professor'] = $Professor;
    $response['classNum'] = $progressStep;//현재 몇차시인지
    $response['classStatus'] = $progressStatus;//현재 차시의 수강상태
    $response['progressNum'] = $ProgressNum;
    $response['chapter'] = $Chapter;
    $response['progressPercent'] = $Progress;
    $response['attachFile'] = $attachFile;
}

// 하단에 들어갈 Contents list 데이터-------------------------------------------------------------------------------------
$sql4chapter = "SELECT ch.Seq AS ChapterSeq, ch.ChapterType, cn.idx AS ContentsIdx, cn.ContentsTitle, p.StudyTime, 
                        IFNULL(p.idx, 0) AS ProgressIdx, IFNULL(p.Progress,0) AS ChapterProgress
                FROM Chapter ch 
                LEFT OUTER JOIN Contents cn ON ch.Sub_idx = cn.idx 
                LEFT OUTER JOIN Progress p ON p.ID = '$id' AND p.Chapter_Seq = ch.Seq
                WHERE ch.LectureCode = '$LectureCode'
                ORDER BY ch.OrderByNum"; 
$query4chapter = mysqli_query($connect, $sql4chapter);
if($query4chapter){
    $i = 1;
    while($row = mysqli_fetch_assoc($query4chapter)){
        $contentsTitle = $row['ContentsTitle'];//
        $chapterType = $row['ChapterType'];//(A:강의차시/B:중간평가/C:최종평가/D:과제/E:토론방)
        $progressIdx = $row['ProgressIdx']; // 이 값으로 progress에 값이 있는지 확인
        $chapterProgress = $row['ChapterProgress']; //챕터 진도율
        $studyTime = $row['StudyTime']; //챕터 진도율
        
        //수강하기 버튼 활성화여부
        $canTakeContent = 'N'; 
        $progressStepNum = str_replace("차시","",$progressStep);
        if($progressIdx > 0 || $i == $progressStepNum){//이미 수강한 컨텐츠일 경우 || 현재 들어야 하는 컨텐츠일 경우
            $canTakeContent = 'Y';
        }
        
        //Play() 함수 파라미터의 마지막 값인 $mode를 구하기 위한 코드
        if($ROW2['StudyTime']<1) {
            $playMode = "S"; // 새로 듣는 모드
        }else{
            $playMode = "C";// 이어보기 모드
        }
        
        //나중에 수강하기 버튼 누를 시 다시 받아야하는 데이터
        $chapterSeq = $row['ChapterSeq'];
        $contentsIdx = $row['ContentsIdx'];
        
        $response['chapterInfo'][] = [$contentsTitle, $chapterType, $canTakeContent, $chapterProgress];//컨텐츠명, 챕터 타입, 수강하기 버튼 활성화여부 데이터, 챕터별 진도율
        $response['returnBack'][] = [$LectureCode, $studySeq, $chapterSeq, $contentsIdx, $progressIdx, $playMode, $progressStep];//강의수강하기 버튼 누르면 프론트가 백에게 주어야하는 데이터
        
        $i++;
    }
}

if (!$connect) {
    $response['title'] = 'include_function.php 파일 확인해야하는 경우';
}
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>
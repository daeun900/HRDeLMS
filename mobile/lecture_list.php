<?php // 전달 값 => id   리런값 => [  강의제목 = title | 현재 차시진행상태 = classNum | 현재 강의진도 = progressNum | 현재 진도율 = progressPercent ]
// 입력 값 받아오기
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

// 아이디와 비밀번호 추출
$id = $data['id'] ?? null;

// 응답 초기화
$response = [];

$hrdeSafeLectureCodes = ['H001', 'H002', 'H03', 'H004', 'H005', 'H006', 'H007', 'H008'];
$hrdeSafeIdCode = 'B01';

$sql4select = "SELECT s.LectureCode, s.ServiceType, s.Seq AS StudySeq, s.Progress, c.ContentsName, c.Chapter, COUNT(p.idx) AS progressNum
                FROM Study s 
                INNER JOIN Course c ON s.LectureCode = c.LectureCode 
                LEFT JOIN Progress p ON p.Study_Seq = s.Seq
                WHERE s.ID='$id' AND (s.LectureStart <= DATE(NOW()) AND s.LectureEnd >= DATE(NOW())) AND s.StudyEnd='N'
                GROUP BY s.Seq ORDER BY StudySeq";
$query4select = mysqli_query($connect, $sql4select);

if($query4select && mysqli_num_rows($query4select)){
    while($row = mysqli_fetch_array($query4select)){
        extract($row);
        
        $studyProgressStatus = StudyProgressStatus($ServiceType,$LectureCode,$StudySeq);
        $studyProgressStatus_Array = explode("|",$studyProgressStatus);
        $progressStep = $studyProgressStatus_Array[0];
        //인재개발원 강의인지 판단값('Y' || 'N')
        if(in_array($LectureCode, $hrdeSafeLectureCodes)){
            $hrdeSafeLecture = 'Y';
        }else{
            $hrdeSafeLecture = 'N';
        }
        //연동 코드값
        
        $response['data'][] = [$ContentsName, $progressStep, $progressNum, $Chapter, $Progress, $StudySeq, $hrdeSafeLecture, $hrdeSafeIdCode];
        
//         $response['title'][$i] = $ContentsName;
//         $response['classNum'][$i] = $progressStep;
//         $response['progressNum'][$i] = $progressNum;
//         $response['progressPercent'][$i] = $Progress;
    }
}

if (!$connect) {
    $response['title'] = 'include_function.php 파일 확인해야하는 경우';
}
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>
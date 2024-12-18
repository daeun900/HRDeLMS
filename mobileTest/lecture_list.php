<?php // 전달 값 => id   리런값 => [  강의제목 = title | 현재 차시진행상태 = classNum | 현재 강의진도 = progressNum | 현재 진도율 = progressPercent ]
// 입력 값 받아오기
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

// 아이디와 비밀번호 추출
$id = $data['id'] ?? null;

// 응답 초기화
$response = [];

$sql4select = "SELECT s.LectureCode, s.ServiceType, s.Seq AS StudySeq, s.Progress, c.ContentsName, c.Chapter, COUNT(p.idx) AS progressNum
                FROM Study s 
                INNER JOIN Course c ON s.LectureCode = c.LectureCode 
                LEFT JOIN Progress p ON p.Study_Seq = s.Seq
                WHERE s.ID='$id' AND (s.LectureStart <= DATE(NOW()) AND s.LectureEnd >= DATE(NOW())) AND s.StudyEnd='N'
                GROUP BY s.LectureCode ORDER BY StudySeq";
/*
플렉스까지 작업 시에 쿼리문 아래의 것으로 대체하기
SELECT  a.Seq AS StudySeq, 
        a.ServiceType, 
        a.LectureCode, 
        a.Progress, 
        COALESCE(b.ContentsName, f.ContentsName) AS ContentsName,
        COALESCE(b.Chapter, f.Chapter) AS Chapter, 
        COUNT(p.idx) AS progressNum
FROM Study AS a 
LEFT OUTER JOIN Course AS b ON a.LectureCode = b.LectureCode 
LEFT OUTER JOIN CourseCyber AS f ON a.LectureCode = f.LectureCode 
LEFT JOIN Progress p ON p.Study_Seq = a.Seq
WHERE a.ID = 'test06' AND (s.LectureStart <= DATE(NOW()) AND s.LectureEnd >= DATE(NOW())) AND a.StudyEnd='N'
GROUP BY a.Seq, a.ServiceType, a.LectureCode, a.Progress, COALESCE(b.ContentsName, f.ContentsName), COALESCE(b.Chapter, f.Chapter)
ORDER BY a.PackageRef ASC, a.PackageLevel ASC, a.InputDate DESC;
 */
$query4select = mysqli_query($connect, $sql4select);
if($query4select && mysqli_num_rows($query4select)){
//     $i = 0;
    while($row = mysqli_fetch_array($query4select)){
        extract($row);
        
        $studyProgressStatus = StudyProgressStatus($ServiceType,$LectureCode,$StudySeq);
        $studyProgressStatus_Array = explode("|",$studyProgressStatus);
        $progressStep = $studyProgressStatus_Array[0];
        $response['data'][] = [$ContentsName, $progressStep, $progressNum, $Chapter, $Progress, $StudySeq];

//         $response['title'][$i] = $ContentsName;
//         $response['classNum'][$i] = $progressStep;
//         $response['progressNum'][$i] = $progressNum;
//         $response['progressPercent'][$i] = $Progress;
        
//         $i++;
    }
}

if (!$connect) {
    $response['title'] = 'include_function.php 파일 확인해야하는 경우';
}
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>
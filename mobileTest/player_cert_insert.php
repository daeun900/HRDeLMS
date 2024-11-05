<?php 
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = Replace_Check_XSS2($data['id']);
$lectureCode = Replace_Check_XSS2($data['lectureCode']);
$studySeq = Replace_Check_XSS2($data['studySeq']);
$certType = Replace_Check_XSS2($data['certType']);
$AGTID = Replace_Check_XSS2($data['AGTID']);
$COURSE_AGENT_PK = Replace_Check_XSS2($data['COURSE_AGENT_PK']);
$CLASS_AGENT_PK = Replace_Check_XSS2($data['CLASS_AGENT_PK']);
$m_Ret = Replace_Check_XSS2($data['m_Ret']);
$m_retCD = Replace_Check_XSS2($data['m_retCD']);
$m_trnID = Replace_Check_XSS2($data['m_trnID']);
$m_trnDT = Replace_Check_XSS2($data['m_trnDT']);

// 응답 초기화
$response = [];

$sql4select = "SELECT COUNT(*) AS certCnt FROM UserCertOTP WHERE ID='$id' AND LectureCode='$lectureCode' AND Study_Seq=$studySeq";
$query4select = mysqli_query($connect, $sql4select);
$row = mysqli_fetch_array($query4select);
if($row){ // 정상 실행.
    extract($row);
    if($certCnt < 1){
        //인증선공시 인증내역에 저장
        $maxSeq = max_number("Seq", "UserCertOTP");
        $certDate = date("Y-m-d");
        
        $sql4insert = "INSERT INTO UserCertOTP(Seq, ID, LectureCode, Study_Seq, CertDate, CertType, AGTID, 
                                               COURSE_AGENT_PK, CLASS_AGENT_PK, m_Ret, m_retCD, m_trnID, m_trnDT, RegDate) 
                       VALUES($maxSeq, '$id', '$lectureCode', $studySeq, '$certDate', '$certType', '$AGTID', 
                              '$COURSE_AGENT_PK', '$CLASS_AGENT_PK', '$m_Ret', '$m_retCD', '$m_trnID', '$m_trnDT', NOW())";
        $row4insert = mysqli_query($connect, $sql4insert);
        
        $sql4update = "UPDATE Study SET certCount=certCount+1 WHERE ID='$id' AND LectureEnd>='$certDate' AND StudyEnd='N'";
        $row4update = mysqli_query($connect, $sql4update);
    }
    
    if($row4insert && $row4update){
        $response['result'] =  'Y1'; // 전부 정상 실행 
    }else{
        $response['result'] =  'Y2'; // 첫 쿼리만 성공하고 나머지는 실패이거나, 첫쿼리 성공했고 조회된 cnt 데이터가 1개 이상이어서 L26 if문에 들어가지 않은 경우.
    }
}else{
    $response['result'] =  'N2'; // 첫 쿼리 실행 자체에 오류가 생긴 경우
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>
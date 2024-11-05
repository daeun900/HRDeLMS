<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$chapterNum = Replace_Check_XSS2($data['chapterNum']);
$lectureCode = Replace_Check_XSS2($data['lectureCode']);
$studySeq = Replace_Check_XSS2($data['studySeq']);
$chapterSeq = Replace_Check_XSS2($data['chapterSeq']);

$response = [];

$_SESSION['PlayStudyAuth_'.$studySeq.$chapterSeq] = "Y";

$response['result'] = $_SESSION['PlayStudyAuth_'.$studySeq.$chapterSeq];

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>
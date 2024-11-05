<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$lectureCode = Replace_Check($data['lectureCode']);
// 응답 초기화
$response = [];
$cnt = 0;

$sql4Course = "SELECT * FROM Course WHERE LectureCode='$lectureCode'";
$query4Course = mysqli_query($connect, $sql4Course);
$row = mysqli_fetch_array($query4Course);

if($row){
    $cnt++;
}else{
    $sql4CourseCyber = "SELECT * FROM CourseCyber WHERE LectureCode='$lectureCode'";
    $query4CourseCyber = mysqli_query($connect, $sql4CourseCyber);
    $row = mysqli_fetch_array($query4CourseCyber);
    if($row4CourseCyber){
        $cnt++;
    }
}
if($cnt > 0){
    $fileName = $row['attachFile'];
    $contentsName = $row['ContentsName'];
//     echo $fileName.'d';
    $contentsName = str_replace(",","",$contentsName);
    $contentsName = str_replace(" ","_",$contentsName);
    $contentsName = str_replace(".","_",$contentsName);
    
    $ext = substr(strrchr($fileName,"."),1);
    $realFileName = "학습자료_".$contentsName.".".$ext;
//     $realFileName = iconv("UTF-8","EUC-KR",$realFileName);
    
    $filePath = $UPLOAD_DIR."/Course/".$fileName;
    $filePath = addslashes($filePath);
    echo $realFileName.'='.$filePath; // 학습자료_.=/web/hrde/html/upload/Course/
    
    header("Content-Type: application/octet-stream");
    Header("Content-Disposition: attachment;; filename=$realFileName");
    header("Content-Transfer-Encoding: binary");
    Header("Content-Length: ".(string)(filesize($filePath)));
    Header("Cache-Control: cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    $fp = fopen($filePath,'r+b');
    if (!fpassthru($fp)) {
        fclose($fp);
    }
}else{
    $response['result'] = $cnt;
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>
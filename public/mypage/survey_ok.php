<?
include "../../include/include_function.php"; //DB연결 및 각종 함수 정의
include "../include/login_check.php";

//트랜잭션 시작
mysqli_query($connect, "SET AUTOCOMMIT=0");
mysqli_query($connect, "BEGIN");

$error_count = 0;
$error_msg;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
<title><?=$SiteName?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" href="/css/style.css" />
<link rel="stylesheet" type="text/css" href="/include/jquery-ui.css" />
<script type="text/javascript" src="/include/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="/include/jquery-ui.js"></script>
<script type="text/javascript" src="/include/jquery.ui.datepicker-ko.js"></script>
<script type="text/javascript" src="/include/function.js"></script>
</head>

<body>
<?
$ContentsName = Replace_Check_XSS2($ContentsName); //과정명
$ServiceType = Replace_Check_XSS2($ServiceType); //환급여부
$LectureCode = Replace_Check_XSS2($LectureCode); //강의코드
$Study_Seq = Replace_Check_XSS2($Study_Seq);

//수강후기 정보
$ReviewVal = Replace_Check_XSS2($ReviewVal); //별점
$title = Replace_Check_XSS2($title); //제목
$content = Replace_Check_XSS2($content); //내용

//설문조사 정보
$ATypeEA = Replace_Check_XSS2($ATypeEA); //설문문항수
$ExamA_idx = Replace_Check_XSS2($ExamA_idx_value); //설문idx
$ExamA_answer = Replace_Check_XSS2($ExamA_answer); //체크한 설문 값

//근로자훈련일 경우에만 수강후기 작성
if($ServiceType == 4){
    //[1]수강후기 등록
    $maxno1 = max_number("idx","Review");
    $Sql1 = "INSERT INTO Review(idx, Study_Seq, LectureCode, ID, Name, StarPoint, IP, Contents, RegDate, UseYN, Title, ContentsName, Status, Del)
        VALUES($maxno1, '$Study_Seq', '$LectureCode', '$LoginMemberID', '$LoginName', $ReviewVal, '$UserIP', '$content', now(), 'N', '$title', '$ContentsName', 'A', 'N')";
    $Row1 = mysqli_query($connect, $Sql1);
    if(!$Row1) { //쿼리 실패시 에러카운터 증가
        $error_count++;
        $error_msg = "수강후기 등록 오류";
    }    
}


//[2]설문조사 등록
//[2-1]수강정보 확인
$Sql2 = "SELECT * FROM Study WHERE LectureCode='$LectureCode' AND Seq=$Study_Seq";
$Result2 = mysqli_query($connect, $Sql2);
$Row2 = mysqli_fetch_array($Result2);
if($Row2) {
    $LectureTerme_idx = $Row2['LectureTerme_idx'];
}
//[2-2]설문결과 등록
$maxno2 = max_number("idx","SurveyAnswer");
$Sql3 = "INSERT INTO SurveyAnswer(idx, ID, LectureCode, Study_Seq, ATypeEA, BTypeEA, ExamA_idx,  ExamB_idx, ExamA_answer, RegDate)
        VALUES($maxno2, '$LoginMemberID', '$LectureCode', $Study_Seq, $ATypeEA, 0, '$ExamA_idx', '', '$ExamA_answer', NOW())";
$Row3 = mysqli_query($connect, $Sql3);
if(!$Row3) { //쿼리 실패시 에러카운터 증가
	$error_count++;
	$error_msg = "설문결과 등록 오류";
}
//[2-3]설문결과 이몬 프로시져를 위한 데이타 넣기(환급여부가 '환급'과 '근로자훈련'일때만 등록)
if($ServiceType=="1" || $ServiceType=="4") {
	$ExamA_idx_Array = explode("|",$ExamA_idx);
	$ExamA_answer_Array = explode("|",$ExamA_answer);

	$i = 0;
	foreach($ExamA_idx_Array as $ExamA_idx_Array_value) {
		$Sql4 = "SELECT * FROM PollBank WHERE idx=$ExamA_idx_Array_value";
		$Result4 = mysqli_query($connect, $Sql4);
		$Row4 = mysqli_fetch_array($Result4);

		if($Row4) {
		    $idx = $Row4['idx'];
		    $Gubun = $Row4['Gubun'];
		    $ExamType = $Row4['ExamType'];
		    $Question = $Row4['Question'];
		    $Example01 = $Row4['Example01'];
		    $Example02 = $Row4['Example02'];
		    $Example03 = $Row4['Example03'];
		    $Example04 = $Row4['Example04'];
		    $Example05 = $Row4['Example05'];
		    $OrderByNum = $Row4['OrderByNum'];
		}
		$Answer = $ExamA_answer_Array[$i];
		$Sql5= "INSERT INTO SurveyAnswerProcedure(Gubun, Exam_idx, OrderByNum, ID, LectureCode, LectureTerme_idx, Answer, AnswerText, RegDate) 
                    VALUES('$Gubun', $idx, $OrderByNum, '$LoginMemberID', '$LectureCode', $LectureTerme_idx, '$Answer', '', NOW())";
		$Row5 = mysqli_query($connect, $Sql5);

		if(!$Row5) { //쿼리 실패시 에러카운터 증가
			$error_count++;
			$error_msg = "프로시져 등록 오류";
		}
	   $i++;
	}
}
//[2-4]설문조사 완료로 데이터 업데이트
$Sql6 = "UPDATE Study SET Survey='Y' WHERE Seq=$Study_Seq AND ID='$LoginMemberID'";
$Row6 = mysqli_query($connect, $Sql6);
if(!$Row6) { //쿼리 실패시 에러카운터 증가
	$error_count++;
}


//[3]에러카운트 확인
if($error_count>0) {
	mysqli_query($connect, "ROLLBACK");
	$Proc = "N";
	$msg = "설문 처리중 문제가 발생했습니다.";
}else{
	mysqli_query($connect, "COMMIT");
	$Proc = "Y";
	$msg = "설문 제출이 완료 되었습니다.";
}
?>
<script type="text/javascript">
	<?if($Proc=="Y") {?>
	alert("<?=$msg?>");
	top.location.href="/public/mypage/survey_complete.html";		
	<?}?>
	<?if($Proc=="N") {?>
	//alert("<?=$msg?>");
	alert("<?=$error_msg?>");
	<?}?>
</script>
</body>
</html>
<?
mysqli_close($connect);
?>
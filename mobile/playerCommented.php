// <?php 
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = Replace_Check_XSS2($id);
$lectureCode = Replace_Check_XSS2($lectureCode);
$studySeq = Replace_Check_XSS2($studySeq);
$chapterSeq = Replace_Check_XSS2($chapterSeq);
$contentsIdx = Replace_Check_XSS2($contentsIdx);
$progressIdx = Replace_Check_XSS2($progressIdx);
$mode = Replace_Check_XSS2($playMode);
$chapterNum = Replace_Check_XSS2($progressStep);

$response = [];

$needMobileAuth = "N"; //모바일 본인인증 필요여부 초기값
$needMobileAuth2 = "N"; //모바일 본인인증 필요여부 초기값
$authMsg = ""; //본인인증 메시지 초기값
$evalCd = ""; //본인인증시 필요한 값

/* 기존의 쿼리 (아래의 $sql4select로 합침. 2024.08.01 Sua)---------------------------------------------------------------------------------------------
-- 과정 정보 구하기
SELECT * FROM Course WHERE LectureCode='$LectureCode';+++

-- 차시 정보 구하기
SELECT * FROM Contents WHERE idx='$Contents_idx';
SELECT * FROM Study WHERE LectureCode='$LectureCode' AND Seq=$Study_Seq;

-- 테스트 아이디 구하기
SELECT * FROM Member WHERE ID='$LoginMemberID';

-- 최종 수강내역 정보 구하기
SELECT * FROM Progress WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq AND Chapter_Seq=$Chapter_Seq AND Contents_idx=$Contents_idx;

-- 하부 컨테츠 수 구하기
SELECT COUNT(*) FROM ContentsDetail WHERE Contents_idx=$Contents_idx AND UseYN='Y';

-- 현재 과정 본인 인증 횟수
SELECT COUNT(*) FROM UserCertOTP WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq;

-- 금일 수강한 차시수
SELECT COUNT(*) FROM Progress WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Study_Seq=$Study_Seq AND LEFT(RegDate,10)='".date('Y-m-d')."' AND Contents_idx <> $Contents_idx;

-- $mode가 'S'일 때
SELECT * FROM ContentsDetail WHERE Contents_idx=$Contents_idx AND UseYN='Y' ORDER BY OrderByNum ASC, Seq ASC LIMIT 0,1;

-- $mode가 'C'일 때
SELECT * FROM ContentsDetail WHERE Contents_idx=$Contents_idx AND Seq=$ContentsDetail_Seq;
------------------------------------------------------------------------------------------------------------------------------------------------- */
if($mode == 'S'){ //처음부터 수강하기
    $modeCondition = "cd2.UseYN = 'Y' ORDER BY cd2.OrderByNum, cd2.Seq LIMIT 0,1";
}elseif($mode == 'C'){ // 이어보기    
    $modeCondition = "cd2.Seq = p.ContentsDetail_Seq";
}

$sql4select = "SELECT c.ContentsName, c.attachFile, c.CompleteTime, c.ChapterLimit, c.ContentsURLSelect AS ContentsURLSelectGlobal, 
                		s.ServiceType, s.LectureTerme_idx, cn.ContentsTitle, CEIL((cn.LectureTime * 60) / 2) AS LectureTimeSec, m.TestID,
                		IFNULL(p.LastStudy,0) AS LastStudy, IFNULL(p.Progress, 0) AS Progress, IFNULL(p.StudyTime, 0) AS StudyTime,
                		COUNT(cd.Seq) AS ContentsDetailCnt, COUNT(otp.Seq) AS MobileAuthCnt, COUNT(p2.idx) AS TodayProgressCnt,
                		cd2.Seq AS ContentsDetailSeq, cd2.ContentsType, cd2.ContentsURLSelect, cd2.ContentsURL, cd2.ContentsURL2, cd2.Caption
                FROM Course c 
                INNER JOIN Study s ON c. LectureCode = s.LectureCode AND s.Seq = 23
                INNER JOIN Chapter ch ON ch.LectureCode = c.LectureCode AND ch.Seq = 409
                INNER JOIN Contents cn ON cn.idx = ch.Sub_idx
                INNER JOIN Member m ON m.ID = s.ID
                LEFT JOIN Progress p ON p.Study_Seq = s.Seq AND p.Chapter_Seq = ch.Seq AND p.Contents_idx = cn.idx
                LEFT JOIN ContentsDetail cd ON cd.Contents_idx = cn.idx AND cd.UseYN ='Y'
                LEFT JOIN UserCertOTP otp ON otp.Study_Seq = s.Seq
                LEFT JOIN Progress p2 ON p2.Study_Seq = s.Seq AND DATE(p2.RegDate) = CURDATE() AND p2.Contents_idx <> cn.idx
                LEFT JOIN ContentsDetail cd2 ON cd2.Contents_idx = cn.idx AND $modeCondition";
$query4select = mysqli_query($connect, $sql4select);
$row = mysqli_fetch_assoc($query4select);

if($row && ($ContentsURLSelect == 'A' && $ContentsURLSelectGlobal == 'A')){
    extract($row);
    $response['contentsName'] = $ContentsName; //과정명
    $response['attachFile'] = $attachFile; //학습자료
    $CompleteTime *= 60; //진도시간 기준 (분 단위를 초 단위로 변환)
    $response['completeTime'] = $CompleteTime; //진도시간 기준
    $response['chapterLimit'] = $ChapterLimit; //차시제한 여부
    $response['contentsTitle'] = $ContentsTitle; //차시명
    $response['serviceType'] = $ServiceType;
    $response['lectureTermeIdx'] = $LectureTerme_idx; //mtop할 때 필요
    $response['studyTime'] = $StudyTime;
    $response['contentsDetailSeq'] = $ContentsDetailSeq;
    $response['contentsType'] = $ContentsType; //A = 플래시 강의, B = 동영상 강의
    $response['contentsDetailCnt'] = $ContentsDetailCnt;
    
    
    if($Progress >= 100){
        $_SESSION["EndTrigger"] = "Y";
        // Brad (2021.11.28) : IsPlaying Session 초기화
        $_SESSION['IsPlaying'] = 'N';
    }
    
    //ContentsURLSelectGlobal는 mode 구분 후 사용됌.// 컨텐츠 URL 주경로, 예비경로 선택 여부 A:주, B:예비
    //LastStudy는 mode 구분로직 거쳐서 값 변경된 후에 response에 담기.
    //mode 내에서 LastStudy가 0이면 null인거임.
    
    if($mode == 'S'){ // 처음부터 수강하기 STARTS ---------------------------------------------------------------
        
        if($ContentsURLSelectGlobal=="B") { // 컨텐츠 URL '예비경로' 선택
            $alert = 'Unexpected Result : ContetnsURLSelectGlobal is B (L104)';
//             $ContentsURLSelect = "B";
//             $ContentsURL = $ContentsURL2;
        }else{ // 컨텐츠 URL '주경로' 선택
            if($ContentsURLSelect=="A") {
                $ContentsURL = $ContentsURL;
            }else{
                $alert = 'Unexpected Result : ContetnsURLSelect is B (L111)';
//                 $ContentsURL = $ContentsURL2;
            }
        }
        if($ContentsDetailCnt>1) { //하부 컨텐츠가 2개 이상인 경우
            $playNum = "0"; // 이 값 나중에 프론트에 넘겨주기 원본에서는 PlayNum
        }
        
        if(($ChapterLimit == "Y" && $TestID == "N") && $TodayProgressCnt > 7){
            $msg = 'over8Chapters';
            $response['msg'] = $msg;
            exit;
        }
        
        
    }elseif($mode == 'C'){ //이어보기 STARTS ------------------------------------------------------------------
        
        if($LastStudy == 0) {
            $LastStudy = $ContentsURL;
        }
        
        if($ContentsType=="A") { //플래시 강의
            $ContentsURL = $LastStudy;
        }elseif($ContentsType=="B") { // 동영상 강의
            if($ContentsURLSelectGlobal=="B") { // 컨텐츠 URL '예비경로' 선택
                $alert = 'Unexpected Result : ContetnsURLSelectGlobal is B (L135)';
//                 $ContentsURLSelect = "B";
//                 $ContentsURL = $ContentsURL2;
            }else{ // 컨텐츠 URL '주경로' 선택
                if($ContentsURLSelect=="A") {
                    $ContentsURL = $ContentsURL;
                }else{
//                      $ContentsURL = $ContentsURL2;
                    $alert = 'Unexpected Result : ContetnsURLSelect is B (L144)';
                }
            }
        }
        
        if($ContentsDetailCnt>1) { //하부 컨텐츠가 2개 이상인 경우
            $playNum = $LastStudy;
//             if(!$playNum) {  //쿼리에서 IFNULL로 변경수정하여 상관없는 코드인듯
//                 $playNum = "0";
//             }
        }
    } //mode 관련 로직들 END----------------------------------------------------------------------------------------------------
    
    if($MobileAuthCnt<1 && ($ServiceType==1 || $ServiceType==4)) { //과정 인증내역이 없으면 본인인증 필요 (과정당 1회만 인증) 입과시 인증
        $needMobileAuth = "Y";
        $evalCd = "00";
        $authMsg = "과정입과 시 본인인증이 필요합니다.";
    }else{
        
        ##### 오늘 첫수강, 8차시 단위 인증 추가 (1,9,17...차시) #####
        if(($ServiceType==1 || $ServiceType==4) && empty($_SESSION['PlayStudyAuth_'.$studySeq.$chapterSeq])){	//환급과정 체크 && 해당 차시를 인증 안한경우
                
            //오늘 첫수강인지 체크
            $sql4auth1 = "SELECT COUNT(*) AS cnt1 FROM Progress WHERE ID='$id' AND LectureCode='$lectureCode' AND Study_Seq=$studySeq AND DATE(RegDate) = CURDATE()";
            $query4auth1 = mysqli_query($connect, $sql4auth1);
            $row = mysqli_fetch_assoc($query4auth1);
            $progressCnt1 = $row['cnt1'];
            if($progressCnt1<1){
                $needMobileAuth2 = "Y";
                $authMsg = "학습 진행 시 본인인증이 필요합니다.";
            }
            
            //1,9,17...차시인경우
            if($chapterNum % 8 == 1){
                //최초 수강인지 체크
                $sql4auth2 = "SELECT COUNT(*) cnt2 FROM Progress WHERE ID='$id' AND LectureCode='$lectureCode' AND Chapter_Number='$chapterNum' AND Study_Seq=$studySeq";
                $query4auth2 = mysqli_query($connect, $sql4auth2);
                $row = mysqli_fetch_assoc($query4auth2);
                $progressCnt2 = $row['cnt2'];
                
                if($progressCnt2<1){
                    $needMobileAuth2 = "Y";
                    $authMsg = "학습 진행 시 본인인증이 필요합니다.";
                }
            }
        }
        
    }
    $response['needMobileAuth'] = $needMobileAuth;
    $response['needMobileAuth2'] = $needMobileAuth2;
    $response['authMsg'] = $authMsg;
    
    //아래는 본인인증할 때 사용 PlayStudyAuth()
    $response['chapterNum'] = $chapterNum;
    $response['lectureCode'] = $lectureCode;
    $response['studySeq'] = $studySeq;
    $response['contentsIdx'] = $contentsIdx;
    $response['mode'] = $mode;
    $response['evalCd'] = $evalCd;
    
    
    
    if($ContentsDetailCnt>1) { //하부 컨텐츠가 2개 이상인 경우
        $alert = 'Unexpected Result : More than 1 ContentsDetailCnt Present (L207)';
    }else{ //하부 컨텐츠가 1개인 경우
        
        if($ContentsType=="A") { //플레쉬 강의의 경우
            $playPath = $FlashServerURL.$ContentsURL;
//             $PlayerFunction = $playPath;
        }elseif($ContentsType=="B") { //동영상 강의의 경우
            if($ContentsURLSelect=="A") {// 컨텐츠 URL '주경로' 선택
                
                $playPath = $MovieServerURL.$ContentsURL; 
                
                if($Caption) {
                    $playPath .= '|'.$Caption;
//                 }else{
//                     $PlayerFunction = $playPath;
                }
                
            }else{
                
//                 $playPath = $ContentsURL.'|'.$LastStudy;
//                 $PlayerFunction = $playPath;
                
            }
        }
    }
 /*   
    $studySeq = Replace_Check_XSS2($studySeq);
    $chapterSeq = Replace_Check_XSS2($chapterSeq);
    $contentsIdx = Replace_Check_XSS2($contentsIdx);
    $progressIdx = Replace_Check_XSS2($progressIdx);
    $mode = Replace_Check_XSS2($playMode);
    $chapterNum = Replace_Check_XSS2($progressStep);
   */ 
    
    
    
    
    //환급과정은 기초차시에 수강시간을 기준으로 완료(100%) 시간을 계산하고 비환급과정은 단과컨텐츠의 진도시간 기준을 기준으로 완료시간을 계산한다.
    if($ServiceType==1 || $ServiceType==4) {
        $CompleteTime = $LectureTimeSec;
    }else{
        $CompleteTime = $CompleteTime; //Brad(2021.12.19) : 비환급수강 완료시간 수정
    }
    
    //위의 $msg도 넘겨주기        
    //if(($ChapterLimit == "Y" && $TestID == "N") && $TodayProgressCnt > 7){
    //$msg = 'over8Chapters';
    
    //alert도 넘겨주기..? 근데 너무 많아서, 그냥 ContetnsURLSelectGlobal, ContetnsURLSelect 이게 A가 아니면 오류 뜨게 해야할 것 같음.
}else{// 강의 정보에 오류가 발생했습니다. 알림
    $response['contentsName'] = 'error';
}


// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>
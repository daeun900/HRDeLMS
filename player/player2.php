<?
##########################################################################
# 차시있는 컨텐츠
##########################################################################
include "../include/include_function.php"; //DB연결 및 각종 함수 정의
include "../include/flex_login_check.php"; //로그인 여부 체크
include "../include/play_check.php";// Brad (2021.11.27): 이중 학습 방지 

$_SESSION["EndTrigger"] = "N"; //EndTrigger 초기화

$Chapter_Number = Replace_Check_XSS2($Chapter_Number); //해당과정의 차시순서
$LectureCode    = Replace_Check_XSS2($LectureCode);    //강의코드

## 과정 정보 구하기 ########################################################################
$Sql = "SELECT * FROM CourseFlex WHERE LectureCode='$LectureCode'";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);
if($Row) {
    $Course_idx   = $Row['idx'];           //과정 고유번호
    $ContentsName = $Row['ContentsName'];  //과정명
    $attachFile   = $Row['attachFile'];    //학습자료
    $ContentsURL  = $Row['ContentsURL'];   //컨텐츠URL
    $MobileURL    = $Row['MobileURL'];     //모바일URL
    $Keyword1     = $Row['Keyword1'];      //관심분야
    $EduGoal      = nl2br($Row['EduGoal']);//학습목표
    $Category1    = $Row['Category1'];     //상위카테고리
    $ContentsURLSelectGlobal = $Row['ContentsURLSelect']; //컨텐츠 URL 주경로, 예비경로 선택 여부 A:주, B:예비
}
## 과정 정보 구하기 ########################################################################

## 차시 정보 구하기 ########################################################################
//첫번째 차시인 경우
if($Chapter_Number == "1"){
    $Sql = "SELECT Sub_idx, Seq AS Chapter_Seq FROM Chapter WHERE LectureCode='$LectureCode' AND ChapterType='A' ORDER BY OrderByNum ASC LIMIT 0,1";
    $Result = mysqli_query($connect, $Sql);
    $Row = mysqli_fetch_array($Result);
    $Contents_idx = $Row['Sub_idx'];
    $Chapter_Seq = $Row['Chapter_Seq'];
    $Chapter_Number = $Contents_idx;

//첫번째 차시가 아닌 경우
}else{
    $Sql = "SELECT Sub_idx, Seq AS Chapter_Seq FROM Chapter WHERE LectureCode='$LectureCode' AND Sub_idx='$Chapter_Number' AND ChapterType='A' ORDER BY OrderByNum ASC LIMIT 0,1";
    $Result = mysqli_query($connect, $Sql);
    $Row = mysqli_fetch_array($Result);
    $Contents_idx = $Row['Sub_idx'];
    $Chapter_Seq = $Row['Chapter_Seq'];
}

$Sql = "SELECT * FROM Contents WHERE idx='$Contents_idx'";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);
if($Row) {
	$ContentsTitle = $Row['ContentsTitle']; //차시명
	$LectureTime = ceil(($Row['LectureTime'] * 60) / 2); //수강시간
	$Expl01 = nl2br($Row['Expl01']); //차시 목표
	$Expl02 = nl2br($Row['Expl02']); //훈련 내용
	$Expl03 = nl2br($Row['Expl03']); //학습 활동
}
## 차시 정보 구하기 ########################################################################

## 최종 수강내역 정보 구하기 ########################################################################
$Sql = "SELECT * FROM ProgressFlex
        WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Chapter_Seq=$Chapter_Seq";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);
if($Row) {    
    $ContentsDetail_Seq = $Row['ContentsDetail_Seq'];
    $LastStudy = $Row['LastStudy'];
    $Progress = $Row['Progress'];
    $StudyTime = $Row['StudyTime'];
    $mode = "C";
}else{    
    $Progress = 0;
    $StudyTime = 0;
    $mode = "S";
}
if($Progress>=100) {
    $_SESSION["EndTrigger"] = "Y";
    // Brad (2021.11.28) : IsPlaying Session 초기화
    $_SESSION['IsPlaying'] = 'N';
}
## 최종 수강내역 정보 구하기 ########################################################################

## 컨텐츠 정보 구하기 ###################################################################
//하부 컨테츠 수 구하기
$Sql = "SELECT COUNT(*) FROM ContentsDetail WHERE Contents_idx=$Contents_idx AND UseYN='Y'";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);
$ContentsDetail_count = $Row[0];
//강의 처음부터 시작
if($mode == "S"){
    $Sql = "SELECT * FROM ContentsDetail WHERE Contents_idx=$Contents_idx AND (ContentsType='A' OR ContentsType='B') ORDER BY OrderByNum ASC, Seq ASC LIMIT 0,1";
    $Result = mysqli_query($connect, $Sql);
    $Row = mysqli_fetch_array($Result);    
    if($Row) {
        $ContentsDetail_Seq = $Row['Seq'];
        $ContentsType = $Row['ContentsType'];
        $ContentsURLSelect = $Row['ContentsURLSelect'];
        $ContentsURL = $Row['ContentsURL'];
        $ContentsURL2 = $Row['ContentsURL2'];
        $Caption = $Row['Caption']; //자막 파일
        
        if($ContentsURLSelectGlobal=="B") {
            $ContentsURLSelect = "B";
            $ContentsURL = $ContentsURL2;
        }else{
            if($ContentsURLSelect=="A") $ContentsURL = $ContentsURL; else $ContentsURL = $ContentsURL2;
        }
    }else{
?>
<script type="text/javascript">
	alert("강의 정보에 오류가 발생했습니다.(-1)");
	location.reload();
</script>
<?
    	exit;
	}
}

//이어보기로 시작.(이미 이전에 강의 봤음)
if($mode == "C"){
    $Sql = "SELECT * FROM ContentsDetail WHERE Contents_idx=$Contents_idx";
    $Result = mysqli_query($connect, $Sql);
    $Row = mysqli_fetch_array($Result);
    if($Row) {
		$ContentsDetail_Seq = $Row['Seq'];
		$ContentsType = $Row['ContentsType'];
		$ContentsURLSelect = $Row['ContentsURLSelect'];
		$ContentsURL = $Row['ContentsURL'];
		$ContentsURL2 = $Row['ContentsURL2'];
		$Caption = $Row['Caption']; //자막 파일

		if(!$LastStudy || $LastStudy=="blank") $LastStudy = $ContentsURL;
		if($ContentsType=="A") $ContentsURL = $LastStudy;
		if($ContentsType=="B") {
			if($ContentsURLSelectGlobal=="B") {
				$ContentsURLSelect = "B";
				$ContentsURL = $ContentsURL2;
			}else{
			    if($ContentsURLSelect=="A") $ContentsURL = $ContentsURL; else $ContentsURL = $ContentsURL2;
			}
		}
	}else{
?>
<script type="text/javascript">
	alert("강의 정보에 오류가 발생했습니다.");
	location.reload();
</script>
<?
    	exit;
	}
}
## 컨텐츠 정보 구하기 ###################################################################
?>

<input type="hidden" name="Chapter_Number" id="Chapter_Number" value="<?=$Chapter_Number?>">
<input type="hidden" name="LectureCode" id="LectureCode" value="<?=$LectureCode?>">
<input type="hidden" name="Chapter_Seq" id="Chapter_Seq" value="<?=$Chapter_Seq?>">
<input type="hidden" name="Contents_idx" id="Contents_idx" value="<?=$Contents_idx?>">
<input type="hidden" name="ContentsDetail_Seq" id="ContentsDetail_Seq" value="<?=$ContentsDetail_Seq?>">
<input type="hidden" name="CompleteTime" id="CompleteTime" value="<?=$LectureTime?>">
<?if($ContentsDetail_count>1) {?>
<input type="hidden" name="MultiContentType" id="MultiContentType" value="Y">
<?}else{?>
<input type="hidden" name="MultiContentType" id="MultiContentType" value="N">
<?}?>
<input type="hidden" name="timeChk" id="timeChk">
<?
## 플레쉬 강의의 경우 ###################################################################
if($ContentsType=="A") {
    (strpos($ContentsURL, "https://") == 0)?  $PlayPath = $ContentsURL : $PlayPath = $FlashServerURL.$ContentsURL;
?>
<div id="CloseBtn" style="z-index:10000"><!-- Javascript:DataResultClose(); -->
	<a href="Javascript:StudyProgressCheck2('End', 'Y', '<?=$ContentsURLSelect?>');" style="position:absolute; top:56px; right:40px; color: #fff;display: flex;align-items: center; font-size: 19px;">학습종료<img src="/hrdflex/img/common/btnbul_close02.png"></a>
</div>
<div class='flashArea'>
	<div class="field"><?=$Keyword1?></div>
	<div class="title" id="drag_play"><?=$ContentsName?></div>
	<input type="hidden" name="ContentsType" id="ContentsType" value="A">
	<iframe name="mPlayer" id="mPlayer"  src="<?=$PlayPath?>" border="0" frameborder="0" onload="resizeIframe(this)" scrolling="no"></iframe>
	<div>
    	<span>수강시간</span>
    	<input type="hidden" name="StartTime" id="StartTime" value="<?=$StudyTime?>"><!-- 초기 수강시간 시작 초 -->
    	<strong><span id="StudyTimeNow">00:00:00</span></strong>
	</div>
	<span>학습 목차</span>
	<ul class="index">
		<?
		$i=1;
    	$SqlA = "SELECT a.Sub_idx, b.ContentsTitle , b.LectureTime 
                FROM Chapter a
                LEFT JOIN Contents b ON b.idx = a.Sub_idx 
                LEFT JOIN ContentsDetail c ON c.Contents_idx = a.Sub_idx 
                WHERE a.LectureCode = '$LectureCode'";
    	$QUERYA = mysqli_query($connect, $SqlA);
    	if($QUERYA && mysqli_num_rows($QUERYA)){
    	    while($ROWA = mysqli_fetch_array($QUERYA)){
    	        $ContentsTitleA = $ROWA['ContentsTitle'];
    	        $LectureTimeA   = $ROWA['LectureTime'];
    	        $Sub_idxA       = $ROWA['Sub_idx'];
    	?>
    	<li>
			<div class='title' <?if($Chapter_Number==$Sub_idxA){?> style="font-weight:400; color:#ffe119;" <?}?>><?=$i?>. <?=$ContentsTitleA?></div>
			<div class='right'>
				<?
				$Sql1 = "SELECT StudyTime FROM ProgressFlexLog WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Contents_idx='$Sub_idxA' ORDER BY idx DESC Limit 0,1";
				$Result1 = mysqli_query($connect, $Sql1);
				$Row1 = mysqli_fetch_array($Result1);
				$StudyTime1 = gmdate("i분s초", $Row1[0]);
				?>
				<div class='time'><span><?=$StudyTime1?></span> / <?=$LectureTimeA?>분</div>
				<button onclick="Javascript:ContentsPlayer2('<?=$LectureCode?>', '<?=$Sub_idxA?>');">학습시작</button>
			</div>
		</li>
    	<?
                $i++;
    	    }
    	}
    	?>
	</ul>
	<span>학습 목표</span>
	<ul class="goal"><li><?=$EduGoal?></li></ul>
</div>
<div class='recommendArea'>
	<span>추천 컨텐츠</span>
	<ul>
		<?
    	$SqlB = "SELECT * FROM CourseFlex WHERE Category1=$Category1 AND LectureCode != '$LectureCode' LIMIT 5";
    	$QUERYB = mysqli_query($connect, $SqlB);
    	if($QUERYB && mysqli_num_rows($QUERYB)){
    	    while($ROWB = mysqli_fetch_array($QUERYB)){
    	        $ContentsNameA = $ROWB['ContentsName'];
    	        $PreviewImageA = $ROWB['PreviewImage'];
    	        $ContentsTimeA = $ROWB['ContentsTime'];
    	        $LectureCodeA  = $ROWB['LectureCode'];
    	        $Chapter       = $ROWB['Chapter'];
    	        
    	        $PreviewImageView = "/upload/Course/".$PreviewImageA;
    	?>
    	<li>
            <?if($Chapter == "0"){?>
			<div class="img" style="background-image: url(<?=$PreviewImageView?>);"onclick="Javascript:ContentsPlayer('<?=$LectureCodeA?>');"></div>
			<?}else{?>
			<div class="img" style="background-image: url(<?=$PreviewImageView?>);" onclick="Javascript:ContentsPlayer2('<?=$LectureCodeA?>', '1');"></div>
			<?}?>
            <div class="title"><?=$ContentsNameA?></div>
            <div class="time"><?=$ContentsTimeA?>분</div>
        </li>
    	<?
    	    }
    	}
    	?>
	</ul>  
</div>

<style>
	#DataResult::-webkit-scrollbar {display: none;}

	.flashArea{background-color:#2f2f2e;  padding:50px; border-radius:30px;color: #fff;}
	.flashArea .title{font-size:30px; line-height:60px; font-weight:600}

	.flashArea > span{display:block; font-size:20px; font-weight:600; padding:15px 0; margin-top:30px; border-bottom:1px solid #ccc}
	.flashArea .index li{display:flex; padding:14px 0; border-bottom:1px solid #ccc; justify-content: space-between;}
	.flashArea .index li .title{font-size:18px; line-height:40px; font-weight:300; color:#ccc;}
	.flashArea .index li .right{display:flex; align-items:center; justify-content:space-between; width:250px}
	.flashArea .index li .right .time{color:#ccc}
	.flashArea .index li .right .time span{ color:#ffe119}
	.flashArea .index li .right button{background-color:transparent; border:1px solid #ffe119; padding:8px 35px; border-radius:50px; color:#ffe119; font-size:16px; transition:all .3s ease;}
	.flashArea .index li .right button:hover{background-color:#ffe11924}

	.flashArea .goal li{padding:15px 0; font-weight:300; font-size:18px;}

	.recommendArea{position: fixed;right: 20px;top: 0px; width: 300px; height: 100%; background-color: #2f2f2e; border-radius: 30px; padding: 20px;color: #fff;}
	.recommendArea span{font-size: 20px; position:relative; display:block; margin-bottom:20px}
	.recommendArea span::before{content:''; position:absolute; bottom:-10px; left:-13px;width:112px; height:1px; background-color:#fff;}
	.recommendArea ul{height:100%; overflow-y:scroll;}
	.recommendArea ul::-webkit-scrollbar {display: none;}
	.recommendArea ul li{margin-bottom:20px;}
	.recommendArea .img{width: 264px; height:152px; background-size:cover;}
	.recommendArea .title{text-align:center;padding:7px 0 2px 0; }
	.recommendArea .time{text-align:center; color:#ffe119}
</style>
<?
}
## 플레쉬 강의의 경우 ###################################################################

## 동영상 강의의 경우 ###################################################################
if($ContentsType=="B") {
    (strpos($ContentsURL, "https://") == 0)?  $PlayPath = $ContentsURL : $PlayPath = $MovieServerURL.$ContentsURL;
?>
<div id='CloseBtn' style='position:relative; z-index:10000'>
		<a href='Javascript:DataResultClose();' style='position:absolute; top:10px; right:10px'>
			<img src='/img/common/btnbul_close02.png'>
		</a>
	</div>
	<div class='flashArea' style='background-color:#000;  padding:50px; border-radius:30px'>
		<div class='field'>직무역량  ｜  의료</div>
		<div class='title' style='font-size:30px; line-height:60px; font-weight:600'>NCS기반 병원고객관리 v1</div>
		<input type='hidden' name='ContentsType' id='ContentsType' value='B'>
		<video id='mPlayer' width='1020' height='655' controls autoplay>
			<source src='".$PlayPath."' type='video/mp4'>
		</video>
	</div>
<?
}
## 동영상 강의의 경우 ###################################################################
?>
<script type="text/javascript">
$(document).ready(function() {
	StudyProgressCheck2('Start','N','<?=$ContentsURLSelect?>'); //시작 진도 - Progress(차시진도), Study(수강내역) 모두 업데이트(트리거 통해 이몬에 등록)

	//수강 시간 초단위로 보여주는 부분
	setInterval(function(){
		var iframeTime = $('#mPlayer').contents().find('.time1 .playerText').html();
		var timeChk = $("#timeChk").val();

		//영상이 재생중일때만 수강시간 늘어남
		if(iframeTime != timeChk){
			StudyTimeCheck();
		}
		$("#timeChk").val(iframeTime);
	},1000);

	//60초 마다 진도 체크 
	setInterval(function(){
		StudyProgressCheck2('Middle','N','<?=$ContentsURLSelect?>'); //Progress(차시진도)만 업데이트
	},60000);

	//동영상 이어보기의 경우 해당 시간으로 이동
	<?if($mode=="C" && $ContentsType=="B" && $ContentsURLSelect=="A" && $Progress < 100) {?>
	setTimeout(function(){
		mPlayer.currentTime=<?=$LastStudy?>;
	},2000);
	<?}?>

	//제목 클리시, 이동 가능하도록
	$("#drag_play").css("cursor","move");
	$("#drag_play").mouseover(function(){
		$("div[id='DataResult']").draggable();
		$("div[id='DataResult']").draggable("option","disabled",false);
	})
	$("#drag_play").mouseleave(function(){
		$("div[id='DataResult']").draggable("option","disabled",true);
	});
});
</script>
<?
$MenuType = "F";
$PageName = "study2";
?>
<? include "./include/include_top.php"; ?>
<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function() {
	$("#ID").bind("focus", function() {
		$(document).keydown(function(e) {
			if(e.keyCode===13) {
				StudySearch2(1);
			}
		});
	});
	$("#LectureCode").select2();
	changeSelect2Style();
});
$(window).load(function() {
	LectureTermeSearch();
});
</SCRIPT>
	<div class="contentBody">
    	<h2>학습관리</h2>
        <div class="conZone">
            <!-- 검색 -->
			<form name="search" id="search" method="POST">
    			<input type="hidden" name="SubmitFunction" id="SubmitFunction" value="StudySearch2(1)">
    			<input type="hidden" name="ctype" id="ctype" value="B">
    			<input type="hidden" name="LectureStart" id="LectureStart">
				<input type="hidden" name="LectureEnd" id="LectureEnd">
    			<div class="neoSearch">
    				<ul class="search">
    					<li>
    						<span class="item01">수강기간</span>&nbsp;
    						<span id="SearchGubunResult1">
                                <select name="SearchYear" id="SearchYear" onchange="LectureTermeSearch()" style="width:100px">
        							<?for($i=2018;$i<=date("Y");$i++) {?>
        							<option value="<?=$i?>" <?if($i==date("Y")) {?>selected<?}?>><?=$i?>년</option>
        							<?}?>
                              	</select>&nbsp;
        						<select name="SearchMonth" id="SearchMonth" onchange="LectureTermeSearch()" style="width:80px">
        							<option value="">전체</option>
        							<?for($i=1;$i<=12;$i++) {?>
        							<option value="<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?if($i==date("m")) {?>selected<?}?>><?=$i?>월</option>
        							<?}?>
                              	</select>
                                <span id="LectureTermeResult"></span>
    						</span>
                            <span id="SearchGubunResult2" style="display:none"><input type="text" name="CompanyName" id="CompanyName" style="width:450px" placeholder="사업주명 입력" onfocus="CompanySearchAutoCompleteGo();" onKeyup="CompanySearchAutoCompleteGo();"></span>
    						<div id="CompanyAutoCompleteResult" class="auto_complete_layer" style="display:none"></div>
    						<span id="CompanySearchLectureTermeResult"></span>
    					</li>
    					<li>
    						<span class="item01">실시회차</span>
                        	<input type="text" name="OpenChapter" id="OpenChapter" style="width:100px">
    					</li>
    				</ul>
    				<ul class="search">
    					<li>
    						<span class="item01">이름,ID</span>
                        	<input type="text" name="ID" id="ID" style="width:150px">
    					</li>
    					<li>
    						<span class="item01">영업자 이름,ID</span>
                        	<input type="text" name="SalesID" id="SalesID" style="width:150px" <?if($AdminWrite != "Y") {?> value='<?=$_SESSION["LoginAdminID"]?>' readonly <? }?> >
    					</li>
    					<li>
    						<span class="item01">진도율</span>
                       	  	<input type="text" name="Progress1" id="Progress1" style="width:40px"> % ~ <input type="text" name="Progress2" id="Progress2" style="width:40px"> %
                    	</li>
                        <li>
                        	<span class="item01">총점</span>
                       	  	<input type="text" name="TotalScore1" id="TotalScore1" style="width:40px"> 점 ~ <input type="text" name="TotalScore2" id="TotalScore2" style="width:40px"> 점
                        </li>
                        <li>
                        	<span class="item01">첨삭정렬</span>
                        	<select name="TutorStatus" id="TutorStatus" style="width:150px">
    							<option value="">전체</option>
    							<option value="N">미첨삭만 보기</option>
    						</select>
                        </li>
    				</ul>
                    <ul class="search">
    					<li>
    						<span class="item01 select2-label">과정명</span>
                        	<select name="LectureCode" id="LectureCode" >
    							<option value="">-- 과정 전체 --</option>
    							<?
    							$SQL = "SELECT * FROM Course WHERE Del='N' AND PackageYN='N' AND ctype='B' ORDER BY ContentsName ASC";
    							$QUERY = mysqli_query($connect, $SQL);
    							if($QUERY && mysqli_num_rows($QUERY)){
    								$i = 1;
    								while($Row = mysqli_fetch_array($QUERY)){
    								    if($Row['PackageYN']=="Y") $PackageYN = " (패키지)"; 
    								    else $PackageYN = "";
    							?>
    							<option value="<?=$Row['LectureCode']?>"><?=$Row['ContentsName']?> | <?=$Row['LectureCode']?> <?=$PackageYN?></option>
    							<?
    								    $i++;
    								}
    							}
    							?>
    						</select>
                        </li>
    					<?if($LoginAdminDept=="A" || $LoginAdminDept=="B") { ?>
    					<li>
    						<span class="item01">교강사</span>
                        	<select name="Tutor" id="Tutor" style="width:150px">
    							<option value="">전체</option>
    							<?
    							$SQL = "SELECT * FROM StaffInfo WHERE Dept='C' AND Del='N' ORDER BY Name ASC";
    							$QUERY = mysqli_query($connect, $SQL);
    							if($QUERY && mysqli_num_rows($QUERY)){
    								$i = 1;
    								while($Row = mysqli_fetch_array($QUERY)){    
    							?>
    							<option value="<?=$Row['ID']?>"><?=$Row['Name']?> | <?=$Row['ID']?></option>
    							<?
    								    $i++;
    								}
    							}
    							?>
    						</select>
                        </li>
    					<?}?>
    					<li>
    						<span class="item01">실명인증 여부</span>
    						<select name="certCount" id="certCount" style="width:150px">
    							<option value="">전체</option>
    							<option value="Y">인증 완료</option>
    							<option value="N">미인증</option>
    						</select>
                        </li>
    				</ul>
                    <ul class="search">
    					<li>
    						<span class="item01">수료여부</span>
                        	<select name="PassOk" id="PassOk" style="width:150px">
    							<option value="">전체</option>
    							<option value="Y">수료</option>
    							<option value="N">미수료</option>
    						</select>
                        </li>
    					<li>
    						<span class="item01">중간평가</span>
    						<select name="MidStatus" id="MidStatus" style="width:150px">
    							<option value="">전체</option>
    							<option value="C">응시(채점완료)</option>
    							<option value="Y">응시(채점대기중)</option>
    							<option value="N">미응시</option>
    						</select>
                        </li>
    					<li>
    						<span class="item01">최종평가</span>
    						<select name="TestStatus" id="TestStatus" style="width:150px">
    							<option value="">전체</option>
    							<option value="C">응시(채점완료)</option>
    							<option value="Y">응시(채점대기중)</option>
    							<option value="N">미응시</option>
    						</select>
                        </li>
                        <li>
                        	<span class="item01">과제</span>
    						<select name="ReportStatus" id="ReportStatus" style="width:150px">
    							<option value="">전체</option>
    							<option value="C">응시(채점완료)</option>
    							<option value="Y">응시(채점대기중)</option>
    							<option value="N">미응시</option>
    							<option value="R">반려</option>
    						</select>
                        </li>
                        <li>
                        	<span class="item01">모사답안</span>
    						<select name="ReportCopy" id="ReportCopy" style="width:150px">
    							<option value="">전체</option>
    							<option value="D">모사답안의심</option>
    							<option value="Y">모사답안</option>
    						</select>
                        </li>
    					<li>
    						<span class="item01">페이징 설정</span>
    						<select name="PageCount" id="PageCount" style="width:150px">
    							<option value="10">10개</option>
    							<option value="30" selected>30개</option>
    							<option value="50">50개</option>
    							<option value="70">70개</option>
    							<option value="100">100개</option>
    							<option value="120">120개</option>
    							<option value="150">150개</option>
    							<option value="200">200개</option>
    						</select>
                        </li>
                        <li> <!-- 240724 yjkwon -->
                            <span class="item01">재응시여부</span>
                            <select name="ReExam" id="ReExam" style="width:150px">
                                <option value="">전체</option>
                                <option value="Y">재응시</option>
                            </select>
                        </li>
    				</ul>
                    
    				<div class="mt10 tc pb5">
                    	<button type="button" name="SearchBtn" id="SearchBtn" class="btn btn_Blue" style="width:200px;" onclick="StudySearch2(1)"><i class="fas fa-search"></i> 검색</button>					
    				</div>
    				<?if($AdminWrite=="Y") {?>
    				<div class="pb5" style="margin:10px auto 0 auto; text-align:center; ">
    					<button type="button" name="ExcelBtn" id="ExcelBtn" class="btn btn_Green line" style="width:200px;" onclick="Study2Excel();">검색 결과 <br><i class="fas fa-file-excel"></i> 엑셀 출력</button>
    					<button type="button" name="SendBtn" id="SendBtn" class="btn btn_Blue line" style="width:200px;" onclick="StudyCheckedKakaoTalk('Start')">체크 항목 <br><i class="xi-message"></i> 개강문자보내기</button>
    					<button type="button" name="SendBtn2" id="SendBtn2" class="btn btn_Blue line" style="width:200px;" onclick="StudyCheckedKakaoTalk('Auth')">체크 항목 <br><i class="xi-message"></i> 본인인증문자보내기</button>
    					<!-- <button type="button" name="SendBtn3" id="SendBtn3" class="btn btn_Blue line" style="width:200px;" onclick="StudyCheckedEduManagerMail()">체크 항목 <br><i class="xi-mail"></i> 교육담당자 안내 메일 발송</button>  -->
    					<button type="button" name="DeleteBtn" id="DeleteBtn" class="btn btn_DGray line" style="width:200px;" onclick="StudyCheckedDelete()">체크 항목 <br>삭제</button>
    				</div>
    				<div class="pb5" style="margin:0 auto 0 auto; text-align:center; ">
    					<button type="button" name="ChangeBtn" id="ChangeBtn" class="btn btn_DGray line" style="width:200px;" onclick="Study2CheckedEnd()">체크 항목 <br>수강마감 처리</button>
    					<button type="button" name="ChangeBtn" id="ChangeBtn" class="btn btn_DGray line" style="width:200px;" onclick="StudyOpenChapterChangeBatch('B')">현재 검색 조건으로 <br>실시회차 변경</button>					
    					<button type="button" name="ChangeBtn2" id="ChangeBtn2" class="btn btn_DGray line" style="width:200px;" onclick="StudyPriceResettingBatch('B')">현재 검색 조건으로 <br>교육비 재설정</button>
    					<button type="button" name="ChangeBtn" id="ChangeBtn" class="btn btn_DGray line" style="width:200px;" onclick="StudySalesChangeBatch('B')">현재 검색 조건으로 <br>영업담당 변경</button>
    					<button type="button" name="ChangeBtn2" id="ChangeBtn2" class="btn btn_DGray line" style="width:200px;" onclick="StudyTutorChangeBatch('B')">현재 검색 조건으로 <br>교강사 변경</button>
    				</div>
    				<?}else{?>
    				<div class="pb5" style="margin:10px auto 0 auto; text-align:center; ">
    					<button type="button" name="ExcelBtn" id="ExcelBtn" class="btn btn_Green line" style="width:200px;" onclick="Study2Excel();">검색 결과 <br><i class="fas fa-file-excel"></i> 엑셀 출력</button>
    				</div>
					<?}?>
    			</div>
			</form>
			<!-- //검색 -->
            
			<div id="SearchResult"><br><br><center><strong>검색 조건을 선택하세요.</strong></center></div>
		</div>
	</div>
</div>
<!-- Content // -->

<!-- Footer -->
<? include "./include/include_bottom.php"; ?>
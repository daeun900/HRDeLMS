function sign_in(){
	let id = document.signInForm.id.value;
	let pwd = document.signInForm.pwd.value;
	let data = { id, pwd };
	$.ajax({
			url: './sign_in.php', 
			type: 'POST',
			data: JSON.stringify(data),
			contentType: 'application/json',
			success: (response) => {
				console.log(response.result);
				document.getElementById('sign_in_return_value').innerHTML = response.result;
				document.getElementById('userNm').innerHTML = response.name;
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});		
}

function sign_out(){
	$.ajax({
			url: './sign_out.php', 
			type: 'POST',
			success: (response) => {
				console.log(response.result);
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});		
}

function signInCheck() {
	$.ajax({
				url: './sign_in_check.php', 
				type: 'POST',
				success: (response) => {
					console.log(response.result);
				},
				error: (jqXHR, textStatus, errorThrown) => {
				    console.log('Error:', textStatus, errorThrown);
				}
		});		
}

function get_lecture_list(){
	let id = document.lectureListForm.id.value;
	let data = { id };
	$.ajax({
			url: './lecture_list.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response.data);
				document.getElementById('response_data').innerHTML = response.data;
//				document.getElementById('lecture_title').innerHTML = response.title;
//				document.getElementById('class_num').innerHTML = response.classNum;
//				document.getElementById('progress_num').innerHTML = response.progressNum;
//				document.getElementById('progress_percent').innerHTML = response.progressPercent;
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function get_lecture_detail(){
	let id = document.lectureDetailForm.id.value;
	let seq = document.lectureDetailForm.seq.value;
	let data = { id, seq };
	$.ajax({
			url: './lecture_detail.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('lecture_title').innerHTML = response.title;
				document.getElementById('lecture_start').innerHTML = response.lectureStart;
				document.getElementById('lecture_end').innerHTML = response.lectureEnd;
				document.getElementById('days_left').innerHTML = response.daysLeft;
				document.getElementById('professor').innerHTML = response.professor;
				document.getElementById('class_num').innerHTML = response.classNum;
				document.getElementById('class_status').innerHTML = response.classStatus;
				document.getElementById('progress_num').innerHTML = response.progressNum;
				document.getElementById('chapter').innerHTML = response.chapter;
				document.getElementById('progress_percent').innerHTML = response.progressPercent;
				document.getElementById('attach_file').innerHTML = response.attachFile;
				
				document.getElementById('chapter_info').innerHTML = response.chapterInfo;
				document.getElementById('return_back').innerHTML = response.returnBack;
				
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function lecture_file_download(){
	let lectureCode = document.lectureFileDownloadForm.lectureCode.value;
	let data = { lectureCode };
	$.ajax({
			url: './lecture_file_download.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function get_notice_list(){
	let data = {};
	$.ajax({
			url: './notice_list.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('noti_info1').innerHTML = response.notiInfo1;
				document.getElementById('noti_info2').innerHTML = response.notiInfo2;
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function get_notice_detail(){
	let idx = document.noticeDetailForm.noti_idx.value;
	let data = { idx };
	$.ajax({
			url: './notice_detail.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('title').innerHTML = response.title;
				document.getElementById('reg_date').innerHTML = response.regDate;
				document.getElementById('hit').innerHTML = response.hit;
				document.getElementById('content').innerHTML = response.content;
				document.getElementById('file_name1').innerHTML = response.fileName1;
				document.getElementById('real_file_name1').innerHTML = response.realFileName1;
				document.getElementById('file_name2').innerHTML = response.fileName2;
				document.getElementById('real_file_name2').innerHTML = response.realFileName2;
				document.getElementById('file_name3').innerHTML = response.fileName3;
				document.getElementById('real_file_name3').innerHTML = response.realFileName3;
				document.getElementById('file_name4').innerHTML = response.fileName4;
				document.getElementById('real_file_name4').innerHTML = response.realFileName4;
				document.getElementById('file_name5').innerHTML = response.fileName5;
				document.getElementById('real_file_name5').innerHTML = response.realFileName5;
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function get_faq_array(){
	let data = { };
	$.ajax({
			url: './faq_array.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('faq_array').innerHTML = response.faqArray;
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function get_faq_search_list(){
	let searchedWord = document.faqSearchListForm.searched_word.value;
	let data = { searchedWord, type:'S' };
	$.ajax({
			url: './faq_list.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('faq_title').innerHTML = response.faqInfo;
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function get_faq_category_list(){
	let category = document.faqCategoryListForm.category.value;
	let data = { category, type:'C' };
	$.ajax({
			url: './faq_list.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('faq_title2').innerHTML = response.faqInfo;
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function get_faq_detail(){
	let idx = document.faqDetailForm.idx.value;
	let data = { idx };
	$.ajax({
			url: './faq_detail.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('faq_detail').innerHTML = response.content;
			},
			error: (jqXHR, textStatus, errorThrown) => {
			    console.log('Error:', textStatus, errorThrown);
			}
	});
}

function get_ask_array(){
	let data = { };
	$.ajax({
			url: './ask_array.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('ask_array').innerHTML = response.askArray;
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function insert_ask(){
	let id = document.askForm.id.value;
	let name = document.askForm.name.value;
	let pass_code_info = document.askForm.pass_code_info.value;
	let type = document.askForm.type.value;
	let title = document.askForm.title.value;
	let content = document.askForm.content.value;
	let data = { id, name, pass_code_info, type, title, content };
	$.ajax({
			url: './ask.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response.result);
				document.getElementById('insert_ask_result').innerHTML = response.result;
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function get_ask_list(){
	let id = document.askListForm.id.value;
	let data = { id };
	$.ajax({
			url: './ask_list.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response.askList);
				document.getElementById('ask_list').innerHTML = response.askList;
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function get_ask_answer(){
	let idx = document.askDetailForm.idx.value;
	let data = { idx };
	$.ajax({
			url: './ask_answer.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('ask_answer').innerHTML = response.answer;
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function show_player(){
	let id = document.playerForm.id.value;
	let lectureCode = document.playerForm.lectureCode.value;
	let studySeq = document.playerForm.studySeq.value;
	let chapterSeq = document.playerForm.chapterSeq.value;
	let contentsIdx = document.playerForm.contentsIdx.value;
	let playMode = document.playerForm.playMode.value;
	let progressStep = document.playerForm.progressStep.value;
	let data = { id, lectureCode, studySeq, chapterSeq, contentsIdx, playMode, progressStep };
	$.ajax({
			url: './player.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
//				document.getElementById('able2Play').innerHTML = response.able2Play;
//				document.getElementById('contentsName').innerHTML = response.contentsName;
//				document.getElementById('attachFile').innerHTML = response.attachFile;
//				document.getElementById('contentsTitle').innerHTML = response.contentsTitle;
//				document.getElementById('serviceType').innerHTML = response.serviceType;
//				document.getElementById('lectureTermeIdx').innerHTML = response.lectureTermeIdx;
//				document.getElementById('studyTime').innerHTML = response.studyTime;
//				document.getElementById('contentsDetailSeq').innerHTML = response.contentsDetailSeq;
//				document.getElementById('contentsType').innerHTML = response.contentsType;
//				document.getElementById('contentsDetailCnt').innerHTML = response.contentsDetailCnt;
//				document.getElementById('completeTime').innerHTML = response.completeTime;
//				document.getElementById('needMobileAuth').innerHTML = response.needMobileAuth;
//				document.getElementById('needMobileAuth2').innerHTML = response.needMobileAuth2;
//				document.getElementById('authMsg').innerHTML = response.authMsg;
//				document.getElementById('chapterNum').innerHTML = response.chapterNum;
//				document.getElementById('lectureCode').innerHTML = response.lectureCode;
//				document.getElementById('studySeq').innerHTML = response.studySeq;
//				document.getElementById('contentsIdx').innerHTML = response.contentsIdx;
//				document.getElementById('mode').innerHTML = response.mode;
//				document.getElementById('chapterSeq').innerHTML = response.chapterSeq;
//				document.getElementById('playPath').innerHTML = response.playPath;
//				document.getElementById('contentsURLSelect').innerHTML = response.contentsURLSelect;
//				document.getElementById('lastStudy').innerHTML = response.lastStudy;
//				document.getElementById('playNum').innerHTML = response.playNum;
//				document.getElementById('progress').innerHTML = response.progress;
//				document.getElementById('alert').innerHTML = response.alert;
//				document.getElementById('caption').innerHTML = response.caption;
//				console.log('dkdk',response.able2Play);
			},
			error: () => {
			    console.log('Error:');
			}
	});
}
function store_progress(){
	let id = document.lectureProgressForm.id.value;
	let chapterNumber = document.lectureProgressForm.chapterNumber.value;
	let lectureCode = document.lectureProgressForm.lectureCode.value;
	let studySeq = document.lectureProgressForm.studySeq.value;
	let chapterSeq = document.lectureProgressForm.chapterSeq.value;
	let contentsIdx = document.lectureProgressForm.contentsIdx.value;
	let contentsDetailSeq = document.lectureProgressForm.contentsDetailSeq.value;
	let progressTime = document.lectureProgressForm.progressTime.value;
	let lastStudy = document.lectureProgressForm.lastStudy.value;
	let completeTime = document.lectureProgressForm.completeTime.value;
	let progressStep = document.lectureProgressForm.progressStep.value;
	let data = { id, chapterNumber, lectureCode, studySeq, chapterSeq, contentsIdx, contentsDetailSeq, progressTime, lastStudy, completeTime, progressStep };
	$.ajax({
			url: './store_progress.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function show_test_result(){
	let input = document.returnTestForm.input.value;
	let data = { input };
	$.ajax({
			url: './return_test.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('result').innerHTML = response.result;
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

//입과시 motp
function store_motp(){
	let id = document.playerCertInsertForm.id.value;
	let lectureCode = document.playerCertInsertForm.lectureCode.value;
	let studySeq = document.playerCertInsertForm.studySeq.value;
	let certType = document.playerCertInsertForm.certType.value;
	let AGTID = document.playerCertInsertForm.AGTID.value;
	let COURSE_AGENT_PK = document.playerCertInsertForm.COURSE_AGENT_PK.value;
	let CLASS_AGENT_PK = document.playerCertInsertForm.CLASS_AGENT_PK.value;
	let m_Ret = document.playerCertInsertForm.m_Ret.value;
	let m_retCD = document.playerCertInsertForm.m_retCD.value;
	let m_trnID = document.playerCertInsertForm.m_trnID.value;
	let m_trnDT = document.playerCertInsertForm.m_trnDT.value;
	let data = { id, lectureCode, studySeq, certType, AGTID, COURSE_AGENT_PK, CLASS_AGENT_PK, m_Ret, m_retCD, m_trnID, m_trnDT };
	$.ajax({
			url: './player_cert_insert.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function give_session_motp(){
	let chapterNum = document.playerCaptchaSessionForm.chapterNum.value;
	let lectureCode = document.playerCaptchaSessionForm.lectureCode.value;
	let studySeq = document.playerCaptchaSessionForm.studySeq.value;
	let chapterSeq = document.playerCaptchaSessionForm.chapterSeq.value;
	let data = { chapterNum, lectureCode, studySeq, chapterSeq };
	$.ajax({
			url: './player_captcha_session.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function create_encoded_data(){
	let lectureCode = document.createEncodedDataForm.lectureCode.value;
	let data = { lectureCode };
	$.ajax({
			url: './create_encoded_data.php', //여기 php 파일 명부터 정하기.
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function checkplus_success(){
	let encData = document.checkplusSuccessForm.encData.value;
	let data = { encData };
	$.ajax({
			url: './checkplus_success.php',
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
			},
			error: () => {
			    console.log('Error:');
			}
	});
}

function checkplus_fail(){
	let encData = document.checkplusFailForm.encData.value;
	let data = { encData };
	$.ajax({
			url: './checkplus_fail.php',
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
			},
			error: () => {
			    console.log('Error:');
			}
	});
}











function give_session(){
	let id = document.giveSessionForm.id.value;
	let data = { id };
	$.ajax({
			url: './give_session.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('session_value1').innerHTML = response.sessionValue;
			},
			error: () => {
			    console.log('Error:');
			}
	});
}
function check_session(){
	let data = { };
	$.ajax({
			url: './check_session.php', 
			type: 'POST', 
			data: JSON.stringify(data), 
			contentType: 'application/json', 
			success: (response) => {
				console.log(response);
				document.getElementById('session_value2').innerHTML = response.sessionValue2;
			},
			error: () => {
			    console.log('Error:');
			}
	});
}



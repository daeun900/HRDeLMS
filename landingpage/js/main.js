
$(document).ready(function () {
  var timeonoff; // 타이머 처리
  var imageCount = $(".gallery ul li").size(); // 이미지 총 개수
  var cnt = 1; //이미지 순서
  var onoff = true; // true:타이머 동작중 , false:동작하지 않을 때

  $('.btn1').addClass('on').css({
    background: 'rgba(255, 255, 255, 0.5)',
    width: '100px',
  });

  $('.gallery .link1').fadeIn('fast');
  $('.gallery .link1  .up').delay(350).animate({top: 0, opacity: 1},'slow');

  $('.dock .num').text(`0${cnt}`);
  $('.dock .max').text(`0${imageCount}`);


  function moveg() {
    cnt++; // cnt 값을 증가시켜 다음 이미지를 가리키도록 함
    if (cnt > imageCount) cnt = 1; // cnt가 이미지 수를 넘으면 다시 1로 초기화
  
    const currentImage = $('.gallery li:visible');
    const nextImage = $(`.gallery .link${cnt}`);
  
    currentImage.css({ zIndex: 1 });
    nextImage.css({ zIndex: 2, opacity: 0, display: 'block' }); 
    
    nextImage.animate({ opacity: 1 }, 1000);
  
    currentImage.animate({ opacity: 0 }, 1000, function() {
      currentImage.hide();
    });
  
    // 버튼 업데이트
    $('.mbutton').css({
      background: 'rgba(255, 255, 255, 0.5)',
      width: '100px',
    }).removeClass('on');
  
    $(`.btn${cnt}`).css({
      background: 'rgba(255, 255, 255, 0.5)',
      width: '100px',
    }).addClass('on');
  
    // 텍스트 애니메이션
    $('.gallery li .up').css({ top: '30px', opacity: 0 });
    $(`.gallery .link${cnt} .up`).delay(350).animate({ top: 0, opacity: 1 }, 'slow');
    $('.dock .num').text(`0${cnt}`);
  }
  
  

  timeonoff = setInterval(moveg, 4000);

  $('.mbutton').click(function (event) {
    var $target = $(event.target); // 클릭한 버튼 $target == $(this)
    clearInterval(timeonoff);

    $('.gallery li').hide();

    if ($target.is('.btn1')) {
      cnt = 1;
    } else if ($target.is('.btn2')) {
      cnt = 2;
    } else if ($target.is('.btn3')) {
      cnt = 3;
    } else if ($target.is('.btn4')) {
      cnt = 4;
    }

    $(`.gallery .link${cnt}`).fadeIn('fast');

    $('.dock .num').text(`${cnt}`);

    $('.mbutton').css({
      background: 'rgba(255, 255, 255, 0.5)',
      width: '24px',
    }).removeClass('on');

    $(`.btn${cnt}`).css({
      background: 'rgba(255, 255, 255, 0.5)',
      width: '100px',
    }).addClass('on');

    $(`.gallery li .up`).css({top: '30px', opacity: 0,});
    $(`.gallery .link${cnt} .up`).delay(350).animate({top: 0, opacity: 1},'slow');

    if (cnt == imageCount) cnt = 0;
    timeonoff = setInterval(moveg, 4000);

    if (onoff == false) {
      onoff = true;
      $(this).html('<i class="ph-fill ph-pause"></i>');
    }
  });

  // stop/play 버튼 클릭시 타이머 동작/중지
  $('.ps').click(function () {
    if (onoff == true) {
      clearInterval(timeonoff);
      $(this).html('<i class="ph-fill ph-play"></i>');
      $('.visual .gallery img').css('animation-play-state','paused')
      onoff = false;
    } else {
      timeonoff = setInterval(moveg, 4000);
      $(this).html('<i class="ph-fill ph-pause"></i>');
      onoff = true;
      $('.visual .gallery img').css('animation-play-state','running')
    }
  });

});
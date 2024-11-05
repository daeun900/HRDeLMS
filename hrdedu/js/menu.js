$(document).ready(function() {

  var on_off=false;  //false(오버아웃)  true(오버)


     $(window).on('scroll',function(){
          var scroll = $(window).scrollTop(); 
          var height = $(document).height();

          if(scroll>150){
            if(height>1300){
              $('.navArea').addClass('fixed')}
            }else{
              $('.navArea').removeClass('fixed')     
          }
      
       ;
});
  
  //2depth 열기/닫기
   $('ul.dropdownmenu').hover(
      function() { 
         $('ul.dropdownmenu li.menu ul').css('height','400px'); 
         $('.nav_back').css('height','400px'); //모든 서브를 다 열어라
      },function() {
         $('ul.dropdownmenu li.menu ul').css('height','0px');
         $('.nav_back').css('height','0px'); //모든 서브를 다 닫아라
    });
   
    //1depth 효과
    $('ul.dropdownmenu li.menu').hover(
      function() { 
          $(this).find('a:first').css('color','#4185E1');
          $(this).find('ul').css('background-color','#fafafa');
      },function() {
          $(this).find('a:first').css('color','#333');
          $(this).find('ul').css('background-color','#fff');
    });


    //2depth효과
    $('ul.dropdownmenu li.menu ul li a').hover(
      function() { 
          $(this).css('color','#4185E1');
      },function() {
         $(this).css('color','#666');
    });
  }
  );

  //MOBILE


$(document).ready(function() {
  var op = false;  //네비가 열려있으면(true) , 닫혀있으면(false)
    
  $(".menuicon").click(function(e) { //메뉴버튼 클릭시
      e.preventDefault();
      var documentHeight =  $(document).height(); //전체페이지의 높이
      $(".navArea_m").css('height',documentHeight); 

     if(op==false){ //메뉴가 닫혀있는 상태에서 클릭하면
       $(".navArea_m").animate({right:0,opacity:1}, 'fast');
       op=true;
     }else{ //메뉴가 열려있는 상태에서 클릭하면
       $(".navArea_m").animate({right:'-100%',opacity:0}, 'fast');
       op=false;
     }

  });
   //2depth 메뉴 교대로 열기 처리 
   var onoff=[false,false,false,false,false,false]; //가정법 false(2depth서브가 닫혀있는 상태) true(2depth서브가 열려있는 상태)
   var arrcount=onoff.length; //배열의 개수 6개
   
   //console.log(arrcount);
   
   $('.navArea_m .depth1 > a').click(function(e){ //1depth메뉴를 클릭하면
     e.preventDefault();
       var ind=$(this).parents('.depth1').index(); //0~5
       //var ind=$(this).index('.navArea_m .depth1 h3 a')
      // console.log(ind);
       
      if(onoff[ind]==false){ //1depth 메뉴의 서브가 닫혀있으면
       //$('.navArea_m .depth1 ul').hide();
       $(this).parents('.depth1').find('ul').slideDown('slow'); //자신의 서브를 열어라
       $(this).parents('.depth1').siblings('li').find('ul').slideUp('fast'); //자신을 제외한 모든 서브를 닫아라
        for(var i=0; i<arrcount; i++) onoff[i]=false; // 모든 배열값을 fALSE (닫힘) 상태로 만들고
        onoff[ind]=true; //자신에 대한 (열린)배열만 TRUE로 변경
          
        $(this).css('color','#005793')
        $('.depth1 span').html('<i class="fa-solid fa-chevron-down"></i>'); 
        $(this).find('span').html('<i class="fa-solid fa-chevron-up"></i>'); 
          
      }else{//1depth 메뉴의 서브가 열려있으면
          $(this).css('color','#333')
        $(this).parents('.depth1').find('ul').slideUp('fast'); //서브 닫기 
        onoff[ind]=false;   
          
        $(this).find('span').html('<i class="fa-solid fa-chevron-down"></i>');   
        
      }
   });    
 
 });
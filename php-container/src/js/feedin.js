$(function() {
	setTimeout(function(){
		$('.logo_fadein p').fadeIn(1000);
	},500);
	setTimeout(function(){
		$('.logo_fadein').fadeOut(1000);
	},2500);
});

$(window).scroll(function (){
	$('.fadein').each(function(){
		var elemPos = $(this).offset().top,
		scroll = $(window).scrollTop(),
		windowHeight = $(window).height();

			if (scroll > elemPos - windowHeight + 150){
				$(this).addClass('scrollin');
			}
	});
});


$(window).on('load', function () {
  //テキストごとにspanタグを生成する
  var element = $(".typing");
  var thisChild = ""
  element.each(function () {
    var text = $(this).html();
    var textbox = "";
    text.split('').forEach(function (target) {
      if (target !== " ") {
        textbox += '<span>' + target + '</span>';
      } else {
        textbox += target;
      }
    });
    $(this).html(textbox);
  });

$(window).scroll(function (){
  // タイピングアニメーション
   $(element).each(function () {
      thisChild = $(this).children(); //生成したspanタグを取得
      thisChild.each(function (i) {
        var time = 100;
        $(this).delay(time * i).fadeIn(time);
      });
  	});
		});
});

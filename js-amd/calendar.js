define([
  'jquery',
  'jquery/qtip'
], function($) {

  "use strict";

  $(function() {
    ajaxCalendar();
    dayTips();
    monthTip();
  });
  
  function monthTip() {
    var $tip = $('.js-cal-month-tip');
    if (!$tip.length) return;
    $tip.qtip(
        {
          content: $('.js-cal-m-events').html(),
          style: { 
            width: 244,
            padding: 5
          },
          position: {
            corner: {
              target: 'bottomMiddle',
              tooltip: 'topMiddle'
            }
          }
        }
      );
  }
  
  function dayTips() {
    var $tips = $('.js-cal-tip');
    if (!$tips.length) return;
    
    var i = 0;
    var images = new Array();
    
    var target, tooltip, isLeft, isRight, isCenter, dow;
    var bodyWidth = $('body').width();
    
    var isMobile = bodyWidth < 400;
    
    $tips.each(function(){
      
      // Preload
      images[++i] = new Image();
			images[i].src = $(this).data('image');
      
      dow = $(this).data('dow');
      
      switch(true) {
        case isMobile:
          isLeft = dow == 0;
          isRight = dow >= 5;
          break;
        default:
          isLeft = dow <= 4;
          isRight = !isLeft;
      }
      
      isCenter = !isLeft && !isRight;
      if (isLeft) { target = 'bottomLeft'; tooltip = 'topLeft'; }
      if (isRight) { target = 'bottomRight'; tooltip = 'topRight'; }
      if (isCenter) { target = 'bottomMiddle'; tooltip = 'topMiddle'; }
      
      
      var cssStyle = isMobile ? 'width:234px; height:152px;' : 'max-width:250px; max-height:228px;'
      $(this).qtip(
        {
          content: '<img src="' + $(this).data('image') + '" alt="" style="' + cssStyle + '" />',
          style: { 
            width: isMobile ? 244 : 260,
            padding: 5
          },
          position: {
            corner: {
              target: target,
              tooltip: tooltip
            },
            adjust: {
              x: isLeft ? -1 : 1
            }
          }
        }
      );
    });
  }
  
  function ajaxCalendar() {
    $('.js-calendar-arrow').live('click', function(){
      var $calendarWrapper = $(this).parentsUntil('.js-calendar').parent();
      
      var requestyear = $calendarWrapper.data('requestyear');
      var requestmonth = $calendarWrapper.data('requestmonth');
      
      var curyear = $calendarWrapper.data('curyear');
      var curmonth = $calendarWrapper.data('curmonth');
      var curday = $calendarWrapper.data('curday');
      
      var iblocks = $calendarWrapper.data('iblocks');
      var showdow = $calendarWrapper.data('showdow');
      var urltemplate = $calendarWrapper.data('urltemplate');
      var direction = $(this).data('direction');
      
      var senderObj = {ajax:true, iblocks: iblocks, showdow: showdow, urltemplate:urltemplate, 
        requestyear: requestyear, requestmonth: requestmonth,
        curyear:curyear, curmonth:curmonth, curday:curday,
        direction: direction};

      $.ajax({
        type: "POST",
        url: "/ajax/calendar.php?format=ajax",
        data: senderObj,
        success: function(response) {
          if (response) $(".js-calendar-wrapper").html(response);
          dayTips();
          monthTip();
          }
        });
      });
  }
    
});


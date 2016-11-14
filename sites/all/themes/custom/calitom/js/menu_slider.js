(function($){
  Drupal.behaviors.menu_slider_behaviors = {
    attach: function(context, settings) {
      if(!($('nav ul.menu ul.dropdown-menu .nav-wrapper').length > 0)) {
        $('nav ul.menu > li').children('ul.dropdown-menu').wrapInner("<div class='nav-wrapper'></div>");
        $('nav .nav-wrapper').wrapInner("<div class='nav-wrapper-inner'></div>");
        $('<div class="hover-wrapper"><div class="hover-inner"></div></div>').insertBefore('nav .nav-wrapper-inner');
        $('<div class="hover-wrapper-2"><div class="hover-inner"></div></div>').insertBefore('nav .hover-wrapper');
      }

      function reset_hover_menu(hover_wrapper){
        hover_wrapper.removeClass('opened');
        hover_wrapper.parent().removeProp('style');
        hover_wrapper.find('.hover-inner').children().remove();
        if(hover_wrapper.hasClass('hover-wrapper')){
          $('span.back-arrow').remove();
        } else {
          hover_wrapper = hover_wrapper.parent().find('.hover-wrapper');
          inner = hover_wrapper.parent().find('.nav-wrapper-inner');
          setTimeout(function(){
            if (hover_wrapper.find('.hover-inner').height() > inner.parent().height()){
              inner.parent().height(hover_wrapper.find('.hover-inner').height());
            }
          }, 50);
        };
      }

      $('nav ul.menu > li > ul').mouseleave(function(){
        var hover_wrapper_2 = $('.hover-wrapper-2');
        var hover_wrapper = $('.hover-wrapper');
        reset_hover_menu(hover_wrapper_2);
        reset_hover_menu(hover_wrapper);
      });

      $(document).on('click', 'nav ul.menu > li .nav-wrapper-inner > li.expanded > a, nav ul.menu > li .nav-wrapper-inner > li.expanded > span', function(event) {
        event.preventDefault();
        
        var item = $(this).parent();
        var inner = item.parent();
        var hover_wrapper = inner.parent().find('.hover-wrapper');
        var items = item.find('> .dropdown-menu > li').clone();
        var title = item.find('> .dropdown-toggle').text();
        title = title.replace(' ￫', '');
        setTimeout(function(){
          if (hover_wrapper.find('.hover-inner').height() > inner.parent().height()){
            inner.parent().height(hover_wrapper.find('.hover-inner').height());
          }
        }, 50);

        if (!hover_wrapper.hasClass('opened')){
          hover_wrapper.find('.hover-inner').append('<h2 class="text-uppercase">' + title + '</h2>');
          hover_wrapper.find('.hover-inner').append(items);
          back_arrow = Drupal.t('×');
          inner.parent().parent().prepend("<span class='back-arrow'>" + back_arrow + "</span>");
          hover_wrapper.addClass('opened');

          $('span.back-arrow').click(function(){
            if(!$('nav ul.menu ul.dropdown-menu .nav-wrapper .hover-wrapper-2 li').length > 0) {
              reset_hover_menu(hover_wrapper);
            }
          });
        }
      });
      
      $(document).on('click', 'nav ul.menu > li .hover-wrapper .hover-inner > li.expanded > a, nav ul.menu > li .hover-wrapper .hover-inner > li.expanded > span', function(event) {
        event.preventDefault();

        var item = $(this).parent();
        var inner = item.parents('.hover-wrapper');
        var hover_wrapper = inner.parent().find('.hover-wrapper-2');
        var items = item.find('> .dropdown-menu > li').clone();
        var title = item.find('> .dropdown-toggle').text();
        title = title.replace(' ￫', '');
        setTimeout(function(){
          console.log(hover_wrapper.find('.hover-inner').height() + '-' +inner.parent().height());
          if (hover_wrapper.find('.hover-inner').height() > inner.parent().height()){
            inner.parent().height(hover_wrapper.find('.hover-inner').height());
          }
        }, 50);

        if (!hover_wrapper.hasClass('opened')){
          hover_wrapper.find('.hover-inner').append('<h2 class="text-uppercase">' + title + '</h2>');
          hover_wrapper.find('.hover-inner').append(items);
          hover_wrapper.addClass('opened');

          $('span.back-arrow').click(function(){
            reset_hover_menu(hover_wrapper);
          });
        }
      });
    }
  };
})(jQuery);
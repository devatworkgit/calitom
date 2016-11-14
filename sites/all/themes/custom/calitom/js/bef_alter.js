(function($){
  Drupal.behaviors.betterExposedFiltersSelectAllNone = {
    attach: function(context) {

      /*
       * Add Select all/none links to specified checkboxes
       */
      var selected = $('.form-checkboxes.bef-select-all-none:not(.bef-processed)');
      if (selected.length) {
        selected.each(function( index ) {
          category = $(this).parents('.views-exposed-widget').find('label').eq(0).text();
          var selAll = Drupal.t('Select All') + category;
          var selNone = Drupal.t('Select None');

          // Set up a prototype link and event handlers
          var link = $('<a class="bef-toggle" href="#">'+ selAll +'</a>')
          link.click(function(event) {
            // Don't actually follow the link...
            event.preventDefault();
            event.stopPropagation();

            if (selAll == $(this).text()) {
              // Select all the checkboxes
              $(this)
                .html(selNone)
                .siblings('.bef-checkboxes, .bef-tree')
                  .find('.form-item input:checkbox').each(function() {
                    $(this).attr('checked', true);
                    _bef_highlight(this, context);
                  })
                .end()

                // attr() doesn't trigger a change event, so we do it ourselves. But just on
                // one checkbox otherwise we have many spinning cursors
                .find('input[type=checkbox]:first').change()
              ;
            }
            else {
              // Unselect all the checkboxes
              $(this)
                .html(selAll)
                .siblings('.bef-checkboxes, .bef-tree')
                  .find('.form-item input:checkbox').each(function() {
                    $(this).attr('checked', false);
                    _bef_highlight(this, context);
                  })
                .end()

                // attr() doesn't trigger a change event, so we do it ourselves. But just on
                // one checkbox otherwise we have many spinning cursors
                .find('input[type=checkbox]:first').change()
              ;
            }
          });
          // Add link to the page for each set of checkboxes.
          $(this).addClass('bef-processed')
          // Clone the link prototype and insert into the DOM
          var newLink = link.clone(true);

          newLink.insertAfter($('.bef-checkboxes, .bef-tree', this));

          // If all checkboxes are already checked by default then switch to Select None
          if ($('input:checkbox:checked', this).length == $('input:checkbox', this).length) {
            newLink.click();
          }
        });
      }

      // Check for and initialize datepickers
      var befSettings = Drupal.settings.better_exposed_filters;
      if (befSettings && befSettings.datepicker && befSettings.datepicker_options && $.fn.datepicker) {
        var opt = befSettings.datepicker_options.dateformat ? {dateFormat: befSettings.datepicker_options.dateformat} : {};
        $('.bef-datepicker').datepicker(opt);
      }

    }                   // attach: function() {
  }; 
  
  /**
   * Adds/Removes the highlight class from the form-item div as appropriate
   */
  function _bef_highlight(elem, context) {
    $elem = $(elem, context);
    $elem.attr('checked')
      ? $elem.closest('.form-item', context).addClass('highlight')
      : $elem.closest('.form-item', context).removeClass('highlight');
  }
})(jQuery);
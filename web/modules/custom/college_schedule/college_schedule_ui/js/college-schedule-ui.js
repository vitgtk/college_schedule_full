/**
 * @file
 * College schedule UI behaviors.
 */

(function ($, Drupal) {
  Drupal.behaviors.collegeDashboardUi = {
    attach: function (context, settings) {


      $('.cs-board--day-area .cs-board--day-grid', context).sortable({
        handle: '.handle',
        connectWith: '.cs-board--day-grid',
        items: ".cs-board--hour-item",
        cursor: "move",
       // cancel: ".hour-type--lunch",
        change: function (event, ui) {
          // console.log(event);
        //  console.debug('change');
          ui.item.each(function () {
         //   console.log(this);
            $(this).addClass('cs-board--hour-item--change');
          })
        },
        update: function (event, ui) {
          console.debug('update');
          console.log(ui);

          var data = $(this).sortable('toArray', {attribute: 'data-hour-id'});
          console.log(data);

          ui.item.each(function () {
            console.log(this);
            $(this).addClass('cs-board--hour-item--update');
          })

        },
      });

    }

  };

})(jQuery, Drupal);

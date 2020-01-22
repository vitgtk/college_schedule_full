/**
 * @file
 * College schedule UI behaviors.
 */

(function ($, Drupal) {
  Drupal.behaviors.collegeDashboardUi = {
    attach: function (context, settings) {


      $('.schedule-day-item', context).sortable({
        handle: '.handle',
        connectWith: '.schedule-day-item',
        items: ".schedule-hour-item",
        change: function (event, ui) {
          console.log(event);
          console.log(ui);
        }
      });

    }

  };

})(jQuery, Drupal);

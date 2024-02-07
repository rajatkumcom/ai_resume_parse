(function ($, Drupal) {
  Drupal.behaviors.resumeParserAI = {
    attach: function (context, settings) {
      // _.once(function () {
      $('input[type="file"]').change(function () {
        $("#edit-ajax-submit").trigger("click");
      });
      // });
    },
  };
  // $(document).ready(function () {
  //   $('input[type="file"]').change(function () {
  //     $("#edit-ajax-submit").trigger("click");
  //   });
  // });
})(jQuery, Drupal);

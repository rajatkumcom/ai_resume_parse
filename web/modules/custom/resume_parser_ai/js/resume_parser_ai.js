(function ($, Drupal) {
  Drupal.behaviors.resumeParserAI = {
    attach: function (context, settings) {
      $('input[type="file"]').change(function () {
        $("#edit-ajax-submit").trigger("click");
      });
      $("#edit-field-job-role").change(function () {
        if ($("#edit-field-job-role").val() == "_none") {
          $("#resume-hide-job-role").hide();
          $(".divider").hide();
          $("#edit-footer").hide();
          $("#edit-actions").hide();
        } else {
          $("#resume-hide-job-role").show();
          $(".divider").show();
          $("#edit-footer").show();
          $("#edit-actions").show();
        }
      });
    },
  };
  $(document).ready(function () {
    if ($("#edit-field-job-role").val() == "_none") {
      $("#resume-hide-job-role").hide();
      $(".divider").hide();
      $("#edit-footer").hide();
      $("#edit-actions").hide();
    } else {
      $("#resume-hide-job-role").show();
      $(".divider").show();
      $("#edit-footer").show();
      $("#edit-actions").show();
    }
  });
})(jQuery, Drupal);

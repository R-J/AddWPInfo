// toggle visibility of #WPInfoContainer depending on #Form_CategoryID
$(document).ready(function() {
   var wPInfoCategoryIDs = jQuery.parseJSON(gdn.definition('WPInfoCategoryIDs'));
   $('#Form_CategoryID').change(function() {
      if (wPInfoCategoryIDs.indexOf($('#Form_CategoryID').val()) > -1) {
         $('#WPInfo').addClass('Hidden');
      } else {
         $('#WPInfo').removeClass('Hidden');
      }
   });
});

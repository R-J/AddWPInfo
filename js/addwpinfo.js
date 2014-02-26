// toggle visibility of #AddWPInfos depending on #Form_CategoryID
$(document).ready(function() {
   var addWPInfoCategoryIDs = jQuery.parseJSON(gdn.definition('AddWPInfoCategoryIDs'));
   $('#Form_CategoryID').on('change', function(e) {
      if (addWPInfoCategoryIDs.indexOf(e.target.value) > -1) {
         $('#AddWPInfo').addClass('Hidden');
      } else {
         $('#AddWPInfo').removeClass('Hidden');
      }
   });
});

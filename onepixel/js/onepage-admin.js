jQuery(function() {
	'use strict';
	
	var divPageId = jQuery('div#pageID');
	var divsCategoryId = jQuery('div[id*="categoryID_"]');
	jQuery('#postType-cmb-field-0').select2().on("change", function(event) {
		if (event.val == 'page') {
			divsCategoryId.val('');
			divPageId.show();
		} else {
			divPageId.val('');
			divsCategoryId.each(function(){
				var el = jQuery(this);
				if (this.id.indexOf(event.val) > 0) {
					el.show();
				} else {
					el.val('').hide();
				}
			});
		}
	});
	
});
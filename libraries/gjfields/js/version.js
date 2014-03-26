jQuery(document).ready(function($) {
	setTimeout(function() {
		var a = $("#style-form ul.nav.nav-tabs li");
		$(a).on('click', function() {
			var el = $(this);
			if (!el.hasClass('active')) {
				var href =  el.find('a').attr('href');
				$('input#jform_params__version').val(href);
			}
		});
		$('a[href='+$('input#jform_params__version').val()+']').tab('show');
    },100);
});

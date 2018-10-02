(function ($) {
	$(document).ready(function () {

		var woo_cao_select = '.woo_cao-field-mode';

		function woo_cao_check_mode() {
			$('.woo_cao-field-moded').parents('tr').hide();

			var woo_cao_mode = $(woo_cao_select).find(':selected').val();
			$('.woo_cao-field-' + woo_cao_mode).parents('tr').show();
		}

		$(woo_cao_select).on('change', function () {
			woo_cao_check_mode();
		});

		woo_cao_check_mode();
	});
})(jQuery);

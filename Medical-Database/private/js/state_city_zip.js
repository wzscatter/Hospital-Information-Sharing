$().ready(function () {
	$("#hstate").change(function () {
		var hstate = $("#hstate").val();
		$("#hstate option[value='0']").remove();
		$.ajax({
			url: "../../../private/certain_state.php",
			data: { state_code: hstate },
			type: 'get',
			async: false,
			success:
				function (msg) {
					var cityzip = jQuery.parseJSON(msg);
					var city_state = cityzip[0];
					var zip_state = cityzip[1];

					$('#hst_city').find('option').remove().end();
					$('#hzip').find('option').remove().end();
					$('#hst_city').append('<option value="0" selected>Please Select City</option>');
					$('#hzip').append('<option value="0" selected>Please Select City</option>');

					for (var i = 0; i < city_state.length; i++) {
						$('#hst_city').append('<option value="' + city_state[i].city + '">' + city_state[i].city + '</option>')
					}
				}
		});
	});

	$("#hst_city").change(function () {
		var hst_city = $("#hst_city").val();
		$.ajax({
			url: "../../../private/certain_city.php",
			data: { city: hst_city },
			type: 'get',
			async: false,
			success:
				function (msg) {
					var zip_city = jQuery.parseJSON(msg);

					$('#hzip').find('option').remove().end();
					$('#hzip').append('<option value="0" selected>Please Select Zipcode</option>');
					for (var i = 0; i < zip_city.length; i++) {
						$('#hzip').append('<option value="' + zip_city[i].zip + '">' + zip_city[i].zip + '</option>');
					}
				}
		});
	});
});
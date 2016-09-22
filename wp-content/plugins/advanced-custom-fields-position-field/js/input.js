jQuery( document ).ready(function( $ ) {

	var originalCountry = 0;
	var $body = $('body');

	//acf.add_action('ready append', function( $el ){
	$('select.select2').select2({
		width: 300
	});
	//});

	$body.on('change', 'select[id$=country_id]', function() {
		var $this              = $(this);
		var $list               = $this.parents('ul');
		var countryCity   = $list.find('select[name*="city_id"]');
		var regionSelect = $list.find('select[name*="region_id"]');

		if ($this.val() !== originalCountry) {
			originalCountry = $this.val();

			var optionsValues   = '';
			var $countryParent  = regionSelect.parents("li");

			$countryParent.find(".field-inner").css("visibility", "hidden");
			$countryParent.find(".css3-loader").show();

			get_related_regions($this.val(), function(response) {
				regionSelect.find('option:not(:first)').remove();
				countryCity.find('option:not(:first)').remove();
				$.each(response, function(k, v) {
					optionsValues += '<option value="'+k+'">'+v+'</option>';
				});
				regionSelect.append(optionsValues).trigger('change');
				$countryParent.find(".field-inner").css("visibility", "visible");
				$countryParent.find(".css3-loader").hide();
			});
		}
	});

	$body.on('change', 'select[id$=region_id]', function() {
		var $this              = $(this);
		var $list               = $this.parents('ul');
		var citySelect   = $list.find('select[name*="city_id"]');

		//if ($this.val() !== originalCountry) {
		originalCountry = $this.val();

		var optionsValues   = '';
		var $countryParent  = citySelect.parents("li");

		$countryParent.find(".field-inner").css("visibility", "hidden");
		$countryParent.find(".css3-loader").show();

		get_related_cities($this.val(), function(response) {
			citySelect.find('option:not(:first)').remove();
			$.each(response, function(k, v) {
				optionsValues += '<option value="'+k+'">'+v+'</option>';
			});
			citySelect.append(optionsValues).trigger('change');
			$countryParent.find(".field-inner").css("visibility", "visible");
			$countryParent.find(".css3-loader").hide();
		});


		//}
	});


	function get_related_regions(countryID, callback) {

		$.ajax({
			url              :   acfCountry.ajaxurl,
			type           :   'post',
			dataType  :   'json',
			data           :   {
				action      :   'get_regions',
				countryId : countryID
			},
			success    : function(response) {
				callback(response);
			}
		});

	}

	function get_related_cities(regionID, callback) {
		//var storageKey      = "cities"+countryID;
		//var cities          = getLocalStorage(storageKey);

		//if (cities !== null)
		//{
		//    callback(JSON.parse(cities));
		//}
		//else
		//{
		$.ajax({
			url              :   acfCountry.ajaxurl,
			type           :   'post',
			dataType  :   'json',
			data           :   {
				action      :   'get_cities',
				regionId : regionID
			},
			success    : function(response) {
				callback(response);
				//setLocalStorage(storageKey, JSON.stringify(response));
			}
		});
		//}
	}

	function setLocalStorage(key, value, expires) {
		if (expires==undefined || expires=='null') { var expires = 18000; } // default: 5h

		var date = new Date();
		var schedule = Math.round((date.setSeconds(date.getSeconds()+expires))/1000);

		localStorage.setItem(key, value);
		localStorage.setItem(key+'_time', schedule);
	}

	function getLocalStorage(key) {
		var date     = new Date();
		var current = Math.round(+date/1000);

		// Get Schedule
		var stored_time = localStorage.getItem(key+'_time');
		if (stored_time==undefined || stored_time=='null') { var stored_time = 0; }

		if (stored_time < current) {
			clearLocalStorage(key);
			return null;

		} else {
			return localStorage.getItem(key);
		}
	}

	function clearLocalStorage(key) {
		localStorage.removeItem(key);
		localStorage.removeItem(key+'_time');
	}


});
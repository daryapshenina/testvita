DAN_ready(function() {

	var map = null;
	var routingMode = 'masstransit';
	var userPosition = null;
	var targetPosition = []; // 53.213245, 50.18344

	var uiAddress = document.getElementById('route_address');
	var uiButton = document.getElementById('route_button');
	var uiPosX = document.getElementById('route_pos_x');
	var uiPosY = document.getElementById('route_pos_y');

	if(uiAddress === null || uiButton === null || uiPosX === null || uiPosY === null)
		return;

	targetPosition = [uiPosY.value, uiPosX.value];
	ymaps.ready(init);

	function init()
	{
		map = new ymaps.Map('route_map', {
			center: [55.76, 37.64],
			zoom: 7,
			controls: ['zoomControl', 'fullscreenControl']
		});

		map.behaviors.disable(['scrollZoom']);

		ymaps.geolocation.get({
			provider: 'auto',
			mapStateAutoApply: false
		}).then(function (_result) {

			userPosition = _result.geoObjects.get(0).geometry.getCoordinates();
			route();

			uiButton.addEventListener('click', function() {

				ymaps.geocode(uiAddress.value, {
					results: 1
				}).then(function (_result) {

					if(_result.geoObjects.getLength() === 0)
						return (uiAddress.style.outline = '3px solid #FF0000');

					uiAddress.style.outline = '';

					var object = _result.geoObjects.get(0);
					userPosition = object.geometry.getCoordinates();

					route();

				});

			});

		});
	}

	function route()
	{
		map.geoObjects.removeAll();

		var multiRoute = new ymaps.multiRouter.MultiRoute({
			referencePoints: [
				userPosition,
				targetPosition
			],
			params: {
				routingMode: routingMode
			}
		}, {
			boundsAutoApply: true,
			zoomMargin: 5
		});

		map.geoObjects.add(multiRoute);
	}

	Array.prototype.forEach.call(document.getElementsByName('route_type'), function(_element) {

		if(_element.checked)
			routingMode = _element.value;

		_element.addEventListener('change', function() {
			routingMode = this.value;
		});

	});

});

DAN_ready(function() {

	var map = null;
	var point = null;

	var uiY = document.getElementById('route_y');
	var uiX = document.getElementById('route_x');

	ymaps.ready(init);

	function init()
	{
		map = new ymaps.Map('route_map', {
			center: [55.76, 37.64],
			zoom: 7,
			controls: ['zoomControl', 'fullscreenControl']
		});

		createPoint();

		map.setCenter(point.geometry.getCoordinates(), 15);

		map.events.add('click', function(_event) {
			var coords = _event.get('coords');
			uiY.value = coords[0];
			uiX.value = coords[1];
			createPoint();
		});
	}

	function createPoint()
	{
		map.geoObjects.removeAll();

		point = new ymaps.GeoObject(
			{
				geometry:
				{
					type: "Point",
					coordinates: [uiY.value, uiX.value]
				},
				properties: {}
			},
			{
				preset: 'islands#blueHomeCircleIcon',
			}
		);

		map.geoObjects.add(point);
	}

});

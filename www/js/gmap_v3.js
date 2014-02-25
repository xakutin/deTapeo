
var g_oGeocoder = null;
var g_sLastAddress = '';
var g_oLastLocation = null;
var g_oDefaultLocation = null;
var g_oMap = null;
var g_aMarkers = Array();
var ZOOM = 16;

$(document).ready(function(){
   startMap();
});

//Crea el objeto de google maps
function createMap(){
	//Inicializamos el mapa
	g_oDefaultLocation = new google.maps.LatLng(40.413496,-3.779296);
	var aMapOptions = {
    zoom: ZOOM,
    center: g_oDefaultLocation,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
  	mapTypeControl: true,
    mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
    navigationControl: true,
  	scaleControl: true
  };
	g_oMap = new google.maps.Map($("#map").get(0), aMapOptions);
}

//Carga el punto guardado en la BD
function loadSavedPoint(){
	if (GMAP_LAT!=0 && GMAP_LNG!=0)
		g_oLastLocation = new google.maps.LatLng(GMAP_LAT,GMAP_LNG);
}

//Marca el punto en el mapa al cargarse la página
function markStartCenter(){
	if (g_oLastLocation){
		g_oMap.set_center(g_oLastLocation, ZOOM);
		markLocation(g_oLastLocation);
	}else if (GMAP_SEARCH_DIRECTION){
		searchDirection();
	}
}

//Inicializa el mapa y marca el punto de inicio
function startMap() {
	try{
		createMap();
		loadSavedPoint();
		markStartCenter();
		//Añadimos el evento de click para modificar la posición
		if (GMAP_LOAD_CLICK_LISTENER)
			addClickListener();
	}catch(e){}
}

//Crea un objeto Marker con las coordenadas que recibe por parámetro
function createMarker(oLocation){
	var oMarker = new google.maps.Marker({
      position: oLocation,
      map: g_oMap
  });
	g_aMarkers.push(oMarker);
	return oMarker;
}

//Borra todas las marcas del mapa
function clearMarkers(){
	while (g_aMarkers.length>0){
		var oMarker = g_aMarkers.pop();
		oMarker.set_map(null);
		oMarker = null;
	}
}

//Marca una localización en el mapa
function markLocation(oLocation){
	clearMarkers();
	g_oMap.set_center(oLocation, ZOOM);
	createMarker(oLocation);
	if (g_oMap.get_zoom()<8)
		g_oMap.set_zoom(ZOOM);
	g_oLastLocation = oLocation;
	//Guardamos la latitud y la longitud en los campos de un formulario
	$("#map_lat").val(oLocation.lat());
	$("#map_lng").val(oLocation.lng());

}

//Busca una dirección y la marca en el mapa.
//La dirección se encuentra en un textbox con id "address"
function searchDirection(){
	var sAddress = $("#address").val();
	if (!g_oGeocoder)
		g_oGeocoder = new google.maps.Geocoder();

	//Comprobamos que se ha introducido una dirección y no ha cambiado respecto a la última búsqueda
	if (g_oGeocoder && sAddress && g_sLastAddress!=sAddress){
		//Buscamos la dirección
		g_oGeocoder.geocode({address: sAddress, language: "es", country: "es"},
			function(results, status){
				if (status == google.maps.GeocoderStatus.OK && results.length) {
					if (status != google.maps.GeocoderStatus.ZERO_RESULTS){
						markLocation(results[0].geometry.location);
						g_sLastAddress=sAddress;
					}
				}
			}
		);
	}
}
///////////////////////////////////////////////////////////////////
// Events
//////////////////////////////////////////////////////////////////
//Añade el evento de hacer click en el mapa.
//Se moverá la marca del mapa al punto donde se ha pulsado click.
function addClickListener() {
	google.maps.event.addListener(g_oMap, 'click', function(event) {
    markLocation(event.latLng);
  });
}

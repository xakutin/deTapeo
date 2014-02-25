var g_oGeocoder = null;
var g_sLastAddress = '';
var g_oLastLocation = null;
var g_oDefaultLocation = null;
var g_oMap = null;
var ZOOM = 16;
var DEFAULT_LOCATION_ZOOM = 5;

//Descargamos el API de google maps, de forma asyncrona
//Al terminar la descarga se llama a la función de carga del Mapa		
$(document).ready(function(){
	google.load("maps", "2", {"callback" : startMap});  
});

//Descarga del mapa al cerrar la ventana
$(window).unload(function () {google.maps.Unload();});
	
//Crea el objeto de google maps	
function createMap(){
	//Inicializamos el mapa
	g_oMap = new google.maps.Map2($("#map").get(0), {'mapTypes': [G_NORMAL_MAP, G_HYBRID_MAP, G_SATELLITE_MAP, G_PHYSICAL_MAP]});	
	g_oMap.addControl(new google.maps.LargeMapControl());
	g_oMap.addControl(new google.maps.HierarchicalMapTypeControl());
		
	g_oDefaultLocation = new google.maps.LatLng(40.413496,-3.779296);
}

//Carga el punto guardado en la BD  
function loadSavedPoint(){
	if (GMAP_LAT!=0 && GMAP_LNG!=0)
		g_oLastLocation = new google.maps.LatLng(GMAP_LAT,GMAP_LNG);	
}

//Marca el punto en el mapa al cargarse la página
function markStartCenter(){
	if (g_oLastLocation){
		g_oMap.setCenter(g_oLastLocation, ZOOM);
		markLocation(g_oLastLocation);
	}else if (GMAP_SEARCH_DIRECTION){
		searchDirection();
	}else{
		g_oMap.setCenter(g_oDefaultLocation, DEFAULT_LOCATION_ZOOM);
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
	var oMarker = new google.maps.Marker(oLocation);	
	return oMarker;
}

//Marca una localización en el mapa	
function markLocation(oLocation){
	g_oMap.clearOverlays();
	if (!g_oMap.getCenter())
		g_oMap.setCenter(oLocation, ZOOM);
	g_oMap.panTo(oLocation);
	g_oMap.addOverlay(createMarker(oLocation));
	if (g_oMap.getZoom()<8)
		g_oMap.setZoom(ZOOM);
	g_oLastLocation = oLocation;
	//Guardamos la latitud y la longitud en el formulario
	$("#map_lat").val(oLocation.lat());
	$("#map_lng").val(oLocation.lng());		
}

//Busca una dirección y la marca en el mapa.
//La dirección se encuentra en un textbox con id "address"
function searchDirection(){
	var sAddress = $("#address").val();	
	if (!g_oGeocoder){
		g_oGeocoder = new google.maps.ClientGeocoder();
		g_oGeocoder.setBaseCountryCode('ES');
	}
	if (g_oGeocoder && sAddress && g_sLastAddress!=sAddress){		
		g_oGeocoder.getLatLng(
			sAddress,
    	function(oLocation) {
      	if (oLocation) {
	      	markLocation(oLocation);
	      }else{
	      	if (!g_oLastLocation)
	      		g_oMap.setCenter(g_oDefaultLocation, DEFAULT_LOCATION_ZOOM);
	      }
	      g_sLastAddress = sAddress;
  	  }
		);
	}else{		
		if (!g_oLastLocation)
			g_oMap.setCenter(g_oDefaultLocation, DEFAULT_LOCATION_ZOOM);
	}
}	
///////////////////////////////////////////////////////////////////
// Events
//////////////////////////////////////////////////////////////////
//Añade el evento de hacer click en el mapa.
//Se moverá la marca del mapa al punto donde se ha pulsado click.  
function addClickListener() {
	google.maps.Event.addListener(g_oMap, "click", function(oOverlay, oLocation) {
		if (!oOverlay){
			markLocation(oLocation);
			g_sLastAddress = '';
		}		
	});	
}
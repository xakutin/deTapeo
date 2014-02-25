var g_bIsIE = false;					//Indica si el navegador es Internet Explorer
var g_bIsOldIE = false;				//Indica si se trata de una versión antigua de Internet Explorer
//Comprobamos si el navegador e IE, y si es una versión antigua
if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){ //test for MSIE x.x;
	g_bIsIE = true;
	//Comprobamos la versión de IE
 	var ieversion=new Number(RegExp.$1); // capture x.x portion and store as a number
 	if (ieversion<7)
  	g_bIsOldIE = true;
}

/**
 * Selecciona una foto como portada de un bar
 * @param {int} photo_id Id de la foto a seleccionar
 * @param {int} bar_id Id del bar
 */
function selectPhoto(photo_id, bar_id){
	$.get(BASE_URL + "backend/photo.php", { op: "select", id: photo_id, bar_id: bar_id}, function(data){
		if (data>0){
			$(".selected").removeClass("selected");
			$("#photo" + data).addClass("selected");
		}
  });
}

/**
 * Elimina una foto de un bar de la BD
 * @param {int} photo_id Id de la foto a eliminar
 * @param {int} bar_id Id del bar
 */
function deletePhoto(photo_id, bar_id){
	$.get(BASE_URL + "backend/photo.php", { op: "delete", id: photo_id, bar_id: bar_id}, function(data){
		if (data>=0){
			$("#li" + photo_id).remove();
			if (data==0){			//El bar no tiene fotos, eliminamos la lista de fotos
				$("#images_list").remove();

			}else{						//Seleccionamos la nueva foto de portada
				if (!$("#photo" + data).hasClass("selected"))
					$("#photo" + data).addClass("selected");
			}
		}
  });
}

/**
 * Finaliza el proceso de alta de un bar
 */
function finishUploadPhotos(){
	document.frmUpload.finish.value="true";
	document.frmUpload.submit();
}

/**
 * Añade un elemento a un combo y lo selecciona
 * @param {String} cboId Id del combo
 * @param {String} txtId Id del cuadro de texto con el valor a añadir al combo
 */
function addNewOption(cboId, txtId){
	var value = jQuery.trim($("#" + txtId).val());
	if (value != "" && value != undefined){
		$("#" + cboId).append("<option value=\"" + value +"\" selected>" + value +"</option>");
	}
	closeNewOption();
}

/**
 * Elimina la ventana de nuevo elemento de un combo
 */
function closeNewOption(){
	$("#newOption").remove();
}

/**
 * Comprueba si en un combo se ha seleccionado la option de nuevo elemento, y en caso afirmativo
 * muestra un cuadro de texto para poder añadir un nuevo elemento.
 * @param {String} cboId Id del combo
 */
function checkNewOption(cboId){
	if ($("#" + cboId).val() == -1){
		var offset = $("#" + cboId).offset();
		var top = offset.top + 24;		//Para que este debajo del combo
		var left = offset.left;
		var width = $("#" + cboId).width();
		var innerHTML='<div id="newOption" style="position:absolute;top:' + top + 'px;left:' + left + 'px;width:' + width + 'px;"><input type="text" name="option_name" id="option_name" value=""/><p><a href="#" class="bot" onclick="addNewOption(\'' + cboId + '\',\'option_name\');return false;">Aceptar</a>&nbsp;<a href="#" class="bot" onclick="closeNewOption();return false;">Cancelar</a></p></div>';
		$(innerHTML).appendTo("body");
		$("#option_name").focus();

	}else{
		closeNewOption();
	}
}

/**
 * Elimina todas las opciones de un combo cuyo valor no sea ni 0 ni -1
 * @param {String} cboId Id del combo
 */
function removeOptions(cboId){
	$("#" + cboId).children("option").each(function(i){
		if ($(this).val()!=0 && $(this).val()!=-1)
	  	$(this).remove();
	});
}

/**
 * Añade las localidades de una provincia a un combobox.
 * @param {String} cboId Id del combo al que añadir las localidades
 * @param {String} zoneCboId Id del combo de zonas de una localidad que se deshabilitará
 * @param {String} provCboId Id del combo de provincias, del que se extrae el id de la provincia a la que pertenecen las localidades a buscar
 */
function loadTowns(cboId, zoneCboId, provCboId){
	removeOptions(cboId);				//Eliminamos las opciones anteriores
	removeOptions(zoneCboId);		//Eliminamos las opciones de zonas y la deshabilitamos
	$("#" + zoneCboId).attr("disabled", "disabled");

	var provId = jQuery.trim($("#" + provCboId).val());
	if (provId!= "" && provId!= "undefined"){
		$.getJSON(BASE_URL + "backend/direction.php",{ op: "towns", id: provId}, function(data){
			//Añadimos las opciones
	 		$.each(data, function(i,item){
	 			$("#" + cboId).append("<option value=\"" + item.id +"\">" + item.name +"</option>");
	    });
		});
		$("#" + cboId).removeAttr("disabled");
	}else{
		$("#" + cboId).attr("disabled", "disabled");
	}
}

//Busca las localidades de una provincia y las añade al combo, cuyo id recibe por parámetro
function loadZones(cboId, townCboId){
	removeOptions(cboId);	//Eliminamos las zonas anteriores

	var townId = jQuery.trim($("#" + townCboId).val());
	if (townId!= "" && townId!= "undefined" && townId!=0){
		$.getJSON(BASE_URL + "backend/direction.php",{ op: "zones", id: townId}, function(data){
			//Añadimos las opciones
	 		$.each(data, function(i,item){
	 			$("#" + cboId).append("<option value=\"" + item.id +"\">" + item.name +"</option>");
	    });
		});
		$("#" + cboId).removeAttr("disabled");
	}else{
		$("#" + cboId).attr("disabled", "disabled");
	}
}

//Envia la imagen de un formulario y muestra un texto informativo
function uploadPhoto(){
	$("#new_image").hide();
	var file_name = $("#file_image").val();
	var innerHTML = "<tr><td width='40'><img src='" + BASE_URL + "img/busy.gif' /></td><td>Subiendo el fichero: <b>" + file_name + "</b></td></tr>";
	$("#images_table_body").append(innerHTML);
	document.frmUpload.submit();
}

//Envia el formulario de nuevo comentario
function sendComment(){
	if ($("#comment_text").size() && jQuery.trim($("#comment_text").attr("value"))!="")
		document.frmComment.submit();
}

//Recupera el texto de un comentario
function getComment(commentId){
	$("#comment-id-" + commentId).load(BASE_URL + "backend/get_comment.php", {id: commentId});
}

//Muestra el formulario de edición de un comentario o el texto del comentario, dependiendo
//de lo que se esté visualizando
function editComment(commentId){
	if ($("#ce_text_" + commentId).size())
		$("#comment-id-" + commentId).load(BASE_URL + "backend/get_comment.php", {id: commentId, show_type: true});
	else
		$("#comment-id-" + commentId).load(BASE_URL + "backend/comment_edit.php", {id: commentId});
}

//Envia el texto modificado del comentario o la operación de borrado
function sendEditedComment(id, operation){
	var commentText = $("#ce_text_" + id).val();
	var commentType = $("#ce_type_" + id).val();
	var commentKey = $("#ce_key_" + id).val();

	$.post(BASE_URL + "backend/comment_edit.php", {id: id, text: commentText, key: commentKey, type: commentType, op: operation, submitted: "true" },
  	function(data){
    	if (data!=null){
				if (data.success == "true"){
					if (operation == "delete")
						$("#comment-item-" + id).remove();
					else
						$("#comment-id-" +id).html(data.msg);
				}else{
					$("#comment-id-" +id).html(data.msg);
				}
			}
  	}, "json");
}

//Desmarca el valor marcado en las estrellas
function overStar(value){
	if (value>=1 && value<=10){
		$("#star"+value).attr("class", "star" + value);
	}
}

//Marca en estrellas el valor del voto que recibe por parámetro
function outStar(value){
	if (value>=1 && value<=10){
		$("#star"+value).attr("class", "star" + value + "_sel");
	}
}

//Retarda el envio del voto para mostrar una imagen de ocupado
function vote_delayed(voteValue, barId){
	tooltip.menuhide = false;
	tooltip.showBusy();
	setTimeout("vote("+voteValue+", "+barId+", true)", 100);
}

var vote_detail_cache = new JSOC();		//Cache de voto desde la página de detalle

//Envia un voto al servidor
function vote(voteValue, barId, tooltipVote){
	if (!tooltipVote){	//Recuperamos la cache del voto en detalle
		if ((object = vote_detail_cache.get('vote.php'+barId)) != undefined){
			show_vote_detail_msg(object['vote.php'+barId]);
			return;
		}
	}
	//Mandamos el voto al servidor
	$.getJSON(BASE_URL + "backend/vote.php",{ id: barId, value: voteValue}, function(data){
		if (data!=null){
			if (tooltipVote){	//Voto desde el tooltip
				tooltip.setText(data.msg);
				if (data.success == "true"){
					$("#summary-stars-" + barId).html(data.stars);
				}
				if (data.cache != 'undefined' && data.cache>0){
					tooltip.set_vote_cache(barId, data.cache, data.msg);
				}
				tooltip.menuhide = true;
				tooltip.hide();

			}else{ //Voto desde la página de detalle
				show_vote_detail_msg(data.msg);
				if (data.success == "true"){
					$("#vote").val(voteValue);
					if (data.num_votes==1)
						$("#votesCount").html("(1 voto)");
					else
						$("#votesCount").html("("+ data.num_votes +" votos)");
				}
				if (data.cache != 'undefined' && data.cache>0){
					vote_detail_cache.set('vote.php'+barId, data.msg, {'ttl':data.cache});
				}
			}
		}
	});
}

function show_vote_detail_msg(text){
	var offset = $("#star1").offset();	//usamos la primera estrella para calcular el posicionamiento del mensaje
	tooltip.showMessage(text, (offset.top+10), (offset.left));
}

var timeoutHidePhoto = null;
var TIME_TO_HIDE_PHOTO = 1000;
var g_bPhotoContainerPositioned = false;
/**
 * Mueve el contenedor de la foto grande de un bar, encima del mapa
 */
function setPhotoContainerPosition(){
	var mapOffset = $("#map").offset();
	var top = mapOffset.top;
	var left = mapOffset.left;
	var photo_big = $("#photo_big_container").get(0);
	photo_big.style.left = left + "px";
	photo_big.style.top = top + "px";
	g_bPhotoContainerPositioned = true;
}

/**
 * Muestra la foto grande de un bar
 */
function showPhoto(imgUrl){
	if (timeoutHidePhoto) clearTimeout(timeoutHidePhoto);
	if (!g_bPhotoContainerPositioned)
		setPhotoContainerPosition();
	$("#photo_big").attr("src",imgUrl);
	$("#photo_big_container").show();
}

/**
 * Oculta la foto grande
 */
function hidePhoto(){
	if (g_bIsOldIE)
		$("#photo_big_container").hide();		//Para versiones antiguas de IE, no usamos la animación
	else
		timeoutHidePhoto = setTimeout('$("#photo_big_container").fadeOut("slow")',TIME_TO_HIDE_PHOTO);
}

//Scroll de las miniaturas de las fotos
var scrollTimeout;
var scrollSpeed=100;        //Velocidad en el movimiento del scroll
var scrollIncrease = 20;    //Número de pixeles que se suman o restan cada vez

//Mueve hacia abajo la capa de miniaturas de foto
function scrollDown(){
	var topPos=$("#photo_thumbs").scrollTop();
  var newTop = topPos + scrollIncrease;
  $("#photo_thumbs").scrollTop(newTop);
  scrollTimeout=setTimeout("scrollDown()",scrollSpeed);
}

//Mueve hacia arriba la capa de miniaturas de foto
function scrollUp(){
	var topPos=$("#photo_thumbs").scrollTop();
	if (topPos>0){
  	var newTop = topPos - scrollIncrease;
  	$("#photo_thumbs").scrollTop(newTop);
  	scrollTimeout=setTimeout("scrollUp()",scrollSpeed);
  }
}

//Para el movimiento de scroll
function stopScroll(){
	clearTimeout(scrollTimeout);
}


/**
 * Precarga varias imágenes
 * @param {Array} img_urls Array con las URLs de las imágenes a precargar
 */
function preloadImages(img_urls){
	if (document.images && img_urls!=null && img_urls.length>0){
		var img=new Image();
		for (var i=0;i<img_urls.length;i++)
			img.src=img_urls[i];
	}
}

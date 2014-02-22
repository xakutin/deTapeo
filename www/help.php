<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';
include includes.'html_stars.php';
$settings['ROBOTS'] = 'noindex, nofollow';
print_header('Ayuda - deTapeo');
print_tabs("Ayuda");
$q=0;
?>
<div id="main_sub">
		<p id="<?php $q=0;echo "q-$q";++$q;?>">
			<strong>¿Qué es deTapeo?</strong><br/>
			deTapeo es un proyecto para crear una guía de bares de tapas gratis,
			lo más completa y actualizada posible.<br/>
			Los bares tienen una puntuación en estrellas sobre la calidad de las tapas,
			esta valoración se calcula con los votos de los usuarios.<br/>
			Además de votar, se puede colaborar enviando información de los bares que se conocen
			y darnos tu opinión en los comentarios.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Qué normas debe cumplir un bar?</strong><br/>
		Solo una: debe ser un bar que acompañe la consumición con una tapa <strong>gratis</strong>.<br/>
		No se admiten bares de pinchos.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Cómo funciona el envío de bares?</strong><br/>
		Para aportar tu granito de arena solo tienes que seguir los siguientes pasos:
		</p>
		<ul>
			<li class="bullet">Pulsar el botón <a href="<?php echo $settings['BASE_URL'],'bar_data.php?op=new';?>" class="und" rel="nofollow">Añadir Bar</a>, que encontrarás en la parte derecha de todas las páginas</li>
			<li class="bullet">Rellenar el formulario con la información del bar, y pulsar el botón "Siguiente"</li>
			<li class="bullet">Comprobar en el callejero que la localización es la correcta, y en caso contrario modificarla pulsando sobre el mapa. Una vez que la localización sea la correcta pulsar el botón "Siguiente"</li>
			<li class="bullet">En el último paso se pueden añadir fotos del bar (si se desea), preferentemente fotos de las tapas que ofrecen. Para terminar el proceso de alta se pulsa el botón "Finalizar"</li>
		</ul>
		Una vez que se ha añadido, no aparecerá en la portada hasta que los editores comprueben que la información
		introducida es correcta.<br/>
		Los usuarios pueden consultar y editar sus envios desde su perfil de usuario, pulsando en el enlace "Enviados".
		<br/><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Cómo comprobáis que un bar existe y cumple las normas?</strong><br/>
		En primer lugar comprobamos que la información introducida es verosímil, y después verificamos que no se trata
		de un duplicado. Finalmente se realiza la publicación y se deja en manos de los usuarios la revisión final.<br/>
		Esta revisión es continua en el tiempo, puesto que los bares pueden cerrar, dejar de ofrecer tapas...<br/>
		Para avisar de que un bar publicado no cumple las normas o su información ha quedado obsoleta, se pulsa sobre la opción
		"advertir" y se selecciona una de las siguientes opciones:
	  </p>
	  <ul>
	  	<li class="bullet">Información obsoleta</li>
	  	<li class="bullet">Duplicado</li>
	  	<li class="bullet">No pone tapas gratis</li>
	  	<li class="bullet">No existe</li>
	  </ul>
	  Cuando se llegue a un número de votos negativos se mostrará un mensaje de advertencia y cuando se
	  sobrepase un umbral superior se modificará el estado y desaparecerá de la portada.
	  <br/><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Por qué no habéis publicado el bar que he enviado?</strong><br/>
		Por alguna de estas circuntancias:
		</p>
		<ul>
			<li class="bullet">Los editores todavía no lo han revisado</li>
			<li class="bullet">Es un bar duplicado</li>
			<li class="bullet">No cumple las normas de la web, es decir, la información enviada no hace
			referencia a un bar que ofrece tapas <strong>gratis</strong></li>
		</ul>
		Si quieres preguntar directamente a los editores puedes usar los comentarios del bar que has enviado
		como un foro.<br/>
		Este tipo de comentarios serán eliminados al publicar el contenido.<br/><br/>


		<p id="<?php echo "q-$q";++$q;?>">
			<strong>¿Necesito estar registrado para poder votar?</strong><br/>
			Sí.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Valen todos los votos lo mismo?</strong><br/>
		Sí, la valoración es una media de los votos recibidos.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Cómo puedo consultar los votos de un usuario?</strong><br/>
		Accediendo al perfil del usuario y pulsando el enlace "Votados".
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Cómo puedo consultar el perfil de un usuario?</strong><br/>
		Pulsando sobre la imagen que lo representa en los comentarios o en los listados de bares.
		<p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Cómo puedo registrarme?</strong><br/>
		Accediendo a <a href="<?php echo $settings['BASE_URL'],'user_register.php';?>" class="und" rel="nofollow">esta página</a>.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>He olvidado mi contraseña ¿qué hago para poder recuperarla?</strong><br/>
		Accede a esta <a href="<?php echo $settings['BASE_URL'],'user_pass_recover.php';?>" class="und" rel="nofollow">página</a> y sigue los pasos.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Dónde puedo consultar los bares que he enviado?</strong><br/>
		Desde tu perfil de usuario pulsando en el enlace "Enviados".
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Puedo modificar mi avatar?</strong><br/>
		Sí. Desde tu perfil de usuario pulsando el botón "Modificar" se mostrará un formulario donde podrás seleccionar tu nuevo avatar.<br/>
		Recuerda que debe ser una imagen cuadrada de no más de 100 KB, y que deberás pulsar el botón de refrescar de tu navegador
		web una vez lo hayas modificado (de esta manera se actualizará la caché de tu navegador web).
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Por qué no puedo acceder a la web con mi login de usuario?</strong><br/>
		Por alguna de estas razones:
		</p>
		<ul>
			<li class="bullet">La contraseña o el login son incorrectos, prueba a <a href="<?php echo $settings['BASE_URL'],'user_pass_recover.php';?>" class="und" rel="nofollow">recuperar tu contraseña</a></li>
			<li class="bullet">No validaste la cuenta cuando te diste de alta, prueba a <a href="<?php echo $settings['BASE_URL'],'user_register.php';?>" class="und" rel="nofollow">crearte otro usuario</a> y recuerda que debes validar la cuenta accediendo al enlace que se te enviará por correo electrónico.</li>
			<li class="bullet">Te diste de baja en la web</li>
			<li class="bullet">Se ha bloqueado tu cuenta</li>
		</ul><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Por qué habéis bloqueado mi cuenta?</strong><br/>
		Por no cumplir las <a href="<?php echo $settings['BASE_URL'],'legal.html#normas';?>" class="und" rel="nofollow">normas de uso</a>.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>">
			<strong>¿Necesito estar registrado para enviar comentarios?</strong><br/>
			Sí.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Por qué se ha borrado mi comentario?</strong><br/>
		Por no cumplir las <a href="<?php echo $settings['BASE_URL'],'legal.html#normas';?>" class="und" rel="nofollow">normas de uso</a> o por ser un comentario referente al proceso de publicación del contenido.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Cuál es la licencia de los contenidos?</strong><br/>
		La licencia de los contenidos es <a href="http://creativecommons.org/licenses/by-sa/3.0/deed.es" class="und" rel="nofollow">Creative Commons Reconocimiento-Compartir bajo la misma licencia 3.0</a>.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Qué software se usa?</strong><br/>
		El software ha sido desarrollado por nosotros basándonos en el código del <a href="http://meneame.net" class="und">Menéame</a>.
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Cuál es la licencia del software?</strong><br/>
		La licencia es <a href="<?php echo $settings['BASE_URL'],'code_license.html';?>" class="und" rel="nofollow">Affero General Public License</a>
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>"><strong>¿Donde puedo descargarme el código?</strong><br/>
		El código lo puedes encontrar <a href="https://github.com/xakutin/deTapeo" class="und" rel="nofollow">aquí</a>
		</p><br/>

		<p id="<?php echo "q-$q";++$q;?>">
			<strong>¿Cómo puedo notificar errores, problemas o sugerencias?</strong><br/>
			Mandando un correo a webmaster[arroba]detapeo.net.
		</p><br/>
</div>
<?php
print_footer();
?>

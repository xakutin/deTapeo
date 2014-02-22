<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class Bar {
	var $id = 0;
	var $randkey = 0;
	var $type = 0;
	var $text = 0;

	/**
	 * Contructor
	 */
	public function __construct() {

	}

	/**
	 * Carga la información recibida en las propiedades del objeto
	 * @param $dbban Objeto con la información del Baneo
	 */
	function load($dbbar) {
	}

	/**
	 * Guarda la información del Baneo en la BD
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function store() {
	}
}
?>

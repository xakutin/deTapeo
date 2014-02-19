<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include classes.'class.phpmailer.php';
class Mail{
	/**
	 * Envia un correo electrónico
	 *
	 * @param strig $to Dirección a la que se enviará el correo
	 * @param string $subject Asunto del correo electrónico
	 * @param string $txtmessage Mensaje del correo en texto plano
	 * @param string $htmlmessage Mensaje del correo en HTML
	 * @return true si no se produce ningún error, false en caso contrario
	 */
	public static function send($to, $subject, $txtmessage, $htmlmessage=""){
		$mail = self::get_mailer();
		$mail->From = "webmaster@detapeo.net";
		$mail->FromName = "deTapeo.net";
		$mail->Subject = $subject;
		$mail->WordWrap = 150; // set word wrap
		if (!empty($htmlmessage)){
			$mail->MsgHTML($htmlmessage);
			if (!empty($txtmessage))
				$mail->AltBody = $txtmessage;
			$mail->IsHTML(true);
		}else{
			$mail->Body = $txtmessage;
			$mail->IsHTML(false);
		}
		$mail->AddAddress("$to", "$to");
		if(!$mail->Send()){
			return false;
		}
		return true;
	}

	//Construye el objeto para enviar correos
	private static function get_mailer(){
		global $settings;

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet = 'utf-8';
		if ($settings['SMTP_USER'])
			$mail->SMTPAuth   = true;
		if ($settings['SMTP_SSL'])
			$mail->SMTPSecure = "ssl";

		$mail->Host       = $settings['SMTP_HOST'];
		$mail->Port       = $settings['SMTP_PORT'];
		$mail->Username   = $settings['SMTP_USER'];
		$mail->Password   = $settings['SMTP_PASSWORD'];
		return $mail;
	}
}
?>

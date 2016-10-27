<?php
class EEMI_Mail {

	// Variables
	private $_nom_expediteur;		
	private $_mail_expediteur;
	private $_mail_replyto;
	private $_mails_destinataires; 	// Séparées par ;
	private $_mails_bcc;			// Séparées par ;
	private $_objet;
	private $_texte;
	private $_html;
	private $_fichiers;				// Séparées par ;
	private $_message;	
	private $_frontiere;
	private $_headers;

	// Validation email
	public static function _validateEmail($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	// Constructeur
	public function __construct($mail_expediteur, $nom_expediteur, $mail_replyto="") {
		// Test des paramètres
		if(!self::_validateEmail($mail_expediteur)) {
			throw new InvalidArgumentException("Mail expéditeur invalide !");
		}
		if(!self::_validateEmail($mail_replyto)) {
			throw new InvalidArgumentException("Mail replyto invalide !");
		}
	

		// Initialiser les propriétés 
		$this->_nom_expediteur	= $nom_expediteur;
		$this->_mail_expediteur = $mail_expediteur;
		$this->_mail_replyto = $mail_replyto;
		$this->_mails_destinataires = '';
		$this->_mails_bcc = '';
		$this->_objet = '';
		$this->_texte = '';
		$this->_html = '';
		$this->_fichiers = '';
		$this->_message = '';	
		$this->_frontiere = md5(uniqid(mt_rand()));
		$this->_headers = '';
	}

	// Ajouter destinataire
	public function ajouter_destinataire($mail) {
		if(!self::_validateEmail($mail)) {
			throw new InvalidArgumentException("Le mail : ".$mail." est invalide !");
		}
		if($this->_mails_destinataires == '') {
			$this->_mails_destinataires = $mail; 
		} else {
			$this->_mails_destinataires .= ';'.$mail;
		}
	}

	// Ajouter un destinataire caché
	public function ajouter_bcc($mail) {
		if(!self::_validateEmail($mail)) {
			throw new InvalidArgumentException("Le mail d'ajout caché : ".$mail." est invalide !");
		}
		if($this->_mails_bcc == '') {
			$this->_mails_bcc = $mail; 
		} else {
			$this->_mails_bcc .= ';'.$mail;
		}
	}

	// Ajouter une pièce jointe
	public function ajouter_pj($fichier) {
		if(!file_exists($fichier)) {
			throw new InvalidArgumentException("La pièce jointe : ".$fichier." est invalide !");
		}
		// if(pathinfo_extension($fichier) {
		// 	$content_file = "application/pdf";
		// } else {
		// 	echo "ca marche pas";
		// }


		if($this->_fichiers == '') {
			$this->_fichiers = $fichier; 
		} else {
			$this->_fichiers .= ';'.$fichier;
		}
	}

	// Définir le contenu
	public function contenu($objet, $texte, $html) {
		$this->_objet = $objet;
		$this->_texte = $texte;
		$this->_html = $html;
	}

	// Envoyer le mail
	public function envoyer () {
		// Header du mail
		$this->_headers = 'From: "'.$this->_nom_expediteur.'" <'.$this->_mail_expediteur.'>'."\n";
		$this->_headers .= 'Return-Path:<'.$this->_mail_replyto.'>'."\n";
		$this->_headers .= 'MIME-Version: 1.0'."\n";
		if ($this->_mails_bcc != '') {
			$this->_headers .= "Bcc: ".$this->_mails_bcc."\n";
		}
		$this->_headers .= 'Content-Type: multipart/mixed; boundary="'.$this->_frontiere.'"';

		// Partie texte brut
		if ($this->_texte != '') {
			$this->_message .= '--'.$this->_frontiere."\n";
			$this->_message .= 'Content-Type: text/plain; charset="utf-8"'."\n";
			$this->_message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
			$this->_message .= $this->_texte."\n\n";
		}

		// Partie texte html
		if ($this->_html != '') {
			$this->_message .= '--'.$this->_frontiere."\n";
			$this->_message .= 'Content-Type: text/html; charset="utf-8"'."\n";
			$this->_message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
			$this->_message .= $this->_html."\n\n";
		}

		// Pièces jointes (séparées par un point-virgule)
		if ($this->_fichiers != '') {
			$tab_fichiers = explode(';', $this->_fichiers);
			$nb_fichiers = count($tab_fichiers);

			for ($i=0; $i<$nb_fichiers; $i++) {
				$this->_message .= '--'.$this->_frontiere."\n";
				$this->_message .= 'Content-Type: '. mime_content_type($tab_fichiers[$i]).'; name="'.$tab_fichiers[$i].'"'."\n";
				$this->_message .= 'Content-Transfer-Encoding: base64'."\n";
				$this->_message .= 'Content-Disposition: attachement; filename="'.$tab_fichiers[$i].'"'."\n\n";
				$this->_message .= chunk_split(base64_encode(file_get_contents($tab_fichiers[$i])))."\n\n";
			}
		}

		// Envoi du mail
		if (!mail($this->_mails_destinataires, $this->_objet, $this->_message, $this->_headers)) {
			throw new Exception("Envoi de mail échoué !");
		}
	}
}
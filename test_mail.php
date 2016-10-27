<!DOCTYPE html>
<html lang="fr">

<head>
	<title>Envoi Mail</title>
	<?php include_once("mail.class.php"); ?>
</head>

<body>
	<h1>Envoi Mail</h1>

	<div>

		<?php
			try {
				echo"<h2>Instanciation Mail</h2>";
				$mail = new EEMI_Mail("contact@test.com", 
										"Contact ", 
										"contact@test.com");

				$mail->ajouter_destinataire("gregoire.sayer@eemi.com");
				// $mail->ajouter_destinataire("test1@test.com");
				// $mail->ajouter_destinataire("test1@test.com");

				// $mail->ajouter_bcc("test1@gmail.com");
				// $mail->ajouter_bcc("test2@gmail.com");
				// $mail->ajouter_bcc("test3@gmail.com");

				// $mail->ajouter_pj("cours_mail.pdf");
				// $mail->ajouter_pj("img/Ordredujour27nov.pdf");
				// $mail->ajouter_pj("img/logo.jpg");

				$mail->contenu("Bienvenu !",
								"Heureux de vous savoir parmis nous ! Faites que vos soirées soit des plus tendre !", //texte
								"Heureux de vous savoir parmis nous ! Faites que vos soirées soit des plus tendre !"); //html

				$mail->envoyer();
				var_dump($mail);

			}
			catch (Exception $e) {
				echo $e->getMessage();
			}



		?>

	</div>

</body>
</html>			
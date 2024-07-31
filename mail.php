<?php
declare(strict_types=1);

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Classe permettant l'envoi d'un mail
 * Nécessite le composant PHPMailer
 * Nécessite un fichier de configuration mail.php stocké dans le répertoire /.config du projet
 * Le fichier de configuration doit retourner un tableau associatif dont les clés sont les paramètres de connexion
 * @Author : Guy Verghote
 * @Version : 1.1.1
 * @Date : 31/07/2024
 */

class Mail {
    private $mail;

    public function __construct()
    {
        $lesParametres = require($_SERVER['DOCUMENT_ROOT']  . "/.config/mail.php");
        $this->mail = new PHPMailer;
        //Server settings
        // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->isSMTP();
        $this->mail->Host = $lesParametres['host'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $lesParametres['user'];
        $this->mail->Password = $lesParametres['password'];
        $this->mail->SMTPSecure = $lesParametres['secure'];
        $this->mail->Port = $lesParametres['port'];
        $this->mail->setFrom($lesParametres['user'], $lesParametres['from']);
    }

    /**
     * Indiquer l'expéditeur du mail (utile dans 'nous contacter')
     * @param string $email
     * @param string $nom
     * @return void
     */
    public function setExpediteur(string $email, string $nom)  {
        $this->mail->setFrom($email, $nom);
    }

    /**
     * @param string $destinataire
     * @param string $sujet
     * @param string $message
     * @return int
     */
    public function envoyer($destinataire, $sujet, $message)
    {
        $this->mail->isHTML(true); // Set email format to HTML
        $this->mail->Subject = mb_convert_encoding($sujet, 'ISO-8859-1');
        $this->mail->Body = mb_convert_encoding($message, 'ISO-8859-1');
        $this->mail->AltBody = mb_convert_encoding($message, 'ISO-8859-1');
        $this->mail->addAddress($destinataire);
        try {
            $this->mail->send();
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * @param string $destinataire
     * @param string $sujet
     * @param string $message
     * @param string $piece
     * @return int
     */
    public function envoyerAvec($destinataire, $sujet, $message, $piece)
    {
        $this->mail->isHTML(true); // Set email format to HTML
        $this->mail->Subject = mb_convert_encoding($sujet, 'ISO-8859-1');
        $this->mail->Body = mb_convert_encoding($message, 'ISO-8859-1');
        $this->mail->AltBody = mb_convert_encoding($message, 'ISO-8859-1');
        $this->mail->addAddress($destinataire);
        $this->mail->AddAttachment($piece);
        try {
            $this->mail->send();
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

}

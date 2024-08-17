<?php
declare(strict_types=1);

/**
 * Classe Erreur : Classe permettant de générer la réponse du serveur en cas d'erreur détectée
 * Utilise les classes techniques Journal et ListeNoire (table listenoire)
 * @Author : Guy Verghote
 * @Version : 1.1.0
 * @Date : 17/08/2024
 */
class Erreur
{
    /**
     * Réponse du serveur en cas de détection d'une erreur
     *
     * Si le script a été appelé directement
     *    La méthode redirige l'utilisateur vers la page erreur/index.php
     *    Le message d'erreur et le script (page) à l'origine de l'erreur sont conservés dans une variable de session
     * Si le script a été appelé par un appel Ajax
     *    La méthode retourne le message et le type du message dans le format json
     *    Si le type n'est pas précisé, il est déduit à partir du contenu du message
     * @param string $message message associé au type de l'erreur
     * @param string|null $type [facultatif] type de l'erreur :  'global' ou 'system'
     * @return void
     */
    public static function envoyerReponse(string $message, ?string $type = null): void
    {
        // le type de l'erreur n'est pas précisé, il s'agit d'une erreur capturée dans un try catch
        // il peut s'agir 'une erreur provenant d'un déclencheur ou d'une erreur système
        // dans le cas d'un déclencheur, l'erreur sera traitée comme une erreur globale afin d'être affichée à l'utilisateur
        if ($type === null) {
            // recherche de la présence d'un # qui signale un message provenant d'un déclencheur
            $messageDeclencheur = strstr($message, '#');
            if ($messageDeclencheur) {
                $type = 'global';
                $message = substr($messageDeclencheur, 1);
            } else {
                Journal::enregistrer($message, 'erreur');
                $type = 'system';
                $message = "Une erreur inattendue s'est produite, veuillez contacter l'administrateur";
            }
        } elseif ($type === 'system') {
            Journal::enregistrer($message, 'erreur');
            $message = "Une erreur s'est produite, veuillez contacter l'administrateur";
        }

        // Si le script a été appelé directement
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            $_SESSION['erreur'] = [];
            $_SESSION['erreur']['page'] = $_SERVER['PHP_SELF'];
            $_SESSION['erreur']['message'] = $message;
            header('Location:/erreur');
        } else {
            $lesErreurs[$type] = $message;
            echo json_encode(['error' => $lesErreurs], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Rejette l'accès à une url jugée malveillante et enregistre l'ip dans la liste noire
     * @param string $url
     * @return void
     */
    public static function rejeterUrl(string $url): void
    {
        $ip = Journal::getIp();
        ListeNoire::ajouter($ip);
        Journal::enregistrer("Url malveillante : $url", 'erreur');
        $_SESSION['erreur'] = [];
        $_SESSION['erreur']['message'] = "Votre requête a été jugée malveillante, votre adresse IP a été enregistrée dans la liste noire";
        header('Location:/erreur');
        exit;
    }
}

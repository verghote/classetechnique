<?php
declare(strict_types=1);

/**
 * Classe Jeton : ensemble de méthodes statiques concernant les opérations sur des fichiers et des répertoires
 *
 * @Author : Guy Verghote
 * @Version 2024.4
 * @Date : 29/07/2024
 */
class Jeton {

    /**
     * Création d'un jeton de vérification
     * @return string
     */
    public static function creer() {
        // Mise en place d'un jeton pour garantir l'origine de l'appel
        $token = bin2hex(random_bytes(32));
        // Mémorisation du jeton dans une variable de session
        $_SESSION['token'] = $token;
        return $token;
    }

    /**
     * Vérification de la validité du jeton
     * @return bool
     */
    public static function verifier() {
        // Vérifiez la correspondance des jetons
        if (!isset($_SESSION['token'])) {
            Erreur::envoyerReponse("Le jeton de vérification n'est pas présent sur le serveur.", 'global');
            return false;
        }
        if (!isset($_REQUEST['token'])) {
            Erreur::envoyerReponse("Le jeton de vérification n'a pas été transmis.", 'global');
            return false;
        }
        if ($_SESSION['token'] !== $_REQUEST['token']) {
            Erreur::envoyerReponse("Absence d'un jeton valide vous autorisant à réaliser cette opération.", 'global');
            return false;
        }
        return true;
    }
}


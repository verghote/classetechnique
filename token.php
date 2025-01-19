<?php
declare(strict_types=1);

/**
 * Classe Jeton : ensemble de méthodes statiques concernant les opérations sur des fichiers et des répertoires
 *
 * @Author : Guy Verghote
 * @Version 2025.1
 * @Date : 19/01/2025
 */
class Token {

    /**
     * Création d'un jeton de vérification
     */
    public static function creer() {
        // Générer un jeton aléatoire
        $token = bin2hex(random_bytes(32));

        // Définir la durée d'expiration du jeton (5 minutes ici)
        $expires = time() + 300;

        // Stocker le jeton et son expiration en session
        $_SESSION['token'] = [
            'value' => $token,
            'expires' => $expires
        ];

        // Transmettre le jeton dans un cookie sécurisé
        setcookie('token', $token, [
            'expires' => $expires,  // Durée du cookie
            'path' => '/',          // Accessible sur tout le site
            'secure' => true,       // Cookie uniquement en HTTPS
            'httponly' => true,     // Empêche l'accès via JavaScript
            'samesite' => 'Strict'  // Protection contre les attaques CSRF inter-domaines
        ]);

    }

    /**
     * Vérification de la validité du jeton
     * @return bool
     */
    public static function verifier() {
        // Vérifier que le jeton est stocké en session
        if (!isset($_SESSION['token'])) {
            Erreur::envoyerReponse("Le jeton de vérification n'est pas présent sur le serveur.", 'global');
            return false;
        }

        // Récupérer le jeton depuis le cookie
        $token = $_COOKIE['token'] ?? null;

        // Si le jeton est manquant dans le cookie
        if ($token === null) {
            Erreur::envoyerReponse("Le jeton de vérification est manquant dans le cookie.", 'global');
            return false;
        }

        // Vérifier que le jeton correspond à celui en session
        if ($_SESSION['token']['value'] !== $token) {
            Erreur::envoyerReponse("Le jeton est invalide.", 'global');
            return false;
        }

        // Vérifier si le jeton a expiré
        if ($_SESSION['token']['expires'] < time()) {
            Erreur::envoyerReponse("Le jeton a expiré.", 'global');
            return false;
        }

        // Si toutes les vérifications sont réussies, renvoyer true
        return true;
    }
}


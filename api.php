<?php
declare(strict_types=1);

/**
 * Classe permettant d'interroger une API par la méthode get
 * @Author : Guy Verghote
 * @Version : 1.0.0
 * @Date : 02/11/2024
 */
class Api
{

    /**
     * Récupérer la réponse de l'API dans le format JSON
     * @param $url string URL de l'API à interroger
     * ex : https://api.github.com/repos/verghote/ pour tous les référentiels
     * ex : https://api.github.com/repos/verghote/verghote.github.io pour un référentiel
     * @return string réponse de l'API dans le format json
     */
    public static function get(string $url): string
    {
        // Récupérer le token d'autorisation depuis la variable d'environnement
        $token = getenv('CALENDRIER_TOKEN');

        // Initialisation de cURL
        $ch = curl_init();

        // Configuration des options cURL
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CAINFO => 'i:/cacert.pem',
            CURLOPT_USERAGENT => "verghote",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: token ' . $token
            ],
            CURLOPT_FOLLOWLOCATION => true // Suivre les redirections
        ];
        curl_setopt_array($ch, $options);

        // Exécution de la requête
        $reponse = curl_exec($ch);

        // Vérification des erreurs cURL
        if (curl_errno($ch)) {
            curl_close($ch);
            Erreur::envoyerReponse("Erreur cURL: " . curl_error($ch), 'global');
        }

        // Fermer la session cURL
        curl_close($ch);

        // Vérification du code HTTP
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            Erreur::envoyerReponse(Erreur::getErreurHttp($httpCode));
        }

        // Transmission des données à l'interface
        return $reponse;
    }
}
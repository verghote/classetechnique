<?php
declare(strict_types=1);

/**
 * Classe TraficIp : Classe permettant de mémoriser le trafic afin de détecter les ip à bloquer
 *
 * @Author : Guy Verghote
 * @Date :18/08/2024
 */
class TraficIp
{
    /**
     * Enregistre l'ip du visiteur à l'origine de la requête
     * @return bool
     */
    public static function ajouter(string $ip)
    {
        try {
            $sql = <<<EOD
            INSERT INTO traficip(ip) VALUES (:ip);
EOD;
            $db = Database::getInstance();
            $curseur = $db->prepare($sql);
            $curseur->bindParam('ip', $ip);
            $curseur->execute();
        } catch (Exception $e) {
            Erreur::envoyerReponse($e->getMessage());
        }
    }
}
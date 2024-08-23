<?php
declare(strict_types=1);

/**
 * Classe TraficIp : Classe permettant de mémoriser le trafic afin de détecter les ip à bloquer
 *
 * @Author : Guy Verghote
 * @Date :23/08/2024
 */
class TraficIp
{
    /**
     * Supprime le trafic de la semaine précédente et enregistre l'ip du visiteur à l'origine de la requête
     * @return bool
     */
    public static function ajouter(string $ip)
    {
        try {
            $db = Database::getInstance();
            $sql = <<<EOD
            DELETE FROM traficip WHERE horodatage < NOW() - INTERVAL 1 week;  
EOD;
            $db->exec($sql);
            $sql = <<<EOD
            INSERT INTO traficip(ip) VALUES (:ip);
EOD;
            $curseur = $db->prepare($sql);
            $curseur->bindParam('ip', $ip);
            $curseur->execute();
        } catch (Exception $e) {
            Erreur::envoyerReponse($e->getMessage());
        }
    }
}
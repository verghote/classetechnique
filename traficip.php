<?php
declare(strict_types=1);

/**
 * Classe TraficIp : Classe permettant de mémoriser le trafic afin de détecter les ip à bloquer
 *
 * @Author : Guy Verghote
 * @Date :13/07/2024
 */
class TraficIp
{
    /**
     * Retourne la liste des ip enregistrées
     * @return array
     */
    public static function getALL(): array
    {
        $sql = <<<EOD
                    SELECT ip, date
                    FROM traficip
EOD;
        $select = new Select();
        return $select->getRows($sql);
    }


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

    /**
     * Vérifie si une adresse ip demande trop de requête : plus de 6 en 2 secondes
     * @param string $ip
     * @return bool | void
     */
    public static function estUnRobot(string $ip)
    {
        try {
            $sql = <<<EOD
                    SELECT count(*)
                    FROM traficip
                    Where ip = :ip
                    and  date > now() - interval 2 second ; 
EOD;
            $select = new Select();
            $nb = (int)$select->getValue($sql, ['ip' => $ip]);
            return $nb > 6 ? true : false;
        } catch (Exception $e) {
            Erreur::envoyerReponse($e->getMessage());
        }
    }

    /**
     * Supprime les enregistrements de plus d'un mois
     * @return bool
     */
    public static function supprimerAncien()
    {
        $sql = <<<EOD
            delete from traficip 
            where date < now() - interval 1 month 
EOD;
        $db = Database::getInstance();
        try {
            $db->exec($sql);
            return true;
        } catch (Exception $e) {
            Erreur::envoyerReponse($e->getMessage());
        }
    }
}


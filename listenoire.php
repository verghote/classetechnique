<?php
declare(strict_types=1);

/**
 * Classe ListeNoire : Classe permettant de gérer une liste noire des adresses IP
 *
 * @Author : Guy Verghote
 * @Date : 18/08/2024
 */
class ListeNoire
{
    /**
     * Retourne la liste des ip enregistrées
     * @return array
     */
    public static function getALL(): array
    {
        $sql = <<<EOD
                    SELECT ip, horodatage
                    FROM listenoire
                    ORDER BY horodatage DESC;
EOD;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * Enregistre l'ip du visiteur
     */
    public static function ajouter(string $ip)
    {
        try {
            $sql = <<<EOD
            INSERT INTO listenoire (ip) VALUES (:ip);
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
     * Vérifie si une adresse ip est bloquée
     * @param string $ip
     * @return bool
     */
    public static function estDansLaListe(string $ip): bool
    {
        $sql = <<<EOD
                    SELECT 1
                    FROM listenoire
                    Where ip = :ip
EOD;
        $select = new Select();
        $nb = (int)$select->getValue($sql, ['ip' => $ip]);
        return $nb === 1 ? true : false;
    }

    /**
     * Supprime une adresse ip de la liste noire
     * @param string $ip
     */
    public static function supprimer(string $ip)
    {
        try {
            $sql = <<<EOD
            delete from listenoire where ip = :ip;
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

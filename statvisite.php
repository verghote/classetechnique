<?php
/**
 * Classe permettant de gérer les visites journalières
 * Nécessite la création de la table statvisite(date date, nb int) et de la procédure stockée comptabiliserVisite()
 * @Author : Guy Verghote
 * @Version : 2024.3
 * @Date : 16/07/2024
 */
class StatVisite
{
    private static $lesRobots = [
        'Googlebot', 'Bingbot', 'Slurp', 'DuckDuckBot', 'Baiduspider',
        'YandexBot', 'Sogou', 'Exabot', 'facebookexternalhit'
    ];

    /**
     * Vérifie si l'entête HTTP user_agent correspond à celle d'un robot
     * @return bool
     */
    private static function estUnRobot() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        foreach (self::$lesRobots as $robot) {
            if (stripos($userAgent, $robot) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Récupération de toutes les visites
     * @return array
     */
    public static function getAll()
    {
        $db = Database::getInstance();
        $curseur =  $db->query("call getLesVisites()");
        return $curseur->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupération des statistiques
     * @return array
     */
    public static function getStat()
    {
        $db = Database::getInstance();
        $curseur =  $db->query("call getStatVisite()");
        return $curseur->fetch(PDO::FETCH_ASSOC);
    }

    public static function getTotal()
    {
        $db = Database::getInstance();
        $curseur =  $db->query("select getTotalVisite()");
        return $curseur->fetchColumn();
    }

    /**
     * Comptabilisation d'une visite
     */
    public static function comptabiliser()
    {
      if (!isset($_SESSION['visiteur']) && !self::estUnRobot()) {
            $_SESSION['visiteur']= true;
            $db = Database::getInstance();
            $db->exec("call comptabiliserVisite()");
      }
    }

    public static function init()
    {
        $db = Database::getInstance();
        $sql = "delete from statvisite";
        $db->exec($sql);
    }
}
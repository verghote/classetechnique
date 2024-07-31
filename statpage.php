<?php
/**
 * Classe permettant de gérer les visites journalières
 * Nécessite la création de la table statpage(nom varchar(50), nb int)
 * @Author : Guy Verghote
 * @Version : 2024.3
 * @Date : 19/07/2024
 */

class StatPage
{
    /**
     * Récupérer les statistiques des pages visitées
     * @return array tableau associatif contenant les statistiques
     */
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $curseur = $db->query('call getLesPages()');
        return $curseur->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Comptabiliser la visite d'une page dans la table statpage
     * seul le nom de la page est pris en compte sans les paramètres éventuels
     */
    public static function comptabiliser(): void
    {
        $nom = parse_url($_SERVER['REQUEST_URI'])['path'];
        if (strlen($nom) > 2) {
            $nom = trim($nom, '/');
        }
        $db = Database::getInstance();
        // $db->exec("call comptabiliserPage($nom)");
        $curseur = $db->prepare('CALL comptabiliserPage(:nom)');
        $curseur->bindParam(':nom', $nom, PDO::PARAM_STR);
        $curseur->execute();

    }

    /**
     * Réinitialiser les statistiques des pages visitées
     * @return void
     */
    public static function init(): void
    {
        $db = Database::getInstance();
        $sql = 'update statpage set nb = 0 ';
        $db->exec($sql);
    }

    /**
     * supprimer les statistiques des pages visitées
     * @return void
     */
    public static function delete(): void
    {
        $db = Database::getInstance();
        $sql = 'delete from statpage';
        $db->exec($sql);
    }
}

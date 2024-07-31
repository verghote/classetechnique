<?php
declare(strict_types=1);
/**
 * Classe permettant de gérer toutes les requêtes de consultation de la base de données
 *
 * @Author : Guy Verghote
 * @Version : 2.0.1
 * @Date : 31/07/2024
 */

class Select
{
    const MSG_ERREUR = "Erreur SQL : ";

    //  attribut privé pour stocker l'objet Dbo assurant la connexion à la base de données
    private PDO $db; // stocke l'adresse de l'unique objet instantiable

    /*
    -------------------------------------------------------------------------------------------------------------------
    Le constructeur
    --------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Constructeur d'un objet Select
	 * Inialise l'attribut privé $db (objet PDO) en appelant la méthode getInstance de la classe Database
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /*
    -------------------------------------------------------------------------------------------------------------------
    Les méthodes applicables sur un objet de la classe Select
    --------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Retourne dans un tableau numérique, le résultat d'une requête SQL retournant plusieurs lignnes
     *  chaque ligne étant un tableau associatif clé = nomcolonne et valeur = valeur de la colonne
     * @param string $sql requête Sql
     * @param array $lesParametres tableau associatif clé = nomcolonne et valeur = valeur transmise
     * @return array
     */
    public function getRows(string $sql, array $lesParametres = []): array
    {
        try {
            if ($lesParametres === []) {
                $curseur = $this->db->query($sql);
            } else {
                $curseur = $this->db->prepare($sql);
                foreach ($lesParametres as $cle => $valeur) {
                    $curseur->bindValue($cle, $valeur);
                }
                $curseur->execute();
            }
            $lesLignes = $curseur->fetchAll();
            $curseur->closeCursor();
        } catch (PDOException $e) {
            Erreur::envoyerReponse(self::MSG_ERREUR . $e->getMessage());
        }
        return $lesLignes;
    }

    /**
     * Retourne dans un tableau associatif, le résultat d'une requête retournant une seule ligne
     * @param string $sql requête Sql
     * @param array $lesParametres tableau associatif clé = nomcolonne et valeur = valeur transmise
     * @return array | false
     */
    public function getRow( string $sql, array $lesParametres = [])
    {
        try {
            if ($lesParametres === []) {
                $curseur = $this->db->query($sql);
            } else {
                $curseur = $this->db->prepare($sql);
                foreach ($lesParametres as $cle => $valeur) {
                    $curseur->bindValue($cle, $valeur);
                }
                $curseur->execute();
            }
            // fecth retourne false si aucun enregistrement n'est trouvé
            $ligne = $curseur->fetch();
            $curseur->closeCursor();

        } catch (PDOException $e) {
            Erreur::envoyerReponse(self::MSG_ERREUR . $e->getMessage());
        }
        return $ligne;
    }

    /**
     * Retourne dans une variable, le résultat d'une requête retournant une seule valeur
     * @param string $sql requête Sql
     * @param array $lesParametres tableau associatif clé = nomcolonne et valeur = valeur transmise
     * @return mixed pour couvrir tous les cas possibles (string, int, float, bool, null, ou false)
    */
    public function getValue(string $sql, array $lesParametres = [])
    {
        try {
            if ($lesParametres === []) {
                $curseur = $this->db->query($sql);
            } else {
                $curseur = $this->db->prepare($sql);
                foreach ($lesParametres as $cle => $valeur) {
                    $curseur->bindValue($cle, $valeur);
                }
                $curseur->execute();
            }
            // fecthColumn retourne false si aucun enregistrement n'est trouvé
            $valeur = $curseur->fetchColumn();
            $curseur->closeCursor();

        } catch (PDOException $e) {
            Erreur::envoyerReponse(self::MSG_ERREUR . $e->getMessage(), 'global');
        }
        return $valeur;
    }
}
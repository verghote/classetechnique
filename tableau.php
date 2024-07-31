<?php

/**
 * Classe Tableau permet de générer un tableau de données au format HTML
 *
 * @Author : Guy Verghote
 * @Version : 1.0.0
 * @Date : 31/07/2024
 */
class Tableau
{
    private $id; // Valeur de l'attribut id associé au tableau
    private $classe; // Valeur de l'attribut classe associé au tableau
    private $lesTailles; // Tableau contenant les tailles de chaque colonne
    private $lesColonnes; // Tableau contenant le nom de chaque colonne
    private $lesClasses; // Tableau contenant la valeur de l'attribut classe associé à chaque cellule
    private $lesStyles; // Tableau contenant la valeur de l'attribut style associé à chaque cellule
    private $tableau; // contient le code HTML du tableau


    /**
     * Constructeur d'un objet Tableau
     *

     * @param array $lesTailles
     * @param array $lesColonnes
     * @param array $lesClasses
     * @param array $lesStyles
     * @param string $id
     * @param string $classe
     */

    public function __construct($lesColonnes, $lesTailles, $lesStyles, $lesClasses, $id = '', $classe = '')
    {
        $this->tableau ="<div class='table-responsive'>";
      //  $this->tableau .= "<table id='" . $id . "' class='table table-condensed table-bordered table-hover" . $classe . "'>";
        $this->tableau .= "<table id='" . $id . "' class='table " . $classe . "'>";

        // définition des balises col
        $nb = count($lesTailles);
        for ($i = 0; $i < $nb; $i++) {
            if ($lesTailles[$i] != '') {
                $this->tableau .= "<col style='width:" . $lesTailles[$i] . "px;'>";
            } else {
                $this->tableau .= "<col >";
            }
        }
        // définition de l'entête
        $this->tableau .= "<thead><tr>";
        $nb = count($lesColonnes);
        for ($i = 0; $i < $nb; $i++) {
            $this->tableau .= "<th class='" . $lesClasses[$i] . "' style='" . $lesStyles[$i] . "'>" . $lesColonnes[$i] . "</th>";
        }
        $this->tableau  .= "</tr></thead><tbody>";
    }

    /**
     * Ajouter une ligne
     *
     * @param array $lesCellules valeur de chaque cellule
     * @param array $lesClasses  classe associée à chaque cellule
     * @param array $lesStyles style associé à chaque cellule
     * @param $id : identifiant associé à la ligne
     */

    public function ajouterLigne(array $lesCellules, array $lesStyles, array $lesClasses, $id= '')
    {
        $this->tableau .= "<tr id='" . $id . "'>";
        $nb = count($lesCellules);
        for ($i = 0; $i < $nb; $i++) {
            $this->tableau .= "<td class='" . $lesClasses[$i] . "' style='" . $lesStyles[$i] . "'>" . $lesCellules[$i] . "</td>";
        }
        $this->tableau .= "</tr>";
    }

    /**
     * Fermer le tableau
     */

    public function fermer()
    {
        $this->tableau .= "</tbody></table></div>";
    }

    /**
     * retourner le tableau au format HTML
     * @return string Code html du tableau
     *
     */
    public function toHTML() : string
    {
        return $this->tableau;
    }
}
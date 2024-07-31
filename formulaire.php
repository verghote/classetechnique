<?php
declare(strict_types=1);

/**
 * Classe Formulaire : représente les données envoyé depuis un formulaire
 * @Author : Guy Verghote
 * @Date : 31/07/2024
 */
abstract class Formulaire
{

    //  tableau d'objet Erreur alimenté par les méthodes de gestion
    private $lesErreurs = [];

    public function getLesErreurs()
    {
        return $this->lesErreurs;
    }


    // Tableau des champs composant le formulaire
    // Tableau associatif - clé : nom de la colonne, valeur : un objet Input (contient la valeur et les règles de validations)
    protected array $columns;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->columns = [];
    }

    /*
    -------------------------------------------------------------------------------------------------------------------
    Les accesseurs
    --------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Retourne l'objet Input associé à la clé (colonne)
     * @param string $colonne Nom de la colonne de la table
     * @return Input
     */
    public function getColonne($colonne): Input
    {
        return $this->columns[$colonne];
    }

    /*
    -------------------------------------------------------------------------------------------------------------------
    Les méthodes de contrôle
    --------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Contrôle la valeur attribuée à chaque colonne à partir des règles de validations associées à chaque colonne
     * @return bool
     */
    public function donneesTransmises(): bool
    {
        // Alimente les objets Input à l'aide du tableau $_POST
        foreach ($_POST as $cle => $valeur) {
            $valeur = trim($valeur);
            if ($valeur !== '' && isset($this->columns[$cle])) $this->columns[$cle]->Value = $valeur;
        }

        // Vérification que toutes les colonnes (input) obligatoire sont bien renseignée

        $ok = true;

        foreach ($this->columns as $cle => $input) {
            // s'il s'agit d'un fichier il faut aussu vérifier sa transmission
            if ($input instanceof InputFile) {
                if ($input->Require && !$input->fichierTransmis()) {
                    $this->lesErreurs[$cle] = 'Veuillez renseigner ce champ.';
                    $ok = false;
                }
                //
            } elseif ($input->Require && $input->Value === null) {
                $this->lesErreurs[$cle] = 'Veuillez renseigner ce champ.';
                $ok = false;
            }
        }
        return $ok;
    }

    /**
     * Contrôle la valeur attribuée à chaque colonne à partir des règles de validations associées à chaque colonne
     * @return bool
     */
    public
    function checkAll(): bool
    {
        // Contrôle des valeurs
        $correct = true;
        foreach ($this->columns as $cle => $input) {
            if (!$input->checkValidity()) {
                $this->lesErreurs[$cle] = $input->getValidationMessage();
                $correct = false;
            }
        }
        return $correct;
    }
}
<?php
declare(strict_types=1);

/**
 * Classe InputFileImg : ajoute à la classe InputFile les opérations spécifiques sur un fichier image
 * Possibilité de définir les dimensions à respecter et de redimensionner (extraire une partie en fonction des dimensions à respecter)
 * Nécessite la bibliothèque Gumlet/ImageResize
 * @Author : Guy Verghote
 * @Version : 1.0.1
 * @Date : 18/10/2024
 */

// chargement du composant permettant de redimensionner l'image
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

class InputFileImg extends InputFile
{
    // attributs spécifiques
    // la largeur et la hauteur pour une image si besoin
    // La possibilité de mettre en place un redimensionnement automatique de l'image qui dépasserait les dimensions définies

    //  Dimension demandée pour l'image : la hauteur et la largeur en pixel
    // L'image sera redimensionnée selon les dimensions demandées si la propriété $redimensionner est vraie sinon l'image ne devra pas dépasser ces dimensions
    public int $Width;
    public int $Height;

    // Indique si l'image doit absolument respecter les dimensions (false) ou si elle sera automatiquement redimensionnée aux dimensions demandées
    public bool $Redimensionner = false;

    public function __construct($lesParametres)
    {
        parent::__construct($lesParametres);
        $this->Height = $lesParametres['height'] ?? 0;
        $this->Width = $lesParametres['width']?? 0;
        $this->Redimensionner = $lesParametres['redimensionner'] ?? false;
    }

    public function checkValidity(): bool
    {
        if (!parent::checkValidity()) {
            return false;
        }

        if ($this->file === null) {
            return true;
        }

        // contrôle éventuel des dimensions si elles sont fixées et si l'image ne doit pas être redimensionnée
        if (!$this->Redimensionner && ($this->Width !== 0 || $this->Height !== 0)) {
            // Récupération des dimensions de l'image
            $lesDimensions = getimagesize($this->file['tmp_name']);
            $width = $lesDimensions[0];
            $height = $lesDimensions[1];
            if ($width > $this->Width || $height > $this->Height) {
                $this->validationMessage = "Les dimensions de l'image ($width*$height) dépassent les dimensions acceptées ($this->Width*$this->Height)";
                return false;
            }
        }
        return true;
    }

    /**
     * Copie du fichier téléversé sur le serveur sous le nom contenu dans la propriété Value
     * Condition : avoir appelé la méthode checkValidity avant et avoir renseigné la propriété Directory
     * @return bool
     */
    public function copy(): bool
    {
        // le fichier ne peut être copié que s'il a été préalablement vérifié
        if (!$this->valide) {
            $this->validationMessage = " Le fichier doit être contrôlé avant d'être copié";
            return false;
        }

        $nomFichier = $this->Value;
        $tmpName = $this->file['tmp_name'];

        // adaptation des dimensions aux dimensions demandées

        // Si aucune contrainte sur les dimensions ou si le redimensionnement n'est pas activé, le fichier peut être copié
        if (($this->Width === 0 && $this->Height === 0) || !$this->Redimensionner) {
            copy($tmpName, "$this->Directory/$nomFichier");
            return true;
        }

        // au moins une contrainte est fixée
        $lesDimensions = getimagesize($tmpName);
        if ($lesDimensions[0] > $this->Width) {
            if ($lesDimensions[1] > $this->Height) {
                // Contrainte sur la largeur et la hauteur
                $image = new ImageResize($tmpName);
                $image->crop($this->Width, $this->Height, true, ImageResize::CROPCENTER);
                $image->save("$this->Directory/$nomFichier");
            } else {
                // contrainte sur la largeur, mais pas la hauteur
                $image = new ImageResize($tmpName);
                $image->crop($this->Width, $lesDimensions[1], true, ImageResize::CROPLEFT);
                $image->save("$this->Directory/$nomFichier");
            }
        } else {
            if ($lesDimensions[1] > $this->Height) {
                // contrainte sur la hauteur, mais pas la largeur
                $image = new ImageResize($tmpName);
                $image->crop($lesDimensions[0], $this->Height, true, ImageResize::CROPTOP);
                $image->save("$this->Directory/$nomFichier");
            } else {
                copy($tmpName, "$this->Directory/$nomFichier");
                return true;
            }
        }
        return true;
    }
}

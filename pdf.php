<?php
declare(strict_types=1);

/**
 * Classe PDF : hérite de FPDF
 * Définition des méthodes header footer titre impressionEnteteTableau ...
 * @Author : Guy Verghote
 * @Date : 04/08/2024
 */

class PDF extends FPDF
{
    private string $txtEntete = '';  // Titre placé dans l'entête
    private string $image = '';      //  image placée à droite dans l'entête
    private string $txtPied = "";    // titre placé à gauche dans le pied
    private bool $pagination = false; // active ou désactive la pagination au centre du pied de page
    private int $taillePolice = 10; // taille initiale de la police
    private string $police = 'helvetica'; // police initiale
    private string $titre;  // titre à afficher au début de chaque page

    // accesseur sur l'attribut privé PageBreakTrigger pour gérer le saut de page
    public function getPageBreakTrigger()
    {
        return $this->PageBreakTrigger;
    }

    // accesseur sur l'attribut privé lMargin
    public function getLeftMargin()
    {
        return $this->lMargin;
    }

    // accesseur sur l'attribut privé rMargin
    public function getRightMargin()
    {
        return $this->rMargin;
    }

    // accesseur sur l'attribut privé rMargin
    public function getTopMargin()
    {
        return $this->tMargin;
    }

    public function getTaillePolice(): int
    {
        return $this->taillePolice;
    }

    public function setTaillePolice($taille)
    {
        $this->taillePolice = $taille;
        $this->SetFont('', '', $this->taillePolice);
    }

    public function getPolice(): string
    {
        return $this->police;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre)
    {
        $this->titre = $titre;
    }

    /**
     * Définition de l'entête des pages pdf
     *
     * @param string $texte string contenant le texte de l'entête
     * @param string $image string contenant le lien vers l'image de droite
     */

    public function setHeader(string $texte = '', string $image = '')
    {
        $this->txtEntete = $texte;
        $this->image = $image;
    }

    /**
     * Définition de pied de page pdf
     *
     * @param $unTexte string contenant le texte du pied
     * @param $pagination int|string pour afficher ou non le numéro de page
     *
     */
    public function setFooter(string $unTexte, bool $pagination = true)
    {
        $this->txtPied = $unTexte;
        $this->pagination = $pagination;
    }

    /**
     * Génération de l'entête
     */

    public function Header()
    {
        if ($this->image != "") {
            $wImage = 20;
            $x = 20;
            $y = $this->getTopMargin();
            $this->image($this->image, $x, $y, $wImage);
        }
        // impression du titre
        if ($this->txtEntete != "") {
            $this->SetFont('Helvetica', 'B', 13);
            $this->text(45, $this->getTopMargin() + 10, $this->txtEntete);
        }
        // $this->Line(20, 33, $this->GetPageWidth() - $this->getRightMargin(), 33);
        // positionnement sur y
        $this->SetY($this->getTopMargin() + 25);
    }

    /**
     * Génération du pied des pages
     */
    public function Footer()
    {
        $this->SetFontSize(9);
        // Texte du pied de page
        if ($this->txtPied != "") {
            $this->SetY(-15); //Positionnement à 1, 5 cm du bas
            // texte sur toute la longueur, hauteur de 10, bordure en haut, curseur à droite, alignement à gauche
            $this->Cell(0, 10, $this->txtPied, 'T', 0, 'L');
        }
        //Numéro de page
        if ($this->pagination == 1) {
            $texte = 'Page ' . $this->PageNo() . '/{nb}';
            $x = $this->GetPageWidth() - $this->getRightMargin() - $this->GetStringWidth($texte) + 4;
            $this->Text($x, $this->GetY() + 5, $texte);
        }
        $this->SetFontSize($this->taillePolice);
    }


    // redéfinition de la méthode addPAge
    public function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        parent::AddPage($orientation, $size, $rotation);
        /*
        if ($this->titre != '') {
            $this->SetFont('', 'B', 12);
            $this->SetX(20);
            $this->SetY(40);
            // texte sur toute la longueur, hauteur de 3, sans bordure, curseur sur ligne suivante, alignement à gauche
            $this->Cell(0, 6, $this->titre, 0, 1, 'L', 0);
            $this->Ln();
            $this->SetFont('', '', '10');
        }
        */
    }

    /**
     * Titre d'une zone de la page :
     *
     * @param string $titre titre à afficher
     * @param string $alignement alignement à appliquer (L, C ou R)
     */
    public function impressionTitre(string $titre, string $alignement = 'L', $couleur = 'n')
    {
        $this->SetFont('Times', 'B', 12);
        $this->SetFillColor(200, 220, 255); //Couleur de fond
        if ($couleur != 'n') {
            $couleur = strtolower($couleur);
            switch ($couleur) {
                case 'r':
                    $this->SetTextColor(255, 0, 0);
                    break;
                case 'v':
                    $this->SetTextColor(0, 255, 0);
                    break;
                case 'b':
                    $this->SetTextColor(0, 0, 255);
                    break;
                default:
                    $this->SetTextColor(0, 0, 0);
                    break;
            }

        }
        if ($alignement != 'L') {
            $alignement = strtoupper($alignement);
            // une valeur différente des 3 valeurs attendues L, C ou R ne pose pas de problème
        }

        $this->Cell(0, 6, $titre, 0, 1, $alignement, 0);
        // $this->Ln();
        $this->SetFont('');
        $this->SetTextColor(0, 0, 0);
    }

    /**
     * Définition d'une ligne d'entête de tableau
     * @param array $lesValeurs contenu de chaque cellule
     * @param array $lesTailles taille de chaque cellule
     * @param array $lesEncadrements encadrement de chaque cellule
     * @param array $lesAlignements alignement de chaque cellule
     * @param int $marge marge de gauche
     * @param int $taillePolice taille de la police
     */
    public function impressionEnteteTableau(array $lesValeurs, array $lesTailles, array $lesEncadrements, array $lesAlignements, int $marge = 20, int $taillePolice = 12)
    {
        $this->setx($marge);
        if ($taillePolice === null) {
            $this->SetFont('', 'B', $this->getTaillePolice());
        } else {
            $this->SetFont('', 'B', $taillePolice);
        }

        $this->SetTextColor(0);
        $this->SetLineWidth(0.1);
        $nb = count($lesValeurs);
        for ($i = 0; $i < $nb; $i++) {
            $this->Cell($lesTailles[$i], 10, $lesValeurs[$i], $lesEncadrements[$i], 0, $lesAlignements[$i], 0);
        }
        $this->Ln();
        $this->SetX($marge);
        $this->SetFont('', '', $this->taillePolice);
    }

    // gestion d'unn tableau avec des lignes de tailles variables
    // Source : http://www.fpdf.org/fr/script/script3.php
    // auteur : oliver@fpdf.org
    // adaptation : guy verghote : intégration des largeurs et des alignements comme paramètre de la méthode

    /**
     * Imprime une ligne d'un tableau en calculant sa hauteur en fonction du contenu des cellules
     * @param array $data
     * @param array $widths
     * @param array $aligns
     * @return void
     */
    public function row(array $data, array $widths, array $aligns)
    {
        $nb = 0;
        // recherche le nombre de lignes dans une cellule
        $nbdata = count($data);
        for ($i = 0; $i < $nbdata; $i++) {
            $nb = max($nb, $this->nbLines($widths[$i], (string) $data[$i]));
        }
        $h = 5 * $nb;
        //Effectue un saut de page si nécessaire
        $this->checkPageBreak($h);
        //Dessine les cellules
        for ($i = 0; $i < $nbdata; $i++) {
            $w = $widths[$i];
            //Sauve la position courante
            $x = $this->GetX();
            $y = $this->GetY();
            //Dessine le cadre
            $this->Rect($x, $y, $w, $h);
            //Imprime le texte
            $this->MultiCell($w, 5, $data[$i], 0, $aligns[$i]);
            //Repositionne à droite
            $this->SetXY($x + $w, $y);
        }
        //Va à la ligne
        $this->Ln($h);
    }

    /**
     * Si la hauteur h provoque un débordement, saut de page manuel
     * @param int $h
     */
    private function checkPageBreak(int $h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);

        }
    }

    /**
     * Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
     * @param int $w
     * @param string $txt
     * @return int $nl nombre de lignes
     */
    private function nbLines(int $w, string $txt): int
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if (($nb > 0) && ($s[$nb - 1] == "\n")) {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
}

<?php
declare(strict_types=1);

/**
 * Classe permettant de gérer des dates
 * @Author : Guy Verghote
 * @Version : 1.0.0
 * @Date : 20/07/2024
 */
class DateFr
{
    private int $nbJour; // Nombre de jours écoulés depuis le lundi 01/01/1900

    // -----------------------------------
    // méthode statiques(à portée classe)
    // -----------------------------------

    /**
     * Retourne true si l'année est bissextile
     * @param int $annee
     * @return bool
     */
    public static function estBissextile(int $annee): bool
    {
        return ($annee % 4 == 0 && $annee % 100 != 0) || $annee % 400 == 0;
    }

    /**
     * Retourne le nombre de jours d'un mois dans une année
     * @param int $mois
     * @param int $annee
     * @return int
     */
    public static function joursDansMois(int $mois, int $annee): int
    {
        if ($mois == 2) {
            return DateFr::estBissextile($annee) ? 29 : 28;
        }
        if ($mois == 4 || $mois == 6 || $mois == 9 || $mois == 11) {
            return 30;
        }
        return 31;
    }

    /**
     * Retourne le nombre de jours dans l'année
     * @param int $annee
     * @return int
     */
    public static function joursDansAnnee(int $annee): int
    {
        return DateFr::estBissextile($annee) ? 366 : 365;
    }

    /**
     * Retourne le nombre de jours écoulés depuis le premier janvier de l'année
     * @param int $jour
     * @param int $mois
     * @param int $annee
     * @return int
     */
    public static function quantieme(int $jour, int $mois, int $annee): int
    {
        $total = $jour;
        $i = 1;
        while ($i != $mois) {
            $total += DateFr::joursDansMois($i, $annee);
            $i++;
        }
        return $total;
    }

    /**
     * Retourne la différence en jours entre 2 années
     * @param int $a1
     * @param int $a2
     * @return int
     */
    public static function joursEntre2Annees(int $a1, int $a2): int
    {
        $annee = $a1;
        $nbjour = 0;
        while ($annee != $a2) {
            $nbjour += DateFr::joursDansAnnee($annee);
            $annee++;
        }
        return $nbjour;
    }

    /**
     * Retourne le nombre de jours entre les deux dates
     * @param DateFr $dateDebut
     * @param DateFr $dateFin
     * @return int
     */
    public static function joursEntre2Dates(DateFr $dateDebut, DateFr $dateFin): int
    {
        return $dateFin->nbJour - $dateDebut->nbJour;
    }

    /**
     * Contrôle la validité de la Date saisie
     * @param int $jour
     * @param int $mois
     * @param int $annee
     * @return bool
     */
    public static function estValide(int $jour, int $mois, int $annee): bool
    {
        return $mois >= 1 && $mois <= 12 && $jour >= 1 && $jour <= DateFr::joursDansMois($mois, $annee) && $annee >= 1900;
    }

    /**
     * Retourne le mois en lettre et sa particule dans un array associatif
     * avec deux clés particule et libelle
     * @param int $mois
     * @return array
     */

    public static function getLeMois(int $mois): array
    {
        $lesMois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        $result['particule'] = "de ";
        if ($mois === 4 || $mois === 8 || $mois === 10) {
            $result['particule'] = "d'";
        }
        $result['libelle'] = $lesMois[$mois - 1];
        return $result;
    }

    /**
     * Retourne la date correspondant au premier jour d'une semaine dans le calendrier français
     * @param int $annee
     * @param int $numSemaine
     * @return DateFr
     */
    public static function getDebutSemaine(int $annee, int $numSemaine): dateFr
    {
        // on se place sur le 4 janvier qui est forcément dans la semaine 1
        $uneDate = new DateFr(4, 1, $annee);
        // On détermine le lundi de la première semaine
        // si le 4/01 est un lundi(1) 0, mardi(2) - 1, ... Dimanche(7) -6 soit 1 - n° du jour
        $uneDate = $uneDate->ajouterJour(1 - $uneDate->getJourSemaine());

        // on calcule le décalage en ajoutant 7 * numSemaine
        return $uneDate->ajouterJour(($numSemaine - 1) * 7);
    }

    // méthodes statiques retournant un objet DateFr


    /**
     * Génération d'un objet DateFr à partir d'une Date au format Mysql aaaa-mm-jj
     * @param string $uneDateMysql
     * @return ?DateFr
     */
    public static function getFromDateMysql(string $uneDateMysql): ?DateFr
    {
        $tab = preg_split("`[/.-]`", $uneDateMysql);

        if (DateFr::estValide((int)$tab[2], (int)$tab[1], (int)$tab[0])) {
            return new DateFr((int)$tab[2], (int)$tab[1], (int)$tab[0]);
        } else {
            return null;
        }
    }

    /**
     * Génération d'un objet DateFr à partir d'une Date au format jj/mm/aaaa
     * quel que soit le séparateur : "/-." et le format j ou jj m ou mm
     * @param string $uneDateFr
     * @return ?DateFr
     */
    public static function getFromDateFr(string $uneDateFr): ?DateFr
    {
        $tab = preg_split("`[/.-]`", $uneDateFr);
        if (DateFr::estValide((int)$tab[0], (int)$tab[1], (int)$tab[2])) {
            return new DateFr((int)$tab[0], (int)$tab[1], (int)$tab[2]);
        } else {
            return null;
        }
    }

    /**
     * Génération d'un objet DateFr à partir d'un autre objet DateFr
     * correspond à la notion de constructeur par copie
     * @param DateFr $uneDate
     * @return DateFr
     */
    public static function getFromObject(DateFr $uneDate): DateFr
    {
        return new DateFr($uneDate->getJour(), $uneDate->getMois(), $uneDate->getAnnee());
    }

    /**
     * Retourne le dimanche de Pâques
     * @param int $annee
     * @return DateFr
     */
    public static function getPaques(int $annee): DateFr
    {
        $var1 = $annee % 19 + 1;
        $var2 = floor(($annee / 100)) + 1; // problème, car le nombre n'est pas arrondi
        $var3 = floor(((3 * $var2) / 4) - 12);
        $var4 = floor((((8 * $var2) + 5) / 25) - 5);
        $var5 = floor(((5 * $annee) / 4) - $var3 - 10);
        $var6 = floor((11 * $var1 + 20 + $var4 - $var3) % 30);
        if (($var6 == 25 && $var1 > 11) || ($var6 == 24)) {
            $var6 = $var6 + 1;
        }
        $var7 = 44 - $var6;
        if ($var7 < 21) {
            $var7 = $var7 + 30;
        }
        $var7 = $var7 + 7;
        $var7 = $var7 - ($var5 + $var7) % 7;
        if ($var7 <= 31) {
            $paques = new DateFr($var7, 3, $annee);
        } else {
            $paques = new DateFr($var7 - 31, 4, $annee);
        }
        return $paques;
    }

    /**
     * Retourne les jours fériés de l'année dans un tableau
     * deux clés : date et libelle
     * @param int $annee
     * @return array
     */

    public static function getLesJoursFeries(int $annee): array
    {
        // paques [22 mars - 25 avril]
        // ascension [30 avril - 2 juin]
        // Pentecôte [11 mai - 13 juin]
        $lesJoursFeries = [];

        $paques = DateFr::getPaques($annee);
        $huitMai = new DateFr(8, 5, $annee);
        $lundiPaques = new DateFr();
        $lundiPaques->nbJour = $paques->nbJour + 1;
        $ascension = new DateFr();
        $ascension->nbJour = $paques->nbJour + 39;
        $pentecote = DateFr::getFromObject($paques);
        $pentecote->ajouterJour(50);
        $premierMai = new DateFr(1, 5, $annee);

        $jour['libelle'] = "Jour de l'an";
        $jour['date'] = new DateFr(1, 1, $annee);
        $lesJoursFeries[] = $jour;

        $jour['libelle'] = "Pâques";
        $jour['date'] = $paques;
        $lesJoursFeries[] = $jour;

        $jour['libelle'] = "Lundi de Pâques";
        $jour['date'] = $lundiPaques;
        $lesJoursFeries[] = $jour;

        if ($ascension->nbJour < $premierMai->nbJour) {
            $jour['libelle'] = "Jeudi de l'Ascension";
            $jour['date'] = $ascension;
            $lesJoursFeries[] = $jour;
            $jour['libelle'] = "Fête du travail";
            $jour['date'] = $premierMai;
            $lesJoursFeries[] = $jour;
            $jour['libelle'] = "Armistice 1945";
            $jour['date'] = new DateFr(8, 5, $annee);
            $lesJoursFeries[] = $jour;
        } elseif ($ascension->nbJour < $huitMai->nbJour) {
            $jour['libelle'] = "Fête du travail";
            $jour['date'] = $premierMai;
            $lesJoursFeries[] = $jour;
            $jour['libelle'] = "Jeudi de l'Ascension";
            $jour['date'] = $ascension;
            $lesJoursFeries[] = $jour;
            $jour['libelle'] = "Armistice 1945";
            $jour['date'] = new DateFr(8, 5, $annee);
            $lesJoursFeries[] = $jour;
        } else {
            $jour['libelle'] = "Fête du travail";
            $jour['date'] = $premierMai;
            $lesJoursFeries[] = $jour;
            $jour['libelle'] = "Armistice 1945";
            $jour['date'] = new DateFr(8, 5, $annee);
            $lesJoursFeries[] = $jour;
            $jour['libelle'] = "Jeudi de l'Ascension";
            $jour['date'] = $ascension;
            $lesJoursFeries[] = $jour;
        }

        $jour['libelle'] = "Lundi de Pentecôte";
        $jour['date'] = $pentecote;
        $lesJoursFeries[] = $jour;

        $jour['libelle'] = "Fête Nationale";
        $jour['date'] = new DateFr(14, 7, $annee);
        $lesJoursFeries[] = $jour;

        $jour['libelle'] = "Assomption";
        $jour['date'] = new DateFr(15, 8, $annee);
        $lesJoursFeries[] = $jour;

        $jour['libelle'] = "Toussaint";
        $jour['date'] = new DateFr(1, 11, $annee);
        $lesJoursFeries[] = $jour;

        $jour['libelle'] = "Armistice 1918";
        $jour['date'] = new DateFr(11, 11, $annee);
        $lesJoursFeries[] = $jour;

        $jour['libelle'] = "Noël";
        $jour['date'] = new DateFr(25, 12, $annee);
        $lesJoursFeries[] = $jour;

        return $lesJoursFeries;
    }

    /**
     * Constructeur d'un objet DateFr
     * En l'absence de paramètre le constructeur retourne la Date courante.
     * Le fait d'accepter un nombre variable de paramètres permet de contourner l'impossibilité de surcharge du contructeur
     * $date = new DateFr()
     * $date = new DateFr("jj/mm/aaaa")
     * $date = new DateFr("aaaa-mm-jj")
     * $date = new DateFr(jj, mm, aaaa)
     */

    public function __construct(...$args)
    {
        $annee = 1900;
        $mois = 1;
        $jour = 1;
        $nb = count($args);
        if ($nb === 0) {
            $annee = Date("Y");
            $mois = Date("m");
            $jour = Date("d");
        } elseif ($nb === 1 && gettype($args[0]) === "string") {
            $valeur = $args[0];
            if (preg_match('#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#', $valeur, $lesElements)) {
                $jour = $lesElements[1];
                $mois = $lesElements[2];
                $annee = $lesElements[3];
            } elseif (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $valeur, $lesElements)) {
                $jour = $lesElements[3];
                $mois = $lesElements[2];
                $annee = $lesElements[1];
            }
        } elseif ($nb === 3) {
            $jour = $args[0];
            $mois = $args[1];
            $annee = $args[2];
        }
        $jour = (int)$jour;
        $mois = (int)$mois;
        $annee = (int)$annee;
        if (DateFr::estValide($jour, $mois, $annee)) {
            $this->nbJour = DateFr::joursEntre2Annees(1900, $annee) + DateFr::quantieme($jour, $mois, $annee);
        } else {
            $this->nbJour = 1;
        }
    }

    /**
     *
     * @return array tableau contenant l'année, le mois et le jour de l'objet DateFr
     */
    private function getLesElements(): array
    {
        $lesElements['annee'] = 1900;
        $lesElements['mois'] = 1;
        $nbjr = $this->nbJour;
        while ($nbjr > DateFr::joursDansAnnee($lesElements['annee'])) {
            $nbjr -= DateFr::joursDansAnnee($lesElements['annee']);
            $lesElements['annee']++;
        }
        while ($nbjr > DateFr::joursDansMois($lesElements['mois'], $lesElements['annee'])) {
            $nbjr -= DateFr::joursDansMois($lesElements['mois'], $lesElements['annee']);
            $lesElements['mois']++;
        }
        $lesElements['jour'] = $nbjr;
        return $lesElements;
    }

    // -----------------------------------
    // méthode sur les objets
    // -----------------------------------

    /**
     *
     * Nombre de jours écoulés depuis le 01/01/1900
     */
    public function getNbJour(): int
    {
        return $this->nbJour;
    }

    /**
     * Jour en chiffre
     */
    public function getJour(): int
    {
        return $this->getLesElements()['jour'];
    }

    /**
     *
     * Jour en lettre
     */
    public function getJourEnLettre(): string
    {
        $lesJours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        return $lesJours[$this->getJourSemaine() - 1];
    }

    /**
     * Retourne le mois en chiffre
     */
    public function getMois(): int
    {
        $lesElements = $this->getLesElements();
        return $lesElements['mois'];
    }

    /**
     * Retourne le mois en lettre
     */
    public function getMoisEnLettre(): string
    {
        $lesMois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        return $lesMois[$this->getMois() - 1];
    }

    /**
     * Retourne l'année
     */
    public function getAnnee(): int
    {
        return $this->getLesElements()['annee'];
    }

    /**
     * Retourne le numéro du jour dans la semaine (lundi = 1, mardi = 2....)
     */
    public function getJourSemaine(): int
    {
        return ($this->nbJour - 1) % 7 + 1;
    }

    /**
     * Retourne le nombre de jours écoulés depuis le premier janvier de l'année
     */
    public function getQuantieme(): int
    {
        return DateFr::quantieme($this->getJour(), $this->getMois(), $this->getAnnee());
    }

    /**
     * Retourne le nombre de jours dans le mois
     */
    public function getNbJoursMois(): int
    {
        return DateFr::joursDansMois($this->getMois(), $this->getAnnee());
    }

    /**
     * Retourne le numéro de la semaine numérotation française
     */
    public function getNumeroSemaine(): int
    {
        // pour calculer le nombre de semaines, il faut trouver la date de référence de la première semaine
        // ce n'est pas chose facile, mais on peut simplifier le traitement à l'aide de deux subtilités :

        // Au lieu de réfléchir à partir de l'objet DateFr, on se place sur le jeudi de la même semaine,
        // car on sait que ce jeudi est forcément dans l'année à prendre en compte pour la numérotation
        // La codification des jours lundi = 1, ... jeudi = 4 dont pour se placer sur le jeudi : 4 - jourSemaine()
        $jeudi = $this->ajouterJour(4 - $this->getJourSemaine());

        // Séconde subtilité : le 4 janvier est forcément dans la semaine 1
        $le4janvier = new DateFr(4, 1, $jeudi->getAnnee());

        // reste à se mettre sur le lundi de cette semaine
        $lundi = $le4janvier->ajouterJour(1 - $le4janvier->getJourSemaine());
        // on fait la différence entre $lundi et $this et on divise cette différence par 7.

        return intval(($this->nbJour - $lundi->nbJour) / 7) + 1;
    }

    /**
     * Retourne une date augmentée de $nb jours (l'objet DateFr n'est pas modifié)
     * @param int $nb
     * @return DateFr
     */
    public function ajouterJour(int $nb): Datefr
    {
        $res = new DateFr();
        $res->nbJour = $this->nbJour + $nb;
        return $res;
    }

    /**
     * Retourne une date diminuée de $nb jours (l'objet DateFr n'est pas modifié)
     * @param int $nb
     * @return DateFr
     */
    public function retirerJour(int $nb): DateFr
    {
        $res = new DateFr();
        $res->nbJour = $this->nbJour - $nb > 0 ? $this->nbJour - $nb : 1;
        return $res;
    }

    /**
     * Retourne une date augmentée de $nb mois (l'objet DateFr n'est pas modifié)
     * Si on part du dernier jour du mois, on doit retomber sur le dernier jour
     *
     * @param int $nb nombre de mois à ajouter
     * @return DateFr
     */
    public function ajouterMois(int $nb): DateFr
    {
        $lesElements = $this->getLesElements();
        $jour = $lesElements["jour"];
        $mois = $lesElements["mois"];
        $annee = $lesElements["annee"];

        for ($i = 1; $i <= $nb; $i++) {
            $mois++;
            if ($mois > 12) {
                $mois = 1;
                $annee++;
            }
        }
        if ($jour > DateFr::joursDansMois($mois, $annee)) {
            $jour = DateFr::joursDansMois($mois, $annee);
        }
        $res = new DateFr();
        $res->nbJour = DateFr::joursEntre2Annees(1900, $annee) + DateFr::quantieme($jour, $mois, $annee);
        return $res;
    }

    /**
     * Retourne une date diminuée de $nb mois (l'objet DateFr n'est pas modifié)
     * Si on part du dernier jour du mois, on doit retomber sur le dernier jour
     * @param int $nb
     * @return DateFr
     */
    public function retirerMois(int $nb): DateFr
    {
        $lesElements = $this->getLesElements();
        $jour = $lesElements["jour"];
        $mois = $lesElements["mois"];
        $annee = $lesElements["annee"];
        for ($i = 1; $i <= $nb; $i++) {
            $mois--;
            if ($mois < 1) {
                $mois = 12;
                $annee--;
                if ($annee < 1900) {
                    $this->nbJour = 1;
                    return $this;
                }
            }
        }
        if ($jour > DateFr::joursDansMois($mois, $annee)) {
            $jour = DateFr::joursDansMois($mois, $annee);
        }
        $res = new DateFr();
        $res->nbJour = DateFr::joursEntre2Annees(1900, $annee) + DateFr::quantieme($jour, $mois, $annee);
        return $res;

    }

    /**
     * Retourne une date augmentée de $nb annee (l'objet DateFr n'est pas modifié)
     * Si on part du dernier jour du mois, on doit retomber sur le dernier jour
     *
     * @param int $nb nombre d'années à ajouter
     * @return DateFr
     */
    public function ajouterAnnee(int $nb): DateFr
    {
        $lesElements = $this->getLesElements();
        $jour = $lesElements["jour"];
        $mois = $lesElements["mois"];
        $annee = $lesElements["annee"] + $nb;


        if ($jour > DateFr::joursDansMois($mois, $annee)) {
            $jour = DateFr::joursDansMois($mois, $annee);
        }

        $res = new DateFr();
        $res->nbJour = DateFr::joursEntre2Annees(1900, $annee) + DateFr::quantieme($jour, $mois, $annee);
        return $res;
    }

    /**
     * Retourne une date diminuée de $nb années (l'objet DateFr n'est pas modifié)
     *
     * @param int $nb nombre d'années à retirer
     * @return DateFr
     */
    public function retirerAnnee(int $nb): DateFr
    {
        $lesElements = $this->getLesElements();
        $jour = $lesElements["jour"];
        $mois = $lesElements["mois"];
        $annee = $lesElements["annee"] - $nb;

        if ($annee < 1900) {
            $this->nbJour = 1;
            return $this;
        }

        if ($jour > DateFr::joursDansMois($mois, $annee)) {
            $jour = DateFr::joursDansMois($mois, $annee);
        }

        $res = new DateFr();
        $res->nbJour = DateFr::joursEntre2Annees(1900, $annee) + DateFr::quantieme($jour, $mois, $annee);
        return $res;
    }

    /**
     *
     * @param DateFr $uneDate
     * @return bool {boolean} true si les dates sont identiques
     */
    public function estEgale(DateFr $uneDate): bool
    {
        return $this->nbJour == $uneDate->nbJour;
    }

    /**
     * @param DateFr $uneDate
     * @return bool
     */
    public function estPlusGrande(DateFr $uneDate): bool
    {
        return $this->nbJour > $uneDate->nbJour;
    }

    /**
     * @param DateFr $uneDate
     * @return bool
     */
    public function estPlusPetite(DateFr $uneDate): bool
    {
        return $this->nbJour < $uneDate->nbJour;
    }

    /**
     * @param DateFr $uneDate
     * @return int {integer} écart en jours avec la date passée en paramètre
     */

    public function ecartEnJours(DateFr $uneDate): int
    {
        return $this->nbJour - $uneDate->nbJour;
    }

    /**
     *
     * @return bool true si la date correspond à un jour férié français
     */
    public function estFerie(): bool
    {
        $lesElements = $this->getLesElements();
        $annee = $lesElements['annee'];
        $mois = $lesElements['mois'];
        $jour = $lesElements['jour'];
        $reponse = false;
        if (($mois == 1 && $jour == 1) || ($mois == 5 && $jour == 1) ||
            ($mois == 5 && $jour == 8) || ($mois == 7 && $jour == 14) ||
            ($mois == 8 && $jour == 15) || ($mois == 11 && $jour == 11) ||
            ($mois == 11 && $jour == 1) || ($mois == 12 && $jour == 25)) {
            $reponse = true;
        } else {
            $var1 = $annee % 19 + 1;
            $var2 = floor(($annee / 100)) + 1; // problème, car le nombre n'est pas arrondi
            $var3 = ((3 * $var2) / 4) - 12;
            $var4 = (((8 * $var2) + 5) / 25) - 5;
            $var5 = ((5 * $annee) / 4) - $var3 - 10;
            $var6 = intval(11 * $var1 + 20 + $var4 - $var3) % 30;
            if (($var6 == 25 && $var1 > 11) || ($var6 == 24)) {
                $var6 = $var6 + 1;
            }
            $var7 = 44 - $var6;
            if ($var7 < 21) {
                $var7 = $var7 + 30;
            }
            $var7 = $var7 + 7;
            $var7 = intval($var7 - ($var5 + $var7)) % 7;
            if ($var7 <= 31) {
                $paques = new DateFr($var7, 3, $annee);
            } else {
                $paques = new DateFr($var7 - 31, 4, $annee);
            }
            $lundiPaques = new DateFr($paques->getJour(), $paques->getMois(), $paques->getAnnee());
            $lundiPaques->ajouterJour(1);

            $ascension = new DateFr($paques->getJour(), $paques->getMois(), $paques->getAnnee());
            $ascension->ajouterJour(39);
            $pentecote = new DateFr($paques->getJour(), $paques->getMois(), $paques->getAnnee());
            $pentecote->ajouterjour(50);
            if ($this->nbJour == $paques->nbJour || $this->nbJour == $lundiPaques->nbJour || $this->nbJour == $ascension->nbJour || $this->nbJour == $pentecote->nbJour) {
                $reponse = true;
            }
        }
        return $reponse;
    }

    /**
     * @return string nom du jour férié. "Ce n'est pas un jour férié" si la date ne correspond pas à un jour férié
     */
    public function getNomJourFerie(): string
    {
        $nom = "Ce n'est pas un jour férié";
        $lesElements = $this->getLesElements();
        $annee = $lesElements['annee'];
        $mois = $lesElements['mois'];
        $jour = $lesElements['jour'];
        $paques = DateFr::getPaques($annee);
        if ($mois == 1 && $jour == 1) {
            $nom = "jour de l'An";
        } elseif ($mois == 5 && $jour == 1) {
            $nom = "Fête de Travail";
        } elseif ($mois == 5 && $jour == 8) {
            $nom = "victoire 1945";
        } elseif ($mois == 7 && $jour == 14) {
            $nom = "Fête Nationale";
        } elseif ($mois == 8 && $jour == 15) {
            $nom = "Assomption";
        } elseif ($mois == 11 && $jour == 1) {
            $nom = "Toussaint";
        } elseif ($mois == 11 && $jour == 11) {
            $nom = "Armistice 1918";
        } elseif ($mois == 12 && $jour == 25) {
            $nom = "Noël";
        }
        if ($this->nbJour == $paques->nbJour) {
            $nom = "Pâques";
        } elseif ($this->nbJour == $paques->nbJour + 1) {
            $nom = "Lundi de Pâques";
        } elseif ($this->nbJour == $paques->nbJour + 39) {
            $nom = "Ascencion";
        } elseif ($this->nbJour == $paques->nbJour + 50) {
            $nom = "Lundi de Pentecôte";
        }
        return $nom;
    }

    /**
     *
     * @return bool true si le jour est un jour ouvrable : du lundi au vendredi sauf jour férié
     */
    public function estJourOuvrable(): bool
    {
        return !$this->estFerie() && $this->getJourSemaine() < 6;
    }

    /**
     *
     * @return DateFr jour ouvrable suivant : du lundi au vendredi sauf jour férié
     */
    public function getJourOuvrableSuivant(): DateFr
    {
        $jourOuvrableSuivant = new DateFr();
        $jourOuvrableSuivant->nbJour = $this->nbJour + 1;
        while (!$jourOuvrableSuivant->estJourOuvrable()) {
            $jourOuvrableSuivant->nbJour += 1;
        }
        return $jourOuvrableSuivant;
    }

    /**
     * @return DateFr jour ouvrable précédent : du lundi au vendredi sauf jour férié
     */

    public function getJourOuvrablePrecedent(): DateFr
    {
        $jourOuvrablePrecedent = new DateFr();
        $jourOuvrablePrecedent->nbJour = $this->nbJour - 1;
        while (!$jourOuvrablePrecedent->estJourOuvrable()) {
            $jourOuvrablePrecedent->nbJour -= 1;
        }
        return $jourOuvrablePrecedent;
    }

    /**
     * @param string $separateur
     * @return string  date au format jj/mm/aaaa
     */
    public function toFormatCourt(string $separateur = "/"): string
    {
        $lesElements = $this->getLesElements();
        $a = $lesElements['annee'];
        $m = strval($lesElements['mois']);
        $j = strval($lesElements['jour']);
        return ((strlen($j) < 2) ? "0" : "") . $j . $separateur . (strlen($m) < 2 ? "0" : "") . $m . $separateur . $a;
    }

    /**
     * @return string date au format au format jjjj jj mmmm aaaa en remplaçant jj par 1er lorsque j = 1j
     */
    public function toFormatLong(): string
    {
        $lesElements = $this->getLesElements();
        $a = $lesElements['annee'];
        $j = $lesElements['jour'];
        $resultat = $this->getJourEnLettre() . " ";
        if ($j == 1) {
            $resultat .= "1er";
        } else {
            $resultat .= ((strlen(strval($j)) < 2) ? "0" : "") . $j;
        }
        $resultat .= " " . $this->getMoisEnLettre() . " " . $a;
        return $resultat;
    }

    /**
     * retourne la DateFr au format Mysql aaaa-mm-jj
     * @return string date au format mySQL
     */
    public function toFormatMySQL(): string
    {
        $lesElements = $this->getLesElements();
        return $lesElements['annee'] . "-" . ((strlen(strval($lesElements['mois'])) < 2) ? "0" : "") . $lesElements['mois'] . "-" . ((strlen(strval($lesElements['jour'])) < 2) ? "0" : "") . $lesElements['jour'];
    }
}

<?php
declare(strict_types=1);

/**
 * Classe permettant de gérer la journalisation des événements dans différents journaux
 * Nécessite un fichier de configuration journal.php stocké dans le répertoire /.config du projet
 * Le fichier de configuration doit retourner un tableau associatif dont les clés sont les noms des journaux
 * @Author : Guy Verghote
 * @Version : 1.1.0
 * @Date : 29/07/2024
 */

class Journal
{
    const REPERTOIRE = '/.log';

    /**
     * Retourne la liste des journaux gérés par l'application
     * @return array
     */
    public static function getListe(): array
    {
        $configFile = $_SERVER['DOCUMENT_ROOT']  . "/.config/journal.php";
        // Vérification de l'existence du fichier de configuration
        if (!file_exists($configFile)) {
            Erreur::envoyerReponse('Fichier de configuration des journaux introuvable', 'global');
        }
        $lesJournaux = require $configFile;

        if (!is_array($lesJournaux)) {
            Erreur::envoyerReponse("Le fichier de configuration des journaux n'est pas valide", 'global');
        }
        return $lesJournaux;
    }

    /**
     * Retourne le nom du fichier log correspondant au nom est passé en paramètre
     * @param string $nom
     * @return string
     */
    private static function getByName(string $nom): string
    {
        $lesJournaux = self::getListe();

        // vérification de l'existence du journal
        if (!isset($lesJournaux[$nom])) {
            Erreur::envoyerReponse("La demande ne fait pas référence à un journal géré dans l'application", 'global');
        }
        $racine = $_SERVER['DOCUMENT_ROOT'];
        $repertoire = self::REPERTOIRE;
        $fichier = "$racine/$repertoire/$nom.log";
        return "$racine/$repertoire/$nom.log";
    }

    /**
     *  Mémoriser un événement dans un fichier log
     * @param string $evenement description de l'événement
     * @param string $journal nom du fichier log utilisé
     */
    public static function enregistrer(string $evenement, string $journal = 'evenement'): void
    {
        $fichier = self::getByName($journal);
        $date = date('d/m/Y H:i:s');
        $script = $_SERVER['SCRIPT_NAME'];
        $ip = self::getIp();
        $file = fopen($fichier, 'a');
        fwrite($file, "$date;$evenement;$script;$ip\n");
    }

    /**
     * Supprime le fichier log dont le nom est passé en paramètre
     * @param string $nom
     */
    public static function supprimer(string $nom): void
    {
        $fichier = self::getByName($nom);
        unlink($fichier);
    }

    /**
     * Retourne les événements du journal passé en paramètre dans un tableau
     * @param string $journal
     * @return array
     */
    public static function getLesEvenements(string $journal = 'evenement'): array
    {
        $fichier = self::getByName($journal);
        if (!file_exists($fichier)) {
            Erreur::envoyerReponse('Ce journal est vide', 'global');
        }
        $lignes = file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lignes = array_reverse($lignes);
        $lesLignes = [];
        foreach ($lignes as $ligne) {
            $lesLignes[] = explode(';', $ligne);
        }
        return $lesLignes;
    }

    /**
     * Retourne l'adresse ip du client connecté à l'application web mais sans garantie
     * @return string
     */
    public static function getIp(): string
    {
        if (isset ($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset ($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}

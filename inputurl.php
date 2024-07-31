<?php
declare(strict_types=1);

/**
 * Classe InputUrl : contrôle une URL
 * @Author : Guy Verghote
 * @Version : 1.0.0
 * @Date : 20/07/2024
 */
 
class InputUrl extends Input
{
    // attribut permettant de préciser si l'on souhaite vérifier l'existence de l'url
    public bool $VerifierExistence = false;

    /**
     * Redéfinition de la méthode checkValidity
     * @return bool
     */
    public function checkValidity(): bool
    {
        $valide = true;

        if (!parent::checkValidity()) {
            $valide = false;
        } elseif ($this->Value !== null) {
            $valeur = (string)$this->Value;
            if (!filter_var($valeur, FILTER_VALIDATE_URL)) {
                $this->validationMessage = "URL non valide";
                $valide = false;
            } elseif ($this->VerifierExistence) {
                $correct = $this->verifyUrlExistence($valeur);
                if (!$correct) {
                    $this->validationMessage = "ne correspond pas à une url existante";
                    $valide = false;
                }
            }
        }
        return $valide;
    }

    /**
     * Vérifie si l'url existe
     * @param string $url
     * @return bool
     */
    private function verifyUrlExistence(string $url): bool
    {
        $f = @fopen($url, "r");
        if ($f) {
            fclose($f);
            return true;
        }
        $f = @fopen($url . "/index.php", "r");
        if ($f) {
            fclose($f);
            return true;
        }
        return false;
    }
}

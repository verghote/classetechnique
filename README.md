# Ensemble des classes PHP pouvant être utilisées dans tous les projets

| Classe                    | Rôle                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
|---------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Database                  | Assure la connexion à la base de données. Les paramètres de connexion sont conservés dans le fichier de configuration /.config/database.php                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| Erreur                    | Propose une méthode envoyerErreur() qui va en fonction du type de l'appel du script en cours (direct, ou ajax) rediriger vers la page d'erreur ou retourner dans le format JSON le message d'erreur et son type                                                                                                                                                                                                                                                                                                                                                                                                              |
| Input et classes dérivées | Ensemble des classes permettant de contrôler les données en fonction de leur type (texte, entier, date, liste). Possibilité de paramétrer les règles de vérification (min, max, pattern)                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| Journal                   | Permet d'enregistrer des événements dans des fichiers de journalisations. On mémorise, la date et l'heure, l'événement, le script et l'adresse IP                                                                                                                                                                                                                                                                                                                                                                                                                                                                            |
| Mail                      | Elle permet d'envoyer des mails simples et des mails avec pièces jointes. Elle repose sur la librairie très répandue PHPMAILER (https://github.com/PHPMailer/PHPMailer) qui a l'avantage de pouvoir facilement paramétrer le SMTP pour l'envoi, faire des mails en HTML, et inclure des pièces jointes. La classe Mail propose deux méthodes : envoyer(string $destinataire, string $sujet, string $message) envoyerAvec(string $destinataire, string $sujet, string $message, string $piece) Les paramètres de configuration (host, user, password, etc.) sont conservés dans le fichier de configuration /.config/mail.php |
| Select                    | Classe permettant de lancer des requêtes SQL de consultation.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| Std                       | La méthode formatValide($valeur, 'format') permet de vérifier le respect des principaux formats de données à l'aide d'expressions régulières. Le paramètre 'format' codifie chaque expression régulière.                                                                                                                                                                                                                                                                                                                                                                                                                     |
| Table                     | Classe abstraite dont la plupart des classes métier dérivent afin de rendre totalement standard les opérations d'ajout, de modification et de suppression sur une table de la base de données possédant une clé primaire simple.                                                                                                                                                                                                                                                                                                                                                                                             |
| Formulaire                | Classe permettant d'automatiser les contrôles sur les champs des formulaires HTML.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| Fichier                   | Classe permettant de manipuler les fichiers. Elle propose des méthodes pour lire, écrire, supprimer, renommer, etc.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| Fpdf                      | Classe permettant de générer des fichiers PDF. Elle repose sur la librairie très répandue FPDF (http://www.fpdf.org/) qui a l'avantage de pouvoir facilement générer des fichiers PDF.                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| Pdf                       | Classe dérivant de la classe Fpdf permettant de personnaliser l'entête et le pied de page.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   |
| Jeton                     | Classe permettant de générer des jetons pour sécuriser les formulaires.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| StatPage                  | Classe permettant de mémoriser les statistiques de consultation des pages.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   |
| StatVisite                | Classe permettant de mémoriser les statistiques de consultation des visiteurs.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| TraficIp                  | Classe permettant de mémoriser l'ensemble du trafic en le stockant dans la table de même nom.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| Tableau                   | Classe permettant de générer des tableaux HTML à partir de données.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
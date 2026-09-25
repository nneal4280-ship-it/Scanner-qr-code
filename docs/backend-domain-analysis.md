# Analyse du domaine backend

Date : 2026-09-25  
Source : `Diagramme neal atangana.docx` (quatre images EMF extraites et rendues localement).  
Périmètre : backend uniquement; aucune migration ni vue créée pendant cette phase.

## 1. Audit du projet

| Élément | Constat |
| --- | --- |
| Laravel / PHP | Laravel 12.69.2 / PHP 8.2.0 |
| Base configurée | MySQL local (`mon_projet_db`); les migrations actuellement exécutées sont users, cache et jobs |
| Authentification | Laravel Breeze 2.4, routes et contrôleurs dans `routes/auth.php` |
| User | `App\\Models\\User`, `id`, `name`, `email`, mot de passe hashé, vérification email |
| Routes métier | Aucune route présence/absence; seul `routes/web.php` est déclaré dans `bootstrap/app.php` |
| Policies / Events / Listeners / Services / Enums | Aucun élément métier existant |
| Infrastructure | Queue et cache configurés sur base de données; notifications Laravel disponibles |
| Tests | Tests Breeze/Profile et exemples présents |

Breeze est fonctionnel et doit rester inchangé. Le terme UML `utilisateur` sera donc raccordé à `User`, sans second système d'authentification.

## 2. Acteurs

| Acteur | Responsabilités | Cas associés |
| --- | --- | --- |
| Personnel | Pointer, consulter son profil, soumettre un justificatif | Effectuer pointage; Soumettre justificatif d'absence; Consulter profil |
| Responsable du personnel | Rechercher les collaborateurs, apprécier les justificatifs, gérer les présences | Rechercher personnel; Apprécier justificatif; Gérer fiche présences; Produire statistiques |
| Chef de centre | Contrôler les statistiques | Valider statistiques |
| Administrateur | Administrer les utilisateurs | Gérer utilisateurs |
| API de géolocalisation | Système externe associé au contrôle de position | Me localiser / Effectuer pointage |

`S'authentifier` est un cas inclus par les opérations protégées et correspond à Breeze existant.

## 3. Cas d'utilisation

### Effectuer un pointage

- Acteur principal : Personnel; secondaire : API de géolocalisation.
- Objectif : enregistrer une présence.
- Préconditions nécessaires : session authentifiée, contexte de pointage valide, coordonnées vérifiables si exigées.
- Résultat : pointage horodaté par le serveur, associé à l'utilisateur.
- Relations visibles : `<<include>> S'authentifier`; `<<extend>> Scanner_QR_code`, `Me_localiser`, `Valider`.
- Le diagramme ne définit pas les horaires, le rayon, la durée du QR ni les états arrivée/départ.

### Scanner un QR code

- Contexte : extension de `Effectuer_pointage`.
- Résultat attendu : validation backend d'un jeton temporaire, avec expiration et anti-rejeu.
- La mission impose que les données sensibles ne soient pas mises directement dans le QR.

### Me localiser

- Personnel et API de géolocalisation.
- Contexte : extension du pointage.
- Le backend reçoit latitude, longitude et éventuellement précision/heure de collecte, puis calcule la distance; Nominatim ne fournit pas la position GPS du client.

### Soumettre un justificatif d'absence

- Acteur : Personnel.
- Objectif : soumettre une absence et un justificatif éventuel.
- Résultat : demande avec statut initial traçable.
- Relations visibles : `<<include>> S'authentifier`; extensions `Importer_fichier`, `Vérification_du_statut`, `Valider`.
- L'acteur exact de la vérification/validation est ambigu dans l'image; le rôle du responsable est toutefois visible dans le diagramme global.

### Importer fichier

- Extension de la soumission.
- Le fichier devra être validé par MIME, taille et nom sûr; le diagramme de classes ne montre pas son stockage.

### Rechercher personnel

- Acteur : Responsable du personnel.
- Retour attendu : collaborateurs accessibles dans son périmètre.

### Apprécier justificatif

- Acteur : Responsable du personnel.
- Retour attendu : décision enregistrée et traçable.

### Gérer fiche présences

- Acteur : Responsable du personnel.
- Retour attendu : consultation/gestion autorisée des présences; toute modification doit être explicitement autorisée.

### Produire les statistiques / Valider statistiques

- `Produire_les_statistiques` : Responsable du personnel; agrégats filtrables à préparer.
- `Valider_statistiques` : Chef de centre; contrôle des statistiques produites.

### Gérer utilisateurs

- Acteur : Administrateur.
- Le diagramme ne détaille ni CRUD ni catalogue de permissions; aucun package de permissions n'est justifié à ce stade.

### Consulter profil

- Acteur : Personnel.
- Le profil Breeze existe déjà; la classe UML `Profil` doit être distinguée du profil d'authentification avant toute nouvelle table.

## 4. Classes UML

### Utilisateur

Attributs visibles : `id_personnel:int`, `Nom:String`, `Prenom:String`, `MotDePasse:String`. Méthodes : `se_connecter()`, `se_deconnecter()`. Correspondance technique actuelle : `User`; le code existant impose aussi email, vérification et timestamps.

### Personnel / Responsable du personnel / Chef de centre / Administrateur

Ces quatre classes sont reliées par héritage à `Utilisateur`. `Chef de centre` montre `convoquer_personnel()` et `valider_justificatif()`. L'Administrateur affiche `GererUtilisateur:int`, lecture incertaine (attribut ou méthode mal positionné). Les autres attributs ne sont pas visibles.

### Profil

Attributs : `id_profil:int`, `DateCreation:Date`. Association avec `Utilisateur` visible mais cardinalités difficiles à lire.

### Pointage

Attributs : `id_pointage:int`, `dateheure:Date`, `type:boolean`. Association `Utilisateur` — `Effectuer` — `Pointage`; l'image semble montrer `0..1` côté utilisateur et `0..*` côté pointage. Le booléen ne suffit pas nécessairement à distinguer arrivée/départ : ne pas décider silencieusement.

### Justificatif

Attributs visibles : `id_justificatif:int`, `Statut` et `motif` (types peu lisibles), `DateSoumission:Date`. L'utilisateur soumet; le responsable du personnel statue. Aucun chemin de fichier n'est visible.

### Rapport

Attributs : `id_rapport:int`, `DateGeneration:Date`, `contenu:String`. Le responsable du personnel produit des rapports; format d'export non défini.

## 5. Relations observées

| Relation | Cardinalité / nature | Confiance |
| --- | --- | --- |
| Personnel —|> Utilisateur | héritage | élevée |
| Responsable du personnel —|> Utilisateur | héritage | élevée |
| Chef de centre —|> Utilisateur | héritage | élevée |
| Administrateur —|> Utilisateur | héritage | élevée |
| Utilisateur — Profil | environ `0..1` / `0..*`, `consulter` | moyenne; à confirmer |
| Utilisateur — Pointage | environ `0..1` / `0..*`, `Effectuer` | moyenne |
| Utilisateur — Justificatif | environ `0..1` / `0..*`, `Soumettre` | moyenne |
| Responsable du personnel — Justificatif | environ `0..1` / `0..*`, `Statuer` | moyenne |
| Responsable du personnel — Rapport | environ `0..1` / `0..*`, `produire` | moyenne |

## 6. Première correspondance base de données

| Élément UML / besoin | Table envisagée | Justification | Statut |
| --- | --- | --- | --- |
| Utilisateur | `users` existante | Authentification Breeze | CONFIRMÉ PAR LE DIAGRAMME |
| Spécialisations utilisateur | rôle/attribut ou structure dédiée à décider | Héritages visibles, stratégie non indiquée | AMBIGU |
| Profil | `profiles` possible | Classe et attributs visibles; relation à confirmer | CONFIRMÉ PAR LE DIAGRAMME |
| Pointage | `pointages` possible | Classe et cas d'utilisation visibles | CONFIRMÉ PAR LE DIAGRAMME |
| Justificatif | `justificatifs` possible | Classe et flux visibles | CONFIRMÉ PAR LE DIAGRAMME |
| Fichier justificatif | chemin de fichier ou table technique | `Importer_fichier` visible, stockage absent du diagramme | REQUIS PAR LE BESOIN |
| Rapport | `rapports` possible | Classe visible et production de statistiques | CONFIRMÉ PAR LE DIAGRAMME |
| QR temporaire | stockage technique à étudier | QR dynamique, expiration et anti-rejeu imposés par le besoin | TECHNIQUE NÉCESSAIRE |
| Site / zone | stockage technique à étudier | contrôle de distance et rayon imposé par le besoin | TECHNIQUE NÉCESSAIRE |
| Alertes / anomalies | non retenu avant confirmation | présentes dans le texte fonctionnel mais absentes des diagrammes | REQUIS PAR LE BESOIN |
| Authentification | tables Breeze existantes | déjà implémentée | NON NÉCESSAIRE |

## 7. Points à résoudre avant la phase C

1. Persistance des quatre spécialisations : rôle sur `users` ou autre stratégie.
2. Cardinalités exactes de Profil, Pointage, Justificatif et Rapport.
3. Signification de `Pointage.type:boolean`.
4. Valeurs et transitions exactes de `Justificatif.Statut`.
5. Absence dans le diagramme de Site, QR, anomalie et alerte.
6. Incohérence apparente entre `Chef de centre.valider_justificatif()` et le cas `Apprécier justificatif` porté par le responsable.

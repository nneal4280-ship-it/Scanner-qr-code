# Matrice de traçabilité backend

Cette matrice est la référence de la suite du développement. `À définir` signifie qu'aucun code métier ne doit encore être généré pour cette colonne sans décision de conception.

| Cas d'utilisation | Entité | Table | Modèle | Policy | Request | Controller | Route | Test |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| S'authentifier | Utilisateur | `users` + tables Breeze | `User` | Existant | `LoginRequest` | `AuthenticatedSessionController` | `routes/auth.php` | `tests/Feature/Auth/*` |
| Consulter profil | Utilisateur / Profil à confirmer | `users`; `profiles` à confirmer | `User`; `Profile` à confirmer | À définir | `ProfileUpdateRequest` | `ProfileController` | `/profile` | `ProfileTest` |
| Effectuer pointage | Utilisateur / Pointage | `users`; `pointages` à concevoir | `User`; `Pointage` | Personnel authentifié | À définir | À définir | À définir | valide, doublon, horaires, historique |
| Scanner QR | Jeton QR / Pointage | table technique à justifier | service/modèle à définir | anti-rejeu | À définir | À définir | À définir | expiré, invalide, rejeu |
| Me localiser | Pointage / zone | tables à concevoir | service géolocalisation | validation backend | À définir | À définir | À définir | coordonnées invalides, hors zone |
| Soumettre justificatif | Utilisateur / Justificatif | `users`; `justificatifs` | `User`; `Justificatif` | propriétaire | À définir | À définir | À définir | création, accès d'autrui |
| Importer fichier | Justificatif / fichier | colonne ou stockage à décider | `Justificatif` | propriétaire, MIME, taille | À définir | À définir | À définir | fichier valide/invalide |
| Vérifier statut | Justificatif | `justificatifs` | `Justificatif` | transitions à définir | À définir | À définir | À définir | statuts |
| Apprécier justificatif | Justificatif / responsable | `justificatifs` | `Justificatif` | périmètre responsable | À définir | À définir | À définir | valider/refuser |
| Rechercher personnel | Utilisateur | `users` + périmètre à décider | `User` | périmètre responsable | À définir | À définir | À définir | hors périmètre |
| Gérer fiche présences | Pointage / Utilisateur | `pointages` | `Pointage` | responsable autorisé | À définir | À définir | À définir | historique |
| Produire statistiques | Pointage / Justificatif / Rapport | tables à concevoir | modèles à concevoir | responsable | filtres à définir | À définir | À définir | agrégats |
| Valider statistiques | Rapport | `rapports` | `Rapport` | chef de centre | À définir | À définir | À définir | autorisation |
| Gérer utilisateurs | Utilisateur / rôle | `users` + stratégie à décider | `User` | administrateur | À définir | À définir | À définir | autorisation admin |

## État

- Authentification et profil : déjà couverts par Breeze.
- Domaine présence/absence : aucune implémentation existante.
- Migrations métier : aucune, conformément à la première action demandée.
- Décisions de phase C : rôles, cardinalités opérationnelles, statuts, modèle QR et géolocalisation sont documentés dans `backend-assumptions.md`.

## Couverture après implémentation initiale

Les éléments suivants sont maintenant implémentés : migrations, modèles, enums, services, policies, Requests, contrôleurs, routes, seeders et tests Feature du pointage QR/géofence et des justificatifs. Les contrats détaillés sont dans `backend-api-contract.md`; les décisions sont dans `backend-assumptions.md`.

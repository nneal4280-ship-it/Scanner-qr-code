# Contrat backend initial

Toutes les routes ci-dessous nécessitent une session Breeze authentifiée. Les réponses métier sont JSON; aucun frontend n'est créé.

| Méthode | Route | Autorisation | Entrée principale | Réponse |
| --- | --- | --- | --- | --- |
| GET | `/attendance` | utilisateur authentifié | pagination | historique de ses pointages |
| POST | `/attendance/qr-tokens` | responsable, chef de centre ou admin | `site_id` | token opaque, expiration, site |
| POST | `/attendance` | utilisateur autorisé | `site_id`, `qr_token`, `type`, `latitude`, `longitude`, `accuracy_meters?` | pointage créé; 422 si QR/zone/doublon invalide |
| GET | `/absences` | utilisateur authentifié | pagination | ses justificatifs |
| POST | `/absences` | utilisateur authentifié | `reason`, `starts_on`, `ends_on`, `file?` | justificatif pending |
| GET | `/absences/{justificatif}` | propriétaire ou reviewer | — | justificatif |
| PATCH | `/absences/{justificatif}/review` | responsable de périmètre, chef ou admin | `status`, `review_comment?` | décision enregistrée |
| GET | `/supervision/personnel` | supervision/admin | `q?`, pagination | personnel autorisé |
| GET | `/supervision/attendance` | supervision/admin | pagination | présences du périmètre |
| POST | `/reports` | responsable, chef ou admin | `period_start`, `period_end` | rapport statistique |
| PATCH | `/reports/{rapport}/validate` | chef de centre ou admin | — | rapport validé |
| GET | `/admin/users` | administrateur | pagination | utilisateurs |
| PATCH | `/admin/users/{user}` | administrateur hors son propre compte | `role`, `supervisor_id?` | utilisateur modifié |

## Validation et erreurs

- 401 : session absente.
- 403 : policy ou périmètre insuffisant.
- 404 : ressource inexistante ou binding invalide.
- 422 : données invalides, QR expiré/rejoué, pointage hors zone ou doublon.
- Les fichiers acceptés sont PDF/JPEG/PNG, taille maximale 5 Mo; seul le chemin et les métadonnées sont renvoyés, jamais le contenu sensible.

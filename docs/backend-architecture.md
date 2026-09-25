# Architecture backend

## Principes

- Laravel 12 + Breeze existant, sans remplacement de l'authentification.
- Routes web authentifiées retournant des réponses JSON pour préparer le futur client; aucune vue métier n'est ajoutée.
- Controllers fins : autorisation, validation, appel d'un service, réponse.
- Services métier : `AttendanceService`, `QrCodeService`, `GeolocationService`, `AbsenceService`, `ReportService`.
- Enums PHP pour les rôles, types de pointage, statuts d'absence, anomalies et alertes.

## Données

- `users` reste la source d'identité Breeze; `role` et `supervisor_id` matérialisent les spécialisations UML avec une stratégie simple.
- `profiles` porte les attributs de la classe UML Profil.
- `sites` porte les coordonnées attendues et le rayon autorisé.
- `pointages` porte l'horodatage serveur, le type, les coordonnées reçues, la distance calculée et le QR consommé.
- `qr_tokens` ne stocke jamais le token en clair : seul son SHA-256 est persisté; un token est temporaire et à usage unique.
- `justificatifs` porte la demande, son statut, sa décision et les métadonnées sûres du fichier.
- `rapports`, `anomalies` et `alerts` couvrent les statistiques et la traçabilité fonctionnelle requises par le prompt.

## Flux de pointage

1. Un responsable/admin génère un token temporaire pour un site.
2. Le client envoie le token, le site, le type et ses coordonnées.
3. Le backend compare le hash, vérifie l'expiration et consomme le token dans une transaction.
4. Le backend calcule la distance Haversine au site et refuse une position hors rayon.
5. Le timestamp serveur et les résultats de contrôle sont enregistrés.

Le QR n'est donc pas une preuve suffisante à lui seul : il est combiné à l'utilisateur authentifié, au site, à l'expiration, à l'anti-rejeu et à la géofence.

## Géolocalisation et OpenStreetMap

`GeocodingProviderInterface` découple le domaine d'un fournisseur. L'implémentation injectée actuellement est `DisabledGeocodingProvider` : aucun appel externe ni transmission de coordonnées précises n'est effectué automatiquement. La configuration Nominatim est préparée dans `config/geolocation.php`; son activation devra être explicitement autorisée et respecter les conditions du fournisseur.

## Autorisation

Les policies centralisent les droits : propriétaire d'un pointage/justificatif, périmètre du responsable via `supervisor_id`, chef de centre pour les rapports et administrateur pour les utilisateurs.

## Limites actuelles

- Les horaires de travail ne figurent pas dans le diagramme; aucune règle d'horaire inventée n'est activée.
- Les exports Excel/PDF et notifications asynchrones ne sont pas générés dans cette première version.
- Les routes sont web-authentifiées; Sanctum n'a pas été ajouté puisqu'il n'existe pas dans le projet initial.

# Hypothèses et décisions backend

| Décision | Raison | Impact | Alternative |
| --- | --- | --- | --- |
| Utiliser `users.role` pour les héritages UML | Breeze fournit déjà `users`; le diagramme ne définit pas de tables de sous-classes | Pas de duplication d'identités; policy centralisée | Tables `personnel`, `responsables`, etc. |
| Ajouter `users.supervisor_id` | Nécessaire pour le périmètre du responsable, absent du diagramme | Un responsable voit ses collaborateurs directs | Table d'équipes dédiée |
| Créer `profiles` en relation 1–1 | La classe Profil et ses attributs sont visibles, mais ses cardinalités sont ambiguës | Profil métier séparé des données Breeze | Ajouter les champs directement à `users` |
| Remplacer le booléen UML `Pointage.type` par un enum arrivée/départ | Un booléen ne permet pas un contrat explicite et lisible | Validation et statistiques fiables | Conserver un booléen, moins expressif |
| Ajouter `sites` | Le prompt impose un emplacement attendu et un rayon | Contrôle géographique local et fournisseur-indépendant | Coordonnées en configuration, moins flexible |
| Ajouter `qr_tokens` technique | Le QR dynamique impose expiration et anti-rejeu | Hash, TTL et consommation atomique | Cache uniquement, moins traçable |
| Ajouter anomalies/alertes | Exigence fonctionnelle explicite, non visible dans la classe UML | Traçabilité minimale des détections | Événements sans persistance |
| Utiliser une transaction pour QR + pointage | Le token ne doit pas être consommé sans pointage valide | Évite les incohérences et le rejeu | Deux écritures séparées, non retenu |
| N'activer aucun appel Nominatim par défaut | Une coordonnée GPS précise ne doit pas partir vers un tiers sans consentement/configuration explicite | `DisabledGeocodingProvider` actif; calcul de distance local | Fournisseur Nominatim opt-in avec politique de confidentialité |
| Réponses JSON dans `web.php` | Le projet n'a pas `api.php` ni Sanctum; le frontend est interdit maintenant | Contrat backend testable sans nouvelle auth | Ajouter API/Sanctum plus tard si besoin confirmé |
| Ne pas implémenter les horaires | Aucun horaire n'est présent dans le diagramme ni le code initial | Pas de faux rejet « hors horaires » | Ajouter `schedules` après spécification métier |
| Stockage privé des justificatifs | Le fichier est sensible et doit être protégé | MIME/taille/chemin contrôlés | Stockage objet privé ultérieur |

# Architecture des QR journaliers

Le système utilise un QR opaque différent pour chaque journée et chaque site.
Le contenu du QR est un token aléatoire de 64 caractères. Seule son empreinte
SHA-256 est conservée en base.

## Cycle de vie

POST /attendance/qr-tokens crée le QR du jour pour un site. La requête est
réservée au rôle responsable_personnel. Une transaction verrouille le site :
si un QR actif existe déjà pour la date serveur, il est retourné sans créer de
doublon.

La régénération désactive logiquement le QR actif, renseigne deactivated_at,
puis crée un nouveau token pour la même date. L'ancien enregistrement reste
conservé pour la traçabilité. GET /attendance/qr-tokens/current permet de
consulter l'état courant et PATCH /attendance/qr-tokens/{qrToken}/deactivate
désactive un QR créé par le responsable.

## Validation d'un pointage

AttendanceService valide le QR avant la géolocalisation et avant la création
du pointage. QrCodeService::validateForAttendance vérifie l'empreinte, le
site, valid_on = today() selon le timezone Laravel, is_active et expires_at.
La date ou l'heure envoyée par le navigateur n'est jamais utilisée pour
décider du jour.

Un QR quotidien est réutilisable par plusieurs employés et pour l'entrée puis
la sortie. Le rejeu interdit concerne le pointage métier : un utilisateur ne
peut enregistrer qu'une entrée et une sortie par jour. Les anciennes versions
de QR statiques ou les QR d'un autre jour ne sont plus acceptées.

La géolocalisation et les autres règles du service de pointage restent
obligatoires après validation du QR.

## Données et permissions

qr_tokens contient le hash, le site, la date de validité, l'expiration,
l'état actif, le créateur et la date de désactivation. La policy autorise
uniquement le Responsable du personnel à créer, consulter et administrer les
QR journaliers. Les employés peuvent uniquement les utiliser via le processus
de pointage authentifié.

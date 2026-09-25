<?php

return [
    'qr_ttl_seconds' => (int) env('ATTENDANCE_QR_TTL_SECONDS', 60),
    /*
     * QR affiché physiquement au Centre principal.
     * Il est réutilisable : les contrôles de géolocalisation et d'un pointage
     * unique par type/jour restent appliqués côté serveur.
     */
    'static_qr_value' => env('ATTENDANCE_STATIC_QR_VALUE', 'CENADI-DOUALA-POINTAGE-2026'),
];

<?php

return [
    'qr_ttl_seconds' => (int) env('ATTENDANCE_QR_TTL_SECONDS', 60),
    'latitude' => (float) env('POINTAGE_LATITUDE', 4.0503060),
    'longitude' => (float) env('POINTAGE_LONGITUDE', 9.6940653),
    'radius_meters' => (int) env('POINTAGE_RADIUS_METERS', 250),
    /*
     * QR affiché physiquement au Centre principal.
     * Il est réutilisable : les contrôles de géolocalisation et d'un pointage
     * unique par type/jour restent appliqués côté serveur.
     */
];

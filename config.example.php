<?php
/**
 * Beispiel-Konfiguration für das CMS.
 * Diese Datei wird ins Repo eingecheckt.
 * Auf dem Server gibt es zusätzlich eine config.php mit echten Zugangsdaten,
 * die NICHT im Repo liegt.
 */
return [
    'db_host'    => 'localhost',      // oder der Hostname aus Plesk
    'db_name'    => 'cms_core',       // dein DB-Name
    'db_user'    => 'cms_core_user',  // dein DB-User
    'db_pass'    => 'CHANGE_ME',      // Platzhalter, kein echtes Passwort
    'db_charset' => 'utf8mb4',
];
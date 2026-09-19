<?php

declare(strict_types=1);

class DB
{
    private static ?PDO $pdo = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $baseDir = dirname__DIR__;
        $configFile = $baseDir . '/config/config.php';
        $exampleConfigFile = $baseDir . '/config/config.example.php';

        $configPath = null;
        if (file_exists($configFile)) {
            $configPath = $configFile;
        } elseif (file_exists($exampleConfigFile)) {
            $configPath = $exampleConfigFile;
        } else {
            throw new RuntimeException(
                "Konfigurationsdatei nicht gefunden. Weder '{$configFile}' noch '{$exampleConfigFile}' existiert."
            );
        }

        $config = require $configPath;

        if (!is_array($config)) {
            throw new RuntimeException(
                "Die Konfigurationsdatei '{$configPath}' muss ein Array zurückgeben."
            );
        }

        $requiredKeys = ['db_host', 'db_name', 'db_user', 'db_pass', 'db_charset'];
        $missingKeys = [];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            throw new RuntimeException(
                "Unvollständige Datenbankkonfiguration in '{$configPath}'. Fehlende Schlüssel: " . implode(', ', $missingKeys)
            );
        }

        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$pdo = new PDO($dsn, (string)$config['db_user'], (string)$config['db_pass'], $options);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Datenbankverbindung fehlgeschlagen: ' . $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }

        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result === false ? null : $result;
    }

    public static function execute(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function lastInsertId(): string
    {
        return (string)self::getConnection()->lastInsertId();
    }
}

<?php

date_default_timezone_set('America/Sao_Paulo');

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {

        $db_name = 'local_data.db';

        $dbFile = __DIR__ . '/' .$db_name; // Caminho completo para o arquivo do banco

        try {
            $this->connection = new PDO("sqlite:" .$dbFile);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //$this->connection->exec("PRAGMA foreign_keys = ON;");
            //$this->connection->exec("PRAGMA timezone = 'America/Sao_Paulo';"); // Configurar fuso horário

            $this->connection->exec("CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY, name TEXT, price TEXT, dt TEXT)");

        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
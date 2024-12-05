<?php

abstract class Model {
    protected $table; // Nome da tabela será definido nas subclasses
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Obter todos os registros
    public function all() {
        $stmt = $this->db->prepare(
            "SELECT * 
            FROM {$this->table} 
            ORDER BY id DESC");
        $stmt->execute();

        return $stmt; 
    }

    // Obter um registro por ID
    public function find($id) {
        $stmt = $this->db->prepare(
            "SELECT * 
            FROM {$this->table} 
            WHERE id = :id
            ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Inserir
    public function create(array $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        return $stmt->execute($data);
    }

    // Atualizar
    public function update($id, array $data) {
        $fields = implode(' = ?, ', array_keys($data)) . ' = ?';
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $fields WHERE id = ?");
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    // Deletar
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

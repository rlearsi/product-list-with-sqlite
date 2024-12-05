<?php

require_once './model.php';

class Products extends Model {
    protected $table = 'products'; // Nome da tabela

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByName($name) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt;
    }

    public function allByGroup() {
        $stmt = $this->db->prepare(
            "SELECT id, name, price, COUNT(name) as sells, SUM(price) as earnings
             FROM {$this->table} 
             GROUP BY name 
             ORDER BY sells DESC
             ");
        $stmt->execute();

        return $stmt; 
    }
    
}

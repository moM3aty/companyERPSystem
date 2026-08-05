<?php
// app/models/User.php

class User extends Model {
    // جلب مستخدم عن طريق البريد الإلكتروني
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $row = $this->db->single();

        // إذا وجد المستخدم، أعد true
        if($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // جلب بيانات مستخدم عن طريق الـ ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
}
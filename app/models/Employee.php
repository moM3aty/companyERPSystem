<?php
// app/models/Employee.php

class Employee extends Model {
    
    // جلب جميع الموظفين مع اسم القسم الخاص بهم
    public function getEmployees() {
        $this->db->query('SELECT e.*, d.name as dept_name 
                          FROM employees e 
                          LEFT JOIN departments d ON e.department_id = d.id 
                          ORDER BY e.id DESC');
        return $this->db->resultSet();
    }

    public function getEmployeeById($id) {
        $this->db->query('SELECT * FROM employees WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function addEmployee($data) {
        $this->db->query('INSERT INTO employees (name, email, phone, position, salary, department_id) 
                          VALUES (:name, :email, :phone, :position, :salary, :department_id)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':position', $data['position']);
        $this->db->bind(':salary', $data['salary']);
        $this->db->bind(':department_id', $data['department_id']);
        return $this->db->execute();
    }

    public function updateEmployee($data, $id) {
        $this->db->query('UPDATE employees SET 
                          name = :name, email = :email, phone = :phone, 
                          position = :position, salary = :salary, department_id = :department_id 
                          WHERE id = :id');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':position', $data['position']);
        $this->db->bind(':salary', $data['salary']);
        $this->db->bind(':department_id', $data['department_id']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function deleteEmployee($id) {
        $this->db->query('DELETE FROM employees WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // دالة مساعدة لجلب الأقسام (لعرضها في القوائم المنسدلة)
    public function getDepartments() {
        $this->db->query('SELECT * FROM departments ORDER BY name ASC');
        return $this->db->resultSet();
    }
}
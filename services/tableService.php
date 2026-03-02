<?php
// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

class tableService
{
    private $tableModel;

    public function __construct($tableModel)
    {
        $this->tableModel = $tableModel;
    }

    public function saveTable($data, $id = null)
    {
        if($id) {
            return $this->tableModel->update($id, $data);
        } else {
            return $this->tableModel->create($data);
        }
    }

    public function supprimerTable($id)
    {
        return $this->tableModel->delete($id);
    }
}
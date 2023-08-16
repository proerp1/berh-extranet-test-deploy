<?php

class CSVImport extends AppModel {
    public $name = 'CSVImport';
    public $useTable = 'csv_imports';
    public $hasMany = array(
        'CSVImportLine' => array(
            'className' => 'CSVImportLine',
            'foreignKey' => 'csv_import_id',
            'dependent' => true
        ),
    );

    public $belongsTo = array(
        'Status' => array(
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 19]
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'user_id',
            'conditions' => ['CSVImport.imported_by_customer' => true]
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => ['CSVImport.imported_by_customer' => false]
        ),
    );

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['created_at'])) {
                $results[$key][$this->alias]['created_at_nao_formatado'] = $results[$key][$this->alias]['created_at'];
                $results[$key][$this->alias]['created_at'] = date("d/m/Y", strtotime($results[$key][$this->alias]['created_at']));
            }
        }

        return $results;
    }
}

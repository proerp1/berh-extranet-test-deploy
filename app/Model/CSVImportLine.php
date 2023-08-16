<?php

class CSVImportLine extends AppModel {
    public $name = 'CSVImportLine';
    public $useTable = 'csv_import_lines';
    public $belongsTo = array(
        'CSVImport' => array(
            'className' => 'CSVImport',
            'foreignKey' => 'csv_import_id'
        ),
        'Status' => array(
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 19]
        )
    );
}

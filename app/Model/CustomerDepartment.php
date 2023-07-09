<?php
class CustomerDepartment extends AppModel {
    public $name = 'CustomerDepartment';
    public $useTable = 'customer_departments';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id'
        )
    );
}

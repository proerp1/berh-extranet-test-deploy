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

    public function beforeFind($queryData) {
		$queryData['conditions'][] = array('CustomerDepartment.data_cancel' => '1901-01-01 00:00:00');
	  
	  return $queryData;
	}
}

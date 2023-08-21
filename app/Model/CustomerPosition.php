<?php
class CustomerPosition extends AppModel {
    public $name = 'CustomerPosition';
    public $useTable = 'customer_positions';
    public $primaryKey = 'id';

    public $hasMany = array(
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_positions_id'
        )
    );

    public function beforeFind($queryData) {
		$queryData['conditions'][] = array('CustomerPosition.data_cancel' => '1901-01-01 00:00:00');
	  
	    return $queryData;
	}
}

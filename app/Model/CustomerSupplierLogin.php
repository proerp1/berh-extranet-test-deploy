<?php
class CustomerSupplierLogin extends AppModel {
    public $name = 'CustomerSupplierLogin';
    public $useTable = 'customer_supplier_logins';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Customer',
        'Supplier'
    );

    public function beforeFind($queryData) {
		$queryData['conditions'][] = array('CustomerSupplierLogin.data_cancel' => '1901-01-01 00:00:00');
	  
	    return $queryData;
	}
}

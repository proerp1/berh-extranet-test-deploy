<?php
class CustomerSupplierLogin extends AppModel {
    public $name = 'CustomerSupplierLogin';
    public $useTable = 'customer_supplier_logins';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Customer',
        'Supplier',
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_created_id',
        ],
        'UserUpdated' => [
            'className' => 'User',
            'foreignKey' => 'user_updated_id',
        ],
    );

    public function beforeFind($queryData) {
		$queryData['conditions'][] = array('CustomerSupplierLogin.data_cancel' => '1901-01-01 00:00:00');
	  
	    return $queryData;
	}

    public function afterFind($results, $primary = false){
        foreach ($results as $key => $val) {
            if (isset($val['CustomerSupplierLogin']['created'])) {
                $results[$key]['CustomerSupplierLogin']['created_nao_formatado'] = $val['CustomerSupplierLogin']['created'];
                $results[$key]['CustomerSupplierLogin']['created'] = date("d/m/Y h:i:s", strtotime($val['CustomerSupplierLogin']['created']));
            }

            if (isset($val['CustomerSupplierLogin']['updated'])) {
                $results[$key]['CustomerSupplierLogin']['updated_nao_formatado'] = $val['CustomerSupplierLogin']['updated'];
                $results[$key]['CustomerSupplierLogin']['updated'] = date("d/m/Y h:i:s", strtotime($val['CustomerSupplierLogin']['updated']));
            }
        }

        return $results;
    }
}

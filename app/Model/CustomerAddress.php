<?php
class CustomerAddress extends AppModel {
    public $name = 'CustomerAddress';
    public $useTable = 'customer_addresses';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id'
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ),
        'Status' => array(
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 24]
        )
    );

    public function beforeFind($queryData) {
		$queryData['conditions'][] = array('CustomerAddress.data_cancel' => '1901-01-01 00:00:00');
	  
	  return $queryData;
	}

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($results[$key]['CustomerAddress']['address_line'])) {
                $addrLine = $results[$key]['CustomerAddress']['address_line'];
                $num = $results[$key]['CustomerAddress']['address_number'];
                $comp = $results[$key]['CustomerAddress']['address_complement'] ? " - ".$results[$key]['CustomerAddress']['address_complement'] : "";
                $neighborhood = $results[$key]['CustomerAddress']['neighborhood'] ? $results[$key]['CustomerAddress']['neighborhood'].", " : "";
                $city = $results[$key]['CustomerAddress']['city'];
                $state = $results[$key]['CustomerAddress']['state'];
                $zipCode = $results[$key]['CustomerAddress']['zip_code'];

                $results[$key]['CustomerAddress']['address'] = "$addrLine, $num$comp";
                $results[$key]['CustomerAddress']['city_data'] = "$zipCode, $neighborhood$city - $state";
            }

        }

        return $results;
    }
}

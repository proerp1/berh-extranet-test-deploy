<?php
class CustomerUserAddress extends AppModel {
    public $name = 'CustomerUserAddress';
    public $useTable = 'customer_user_addresses';
    public $primaryKey = 'id';

    public $belongsTo = [
        'CustomerUser' => [
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_id'
        ],
        'AddressType' => [
            'className' => 'AddressType',
            'foreignKey' => 'address_type_id'
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CustomerUserAddress.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
}
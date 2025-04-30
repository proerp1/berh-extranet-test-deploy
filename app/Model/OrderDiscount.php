<?php
class OrderDiscount extends AppModel {
    public $name = 'OrderDiscount';

    public $belongsTo = [
        'Order',
        'OrderParent' => [
            'className' => 'Order',
            'foreignKey' => 'order_parent_id'
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['OrderDiscount.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
}

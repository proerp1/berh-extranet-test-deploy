<?php
class ProductAttribute extends AppModel
{
    public $name = 'ProductAttribute';

    public $belongsTo = [
        'Product' => [
            'className' => 'Product',
            'foreignKey' => 'product_id',
            'conditions' => ['Product.data_cancel' => '1901-01-01 00:00:00']
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['ProductAttribute.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public $validate = [
        'name' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ]
    ];
}

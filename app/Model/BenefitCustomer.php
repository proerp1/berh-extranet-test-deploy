<?php

class BenefitCustomer extends AppModel
{
    public $useTable = 'benefits_customer';
    public $primaryKey = 'id';

    public $belongsTo = [
        'Benefits',
        'Customer',
    ];

    public $validate = [
        'code' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O código é obrigatório',
                'last' => false,
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['BenefitCustomer.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}

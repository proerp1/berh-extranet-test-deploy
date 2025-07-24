<?php

class CustomerBenefitCode extends AppModel
{
    public $belongsTo = [
        'Customer',
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
    ];

    public $validate = [
        'code_be' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O código BE é obrigatório',
                'last' => false,
            ],
        ],
        'code_customer' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O código cliente é obrigatório',
                'last' => false,
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CustomerBenefitCode.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}

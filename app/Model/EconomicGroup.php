<?php

class EconomicGroup extends AppModel
{
    public $name = 'EconomicGroup';

    public $belongsTo = [
        'Customer',
        'Status'
    ];

    public $validate = [
        'status_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'name' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'razao_social' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'document' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['EconomicGroup.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}

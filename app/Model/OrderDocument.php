<?php

class OrderDocument extends AppModel
{
    public $name = 'OrderDocument';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1],
        ],
        'Order',
    ];

    public $actsAs = [
        'Upload.Upload' => [
            'file_name',
        ],
    ];

    public $validate = [
        'name' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O nome é obrigatório',
            ],
        ],
        'status_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O nome é obrigatório',
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['OrderDocument.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}

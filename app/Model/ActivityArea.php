<?php
class ActivityArea extends AppModel
{
    public $name = 'ActivityArea';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['ActivityArea.data_cancel' => '1901-01-01 00:00:00'];
        
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

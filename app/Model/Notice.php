<?php
class Notice extends AppModel
{
    public $name = 'Notice';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Notice.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public $actsAs = [
        'Upload.Upload' => [
            'file'
        ]
    ];

    public $validate = [
        'title' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ]
    ];
}

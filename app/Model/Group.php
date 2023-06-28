<?php
class Group extends AppModel
{
    public $name = 'Group';

    public $hasMany = [
        'User' => [
            'className' => 'User',
            'order' => 'User.name DESC'
        ]
    ];

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Group.data_cancel' => '1901-01-01 00:00:00'];
    
        return $queryData;
    }

    public $validate = [
        'name' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O nome é obrigatório'
            ]
        ]
    ];
}

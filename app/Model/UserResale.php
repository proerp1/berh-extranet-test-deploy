<?php
class UserResale extends AppModel
{
    public $name = 'UserResale';

    public $belongsTo = [
        'User',
        'Resale'
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['UserResale.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public $validate = [
        'resale_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ]
    ];
}

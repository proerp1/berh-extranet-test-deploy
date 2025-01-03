<?php

class LinkBenefit extends AppModel
{
    public $name = 'LinkBenefit';
    public $useTable = 'link_benefits';

    public $belongsTo = [
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
    ];

    public $actsAs = [
        'Upload.Upload' => [
            'file_name',
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['LinkBenefit.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}

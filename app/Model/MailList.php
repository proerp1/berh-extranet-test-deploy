<?php
class MailList extends AppModel
{
    public $name = 'MailList';

    public $belongsTo = [
        'EmailsCampanha' => [
            'className' => 'EmailsCampanha',
            'foreignKey' => 'email_campanha_id'
        ],
        'Customer'
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['MailList.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public $validate = [
        'subject' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ]
    ];
}

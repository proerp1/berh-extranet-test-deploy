<?php
class CustomerUserBankAccount extends AppModel {
    public $name = 'CustomerUserBankAccount';
    public $useTable = 'customer_user_bank_accounts';
    public $primaryKey = 'id';

    public $belongsTo = [
        'CustomerUser' => [
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ],
        'BankAccountType' => [
            'className' => 'BankAccountType',
            'foreignKey' => 'account_type_id'
        ],
        'BankCode' => [
            'className' => 'BankCode',
            'foreignKey' => 'bank_code_id'
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CustomerUserBankAccount.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
}

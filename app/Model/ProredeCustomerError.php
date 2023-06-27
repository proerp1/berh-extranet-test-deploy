<?php
class ProredeCustomerError extends AppModel
{
    public $name = 'ProredeCustomerError';
    public $useTable = 'prorede_customers';

    public $belongsTo = ['Prorede', 'Customer'];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['ProredeCustomerError.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
}

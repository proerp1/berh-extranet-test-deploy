<?php
class ProredeCustomer extends AppModel
{
    public $name = 'ProredeCustomer';

    public $belongsTo = ['Prorede', 'Customer'];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['ProredeCustomer.data_cancel' => '1901-01-01 00:00:00', 'ProredeCustomer.status' => 1];
        
        return $queryData;
    }
}

<?php
class CustomerUserVacation extends AppModel {
    public $name = 'CustomerUserVacation';
    public $useTable = 'customer_user_vacations';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        )
    );

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CustomerUserVacation.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
}

<?php 
App::uses('AuthComponent', 'Controller/Component');

class CustomerGeLog extends AppModel {
    public $name = 'CustomerGeLog';
    
    public $belongsTo = array(
        'Customer',
        'UsuarioCriacao' => array(
            'className' => 'User',
            'foreignKey' => 'user_creator_id'
        )
    );

    public function beforeFind($queryData) {

        $queryData['conditions'][] = array('CustomerGeLog.data_cancel' => '1901-01-01 00:00:00');
        
        return $queryData;
    }
}

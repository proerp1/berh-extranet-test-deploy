<?php

class Revenue extends AppModel
{
    public $name = 'Revenue';

    public $belongsTo = array(
        'Status' => array(
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => array('Status.categoria' => 1)
        )
    );

    public function beforeFind($queryData)
    {

        $queryData['conditions'][] = array('Revenue.data_cancel' => '1901-01-01 00:00:00');

        return $queryData;
    }

    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notBlank'),
                'message' => 'Campo obrigatório'
            )
        )
    );
}

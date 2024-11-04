<?php

class OrderBalanceFile extends AppModel
{
    public $name = 'OrderBalanceFile';

    public $belongsTo = [
        'Creator' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id'
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['OrderBalanceFile.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created_nao_formatado'] = $val[$this->alias]['created'];
                $results[$key][$this->alias]['created'] = date("d/m/Y H:i:s", strtotime($val[$this->alias]['created']));
            }
        }

        return $results;
    }
}

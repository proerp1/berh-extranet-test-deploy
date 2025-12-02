<?php

class BancoPadrao extends AppModel
{
    public $useTable = 'banco_padrao';
    public $name = 'BancoPadrao';

    public $belongsTo = [
        'Bank' => [
            'className' => 'Bank',
            'foreignKey' => 'bank_id',
        ],
        'UserUpdated' => [
            'className' => 'User',
            'foreignKey' => 'user_updated_id'
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['BancoPadrao.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['updated'])) {
                $results[$key][$this->alias]['updated_nao_formatado'] = $results[$key][$this->alias]['updated'];
                $results[$key][$this->alias]['updated'] = date('d/m/Y H:i:s', strtotime($results[$key][$this->alias]['updated']));
            }
        }

        return $results;
    }
}

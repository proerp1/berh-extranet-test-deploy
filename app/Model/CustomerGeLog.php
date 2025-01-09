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
        // Definindo uma condição de filtro, como já estava no código original
        $queryData['conditions'][] = array('CustomerGeLog.data_cancel' => '1901-01-01 00:00:00');
        return $queryData;
    }

    public function afterFind($results, $primary = false) {
        // Verificando se a chave 'CustomerGeLog' e o campo 'created' estão disponíveis
        foreach ($results as $key => $val) {
            if (isset($val['CustomerGeLog']['created'])) {
                // Formatar a data para o formato brasileiro (dd/mm/yyyy)
                $results[$key]['CustomerGeLog']['created'] = (new DateTime($val['CustomerGeLog']['created']))->format('d/m/Y H:i:s');
            }
        }
        return $results;
    }
}


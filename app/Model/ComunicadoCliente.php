<?php
class ComunicadoCliente extends AppModel {
    public $name = 'ComunicadoCliente';
    public $useTable = 'comunicado_customers';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id'
        ),
        'Comunicado' => array(
            'className' => 'Comunicado',
            'foreignKey' => 'comunicado_id'
        )
    );

    public function beforeFind($queryData) {
		$queryData['conditions'][] = array('ComunicadoCliente.data_cancel' => '1901-01-01 00:00:00');
	  
	  return $queryData;
	}

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['sent'])) {
                $results[$key][$this->alias]['sent_nao_formatado'] = $results[$key][$this->alias]['sent'];
                $results[$key][$this->alias]['sent'] = date('d/m/Y H:i:s', strtotime($results[$key][$this->alias]['sent']));
            }
        }

        return $results;
    }
}

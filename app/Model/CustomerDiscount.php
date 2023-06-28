<?php 
App::uses('AuthComponent', 'Controller/Component');
class CustomerDiscount extends AppModel {
    public $name = 'CustomerDiscount';
    
    public $belongsTo = array(
        'Customer',
        'Status' => array(
          'className' => 'Status',
          'foreignKey' => 'status_id',
          'conditions' => array('Status.categoria' => 1)
        ),
        'UsuarioAlteracao' => array(
            'className' => 'User',
            'foreignKey' => 'user_updated_id'
        )
    );

    public function beforeFind($queryData) {
        $queryData['conditions'][] = array('CustomerDiscount.data_cancel' => '1901-01-01 00:00:00');

      return $queryData;
    }

    public function beforeSave($options = array()) {
        if (!empty($this->data[$this->alias]['expire_date'])) {
            $this->data[$this->alias]['expire_date'] = $this->dateFormatBeforeSave($this->data[$this->alias]['expire_date']);
        }
        return true;
    }

    public function dateFormatBeforeSave($dateString) {
        return date('Y-m-d', strtotime($this->date_converter($dateString)));
    }

    public function date_converter($_date = null) {
        $format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
        if ($_date != null && preg_match($format, $_date, $partes)) {
            return $partes[3].'-'.$partes[2].'-'.$partes[1];
        }
        
        return false;
    }

    public function afterFind($results, $primary = false){
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['expire_date'])) {
                $results[$key][$this->alias]['expire_date_nao_formatado'] = $val[$this->alias]['expire_date'];
                $results[$key][$this->alias]['expire_date'] = date("d/m/Y", strtotime($val[$this->alias]['expire_date']));
            }

      if (isset($val[$this->alias]['discount'])) {
                $results[$key][$this->alias]['discount_nao_formatado'] = $val[$this->alias]['discount'];
        $results[$key][$this->alias]['discount'] = number_format($results[$key][$this->alias]['discount'],2,',','.');
      }
        }

        return $results;
    }

    public $validate = array(
        'status_id' => array(
            'required' => array(
                'rule' => array('notBlank'),
                'message' => 'O status é obrigatório'
            )
        ),
        'discount' => array(
            'required' => array(
                'rule' => array('notBlank'),
                'message' => 'O valor do desconto é obrigatório'
            )
        ),
        'expire_date' => array(
            'required' => array(
                'rule' => array('notBlank'),
                'message' => 'A data da validade é obrigatório'
            )
        )
    );
}
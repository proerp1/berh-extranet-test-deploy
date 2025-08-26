<?php 
App::uses('AuthComponent', 'Controller/Component');

class LogCustomer extends AppModel {
    public $name = 'LogCustomer';
    public $useTable = 'log_customers';

    public $belongsTo = [
        'Customer',
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['LogCustomer.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function beforeSave($options = array()) {
      if (!empty($this->data[$this->name]['dt_economia_inicial'])) {
        $this->data[$this->name]['dt_economia_inicial'] = $this->dateFormatBeforeSave($this->data[$this->name]['dt_economia_inicial']);
      }

      if (!empty($this->data[$this->name]['economia_inicial'])) {
        $this->data[$this->name]['economia_inicial'] = $this->priceFormatBeforeSave($this->data[$this->name]['economia_inicial']);
      }

      return true;
    }

    public function afterFind($results, $primary = false){
      foreach ($results as $key => $val) {
        if (isset($val[$this->alias]['created'])) {
          $results[$key][$this->alias]['created_nao_formatado'] = $val[$this->alias]['created'];
          $results[$key][$this->alias]['created'] = date("d/m/Y H:i:s", strtotime($val[$this->alias]['created']));
        }
        if (isset($val[$this->alias]['dt_economia_inicial'])) {
          $results[$key][$this->alias]['dt_economia_inicial_nao_formatado'] = $val[$this->alias]['dt_economia_inicial'];
          $results[$key][$this->alias]['dt_economia_inicial'] = date("d/m/Y", strtotime($val[$this->alias]['dt_economia_inicial']));
        }

        if (isset($val[$this->alias]['economia_inicial'])) {
          $results[$key][$this->alias]['economia_inicial_nao_formatado'] = $val[$this->alias]['economia_inicial'];
          $results[$key][$this->alias]['economia_inicial'] = number_format($results[$key][$this->alias]['economia_inicial'],2,',','.');
        }
      }

      return $results;
    }

    public function createLogCustomer($data_item)
    {
        $userId = CakeSession::read("Auth.User.id");

        $data = $data_item['Customer'];

        $registro = [
            'LogCustomer' => [
                'customer_id'         => $data['id'],
                'emitir_nota_fiscal'  => $data['emitir_nota_fiscal'],
                'economia_inicial'    => $data['economia_inicial'],
                'dt_economia_inicial' => $data['dt_economia_inicial'],
                'user_creator_id'     => $userId,
            ]
        ];

        $this->create();

        return $this->save($registro);
    }
}
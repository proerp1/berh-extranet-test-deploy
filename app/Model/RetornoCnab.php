<?php
class RetornoCnab extends AppModel
{
    public $name = 'RetornoCnab';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 11],
        ],
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
    ];

    public $actsAs = [
        'Upload.Upload' => [
            'arquivo',
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['RetornoCnab.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function beforeSave($options = [])
    {
        if (!empty($this->data[$this->alias]['data_pagamento'])) {
            $this->data[$this->alias]['data_pagamento'] = $this->dateFormatBeforeSave($this->data[$this->alias]['data_pagamento']);
        }

        return true;
    }

  public function priceFormatBeforeSave($price)
  {
      $valueFormatado = str_replace('.', '', $price);

      return str_replace(',', '.', $valueFormatado);
  }

  public function dateFormatBeforeSave($dateString)
  {
      return date('Y-m-d', strtotime($this->date_converter($dateString)));
  }

  public function date_converter($_date = null)
  {
      $format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
      if ($_date != null && preg_match($format, $_date, $partes)) {
          return $partes[3].'-'.$partes[2].'-'.$partes[1];
      }

      return false;
  }

  public function afterFind($results, $primary = false)
  {
      foreach ($results as $key => $val) {
          if (isset($val[$this->alias]['data_pagamento'])) {
              $results[$key][$this->alias]['data_pagamento_nao_formatado'] = $val[$this->alias]['data_pagamento'];
              $results[$key][$this->alias]['data_pagamento'] = date('d/m/Y', strtotime($val[$this->alias]['data_pagamento']));
          }
      }

      return $results;
  }

    // / Verifica se o arquivo já foi enviado
    // / Impede que o usuário selecione o mesmo arquivo duas ou mais vezes por engano
    public function verifica_arquivo_enviado($id, $data_arquivo, $lote)
    {
        $result = true;

        $query = "SELECT *
          FROM retorno_cnabs r
          WHERE r.data_cancel = '1901-01-01' and r.data_arquivo = '".$data_arquivo."' AND r.lote = '".$lote."' AND r.id != ".$id;

        $result = $this->query($query);

        return count($result) > 0;
    }
}

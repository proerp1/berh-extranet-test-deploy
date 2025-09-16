<?php

class OrderDesconto extends AppModel
{
    public $belongsTo = [
        'Order',
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
    ];

    public $validate = [
        'value' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O valor é obrigatório',
                'last' => false,
            ],
        ],
        'tipo' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O tipo é obrigatório',
                'last' => false,
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
      $queryData['conditions'][] = ['OrderDesconto.data_cancel' => '1901-01-01 00:00:00'];

      return $queryData;
    }

    public function beforeSave($options = []) {
      if (isset($this->data[$this->name]['valor'])) {
        $this->data[$this->name]['valor'] = $this->priceFormatBeforeSave($this->data[$this->name]['valor']);
      }
    }

    public function afterFind($results, $primary = false)
  {
    foreach ($results as $key => $val) {
      if (isset($val[$this->alias]['valor'])) {
        $results[$key][$this->alias]['valor_nao_formatado'] = $results[$key][$this->alias]['valor'];
        $results[$key][$this->alias]['valor'] = number_format($results[$key][$this->alias]['valor'], 2, ',', '.');
      }
      if (isset($val[$this->alias]['tipo'])) {
        $tipos = [
          "Selecione",
          "REEMBOLSO",
          "ECONOMIA = CREDITA CONTA",
          "AJUSTE = CREDITA E DEBITA",
          "INCONSISTENCIA = SOMENTE CREDITA",
          "SALDO",
          "BOLSA DE CREDITO",
          "CONTESTACAO GE = SOMENTE DEBITA",
          "RECEITA DERIVADA = SOMENTE CREDITA)",
        ];
        $results[$key][$this->alias]['tipo_nome'] = $tipos[$val[$this->alias]['tipo']];
      }
    }

    return $results;
  }
}

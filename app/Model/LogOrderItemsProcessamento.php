<?php 
App::uses('AuthComponent', 'Controller/Component');

class LogOrderItemsProcessamento extends AppModel {
    public $name = 'LogOrderItemsProcessamento';
    public $useTable = 'log_order_items_processamento';

    public $belongsTo = [
        'OrderItem' => [
            'className' => 'OrderItem',
            'foreignKey' => 'order_item_id'
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['LogOrderItemsProcessamento.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function beforeSave($options = array()) 
    {
        if (!empty($this->data[$this->alias]['data_entrega'])) {
            $this->data[$this->alias]['data_entrega'] = $this->dateFormatBeforeSave($this->data[$this->alias]['data_entrega']);
        }

		return true;
	}

    public function dateFormatBeforeSave($dateString)
    {
        $date = DateTime::createFromFormat('d/m/Y', $dateString);

        if ($date === false) {
            $date = new DateTime($dateString);
        }

        # Check if it contains time
        if (strpos($dateString, ':') !== false) {
            return $date->format('Y-m-d H:i:s');
        }

        return $date->format('Y-m-d');
    }

    public function logProcessamento($data_item)
    {
        $userId = CakeSession::read("Auth.User.id");

        $data = $data_item['OrderItem'];

        $registro = [
            'LogOrderItemsProcessamento' => [
                'order_item_id'           => $data['id'],
                'status_processamento'    => $data['status_processamento'],
                'pedido_operadora'        => $data['pedido_operadora'],
                'data_entrega'            => $data['data_entrega'],
                'motivo_processamento'    => $data['motivo_processamento'],
                'user_creator_id'         => $userId,
            ]
        ];

        $this->create();

        return $this->save($registro);
    }
}
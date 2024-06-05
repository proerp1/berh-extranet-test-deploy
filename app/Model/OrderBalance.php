<?php
class OrderBalance extends AppModel {
    public $name = 'OrderBalance';

    public $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id'
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ),
        'Benefit' => array(
            'className' => 'Benefit',
            'foreignKey' => 'benefit_id'
        ),
    );

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['OrderBalance.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['total'])) {
                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['total'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['total_not_formated'] = 0;
                $results[$key][$this->alias]['total'] = '0,00';
            }
        }

        return $results;
    }

    public function beforeSave($options = array()) 
    {
        if (!empty($this->data[$this->alias]['total'])) {
            $this->data[$this->alias]['total'] = $this->priceFormatBeforeSave($this->data[$this->alias]['total']);
        }
		
		return true;
	}

    public function priceFormatBeforeSave($price)
	{
        if(is_numeric($price)){
            return $price;
        }
		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
	}

    public function update_order_item_saldo($orderID, $userID) {
        $sql = "SELECT i.id, i.saldo, b.total 
                    FROM orders o
                        INNER JOIN order_items i ON i.order_id = o.id
                        INNER JOIN order_balances b ON b.order_id = o.id
                        INNER JOIN customer_user_itineraries t ON t.id = i.customer_user_itinerary_id
                    WHERE o.id = ".$orderID."
                            AND o.customer_id = t.customer_id
                            AND b.customer_user_id = i.customer_user_id
                            AND b.benefit_id = t.benefit_id
                            AND o.data_cancel = '1901-01-01 00:00:00'
                            AND i.data_cancel = '1901-01-01 00:00:00'
                            AND b.data_cancel = '1901-01-01 00:00:00'
                            AND t.data_cancel = '1901-01-01 00:00:00'
                ";
        $result = $this->query($sql);

        if ($result) { 
            for ($i=0; $i < count($result); $i++) { 
                $itemID = $result[$i]['i']['id'];
                $total = $result[$i]['b']['total'];

                $this->query("UPDATE order_items SET saldo = ".$total.", total_saldo = (subtotal - ".$total."), updated = now(), updated_user_id = ".$userID." WHERE id = ".$itemID);
            }
        }

        $sql = "SELECT o.id, sum(i.saldo) AS saldo, sum(i.total_saldo) AS total_saldo 
                    FROM orders o
                        INNER JOIN order_items i ON i.order_id = o.id
                    WHERE o.id = ".$orderID."
                            AND o.data_cancel = '1901-01-01 00:00:00'
                            AND i.data_cancel = '1901-01-01 00:00:00'
                ";
        $result = $this->query($sql);

        if ($result) { 
            for ($i=0; $i < count($result); $i++) { 
                $orderID = $result[$i]['o']['id'];
                $saldo = $result[$i][0]['saldo'];
                $total_saldo = $result[$i][0]['total_saldo'];

                $this->query("UPDATE orders SET saldo = ".$saldo.", total_saldo = ".$total_saldo.", updated = now() WHERE id = ".$orderID);
            }
        }

        return true;
    }
}

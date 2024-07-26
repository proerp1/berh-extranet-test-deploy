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
        $this->query("UPDATE order_items SET saldo = 0, total_saldo = 0, updated = now(), updated_user_id = ".$userID." WHERE order_id = ".$orderID);

        $sql = "SELECT MIN(i.id) AS id, be.total AS total 
                    FROM orders o
                        INNER JOIN order_items i ON i.order_id = o.id
                        INNER JOIN customer_user_itineraries t ON t.id = i.customer_user_itinerary_id
                        INNER JOIN (SELECT b.customer_user_id, b.benefit_id, b.order_id, SUM(b.total) AS total
                                        FROM order_balances b
                                        WHERE b.data_cancel = '1901-01-01'
                                        GROUP BY b.customer_user_id, b.benefit_id
                                    ) be ON be.customer_user_id = i.customer_user_id
                                            AND be.benefit_id = t.benefit_id
                                            AND be.order_id = o.id
                    WHERE o.id = ".$orderID."
                            AND o.customer_id = t.customer_id
                            AND o.data_cancel = '1901-01-01 00:00:00'
                            AND i.data_cancel = '1901-01-01 00:00:00'
                            AND t.data_cancel = '1901-01-01 00:00:00'
                    GROUP BY be.customer_user_id, be.benefit_id
                ";
        $result = $this->query($sql);

        if ($result) { 
            for ($i=0; $i < count($result); $i++) { 
                $itemID = $result[$i][0]['id'];
                $total  = $result[$i]['be']['total'];

                $this->query("UPDATE order_items SET saldo = ".$total.", total_saldo = (subtotal - ".$total."), updated = now(), updated_user_id = ".$userID." WHERE id = ".$itemID);
            }
        }

        $sql = "SELECT o.id, coalesce(sum(i.saldo), 0) AS saldo, coalesce(sum(i.total_saldo), 0) AS total_saldo 
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

    public function update_cancel_balances($orderID, $userID) {
        $sql = "UPDATE order_balances 
                    SET usuario_id_cancel = ".$userID.", 
                        data_cancel = '".date("Y-m-d H:i:s")."' 
                    WHERE order_id = ".$orderID." 
                            AND data_cancel = '1901-01-01 00:00:00' ";

        $this->query($sql);
    }
}

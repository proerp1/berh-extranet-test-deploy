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

    public $actsAs = ['Containable'];

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

        $sql = "SELECT MIN(i.id) AS id, be.total AS total, be.order_item_id 
                    FROM orders o
                        INNER JOIN order_items i ON i.order_id = o.id
                        INNER JOIN customer_user_itineraries t ON t.id = i.customer_user_itinerary_id 
                                                                    AND o.customer_id = t.customer_id
                        INNER JOIN (SELECT b.customer_user_id, b.benefit_id, b.order_id, b.order_item_id, SUM(b.total) AS total
                                        FROM order_balances b
                                        WHERE b.data_cancel = '1901-01-01'
                                                AND b.tipo = 1 
                                        GROUP BY b.customer_user_id, b.benefit_id, b.order_id, b.order_item_id
                                    ) be ON be.customer_user_id = i.customer_user_id
                                            AND be.benefit_id = t.benefit_id
                                            AND be.order_id = o.id
                    WHERE o.id = ".$orderID." 
                            AND o.data_cancel = '1901-01-01 00:00:00'
                            AND i.data_cancel = '1901-01-01 00:00:00'
                    GROUP BY be.customer_user_id, be.benefit_id, be.order_id, be.order_item_id
                ";
        $result = $this->query($sql);

        if ($result) { 
            for ($i=0; $i < count($result); $i++) { 
                $itemID = $result[$i]['be']['order_item_id'] ? $result[$i]['be']['order_item_id'] : $result[$i][0]['id'];
                $total  = $result[$i]['be']['total'];

                $this->query("UPDATE order_items SET saldo = ".$total.", total_saldo = (subtotal - ".$total."), updated = now(), updated_user_id = ".$userID." WHERE id = ".$itemID);
            }
        }

        $sql = "SELECT o.id, COALESCE(SUM(i.saldo), 0) AS saldo, COALESCE(SUM(i.total_saldo), 0) AS total_saldo, COALESCE(p.management_feel, 0) AS fee_saldo
                    FROM orders o
                        INNER JOIN order_items i ON i.order_id = o.id
                        INNER JOIN customers c ON c.id = o.customer_id
                        LEFT JOIN proposals p ON p.customer_id = c.id AND p.status_id = 99 AND p.data_cancel = '1901-01-01 00:00:00'
                    WHERE o.id = ".$orderID." 
                    AND o.data_cancel = '1901-01-01 00:00:00' 
                    AND i.data_cancel = '1901-01-01 00:00:00'
                    AND c.data_cancel = '1901-01-01 00:00:00'
                ";
        $result = $this->query($sql);

        if ($result) { 
            for ($i=0; $i < count($result); $i++) { 
                $orderID = $result[$i]['o']['id'];
                $saldo = $result[$i][0]['saldo'];
                $total_saldo = $result[$i][0]['total_saldo'];
                $fee_saldo = $result[$i][0]['fee_saldo'];

                $this->query("UPDATE orders SET saldo = ".$saldo.", total_saldo = ".$total_saldo.", fee_saldo = ".$fee_saldo.", updated = now() WHERE id = ".$orderID);
            }
        }

        return true;
    }

    public function update_cancel_balances($orderID, $tipo, $userID) {
        $sql = "UPDATE order_balances 
                    SET usuario_id_cancel = ".$userID.", 
                        data_cancel = '".date("Y-m-d H:i:s")."' 
                    WHERE order_id = ".$orderID." 
                            AND tipo = ".$tipo." 
                            AND data_cancel = '1901-01-01 00:00:00' ";

        $this->query($sql);
    }

    public function find_user_order_items($orderID, $cpf) {
        $sql = "SELECT u.id
                    FROM order_items i
                        INNER JOIN customer_users u ON u.id = i.customer_user_id 
                    WHERE i.order_id = ".$orderID."
                            AND i.data_cancel = '1901-01-01 00:00:00'
                            AND u.data_cancel = '1901-01-01 00:00:00'
                            AND REPLACE(REPLACE(u.cpf, '-', ''), '.', '') LIKE '%".$cpf."%'
                ";
        $rsSql = $this->query($sql);

        return $rsSql;
    }

    public function update_cancel_balances_all($userID) {
        $sql = "SELECT t.order_id 
                    FROM tmp_order_balances t
                    GROUP BY t.order_id
                    ORDER BY 1
                ";
        $result = $this->query($sql);

        if ($result) { 
            for ($i=0; $i < count($result); $i++) { 
                $order_id = $result[$i]['t']['order_id'];

                $this->query("UPDATE order_balances 
                                SET usuario_id_cancel = ".$userID.", 
                                    data_cancel = '".date("Y-m-d H:i:s")."' 
                                WHERE order_id = ".$order_id." 
                                        AND data_cancel = '1901-01-01 00:00:00' 
                            ");
            }
        }

        return true;
    }

    public function find_order_balances_all($userID) {
        $sql = "SELECT t.order_id, t.benefit, t.document, t.total, b.id 
                    FROM tmp_order_balances t 
                        LEFT JOIN benefits b ON b.code = t.benefit AND b.data_cancel = '1901-01-01 00:00:00' 
                    ORDER BY t.order_id
                ";
        $result = $this->query($sql);

        if ($result) { 
            for ($i=0; $i < count($result); $i++) { 
                $order_id = $result[$i]['t']['order_id'];
                $benefit_code = $result[$i]['t']['benefit'];
                $document = $result[$i]['t']['document'];
                $total = $result[$i]['t']['total'];
                $benefit_id = $result[$i]['b']['id'];
                $customer_user_id = 0;

                $this->query("INSERT INTO order_balances (order_id, customer_user_id, benefit_id, document, total, created, user_created_id, pedido_operadora) 
                                VALUES ('".$order_id."', '".$customer_user_id."', '".$benefit_id."', '".$document."', '".$total."', now(), '".$userID."', '".$order_id."')");
            }
        }

        return true;
    }

    public function update_order_item_saldo_all($userID) {
        $sql = "SELECT t.order_id 
                    FROM tmp_order_balances t
                    GROUP BY t.order_id
                    ORDER BY 1
                ";
        $result = $this->query($sql);

        if ($result) { 
            for ($i=0; $i < count($result); $i++) { 
                $order_id = $result[$i]['t']['order_id'];

                $this->update_order_item_saldo($order_id, $userID);
            }
        }

        return true;
    }

    public function update_user_order_item_saldo_all() {
        $sql = "UPDATE order_balances AS b1, 
                        (SELECT b.id, u.id AS customer_user_id
                            FROM order_balances b
                            INNER JOIN order_items i ON i.order_id = b.order_id 
                            INNER JOIN customer_users u ON u.id = i.customer_user_id
                            WHERE b.order_id = b.pedido_operadora 
                            AND b.customer_user_id = 0 
                            AND i.data_cancel = '1901-01-01 00:00:00' 
                            AND u.data_cancel = '1901-01-01 00:00:00' 
                            AND b.data_cancel = '1901-01-01 00:00:00' 
                            AND REPLACE(REPLACE(u.cpf, '-', ''), '.', '') LIKE CONCAT('%', b.document,'%')
                            GROUP BY b.id, u.id
                        ) AS b2
                    SET b1.customer_user_id = b2.customer_user_id, b1.pedido_operadora = null 
                    WHERE b1.id = b2.id
                ";
        $result = $this->query($sql);

        return true;
    }
}

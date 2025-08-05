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
    'OrderItem' => array( // Adicione esta associação
        'className' => 'OrderItem',
        'foreignKey' => 'order_item_id'
    ),
        'UserUpdated' => array(  
        'className' => 'User',
        'foreignKey' => 'user_updated_id'
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

                $sql_sup = "SELECT i.id, s.transfer_fee_type, IFNULL(s.transfer_fee_percentage, 0) AS transfer_fee_percentage 
                                FROM order_items i 
                                    INNER JOIN customer_user_itineraries u ON u.id = i.customer_user_itinerary_id 
                                    INNER JOIN benefits b ON b.id = u.benefit_id 
                                    INNER JOIN suppliers s ON s.id = b.supplier_id 
                                WHERE i.id = ".$itemID." 
                                        AND i.data_cancel = '1901-01-01 00:00:00'
                                        AND u.data_cancel = '1901-01-01 00:00:00'
                                        AND b.data_cancel = '1901-01-01 00:00:00'
                                        AND s.data_cancel = '1901-01-01 00:00:00'
                            ";
                $res_sup = $this->query($sql_sup);

                if (isset($res_sup[0])) {
                    $transfer_fee_percentage = $res_sup[0][0]['transfer_fee_percentage'];

                    if ($res_sup[0]['s']['transfer_fee_type'] == 2) {
                        $transfer_fee = $total * ($transfer_fee_percentage / 100);
                    } else {
                        $transfer_fee = $transfer_fee_percentage;
                    }     
                } else {
                    $transfer_fee = 0;
                }

                $this->query("UPDATE order_items 
                                SET saldo_transfer_fee = ".$transfer_fee.", saldo = ".$total.", total_saldo = (subtotal - ".$total."), updated = now(), updated_user_id = ".$userID." 
                                WHERE id = ".$itemID);
            }
        }

        $sql = "SELECT o.id, 
                        COALESCE(SUM(i.saldo_transfer_fee), 0) AS saldo_transfer_fee, 
                        COALESCE(SUM(i.saldo), 0) AS saldo, 
                        COALESCE(SUM(i.total_saldo), 0) AS total_saldo, 
                        COALESCE(p.management_feel, 0) AS fee_saldo 
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
                $saldo_transfer_fee = $result[$i][0]['saldo_transfer_fee'];
                $saldo = $result[$i][0]['saldo'];
                $total_saldo = $result[$i][0]['total_saldo'];
                $fee_saldo = $result[$i][0]['fee_saldo'];

                if ($orderID) {
                    $this->query("UPDATE orders 
                                    SET saldo_transfer_fee = ".$saldo_transfer_fee.", saldo = ".$saldo.", total_saldo = ".$total_saldo.", fee_saldo = ".$fee_saldo.", updated = now() 
                                    WHERE id = ".$orderID);
                }
            }
        }

        return true;
    }

    public function update_cancel_balances($orderID, $tipo, $userID, $itemId) {
        $sql = "UPDATE order_balances 
                    SET usuario_id_cancel = ".$userID.", 
                        data_cancel = '".date("Y-m-d H:i:s")."' 
                    WHERE order_id = ".$orderID." 
                            AND tipo = ".$tipo." 
                            AND order_item_id = ".$itemId." 
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
}

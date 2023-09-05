<?php
class OrderItem extends AppModel {
    public $name = 'OrderItem';
    public $useTable = 'order_items';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id'
        ),
        'CustomerUserItinerary' => array(
            'className' => 'CustomerUserItinerary',
            'foreignKey' => 'customer_user_itinerary_id'
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        )
    );

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['OrderItem.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['transfer_fee'])) {
                $results[$key][$this->alias]['transfer_fee_not_formated'] = $results[$key][$this->alias]['transfer_fee'];
                $results[$key][$this->alias]['transfer_fee'] = number_format($results[$key][$this->alias]['transfer_fee'], 2, ',', '.');
            }

            // var = desconto
            if (isset($val[$this->alias]['var'])) {
                $results[$key][$this->alias]['var_not_formated'] = $results[$key][$this->alias]['var'];
                $results[$key][$this->alias]['var'] = number_format($results[$key][$this->alias]['var'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['price_per_day'])) {
                $results[$key][$this->alias]['price_per_day_not_formated'] = $results[$key][$this->alias]['price_per_day'];
                $results[$key][$this->alias]['price_per_day'] = number_format($results[$key][$this->alias]['price_per_day'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['subtotal'])) {
                $results[$key][$this->alias]['subtotal_not_formated'] = $results[$key][$this->alias]['subtotal'];
                $results[$key][$this->alias]['subtotal'] = number_format($results[$key][$this->alias]['subtotal'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['total'])) {
                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['total'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total'], 2, ',', '.');
            }
        }

        return $results;
    }

    public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['transfer_fee'])) {
			$this->data[$this->alias]['transfer_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['transfer_fee']);
		}

        if (!empty($this->data[$this->alias]['var'])) {
			$this->data[$this->alias]['var'] = $this->priceFormatBeforeSave($this->data[$this->alias]['var']);
		}

        if (!empty($this->data[$this->alias]['price_per_day'])) {
			$this->data[$this->alias]['price_per_day'] = $this->priceFormatBeforeSave($this->data[$this->alias]['price_per_day']);
		}

        if (!empty($this->data[$this->alias]['subtotal'])) {
			$this->data[$this->alias]['subtotal'] = $this->priceFormatBeforeSave($this->data[$this->alias]['subtotal']);
		}

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

    public function apiLastOrders(){
        $sql = "
        SELECT
            sum(OrderItem.subtotal) AS pedido_valor,
            CONCAT(CONCAT(CONCAT('5803-', Order.id), '-'), CustomerUser.id) AS pedido_id,
            CustomerUser.id AS customer_user_id,
            CustomerUser.name AS colaborador_nome,
            CustomerUser.cpf AS colaborador_cpf,
            CustomerUser.rg AS colaborador_rg,
            CustomerUser.emissor_rg AS colaborador_emissor_rg,
            CustomerUser.emissor_estado AS colaborador_estado_emissao_rg,
            CustomerUser.sexo AS colaborador_sexo,
            CustomerUser.data_nascimento AS colaborador_data_nascimento,
            CustomerUser.nome_mae AS colaborador_nome_mae,
            CustomerUser.email AS colaborador_email,
            CustomerUserAddress.address_line AS colaborador_endereco_logradouro,
            CustomerUserAddress.address_number AS colaborador_endereco_numero,
            CustomerUserAddress.neighborhood AS colaborador_endereco_bairro,
            CustomerUserAddress.city AS colaborador_endereco_cidade,
            CustomerUserAddress.state AS colaborador_endereco_estado,
            CustomerUserAddress.zip_code AS colaborador_endereco_cep,
            CustomerUserAddress.address_complement AS colaborador_endereco_complemento,
            Customer.nome_primario AS empresa_razao_social,
            Customer.documento AS empresa_cnpj,
            Customer.nome_secundario AS empresa_nome_reduzido,
            Customer.endereco AS empresa_endereco_logradouro,
            Customer.numero AS empresa_endereco_numero,
            Customer.bairro AS empresa_endereco_bairro,
            Customer.cidade AS empresa_endereco_cidade,
            Customer.estado AS empresa_endereco_estado,
            Customer.cep AS empresa_endereco_cep,
            Customer.complemento AS empresa_endereco_complemento
        FROM
            order_items OrderItem
        INNER JOIN orders `Order` on OrderItem.order_id = Order.id
        INNER JOIN customer_users CustomerUser ON OrderItem.customer_user_id = CustomerUser.id
        INNER JOIN customer_user_addresses CustomerUserAddress on CustomerUser.id = CustomerUserAddress.customer_user_id
        INNER JOIN customers Customer ON CustomerUser.customer_id = Customer.id
        WHERE OrderItem.data_cancel = '1901-01-01 00:00:00'
        and `Order`.data_cancel = '1901-01-01 00:00:00'
        and CustomerUser.data_cancel = '1901-01-01 00:00:00'
        and Customer.data_cancel = '1901-01-01 00:00:00'
        and `Order`.status_id = 85
        GROUP BY
            Order.id, OrderItem.customer_user_id
        ";

        return $this->query($sql);
    }
}

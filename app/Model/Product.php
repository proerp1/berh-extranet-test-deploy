<?php
class Product extends AppModel
{
    public $name = 'Product';

    public $hasMany = [
        'Answer' => [
            'className' => 'Answer',
            'foreignKey' => 'product_id',
            'conditions' => ['Answer.data_cancel' => '1901-01-01 00:00:00']
        ],
        'Feature' => [
            'className' => 'Feature',
            'foreignKey' => 'product_id',
            'conditions' => ['Feature.data_cancel' => '1901-01-01 00:00:00']
        ],
        'ProductPrice' => [
            'conditions' => ['ProductPrice.data_cancel' => '1901-01-01 00:00:00']
        ],
        'ProductFeature' => [
            'conditions' => ['ProductFeature.data_cancel' => '1901-01-01 00:00:00']
        ]
    ];

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Product.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function beforeSave($options = [])
    {
        if (!empty($this->data['Product']['valor'])) {
            $this->data['Product']['valor'] = $this->priceFormatBeforeSave($this->data['Product']['valor']);
        }
        if (!empty($this->data['Product']['valor_minimo'])) {
            $this->data['Product']['valor_minimo'] = $this->priceFormatBeforeSave($this->data['Product']['valor_minimo']);
        }
        
        return true;
    }

    public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val['Product']['valor'])) {
                $results[$key]['Product']['valor'] = number_format($results[$key]['Product']['valor'], 2, ',', '.');
            }
            if (isset($val['Product']['valor_minimo'])) {
                $results[$key]['Product']['valor_minimo'] = number_format($results[$key]['Product']['valor_minimo'], 2, ',', '.');
            }
        }

        return $results;
    }

    public function queryRanking($billing_id, $page = 1, $limit = 10)
    {
        $offset = $limit * ($page - 1);
        $sql = "SELECT trim(p.name) AS produto, SUM(n.qtde_consumo) AS qtdeProduto,
			round(SUM(n.valor_consumo),2) AS valor_consumo, round(SUM(n.valor_total),2) AS valor_cobrado
			FROM billing_monthly_payments bm
			INNER JOIN customers c ON c.id = bm.customer_id
			INNER JOIN negativacao n ON n.billing_id = bm.billing_id AND n.customer_id = c.id AND n.data_cancel = '1901-01-01'
			LEFT JOIN products p ON p.id = n.product_id
			WHERE bm.billing_id = ".$billing_id."
			AND bm.data_cancel = '1901-01-01'
			GROUP BY p.id
			
			UNION
			
			SELECT trim(p.name) AS produto, SUM(n.qtde_realizado) AS qtdeProduto,
				round(SUM(n.valor_total),2) AS valor_consumo, round(SUM(n.valor_total),2) AS valor_cobrado
			FROM billing_monthly_payments bm
			INNER JOIN customers c ON c.id = bm.customer_id
			INNER JOIN pefin n ON n.billing_id = bm.billing_id AND n.customer_id = c.id AND n.data_cancel = '1901-01-01'
			INNER JOIN products p ON p.id = n.product_id
			WHERE bm.billing_id = ".$billing_id."
			AND bm.data_cancel = '1901-01-01'
			GROUP BY p.id
			
			ORDER BY valor_cobrado DESC, produto
			LIMIT ".$limit." OFFSET ".$offset;

        return $sql;
    }

    public function countRanking($billing_id)
    {
        return $this->query("select count(1) tot from (
								SELECT trim(p.name)                   AS produto,
								SUM(n.qtde_consumo)            AS qtdeProduto,
								round(SUM(n.valor_consumo), 2) AS valor_consumo,
								round(SUM(n.valor_total), 2)   AS valor_cobrado
							FROM billing_monthly_payments bm
									INNER JOIN customers c ON c.id = bm.customer_id
									INNER JOIN negativacao n
												ON n.billing_id = bm.billing_id AND n.customer_id = c.id AND n.data_cancel = '1901-01-01'
									LEFT JOIN products p ON p.id = n.product_id
							WHERE bm.billing_id = ".$billing_id."
							AND bm.data_cancel = '1901-01-01'
							GROUP BY p.id
							
							UNION
							
							SELECT trim(p.name)                 AS produto,
								SUM(n.qtde_realizado)        AS qtdeProduto,
								round(SUM(n.valor_total), 2) AS valor_consumo,
								round(SUM(n.valor_total), 2) AS valor_cobrado
							FROM billing_monthly_payments bm
									INNER JOIN customers c ON c.id = bm.customer_id
									INNER JOIN pefin n ON n.billing_id = bm.billing_id AND n.customer_id = c.id AND n.data_cancel = '1901-01-01'
									INNER JOIN products p ON p.id = n.product_id
							WHERE bm.billing_id = ".$billing_id."
							AND bm.data_cancel = '1901-01-01'
							GROUP BY p.id
							
							ORDER BY valor_cobrado DESC, produto) a");
    }
}

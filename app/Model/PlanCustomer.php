<?php 
App::uses('AuthComponent', 'Controller/Component');
class PlanCustomer extends AppModel {
    public $name = 'PlanCustomer';

    public $belongsTo = array(
        'Plan',
        'Customer',
        'PriceTable',
        'Status' => array(
            'className' => 'Status',
            'foreignKey' => 'status_id'
        ),
        'UsuarioAlteracao' => array(
            'className' => 'User',
            'foreignKey' => 'user_updated_id'
        )
    );

    public function beforeFind($queryData) {

        $queryData['conditions'][] = array('PlanCustomer.data_cancel' => '1901-01-01 00:00:00');
        
        return $queryData;
    }

    public function beforeSave($options = array()) {
        if (!empty($this->data['PlanCustomer']['mensalidade'])) {
            $this->data['PlanCustomer']['mensalidade'] = $this->priceFormatBeforeSave($this->data['PlanCustomer']['mensalidade']);
        }

        if (!empty($this->data['PlanCustomer']['created'])) {
            $this->data['PlanCustomer']['created'] = $this->priceFormatBeforeSave($this->data['PlanCustomer']['created']);
        }
        
        return true;
    }

    public function priceFormatBeforeSave($price) {
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }

    public function afterFind($results, $primary = false){
        foreach ($results as $key => $val) {
            if (isset($val['PlanCustomer']['mensalidade'])) {
                $results[$key]['PlanCustomer']['mensalidade_nao_formatada'] = $results[$key]['PlanCustomer']['mensalidade'];
                $results[$key]['PlanCustomer']['mensalidade'] = number_format($results[$key]['PlanCustomer']['mensalidade'],2,',','.');
            }

            if (isset($val['PlanCustomer']['created'])) {
                $results[$key]['PlanCustomer']['created_nao_formatada'] = $results[$key]['PlanCustomer']['created'];
                $results[$key]['PlanCustomer']['created'] = date("d/m/Y", strtotime($val['PlanCustomer']['created']));
            }
        }

        return $results;
    }

  public function find_produto_composicao_plano($customer_id, $product_id) {
    $result = $this->query("SELECT p.id, pc.id, p.quantity, p.type, plp.gratuidade
                                                            FROM plans p
                                                                INNER JOIN plan_customers pc ON p.id = pc.plan_id
                                                                INNER JOIN plan_products plp ON plp.plan_id = p.id
                                                                INNER JOIN products po ON po.id = plp.product_id and po.data_cancel = '1901-01-01 00:00:00' AND po.tipo NOT IN (3) 
                                                            WHERE p.data_cancel = '1901-01-01 00:00:00' AND pc.data_cancel = '1901-01-01 00:00:00' AND plp.data_cancel = '1901-01-01 00:00:00' 
                                                                        AND p.status_id = 1 AND pc.status_id = 1 AND pc.customer_id = ".$customer_id." AND plp.product_id = ".$product_id."");

    return $result;
  }

  public function find_tipo_plano($customer_id) {
    $result = $this->query("SELECT p.id, p.type
                                                        FROM plans p
                                                        INNER JOIN plan_customers pc ON p.id = pc.plan_id
                                                        WHERE p.data_cancel = '1901-01-01 00:00:00' AND pc.data_cancel = '1901-01-01 00:00:00' AND pc.status_id = 1 AND pc.customer_id = ".$customer_id);

    return $result;
  }

    
}
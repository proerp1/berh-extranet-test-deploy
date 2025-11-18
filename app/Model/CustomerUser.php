<?php

App::uses('AuthComponent', 'Controller/Component');
class CustomerUser extends AppModel
{
    public $name = 'CustomerUser';
    public $actsAs = [
        'Containable',
        'Upload.Upload' => [
            'img_profile' => [
                'rootDir' => ROOT_SITE.'/app/'
            ]
        ],
    ];

    public $hasMany = [
        'CustomerUserItinerary' => [
            'className' => 'CustomerUserItinerary',
            'foreignKey' => 'customer_user_id',
            'dependent' => true
        ]
        
    ];

    public $belongsTo = [
        'Customer' => [
            'className' => 'Customer',
            'foreignKey' => 'customer_id'
        ],
        'Resale' => [
            'className' => 'Resale',
            'foreignKey' => 'customer_id',
            // 'conditions' => array('CustomerUser.resale' => 1)
        ],
        'Seller' => [
            'className' => 'Seller',
            'foreignKey' => 'customer_id',
            // 'conditions' => array('CustomerUser.seller' => 1)
        ],
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1],
        ],
        'CustomerDepartment' => [
            'className' => 'CustomerDepartment',
            'foreignKey' => 'customer_departments_id',
        ],
        'CostCenter' => [
            'className' => 'CostCenter',
            'foreignKey' => 'customer_cost_center_id',
        ],
        'UserUpdated' => [
            'className' => 'User',
            'foreignKey' => 'user_updated_id',
        ],
    ];

    public $hasAndBelongsToMany = [
        'EconomicGroup' => [
            'className' => 'EconomicGroup',
            'joinTable' => 'customer_users_economic_groups',
            'foreignKey' => 'customer_user_id',
            'associationForeignKey' => 'economic_group_id',
        ],
        'EconomicGroupLogin' => [
            'className' => 'EconomicGroup',
            'joinTable' => 'customer_users_login_economic_groups',
            'foreignKey' => 'customer_user_id',
            'associationForeignKey' => 'economic_group_id',
        ],
    ];

    public $hasOne = [
        'CustomerAddress' => [
            'conditions' => ['CustomerAddress.data_cancel' => '1901-01-01 00:00:00']
        ],
    ];

    public $validate = [
        'email' => [
            // 'email' => [
            //     'rule' => 'email',
            //     'message' => 'O e-mail deve ser válido',
            // ],
            
            // 'required' => [
            //     'rule' => ['notBlank'],
            //     'message' => 'Campo obrigatório',
            // ],
            'customUnique' => [
                'rule' => ['customUnique'],
                'message' => 'O e-mail fornecido já foi cadastrado',
            ],
        ],
        'name' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'status_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
    ];

    public function customUnique($check){
        if(empty(trim($check['email']))){
            return true;
        }
        $cond = array_merge($check, ['CustomerUser.data_cancel' => '1901-01-01 00:00:00']);
        if(!empty($this->id)){
            $cond['CustomerUser.id !='] = $this->id;
        }
        $emailUnique = $this->find('count', array(
            'conditions' => $cond,
            'recursive' => -1
        ));
        return $emailUnique < 1;
    }

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CustomerUser.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['data_nascimento'])) {
                $results[$key][$this->alias]['data_nascimento_nao_formatado'] = $results[$key][$this->alias]['data_nascimento'];
                $results[$key][$this->alias]['data_nascimento'] = date('d/m/Y', strtotime($results[$key][$this->alias]['data_nascimento']));
            }

            if (isset($val[$this->alias]['data_flag_lgpd'])) {
                $results[$key][$this->alias]['data_flag_lgpd_nao_formatado'] = $results[$key][$this->alias]['data_flag_lgpd'];
                $results[$key][$this->alias]['data_flag_lgpd'] = date('d/m/Y H:i:s', strtotime($results[$key][$this->alias]['data_flag_lgpd']));
            }

            if (isset($val[$this->alias]['cel'])) {
                $cel = str_replace(['(', ')', ' ', '-'], '', $results[$key][$this->alias]['cel']);
                $results[$key][$this->alias]['cel_sem_ddd'] = substr($cel, 2);
                $results[$key][$this->alias]['ddd_cel'] = substr($cel, 0, 2);
            } else {
                $results[$key][$this->alias]['cel_sem_ddd'] = null;
                $results[$key][$this->alias]['ddd_cel'] = null;
            }

            if (isset($val[$this->alias]['tel'])) {
                $tel = str_replace(['(', ')', ' ', '-'], '', $results[$key][$this->alias]['tel']);
                $results[$key][$this->alias]['tel_sem_ddd'] = substr($tel, 2);
                $results[$key][$this->alias]['ddd_tel'] = substr($tel, 0, 2);
            } else {
                $results[$key][$this->alias]['tel_sem_ddd'] = null;
                $results[$key][$this->alias]['ddd_tel'] = null;
            }
        }

        return $results;
    }

    public function beforeSave($options = [])
    {
        if (isset($this->data[$this->alias]['cpf'])) {
            $this->data[$this->alias]['cpf'] = preg_replace('/\D/', '', $this->data[$this->alias]['cpf']);
        }
        if (isset($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password'], null, true);
        }

        if (!empty($this->data[$this->alias]['data_nascimento'])) {
            $this->data[$this->alias]['data_nascimento'] = $this->dateFormatBeforeSave($this->data[$this->alias]['data_nascimento']);
        }

        return true;
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

    public function find_pedido_beneficiarios_info($orderID, $pix = 'ambos')
    {
        $pixWhere = '';
        if ($pix == 'sim') {
            $pixWhere = "AND b.pix_id != ''";
        } elseif ($pix == 'nao') {
            $pixWhere = "AND b.pix_id = ''";
        }

        $sql = "SELECT u.name, u.cpf, u.email, IFNULL(u.cel, u.tel) as telefone, k.name, k.code, CONCAT(b.branch_number, '-', b.branch_digit) AS agencia, CONCAT(b.acc_number, '-', b.acc_digit) AS conta, b.pix_type, b.pix_id, t.description, i.subtotal, i.total, i.id, i.pix_status_id
                    FROM orders o 
                        INNER JOIN order_items i ON i.order_id = o.id 
                        INNER JOIN customers c ON c.id = o.customer_id 
                        INNER JOIN customer_users u ON u.customer_id = c.id 
                                                        AND u.id = i.customer_user_id 
                        LEFT JOIN customer_user_bank_accounts b ON b.customer_user_id = u.id AND b.data_cancel = '1901-01-01 00:00:00' 
                        LEFT JOIN bank_codes k ON k.id = b.bank_code_id 
                        LEFT JOIN bank_account_types t ON t.id = b.account_type_id 
                    WHERE o.id = ".$orderID." $pixWhere
                            AND o.data_cancel = '1901-01-01 00:00:00' 
                            AND c.data_cancel = '1901-01-01 00:00:00' 
                            AND u.data_cancel = '1901-01-01 00:00:00' 
                            AND i.data_cancel = '1901-01-01 00:00:00' 
                    ORDER BY 1 
                    ";

        $res = $this->query($sql);

        return $res;
    }

    public function find_pix_pendentes($orderID)
    {
        $sql = "SELECT 
                  u.name, 
                  u.cpf, 
                  u.email, 
                  IFNULL(u.cel, u.tel) AS telefone, 
                  k.name, 
                  k.code, 
                  CONCAT(b.branch_number, '-', b.branch_digit) AS agencia, 
                  CONCAT(b.acc_number, '-', b.acc_digit) AS conta, 
                    b.status_id,
                  b.pix_type, 
                  b.pix_id, 
                  t.description, 
                  i.subtotal, 
                  i.total, 
                  i.id, 
                  i.pix_status_id
              FROM orders o 
              INNER JOIN order_items i 
                  ON i.order_id = o.id 
              INNER JOIN customers c 
                  ON c.id = o.customer_id 
              INNER JOIN customer_users u 
                  ON u.customer_id = c.id 
                 AND u.id = i.customer_user_id 
              LEFT JOIN (
                  SELECT b1.*
                  FROM customer_user_bank_accounts b1
                  WHERE b1.data_cancel = '1901-01-01 00:00:00'
                    AND b1.id = (
                        SELECT MAX(b2.id) 
                        FROM customer_user_bank_accounts b2 
                        WHERE b2.customer_user_id = b1.customer_user_id
                          AND b2.data_cancel = '1901-01-01 00:00:00'
                          AND b2.status_id = 1
                    )
              ) b ON b.customer_user_id = u.id
              LEFT JOIN bank_codes k ON k.id = b.bank_code_id 
              LEFT JOIN bank_account_types t ON t.id = b.account_type_id 
              WHERE o.id in (".implode(', ', $orderID).")
                AND i.pix_status_id = 109
                AND o.data_cancel = '1901-01-01 00:00:00' 
                AND c.data_cancel = '1901-01-01 00:00:00' 
                AND u.data_cancel = '1901-01-01 00:00:00' 
                AND i.data_cancel = '1901-01-01 00:00:00' 
                AND b.status_id = 1 
              ORDER BY 1;
                    ";

        $res = $this->query($sql);

        return $res;
    }

}

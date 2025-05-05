<?php
class Income extends AppModel
{
    public $name = 'Income';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 5]
        ],
        'UsuarioBaixa' => [
            'className' => 'User',
            'foreignKey' => 'usuario_id_baixa',
        ],
        'UsuarioCancelamento' => [
            'className' => 'User',
            'foreignKey' => 'usuario_id_cancelamento',
        ],
        'BankAccount',
        'Revenue',
        'CostCenter',
        'Customer' => [
            'order' => ['Customer.nome_secundario' => 'asc']
        ],
        'Order',
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
    ];

    public $hasOne = [
        'CnabItem' => [
            'order' => ['CnabItem.id' => 'desc']
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Income.data_cancel' => '1901-01-01 00:00:00'];
    
        return $queryData;
    }

    public function beforeSave($options = [])
    {
        if (!empty($this->data[$this->alias]['vencimento'])) {
            $this->data[$this->alias]['vencimento'] = $this->dateFormatBeforeSave($this->data[$this->alias]['vencimento']);
        }
 
        if (!empty($this->data[$this->alias]['data_competencia'])) {
            $this->data[$this->alias]['data_competencia'] = $this->dateFormatBeforeSave($this->data[$this->alias]['data_competencia']);
        }

        if (!empty($this->data[$this->alias]['valor_bruto'])) {
            $this->data[$this->alias]['valor_bruto'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_bruto']);
        }

        if (!empty($this->data[$this->alias]['valor_multa'])) {
            $this->data[$this->alias]['valor_multa'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_multa']);
        }

        if (!empty($this->data[$this->alias]['valor_desconto'])) {
            $this->data[$this->alias]['valor_desconto'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_desconto']);
        }

        if (!empty($this->data[$this->alias]['valor_total'])) {
            $this->data[$this->alias]['valor_total'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_total']);
        }

        if (!empty($this->data[$this->alias]['nosso_numero'])) {
            $this->data[$this->alias]['nosso_numero'] = str_replace(['-','/','.'], '', $this->data[$this->alias]['nosso_numero']);
        }
    
        return true;
    }

    public function afterSave($created, $options = [])
    {
        if ($created) {
            $nosso_numero = $this->gerarNossoNumero($this->data[$this->alias]['id']);
      
            $this->query("UPDATE incomes i set i.nosso_numero = '$nosso_numero', i.doc_num = '$nosso_numero' WHERE i.id = ".$this->data[$this->alias]['id']);
        }
    }

    public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
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

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['vencimento'])) {
                $results[$key][$this->alias]['vencimento_nao_formatado'] = $val[$this->alias]['vencimento'];
                $results[$key][$this->alias]['vencimento'] = date("d/m/Y", strtotime($val[$this->alias]['vencimento']));
            }
            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created_nao_formatado'] = $val[$this->alias]['created'];
                $results[$key][$this->alias]['created'] = date("d/m/Y", strtotime($val[$this->alias]['created']));
            }

            if (isset($val[$this->alias]['data_competencia'])) {
                $results[$key][$this->alias]['data_competencia_nao_formatado'] = $val[$this->alias]['data_competencia'];
                $results[$key][$this->alias]['data_competencia'] = date("d/m/Y", strtotime($val[$this->alias]['data_competencia']));
            }
            if (isset($val[$this->alias]['data_pagamento'])) {
                $results[$key][$this->alias]['data_pagamento_nao_formatado'] = $val[$this->alias]['data_pagamento'];
                $results[$key][$this->alias]['data_pagamento'] = date("d/m/Y H:i:s", strtotime($val[$this->alias]['data_pagamento']));
            }
            if (isset($val[$this->alias]['data_baixa'])) {
                $results[$key][$this->alias]['data_baixa_nao_formatado'] = $val[$this->alias]['data_baixa'];
                $results[$key][$this->alias]['data_baixa'] = date("d/m/Y", strtotime($val[$this->alias]['data_baixa']));
            }
            if (isset($val[$this->alias]['valor_bruto'])) {
                $results[$key][$this->alias]['valor_bruto_nao_formatado'] = $results[$key][$this->alias]['valor_bruto'];
                $results[$key][$this->alias]['valor_bruto'] = number_format($results[$key][$this->alias]['valor_bruto'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['valor_multa'])) {
                $results[$key][$this->alias]['valor_multa_nao_formatado'] = $results[$key][$this->alias]['valor_multa'];
                $results[$key][$this->alias]['valor_multa'] = number_format($results[$key][$this->alias]['valor_multa'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['valor_desconto'])) {
                $results[$key][$this->alias]['valor_desconto_nao_formatado'] = $results[$key][$this->alias]['valor_desconto'];
                $results[$key][$this->alias]['valor_desconto'] = number_format($results[$key][$this->alias]['valor_desconto'], 2, ',', '.');
            }
            if (isset($val[$this->alias]['valor_total'])) {
                $results[$key][$this->alias]['valor_total_nao_formatado'] = $results[$key][$this->alias]['valor_total'];
                $results[$key][$this->alias]['valor_total'] = number_format($results[$key][$this->alias]['valor_total'], 2, ',', '.');
            }
            if (isset($val[$this->alias]['valor_pago'])) {
                $results[$key][$this->alias]['valor_pago_nao_formatado'] = $results[$key][$this->alias]['valor_pago'];
                $results[$key][$this->alias]['valor_pago'] = number_format($results[$key][$this->alias]['valor_pago'], 2, ',', '.');
            }
           
        }

        return $results;
    }

    public $validate = [
        'name' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'supplier_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'valor_bruto' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'valor_total' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'bank_account_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'status_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'cost_center_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'vencimento' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'data_competencia' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'expense_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ]
    ];

    public function getDadosBoleto($id, $type = 'first')
    {
        return $this->find($type, [
            'conditions' => ['Income.id in ('.$id.')'],
            'order' => ['Income.vencimento' => 'asc', 'Customer.nome_primario' => 'asc'], 
            "fields" => [
                "Income.*", 
                'Customer.*', 
                'BankAccount.*', 
                'Resale.id', 
                'Resale.razao_social', 
                'Resale.cnpj',
                'Resale.cep',
                'Resale.endereco',
                'Resale.numero',
                'Resale.bairro',
                'Resale.cidade',
                'Resale.estado',
                'BankTicket.*',
                'Order.economic_group_id'
            ],
            'joins' => [
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'INNER',
                    'conditions' => [
                        'Customer.id = Income.customer_id', 'Customer.data_cancel' => '1901-01-01'
                    ]
                ],
                [
                    'table' => 'resales',
                    'alias' => 'Resale',
                    'type' => 'INNER',
                    'conditions' => [
                        'Resale.id = Customer.cod_franquia'
                    ]
                ],
                [
                    'table' => 'bank_accounts',
                    'alias' => 'BankAccount',
                    'type' => 'INNER',
                    'conditions' => [
                        'BankAccount.id = Income.bank_account_id', 'BankAccount.data_cancel' => '1901-01-01'
                    ]
                ],
                [
                    'table' => 'bank_tickets',
                    'alias' => 'BankTicket',
                    'type' => 'INNER',
                    'conditions' => [
                        'BankAccount.id = BankTicket.bank_account_id', 'BankTicket.data_cancel' => '1901-01-01', 'BankTicket.status_id' => 1
                    ]
                ],
                [
                    'table' => 'orders',
                    'alias' => 'Order',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Order.id = Income.order_id'
                    ],
                ],
            ],
            'recursive' => -1
        ]);
    }


    // nosso numero
    public function gerarNossoNumero($income_id)
    {
        // Composição Nosso Numero - CEF SIGCB
      $dadosboleto["nosso_numero1"] = "000"; // tamanho 3
      $dadosboleto["nosso_numero_const1"] = "1"; //constanto 1 , 1=registrada , 2=sem registro
      $dadosboleto["nosso_numero2"] = "000"; // tamanho 3
      $dadosboleto["nosso_numero_const2"] = "4"; //constanto 2 , 4=emitido pelo proprio cliente
      $dadosboleto["nosso_numero3"] = str_pad($income_id, 9, '0', STR_PAD_LEFT); // tamanho 9

      //nosso número (sem dv) é 17 digitos
        $nnum = $this->formataNumero($dadosboleto["nosso_numero_const1"], 1, 0).$this->formataNumero($dadosboleto["nosso_numero_const2"], 1, 0).$this->formataNumero($dadosboleto["nosso_numero1"], 3, 0).$this->formataNumero($dadosboleto["nosso_numero2"], 3, 0).$this->formataNumero($dadosboleto["nosso_numero3"], 9, 0);
        //nosso número completo (com dv) com 18 digitos
        $nossonumero = $nnum . $this->digitoVerificadorNossonumero($nnum);

        return $nossonumero;
    }

    public function digitoVerificadorNossonumero($numero)
    {
        $resto2 = $this->modulo11($numero, 9, 1);
        $digito = 11 - $resto2;
        if ($digito == 10 || $digito == 11) {
            $dv = 0;
        } else {
            $dv = $digito;
        }
        return $dv;
    }

    public function formataNumero($numero, $loop, $insert, $tipo = "geral")
    {
        if ($tipo == "geral") {
            $numero = str_replace(",", "", $numero);
            while (strlen($numero)<$loop) {
                $numero = $insert . $numero;
            }
        }
        if ($tipo == "valor") {
            /*
            retira as virgulas
            formata o numero
            preenche com zeros
            */
            $numero = str_replace(",", "", $numero);
            while (strlen($numero)<$loop) {
                $numero = $insert . $numero;
            }
        }
        if ($tipo == "convenio") {
            while (strlen($numero)<$loop) {
                $numero = $numero . $insert;
            }
        }
        return $numero;
    }

    public function modulo11($num, $base=9, $r=0)
    {
        /**
         *   Autor:
         *           Pablo Costa <pablo@users.sourceforge.net>
         *
         *   Função:
         *    Calculo do Modulo 11 para geracao do digito verificador
         *    de boletos bancarios conforme documentos obtidos
         *    da Febraban - www.febraban.org.br
         *
         *   Entrada:
         *     $num: string numérica para a qual se deseja calcularo digito verificador;
         *     $base: valor maximo de multiplicacao [2-$base]
         *     $r: quando especificado um devolve somente o resto
         *
         *   Saída:
         *     Retorna o Digito verificador.
         *
         *   Observações:
         *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
         *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
         */

        $soma = 0;
        $fator = 2;

        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i-1, 1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2
                $fator = 1;
            }
            $fator++;
        }

        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } elseif ($r == 1) {
            $resto = $soma % 11;
            return $resto;
        }
    }
}

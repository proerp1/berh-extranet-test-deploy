<?php
class ImportarNegativacaoComponent extends Component
{
    public $components = ['Session'];

    public function importar_negativacao($arquivo, $billing_id)
    {
        ini_set('max_execution_time', 300);
        $Product = ClassRegistry::init('Product');
        $ProductAttribute = ClassRegistry::init('ProductAttribute');
        $Customer = ClassRegistry::init('Customer');
        $Negativacao = ClassRegistry::init('Negativacao');
        $PlanCustomer = ClassRegistry::init('PlanCustomer');
        $BillingMonthlyPayment = ClassRegistry::init('BillingMonthlyPayment');
        $ProductPrice = ClassRegistry::init('ProductPrice');
        $LoginConsulta = ClassRegistry::init('LoginConsulta');
        $Pefin = ClassRegistry::init('Pefin');
        $PefinMaintenance = ClassRegistry::init('PefinMaintenance');
        $ProdutosNaoCadastrados = ClassRegistry::init('ProdutosNaoCadastrados');
        $LinhasNaoImportadas = ClassRegistry::init('LinhasNaoImportadas');
        $BillingNovaVida = ClassRegistry::init('BillingNovaVida');
        $NovaVidaLogConsulta = ClassRegistry::init('NovaVidaLogConsulta');
        $CustomerDiscountsProduct = ClassRegistry::init('CustomerDiscountsProduct');
        
        $LinhasNaoImportadas->deleteAll(['LinhasNaoImportadas.billing_id' => $billing_id], false);
        //$BillingNovaVida->deleteAll(['BillingNovaVida.billing_id' => $billing_id], false);
        $ProdutosNaoCadastrados->deleteAll(['ProdutosNaoCadastrados.billing_id' => $billing_id], false);
        $ProdutosNaoCadastrados->deleteAll(['ProdutosNaoCadastrados.billing_id' => $billing_id], false);

        //update nova vida anterior
        $billing_atual = $BillingMonthlyPayment->find('first', ['conditions' => ['BillingMonthlyPayment.billing_id' => $billing_id]]);

        $NovaVidaLogConsulta->query("UPDATE nova_vida_log_consultas n SET n.faturado = 0 WHERE DATE_FORMAT(n.created, '%m-%Y') = '".date('m-Y', strtotime(str_replace('/', '-', $billing_atual['Billing']['date_billing'])))."' ");

        $data_ini = $billing_atual['Billing']['date_billing_nao_formatado'];
        $data_fim = date('Y-m-t', strtotime($billing_atual['Billing']['date_billing_nao_formatado']));

        $Customer->query("UPDATE cadastro_pefin AS d
											INNER JOIN customers AS c ON d.customer_id = c.id
											set d.faturado = 0
											WHERE d.faturado = 1 and d.created BETWEEN '$data_ini' AND '$data_fim' AND d.data_cancel = '1901-01-01'");

        /*
        //comentado por rodolfo 23/10 a partir dessa data o primeiro processo é faturar as negativações
        //deleta negativação anterior
        $update_data = ['Negativacao.data_cancel' => 'current_timestamp', 'Negativacao.usuario_id_cancel' => CakeSession::read("Auth.User.id")];
        $Negativacao->updateAll(
            $update_data, //set
            ['Negativacao.billing_id' => $billing_id] //where
        );

        //deleta negativação anterior
        $update_data = ['Pefin.data_cancel' => 'current_timestamp', 'Pefin.usuario_id_cancel' => CakeSession::read("Auth.User.id")];
        $Pefin->updateAll(
            $update_data, //set
            ['Pefin.billing_id' => $billing_id] //where
        );

        $BillingMonthlyPayment->update_monthly_value_total($billing_id);
        */
        
        //loop through the csv file and insert into database
        $file = $arquivo['tmp_name'];
        $handle = fopen($file, "r");

        while ($data = fgetcsv($handle, 1000, ";", "'")) {
            $planoComposicao = [];
            $dados_negativacao = [];
            $data_billing = [];
            $valTotal = 0;
            $linha_nao_importada = [];
            $data_nao_cadastrado = [];
            if (trim($data[1]) != "") {
                $nome 			= utf8_encode($data[0]);
                $logon			= utf8_encode($data[1]);
                $logon 			= str_pad($logon, 8, '0', STR_PAD_LEFT);
                $numero			= utf8_encode($data[2]);
                $qtdConsumo = utf8_encode($data[3]);
                $valorUnitarioExcel = $data[4];
                $valorUnitarioExcel = str_replace('R$', '', $valorUnitarioExcel);
                $valorUnitarioExcel = trim($valorUnitarioExcel);

                $valorTotalExcel = $data[5];
                $valorTotalExcel = str_replace('R$', '', $valorTotalExcel);
                $valorTotalExcel = trim($valorTotalExcel);

                $produto = $ProductAttribute->find('first', ['conditions' => ['ProductAttribute.name LIKE' => "%".trim($nome)."%"]]);

                $cliente = [];
                // se for o logon da CREDCHEK (21976877) busca o cliente pelo documento, senao busca pelo login de consulta
                if ($logon == '21976877' || $logon == '76074527' || $logon == '47041107' || $logon == '13137277') {
                    if (!empty($numero)) {
                        if($logon == '21976877'){
                            //$documento = ($logon = 21976877 ? substr($numero, 2, 8) : $numero );
                            $documento = substr($numero, 2, 8);
                        } else {
                            $documento = $numero;
                        }
                        if ($documento) {
                            $cliente = $Customer->find('first', ['conditions' => ["REPLACE(Customer.documento,'.','') LIKE" => "%".$documento."%", "Customer.tipo_credor" => "C", "Customer.faturar" => "S", "Customer.status_id IN (3,4)"], 'recursive' => 2]);
                        }
                    }
                } else {
                    $login = $LoginConsulta->find('first', ['conditions' => ["LoginConsulta.login" => $logon, "LoginConsulta.data_cancel" => '1901-01-01'], 'recursive' => 2]);

                    if ($login) {
                        $cliente = $Customer->find('first', ['conditions' => ["Customer.id" => $login['LoginConsulta']['customer_id'], "Customer.tipo_credor" => "C", "Customer.faturar" => "S"], 'recursive' => 2]);
                    }
                }

                if (!empty($produto)) {
                    if (!empty($cliente)) {
                        $produtoID = $produto['Product']['id'];
                        $clienteID = $cliente['Customer']['id'];
                        $clientePlano = $PlanCustomer->find('first', ['conditions' => ['PlanCustomer.status_id' => 1, 'PlanCustomer.customer_id' => $clienteID]]);
                        if (empty($clientePlano)) {
                            debug('CLIENTE '.$clienteID.' SEM PLANO');
                            die();
                        }

                        $mensalidade = $BillingMonthlyPayment->find('first', ['conditions' => ["BillingMonthlyPayment.billing_id" => $billing_id, "BillingMonthlyPayment.customer_id" => $clienteID]]);

                        // se o cliente nao tiver nenhum registro na billing_monthly, cria um registro pra ele
                        if (empty($mensalidade)) {
                            $manutencao = $PefinMaintenance->find('first');

                            $maintenanceId = $manutencao['PefinMaintenance']['id'];
                            if ($cliente['Customer']['pefin_maintenance'] == 0) {
                                $maintenanceId = 0;
                            }

                            $dados_monthly = [
                            	'BillingMonthlyPayment' => [
                            		'billing_id' => $billing_id,
	                                'pefin_maintenance_id' => $maintenanceId,
	                                'customer_id' => $clienteID,
	                                'login_consulta_id' => $login['LoginConsulta']['id'],
	                                'monthly_value' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
	                                'monthly_value_total' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
	                                'desconto' => $clientePlano['Customer']['desconto'],
	                                'balance_available' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
	                                'quantity' => $clientePlano['Plan']['type'] == '2' ? '0' : $clientePlano['Plan']['quantity'],
	                                'user_creator_id' => CakeSession::read("Auth.User.id"),
	                                'created' => date('Y-m-d H:i:s')
	                            ]
	                        ];

                            $BillingMonthlyPayment->create();
                            $BillingMonthlyPayment->save($dados_monthly);

                            $mensalidade = $BillingMonthlyPayment->read();
                        }

                        /*if($produtoID == ''){
                            print trim($nome).'.....';
                            debug($produto);
                            die();
                        }*/
                        // busca o produto na composicao do plano do cliente
                        $planoComposicao = $PlanCustomer->find_produto_composicao_plano($clienteID, $produtoID);
                        $find_tipo_plano = $PlanCustomer->find_tipo_plano($clienteID);

                        $qtdePlano = $mensalidade['BillingMonthlyPayment']['quantity'];
                        $qtdeCon = $mensalidade['BillingMonthlyPayment']['billing_quantity'];
                        $saldo = $mensalidade['BillingMonthlyPayment']['balance_available'];
                        $valConsumed = $mensalidade['BillingMonthlyPayment']['monthly_value_total'];

                        $qtdeFaturado = $qtdeCon;
                        if (!isset($find_tipo_plano[0])) {
                            debug($clienteID);
                        }
                        
                        $tipo_plano = $find_tipo_plano[0]['p']['type'];
                        
                        //pega a qtde excedente
                        $dados_excedente = $this->get_qtd_excedente($planoComposicao, $qtdeFaturado, $qtdeCon, $qtdConsumo, $qtdePlano);

                        $qtdeExcedente = $dados_excedente['qtdeExcedente'];
                        $qtdeFaturado = $dados_excedente['qtdeFaturado'];

                        $precoProduto = $ProductPrice->find('all', ['conditions' => ["ProductPrice.product_id" => $produtoID]]);

                        $valor_consumido = 0;
                        $valor_total_consumido = 0;
                        $saldo_novo = 0;

                        // pega o preco do cliente na tabela de preços
                        $cliente_tabela_preco = $clientePlano['PlanCustomer']['price_table_id'];
                        // $tabela_precos_produto = $precoProduto['ProductPrice'];

                        foreach ($precoProduto as $key => $tabela_preco) {
                            if ($cliente_tabela_preco == $tabela_preco['ProductPrice']['price_table_id']) {
                                $valor_consulta = $tabela_preco['ProductPrice']['value'];
                            }
                        }
                            
                        $clientePlanoID = $clientePlano['PlanCustomer']['id'];

                        $valUnit = str_replace(',', '.', $valor_consulta);

                        // consulta para ver se o produto tem um desconto
                        $tem_desconto = false;
                        $desconto = $CustomerDiscountsProduct->find('all', ['conditions' => ['CustomerDiscount.customer_id' => $clienteID, 'CustomerDiscountsProduct.product_id' => $produtoID, 'CustomerDiscount.expire_date > ' => date('Y-m-d'), 'CustomerDiscount.data_cancel' => '1901-01-01'], 'fields' => ['sum(CustomerDiscount.discount) as total_desconto', 'CustomerDiscount.id']]);

                        if ($desconto[0][0]['total_desconto'] != null) {
                            $valUnit = number_format($valUnit - ($valUnit*($desconto[0][0]['total_desconto']/100)), 2, '.', '');
                            $tem_desconto = true;
                        }
                        // fim da consulta

                        if (empty($planoComposicao)) { // se nao tiver na composicao salva como excedente
                            $valMens = $mensalidade['BillingMonthlyPayment']['monthly_value_total'];

                            $valor_linha	= $valUnit * abs($qtdeExcedente);

                            $valTotal = $valor_linha + $valMens;

                            // dados para salvar a negativacao
                            $dados_negativacao = [
                                "Negativacao" => [
                                    "type"						=> 3,
                                    "billing_id"			=> $billing_id,
                                    "product_id"			=> $produtoID,
                                    "customer_id"			=> $clienteID,
                                    "centro_custo" 		=> $numero,
                                    "qtde_consumo" 		=> $qtdConsumo,
                                    "qtde_excedente"	=> $qtdeExcedente,
                                    "valor_excedente"	=> $valor_linha,
                                    "valor_unitario"	=> $valUnit,
                                    "valor_total" 		=> $valor_linha,
                                    "valor_unitario_excel" => $valorUnitarioExcel,
                                    "valor_total_excel" => $valorTotalExcel,
                                    "user_updated_id" => CakeSession::read("Auth.User.id")
                                ]
                            ];
                        } else {
                            if ($tipo_plano == 1) { // se for plano do tipo quantidade
                                $valMens = $mensalidade['BillingMonthlyPayment']['monthly_value_total'];

                                if ($qtdeExcedente > 0) {
                                    $valor_linha	= $valUnit * abs($qtdeExcedente);
                                } else {
                                    $valor_linha	= 0;
                                }

                                $valTotal = $valor_linha + $valMens;

                                // dados para salvar a negativacao
                                $dados_negativacao = [
                                    "Negativacao" => [
                                        "type"						=> 1,
                                        "billing_id"			=> $billing_id,
                                        "product_id"			=> $produtoID,
                                        "customer_id"			=> $clienteID,
                                        "centro_custo" 		=> $numero,
                                        "qtde_consumo" 		=> $qtdConsumo,
                                        "qtde_excedente"	=> $qtdeExcedente,
                                        "valor_excedente"	=> $valor_linha,
                                        "valor_unitario"	=> $valUnit,
                                        "valor_total" 		=> $valor_linha,
                                        "valor_unitario_excel" => $valorUnitarioExcel,
                                        "valor_total_excel" => $valorTotalExcel,
                                        "user_updated_id" => CakeSession::read("Auth.User.id")
                                    ]
                                ];
                            } else { // se for plano do tipo consumo

                                $valMens = $mensalidade['BillingMonthlyPayment']['monthly_value'];
                                $valor_consumido = $valUnit * abs($qtdConsumo);
                                $valor_consumido = str_replace(',', '.', $valor_consumido);

                                if ($valor_consumido < $saldo) {
                                    $saldo_novo = $saldo - $valor_consumido;
                                    $valTotal = $valConsumed;
                                    $excedente = 0;
                                } else {
                                    $saldo_novo = 0;
                                    $valTotal = $valConsumed + ($valor_consumido - $saldo);
                                    $excedente = $valor_consumido - $saldo;
                                }

                                $data_billing['BillingMonthlyPayment']['balance_available'] = $saldo_novo;

                                // dados para salvar a negativacao
                                $dados_negativacao = [
                                    "Negativacao" => [
                                        "type"						=> 2,
                                        "billing_id"			=> $billing_id,
                                        "product_id"			=> $produtoID,
                                        "customer_id"			=> $clienteID,
                                        "centro_custo" 		=> $numero,
                                        "valor_unitario"	=> $valUnit,
                                        "qtde_consumo"		=> $qtdConsumo,
                                        "qtde_excedente"	=> 0,
                                        "valor_consumo"		=> $valor_consumido,
                                        "valor_excedente"	=> $excedente,
                                        "valor_total"			=> $excedente,
                                        "valor_unitario_excel" => $valorUnitarioExcel,
                                        "valor_total_excel" => $valorTotalExcel,
                                        "user_updated_id" => CakeSession::read("Auth.User.id")
                                    ]
                                ];
                            }
                        }
                        
                        if ($tem_desconto) {
                            $dados_negativacao = array_merge($dados_negativacao['Negativacao'], ['customer_discount_id' => $desconto[0]['CustomerDiscount']['id']]);
                        }

                        //update billing_quantity
                        $BillingMonthlyPayment->id = $mensalidade['BillingMonthlyPayment']['id'];
                        $data_billing['BillingMonthlyPayment']['billing_quantity'] = $qtdeFaturado;
                        $data_billing['BillingMonthlyPayment']['monthly_value_total'] = $valTotal;

                        $BillingMonthlyPayment->save($data_billing);

                        $Negativacao->create();
                        $Negativacao->save($dados_negativacao);

                        $this->salva_logons($billing_id, $clienteID, $logon, $produtoID, $qtdConsumo);
                    } else {
                        $LinhasNaoImportadas->create();

                        $linha_nao_importada = ['LinhasNaoImportadas' => ['billing_id' => $billing_id, 'product_id' => $produto['Product']['id'], 'logon' => $logon, 'documento' => $numero, 'qtd_consumo' => $qtdConsumo, 'valor_unitario' => $valorUnitarioExcel, 'valor_total' => $valorTotalExcel]];
                        $LinhasNaoImportadas->save($linha_nao_importada);
                    }
                } else {
                    $ProdutosNaoCadastrados->create();

                    $data_nao_cadastrado = ['ProdutosNaoCadastrados' => ['billing_id' => $billing_id, 'name' => trim($nome)]];
                    $ProdutosNaoCadastrados->save($data_nao_cadastrado);
                }
            }
        }
    }

    public function salva_logons($billingID, $customerID, $logon, $produto, $qtdConsumo)
    {

        $NegativacaoLogon = ClassRegistry::init('NegativacaoLogon');
        $hasLogon = $NegativacaoLogon->find('first', [
            'conditions' => [
                "NegativacaoLogon.billing_id" => $billingID,
                "NegativacaoLogon.customer_id" => $customerID,
                "NegativacaoLogon.product_id" => $produto,
                "NegativacaoLogon.logon" => $logon,
            ]
        ]);

        if (empty($hasLogon)) {
            $dados = [
                "NegativacaoLogon" => [
                    "billing_id" => $billingID,
                    "customer_id" => $customerID,
                    "product_id" => $produto,
                    "logon" => $logon,
                    "qtde" => $qtdConsumo,
                ]
            ];

            $NegativacaoLogon->create();
            
        } else {
            $dados = [
                "NegativacaoLogon" => [
                    "id" => $hasLogon['NegativacaoLogon']['id'],
                    "qtde" => $hasLogon['NegativacaoLogon']['qtde'] + $qtdConsumo,
                ]
            ];
        }

        $NegativacaoLogon->save($dados);        
    }

    public function importar_pefin($dados, $billing_id)
    {
        ini_set('max_execution_time', 300);
        $Product = ClassRegistry::init('Product');
        $Customer = ClassRegistry::init('Customer');
        $Pefin = ClassRegistry::init('Pefin');
        $PefinMaintenance = ClassRegistry::init('PefinMaintenance');
        $PlanCustomer = ClassRegistry::init('PlanCustomer');
        $BillingMonthlyPayment = ClassRegistry::init('BillingMonthlyPayment');
        $LoginConsulta = ClassRegistry::init('LoginConsulta');
        $CadastroPefin = ClassRegistry::init('CadastroPefin');

        //deleta negativação anterior
        $update_data = ['Pefin.data_cancel' => "'".date('Y-m-d H:i:s')."'", 'Pefin.usuario_id_cancel' => CakeSession::read("Auth.User.id")];
        $Pefin->updateAll(
            $update_data, //set
            ['Pefin.billing_id' => $billing_id] //where
        );

        foreach ($dados as $data) {
            $planoComposicao = [];
            $dados_pefin = [];
            $data_billing = [];
            $valTotal = 0;
            $valMens = 0;

            $qtdConsumo = $data[0]['qtde'];
            $cod_associado = $data['c']['codigo_associado'];
            $estado = $data['d']['estado'];
            $tipo_pessoa = $data['d']['tipo_pessoa'];

            
            // pessoa fisica
            if ($tipo_pessoa == '2') {
                $produto = $Product->find('first', ['conditions' => ['Product.id' => 460]]);
            } else {
                $produto = $Product->find('first', ['conditions' => ['Product.id' => 461]]);
            }

            $cliente = $Customer->find('first', ['conditions' => ["Customer.codigo_associado" => $cod_associado, "Customer.tipo_credor" => "C", "Customer.faturar" => "S"], 'recursive' => 2]);

            if (!empty($produto)) {
                if (!empty($cliente)) {
                    $produtoID = $produto['Product']['id'];
                    $clienteID = $cliente['Customer']['id'];

                    $clientePlano = $PlanCustomer->find('first', ['conditions' => ['PlanCustomer.status_id' => 1, 'PlanCustomer.customer_id' => $clienteID]]);

                    $mensalidade = $BillingMonthlyPayment->find('first', ['conditions' => ["BillingMonthlyPayment.billing_id" => $billing_id, "BillingMonthlyPayment.customer_id" => $clienteID]]);

                    // se o cliente nao tiver nenhum registro na billing_monthly, cria um registro pra ele
                    if (empty($mensalidade)) {
                        $loginConsulta = $LoginConsulta->find('first', ['conditions' => ['LoginConsulta.status_id' => 1, 'LoginConsulta.customer_id' => $clienteID]]);
                        
                        $manutencao = $PefinMaintenance->find('first');

                        $maintenanceId = $manutencao['PefinMaintenance']['id'];
                        if ($cliente['Customer']['pefin_maintenance'] == 0) {
                            $maintenanceId = 0;
                        }
                        
                        $dados_monthly = [
                        	'BillingMonthlyPayment' => [
                        		'billing_id' => $billing_id,
	                            'pefin_maintenance_id' => $maintenanceId,
	                            'customer_id' => $clienteID,
	                            'login_consulta_id' => !empty($loginConsulta) ? $loginConsulta['LoginConsulta']['id'] : null,
	                            'monthly_value' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
	                            'monthly_value_total' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
	                            'desconto' => $clientePlano['Customer']['desconto'],
	                            'quantity' => $clientePlano['Plan']['type'] == '2' ? '0' : $clientePlano['Plan']['quantity'],
	                            'user_creator_id' => CakeSession::read("Auth.User.id"),
	                            'created' => date('Y-m-d H:i:s')
	                        ]
	                    ];

                        $BillingMonthlyPayment->create();
                        $BillingMonthlyPayment->save($dados_monthly);

                        $mensalidade = $BillingMonthlyPayment->read();
                    }

                    // busca o produto na composicao do plano do cliente
                    $planoComposicao = $PlanCustomer->find_produto_composicao_plano($clienteID, $produtoID);
                    $find_tipo_plano = $PlanCustomer->find_tipo_plano($clienteID);
                    $tipo_plano = $find_tipo_plano[0]['p']['type'];

                    $valMens = $mensalidade['BillingMonthlyPayment']['monthly_value_total'];

                    $qtdeFaturado = $mensalidade['BillingMonthlyPayment']['billing_quantity'];

                    $cliente_tabela_preco = $clientePlano['PlanCustomer']['price_table_id'];
                    $tabela_precos_produto = $produto['ProductPrice'];
                    foreach ($tabela_precos_produto as $key => $tabela_preco) {
                        if ($cliente_tabela_preco == $tabela_preco['price_table_id']) {
                            $valor_consulta = $tabela_preco['value'];
                        }
                    }

                    $valUnit = str_replace(',', '.', $valor_consulta);

                    $qtdePlano = $mensalidade['BillingMonthlyPayment']['quantity'];
                    $dados_excedente = $this->get_qtd_excedente($planoComposicao, $qtdeFaturado, $qtdeFaturado, $qtdConsumo, $qtdePlano);

                    // se o produto nao tiver na composicao do plano, sobra como excedente
                    if (empty($planoComposicao)) {
                        $valTotal	= $valUnit * abs($qtdConsumo);

                        //update billing_quantity
                        $BillingMonthlyPayment->id = $mensalidade['BillingMonthlyPayment']['id'];
                        $data_billing['BillingMonthlyPayment']['billing_quantity'] = $qtdeFaturado+$qtdConsumo;
                        $data_billing['BillingMonthlyPayment']['monthly_value_total'] = $valTotal + $valMens;
                        $BillingMonthlyPayment->save($data_billing);

                        $dados_pefin = ["Pefin" => ["billing_id"			=> $billing_id,
                            "product_id"			=> $produtoID,
                            "customer_id"			=> $clienteID,
                            "qtde_realizado"	=> $qtdConsumo,
                            "qtde_excedente"	=> $qtdConsumo,
                            "valor_unitario"	=> $valUnit,
                            "valor_total" 		=> $valTotal,
                            "user_updated_id" => CakeSession::read("Auth.User.id")]];
                    } else {
                        if ($tipo_plano == 2) {
                            $saldo = $mensalidade['BillingMonthlyPayment']['balance_available'];
                            $valMens = $mensalidade['BillingMonthlyPayment']['monthly_value'];
                            $valConsumed = $mensalidade['BillingMonthlyPayment']['monthly_value_total'];
                            $valor_consumido = $valUnit * abs($qtdConsumo);
                            $valor_consumido = str_replace(',', '.', $valor_consumido);

                            if ($valor_consumido < $saldo) {
                                $saldo_novo = $saldo - $valor_consumido;
                                $valTotal = $valConsumed;
                                $excedente = 0;
                            } else {
                                $saldo_novo = 0;
                                $valTotal = $valConsumed + ($valor_consumido - $saldo);
                                $excedente = $valor_consumido - $saldo;
                            }

                            //update billing_quantity
                            $BillingMonthlyPayment->id = $mensalidade['BillingMonthlyPayment']['id'];
                            $data_billing['BillingMonthlyPayment']['billing_quantity'] = $qtdeFaturado+$qtdConsumo;
                            $data_billing['BillingMonthlyPayment']['balance_available'] = $saldo_novo;
                            $data_billing['BillingMonthlyPayment']['monthly_value_total'] = $valTotal;
                            $BillingMonthlyPayment->save($data_billing);

                            $dados_pefin = ["Pefin" => ["billing_id"			=> $billing_id,
                                "product_id"			=> $produtoID,
                                "customer_id"			=> $clienteID,
                                "qtde_realizado"	=> $qtdConsumo,
                                "qtde_excedente"	=> 0,
                                "valor_unitario"	=> $valUnit,
                                "valor_total" 		=> $excedente,
                                "user_updated_id" => CakeSession::read("Auth.User.id")]];
                        } else {
                            // $valTotal	= $valUnit * abs($qtdConsumo);
                            $qtdeExcedente = $dados_excedente['qtdeExcedente'];
                            $qtdeFaturado = $dados_excedente['qtdeFaturado'];

                            $valMens = $mensalidade['BillingMonthlyPayment']['monthly_value_total'];

                            if ($qtdeExcedente > 0) {
                                $valor_linha	= $valUnit * abs($qtdeExcedente);
                            } else {
                                $valor_linha	= 0;
                            }

                            $valTotal = $valor_linha + $valMens;

                            //update billing_quantity
                            $BillingMonthlyPayment->id = $mensalidade['BillingMonthlyPayment']['id'];
                            $data_billing['BillingMonthlyPayment']['billing_quantity'] = $qtdeFaturado;
                            $data_billing['BillingMonthlyPayment']['monthly_value_total'] = $valTotal;
                            $BillingMonthlyPayment->save($data_billing);

                            $dados_pefin = ["Pefin" => ["billing_id"			=> $billing_id,
                                "product_id"			=> $produtoID,
                                "customer_id"			=> $clienteID,
                                "qtde_realizado"	=> $qtdConsumo,
                                "qtde_excedente"	=> $qtdeExcedente,
                                "valor_unitario"	=> $valUnit,
                                "valor_total" 		=> $valor_linha,
                                "user_updated_id" => CakeSession::read("Auth.User.id")]];
                        }
                    }

                    $Pefin->create();
                    $Pefin->save($dados_pefin);

                    $CadastroPefin->id = $data['d']['id'];
                    // $CadastroPefin->save(['CadastroPefin' => ['faturado' => 1, 'user_updated_id' => CakeSession::read("Auth.User.id")]]);
                }
            }
        }

        $atualizaMensalidade = $BillingMonthlyPayment->update_mensalidade_final($billing_id);

        /*while ($data = fgetcsv($handle,1000,";","'") ) {
            $planoComposicao = [];
            $dados_pefin = [];
            $data_billing = [];
            $valTotal = 0;
            $valMens = 0;
            if(trim($data[1]) != ""){

                $qtdConsumo = str_replace('"', '', $data[0]);
                $cod_associado = str_replace('"', '', $data[1]);
                $estado = str_replace('"', '', $data[3]);
                $tipo_pessoa = str_replace('"', '', $data[4]);

                if ($estado == 'SP') {
                    if ($tipo_pessoa == 'F') {
                        $produto = $Product->find('first', ['conditions' => ['Product.id' => 273]]);
                    } else {
                        $produto = $Product->find('first', ['conditions' => ['Product.id' => 274]]);
                    }
                } else {
                    $produto = $Product->find('first', ['conditions' => ['Product.id' => 118]]);
                }

                $cliente = $Customer->find('first', ['conditions' => ["Customer.codigo_associado" => $cod_associado, "Customer.tipo_credor" => "C"], 'recursive' => 2]);

                if(!empty($produto)){
                    if (!empty($cliente)) {

                        $produtoID = $produto['Product']['id'];
                        $clienteID = $cliente['Customer']['id'];

                        $clientePlano = $PlanCustomer->find('first', ['conditions' => ['PlanCustomer.status_id' => 1, 'PlanCustomer.customer_id' => $clienteID]]);

                        $mensalidade = $BillingMonthlyPayment->find('first', ['conditions' => ["BillingMonthlyPayment.billing_id" => $billing_id, "BillingMonthlyPayment.customer_id" => $clienteID]]);

                        // se o cliente nao tiver nenhum registro na billing_monthly, cria um registro pra ele
                        if (empty($mensalidade)) {
                            $loginConsulta = $LoginConsulta->find('first', ['conditions' => ['LoginConsulta.status_id' => 1, 'LoginConsulta.customer_id' => $clienteID]]);

                            $manutencao = $PefinMaintenance->find('first');

                            $dados_monthly = ['BillingMonthlyPayment' => ['billing_id' => $billing_id,
                                                                                                                        'pefin_maintenance_id' => $manutencao['PefinMaintenance']['id'],
                                                                                                                        'customer_id' => $clienteID,
                                                                                                                        'login_consulta_id' => !empty($loginConsulta) ? $loginConsulta['LoginConsulta']['id'] : null,
                                                                                                                        'monthly_value' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
                                                                                                                        'monthly_value_total' => $clientePlano['PlanCustomer']['mensalidade_nao_formatada'],
                                                                                                                        'desconto' => $clientePlano['Customer']['desconto'],
                                                                                                                        'quantity' => $clientePlano['Plan']['type'] == '2' ? '0' : $clientePlano['Plan']['quantity'],
                                                                                                                        'user_creator_id' => CakeSession::read("Auth.User.id"),
                                                                                                                        'created' => date('Y-m-d H:i:s')
                                                                                                                        ]];

                            $BillingMonthlyPayment->create();
                            $BillingMonthlyPayment->save($dados_monthly);

                            $mensalidade = $BillingMonthlyPayment->read();
                        }

                        // busca o produto na composicao do plano do cliente
                        $planoComposicao = $PlanCustomer->find_produto_composicao_plano($clienteID, $produtoID);
                        $find_tipo_plano = $PlanCustomer->find_tipo_plano($clienteID);
                        $tipo_plano = $find_tipo_plano[0]['p']['type'];

                        $valMens = $mensalidade['BillingMonthlyPayment']['monthly_value_total'];

                        $qtdeFaturado = $mensalidade['BillingMonthlyPayment']['billing_quantity']+$qtdConsumo;

                        $cliente_tabela_preco = $clientePlano['PlanCustomer']['price_table_id'];
                        $tabela_precos_produto = $produto['ProductPrice'];
                        foreach ($tabela_precos_produto as $key => $tabela_preco) {
                            if ($cliente_tabela_preco == $tabela_preco['price_table_id']) {
                                $valor_consulta = $tabela_preco['value'];
                            }
                        }

                        $valUnit = str_replace(',', '.', $valor_consulta);

                        if ($tipo_plano == 2 && !empty($planoComposicao)) {
                            $saldo = $mensalidade['BillingMonthlyPayment']['balance_available'];
                            $valMens = $mensalidade['BillingMonthlyPayment']['monthly_value'];
                            $valConsumed = $mensalidade['BillingMonthlyPayment']['monthly_value_total'];
                            $valor_consumido = $valUnit * abs($qtdConsumo);
                            $valor_consumido = str_replace(',', '.', $valor_consumido);

                            if ($valor_consumido < $saldo) {
                                $saldo_novo = $saldo - $valor_consumido;
                                $valTotal = $valConsumed;
                                $excedente = 0;
                            } else {
                                $saldo_novo = 0;
                                $valTotal = $valConsumed + ($valor_consumido - $saldo);
                                $excedente = $valor_consumido - $saldo;
                            }

                            //update billing_quantity
                            $BillingMonthlyPayment->id = $mensalidade['BillingMonthlyPayment']['id'];
                            $data_billing['BillingMonthlyPayment']['billing_quantity'] = $qtdeFaturado;
                            $data_billing['BillingMonthlyPayment']['balance_available'] = $saldo_novo;
                            $data_billing['BillingMonthlyPayment']['monthly_value_total'] = $valTotal;
                            $BillingMonthlyPayment->save($data_billing);

                            $dados_pefin = ["Pefin" => ["billing_id"			=> $billing_id,
                                                                                    "product_id"			=> $produtoID,
                                                                                    "customer_id"			=> $clienteID,
                                                                                    "qtde_realizado"	=> $qtdConsumo,
                                                                                    "qtde_excedente"	=> 0,
                                                                                    "valor_unitario"	=> $valUnit,
                                                                                    "valor_total" 		=> $excedente,
                                                                                    "user_updated_id" => CakeSession::read("Auth.User.id")]];
                        } else {
                            $valTotal	= $valUnit * abs($qtdConsumo);

                            //update billing_quantity
                            $BillingMonthlyPayment->id = $mensalidade['BillingMonthlyPayment']['id'];
                            $data_billing['BillingMonthlyPayment']['billing_quantity'] = $qtdeFaturado;
                            $data_billing['BillingMonthlyPayment']['monthly_value_total'] = $valTotal + $valMens;
                            $BillingMonthlyPayment->save($data_billing);

                            $dados_pefin = ["Pefin" => ["billing_id"			=> $billing_id,
                                                                                    "product_id"			=> $produtoID,
                                                                                    "customer_id"			=> $clienteID,
                                                                                    "qtde_realizado"	=> $qtdConsumo,
                                                                                    "qtde_excedente"	=> $qtdConsumo,
                                                                                    "valor_unitario"	=> $valUnit,
                                                                                    "valor_total" 		=> $valTotal,
                                                                                    "user_updated_id" => CakeSession::read("Auth.User.id")]];
                        }


                        $Pefin->create();
                        $Pefin->save($dados_pefin);
                    }
                }
            }
        }*/
    }



    public function get_qtd_excedente($planoComposicao, $qtdeFaturado, $qtdeCon, $qtdConsumo, $qtdePlano)
    {
        if (!empty($planoComposicao)) {
            //novo valor billing_quantity
            $qtdeFaturado = $qtdeCon + $qtdConsumo;

            //não atingiu a qtde do plano
            if ($qtdeCon < $qtdePlano) {
                //descobrindo qtde disponivel de consumo do plano
                $saldo_qtde = $qtdePlano - $qtdeCon;
                
                if ($saldo_qtde < $qtdConsumo) {
                    $soma_consumo = $qtdeCon + $qtdConsumo;

                    if ($soma_consumo > $qtdePlano) {
                        //novo valor billing_quantity
                        $qtdeFaturado = $qtdePlano;

                        //excedente
                        $qtdeExcedente = $soma_consumo - $qtdePlano;
                    }
                } else {
                    //excedente
                    $qtdeExcedente = 0;
                }
            } else {
                //excedente
                $qtdeExcedente = $qtdConsumo;
            }
        } else {
            //excedente
            $qtdeExcedente = $qtdConsumo;
        }

        return ['qtdeExcedente' => $qtdeExcedente, 'qtdeFaturado' => $qtdeFaturado];
    }
}

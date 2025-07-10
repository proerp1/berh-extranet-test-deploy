<?php

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExcelTemplate
{
    public function getCnabLotes($spreadsheet, $dados)
    {

        $activeWorksheet = $spreadsheet->getActiveSheet();

        $activeWorksheet->setCellValue('A1', "Status")
            ->setCellValue('B1', "Número da Remessa")
            ->setCellValue('C1', "Data")
            ->setCellValue('D1', "Banco")
            ->setCellValue('E1', "Qtde")
            ->setCellValue('F1', "Total")
            ->setCellValue('G1', "Documento");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {

            $indx++;
            $activeWorksheet->setCellValue('A' . $indx, $dados[$i]["Status"]["name"])
                ->setCellValue('B' . $indx, str_pad($dados[$i]['CnabLote']['remessa'], 6, 0, STR_PAD_LEFT))
                ->setCellValue('C' . $indx, date('d/m/Y H:i:s', strtotime($dados[$i]['CnabLote']['created'])))
                ->setCellValue('D' . $indx, $dados[$i]['Bank']['name'])
                ->setCellValue('E' . $indx, $dados[$i][0]['qtde'])
                ->setCellValue('F' . $indx, number_format($dados[$i][0]['valor_total'], 2, ',', '.'))
                ->setCellValue('G' . $indx, $dados[$i]['CnabLote']['arquivo']);
        }
    }

    public function getBloqueioDiario($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Código do associado")
            ->setCellValue('B1', "Razão social")
            ->setCellValue('C1', "Nome fantasia")
            ->setCellValue('D1', "Status atual")
            ->setCellValue('E1', "Data alteração")
            ->setCellValue('F1', "Status antigo");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Customer"]["codigo_associado"])
                ->setCellValue('B' . $indx, $dados[$i]["Customer"]["nome_primario"])
                ->setCellValue('C' . $indx, $dados[$i]["Customer"]["nome_secundario"])
                ->setCellValue('D' . $indx, $dados[$i]["Customer"]["Status"]["name"])
                ->setCellValue('E' . $indx, date('d/m/Y H:i:s', strtotime($dados[$i]["MovimentacaoCredor"]["created"])))
                ->setCellValue('F' . $indx, $dados[$i]["Status"]["name"]);
        }
    }

    public function getClientesBloquear($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Código associado")
            ->setCellValue('C1', "Nome")
            ->setCellValue('D1', "Email")
            ->setCellValue('E1', "Data de cadastro");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Customer"]["Status"]["name"])
                ->setCellValue('B' . $indx, $dados[$i]["Customer"]["codigo_associado"])
                ->setCellValue('C' . $indx, $dados[$i]["Customer"]["nome_secundario"])
                ->setCellValue('D' . $indx, $dados[$i]["Customer"]["email"])
                ->setCellValue('E' . $indx, $dados[$i]["Customer"]["created"]);
        }
    }

    public function getClientes($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Data cadastro")
            ->setCellValue('C1', "Código")
            ->setCellValue('D1', "Nome fantasia")
            ->setCellValue('E1', "Cidade")
            ->setCellValue('F1', "UF")
            ->setCellValue('G1', "Plano")
            ->setCellValue('H1', "Valor")
            ->setCellValue('I1', "Data Plano")
            ->setCellValue('J1', "Vendedor")
            ->setCellValue('K1', "Data Cancelamento");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Statuses"]["name"])
                ->setCellValue('B' . $indx, date('d/m/Y', strtotime($dados[$i]["Customer"]["created"])))
                ->setCellValue('C' . $indx, $dados[$i]["Customer"]["codigo_associado"])
                ->setCellValue('D' . $indx, $dados[$i]["Customer"]["nome_secundario"])
                ->setCellValue('E' . $indx, $dados[$i]["Customer"]["cidade"])
                ->setCellValue('F' . $indx, $dados[$i]["Customer"]["estado"])
                ->setCellValue('G' . $indx, $dados[$i]['Plan']['description'])
                ->setCellValue('H' . $indx, $dados[$i]['PlanCustomer']['mensalidade'])
                ->setCellValue('I' . $indx, ($dados[$i]["PlanCustomer"]["created"] ? date('d/m/Y', strtotime($dados[$i]["PlanCustomer"]["created"])) : ''))
                ->setCellValue('J' . $indx, $dados[$i]["Seller"]["nome_fantasia"])
                ->setCellValue('K' . $indx, ($dados[$i][0]["dataCancelamento"] ? date('d/m/Y', strtotime($dados[$i][0]["dataCancelamento"])) : ''));
        }
    }

    public function getDiarioCobranca($objPHPExcel, $dados, $valor_cobrado, $exito)
    {
        $DistribuicaoCobranca = ClassRegistry::init('DistribuicaoCobranca');

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Cobrador")
            ->setCellValue('B1', "Quantidade")
            ->setCellValue('C1', "Valor Cobrado")
            ->setCellValue('D1', "Total de Cobranças Realizadas")
            ->setCellValue('E1', "Total Cobrado")
            ->setCellValue('F1', "Total Recebido");

        $valor_total = 0;
        $qtde_total_exito = 0;
        $valor_total_exito = 0;
        $total_pago = 0;
        $qtde_total = 0;
        $indx = 1;
        foreach ($dados['QtdeUsuarios'] as $user) {

            $valor_total += $valor_cobrado[$user['user_id']][0][0]['total'];
            $qtde_total_exito += $exito[$user['user_id']][0][0]["qtde"];
            $valor_total_exito += $exito[$user['user_id']][0][0]["valor_total"];
            $total_pago += $exito[$user['user_id']][0][0]["valor_total_pago"];
            $qtde_total += $user['QtdeUsuarios'][0]['total_clientes'];

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $user['User']['name'])
                ->setCellValue('B' . $indx, $user['QtdeUsuarios'][0]['total_clientes'])
                ->setCellValue('C' . $indx, number_format($valor_cobrado[$user['user_id']][0][0]['total'], 2, ',', '.'))
                ->setCellValue('D' . $indx, $exito[$user['user_id']][0][0]['qtde'])
                ->setCellValue('E' . $indx, number_format($exito[$user['user_id']][0][0]['valor_total'], 2, ',', '.'))
                ->setCellValue('F' . $indx, number_format($exito[$user['user_id']][0][0]['valor_total_pago'], 2, ',', '.'));
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $indx, "Total:")
            ->setCellValue('B' . $indx, $qtde_total)
            ->setCellValue('C' . $indx, number_format($valor_total, 2, ',', '.'))
            ->setCellValue('D' . $indx, $qtde_total_exito)
            ->setCellValue('E' . $indx, number_format($valor_total_exito, 2, ',', '.'))
            ->setCellValue('F' . $indx, number_format($total_pago, 2, ',', '.'));
    }

    public function getContasReceber($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Descrição da conta")
            ->setCellValue('B1', "Status")
            ->setCellValue('C1', "Número do documento")
            ->setCellValue('D1', "Código")
            ->setCellValue('E1', "Cliente")
            ->setCellValue('F1', "Valor bruto")
            ->setCellValue('G1', "Valor multa")
            ->setCellValue('H1', "Valor liquido")
            ->setCellValue('I1', "Valor pago")
            ->setCellValue('J1', "Conta bancária")
            ->setCellValue('K1', "Competência")
            ->setCellValue('L1', "Vencimento")
            ->setCellValue('M1', "Data de criação")
            ->setCellValue('N1', "Receita")
            ->setCellValue('O1', "Centro de custo")
            ->setCellValue('P1', "Observações")
            ->setCellValue('Q1', "Data Pagamento")
            ->setCellValue('R1', "Pedido");



        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['Income']['name'])
                ->setCellValue('B' . $indx, $dados[$i]['Status']['name'])
                ->setCellValue('C' . $indx, "'" . $dados[$i]['Income']['doc_num'] . "'")
                ->setCellValue('D' . $indx, $dados[$i]['Customer']['codigo_associado'])
                ->setCellValue('E' . $indx, $dados[$i]['Customer']['nome_secundario'])
                ->setCellValue('F' . $indx, $dados[$i]['Income']['valor_bruto'])
                ->setCellValue('G' . $indx, $dados[$i]['Income']['valor_multa'])
                ->setCellValue('H' . $indx, $dados[$i]['Income']['valor_total'])
                ->setCellValue('I' . $indx, $dados[$i]['Income']['valor_pago'])
                ->setCellValue('J' . $indx, $dados[$i]['BankAccount']['name'])
                ->setCellValue('K' . $indx, $dados[$i]['Income']['data_competencia'])
                ->setCellValue('L' . $indx, $dados[$i]['Income']['vencimento'])
                ->setCellValue('M' . $indx, date('d/m/Y H:i:s', strtotime($dados[$i]['Income']['created_nao_formatado'])))
                ->setCellValue('N' . $indx, $dados[$i]['Revenue']['name'])
                ->setCellValue('O' . $indx, $dados[$i]['CostCenter']['name'])
                ->setCellValue('P' . $indx, $dados[$i]['Income']['observation'])
                ->setCellValue('Q' . $indx, $dados[$i]['Income']['data_pagamento'])
                ->setCellValue('R' . $indx, $dados[$i]['Order']['id']);



        }
    }

    public function getOutcome($objPHPExcel, $dados)
    {
        $paymentMethods = ['1' => 'Boleto','3' => 'Cartão de crédito','6' => 'Crédito em conta corrente','5' => 'Cheque','4' => 'Depósito','7' => 'Débito em conta','8' => 'Dinheiro','2' => 'Transferência','9' => 'Desconto','11' => 'Pix','10' => 'Outros'];

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "N° Documento")
            ->setCellValue('B1', "Fornecedor")
            ->setCellValue('C1', "Nome")        
            ->setCellValue('D1', "Descrição")
            ->setCellValue('E1', "Status")
            ->setCellValue('F1', "Conta bancária")
            ->setCellValue('G1', "Vencimento")
            ->setCellValue('H1', "Data de criação")
            ->setCellValue('I1', "Parcela")
            ->setCellValue('J1', "Valor a pagar")
            ->setCellValue('K1', "Data pagamento")
            ->setCellValue('L1', "Valor pago")
            ->setCellValue('M1', "Observação")
            ->setCellValue('N1', "Tipo Conta")
            ->setCellValue('O1', "Banco")
            ->setCellValue('P1', "Forma de pagamento")
            ->setCellValue('Q1', "Agência")
            ->setCellValue('R1', "Digito")
            ->setCellValue('S1', "Conta")
            ->setCellValue('T1', "Digito")
            ->setCellValue('U1', "Tipo Chave")
            ->setCellValue('V1', "Chave PIX")
            ->setCellValue('W1', "Registro Cobrança");
            
          

            $indx = 1;
            for ($i = 0; $i < count($dados); $i++) {
                $indx++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indx, $dados[$i]["Outcome"]["doc_num"] ?? '')
                    ->setCellValue('B' . $indx, $dados[$i]["Outcome"]["supplier_id"] ?? '')
                    ->setCellValue('C' . $indx, $dados[$i]["Supplier"]["nome_fantasia"] ?? '')
                    ->setCellValue('D' . $indx, $dados[$i]["Outcome"]["name"] ?? '')
                    ->setCellValue('E' . $indx, $dados[$i]['Status']['name'] ?? '')
                    ->setCellValue('F' . $indx, $dados[$i]["BankAccount"]["name"] ?? '')
                    ->setCellValue('G' . $indx, $dados[$i]["Outcome"]["vencimento"] ?? '')
                    ->setCellValue('H' . $indx, $dados[$i]["Outcome"]["created"] ?? '')
                    ->setCellValue('I' . $indx, ($dados[$i]["Outcome"]["parcela"] ?? '') . 'ª')
                    ->setCellValue('J' . $indx, $dados[$i]["Outcome"]["valor_total"] ?? '')
                    ->setCellValue('K' . $indx, $dados[$i]["Outcome"]["data_pagamento"] ?? '')
                    ->setCellValue('L' . $indx, $dados[$i]["Outcome"]["valor_pago"] ?? '')
                    ->setCellValue('M' . $indx, $dados[$i]["Outcome"]["observation"] ?? '')
                    ->setCellValue('N' . $indx, $dados[$i]['BankAccountType']['description'] ?? '')
                    ->setCellValue('O' . $indx, $dados[$i]['BankCode']['name'] ?? '');
            
                $paymentMethodId = $dados[$i]['Supplier']['payment_method'] ?? null;
                $paymentMethodName = isset($paymentMethods[$paymentMethodId]) ? $paymentMethods[$paymentMethodId] : 'Não informado';
            
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('P' . $indx, $paymentMethodName)
                    ->setCellValue('Q' . $indx, $dados[$i]['Supplier']['branch_number'] ?? '')
                    ->setCellValue('R' . $indx, $dados[$i]['Supplier']['branch_digit'] ?? '')
                    ->setCellValue('S' . $indx, $dados[$i]['Supplier']['acc_number'] ?? '')
                    ->setCellValue('T' . $indx, $dados[$i]['Supplier']['acc_digit'] ?? '')
                    ->setCellValue('U' . $indx, $dados[$i]['Supplier']['pix_type'] ?? '')
                    ->setCellValue('V' . $indx, $dados[$i]['Supplier']['pix_id'] ?? '')
                    ->setCellValue('X' . $indx, ($dados[$i]['Supplier']['valor'] ?? '') . ' ' . ($dados[$i]['Supplier']['unidade_tempo'] ?? ''));

            }
            
    }

    public function getFluxo($objPHPExcel, $dados, $conta)
{
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', "Status")
        ->setCellValue('B1', "Conta bancária")
        ->setCellValue('C1', "Código/Id")
        ->setCellValue('D1', "Cliente")
        ->setCellValue('E1', "Fornecedor")
        ->setCellValue('F1', "N° Pedido")
        ->setCellValue('G1', "Data")
        ->setCellValue('H1', "Valor")
        ->setCellValue('I1', "Saldo");

    $indx = 2;
    $saldo = 0;
    if (!empty($conta)) {
        $saldo = $conta['BankAccount']['initial_balance_not_formated'];

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $indx . ':F' . $indx);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $indx, $conta['BankAccount']['name'])
            ->setCellValue('I' . $indx, $conta['BankAccount']['initial_balance']);
    }

    for ($i = 0; $i < count($dados); $i++) {
        $saldo = $dados[$i][0]['operador'] == '+' ? $saldo + $dados[$i][0]['valor_total'] : $saldo - $dados[$i][0]['valor_total'];

        // Use the absolute value to remove '-' sign from saldo
        $valor_total = abs($dados[$i][0]['valor_total']);
        $saldo_abs = abs($saldo); // Absolute value of saldo

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $indx, $dados[$i][0]['status'])
            ->setCellValue('B' . $indx, $dados[$i][0]['name'])
            ->setCellValue('C' . $indx, $dados[$i][0]['codigo'])
            ->setCellValue('D' . $indx, $dados[$i][0]['customer_nome_secundario'])
            ->setCellValue('E' . $indx, $dados[$i][0]['supplier_nome_fantasia'])
            ->setCellValue('F' . $indx, $dados[$i][0]['order_id'])
            ->setCellValue('G' . $indx, date('d/m/Y', strtotime($dados[$i][0]['data_pagamento'])))
            ->setCellValue('H' . $indx, number_format($valor_total, 2, ',', '.'))
            ->setCellValue('I' . $indx, number_format($saldo_abs, 2, ',', '.'));
    }

    $indx++;
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $indx . ':F' . $indx);
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A' . $indx, 'Total:')
        ->setCellValue('I' . $indx, number_format($saldo, 2, ',', '.'));
}


    public function getDespesas($objPHPExcel, $dados, $conta)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Conta bancária")
            ->setCellValue('C1', "Data")
            ->setCellValue('D1', "Valor")
            ->setCellValue('E1', "Saldo");

        $indx = 2;
        $saldo = 0;
        if (!empty($conta)) {
            $saldo = $conta['BankAccount']['initial_balance_not_formated'];

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $indx . ':D' . $indx);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $conta['BankAccount']['name'])
                ->setCellValue('E' . $indx, $conta['BankAccount']['initial_balance']);
        }

        for ($i = 0; $i < count($dados); $i++) {
            $saldo = $dados[$i][0]['operador'] == '+' ? $saldo + $dados[$i]['o']['valor_total'] : $saldo - $dados[$i]['o']['valor_total'];

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['s']['status'])
                ->setCellValue('B' . $indx, $dados[$i]['b']['name'])
                ->setCellValue('C' . $indx, date('d/m/Y', strtotime($dados[$i]['o']['vencimento'])))
                ->setCellValue('D' . $indx, $dados[$i][0]['operador'] . ' ' . number_format($dados[$i]['o']['valor_total'], 2, ',', '.'))
                ->setCellValue('E' . $indx, number_format($saldo, 2, ',', '.'));
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $indx . ':D' . $indx);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $indx, 'Total:')
            ->setCellValue('E' . $indx, number_format($saldo, 2, ',', '.'));
    }

    public function getReceitas($objPHPExcel, $dados, $conta)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Conta bancária")
            ->setCellValue('C1', "Data")
            ->setCellValue('D1', "Valor")
            ->setCellValue('E1', "Saldo");

        $indx = 2;
        $saldo = 0;
        if (!empty($conta)) {
            $saldo = $conta['BankAccount']['initial_balance'];

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $indx . ':D' . $indx);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $conta['BankAccount']['name'])
                ->setCellValue('E' . $indx, $conta['BankAccount']['initial_balance_formated']);
        }

        for ($i = 0; $i < count($dados); $i++) {
            $saldo = $dados[$i][0]['operador'] == '+' ? $saldo + $dados[$i]['i']['valor_total'] : $saldo - $dados[$i]['i']['valor_total'];

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['s']['status'])
                ->setCellValue('B' . $indx, $dados[$i]['b']['name'])
                ->setCellValue('C' . $indx, date('d/m/Y', strtotime($dados[$i]['i']['vencimento'])))
                ->setCellValue('D' . $indx, $dados[$i][0]['operador'] . ' ' . number_format($dados[$i]['i']['valor_total'], 2, ',', '.'))
                ->setCellValue('E' . $indx, number_format($saldo, 2, ',', '.'));
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $indx . ':D' . $indx);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $indx, 'Total:')
            ->setCellValue('E' . $indx, number_format($saldo, 2, ',', '.'));
    }

    public function getCustomersTemplate($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Período : " . $dados['periodo'])
            ->setCellValue('A2', "Minimo Consultas : " . $dados['min_consulta'])
            ->setCellValue('A3', "Produtos")
            ->setCellValue('A4', "Nome")
            ->setCellValue('B4', "Consultas Realizadas")
            ->setCellValue('C4', "Valor Unitário")
            ->setCellValue('D4', "Total")
            ->setCellValue('A5', "Mensalidade")
            ->setCellValue('D5',  $dados['mensalidade']);

        $indx = 5;
        $total = 0;

        for ($i = 0; $i < count($dados['negativacao']); $i++) {
            $total += $dados['negativacao'][$i]['n']['valor_total'];
            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados['negativacao'][$i]['p']['name'])
                ->setCellValue('B' . $indx, $dados['negativacao'][$i]['n']['qtde_consumo'])
                ->setCellValue('C' . $indx,  number_format($dados['negativacao'][$i]['n']['valor_unitario'], 2, ',', '.'))
                ->setCellValue('D' . $indx,  number_format($dados['negativacao'][$i]['n']['valor_total'], 2, ',', '.'));
        }

        for ($i = 0; $i < count($dados['pefin']); $i++) {
            $total += $dados['pefin'][$i]['n']['valor_total'];

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados['pefin'][$i]['p']['name'])
                ->setCellValue('B' . $indx, $dados['pefin'][$i]['n']['qtde_realizado'])
                ->setCellValue('C' . $indx,  number_format($dados['pefin'][$i]['n']['valor_unitario'], 2, ',', '.'))
                ->setCellValue('D' . $indx,  number_format($dados['pefin'][$i]['n']['valor_total'], 2, ',', '.'));
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $indx, "Manutenção PEFIN:")
            ->setCellValue('D' . $indx,  $dados['manutencao']);
        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C' . $indx, "Total Excedente:")
            ->setCellValue('D' . $indx,  number_format($total, 2, ',', '.'));
        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C' . $indx, "Total Fatura:")
            ->setCellValue('D' . $indx,  number_format($dados['mensalidade'] + $total + $dados['manutencao'], 2, ',', '.'));
    }

    public function getRetornoCnab($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Processado")
            ->setCellValue('B1', "Encontrado")
            ->setCellValue('C1', "Status do Cliente")
            ->setCellValue('D1', "Cliente")
            ->setCellValue('E1', "Documento")
            ->setCellValue('F1', "Vencimento")
            ->setCellValue('G1', "Valor pago")
            ->setCellValue('H1', "Valor liquido");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, ($dados[$i]["TmpRetornoCnab"]["processado"] == 1 ? 'Sim' : 'Não'))
                ->setCellValue('B' . $indx, ($dados[$i]["TmpRetornoCnab"]["encontrado"] == 1 ? 'Sim' : 'Não'))
                ->setCellValue('C' . $indx, ($dados[$i]["TmpRetornoCnab"]["encontrado"] == 1 ? $dados[$i]["Income"]["Customer"]['Status']['name'] : ''))
                ->setCellValue('D' . $indx, ($dados[$i]["TmpRetornoCnab"]["encontrado"] == 1 ? $dados[$i]["Income"]["Customer"]['codigo_associado'] . ' - ' . $dados[$i]["Income"]["Customer"]['nome_primario'] : ''))
                ->setCellValue('E' . $indx, " " . $dados[$i]["TmpRetornoCnab"]["nosso_numero"])
                ->setCellValue('F' . $indx, date('d/m/Y', strtotime($dados[$i]["TmpRetornoCnab"]["vencimento"])))
                ->setCellValue('G' . $indx, number_format($dados[$i]["TmpRetornoCnab"]["valor_pago"], 2, ',', '.'))
                ->setCellValue('H' . $indx, number_format($dados[$i]["TmpRetornoCnab"]["valor_liquido"], 2, ',', '.'));
        }
    }

    public function getTwwRelatorio($objPHPExcel, $dados, $grupo)
    {
        $indx = 0;
        for ($i = 0; $i < count($dados); $i++) {

            // hack para funcionar como o ramon pediu
            $celular = '';
            if ($dados[$i]['Customer']['celular'] != '') {
                $celular = $dados[$i]['Customer']['celular'];

                $indx++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indx, $grupo)
                    ->setCellValue('B' . $indx, $dados[$i]['Customer']['nome_primario'] . ' - ' . $dados[$i]['Customer']['nome_secundario'])
                    ->setCellValue('C' . $indx, str_replace(array(" ", "-", "(", ")"), "", $celular))
                    ->setCellValue('D' . $indx, $dados[$i]['Customer']['email'])
                    ->setCellValue('E' . $indx, $dados[$i]['Customer']['codigo_associado']);
            }
            if ($dados[$i]['Customer']['celular1'] != '') {
                $celular = $dados[$i]['Customer']['celular1'];

                $indx++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indx, $grupo)
                    ->setCellValue('B' . $indx, $dados[$i]['Customer']['nome_primario'] . ' - ' . $dados[$i]['Customer']['nome_secundario'])
                    ->setCellValue('C' . $indx, str_replace(array(" ", "-", "(", ")"), "", $celular))
                    ->setCellValue('D' . $indx, $dados[$i]['Customer']['email'])
                    ->setCellValue('E' . $indx, $dados[$i]['Customer']['codigo_associado']);
            }
            if ($dados[$i]['Customer']['celular2'] != '') {
                $celular = $dados[$i]['Customer']['celular2'];

                $indx++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indx, $grupo)
                    ->setCellValue('B' . $indx, $dados[$i]['Customer']['nome_primario'] . ' - ' . $dados[$i]['Customer']['nome_secundario'])
                    ->setCellValue('C' . $indx, str_replace(array(" ", "-", "(", ")"), "", $celular))
                    ->setCellValue('D' . $indx, $dados[$i]['Customer']['email'])
                    ->setCellValue('E' . $indx, $dados[$i]['Customer']['codigo_associado']);
            }
            if ($dados[$i]['Customer']['celular3'] != '') {
                $celular = $dados[$i]['Customer']['celular3'];

                $indx++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indx, $grupo)
                    ->setCellValue('B' . $indx, $dados[$i]['Customer']['nome_primario'] . ' - ' . $dados[$i]['Customer']['nome_secundario'])
                    ->setCellValue('C' . $indx, str_replace(array(" ", "-", "(", ")"), "", $celular))
                    ->setCellValue('D' . $indx, $dados[$i]['Customer']['email'])
                    ->setCellValue('E' . $indx, $dados[$i]['Customer']['codigo_associado']);
            }
            if ($dados[$i]['Customer']['celular4'] != '') {
                $celular = $dados[$i]['Customer']['celular4'];

                $indx++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indx, $grupo)
                    ->setCellValue('B' . $indx, $dados[$i]['Customer']['nome_primario'] . ' - ' . $dados[$i]['Customer']['nome_secundario'])
                    ->setCellValue('C' . $indx, str_replace(array(" ", "-", "(", ")"), "", $celular))
                    ->setCellValue('D' . $indx, $dados[$i]['Customer']['email'])
                    ->setCellValue('E' . $indx, $dados[$i]['Customer']['codigo_associado']);
            }
            if ($dados[$i]['Customer']['celular5'] != '') {
                $celular = $dados[$i]['Customer']['celular5'];

                $indx++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indx, $grupo)
                    ->setCellValue('B' . $indx, $dados[$i]['Customer']['nome_primario'] . ' - ' . $dados[$i]['Customer']['nome_secundario'])
                    ->setCellValue('C' . $indx, str_replace(array(" ", "-", "(", ")"), "", $celular))
                    ->setCellValue('D' . $indx, $dados[$i]['Customer']['email'])
                    ->setCellValue('E' . $indx, $dados[$i]['Customer']['codigo_associado']);
            }
        }
    }

    public function getClientesRelatorio($objPHPExcel, $dados)
    {

        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Código"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Situação"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data de cadastro"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Vencimento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Revenda"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Executivo"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Taxa (%)"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo de pessoa"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CNPJ"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Razão social"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Nome nome_fantasia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "IE"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CPF Responsável"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Responsável"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CEP"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Endereço"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Complemento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cidade"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Bairro"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Estado"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Telefone 1"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Ramal"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Telefone 2"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "E-mail Principal"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "E-mail"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Operadora"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Celular"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Juros e multa?"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Enviar email?"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cobrar taxa do boleto?"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Emitir Nota Fiscal"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Exibir Demanda"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Elegível para gestão econômico"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Margem de segurança"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Incluir qtde. mínina diária"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "PGE*FeeGestao"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "TPP"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipos de GE"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Observações");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CEP Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Endereço Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Complemento Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cidade Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Bairro Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Estado Entrega"); $col++;

        foreach ($dados as $key => $dado) {
            $col = 'A';

            $porcentagem_margem_seguranca = $dado['Customer']['porcentagem_margem_seguranca'];
            if (empty($porcentagem_margem_seguranca)) {
                $porcentagem_margem_seguranca = "0";
            }

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['codigo_associado']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Status']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['created']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['vencimento']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Resale']['nome_fantasia']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Seller']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['commission_fee_percentage']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['tipo_pessoa'] == '2' ? 'Jurídica' : 'Física'); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['documento']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['nome_primario']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['nome_secundario']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['ie']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['cpf_responsavel']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['responsavel']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['cep']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['endereco']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['numero']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['complemento']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['cidade']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['bairro']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['estado']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['telefone1']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['ramal']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['telefone2']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['email']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['email1']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['operadora']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['celular']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['cobrar_juros']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['enviar_email'] ? 'S' : 'N'); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['cobrar_taxa_boleto'] ? 'S' : 'N'); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['emitir_nota_fiscal']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['exibir_demanda'] ? 'S' : 'N'); $col++;
            $isGestao = strtoupper(trim($dado['Customer']['flag_gestao_economico'])) === 'S' ? 'S' : 'N';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $isGestao); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $porcentagem_margem_seguranca); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['qtde_minina_diaria'] == 2 ? 'Sim' : 'Não'); $col++;
            $proposal = isset($dado['Proposal'][0]) ? $dado['Proposal'][0] : null;
            if ($proposal) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $proposal['management_feel']);
                $col++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $proposal['tpp']);
                $col++;
            } else {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), "N/A");
                $col++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), "N/A");
                $col++;
            }
            switch ($dado['Customer']['tipo_ge']) {case '1':$tipoGeNome = 'pré';break;case '2':$tipoGeNome = 'pós';break;case '3':$tipoGeNome = 'garantido';break;      }
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $tipoGeNome);
            $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['cepentrega']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['enderecoentrega']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['numeroentrega']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['complementoentrega']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['cidadeentrega']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['bairroentrega']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['estadoentrega']); $col++;
        }
    }

    public function getFornecedoresRelatorio($objPHPExcel, $dados_sup, $dados_log)
    {
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Fornecedores");

        $paymentMethods = ['1' => 'Boleto','3' => 'Cartão de crédito','6' => 'Crédito em conta corrente','5' => 'Cheque','4' => 'Depósito','7' => 'Débito em conta','8' => 'Dinheiro','2' => 'Transferência','9' => 'Desconto','11' => 'Pix','10' => 'Outros'];

        $col = 'A';
        $sheet->setCellValue($col.'1', "Status"); $col++;
        $sheet->setCellValue($col.'1', "Tipo de pessoa "); $col++;
        $sheet->setCellValue($col.'1', "ID"); $col++;
        $sheet->setCellValue($col.'1', "Razão social"); $col++;
        $sheet->setCellValue($col.'1', "Nome Fantasia"); $col++;
        $sheet->setCellValue($col.'1', "Repasse"); $col++;
        $sheet->setCellValue($col.'1', "CNPJ"); $col++;
        $sheet->setCellValue($col.'1', "Rg"); $col++;
        $sheet->setCellValue($col.'1', "Inscrição Estadual"); $col++;
        $sheet->setCellValue($col.'1', "Inscrição Municipal"); $col++;
        $sheet->setCellValue($col.'1', "Contato"); $col++;
        $sheet->setCellValue($col.'1', "CEP"); $col++;
        $sheet->setCellValue($col.'1', "Endereço"); $col++;
        $sheet->setCellValue($col.'1', "Número"); $col++;
        $sheet->setCellValue($col.'1', "Complemento"); $col++;
        $sheet->setCellValue($col.'1', "Bairro"); $col++;
        $sheet->setCellValue($col.'1', "Cidade"); $col++;
        $sheet->setCellValue($col.'1', "Estado"); $col++;
        $sheet->setCellValue($col.'1', "Endereço Faturamento");$col++;
        $sheet->setCellValue($col.'1', "Numero");$col++; 
        $sheet->setCellValue($col.'1', "Telefone comercial"); $col++;
        $sheet->setCellValue($col.'1', "Telefone residencial"); $col++;
        $sheet->setCellValue($col.'1', "Operadora"); $col++;
        $sheet->setCellValue($col.'1', "Celular"); $col++;
        $sheet->setCellValue($col.'1', "E-mail"); $col++;
        $sheet->setCellValue($col.'1', "Site"); $col++;
        $sheet->setCellValue($col.'1', "Url"); $col++;
        $sheet->setCellValue($col.'1', "Login"); $col++;
        $sheet->setCellValue($col.'1', "Senha"); $col++;
        $sheet->setCellValue($col.'1', "Tipo Conta "); $col++;
        $sheet->setCellValue($col.'1', "Banco"); $col++;
        $sheet->setCellValue($col.'1', "Forma de pagamento"); $col++;
        $sheet->setCellValue($col.'1', "Agência"); $col++;
        $sheet->setCellValue($col.'1', "Digito"); $col++;
        $sheet->setCellValue($col.'1', "Conta"); $col++;
        $sheet->setCellValue($col.'1', "Digito"); $col++;
        $sheet->setCellValue($col.'1', "Tipo Chave"); $col++;
        $sheet->setCellValue($col.'1', "Chave PIX"); $col++;
        $sheet->setCellValue($col.'1', "Valor Boleto"); $col++;
        $sheet->setCellValue($col.'1', "Valor 1° Via"); $col++;
        $sheet->setCellValue($col.'1', "Valor 2° Via"); $col++;
        $sheet->setCellValue($col.'1', "Tecnologia"); $col++;
        $sheet->setCellValue($col.'1', "Modalidade"); $col++; 
        $sheet->setCellValue($col.'1', "Região");$col++; 
        $sheet->setCellValue($col.'1', "Observação");$col++; 
        $sheet->setCellValue($col.'1', "Realiza GE");$col++; 
        $sheet->setCellValue($col.'1', "Quantidade de Tempo");$col++; 
       

        foreach ($dados_sup as $key => $dado) {

            $regioes = [
                '1' => 'Norte',
                '2' => 'Nordeste',
                '3' => 'Centro-Oeste',
                '4' => 'Sudeste',
                '5' => 'Sul'
            ];
            
            $regiao = $dado['Supplier']['regioes'];
            
            $nomeRegiao = isset($regioes[$regiao]) ? $regioes[$regiao] : '';

            $col = 'A';
            $sheet->setCellValue('A' . ($key + 2), $dado['Status']['name']); $col++;
            $sheet->setCellValue('B' . ($key + 2), ($dado['Supplier']['tipo_pessoa'] == 1 ? 'Fisica' : 'Juridica')); $col++;
            $sheet->setCellValue('C' . ($key + 2), $dado['Supplier']['id']); $col++;
            $sheet->setCellValue('D' . ($key + 2), $dado['Supplier']['razao_social']); $col++;
            $sheet->setCellValue('E' . ($key + 2), $dado['Supplier']['nome_fantasia']); $col++;
            $sheet->setCellValue('F' . ($key + 2), $dado['Supplier']['transfer_fee_percentage']); $col++;
            $sheet->setCellValue('G' . ($key + 2), $dado['Supplier']['documento']); $col++;
            $sheet->setCellValue('H' . ($key + 2), $dado['Supplier']['rg']); $col++;
            $sheet->setCellValue('I' . ($key + 2), $dado['Supplier']['inscricao_estadual']); $col++;
            $sheet->setCellValue('J' . ($key + 2), $dado['Supplier']['inscricao_municipal']); $col++;
            $sheet->setCellValue('K' . ($key + 2), $dado['Supplier']['contato']); $col++;
            $sheet->setCellValue('L' . ($key + 2), $dado['Supplier']['cep']); $col++;
            $sheet->setCellValue('M' . ($key + 2), $dado['Supplier']['endereco']); $col++;
            $sheet->setCellValue('N' . ($key + 2), $dado['Supplier']['numero']); $col++;
            $sheet->setCellValue('O' . ($key + 2), $dado['Supplier']['complemento']); $col++;
            $sheet->setCellValue('P' . ($key + 2), $dado['Supplier']['bairro']); $col++;
            $sheet->setCellValue('Q' . ($key + 2), $dado['Supplier']['cidade']); $col++;
            $sheet->setCellValue('R' . ($key + 2), $dado['Supplier']['estado']); $col++;
            $sheet->setCellValue('S' . ($key + 2), $dado['Supplier']['enderecofaturamento']); $col++;
            $sheet->setCellValue('T' . ($key + 2), $dado['Supplier']['numerofaturamento']); $col++;
            $sheet->setCellValue('U' . ($key + 2), $dado['Supplier']['tel_comercial']); $col++;
            $sheet->setCellValue('V' . ($key + 2), $dado['Supplier']['tel_residencial']); $col++;
            $sheet->setCellValue('W' . ($key + 2), $dado['Supplier']['operadora']); $col++;
            $sheet->setCellValue('X' . ($key + 2), $dado['Supplier']['celular']); $col++;
            $sheet->setCellValue('Y' . ($key + 2), $dado['Supplier']['email']); $col++;
            $sheet->setCellValue('Z' . ($key + 2), $dado['Supplier']['site']); $col++;
            $sheet->setCellValue('AA' . ($key + 2), $dado['Supplier']['url']); $col++;
            $sheet->setCellValue('AB' . ($key + 2), $dado['Supplier']['login']); $col++;
            $sheet->setCellValue('AC' . ($key + 2), $dado['Supplier']['senha']); $col++;
            $sheet->setCellValue('AD' . ($key + 2), $dado['BankAccountType']['description']); $col++;
            $sheet->setCellValue('AE' . ($key + 2), $dado['BankCode']['code']." - ".$dado['BankCode']['name']);$col++;
            
            $paymentMethodId = $dado['Supplier']['payment_method'];
            $paymentMethodName = isset($paymentMethods[$paymentMethodId]) ? $paymentMethods[$paymentMethodId] : 'Não informado';
            $sheet->setCellValue('AF' . ($key + 2), $paymentMethodName); $col++;

            $sheet->setCellValue('AG' . ($key + 2), $dado['Supplier']['branch_number']); $col++;
            $sheet->setCellValue('AH' . ($key + 2), $dado['Supplier']['branch_digit']); $col++;
            $sheet->setCellValue('AI' . ($key + 2), $dado['Supplier']['acc_number']); $col++;
            $sheet->setCellValue('AJ' . ($key + 2), $dado['Supplier']['acc_digit']); $col++;
            $sheet->setCellValue('AK' . ($key + 2), $dado['Supplier']['pix_type']); $col++;
            $sheet->setCellValue('AL' . ($key + 2), $dado['Supplier']['pix_id']);
            $sheet->setCellValue('AM' . ($key + 2), $dado['Supplier']['valor_boleto']);
            $sheet->setCellValue('AN' . ($key + 2), $dado['Supplier']['valor_1_via']);
            $sheet->setCellValue('AO' . ($key + 2), $dado['Supplier']['valor_2_via']);
            $sheet->setCellValue('AP' . ($key + 2), $dado['Tecnologia']['name']);
            $sheet->setCellValue('AQ' . ($key + 2), $dado['Modalidade']['name']);
            $sheet->setCellValue('AR' . ($key + 2), $nomeRegiao);
            $sheet->setCellValue('AS' . ($key + 2), $dado['Supplier']['observacao']); $col++;
            $sheet->setCellValue('AT' . ($key + 2), $dado['Supplier']['realiza_gestao_eficiente'] ? 'Sim' : 'Não');
            $valorConcatenado = $dado['Supplier']['valor'] . ' ' . $dado['Supplier']['unidade_tempo'];
            $sheet->setCellValue('AU' . ($key + 2), $valorConcatenado);
            

        }

        $sheet2 = $objPHPExcel->createSheet();
        $sheet2->setTitle("Logins e Senhas");

        $col = 'A';
        $sheet2->setCellValue($col.'1', "Fornecedor"); $col++;
        $sheet2->setCellValue($col.'1', "ID Cliente"); $col++;
        $sheet2->setCellValue($col.'1', "Razão Social"); $col++;
        $sheet2->setCellValue($col.'1', "CNPJ"); $col++;
        $sheet2->setCellValue($col.'1', "URL"); $col++;
        $sheet2->setCellValue($col.'1', "Login"); $col++;
        $sheet2->setCellValue($col.'1', "Senha"); $col++;
        $sheet2->setCellValue($col.'1', "Data de Criação"); $col++;
        $sheet2->setCellValue($col.'1', "Usuário de Criação"); $col++;
        $sheet2->setCellValue($col.'1', "Data de Alteração"); $col++;
        $sheet2->setCellValue($col.'1', "Usuário de Alteração"); $col++;

        foreach ($dados_log as $key => $dado) {
            $col = 'A';
            $sheet2->setCellValue($col.($key + 2), $dado["Supplier"]["nome_fantasia"]); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["Customer"]["id"]); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["Customer"]["nome_primario"]); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["Customer"]["documento"]); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["CustomerSupplierLogin"]["url"]); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["CustomerSupplierLogin"]["login"]); $col++;
            $sheet2->setCellValue($col.($key + 2), " ".trim($dado["CustomerSupplierLogin"]["password"])); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["CustomerSupplierLogin"]["created"]); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["UserCreated"]["name"]); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["CustomerSupplierLogin"]["updated"]); $col++;
            $sheet2->setCellValue($col.($key + 2), $dado["UserUpdated"]["name"]); $col++;
        }
    }

    public function getPedidosRelatorio($objPHPExcel, $dados)
    {

        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Status"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Código"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cliente"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data de criação"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Período"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Subtotal"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Repasse"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Taxa"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Desconto"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "TPP"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Fee Economia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cliente"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Economia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Total"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Usuário"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Grupo Econômico"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data de criação"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Qtde Operadoras"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Qtde Beneficiários"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data pagamento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data Vencimento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data Disponiblização "); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Gestão Eficiente"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Gestão Eficiente - Data Alteração"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Gestão Eficiente - Usuário Alteração"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Gestão Eficiente - Observação"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Antecipada"); $col++;

        foreach ($dados as $key => $dado) {
            $fee_economia = 0;
            $total_economia = 0;
            $period = $dado["Order"]["order_period_from"] . " a " . $dado["Order"]["order_period_to"];
            $dataHora = date('d/m/Y H:i:s', strtotime($dado["Order"]["created_nao_formatado"]));
            $vl_economia = $dado["Order"]["total_balances"];
            $fee_saldo = $dado["Order"]["fee_saldo_not_formated"];
    
            if ($fee_saldo != 0 and $vl_economia != 0) {
                $fee_economia = (($fee_saldo / 100) * ($vl_economia));
            }

            $vl_economia = ($vl_economia - $fee_economia);
            $total_economia = ($vl_economia + $fee_economia);
        
            $col = 'A';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Status"]["name"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Customer"]["codigo_associado"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["id"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Customer"]["nome_primario"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key + 2), $dataHora);$col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key + 2), $period);$col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["subtotal"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["transfer_fee"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["commission_fee"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["desconto"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["tpp_fee"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), number_format($fee_economia,2,',','.')); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), number_format($vl_economia,2,',','.')); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), number_format($total_economia,2,',','.')); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["total"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["CustomerCreator"]["name"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['EconomicGroup']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['created']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['suppliersCount']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['usersCount']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Income']['data_pagamento']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['is_partial'] == 1 ? "Importação" :($dado['Order']['is_partial'] == 2 ? "Automático" :($dado['Order']['is_partial'] == 3 ? "PIX" :($dado['Order']['is_partial'] == 4 ? "Emissão" : "Desconhecido"))));$col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['due_date']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['credit_release_date']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['pedido_complementar']  == 1 ? 'S' : 'N'); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['updated_ge']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['UpdatedGe']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['observation_ge']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Customer"]['emitir_nota_fiscal'] == 'A' ? 'Sim' : 'Não'); $col++;
        }
    }

    public function getRelatorioCompras($objPHPExcel, $dados)
    {

        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Status"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Código"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data de criação"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cliente"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Fornecedor"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Beneficiário"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Benefício"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Dias Úteis"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Quantidade por dia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Valor por dia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Subtotal"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Repasse"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Taxa"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Total"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Economia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Relatório beneficio"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data inicio Processamento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data fim Processamento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Status Processamento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Motivo Processamento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Pedido Operadora"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CPF"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Compra operadora"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número do cartão"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Matrícula operadora"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "GE"); $col++;



        
        foreach ($dados as $key => $dado) {
            $col = 'A';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Status"]["name"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Customer"]["codigo_associado"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['created'] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["id"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Customer"]["nome_primario"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Supplier"]["nome_fantasia"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["CustomerUser"]["name"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Benefit"]["name"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["id"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["manual_quantity"] != 0 ? $dado["OrderItem"]["manual_quantity"] : $dado["CustomerUserItinerary"]["quantity"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), 'R$' . $dado["OrderItem"]["price_per_day"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), 'R$' . $dado["OrderItem"]["subtotal"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), 'R$' . $dado["OrderItem"]["transfer_fee"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), 'R$' . $dado["OrderItem"]["commission_fee"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), 'R$' . $dado["OrderItem"]["total"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), 'R$' . $dado["OrderItem"]["saldo"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), 'R$' . $dado["OrderItem"]["total_saldo"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["data_inicio_processamento"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["data_fim_processamento"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["status_processamento"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["motivo_processamento"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["pedido_operadora"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["data_entrega"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["CustomerUser"]["cpf"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key + 2), number_format(($dado["OrderItem"]["subtotal_not_formated"] - $dado["OrderItem"]["saldo_not_formated"]), 2, ',', '.')); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["CustomerUserItinerary"]["card_number"] ); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["CustomerUserItinerary"]["matricula"] ); $col++;
            $valor = $dado["Order"]["pedido_complementar"] == 1 ? 'Sim' : 'Não';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key + 2), $valor);
            $col++;


        }
    }

    public function getBeneficioRelatorio($objPHPExcel, $dados)
    {

        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Código"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Status Fornecedor"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Status Item"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Nome "); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Id"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Fornecedor"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Valor Unitário"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Prazo Recarga"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Prazo Cartão Novo"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data Atualizacão Tarifa"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cidade"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Estado "); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Item Vraiável "); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Observação "); $col++;

        foreach ($dados as $key => $dado) {
            $col = 'A';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['code']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Supplier']['Status']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Status']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Supplier']['id']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Supplier']['nome_fantasia']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['BenefitType']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['unit_price']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['time_to_recharge']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['time_card']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['last_fare_update']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['city']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['state']); $col++;
            $isVariable = !empty($dado['Benefit']['is_variable']) && $dado['Benefit']['is_variable'] == 1 ? 'Sim' : 'Não';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key + 2), $isVariable); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Benefit']['observation']); $col++;
        }
    }

    public function getNegativacaoTemplate($objPHPExcel, $dados, $errosPefin)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Natureza de operação")
            ->setCellValue('C1', "Tipo de pessoa")
            ->setCellValue('D1', "Cliente")
            ->setCellValue('E1', "CPF")
            ->setCellValue('F1', "Nome")
            ->setCellValue('G1', "CEP")
            ->setCellValue('H1', "Endereço")
            ->setCellValue('I1', "Número")
            ->setCellValue('J1', "Complemento")
            ->setCellValue('K1', "Bairro")
            ->setCellValue('L1', "Cidade")
            ->setCellValue('M1', "Estado")
            ->setCellValue('N1', "Data da compra")
            ->setCellValue('O1', "Nosso número")
            ->setCellValue('P1', "Número do título")
            ->setCellValue('Q1', "Venc da dívida")
            ->setCellValue('R1', "Valor")
            ->setCellValue('S1', "Valor");

        $indx = 1;

        $tipo_pessoa = [2 => "Física", 1 => "Jurídica"];

        for ($i = 0; $i < count($dados); $i++) {
            if ($dados[$i]["Status"]["id"] == 23) {
                $pefinErros = isset($dados[$i]['CadastroPefinErros']) ? $dados[$i]['CadastroPefinErros'] : false;

                $nome_erro = "";
                if ($pefinErros) {
                    for ($a = 0; $a < count($pefinErros); $a++) {
                        $erro = $errosPefin->find('first', ['conditions' => ['ErrosPefin.id' => $pefinErros[$a]['erros_pefin_id']]]);

                        $nome_erro .= $erro['ErrosPefin']['descricao'] . " ";
                    }
                }

                $status = $dados[$i]["Status"]["name"] . " " . $nome_erro;
            } else {
                $status = $dados[$i]["Status"]["name"];
            }

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $status)
                ->setCellValue('B' . $indx, $dados[$i]["NaturezaOperacao"]["nome"])
                ->setCellValue('C' . $indx, $tipo_pessoa[$dados[$i]["CadastroPefin"]["tipo_pessoa"]])
                ->setCellValue('D' . $indx, $dados[$i]["Customer"]["nome_secundario"])
                ->setCellValue('E' . $indx, $dados[$i]["CadastroPefin"]["documento"])
                ->setCellValue('F' . $indx, $dados[$i]["CadastroPefin"]["nome"])
                ->setCellValue('G' . $indx, $dados[$i]["CadastroPefin"]["cep"])
                ->setCellValue('H' . $indx, $dados[$i]["CadastroPefin"]["endereco"])
                ->setCellValue('I' . $indx, $dados[$i]["CadastroPefin"]["numero"])
                ->setCellValue('J' . $indx, $dados[$i]["CadastroPefin"]["complemento"])
                ->setCellValue('K' . $indx, $dados[$i]["CadastroPefin"]["bairro"])
                ->setCellValue('L' . $indx, $dados[$i]["CadastroPefin"]["cidade"])
                ->setCellValue('M' . $indx, $dados[$i]["CadastroPefin"]["estado"])
                ->setCellValue('N' . $indx, $dados[$i]["CadastroPefin"]["data_compra"])
                ->setCellValue('O' . $indx, $dados[$i]["CadastroPefin"]["nosso_numero"])
                ->setCellValue('P' . $indx, $dados[$i]["CadastroPefin"]["numero_titulo"])
                ->setCellValue('Q' . $indx, $dados[$i]["CadastroPefin"]["venc_divida"])
                ->setCellValue('R' . $indx, $dados[$i]["CadastroPefin"]["valor"])
                ->setCellValue('S' . $indx, $dados[$i]["CadastroPefin"]["created"]);
        }
    }

    public function getNovaVidaTemplate($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Produto")
            ->setCellValue('B1', "Data")
            ->setCellValue('C1', "Associado");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['Product']['name'])
                ->setCellValue('B' . $indx, date('d/m/Y H:i:s', strtotime($dados[$i]['NovaVidaLogConsulta']['created'])))
                ->setCellValue('C' . $indx, $dados[$i]['Customer']['codigo_associado'] . ' - ' . $dados[$i]['Customer']['nome_primario']);
        }
    }

    public function getNovaVidaConsolidadoTemplate($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Produto")
            ->setCellValue('B1', "Qtde de consultas realizadas");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['Product']['name'])
                ->setCellValue('B' . $indx, $dados[$i][0]['total']);
        }
    }


    public function getInadimplentesTemplate($objPHPExcel, $dados, $total_valores)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Cliente")
            ->setCellValue('B1', "Estado")
            ->setCellValue('C1', "Cidade")
            ->setCellValue('D1', "Valor");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['Customer']['codigo_associado'] . ' - ' . $dados[$i]['Customer']['nome_primario'] . ' ' . $dados[$i]['Customer']['nome_secundario'])
                ->setCellValue('B' . $indx, $dados[$i]['Customer']['estado'])
                ->setCellValue('C' . $indx, $dados[$i]['Customer']['cidade'])
                ->setCellValue('D' . $indx, number_format($dados[$i][0]['total'], 2, ',', '.'));
        }
        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C' . $indx, "Total")
            ->setCellValue('D' . $indx, number_format($total_valores, 2, ",", "."));
    }

    public function getClientesCobradosTemplate($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Cliente")
            ->setCellValue('B1', "Parcela")
            ->setCellValue('C1', "Vencimento")
            ->setCellValue('D1', "Valor");

        $total = 0;
        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {

            $total += $dados[$i]["Income"]["valor_total"];
            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Income"]['Customer']['codigo_associado'] . ' - ' . $dados[$i]["Income"]['Customer']['nome_primario'])
                ->setCellValue('B' . $indx, $dados[$i]["Income"]["parcela"])
                ->setCellValue('C' . $indx, date("d/m/Y", strtotime($dados[$i]['Income']['vencimento_nao_formatado'])))
                ->setCellValue('D' . $indx, $dados[$i]["Income"]["valor_total"]);
        }
        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('D' . $indx, number_format($total, 2, ",", "."));
    }

    public function getClientesExitoTemplate($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Cliente")
            ->setCellValue('B1', "Parcela")
            ->setCellValue('C1', "Vencimento")
            ->setCellValue('D1', "Valor");
        $total = 0;
        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {

            $total += $dados[$i]["i"]["valor_total"];
            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['c']['codigo_associado'] . ' - ' . $dados[$i]['c']['nome_primario'])
                ->setCellValue('B' . $indx, $dados[$i]["i"]["parcela"])
                ->setCellValue('C' . $indx, date("d/m/Y", strtotime($dados[$i]['i']['vencimento'])))
                ->setCellValue('D' . $indx, $dados[$i]["i"]["valor_total"]);
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('D' . $indx, number_format($total, 2, ",", "."));
    }

    public function getFaturamentoRevendaTemplate($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Revenda")
            ->setCellValue('B1', "Valor a pagar")
            ->setCellValue('C1', "Valor pago");

        $previsao = $dados['previsao'];
        $realizado = $dados['realizado'];

        $indx = 1;
        for ($i = 0; $i < count($previsao); $i++) {

            $valor_pagar = $previsao[$i][0]["valor_comissao"];

            if (!empty($realizado[$i][0])) {
                $valor_pago = $realizado[$i][0]["valor_comissao"];
            } else {
                $valor_pago = 0;
            }

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $previsao[$i]["r"]["nome_fantasia"])
                ->setCellValue('B' . $indx, number_format($valor_pagar, 2, ',', '.'))
                ->setCellValue('C' . $indx, number_format($valor_pago, 2, ',', '.'));
        }
    }

    public function getFaturamentoHipercheckTemplate($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Vendedor")
            ->setCellValue('B1', "Quantidade de planos")
            ->setCellValue('C1', "Comissão")
            ->setCellValue('D1', "Valor a pagar")
            ->setCellValue('E1', "Valor pago");

        $previsao = $dados['previsao'];
        $realizado = $dados['realizado'];

        $indx = 1;
        for ($i = 0; $i < count($previsao); $i++) {

            $valor_pagar = $previsao[$i][0]["valor_comissao"];

            if (!empty($realizado[$i][0])) {
                $valor_pago = $realizado[$i][0]["valor_comissao"];
            } else {
                $valor_pago = 0;
            }

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $previsao[$i]["s"]["nome_fantasia"])
                ->setCellValue('B' . $indx, $previsao[$i][0]["qtde"])
                ->setCellValue('C' . $indx, number_format($previsao[$i]['p']["commission"], 2, ',', '.'))
                ->setCellValue('D' . $indx, number_format($valor_pagar, 2, ',', '.'))
                ->setCellValue('E' . $indx, number_format($valor_pago, 2, ',', '.'));
        }
    }

    public function getFaturamentoTemplate($objPHPExcel, $dados)
    {
        $campoInicial = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . '1', "Código associado");
        $campoInicial++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . '1', "Razão social");
        $campoInicial++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . '1', "Nome fantasia");
        $campoInicial++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . '1', "Documento");
        $campoInicial++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . '1', "Email");
        $campoInicial++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . '1', "Franquia");
        $campoInicial++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . '1', "Vendedor");
        $campoInicial++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . '1', "Total");

        foreach ($dados as $key => $dado) {
            $valorManutencaoPefin = 0;
            if (!empty($dado['PefinMaintenance']['value_nao_formatado']))
                $valorManutencaoPefin = $dado['PefinMaintenance']['value_nao_formatado'];

            $total_sem_desconto = 0;

            $campoInicial = 'A';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . ($key + 2), $dado['Customer']['codigo_associado']);
            $campoInicial++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . ($key + 2), $dado['Customer']['nome_primario']);
            $campoInicial++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . ($key + 2), $dado['Customer']['nome_secundario']);
            $campoInicial++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . ($key + 2), $dado['Customer']['documento']);
            $campoInicial++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . ($key + 2), $dado['Customer']['email']);
            $campoInicial++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . ($key + 2), $dado['Resale']['nome_fantasia']);
            $campoInicial++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . ($key + 2), $dado['Seller']['nome_fantasia']);
            $campoInicial++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($campoInicial . ($key + 2), number_format($total_com_desconto, 2, ',', '.'));
        }
    }

    public function getClientesDesconto($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Código")
            ->setCellValue('C1', "Nome")
            ->setCellValue('D1', "Email")
            ->setCellValue('E1', "Data de cadastro");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Status"]["name"])
                ->setCellValue('B' . $indx, $dados[$i]["Customer"]["codigo_associado"])
                ->setCellValue('C' . $indx, $dados[$i]["Customer"]["nome_secundario"])
                ->setCellValue('D' . $indx, $dados[$i]["Customer"]["email"])
                ->setCellValue('E' . $indx, date('d/m/Y', strtotime($dados[$i]["Customer"]["created"])));
        }
    }

    public function getStatusClientes($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Código")
            ->setCellValue('B1', "Nome fantasia")
            ->setCellValue('C1', "Cancelado")
            ->setCellValue('D1', "Bloqueado")
            ->setCellValue('E1', "Inadimplente")
            ->setCellValue('F1', "Aguardando Carta")
            ->setCellValue('G1', "Login de Consulta");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;

            $status_cliente = $dados[$i]["Customer"]["status_id"];
            $status_resposta = "";


            switch ($status_cliente) {
                case 4:
                    $status_resposta = "Bloqueado";
                    break;
                case 5:
                    $status_resposta = "Cancelado";
                    break;
                case 6:
                    $status_resposta = "Aguardando Carta";
                    break;

                default:
                    $status_resposta = "Inadimplente";
                    break;
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Customer"]["codigo_associado"])
                ->setCellValue('B' . $indx, $dados[$i]["Customer"]["nome_secundario"])
                ->setCellValue('C' . $indx, ($status_resposta == "Cancelado") ? "Sim" : "Não")
                ->setCellValue('D' . $indx, ($status_resposta == "Bloqueado") ? "Sim" : "Não")
                ->setCellValue('E' . $indx, ($status_resposta == "Aguardando Carta") ? "Sim" : "Não")
                ->setCellValue('F' . $indx, ($status_resposta == "Inadimplente") ? "Sim" : "Não")
                ->setCellValue('G' . $indx, $dados[$i]["Status"]["name"]);
        }
    }


    public function getLoginsConsulta($objPHPExcel, $dados)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status do Login de Consulta")
            ->setCellValue('B1', "Login de Consulta")
            ->setCellValue('C1', "Código do Cliente")
            ->setCellValue('D1', "Cliente");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Status"]["name"])
                ->setCellValue('B' . $indx, $dados[$i]["LoginConsulta"]["login"])
                ->setCellValue('C' . $indx, $dados[$i]["Customer"]["codigo_associado"])
                ->setCellValue('D' . $indx, $dados[$i]["Customer"]["nome_secundario"]);
        }
    }

    public function getLogonBlindagem($objPHPExcel, $dados)
    {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Login de Consulta")
            ->setCellValue('B1', "Código do Cliente")
            ->setCellValue('C1', "Cliente")
            ->setCellValue('D1', "Criado Por")
            ->setCellValue('E1', "Data e Hora")
            ->setCellValue('F1', "Status Login de Consulta");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["LoginConsulta"]["login"])
                ->setCellValue('B' . $indx, $dados[$i]["Customer"]["codigo_associado"])
                ->setCellValue('C' . $indx, $dados[$i]["Customer"]["nome_secundario"])
                ->setCellValue('D' . $indx, $dados[$i]["User"]["name"])
                ->setCellValue('E' . $indx, date("d/m/Y H:i:s", strtotime($dados[$i]["LoginConsulta"]["created"])))
                ->setCellValue('F' . $indx, ($dados[$i]["LoginConsulta"]["login_blindado"] == 2) ? "Pendente" : "Blindado");
        }
    }

    public function getPefinDetalhes($objPHPExcel, $dados)
    {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Nome do credor")
            ->setCellValue('C1', "CNPJ do credor")
            ->setCellValue('D1', "Código do Associado")
            ->setCellValue('E1', "Nome")
            ->setCellValue('F1', "Documento")
            ->setCellValue('G1', "Valor")
            ->setCellValue('H1', "Remessa")
            ->setCellValue('I1', "Sequência")
            ->setCellValue('J1', "Erros");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;

            $strErros = '';

            if (!empty($erros)) {
                for ($a = 0; $a < count($erros); $a++) {
                    $strErros .= $erros[$a]['ErrosPefin']['descricao'] . '<br>';
                }
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Status"]["name"])
                ->setCellValue('B' . $indx, $dados[$i]['Customer']['nome_secundario'])
                ->setCellValue('C' . $indx, $dados[$i]['Customer']['documento'])
                ->setCellValue('D' . $indx, $dados[$i]['Customer']['codigo_associado'])
                ->setCellValue('E' . $indx, $dados[$i]['CadastroPefin']['nome'])
                ->setCellValue('F' . $indx, $dados[$i]['CadastroPefin']['documento'])
                ->setCellValue('G' . $indx, $dados[$i]['CadastroPefin']['valor'])
                ->setCellValue('H' . $indx, $dados[$i]['CadastroPefin']['n_remessa'])
                ->setCellValue('I' . $indx, $dados[$i]['CadastroPefin']['n_sequencial'])
                ->setCellValue('J' . $indx, $strErros);
        }
    }

    public function getDivisao($objPHPExcel, $dados)
    {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "BeRH")
            ->setCellValue('A2', "Cliente")
            ->setCellValue('B2', "Valor")
            ->setCellValue('C2', "Vencimento")
            ->setCellValue('E1', "Ivan")
            ->setCellValue('E2', "Cliente")
            ->setCellValue('F2', "Valor")
            ->setCellValue('G2', "Vencimento");

        $indx = 2;
        $total_hiper = 0;
        $berh = $dados['berh'];
        for ($i = 0; $i < count($berh); $i++) {
            $indx++;
            $total_hiper += $berh[$i]['Income']['valor_total_nao_formatado'];

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $berh[$i]['Customer']['nome_primario'])
                ->setCellValue('B' . $indx, $berh[$i]['Income']['valor_total'])
                ->setCellValue('C' . $indx, $berh[$i]['Income']['vencimento']);
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $indx, 'Total:')
            ->setCellValue('B' . $indx, number_format($total_hiper, 2, ',', '.'));

        $indx = 2;
        $total_ivan = 0;
        $ivan = $dados['ivan'];
        for ($i = 0; $i < count($ivan); $i++) {
            $indx++;
            $total_ivan += $ivan[$i]['Income']['valor_total_nao_formatado'];

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('E' . $indx, $ivan[$i]['Customer']['nome_primario'])
                ->setCellValue('F' . $indx, $ivan[$i]['Income']['valor_total'])
                ->setCellValue('G' . $indx, $ivan[$i]['Income']['vencimento']);
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('E' . $indx, 'Total:')
            ->setCellValue('F' . $indx, number_format($total_ivan, 2, ',', '.'));
    }

    public function getDivisaoTodos($objPHPExcel, $dados)
    {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "BeRH")
            ->setCellValue('A2', "Cliente")
            ->setCellValue('B2', "CNPJ")
            ->setCellValue('C2', "Endereço")
            ->setCellValue('D2', "Telefone")
            ->setCellValue('E2', "Valor")
            ->setCellValue('F2', "Vencimento")
            ->setCellValue('H1', "Ivan")
            ->setCellValue('H2', "Cliente")
            ->setCellValue('I2', "CNPJ")
            ->setCellValue('J2', "Endereço")
            ->setCellValue('K2', "Telefone")
            ->setCellValue('L2', "Valor")
            ->setCellValue('M2', "Vencimento");

        $indx = 2;
        $total_hiper = 0;
        $berh = $dados['berh'];
        for ($i = 0; $i < count($berh); $i++) {
            $indx++;
            $total_hiper += $berh[$i]['Income']['valor_total_nao_formatado'];

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $berh[$i]['Customer']['nome_primario'])
                ->setCellValue('B' . $indx, $berh[$i]['Customer']['documento'])
                ->setCellValue('C' . $indx, $berh[$i]['Customer']['endereco'] . ', ' . $berh[$i]['Customer']['numero'] . ' - ' . $berh[$i]['Customer']['bairro'] . ' - ' . $berh[$i]['Customer']['cidade'] . ', ' . $berh[$i]['Customer']['estado'])
                ->setCellValue('D' . $indx, $berh[$i]['Customer']['telefone1'])
                ->setCellValue('E' . $indx, $berh[$i]['Income']['valor_total'])
                ->setCellValue('F' . $indx, $berh[$i]['Income']['vencimento']);
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $indx . ':E' . $indx);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('E' . $indx, 'Total:')
            ->setCellValue('F' . $indx, number_format($total_hiper, 2, ',', '.'));

        $indx = 2;
        $total_ivan = 0;
        $ivan = $dados['ivan'];
        for ($i = 0; $i < count($ivan); $i++) {
            $indx++;
            $total_ivan += $ivan[$i]['Income']['valor_total_nao_formatado'];

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('H' . $indx, $ivan[$i]['Customer']['nome_primario'])
                ->setCellValue('I' . $indx, $ivan[$i]['Customer']['documento'])
                ->setCellValue('J' . $indx, $ivan[$i]['Customer']['endereco'] . ', ' . $ivan[$i]['Customer']['numero'] . ' - ' . $ivan[$i]['Customer']['bairro'] . ' - ' . $ivan[$i]['Customer']['cidade'] . ', ' . $ivan[$i]['Customer']['estado'])
                ->setCellValue('K' . $indx, $ivan[$i]['Customer']['telefone1'])
                ->setCellValue('L' . $indx, $ivan[$i]['Income']['valor_total'])
                ->setCellValue('M' . $indx, $ivan[$i]['Income']['vencimento']);
        }

        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $indx . ':L' . $indx);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('L' . $indx, 'Total:')
            ->setCellValue('M' . $indx, number_format($total_ivan, 2, ',', '.'));
    }

    public function getPlansCustomers($objPHPExcel, $dados)
    {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Código")
            ->setCellValue('C1', "Nome")
            ->setCellValue('D1', "Email")
            ->setCellValue('E1', "Data de cadastro");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]["Status"]["name"])
                ->setCellValue('B' . $indx, $dados[$i]["Customer"]["codigo_associado"])
                ->setCellValue('C' . $indx, $dados[$i]["Customer"]["nome_secundario"])
                ->setCellValue('D' . $indx, $dados[$i]["Customer"]["email"])
                ->setCellValue('E' . $indx, date('d/m/Y', strtotime($dados[$i]["Customer"]["created"])));
        }
    }

    public function getBaixaManual($objPHPExcel, $dados)
    {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "CNPJ associado")
            ->setCellValue('B1', "Cliente")
            ->setCellValue('C1', "Mensalidade")
            ->setCellValue('D1', "Vencimento")
            ->setCellValue('E1', "Data de pagamento")
            ->setCellValue('F1', "Valor total")
            ->setCellValue('G1', "Valor pago")
            ->setCellValue('H1', "Data baixa")
            ->setCellValue('I1', "Usuário baixa");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['c']['documento'])
                ->setCellValue('B' . $indx, $dados[$i]['c']['nome_secundario'])
                ->setCellValue('C' . $indx, $dados[$i]['i']['mensalidade'])
                ->setCellValue('D' . $indx, date('d/m/Y', strtotime($dados[$i]['i']['vencimento'])))
                ->setCellValue('E' . $indx, $dados[$i]['i']['data_pagamento'] ? date('d/m/Y', strtotime($dados[$i]['i']['data_pagamento'])) : '')
                ->setCellValue('F' . $indx, number_format($dados[$i]['i']['valor_total'], 2, ',', '.'))
                ->setCellValue('G' . $indx, number_format($dados[$i]['i']['valor_pago'], 2, ',', '.'))
                ->setCellValue('H' . $indx, $dados[$i]['i']['data_baixa'] ? date('d/m/Y', strtotime($dados[$i]['i']['data_baixa'])) : '')
                ->setCellValue('I' . $indx, $dados[$i]['u']['usuarioBaixa']);
        }
    }

    public function getMovimentacaoStatus($objPHPExcel, $dados)
    {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Código")
            ->setCellValue('B1', "Nome")
            ->setCellValue('C1', "Status atual")
            ->setCellValue('D1', "Status anterior")
            ->setCellValue('E1', "Data")
            ->setCellValue('F1', "Usuário")
            ->setCellValue('G1', "Vendedor")
            ->setCellValue('H1', "Cadastro cliente")
            ->setCellValue('I1', "Valor plano Ativo");

        $indx = 1;
        for ($i = 0; $i < count($dados); $i++) {
            $indx++;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $dados[$i]['c']['codigo_associado'])
                ->setCellValue('B' . $indx, $dados[$i]['c']['nome_secundario'])
                ->setCellValue('C' . $indx, $dados[$i]['s']['statusAtual'])
                ->setCellValue('D' . $indx, $dados[$i]['sm']['statusAnterior'])
                ->setCellValue('E' . $indx, date('d/m/Y H:i:s', strtotime($dados[$i]['mv']['created'])))
                ->setCellValue('F' . $indx, $dados[$i]['u']['usuario'])
                ->setCellValue('G' . $indx, $dados[$i]['ve']['nome_fantasia'])
                ->setCellValue('H' . $indx, date('d/m/Y H:i:s', strtotime($dados[$i]['c']['created'])))
                ->setCellValue('I' . $indx, number_format($dados[$i][0]['mensalidade'], 2, ',', '.'));
        }
    }

    public function getDadosComerciais($objPHPExcel, $data)
    {

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Status")
            ->setCellValue('B1', "Nome fantasia")
            ->setCellValue('C1', "Razão social")
            ->setCellValue('D1', "Responsável")
            ->setCellValue('E1', "Telefone 1")
            ->setCellValue('F1', "Telefone 2")
            ->setCellValue('G1', "Celular 1")
            ->setCellValue('H1', "Celular 2")
            ->setCellValue('I1', "Endereço")
            ->setCellValue('J1', "Cidade/Estado")
            ->setCellValue('K1', "Valor total")
            ->setCellValue('L1', "Plano")
            ->setCellValue('M1', "Valor do plano");

        $indx = 1;
        for ($i = 0; $i < count($data); $i++) {

            $indx++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indx, $data[$i]['Status']['name'])
                ->setCellValue('B' . $indx, $data[$i]['Customer']['codigo_associado'] . ' - ' . $data[$i]['Customer']['nome_secundario'])
                ->setCellValue('C' . $indx, $data[$i]['Customer']['nome_primario'])
                ->setCellValue('D' . $indx, $data[$i]['Customer']['responsavel'])
                ->setCellValue('E' . $indx, $data[$i]['Customer']['telefone1'])
                ->setCellValue('F' . $indx, $data[$i]['Customer']['telefone2'])
                ->setCellValue('G' . $indx, $data[$i]['Customer']['celular1'])
                ->setCellValue('H' . $indx, $data[$i]['Customer']['celular2'])
                ->setCellValue('I' . $indx, trim($data[$i]['Customer']['endereco']) . ', ' . $data[$i]['Customer']['numero'] . ' - ' . $data[$i]['Customer']['bairro'])
                ->setCellValue('J' . $indx, $data[$i]['Customer']['cidade'] . ' - ' . $data[$i]['Customer']['estado'])
                ->setCellValue('K' . $indx, number_format($data[$i][0]['valor_em_aberto'], 2, ',', '.'))
                ->setCellValue('L' . $indx, $data[$i]['Plan']['description'])
                ->setCellValue('M' . $indx, number_format($data[$i]['Plan']['value'], 2, ',', '.'));
        }
    }

    public function getAtendimento($objPHPExcel, $data)
{
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', "Status")
        ->setCellValue('B1', "Atendimento N°")
        ->setCellValue('C1', "Cliente")
        ->setCellValue('D1', "Documento")
        ->setCellValue('E1', "Departamento")
        ->setCellValue('F1', "Arquivo") 
        ->setCellValue('G1', "Assunto")
        ->setCellValue('H1', "Enviado em")
        ->setCellValue('I1', "Finalizado em")
        ->setCellValue('J1', "Atendente");


    $indx = 1;
    for ($i = 0; $i < count($data); $i++) {
        $indx++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $indx, $data[$i]['Status']['name'])
            ->setCellValue('B' . $indx, $data[$i]['Atendimento']['id'])
            ->setCellValue('C' . $indx, $data[$i]['Customer']['nome_primario'])
            ->setCellValue('D' . $indx, $data[$i]['Customer']['documento'])
            ->setCellValue('E' . $indx, $data[$i]['Department']['name'])
            ->setCellValue('F' . $indx, $data[$i]['Atendimento']['file_atendimento']) 
            ->setCellValue('G' . $indx, $data[$i]['Atendimento']['subject'])
            ->setCellValue('H' . $indx, date('d/m/Y H:i:s', strtotime($data[$i]['Atendimento']['created'])))
            ->setCellValue('I' . $indx, $data[$i]['Atendimento']['data_finalizacao'])
            ->setCellValue('J' . $indx, $data[$i]['Atendimento']['name_atendente']);

    }
}


    public function getItinerary($spreadsheet, $dados)
    {
        
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $activeWorksheet->setCellValue('A1', "CNPJ")
        ->setCellValue('B1', "Matrícula")
        ->setCellValue('C1', "Nome")
        ->setCellValue('D1', "CPF")
        ->setCellValue('E1', "RG")
        ->setCellValue('F1', "Orgão Expeditor")
        ->setCellValue('G1', "Data De Nascimento")
        ->setCellValue('H1', "Nome Da Mãe")
        ->setCellValue('I1', "Departamenrto")
        ->setCellValue('J1', "Dias Úteis")
        ->setCellValue('K1', "Tipo Pedido")
        ->setCellValue('L1', "Tipo de Benefício / Serviço")
        ->setCellValue('M1', "Código Operadora")
        ->setCellValue('N1', "Código do Benefício (Ìtem)")
        ->setCellValue('O1', "Número do Cartão (Vale Transporte)")
        ->setCellValue('P1', "Valor Unitário")
        ->setCellValue('Q1', "Quantidade")
        ->setCellValue('R1', "Por Pedido / Dia")
        ->setCellValue('S1', "Qtde do Benefício por Dia")
        ->setCellValue('T1', "Var")
        ->setCellValue('U1', "Total")
        ->setCellValue('V1', "Número Do Sic Uso Exclusivo De Curitiba-Pr")
        ->setCellValue('W1', "Faixa Salarial do Colaborador")
        ->setCellValue('X1', "Cep Residência Colaborador")
        ->setCellValue('Y1', "Rua Residência")
        ->setCellValue('Z1', "Número Residência")
        ->setCellValue('AA1', "Complemento Residência")
        ->setCellValue('AB1', "Bairro Residência")
        ->setCellValue('AC1', "Cidade Residência")
        ->setCellValue('AD1', "Estado Residência")
        ->setCellValue('AE1', "Cep Do Trabalho")
        ->setCellValue('AF1', "Rua Trabalho")
        ->setCellValue('AG1', "Número Trabalho")
        ->setCellValue('AH1', "Complemento Trabalho")
        ->setCellValue('AI1', "Bairro Trabalho")
        ->setCellValue('AJ1', "Cidade Trabalho")
        ->setCellValue('AK1', "Estado Trabalho")
        ->setCellValue('AL1', "Cep De Entrega")
        ->setCellValue('AM1', "Rua Entrega")
        ->setCellValue('AN1', "Número De Entrega")
        ->setCellValue('AO1', "Complemento De Entrega")
        ->setCellValue('AP1', "Bairro De Entrega")
        ->setCellValue('AQ1', "Cidade De Entrega")
        ->setCellValue('AR1', "Estado De Entrega")
        ->setCellValue('AS1', "Tipo De Conta")
        ->setCellValue('AT1', "Nome do banco")
        ->setCellValue('AU1', "Cod. Do Banco")
        ->setCellValue('AV1', "Agencia")
        ->setCellValue('AW1', "Digito Da Agencia")
        ->setCellValue('AX1', "Conta")
        ->setCellValue('AY1', "Digito Da Conta")
        ->setCellValue('AZ1', "Gênero")
        ->setCellValue('BA1', "Estado Civil")
        ->setCellValue('BB1', "E-Mail")
        ->setCellValue('BC1', "Ddd - Telefone ")
        ->setCellValue('BD1', "Número Do Telefone ")
        ->setCellValue('BE1', "Cargo")
        ->setCellValue('BF1', "Pedido")
        ->setCellValue('BG1', "Status")
        ->setCellValue('BH1', "Data")
        ->setCellValue('BI1', "Codigo Operadora");

        $indx = 1;
        $total = 0;
        for ($i = 0; $i < count($dados); $i++) {

            $total += $dados[$i]["OrderItem"]["subtotal_not_formated"];
            $quantity = $dados[$i]["OrderItem"]["manual_quantity"] != 0 ? 
                        $dados[$i]["OrderItem"]["manual_quantity"] : 
                        $dados[$i]["CustomerUserItinerary"]["quantity"];

            $indx++;
            // $activeWorksheet
            //  ->setCellValue('A' . $indx, $dados[$i]["Status"]["name"])
            //  ->setCellValue('B' . $indx, str_pad($dados[$i]['CnabLote']['remessa'], 6, 0, STR_PAD_LEFT))
            //  ->setCellValue('C' . $indx, date('d/m/Y H:i:s', strtotime($dados[$i]['CnabLote']['created'])))
            //  ->setCellValue('D' . $indx, $dados[$i]['Bank']['name'])
            //  ->setCellValue('E' . $indx, $dados[$i][0]['qtde'])
            //  ->setCellValue('F' . $indx, number_format($dados[$i][0]['valor_total'], 2, ',', '.'))
            //  ->setCellValue('G' . $indx, $dados[$i]['CnabLote']['arquivo']);
            $activeWorksheet->setCellValue('A'. $indx, $dados[$i]["Customer"]["documento"])
                ->setCellValue('B'. $indx, '-')
                ->setCellValue('C'. $indx, $dados[$i]['CustomerUser']['name'])
                ->setCellValue('D'. $indx, $dados[$i]['CustomerUser']['cpf'])
                ->setCellValue('E'. $indx, $dados[$i]['CustomerUser']['rg'])
                ->setCellValue('F'. $indx, $dados[$i]['CustomerUser']['emissor_rg'])
                ->setCellValue('G'. $indx, $dados[$i]['CustomerUser']['data_nascimento'])
                ->setCellValue('H'. $indx, $dados[$i]['CustomerUser']['nome_mae'])
                ->setCellValue('I'. $indx, $dados[$i]['CustomerDepartment']['name'])
                ->setCellValue('J'. $indx, $dados[$i]['OrderItem']['working_days'])
                ->setCellValue('K'. $indx, '1')
                ->setCellValue('L'. $indx, '1')
                ->setCellValue('M'. $indx, $dados[$i]['Supplier']['code'])
                ->setCellValue('N'. $indx, $dados[$i]['Benefit']['code'])
                ->setCellValue('O'. $indx, $dados[$i]['CustomerUserItinerary']['card_number'])
                ->setCellValue('P'. $indx, $dados[$i]['CustomerUserItinerary']['unit_price'])
                ->setCellValue('Q'. $indx, $dados[$i]['OrderItem']['working_days'])
                ->setCellValue('R'. $indx, 'Dia')
                ->setCellValue('S'. $indx, $quantity)
                ->setCellValue('T'. $indx, $dados[$i]['OrderItem']['var'])
                ->setCellValue('U'. $indx, $dados[$i]['OrderItem']['subtotal'])
                ->setCellValue('V'. $indx, '-')
                ->setCellValue('W'. $indx, $dados[$i]['SalaryRange']['range'])
                ->setCellValue('X'. $indx, $dados[$i][0]['cep'])
                ->setCellValue('Y'. $indx, $dados[$i][0]['endereco'])
                ->setCellValue('Z'. $indx, $dados[$i][0]['numero'])
                ->setCellValue('AA'. $indx, $dados[$i][0]['complemento'])
                ->setCellValue('AB'. $indx, $dados[$i][0]['bairro'])
                ->setCellValue('AC'. $indx, $dados[$i][0]['cidade'])
                ->setCellValue('AD'. $indx, $dados[$i][0]['estado'])
                ->setCellValue('AE'. $indx, $dados[$i][0]['cep_empresa'])
                ->setCellValue('AF'. $indx, $dados[$i][0]['endereco_empresa'])
                ->setCellValue('AG'. $indx, $dados[$i][0]['numero_empresa'])
                ->setCellValue('AH'. $indx, $dados[$i][0]['complemento_empresa'])
                ->setCellValue('AI'. $indx, $dados[$i][0]['bairro_empresa'])
                ->setCellValue('AJ'. $indx, $dados[$i][0]['cidade_empresa'])
                ->setCellValue('AK'. $indx, $dados[$i][0]['estado_empresa'])
                ->setCellValue('AL'. $indx, $dados[$i][0]['cep'])
                ->setCellValue('AM'. $indx, $dados[$i][0]['endereco'])
                ->setCellValue('AN'. $indx, $dados[$i][0]['numero'])
                ->setCellValue('AO'. $indx, $dados[$i][0]['complemento'])
                ->setCellValue('AP'. $indx, $dados[$i][0]['bairro'])
                ->setCellValue('AQ'. $indx, $dados[$i][0]['cidade'])
                ->setCellValue('AR'. $indx, $dados[$i][0]['estado'])
                ->setCellValue('AS'. $indx, $dados[$i][0]['tipo_conta'])
                ->setCellValue('AT'. $indx, $dados[$i][0]['nome_banco'])
                ->setCellValue('AU'. $indx, $dados[$i][0]['codigo_banco'])
                ->setCellValue('AV'. $indx, $dados[$i][0]['numero_conta'])
                ->setCellValue('AW'. $indx, $dados[$i][0]['digito_conta'])
                ->setCellValue('AX'. $indx, $dados[$i][0]['numero_agencia'])
                ->setCellValue('AY'. $indx, $dados[$i][0]['digito_agencia'])
                ->setCellValue('AZ'. $indx, $dados[$i]['CustomerUser']['sexo'])
                ->setCellValue('BA'. $indx, $dados[$i]['MaritalStatus']['status'])
                ->setCellValue('BB'. $indx, $dados[$i]['CustomerUser']['email'])
                ->setCellValue('BC'. $indx, $dados[$i]['CustomerUser']['ddd_cel'])
                ->setCellValue('BD'. $indx, $dados[$i]['CustomerUser']['cel_sem_ddd'])
                ->setCellValue('BE'. $indx, $dados[$i]['CustomerPosition']['name'])
                ->setCellValue('BF'. $indx, $dados[$i]['Order']['id'])
                ->setCellValue('BG'. $indx, $dados[$i]['OrderStatus']['name'])
                ->setCellValue('BH'. $indx, $dados[$i]['Order']['order_period_from'].' a '.$dados[$i]['Order']['order_period_to'])
                ->setCellValue('BI'. $indx, $dados[$i]['Supplier']['code']);

            }
    }

    public function getOrder($spreadsheet, $dados)
    {
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $activeWorksheet->getStyle('D')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        $activeWorksheet->getStyle('E')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        $activeWorksheet->getStyle('P')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        $col = 'A';
        $activeWorksheet->setCellValue($col.'1', "CNPJ CLIENTE");$col++;
        $activeWorksheet->setCellValue($col.'1', "Matrícula ");$col++;
        $activeWorksheet->setCellValue($col.'1', "Nome");$col++;
        $activeWorksheet->setCellValue($col.'1', "CPF");$col++;
        $activeWorksheet->setCellValue($col.'1', "RG");$col++;
        $activeWorksheet->setCellValue($col.'1', "Orgão Expeditor");$col++;
        $activeWorksheet->setCellValue($col.'1', "Data De Nascimento");$col++;
        $activeWorksheet->setCellValue($col.'1', "Nome Da Mãe");$col++;
        $activeWorksheet->setCellValue($col.'1', "Departamenrto");$col++;
        $activeWorksheet->setCellValue($col.'1', "Dias Úteis");$col++;
        $activeWorksheet->setCellValue($col.'1', "Tipo Pedido");$col++;
        $activeWorksheet->setCellValue($col.'1', "Tipo de Benefício / Serviço");$col++;
        $activeWorksheet->setCellValue($col.'1', "Id(Código Operadora)");$col++;
        $activeWorksheet->setCellValue($col.'1', "Operadora");$col++;
        $activeWorksheet->setCellValue($col.'1', "Id(Código do Benefício / ítem)");$col++;
        $activeWorksheet->setCellValue($col.'1', "Número do Cartão (Vale Transporte)");$col++;
        $activeWorksheet->setCellValue($col.'1', "VlUnit");$col++;
        $activeWorksheet->setCellValue($col.'1', "Qtde");$col++;
        $activeWorksheet->setCellValue($col.'1', "Por Pedido / Dia?");$col++;
        $activeWorksheet->setCellValue($col.'1', "Qtde do Benefício por Dia");$col++;
        $activeWorksheet->setCellValue($col.'1', "Var");$col++;
        $activeWorksheet->setCellValue($col.'1', "Total");$col++;
        $activeWorksheet->setCellValue($col.'1', "Número Do Sic Uso Exclusivo De Curitiba-Pr");$col++;
        $activeWorksheet->setCellValue($col.'1', "Faixa Salarial do Colaborador");$col++;
        $activeWorksheet->setCellValue($col.'1', "Cep Residência Colaborador");$col++;
        $activeWorksheet->setCellValue($col.'1', "Rua Residência");$col++;
        $activeWorksheet->setCellValue($col.'1', "Número Residência");$col++;
        $activeWorksheet->setCellValue($col.'1', "Complemento Residência");$col++;
        $activeWorksheet->setCellValue($col.'1', "Bairro Residência");$col++;
        $activeWorksheet->setCellValue($col.'1', "Cidade Residência");$col++;
        $activeWorksheet->setCellValue($col.'1', "Estado Residência");$col++;
        $activeWorksheet->setCellValue($col.'1', "Cep Do Trabalho");$col++;
        $activeWorksheet->setCellValue($col.'1', "Rua Trabalho");$col++;
        $activeWorksheet->setCellValue($col.'1', "Número Trabalho");$col++;
        $activeWorksheet->setCellValue($col.'1', "Complemento Trabalho");$col++;
        $activeWorksheet->setCellValue($col.'1', "Bairro Trabalho");$col++;
        $activeWorksheet->setCellValue($col.'1', "Cidade Trabalho");$col++;
        $activeWorksheet->setCellValue($col.'1', "Estado Trabalho");$col++;
        $activeWorksheet->setCellValue($col.'1', "Cep De Entrega");$col++;
        $activeWorksheet->setCellValue($col.'1', "Rua Entrega");$col++;
        $activeWorksheet->setCellValue($col.'1', "Número De Entrega");$col++;
        $activeWorksheet->setCellValue($col.'1', "Complemento De Entrega");$col++;
        $activeWorksheet->setCellValue($col.'1', "Bairro De Entrega");$col++;
        $activeWorksheet->setCellValue($col.'1', "Cidade De Entrega");$col++;
        $activeWorksheet->setCellValue($col.'1', "Estado De Entrega");$col++;
        $activeWorksheet->setCellValue($col.'1', "Tipo De Conta");$col++;
        $activeWorksheet->setCellValue($col.'1', "Tipo Chave");$col++;
        $activeWorksheet->setCellValue($col.'1', "Chave");$col++;
        $activeWorksheet->setCellValue($col.'1', "Nome do banco");$col++;
        $activeWorksheet->setCellValue($col.'1', "Cod. Do Banco");$col++;
        $activeWorksheet->setCellValue($col.'1', "Agencia");$col++;
        $activeWorksheet->setCellValue($col.'1', "Digito Da Agencia");$col++;
        $activeWorksheet->setCellValue($col.'1', "Conta");$col++;
        $activeWorksheet->setCellValue($col.'1', "Digito Da Conta");$col++;
        $activeWorksheet->setCellValue($col.'1', "Gênero");$col++;
        $activeWorksheet->setCellValue($col.'1', "Estado Civil");$col++;
        $activeWorksheet->setCellValue($col.'1', "E-Mail");$col++;
        $activeWorksheet->setCellValue($col.'1', "Ddd - Telefone ");$col++;
        $activeWorksheet->setCellValue($col.'1', "Número Do Telefone ");$col++;
        $activeWorksheet->setCellValue($col.'1', "Cargo");$col++;
        $activeWorksheet->setCellValue($col.'1', "Pedido");$col++;
        $activeWorksheet->setCellValue($col.'1', "Data Geração Ped");$col++;
        $activeWorksheet->setCellValue($col.'1', "Status Pedido");$col++;
        $activeWorksheet->setCellValue($col.'1', "RAZAO SOCIAL CLIENTE");$col++;
        $activeWorksheet->setCellValue($col.'1', "Repasse");$col++;
        $activeWorksheet->setCellValue($col.'1', "GE");$col++;
        $activeWorksheet->setCellValue($col.'1', "GE-CNPJ");$col++;
        $activeWorksheet->setCellValue($col.'1', "Código");$col++;
        $activeWorksheet->setCellValue($col.'1', "Economia");$col++;
        $activeWorksheet->setCellValue($col.'1', "id");$col++;
        $activeWorksheet->setCellValue($col.'1', "Liberação do crédito");$col++;
        $activeWorksheet->setCellValue($col.'1', "Período Inicio");$col++;
        $activeWorksheet->setCellValue($col.'1', "Período Fim");$col++;
        $activeWorksheet->setCellValue($col.'1', "Elegível para gestão econômico");$col++;
        $activeWorksheet->setCellValue($col.'1', "Margem de segurança");$col++;
        $activeWorksheet->setCellValue($col.'1', "Incluir qtde. mínina diária");$col++;
        $activeWorksheet->setCellValue($col.'1', "Tipos de GE");$col++;
        $activeWorksheet->setCellValue($col.'1', "Compra Operadora");$col++;
        $activeWorksheet->setCellValue($col.'1', "Primeira Compra");$col++;
        $activeWorksheet->setCellValue($col.'1', "Calculo repasse economia");$col++;
        $activeWorksheet->setCellValue($col.'1', "Calculo repasse pedido compra");$col++;
        $activeWorksheet->setCellValue($col.'1', "Status Operadora");$col++;
        $activeWorksheet->setCellValue($col.'1', "Motivo Processamento");$col++;
        $activeWorksheet->setCellValue($col.'1', "Matricula Operadora");$col++;
        $activeWorksheet->setCellValue($col.'1', "Data Entrega");$col++;
        $activeWorksheet->setCellValue($col.'1', "Gestão Eficiente");$col++;
        $activeWorksheet->setCellValue($col.'1', "Gestão Eficiente - Data Alteração");$col++;
        $activeWorksheet->setCellValue($col.'1', "Gestão Eficiente - Usuário Alteração");$col++;
        $activeWorksheet->setCellValue($col.'1', "Gestão Eficiente - Observação");$col++;

        $indx = 1;
        $total = 0;
        for ($i = 0; $i < count($dados); $i++) {
            $total += $dados[$i]["OrderItem"]["subtotal_not_formated"];

            $tipo_ge = "";
            if ($dados[$i]['Customer']['tipo_ge'] == '1') {
                $tipo_ge = 'GE pré pago';
            } elseif ($dados[$i]['Customer']['tipo_ge'] == '2') {
                $tipo_ge = 'GE Pós pago';
            } elseif ($dados[$i]['Customer']['tipo_ge'] == '3') {
                $tipo_ge = 'GE garantido';
            }

      $tipo_pedido = "";
            if ($dados[$i]['Order']['is_partial'] == '1') {
                $tipo_pedido = 'Automático';
            } elseif ($dados[$i]['Order']['is_partial'] == '2') {
                $tipo_pedido = 'Emissão';
            } elseif ($dados[$i]['Order']['is_partial'] == '3') {
                $tipo_pedido = 'Importação';
            }
             elseif ($dados[$i]['Order']['is_partial'] == '4') {
                $tipo_pedido = 'PIX';
            }

            $porcentagem_margem_seguranca = $dados[$i]['Customer']['porcentagem_margem_seguranca'];
            if (empty($porcentagem_margem_seguranca)) {
                $porcentagem_margem_seguranca = "0";
            }

            $indx++;

            $col = 'A';

            $activeWorksheet->setCellValue($col . $indx, $dados[$i]["Customer"]["documento"]);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]["CustomerUser"]["matricula"]);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['name']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['cpf']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['rg']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['emissor_rg']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['data_nascimento']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['nome_mae']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerDepartment']['name']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['working_days']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $tipo_pedido);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['BenefitType']['name']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Supplier']['id']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Supplier']['nome_fantasia']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Benefit']['code']);$col++;
            $activeWorksheet->setCellValueExplicit($col . $indx, $dados[$i]['CustomerUserItinerary']['card_number'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUserItinerary']['unit_price']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['working_days']);$col++;
            $activeWorksheet->setCellValue($col . $indx, 'Dia');$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUserItinerary']['quantity']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['var']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['subtotal']);$col++;
            $activeWorksheet->setCellValue($col . $indx, '-');$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['SalaryRange']['range']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['cep']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['endereco']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['numero']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['complemento']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['bairro']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['cidade']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['estado']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['cep_empresa']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['endereco_empresa']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['numero_empresa']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['complemento_empresa']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['bairro_empresa']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['cidade_empresa']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['estado_empresa']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['cep']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['endereco']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['numero']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['complemento']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['bairro']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['cidade']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['estado']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['tipo_conta']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['pix_type']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['pix_id']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['nome_banco']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['codigo_banco']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['numero_conta']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['digito_conta']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['numero_agencia']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i][0]['digito_agencia']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['sexo']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['MaritalStatus']['status']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['email']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['ddd_cel']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUser']['cel_sem_ddd']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerPosition']['name']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['id']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['created']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderStatus']['name']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Customer']['nome_primario']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['transfer_fee']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['EconomicGroups']['razao_social']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['EconomicGroups']['document']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Customer']['codigo_associado']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['saldo']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['id']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['credit_release_date']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['order_period_from']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['order_period_to']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Customer']['flag_gestao_economico'] == 'S' ? 'Sim' : 'Não');$col++;
            $activeWorksheet->setCellValue($col . $indx, $porcentagem_margem_seguranca);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Customer']['qtde_minina_diaria'] == 2 ? 'Sim' : 'Não');$col++;
            $activeWorksheet->setCellValue($col . $indx, $tipo_ge);$col++;
            $activeWorksheet->setCellValue($col . $indx, number_format(($dados[$i]['OrderItem']['subtotal_not_formated'] - $dados[$i]['OrderItem']['saldo_not_formated']), 2, ',', '.'));$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']["primeiro_pedido"] == "N" ? "Não" : "Sim");$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['saldo_transfer_fee']);$col++;
            $activeWorksheet->setCellValue($col . $indx, number_format($dados[$i]['OrderItem']['transfer_fee_not_formated'] - $dados[$i]['Order']['saldo_transfer_fee_not_formated'], 2, ',', '.'));$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['status_processamento']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['motivo_processamento']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['CustomerUserItinerary']['matricula']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['OrderItem']['data_entrega']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['pedido_complementar']  == 1 ? 'S' : 'N');$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['updated_ge']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['UpdatedGe']['name']);$col++;
            $activeWorksheet->setCellValue($col . $indx, $dados[$i]['Order']['observation_ge']);$col++;
        }
    }

    public function getProcessamento($spreadsheet, $dados)
    {
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $activeWorksheet
        ->setCellValue('A1', "Data Criação Ped")
        ->setCellValue('B1', "Pedido")
        ->setCellValue('C1', "Código Cliente")
        ->setCellValue('D1', "CNPJ CLIENTE")
        ->setCellValue('E1', "Razão Social")
        ->setCellValue('F1', "Status Pedido")
        ->setCellValue('G1', "Nome")
        ->setCellValue('H1', "Matrícula")
        ->setCellValue('I1', "CPF")
        ->setCellValue('J1', "Cartão")
        ->setCellValue('K1', "Dias Úteis")
        ->setCellValue('L1', "Id(Código Operadora)")
        ->setCellValue('M1', "Operadora")
        ->setCellValue('N1', "Id(Código do Benefício / ítem)")
        ->setCellValue('O1', "Valor por dia")
        ->setCellValue('P1', "Valor unitario")
        ->setCellValue('Q1', "Qtde do Benefício por Dia")
        ->setCellValue('R1', "Subtotal")
        ->setCellValue('S1', "Repasse")
        ->setCellValue('T1', "Taxa ADM")
        ->setCellValue('U1', "Economia")
        ->setCellValue('V1', "Compra Operadora")
        ->setCellValue('w1', "Departamento")
        ->setCellValue('X1', "Centro de Custo")
        ->setCellValue('Y1', " CNPJ Grupo Economico")
        ->setCellValue('Z1', "Nome Grupo Economicoo")
        ->setCellValue('AA1', "Compra Operadora")
        ->setCellValue('AB1', "Status Processamento")
        ->setCellValue('AC1', "Motivo Processamento")
        ->setCellValue('AC1', "Tipo Pedido")
        ->setCellValue('AC1', "Tipo de Benefício / Serviço");

        
        $indx = 1;
        $total = 0;
        for ($i = 0; $i < count($dados); $i++) {
            $total += $dados[$i]["OrderItem"]["subtotal_not_formated"];
            $valor_unit = ($dados[$i]['OrderItem']['manual_quantity'] > 0 ? $dados[$i]['OrderItem']['price_per_day_not_formated'] / $dados[$i]['OrderItem']['manual_quantity'] : $dados[$i]['OrderItem']['price_per_day']);
            $indx++;

            $tipo_pedido = "";
            if ($dados[$i]['Order']['is_partial'] == '1') {
                $tipo_pedido = 'Automático';
            } elseif ($dados[$i]['Order']['is_partial'] == '2') {
                $tipo_pedido = 'Emissão';
            } elseif ($dados[$i]['Order']['is_partial'] == '3') {
                $tipo_pedido = 'Importação';
            }
             elseif ($dados[$i]['Order']['is_partial'] == '4') {
                $tipo_pedido = 'PIX';
            }

            $activeWorksheet
                ->setCellValue('A'. $indx, $dados[$i]['Order']['created'])
                ->setCellValue('B'. $indx, $dados[$i]['Order']['id'])
                ->setCellValue('C'. $indx, $dados[$i]['Customer']['codigo_associado'])
                ->setCellValue('D'. $indx, $dados[$i]["Customer"]["documento"])
                ->setCellValue('E'. $indx, $dados[$i]['Customer']['nome_primario'])
                ->setCellValue('F'. $indx, $dados[$i]['Status']['name'])
                ->setCellValue('G'. $indx, $dados[$i]['CustomerUser']['name'])
                ->setCellValue('H'. $indx, $dados[$i]['CustomerUser']['matricula'])
                ->setCellValue('I'. $indx, $dados[$i]['CustomerUser']['cpf'])
                ->setCellValue('J'. $indx, $dados[$i]['CustomerUserItinerary']['card_number'])
                ->setCellValue('K'. $indx, $dados[$i]['OrderItem']['working_days'])
                ->setCellValue('L'. $indx, $dados[$i]['Supplier']['id'])
                ->setCellValue('M'. $indx, $dados[$i]['Supplier']['nome_fantasia'])
                ->setCellValue('N'. $indx, $dados[$i]['Benefit']['code'].'/'.$dados[$i]['Benefit']['name'])
                ->setCellValue('O'. $indx, $dados[$i]['OrderItem']['price_per_day'])
                ->setCellValue('P'. $indx, $valor_unit)
                ->setCellValue('Q'. $indx, $dados[$i]['OrderItem']['manual_quantity'])
                ->setCellValue('R'. $indx, $dados[$i]['OrderItem']['subtotal'])
                ->setCellValue('S'. $indx, $dados[$i]['OrderItem']['transfer_fee'])
                ->setCellValue('T'. $indx, $dados[$i]['OrderItem']['commission_fee'])
                ->setCellValue('U'. $indx, $dados[$i]['OrderItem']['saldo'])
                ->setCellValue('V'. $indx, number_format(($dados[$i]['OrderItem']['subtotal_not_formated'] - $dados[$i]['OrderItem']['saldo_not_formated']), 2, ',', '.'))
                ->setCellValue('W'. $indx, $dados[$i]['CustomerDepartments']['name'])
                ->setCellValue('X'. $indx, $dados[$i]['CostCenter']['name'])
                ->setCellValue('Y'. $indx, $dados[$i]['EconomicGroups']['document'])
                ->setCellValue('Z'. $indx, $dados[$i]['EconomicGroups']['name'])
                ->setCellValue('AA'. $indx, number_format(($dados[$i]['OrderItem']['subtotal_not_formated'] - $dados[$i]['OrderItem']['saldo_not_formated']), 2, ',', '.'))
                ->setCellValue('AB'. $indx, $dados[$i]['OrderItem']['status_processamento'])
                ->setCellValue('AC'. $indx, $dados[$i]['OrderItem']['motivo_processamento'])
                ->setCellValue('AD' . $indx, $tipo_pedido)
                ->setCellValue('AE'. $indx, $dados[$i]['BenefitType']['name']);
        }
    }

    public function getBeneficiario($spreadsheet, $dados)
{
   

    $activeWorksheet = $spreadsheet->getActiveSheet();

    $headers = [
       
        'A1' => "Status(beneficiário)",
        'B1' => "Nome(beneficiário)",
        'C1' => "Matricula(beneficiário)",
        'D1' => "Email(beneficiário)",
        'E1' => "Telefone(beneficiário)",
        'F1' => "Celular(beneficiário)",
        'G1' => "CPF(beneficiário)",
        'H1' => "RG(beneficiário)",
        'I1' => "Emissor(beneficiário)",
        'J1' => "Estado Emissor(beneficiário)",
        'K1' => "Nome da Mãe(beneficiário)",
        'L1' => "Sexo(beneficiário)",
        'M1' => "Data Nascimento(beneficiário)",
        'N1' => "Departamento(beneficiário)",
        'O1' => "Cargo(beneficiário)",
        'P1' => "Centro de Custo(beneficiário)",
        'Q1' => "Salário(beneficiário)",
        'R1' => "Estado Civil(beneficiário)",
        'S1' => "Empresas do Grupo econômico(beneficiário)",
        //'T1' => "Observações(beneficiário)",
        'U1' => "Nome (Grupo Econômico)",
        'V1' => "CNPJ (Grupo Econômico)",
        'W1' => "COD Benefício",
        'X1' => "Benefício(Benefício)",
        'Y1' => "Dias Úteis(Benefício)",
        'Z1' => "N° Cartão(Benefício)",
        'AA1' => "Quantidade(Benefício)",
        'AB1' => "Valor Unitario(Benefício)",
        'AC1' => "Valor por dia(Benefício)",
    ];

    foreach ($headers as $cell => $text) {
        $activeWorksheet->setCellValue($cell, $text);
    }

    $indx = 1;
    $chunkSize = 100; // Tamanho do lote
    $totalRows = count($dados);

    for ($i = 0; $i < $totalRows; $i += $chunkSize) {
        $chunk = array_slice($dados, $i, $chunkSize);

        foreach ($chunk as $data) {
            $indx++;

            $activeWorksheet
            ->setCellValue('A' . $indx, ($data['CustomerUser']['status_id'] == 1) ? 'Ativo' : (($data['CustomerUser']['status_id'] == 2) ? 'Inativo' : ''))
            ->setCellValue('B' . $indx, $data['CustomerUser']['name'] ?? '')
                ->setCellValue('C' . $indx, $data['CustomerUser']['matricula'] ?? '')
                ->setCellValue('D' . $indx, $data['CustomerUser']['email'] ?? '')
                ->setCellValue('E' . $indx, $data['CustomerUser']['tel'] ?? '')
                ->setCellValue('F' . $indx, $data['CustomerUser']['cel'] ?? '')
                ->setCellValue('G' . $indx, $data['CustomerUser']['cpf'] ?? '')
                ->setCellValue('H' . $indx, $data['CustomerUser']['rg'] ?? '')
                ->setCellValue('I' . $indx, $data['CustomerUser']['emissor_rg'] ?? '')
                ->setCellValue('J' . $indx, $data['CustomerUser']['emissor_estado'] ?? '')
                ->setCellValue('K' . $indx, $data['CustomerUser']['nome_mae'] ?? '')
                ->setCellValue('L' . $indx, $data['CustomerUser']['sexo'] ?? '')
                ->setCellValue('M' . $indx, $data['CustomerUser']['data_nascimento'] ?? '')
                ->setCellValue('N' . $indx, $data['CustomerDepartment']['name'] ?? '')
                ->setCellValue('O' . $indx, $data['CustomerPosition']['name'] ?? '')
                ->setCellValue('P' . $indx, $data['CostCenter']['name'] ?? '')
                ->setCellValue('Q' . $indx, $data['SalaryRange']['range'] ?? '')
                ->setCellValue('R' . $indx, $data['MaritalStatus']['status'] ?? '')
                ->setCellValue('S' . $indx, $data['CustomerUser']['economic_group_id'] ?? '');
               // ->setCellValue('T' . $indx, $data['CustomerUser']['observation'] ?? '');

            if (!empty($data['EconomicGroup'])) {
                $activeWorksheet
                    ->setCellValue('U' . $indx, $data['EconomicGroup']['name'] ?? '')
                    ->setCellValue('V' . $indx, $data['EconomicGroup']['document'] ?? '');
            }

            if (!empty($data['CustomerUserItinerary'])) {
                foreach ($data['CustomerUserItinerary'] as $itinerary) {
                    $activeWorksheet
                        ->setCellValue('W' . $indx, $itinerary['benefit_code'] ?? '')
                        ->setCellValue('X' . $indx, $itinerary['benefit_name'] ?? '')
                        ->setCellValue('Y' . $indx, $itinerary['working_days'] ?? '')
                        ->setCellValue('Z' . $indx, $itinerary['card_number'] ?? '')
                        ->setCellValue('AA' . $indx, $itinerary['quantity'] ?? '')
                        ->setCellValue('AB' . $indx, $itinerary['unit_price'] ?? '')
                        ->setCellValue('AC' . $indx, $itinerary['price_per_day'] ?? '');
                }
            }
        }
    }
}

    


    public function getProposal($spreadsheet, $dados)
    {
        if (is_array($dados)) {
            $activeWorksheet = $spreadsheet->getActiveSheet();

            $activeWorksheet
                ->setCellValue('A1', "Data da Proposta")
                ->setCellValue('B1', "Data da previsão de fechamento ")
                ->setCellValue('C1', "Data do fechamento")
                ->setCellValue('D1', "TPP")
                ->setCellValue('E1', "Taxa administrativa VT")
                ->setCellValue('F1', "Taxa de entrega VT")
                ->setCellValue('G1', "PGE* VT ")
                ->setCellValue('H1', "Qtde de Colaboradores VT")
                ->setCellValue('I1', "Valor por colaborador VT")
                ->setCellValue('J1', "Total por colaborador VT")
                ->setCellValue('K1', "Taxa administrativa VR")
                ->setCellValue('L1', "Taxa de entrega VR")
                ->setCellValue('M1', "Qtde de Colaboradores VR")
                ->setCellValue('N1', "Valor por colaborador VR")
                ->setCellValue('O1', "Total por colaborador VR")
                ->setCellValue('P1', "Taxa administrativa VC")
                ->setCellValue('Q1', "Taxa de entrega VC")
                ->setCellValue('R1', "Qtde de Colaboradores VC ")
                ->setCellValue('S1', "Valor por colaborador VC")
                ->setCellValue('T1', "Total por colaborador VC")
                ->setCellValue('U1', "Taxa administrativa CM")
                ->setCellValue('V1', "Taxa de entrega CM")
                ->setCellValue('W1', "Qtde de Colaboradores CM")
                ->setCellValue('X1', "Valor por colaborador CM")
                ->setCellValue('Y1', "Total por colaboradorCM")
                ->setCellValue('Z1', "Taxa administrativa-Saúde")
                ->setCellValue('AA1', "Taxa de entrega-Saúde")
                ->setCellValue('AB1', "Qtde de Colaboradores-Saúde")
                ->setCellValue('AC1', "Valor por colaborador-Saúde")
                ->setCellValue('AD1', "Total por colaborador-Saúde")
                ->setCellValue('AE1', "Taxa administrativa-Previdenciário")
                ->setCellValue('AF1', "Taxa de entrega-Previdenciário")
                ->setCellValue('AG1', "Qtde de Colaboradores-Previdenciário")
                ->setCellValue('AH1', "Valor por colaborador-Previdenciário")
                ->setCellValue('AI1', "Total por colaborador-Previdenciário")
                ->setCellValue('AJ1', "Total geral");

            
            $indx = 1;
            $total = 0;

            //debug($dados); die;
            foreach ($dados as $data) { // Use um foreach para percorrer o array
                $indx++;

                // Aqui você pode acessar os dados diretamente de $data
                $activeWorksheet->setCellValue('A'. $indx, $data["Proposal"]["date"])
                    ->setCellValue('B'. $indx, $data["Proposal"]["expected_closing_date"])
                    ->setCellValue('C'. $indx, $data['Proposal']['closing_date'])
                    ->setCellValue('D'. $indx, $data['Proposal']['tpp'])
                    ->setCellValue('E'. $indx, $data['Proposal']['transport_adm_fee'])
                    ->setCellValue('F'. $indx, $data['Proposal']['transport_deli_fee'])
                    ->setCellValue('G'. $indx, $data['Proposal']['management_feel'])
                    ->setCellValue('H'. $indx, $data['Proposal']['transport_workers_qty'])
                    ->setCellValue('I'. $indx, $data['Proposal']['transport_workers_price'])
                    ->setCellValue('J'. $indx, $data['Proposal']['transport_workers_price_total'])
                    ->setCellValue('K'. $indx, $data['Proposal']['meal_adm_fee'])
                    ->setCellValue('L'. $indx, $data['Proposal']['meal_deli_fee'])
                    ->setCellValue('M'. $indx, $data['Proposal']['meal_workers_qty'])
                    ->setCellValue('N'. $indx, $data['Proposal']['meal_workers_price'])
                    ->setCellValue('O'. $indx, $data['Proposal']['meal_workers_price_total'])
                    ->setCellValue('P'. $indx, $data['Proposal']['fuel_adm_fee'])
                    ->setCellValue('Q'. $indx, $data['Proposal']['fuel_deli_fee'])
                    ->setCellValue('R'. $indx, $data['Proposal']['fuel_workers_qty'])
                    ->setCellValue('S'. $indx, $data['Proposal']['fuel_workers_price'])
                    ->setCellValue('T'. $indx, $data['Proposal']['fuel_workers_price_total'])
                    ->setCellValue('U'. $indx, $data['Proposal']['multi_card_adm_fee'])
                    ->setCellValue('V'. $indx, $data['Proposal']['multi_card_deli_fee'])
                    ->setCellValue('W'. $indx, $data['Proposal']['multi_card_workers_qty'])
                    ->setCellValue('X'. $indx, $data['Proposal']['multi_card_workers_price'])
                    ->setCellValue('Y'. $indx, $data['Proposal']['multi_card_workers_price_total'])
                    ->setCellValue('Z'. $indx, $data['Proposal']['saude_card_adm_fee'])
                    ->setCellValue('AA'. $indx, $data['Proposal']['saude_card_deli_fee'])
                    ->setCellValue('AB'. $indx, $data['Proposal']['saude_card_workers_qty'])
                    ->setCellValue('AC'. $indx, $data['Proposal']['saude_card_workers_price'])
                    ->setCellValue('AD'. $indx, $data['Proposal']['saude_card_workers_price_total'])
                    ->setCellValue('AE'. $indx, $data['Proposal']['prev_card_adm_fee'])
                    ->setCellValue('AF'. $indx, $data['Proposal']['prev_card_deli_fee'])
                    ->setCellValue('AG'. $indx, $data['Proposal']['prev_card_workers_qty'])
                    ->setCellValue('AH'. $indx, $data['Proposal']['prev_card_workers_price'])
                    ->setCellValue('AI'. $indx, $data['Proposal']['prev_card_workers_price_total'])
                    ->setCellValue('AJ'. $indx, $data['Proposal']['total_price']);
            }
        } 
    }

    public function getPedidosBeneficiariosPIX($objPHPExcel, $dados)
    {
        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Conta de Origem"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Agência de Origem"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Banco do Favorecido"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Conta do Favorecido"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Agência do Favorecido"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo de Conta do Favorecido"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Mesma Titularidade"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Nome do Favorecido"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CPF/CNPJ do Favorecido"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Identificação no extrato"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Valor"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo Chave"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Chave PIX"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo de transferência"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data de pagamento"); $col++;

        foreach ($dados as $key => $dado) {
            $col = 'A';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), "0050"); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), "569539-7"); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["k"]["code"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado[0]["conta"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado[0]["agencia"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["t"]["description"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ""); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["u"]["name"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($col . ($key+2), $dado["u"]["cpf"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ""); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["i"]["subtotal"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["b"]["pix_type"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($col . ($key+2), $dado["b"]["pix_id"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ""); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ""); $col++;
        }
    }

    public function getPedidosNfsRelatorio($objPHPExcel, $dados)
    {
        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Status"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Código"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data de criação"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cliente"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data Pagamento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data Finalização"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Subtotal"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Repasse"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Taxa"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Desconto"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "TPP"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Fee Economia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cliente"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Economia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Total"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Nome Documento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Arquivo"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Antecipado"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cobrança Cancelada"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Pedido Cancelado"); $col++;

        foreach ($dados as $key => $dado) {     
            $fee_economia = 0;  // Inicializa com valor padrão
            $vl_economia = isset($dado["Order"]["total_balances"]) ? $dado["Order"]["total_balances"] : 0;
            $fee_saldo = isset($dado["Order"]["fee_saldo_not_formated"]) ? $dado["Order"]["fee_saldo_not_formated"] : 0;
            
            if ($fee_saldo != 0 && $vl_economia != 0) {
                $fee_economia = (($fee_saldo / 100) * ($vl_economia));
            }
            
            $vl_economia = ($vl_economia - $fee_economia);
            $total_economia = ($vl_economia + $fee_economia);
            
            $col = 'A';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Status"]["name"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Customer"]["codigo_associado"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["created"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["id"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Customer"]["nome_primario"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Income"]["data_pagamento"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["end_date"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["subtotal"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["transfer_fee"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["commission_fee"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["desconto"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["tpp_fee"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), number_format($fee_economia,2,',','.')); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), number_format($vl_economia,2,',','.')); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), number_format($total_economia,2,',','.')); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Order"]["total"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderDocument"]["name"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderDocument"]["file_name"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Customer']['emitir_nota_fiscal'] == 'A' ? "Sim" : "Não"); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Income']['status_id'] == 18 ? "Sim" : "Não"); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['Order']['status_id'] == 94 ? "Sim" : "Não"); $col++;
        }
    }

    public function getPedidoMovimentacoes($objPHPExcel, $dados)
    {
        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CPF"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Código Benefício"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Total"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Pedido Operadora"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Item ID"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Observação"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Beneficiário"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Benefício"); $col++;
        
        foreach ($dados as $key => $dado) {
            $tipo = "";
            if ($dado["OrderBalance"]["tipo"] == '1') {
                $tipo = 'Credita conta';
            } elseif ($dado["OrderBalance"]["tipo"] == '2') {
                $tipo = 'Credita e Debita';
            } elseif ($dado["OrderBalance"]["tipo"] == '3') {
                $tipo = 'Somente Credita';
            } elseif ($dado["OrderBalance"]["tipo"] == '4') {
                $tipo = 'Saldo';
            } elseif ($dado["OrderBalance"]["tipo"] == '5') {
                $tipo = 'Bolsa de Crédito';
            } elseif ($dado["OrderBalance"]["tipo"] == '6') {
                $tipo = 'Somente Debita';
            }

            $col = 'A';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderBalance"]["document"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Benefit"]["code"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderBalance"]["total"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderBalance"]["pedido_operadora"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderItem"]["id"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $tipo); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["OrderBalance"]["observacao"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["CustomerUser"]["name"]); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado["Benefit"]["name"]); $col++;
        }
    }

    public function getLogBeneficiosRelatorio($objPHPExcel, $dados)
    {
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);

        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Nome"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Valor"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "De"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Até"); $col++;

        foreach ($dados as $key => $dado) {
            $col = 'A';

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['User']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['LogBenefits']['log_date']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['LogBenefits']['old_value']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['LogBenefits']['de']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $dado['LogBenefits']['ate']); $col++;
            $col++;
        }
    }

    public function getModeloImportacaoBeneficiarios($objPHPExcel, $dados)
    {
        $col = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CNPJ"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Matrícula"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Nome"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "CPF"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "RG"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Orgão Expeditor"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Data De Nascimento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Nome Da Mãe"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Departamento"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Dias Úteis"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo Pedido"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo de Benefício / Serviço"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Código Operadora"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Código do Benefício (Ìtem)"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número do Cartão (Vale Transporte)"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Valor Unitário"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Quantidade"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Por Pedido / Dia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Qtde do Benefício por Dia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Var"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Total"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número Do Sic Uso Exclusivo De Curitiba-Pr"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Faixa Salarial do Colaborador"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cep Residência Colaborador"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Rua Residência"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número Residência"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Complemento Residência"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Bairro Residência"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cidade Residência"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Estado Residência"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cep Do Trabalho"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Rua Trabalho"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número Trabalho"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Complemento Trabalho"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Bairro Trabalho"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cidade Trabalho"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Estado Trabalho"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cep De Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Rua Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número De Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Complemento De Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Bairro De Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cidade De Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Estado De Entrega"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo De Conta"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Nome do banco"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cod. Do Banco"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Agencia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Digito Da Agencia"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Conta"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Digito Da Conta"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Gênero"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Estado Civil"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "E-Mail"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Ddd - Telefone "); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Número Do Telefone "); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Cargo"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Tipo Chave"); $col++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.'1', "Chave Pix"); $col++;

        foreach ($dados as $key => $data) {
            $col = 'A';

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['Customer']['documento']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['matricula']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['cpf']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['rg']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['emissor_rg']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['data_nascimento']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['nome_mae']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerDepartment']['name']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['sexo']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['MaritalStatus']['status']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), $data['CustomerUser']['email']); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($key+2), ''); $col++;
        }
    }
}

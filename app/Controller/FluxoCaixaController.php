<?php

class FluxoCaixaController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['BankAccount', 'Outcome', 'Income'];

    public $paginate = [];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(30, 'leitura') ? '' : $this->redirect('/not_allowed');

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';
        $t = isset($_GET['t']) ? $_GET['t'] : [];

        // Força ser array, mesmo que só 1 valor
        if (!is_array($t)) {
            $t = [$t];
        }

        $data = [];
        $conta = [];
        $exportar = false;
        $saldo = 0;

        if (!empty($t) && $get_de != '' && $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            // Filtra apenas IDs válidos
            $ids_validos = array_filter($t, 'is_numeric');

            // Busca saldo inicial da primeira conta (ou múltiplas se quiser customizar depois)
            if (count($ids_validos) === 1) {
                $conta = $this->BankAccount->find('first', [
                    'conditions' => ['BankAccount.id' => $ids_validos[0], 'BankAccount.start_date <=' => $de]
                ]);
                $saldo = $conta['BankAccount']['initial_balance_not_formated'];
            }

            $de_anterior = date('Y-m-d', strtotime('-1 month ' . $de));
            $ate_anterior = date('Y-m-t', strtotime('-1 month ' . $ate));

            $buscaValorPagoDe = $this->Outcome->find('all', [
                'conditions' => [
                    "Outcome.data_pagamento BETWEEN '{$de_anterior}' AND '{$ate_anterior}'",
                    'Outcome.status_id' => 13,
                    !empty($ids_validos) ? ['Outcome.bank_account_id' => $ids_validos] : []
                ],
                'fields' => 'SUM(valor_pago) as valor_pago'
            ]);

            $buscaValorRecebidoDe = $this->Income->find('all', [
                'conditions' => [
                    "Income.data_pagamento BETWEEN '{$de_anterior}' AND '{$ate_anterior}'",
                    'Income.status_id' => 17,
                    !empty($ids_validos) ? ['Income.bank_account_id' => $ids_validos] : []
                ],
                'fields' => 'SUM(valor_pago) as valor_recebido'
            ]);

            $saldo += ($buscaValorRecebidoDe[0][0]['valor_recebido'] - $buscaValorPagoDe[0][0]['valor_pago']);

            // SQL filtros
            $filtroOutcome = '';
            $filtroIncome = '';

            if (!empty($ids_validos)) {
                $ids_str = implode(',', array_map('intval', $ids_validos));
                $filtroOutcome = "o.bank_account_id IN ({$ids_str}) AND ";
                $filtroIncome = "i.bank_account_id IN ({$ids_str}) AND ";
            }

            $data = $this->Outcome->query("
                SELECT 
                    s.name as status, 
                    b.name, 
                    'conta a pagar' AS tipo, 
                    o.data_pagamento, 
                    o.valor_pago as valor_total, 
                    '-' AS operador, 
                    o.name AS nome_conta, 
                    f.nome_fantasia AS nome, 
                    f.id as codigo,
                    f.nome_fantasia as supplier_nome_fantasia,
                    NULL as customer_nome_secundario,
                    NULL as order_id
                FROM outcomes o
                LEFT JOIN suppliers f ON f.id = o.supplier_id
                INNER JOIN statuses s ON s.id = o.status_id
                INNER JOIN bank_accounts b ON b.id = o.bank_account_id
                WHERE 
                    {$filtroOutcome}
                    o.data_pagamento BETWEEN '{$de}' AND '{$ate}' AND 
                    o.data_cancel = '1901-01-01' AND 
                    o.status_id = 13
                UNION
                SELECT 
                    s.name as status, 
                    b.name, 
                    'conta a receber' AS tipo, 
                    i.data_pagamento, 
                    i.valor_pago as valor_total, 
                    '+' AS operador, 
                    i.name AS nome_conta, 
                    c.nome_secundario AS nome, 
                    c.codigo_associado as codigo,
                    NULL as supplier_nome_fantasia,
                    c.nome_secundario as customer_nome_secundario,
                    i.order_id as order_id
                FROM incomes i
                LEFT JOIN customers c ON c.id = i.customer_id
                INNER JOIN statuses s ON s.id = i.status_id
                INNER JOIN bank_accounts b ON b.id = i.bank_account_id
                WHERE 
                    {$filtroIncome}
                    i.data_pagamento BETWEEN '{$de}' AND '{$ate}' AND 
                    i.status_id = 17 AND 
                    i.data_cancel = '1901-01-01'
                ORDER BY data_pagamento
            ");

            $exportar = true;
        }

        $conta_bancaria = $this->BankAccount->find('all', [
            'conditions' => ['BankAccount.status_id' => 1],
            'order' => ['BankAccount.name']
        ]);

        if (isset($_GET['exportar'])) {
            $nome = 'fluxo_caixa_' . date('Ymd_His') . '.xlsx';
            $this->ExcelGenerator->gerarExcelFluxo($nome, $data, $conta);
            $this->redirect('/files/excel/' . $nome);
        }

        $action = 'Fluxo de caixa';
        $this->set(compact('conta_bancaria', 'data', 'conta', 'exportar', 'saldo', 'action'));
    }
}

<?php
App::uses('BoletoItau', 'Lib');
class CnabController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Uploader', 'GerarTxtCaixa', 'GerarRemessaCnab', 'ExcelGenerator'];
    public $uses = ['Income', 'BankAccount', 'CnabLote', 'CnabItem', 'Status', 'Bank', 'ChargesHistory'];

    public $paginate = [
        'CnabLote' => ['limit' => 20, 'order' => ['CnabLote.remessa' => 'desc']]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(52, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Status.id' => [15,16], 'Income.cnab_gerado' => 2, 'Income.valor_total >' => 0], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado' => $_GET['q']]);
        }

        $buscar = false;
        if (!empty($_GET['t'])) {
            $buscar = true;
            $condition['and'] = array_merge($condition['and'], ['Income.bank_account_id' => $_GET['t']]);
        }

        if (!empty($_GET['c'])) {
            $buscar = true;
            $condition['and'] = array_merge($condition['and'], ['Income.cnab_gerado' => ($_GET['c'] == 3 ? '2' : $_GET['c'])]);
        }

        $get_de = isset($_GET["de"]) ? $_GET["de"] : '01/'.date('m/Y');
        $get_ate = isset($_GET["ate"]) ? $_GET["ate"] : date('t/m/Y');
        
        if ($get_de != "" and $get_ate != "") {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));
                
            $dataBusca = 'vencimento';
            if (!empty($_GET['c'])) {
                if ($_GET['c'] == 3) {
                    $dataBusca = 'data_cobranca_criada';
                }
            }
            $condition['and'] = array_merge($condition['and'], ['Income.'.$dataBusca.' >=' => $de, 'Income.'.$dataBusca.' <=' => $ate]);
        }

        $data = [];
        if ($buscar) {
            $this->Income->unbindModel(['belongsTo' => ['UsuarioBaixa', 'UsuarioCancelamento', 'BankAccount', 'Revenue', 'CostCenter', 'Billing', 'BillingMonthlyPayment']]);
            $data = $this->Income->find('all', ['conditions' => $condition, 'order' => ['Income.vencimento' => 'asc', 'Customer.nome_primario' => 'asc']]);
        }

        $bancos = $this->BankAccount->find('list', ['order' => ['BankAccount.name' => 'asc']]);

        $action = 'Gerar arquivo Cnab';
        $breadcrumb = ['Financeiro' => '', 'Cnab' => '', 'Gerar arquivo Cnab' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'bancos'));
    }

    public function gerar_txt()
    {
        $this->Permission->check(52, "escrita") ? "" : $this->redirect("/not_allowed");

        if ($this->request->is(['post', 'put'])) {
            $ids = substr($_POST['ids'], 0, -1);

            $contas = $this->Income->getDadosBoleto($ids, 'all');

            if (!empty($contas)) {
                $remessas = $this->CnabLote->find('first', ['order' => ['CnabLote.id' => 'desc'], 'callbacks' => false]);
                $remessa = isset($remessas['CnabLote']) ? $remessas['CnabLote']['remessa'] + 1 : 1;

                $nome_arquivo = "S".$this->zerosEsq($remessa, 6).".REM";

                $data_pefin_lote = [
                    'CnabLote' => [
                        'status_id'                 => 46,
                        'arquivo'                   => $nome_arquivo,
                        'remessa'                   => $remessa,
                        'bank_id'                   => 1,
                        'user_creator_id'   => CakeSession::read('Auth.User.id')
                    ]
                ];

                $this->CnabLote->create();
                $this->CnabLote->save($data_pefin_lote);

                $Bancoob = new BoletoItau();
                $Bancoob->gerarRemessa($contas, $nome_arquivo, $remessa);

                $dados_itens = [];
                $historico = [];
                foreach ($contas as $conta) {
                    $historico[] = [
                        'ChargesHistory' => [
                            'income_id' => $conta['Income']['id'],
                            'text' => 'Lote cnab '.$this->CnabLote->id.' criado',
                            'user_creator_id' => CakeSession::read('Auth.User.id')
                        ]
                    ];

                    $dados_itens[] = [
                        'CnabItem' => [
                            'cnab_lote_id' => $this->CnabLote->id,
                            'income_id' => $conta['Income']['id'],
                            'status_id' => 48,
                            'user_creator_id' => CakeSession::read('Auth.User.id')
                        ]
                    ];

                    $this->Income->updateAll(
                        ['Income.status_id' => 16, 'Income.cnab_gerado' => 1, 'Income.cnab_lote_id' => $this->CnabLote->id, 'Income.cnab_num_sequencial' => "'".$this->zerosEsq($remessa, 6)."'", 'Income.user_updated_id' => CakeSession::read('Auth.User.id'), 'Income.updated' => 'current_timestamp'], //set
                        ['Income.id' => $conta['Income']['id']] //where
                    );
                }
                if (!empty($historico)) {
                    $this->ChargesHistory->saveMany($historico);
                }
                $this->CnabItem->saveMany($dados_itens);

                $this->Flash->set(__('Lote gerado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('Cadastro dos clientes incompletos - Favor verificar Cliente, Endereço de Cliente e Boletos da Conta Bancária das contas a receber.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->redirect(['action' => 'lotes']);
    }

    public function lotes()
    {
        $this->Permission->check(53, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = "";

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition .= "AND CnabLote.remessa LIKE '%".$_GET['q']."%' ";
        }

        if (isset($_GET['t']) and $_GET['t'] != "") {
            $condition .= "AND CnabLote.status_id = ".$_GET['t']." ";
        }

        //$data = $this->Paginator->paginate('CnabLote', $condition);

        $data = $this->CnabLote->query("
                    SELECT CnabLote.id, CnabLote.remessa, CnabLote.created, CnabLote.arquivo, 
                    Status.name, Status.label, Bank.name,
                    COUNT(ci.id) AS qtde, SUM(i.valor_total) AS valor_total
                    FROM cnab_lotes CnabLote
                        INNER JOIN banks Bank ON Bank.id = CnabLote.bank_id
                        LEFT JOIN statuses Status ON Status.id = CnabLote.status_id
                        INNER JOIN cnab_items ci ON ci.cnab_lote_id = CnabLote.id AND ci.data_cancel = '1901-01-01'
                        INNER JOIN incomes i ON i.id = ci.income_id 
                    WHERE CnabLote.data_cancel = '1901-01-01' 
                    ".$condition."
                    GROUP BY CnabLote.id 
                    ORDER BY CnabLote.created desc");

        if (isset($_GET['excel'])) {
            $nome = 'cnab_lotes';

            $this->ExcelGenerator->gerarExcelCnabLotes($nome, $data);
            $this->redirect("/files/excel/".$nome.".xlsx");
        }

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 12], 'order' => ['Status.name']]);

        $action = 'Lotes Boleto';
        $breadcrumb = ['Financeiro' => '', 'CNAB' => '', 'Lotes CNAB' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }

    public function detalhes_lote($id)
    {
        $this->Permission->check(53, "leitura") ? "" : $this->redirect("/not_allowed");

        $data = $this->CnabItem->find('all', ['conditions' => ['CnabItem.cnab_lote_id' => $id], 'recursive' => 2]);

        $action = 'Detalhes lote - '.str_pad($data[0]['CnabLote']['remessa'], 6, 0, STR_PAD_LEFT);
        $breadcrumb = ['Financeiro' => '', 'CNAB' => ['controller' => 'cnab', 'action' => 'lotes'], 'Lotes CNAB' => ['controller' => 'cnab', 'action' => 'lotes'], $action => ''];
        $this->set(compact("data", "action", "breadcrumb"));
    }

    public function download_remessa($arquivo)
    {
        $this->autoRender = false;
        header("Content-disposition: attachment; filename=".$arquivo);
        //header("Content-type: application/pdf:");
        header('Content-Type: text/plain; charset=ansi');

        readfile('files/cnab_txt/'.$arquivo);
    }

    public function zerosEsq($campo, $tamanho)
    {
        $campo = substr($campo, 0, $tamanho);

        $cp = str_pad($campo, $tamanho, 0, STR_PAD_LEFT);
        return $cp;
    }
}

<?php
App::uses('ApiBoleto', 'Lib/Credsis');
App::uses('PdfMerger', 'Lib');
class BoletosController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Uploader', 'GerarTxtCaixa', 'GerarRemessaCnab', 'ExcelGenerator', 'HtmltoPdf'];
    public $uses = ['Income', 'BankAccount', 'CnabLote', 'CnabItem', 'Status', 'Bank', 'Negativacao', 'Pefin', 'BillingNovaVida', 'ClienteMeProteja', 'Billing', 'Resale'];

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

        $condition = [
            "and" => [
                'Status.id' => [15,16],
                'Income.cnab_gerado' => 2,
                'Income.valor_total >' => 0,
                'Customer.cod_franquia' => CakeSession::read("Auth.User.resales"),
                'not' => [
                    'Income.bank_account_id' => 4
                ]
            ], 
            "or" => []
        ];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado' => $_GET['q']]);
        }

        $buscar = false;

        if (!empty($_GET['c'])) {
            $buscar = true;
            $condition['and'] = array_merge($condition['and'], ['Income.cnab_gerado' => ($_GET['c'] == 3 ? '2' : $_GET['c'])]);
        }

        if (!empty($_GET["f"])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.cod_franquia' => $_GET['f']]);
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
            $this->Income->unbindModel(['belongsTo' => ['UsuarioBaixa', 'UsuarioCancelamento', 'BankAccount', 'Revenue', 'CostCenter', 'Billing', 'BillingMonthlyPayment'], 'hasOne' => ['CnabItem', 'CnabItemSicoob']]);
            $data = $this->Income->find('all', ['conditions' => $condition, 'order' => ['Income.vencimento' => 'asc', 'Customer.nome_primario' => 'asc']]);
        }

        $bancos = $this->BankAccount->find('list', ['order' => ['BankAccount.name' => 'asc']]);
        $codFranquias = $this->Resale->find('all', ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], ['order' => 'Resale.nome_fantasia']]);
        $action = 'Emitir boletos';
        $breadcrumb = ['Financeiro' => '', 'Boletos' => '', 'Emitir boletos' => ''];
        $this->set(compact('data', 'action', 'bancos', 'codFranquias', 'breadcrumb'));
    }

    public function emitir()
    {
        $this->Permission->check(52, "escrita") ? "" : $this->redirect("/not_allowed");
        ini_set('max_execution_time', 900);
        if ($this->request->is(['post', 'put'])) {
            $ids = substr($_POST['ids'], 0, -1);

            $contas = $this->Income->find('all', ['conditions' => ['Income.id in ('.$ids.')'], 'order' => ['Income.vencimento' => 'asc', 'Customer.nome_primario' => 'asc'], 'recursive' => -1,
                "fields" => ["Income.*", 'Customer.*', 'BankAccount.*', 'BankTickets.*'],
                'joins' => [
                    [
                        'table' => 'customers',
                        'alias' => 'Customer',
                        'type' => 'inner',
                        'conditions' => [
                            'Customer.id = Income.customer_id', 'Customer.data_cancel' => '1901-01-01'
                        ]
                    ],
                    [
                        'table' => 'bank_accounts',
                        'alias' => 'BankAccount',
                        'type' => 'inner',
                        'conditions' => [
                            'BankAccount.id = Income.bank_account_id', 'BankAccount.data_cancel' => '1901-01-01'
                        ]
                    ],
                    [
                        'table' => 'bank_tickets',
                        'alias' => 'BankTickets',
                        'type' => 'inner',
                        'conditions' => [
                            'BankAccount.id = BankTickets.bank_account_id', 'BankTickets.data_cancel' => '1901-01-01'
                        ]
                    ]
                ]
            ]);

            if (!empty($contas)) {
                $remessas = $this->CnabLote->find('first', ['order' => ['CnabLote.id' => 'desc'], 'callbacks' => false]);
                $remessa = isset($remessas['CnabLote']) ? $remessas['CnabLote']['remessa'] + 1 : 1;
                $conta_bancaria = $this->BankAccount->find("first", ['conditions' => ['BankAccount.id' => $_POST['banco']]]);

                $nome_arquivo = "E".$this->zerosEsq($remessa, 6).".REM";

                $data_pefin_lote = [
                    'CnabLote' => [
                        'status_id'                 => 46,
                        'arquivo'                   => $nome_arquivo,
                        'remessa'                   => $remessa,
                        'bank_id'                   => 2,
                        'user_creator_id'   => CakeSession::read('Auth.User.id')
                    ]
                ];

                $this->CnabLote->create();
                $this->CnabLote->save($data_pefin_lote);

                $ApiBoleto = new ApiBoleto();
                foreach ($contas as $conta) {
                    $boleto = $ApiBoleto->gerarBoleto($conta);

                    if ($boleto['success']) {
                        $this->CnabItem->create();
                        $this->CnabItem->save([
                            'CnabItem' => [
                                'cnab_lote_id' => $this->CnabLote->id,
                                'income_id' => $conta['Income']['id'],
                                'id_web' => $boleto['obj']['titulos']['item']['idWeb'],
                                'status_id' => 48,
                                'user_creator_id' => CakeSession::read('Auth.User.id')
                            ]
                        ]);

                        $this->Income->id = $conta['Income']['id'];
                        $this->Income->save([
                            'Income' => [
                                'cnab_gerado' => 1,
                                'cnab_lote_id' => $this->CnabLote->id,
                                'user_updated_id' => CakeSession::read('Auth.User.id')
                            ]
                        ]);
                    } else {
                        $erros = $boleto['error']->erros->item;
                        if (!is_array($boleto['error']->erros->item)) {
                            $erros = [$boleto['error']->erros->item];
                        }

                        $message = '';
                        foreach ($erros as $erro) {
                            $message .= $erro->message.'<br>';
                        }

                        $this->Flash->set(__($message), ['params' => ['class' => "alert alert-danger"]]);

                        $this->redirect($this->referer());
                    }
                }

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
        $breadcrumb = ['Financeiro' => '', 'Boletos' => '', 'Lotes Boleto' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }

    public function detalhes_lote($id)
    {
        $this->Permission->check(53, "leitura") ? "" : $this->redirect("/not_allowed");

        $data = $this->CnabItem->find('all', ['conditions' => ['CnabItem.cnab_lote_id' => $id], 'recursive' => 2]);

        $action = 'Detalhes lote - '.str_pad($data[0]['CnabLote']['remessa'], 6, 0, STR_PAD_LEFT);
        $breadcrumb = ['Financeiro' => '', 'Boletos' => ['controller' => 'boletos', 'action' => 'lotes'], 'Lotes Boleto' => ['controller' => 'boletos', 'action' => 'lotes'], $action => ''];
        $this->set(compact("data", 'action', 'breadcrumb'));
    }

    public function atualizar_boleto_lote_old()
    {
        

        $boletos = $this->CnabLote->query("SELECT ci.id_web
        FROM billing_monthly_payments b
        INNER JOIN negativacao n ON n.billing_id = b.billing_id AND n.customer_id = b.customer_id AND n.data_cancel = '1901-01-01'
        INNER JOIN customers c ON c.id = b.customer_id
        INNER JOIN incomes i ON i.billing_monthly_payment_id = b.id AND i.data_cancel = '1901-01-01'
        INNER JOIN cnab_items ci ON ci.income_id = i.id AND ci.data_cancel = '1901-01-01'
        WHERE b.billing_id = 286 AND b.data_cancel = '1901-01-01'
        AND n.product_id = 408
        AND i.status_id NOT IN (18)
        AND c.codigo_associado NOT IN (20288, 22232, 21233, 22238)
        ");

        foreach ($boletos as $dados) {
            $this->alterar_boleto($dados['ci']['id_web']);
            echo 'cliente '. $dados['ci']['id_web'].' <br>';
        }
    }

    public function alterar_boleto($idWeb)
    {
        $boleto = $this->CnabItem->find('first', [
            'fields' => ['Income.valor_total', 'Income.vencimento', 'Customer.documento', 'BankTickets.multa_boleto', 'BankTickets.juros_boleto_dia', 'BankTickets.token', 'BankTickets.codigo_cedente'],
            'joins' => [
                [
                    'table' => 'incomes',
                    'alias' => 'Income',
                    'type' => 'inner',
                    'conditions' => [
                        'Income.id = CnabItem.income_id', 'Income.data_cancel' => '1901-01-01'
                    ]
                ],
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'inner',
                    'conditions' => [
                        'Customer.id = Income.customer_id', 'Customer.data_cancel' => '1901-01-01'
                    ]
                ],
                [
                    'table' => 'bank_accounts',
                    'alias' => 'BankAccount',
                    'type' => 'inner',
                    'conditions' => [
                        'BankAccount.id = Income.bank_account_id', 'BankAccount.data_cancel' => '1901-01-01'
                    ]
                ],
                [
                    'table' => 'bank_tickets',
                    'alias' => 'BankTickets',
                    'type' => 'inner',
                    'conditions' => [
                        'BankAccount.id = BankTickets.bank_account_id', 'BankTickets.data_cancel' => '1901-01-01'
                    ]
                ]
            ],
            'conditions' => [
                'CnabItem.id_web' => $idWeb
            ],
            'recursive' => -1
        ]);

        $ApiBoleto = new ApiBoleto();
        $response = $ApiBoleto->alterarBoleto($idWeb, $boleto);

        if ($response['success']) {
            $this->Flash->set(__('Boleto alterado com sucesso!'), ['params' => ['class' => "alert alert-success"]]);
        } else {
            $this->Flash->set(__('O boleto não pode ser alterado!'), ['params' => ['class' => "alert alert-danger"]]);
        }


        $this->redirect($this->referer());
    }

    public function ver_boleto($idWeb)
    {
        $ApiBoleto = new ApiBoleto();
        $boleto = $ApiBoleto->buscarBoleto($idWeb);

        if ($boleto['success']) {
            # Decode the Base64 string, making sure that it contains only valid characters
            $bin = base64_decode($boleto['obj']['boleto'], true);

            # Perform a basic validation to make sure that the result is a valid PDF file
            # Be aware! The magic number (file signature) is not 100% reliable solution to validate PDF files
            # Moreover, if you get Base64 from an untrusted source, you must sanitize the PDF contents
            if (strpos($bin, '%PDF') !== 0) {
                throw new Exception('Missing the PDF file signature');
            }

            # Write the PDF contents to a local file
            file_put_contents('file.pdf', $bin);

            header("Content-disposition: attachment; filename=file.pdf");
            header("Content-type: application/pdf:");
            readfile('file.pdf');

            unlink('file.pdf');
        } else {
            $this->Flash->set(__($boleto['error']->erros->item->message), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }
    }

    public function ver_boleto_logo($idWeb)
    {
        $ApiBoleto = new ApiBoleto();
        $boleto = $ApiBoleto->buscarBoletoLogo($idWeb);
        
        # Write the PDF contents to a local file
        file_put_contents('file.pdf', $boleto);

        header("Content-disposition: attachment; filename=file.pdf");
        header("Content-type: application/pdf:");
        readfile('file.pdf');

        unlink('file.pdf');
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

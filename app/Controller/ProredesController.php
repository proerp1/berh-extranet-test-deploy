<?php
class ProredesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'GerarTxt'];
    public $uses = ['Prorede', 'Status', 'ProredeCustomer', 'Customer'];

    public $paginate = [
        'limit' => 10, 'order' => ['Prorede.numero' => 'desc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(24, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Prorede.numero LIKE' => "%".$_GET['q']."%", 'Prorede.arquivo LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Prorede', $condition);

        $action = "Prorede";
        $breadcrumb = ['Lista' => ''];
        $this->set(compact('data' ,'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(24, "escrita") ? "" : $this->redirect("/not_allowed");

        $clientes_cadastrados = $this->ProredeCustomer->find('list', ['fields' => ['ProredeCustomer.customer_id']]);

        $clientes = $this->Customer->find('all', ['conditions' => ['Customer.responsavel != ""', 'Customer.cod_franquia' => CakeSession::read("Auth.User.resales"), 'not' => ['Customer.id' => $clientes_cadastrados]]]);

        $this->set("action", "Nova remessa");
        $this->set("form_action", "add");
        $this->set(compact('clientes'));
    }

    public function gerar_remessa()
    {
        $this->Permission->check(24, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->autoRender = false;
        
        $this->Prorede->create();
        $this->Prorede->validates();

        $last_number = $this->Prorede->find('first', ['order' => ['Prorede.numero' => 'desc']]);
        if (empty($last_number)) {
            $number = 1;
        } else {
            $number = $last_number['Prorede']['numero']+1;
        }

        $clientes_cadastrados = $this->ProredeCustomer->find('list', ['fields' => ['ProredeCustomer.customer_id']]);

        $clientes = $this->Customer->find('all', ['conditions' => ['Customer.responsavel != ""', 'not' => ['Customer.id' => $clientes_cadastrados]], 'recursive' => -1]);

        $dados_emp = ["cnpj_empresa" => "08663497000130", "numero_remessa" => $number];

        $dados_cli = [];
        foreach ($clientes as $cli) {
            $dados_cli[] = [
                "razao_social" => $cli["Customer"]["nome_primario"],
                "nome_fantasia" => $cli["Customer"]["nome_secundario"],
                "email" => $cli["Customer"]["email"],
                "endereco" => $cli["Customer"]["endereco"],
                "bairro" => $cli["Customer"]["bairro"],
                "cep" => $cli["Customer"]["cep"],
                "cidade" => $cli["Customer"]["cidade"],
                "uf" => $cli["Customer"]["estado"],
                "telefone" => $cli["Customer"]["telefone1"],
                "documento" => $cli["Customer"]["documento"],
                "tipo_documento" => $cli["Customer"]["tipo_pessoa"] == 2 ? 1 : 2,
                "contato" => $cli["Customer"]["responsavel"],
                "status" => "A",
                "ramal" => "",
                "ramo_atividade" => "",
                "agencia_bancaria" => "",
                "conta_corrente" => ""
            ];
        }

        $dados = ["empresa" => $dados_emp, "clientes" => $dados_cli];

        $arquivo = $this->GerarTxt->gerar_prorede($dados);

        $this->request->data['Prorede']['arquivo'] = $arquivo;
        $this->request->data['Prorede']['numero'] = $number;
        $this->request->data['Prorede']['user_creator_id'] = CakeSession::read("Auth.User.id");

        if ($this->Prorede->save($this->request->data)) {
            $data_prorede_clientes = [];
            foreach ($clientes as $cliente) {
                $data_prorede_clientes[] = ['ProredeCustomer' => ['customer_id' => $cliente['Customer']['id'], 'prorede_id' => $this->Prorede->id, 'user_creator_id' => CakeSession::read('Auth.User.id')]];
            }

            $this->ProredeCustomer->saveMany($data_prorede_clientes);

            $this->Session->setFlash(__('O prorede foi gerado com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'index']);
        } else {
            $this->Session->setFlash(__('O prorede não pode ser gerado, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
        }
    }

    public function teste_txt()
    {
        $clientes = $this->Customer->find('all', ['conditions' => ['Customer.codigo_associado' => ['13668', '13669', '13670', '13671', '13672']]]);
 
        $arquivo = $this->GerarTxt->gerar_prorede($clientes, 143);
    }

    public function view($id)
    {
        $this->Permission->check(24, "escrita") ? "" : $this->redirect("/not_allowed");

        $clientes = $this->ProredeCustomer->find('all', ['conditions' => ['ProredeCustomer.prorede_id' => $id]]);

        $this->set("action", 'Remessa nº '.$clientes[0]['Prorede']['numero']);
        $this->set("form_action", "edit");
        $this->set(compact('clientes', 'id'));
        
        $this->render("add");
    }

    public function reenviar($id)
    {
        $this->Permission->check(53, "escrita") ? "" : $this->redirect("/not_allowed");
        $clientes = $this->ProredeCustomer->find('all', ['conditions' => ['ProredeCustomer.prorede_id' => $id]]);

        $this->ProredeCustomer->updateAll(
            ['ProredeCustomer.data_cancel' => 'current_timestamp()', 'ProredeCustomer.usuario_id_cancel' => CakeSession::read("Auth.User.id")], //set
            ["ProredeCustomer.customer_id" => $id] //where
        );

        $this->redirect(['action' => 'add']);
    }

    public function download_remessa($arquivo)
    {
        $this->autoRender = false;
        header("Content-disposition: attachment; filename=".$arquivo);
        header("Content-type: application/pdf:");

        readfile('files/prorede_txt/'.$arquivo);
    }
}

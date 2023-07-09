<?php
App::import('Controller', 'Incomes');
class EmailsCampanhasController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email'];
    public $uses = ['EmailsCampanha', 'Customer', 'Billing', 'Income', 'Status', 'MailList', 'Resale'];

    public $paginate = [
        'limit' => 10, 'order' => ['EmailsCampanha.id' => 'desc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /***************************
                DISPARO DE EMAILS
    ****************************/
    public function index()
    {
        $this->Permission->check(47, "leitura") ? "" : $this->redirect("/not_allowed");
            
        $this->Paginator->settings = $this->paginate;

        $dados = $this->Paginator->paginate("EmailsCampanha");

        $action = 'Emails';
        $breadcrumb = ['Lista' => ''];
        $this->set(compact('dados', 'action', 'breadcrumb'));
    }

    public function add()
    {
        $this->Permission->check(47, "escrita") ? "" : $this->redirect("/not_allowed");
        $send = false;
            
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['EmailsCampanha']['user_creator_id'] = CakeSession::read("Auth.User.id");

            $this->EmailsCampanha->validates();
            $this->EmailsCampanha->create();
                
            $save = $this->EmailsCampanha->save($this->request->data);
            if ($save) {
                $id = $save['EmailsCampanha']['id'];
                $this->redirect("edit/".$id);
            } else {
                $this->Session->setFlash(__('Erro ao salvar a campanha, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $this->set("action", "Nova Campanha");
        $this->set("form_action", "../emails_campanhas/add");

        $action = 'Emails';
        $breadcrumb = ['Nova campanha' => ''];
        $this->set(compact('send', 'action', 'breadcrumb'));
    }

    public function edit($id)
    {
        $this->Permission->check(47, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->EmailsCampanha->id = $id;

        if ($this->request->is(['post', 'put'])) {
            $this->EmailsCampanha->validates();
            $this->request->data['EmailsCampanha']['user_updated_id'] = CakeSession::read("Auth.User.id");

            if ($this->EmailsCampanha->save($this->request->data)) {
                $this->Session->setFlash(__('Campanha alterada com sucesso.'), 'default', ['class' => "alert alert-success"]);
            } else {
                $this->Session->setFlash(__('A campanha não pôde ser alterada, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $temp_errors = $this->EmailsCampanha->validationErrors;
        $this->request->data = $this->EmailsCampanha->read();
        $this->EmailsCampanha->validationErrors = $temp_errors;

        $send 		= $this->request->data['EmailsCampanha']['send'];
        $processing = $this->request->data['EmailsCampanha']['processing'];

        if (!$send) {
            $this->Session->setFlash(__('Emails ainda não enviados'), 'default', ['class' => "alert alert-danger"]);
        }

        if ($send && $processing) {
            $buscar = false;
            $this->Session->setFlash(__('Os emails estão sendo enviados! Aguarde até o processo finalizar'), 'default', ['class' => "alert alert-warning"]);
        } elseif ($send && !$processing) {
            $buscar = false;
            $this->Session->setFlash(__('Processo de envio finalizado!'), 'default', ['class' => "alert alert-success"]);
        }

        $action = 'Emails';
        $breadcrumb = ['Editar campanha' => ''];
        $this->set("action", 'Editar Campanha');
        $this->set("form_action", "../emails_campanhas/edit");
        $this->set(compact("id", "mail_list", "data", "send", "processing", 'action', 'breadcrumb'));

        $this->render("add");
    }

    /***********************
                BUSCAR EMAILS
    ************************/
    public function list_emails($id)
    {
        //bloqueia acesso sem o id da campanha
        isset($id) ? "" : $this->redirect("/emails_campanhas");

        $this->Permission->check(47, "escrita") ? "" : $this->redirect("/not_allowed");
        $buscar = false;

        //salvar destinatários
        if ($this->request->is(['post', 'put'])) {
            $this->EmailsCampanha->id = $id;

            $customer_ids = explode(',', substr($this->request->data['user_id'], 0, -1));
            $income_ids = explode(',', substr($this->request->data['income_id'], 0, -1));
            

            $dados_mail_list = [];
            foreach ($customer_ids as $key => $customer_id) {
                $dados_mail_list[] = [
                	'MailList' => [
                		'customer_id' => $customer_id,
                		'email_campanha_id' => $id,
                		'income_id' => $income_ids[$key],
                		'sent' => false,
                		'user_creator_id' => CakeSession::read('Auth.User.id')
                	]
                ];
            }

            $this->MailList->saveMany($dados_mail_list);

            $this->Session->setFlash(__('Destinatários adicionados. Você já pode enviar a mensagem.'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => "view_emails/".$id]);
        }

        $cadastrados = $this->MailList->find('all', ['conditions' => ['MailList.email_campanha_id' => $id], 'fields' => ['Customer.id']]);
        $ids_cadastrados = [];
        foreach ($cadastrados as $dados) {
            $ids_cadastrados[] = $dados['Customer']['id'];
        }

        $condition = ["and" => ['Customer.status_id != ' => 5, 'Customer.enviar_email' => true, 'Customer.cod_franquia' => CakeSession::read("Auth.User.resales"), 'not' => ['Customer.id' => $ids_cadastrados]], "or" => ['Customer.email != ""']];

        $get_de = isset($_GET["de"]) ? $_GET["de"] : '';
        $get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';
            
        if ($get_de != "" and $get_ate != "") {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['de'])));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['ate'])));

            $condition['and'] = array_merge($condition['and'], ["Income.vencimento between '$de' and '$ate'"]);
        }

        if (!empty($_GET["s"])) {
            $condition['and'] = array_merge($condition['and'], ['Income.status_id' => $_GET["s"]]);
            $buscar = true;
        }

        if (!empty($_GET["sc"])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET["sc"]]);
            $buscar = true;
        }

        if (!empty($_GET["f"])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.cod_franquia' => $_GET['f']]);
        }

        $data = [];
        if ($buscar) {
            $this->Income->unbindModel(['belongsTo' => [
                'BankAccount',
                'Revenue',
                'CostCenter',
                'Billing',
                'BillingMonthlyPayment',
                'UsuarioBaixa',
                'UsuarioCancelamento'
            ], 'hasOne' => ['CnabItem', 'CnabItemSicoob']], false);
            $data = $this->Income->find('all', ['conditions' => $condition, 'order' => ['Customer.nome_primario' => 'asc'], 'group' => ['Income.customer_id']]);
        }

        // $this->status_envio($id);

        $faturamentos = $this->Billing->find('all', ['conditions' => ['Billing.status_id' => 1], 'order' => ['Billing.id' => 'desc']]);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 5], 'order' => ['Status.name' => 'asc']]);
        $statusClientes = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => ['Status.name' => 'asc']]);
        $codFranquias = $this->Resale->find('all', ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], ['order' => 'Resale.nome_fantasia']]);

        $this->EmailsCampanha->id = $id;
        $emails_campanhas = $this->EmailsCampanha->read();

        $send = $emails_campanhas['EmailsCampanha']['send'];
        $processing = $emails_campanhas['EmailsCampanha']['processing'];

        if (!$send) {
            $this->Session->setFlash(__('Emails ainda não enviados'), 'default', ['class' => "alert alert-danger"]);
        }

        if ($send && $processing) {
            $buscar = false;
            $this->Session->setFlash(__('Os emails estão sendo enviados! Aguarde até o processo finalizar'), 'default', ['class' => "alert alert-warning"]);
        } elseif ($send && !$processing) {
            $buscar = false;
            $this->Session->setFlash(__('Processo de envio finalizado!'), 'default', ['class' => "alert alert-success"]);
        }

        $action = 'Emails';
        $breadcrumb = ['Selectionar destinatários' => ''];
        $this->set(compact('id', 'faturamentos', 'status', 'buscar', 'data', 'statusClientes', 'codFranquias', 'action', 'breadcrumb'));
    }

    public function view_emails($id)
    {
        //bloqueia acesso sem o id da campanha
        isset($id) ? "" : $this->redirect("/emails_campanhas");

        $this->Permission->check(47, "escrita") ? "" : $this->redirect("/not_allowed");

        $condition = ['and' => ['MailList.email_campanha_id' => $id], 'or' => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.email LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%", 'Customer.celular LIKE' => "%".$_GET['q']."%", 'Customer.celular1 LIKE' => "%".$_GET['q']."%", 'Customer.celular2 LIKE' => "%".$_GET['q']."%", 'Customer.celular3 LIKE' => "%".$_GET['q']."%", 'Customer.celular4 LIKE' => "%".$_GET['q']."%", 'Customer.celular5 LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->MailList->find('all', ['conditions' => $condition, 'order' => ['MailList.sent' => 'asc', 'Customer.nome_secundario' => 'asc']]);

        $this->EmailsCampanha->id = $id;
        $emails_campanhas = $this->EmailsCampanha->read();

        $send = $emails_campanhas['EmailsCampanha']['send'];
        $processing = $emails_campanhas['EmailsCampanha']['processing'];

        if (!$send) {
            $this->Session->setFlash(__('Emails ainda não enviados'), 'default', ['class' => "alert alert-danger"]);
        }

        if ($send && $processing) {
            $this->Session->setFlash(__('Os emails estão sendo enviados! Aguarde até o processo finalizar'), 'default', ['class' => "alert alert-warning"]);
        } elseif ($send && !$processing) {
            $this->Session->setFlash(__('Processo de envio finalizado!'), 'default', ['class' => "alert alert-success"]);
        }

        $action = 'Emails';
        $breadcrumb = ['Ver destinatários' => ''];
        $this->set(compact('id', 'data', 'send', 'processing', 'action', 'breadcrumb'));
    }

    public function delete_email($id, $list_id)
    {
        $this->Permission->check(3, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->MailList->id = $list_id;

        $data = ['MailList' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->MailList->save($data)) {
            $this->Session->setFlash(__('O cliente foi excluido da lista com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'view_emails/'.$id]);
        }
    }

    /***********************
                ENVIAR EMAILS
    ************************/
    public function send_emails($id)
    {
        $this->Permission->check(47, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->autoRender = false;
        $this->layout = 'ajax';
        //bloqueia acesso sem o id da campanha
        isset($id) ? "" : $this->redirect("/emails_campanhas");

        if ($this->request->is(['post', 'put'])) {
            //setar flag processando para enviar os dados pelo cron
            $this->EmailsCampanha->updateAll(['send' => true, 'processing' => true], ['id' => $id]);

            $this->Session->setFlash(__('Campanha programada com sucesso! Em breve o disparo será realizado.'), 'default', ['class' => "alert alert-success"]);
        }

        $this->redirect("/emails_campanhas");
        die();
    }

    

    public function template_email()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $view = new View($this, false);
        $view->layout=false;

        //$view->set(compact("data"));
        $html=$view->render('template_email2');
 
        echo $html;
        die();
    }
}

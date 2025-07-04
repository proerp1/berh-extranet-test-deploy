<?php
class FaqsController extends AppController
{
    public $helpers = ['Html', 'Form', 'Text'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Faq', 'CategoriaFaq'];

    public $paginate = [
        'Faq' => ['limit' => 100, 'order' => ['Faq.id' => 'desc']]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
{
    $this->Permission->check(81, "leitura") ? "" : $this->redirect("/not_allowed");

    $this->Paginator->settings = [
        'Faq' => [
            'limit' => 100,
            'order' => ['Faq.id' => 'desc'],
            'contain' => ['CategoriaFaq']
        ]
    ];

    $condition = ["and" => [], "or" => []];

    // Filtro de busca por pergunta
    if (!empty($_GET['q'])) {
        $condition['or'][] = ['Faq.pergunta LIKE' => '%' . $_GET['q'] . '%'];
    }

    // Filtro por categoria
    if (!empty($_GET['categoria']) && is_numeric($_GET['categoria'])) {
        $condition['and'][] = ['Faq.categoria_faq_id' => $_GET['categoria']];
    }

    // Filtro por sistema_destino
    if (!empty($_GET['sistema']) && in_array($_GET['sistema'], ['sig', 'cliente', 'todos'])) {
        $condition['and'][] = ['Faq.sistema_destino' => $_GET['sistema']];
    }

    $data = $this->Paginator->paginate('Faq', $condition);

    // Buscar lista de categorias para o filtro
    $categoriasFaq = $this->CategoriaFaq->find('list', [
        'fields' => ['CategoriaFaq.id', 'CategoriaFaq.nome'],
        'order' => ['CategoriaFaq.nome' => 'ASC']
    ]);

    $action = 'FAQ';
    $breadcrumb = ['Configurações' => '', 'FAQ' => ''];

    $this->set(compact('data', 'action', 'breadcrumb', 'categoriasFaq'));
}


    public function add()
    {
        $this->Permission->check(81, "escrita") ? "" : $this->redirect("/not_allowed");

        if ($this->request->is(['post', 'put'])) {
            $this->Faq->create();
            $this->request->data['Faq']['user_creator_id'] = CakeSession::read("Auth.User.id");

            if ($this->Faq->save($this->request->data)) {
                $this->Flash->set(__('A FAQ foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                return $this->redirect(['controller' => 'faqs', 'action' => 'index']);
            } else {
                $this->Flash->set(__('Não foi possível salvar a FAQ, tente novamente.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $categoriasFaq = $this->CategoriaFaq->find('list', [
            'fields' => ['CategoriaFaq.id', 'CategoriaFaq.nome'],
            'order' => ['CategoriaFaq.nome' => 'asc']
        ]);

        $destinos = ['todos' => 'SIG e Cliente', 'sig' => 'Apenas SIG (Extranet)', 'cliente' => 'Apenas Cliente'];

        $action = 'FAQ';
        $breadcrumb = ['Configurações' => '', 'FAQ' => '', 'Nova FAQ' => ''];
        $this->set(compact('action', 'breadcrumb', 'categoriasFaq', 'destinos'));
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(81, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->Faq->id = $id;

        if ($this->request->is(['post', 'put'])) {
            if ($this->Faq->save($this->request->data)) {
                $this->Flash->set(__('A FAQ foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                return $this->redirect(['controller' => 'faqs', 'action' => 'index']);
            } else {
                $this->Flash->set(__('Não foi possível alterar a FAQ, tente novamente.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Faq->validationErrors;
        $this->request->data = $this->Faq->read();
        $this->Faq->validationErrors = $temp_errors;

        $categoriasFaq = $this->CategoriaFaq->find('list', [
            'fields' => ['CategoriaFaq.id', 'CategoriaFaq.nome'],
            'order' => ['CategoriaFaq.nome' => 'asc']
        ]);

        $destinos = ['todos' => 'SIG e Cliente', 'sig' => 'Apenas SIG (Extranet)', 'cliente' => 'Apenas Cliente'];

        $action = 'FAQ';
        $breadcrumb = ['Configurações' => '', 'FAQ' => '', 'Editar FAQ' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'categoriasFaq', 'destinos'));
        $this->render("add");
    }

    public function delete($id = null)
    {
        $this->Permission->check(81, "excluir") ? "" : $this->redirect("/not_allowed");

        $this->Faq->id = $id;
        if ($this->Faq->delete()) {
            $this->Flash->set(__('A FAQ foi excluída com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        } else {
            $this->Flash->set(__('Não foi possível excluir a FAQ'), ['params' => ['class' => "alert alert-danger"]]);
        }

        return $this->redirect(['controller' => 'faqs', 'action' => 'index']);
    }
}

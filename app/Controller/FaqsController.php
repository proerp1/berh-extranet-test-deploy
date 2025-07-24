<?php
class FaqsController extends AppController
{
    public $helpers = ['Html', 'Form', 'Text'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator'];
    
    public $uses = ['Faq', 'CategoriaFaq', 'FaqRelacionamento', 'Supplier'];

    public $paginate = [
        'Faq' => [
            'limit' => 100,
            'order' => ['Faq.id' => 'desc'],
            'contain' => ['CategoriaFaq', 'FaqRelacionamento' => ['Supplier']]
        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(81, "leitura") ? "" : $this->redirect("/not_allowed");

        $condition = ["and" => [], "or" => []];

        if (!empty($_GET['q'])) {
            $condition['or'][] = ['Faq.pergunta LIKE' => '%' . $_GET['q'] . '%'];
        }

        if (!empty($_GET['categoria']) && is_numeric($_GET['categoria'])) {
            $condition['and'][] = ['Faq.categoria_faq_id' => $_GET['categoria']];
        }

        if (!empty($_GET['sistema']) && in_array($_GET['sistema'], ['sig', 'cliente', 'todos'])) {
            $condition['and'][] = ['Faq.sistema_destino' => $_GET['sistema']];
        }
        if (!empty($_GET['fornecedores_relacionados'])) {
            $idsSelecionados = array_filter((array)$_GET['fornecedores_relacionados'], 'is_numeric');

            if (!empty($idsSelecionados)) {
                $faqIdsRelacionados = $this->FaqRelacionamento->find('all', [
            'fields' => ['faq_id'],
            'conditions' => ['supplier_id' => $idsSelecionados],
            'group' => ['faq_id'],
            'recursive' => -1
        ]);

        $faqIds = Hash::extract($faqIdsRelacionados, '{n}.FaqRelacionamento.faq_id');

        if (!empty($faqIds)) {
            $condition['and'][] = ['Faq.id' => $faqIds];
        }

            }
        }


        $data = $this->Paginator->paginate('Faq', $condition);

        // Carrega nomes dos fornecedores manualmente
        foreach ($data as &$faq) {
            if (!empty($faq['FaqRelacionamento'])) {
                foreach ($faq['FaqRelacionamento'] as &$rel) {
                    if ((int)$rel['supplier_id'] !== 0) {
                        $supplier = $this->Supplier->find('first', [
                            'fields' => ['Supplier.nome_fantasia'],
                            'conditions' => ['Supplier.id' => $rel['supplier_id']],
                            'recursive' => -1
                        ]);
                        $rel['Supplier']['nome_fantasia'] = $supplier['Supplier']['nome_fantasia'] ?? null;
                    }
                }
            }
        }

           if (isset($_GET['exportar'])) {
                $nome = 'FAQs_' . date('d_m_Y_H_i_s') . '.xlsx';

                // Mesmo filtro usado no paginate
                $data = $this->Faq->find('all', [
                    'conditions' => $condition,
                    'contain' => ['CategoriaFaq', 'FaqRelacionamento' => ['Supplier']],
                    'order' => ['Faq.id' => 'desc']
                ]);

                // Carregar os nomes dos fornecedores se necessário
                foreach ($data as &$faq) {
                    if (!empty($faq['FaqRelacionamento'])) {
                        foreach ($faq['FaqRelacionamento'] as &$rel) {
                            if ((int)$rel['supplier_id'] !== 0) {
                                $supplier = $this->Supplier->find('first', [
                                    'fields' => ['Supplier.nome_fantasia'],
                                    'conditions' => ['Supplier.id' => $rel['supplier_id']],
                                    'recursive' => -1
                                ]);
                                $rel['Supplier']['nome_fantasia'] = $supplier['Supplier']['nome_fantasia'] ?? null;
                            }
                        }
                    }
                }

                $this->ExcelGenerator->gerarExcelFaq($nome, $data);
                return $this->redirect("/files/excel/" . $nome);
            }

        $categoriasFaq = $this->CategoriaFaq->find('list', [
            'fields' => ['CategoriaFaq.id', 'CategoriaFaq.nome'],
            'order' => ['CategoriaFaq.nome' => 'ASC']
        ]);
        $this->prepareFormData();
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
                $faqId = $this->Faq->id;

                if (!empty($this->request->data['FaqRelacionamento']['supplier_id'])) {
                    $selecionados = (array)$this->request->data['FaqRelacionamento']['supplier_id'];
                    $this->FaqRelacionamento->deleteAll(['faq_id' => $faqId], false);

                    if (in_array('0', $selecionados)) {
                        $this->FaqRelacionamento->create();
                        $this->FaqRelacionamento->save([
                            'faq_id' => $faqId,
                            'supplier_id' => 0
                        ]);
                    } else {
                        foreach ($selecionados as $supplierId) {
                            $supplierId = (int)$supplierId;
                            if ($supplierId >= 0) {
                                $this->FaqRelacionamento->create();
                                $this->FaqRelacionamento->save([
                                    'faq_id' => $faqId,
                                    'supplier_id' => $supplierId
                                ]);
                            }
                        }
                    }
                }

                $this->Flash->set(__('A FAQ foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                return $this->redirect(['controller' => 'faqs', 'action' => 'index']);
            } else {
                $this->Flash->set(__('Não foi possível salvar a FAQ, tente novamente.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->prepareFormData();
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(81, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->Faq->id = $id;

        if ($this->request->is(['post', 'put'])) {
            if ($this->Faq->save($this->request->data)) {
                $this->FaqRelacionamento->deleteAll(['faq_id' => $id], false);

                if (!empty($this->request->data['FaqRelacionamento']['supplier_id'])) {
                    $selecionados = (array)$this->request->data['FaqRelacionamento']['supplier_id'];

                    if (in_array('0', $selecionados)) {
                        $this->FaqRelacionamento->create();
                        $this->FaqRelacionamento->save([
                            'faq_id' => $id,
                            'supplier_id' => 0
                        ]);
                    } else {
                        foreach ($selecionados as $supplierId) {
                            $supplierId = (int)$supplierId;
                            if ($supplierId >= 0) {
                                $this->FaqRelacionamento->create();
                                $this->FaqRelacionamento->save([
                                    'faq_id' => $id,
                                    'supplier_id' => $supplierId
                                ]);
                            }
                        }
                    }
                }

                $this->Flash->set(__('A FAQ foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                return $this->redirect(['controller' => 'faqs', 'action' => 'index']);
            } else {
                $this->Flash->set(__('Não foi possível alterar a FAQ, tente novamente.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Faq->validationErrors;
        $this->request->data = $this->Faq->read();
        $this->Faq->validationErrors = $temp_errors;

        $relacionamentos = $this->FaqRelacionamento->find('list', [
            'fields' => ['FaqRelacionamento.supplier_id'],
            'conditions' => ['faq_id' => $id]
        ]);

        $this->request->data['FaqRelacionamento']['supplier_id'] = array_values($relacionamentos);

        $this->prepareFormData();
        $this->set("form_action", "edit");
        $this->set(compact('id'));
        $this->render("add");
    }

    public function delete($id = null)
    {
        $this->Permission->check(81, "excluir") ? "" : $this->redirect("/not_allowed");

        $this->Faq->id = $id;
        $this->FaqRelacionamento->deleteAll(['faq_id' => $id], false);

        if ($this->Faq->delete()) {
            $this->Flash->set(__('A FAQ foi excluída com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        } else {
            $this->Flash->set(__('Não foi possível excluir a FAQ'), ['params' => ['class' => "alert alert-danger"]]);
        }

        return $this->redirect(['controller' => 'faqs', 'action' => 'index']);
    }

    private function prepareFormData()
    {
        $categoriasFaq = $this->CategoriaFaq->find('list', [
            'fields' => ['CategoriaFaq.id', 'CategoriaFaq.nome'],
            'order' => ['CategoriaFaq.nome' => 'asc']
        ]);

        $destinos = ['todos' => 'SIG e Cliente', 'sig' => 'Apenas SIG (Extranet)', 'cliente' => 'Apenas Cliente'];

        $fornecedores = $this->Supplier->find('list', [
            'fields' => ['Supplier.id', 'Supplier.nome_fantasia'],
            'order' => ['Supplier.nome_fantasia' => 'ASC']
        ]);

        $fornecedores = [0 => 'Todos os fornecedores'] + $fornecedores;

        $action = 'FAQ';
        $breadcrumb = ['Configurações' => '', 'FAQ' => ''];

        $this->set(compact('categoriasFaq', 'destinos', 'fornecedores', 'action', 'breadcrumb'));
    }
}

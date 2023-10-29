<?php
class PrivateFilesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $uses = [];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function baixar($pasta, $arquivo)
    {
        $arquivo = str_replace('_', '.', $arquivo);
        $arquivo = str_replace('-', '_', $arquivo);
        $this->autoRender = false;
        $this->response->file(APP.'Private/'.$pasta.'/'.$arquivo, ['download' => true, 'name' => $arquivo]);
    }

    public function baixar_boleto($pasta, $cliente, $arquivo)
    {
        $arquivo = str_replace('_', '.', $arquivo);
        $arquivo = str_replace('-', '_', $arquivo);
        $this->autoRender = false;
        $this->response->file(APP.'Private/'.$pasta.'/'.$cliente.'/'.$arquivo, ['download' => true, 'name' => $arquivo]);
    }
}

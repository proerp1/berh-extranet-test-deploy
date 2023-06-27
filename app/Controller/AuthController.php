<?php
class AuthController extends AppController {
		public $components = ['GetCep'];

		public function beforeFilter() { 
		parent::beforeFilter(); 
	}

		public function index(){
			
		}

		public function not_allowed(){
			
		}

		public function get_cep() {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$endereco = $this->GetCep->get($_POST['cep']);

			echo json_encode($endereco);
		}
}
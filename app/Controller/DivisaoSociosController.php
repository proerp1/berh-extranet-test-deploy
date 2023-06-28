<?php
class DivisaoSociosController extends AppController {
	public $helpers = array('Html', 'Form');
	public $components = array('Paginator', 'Permission', 'ExcelGenerator');
	public $uses = array('Socios', 'DistribuicaoCobrancaUsuario', 'Income');

	public function beforeFilter() { 
		parent::beforeFilter(); 
	}

	public function index(){
		$this->Permission->check(48, "leitura")? "" : $this->redirect("/not_allowed");

		$dados = $this->Income->query("SELECT date_format(Income.vencimento, '%m/%Y') as mes, sum(Income.valor_total) as valor
																	FROM incomes Income
																	INNER JOIN customers c on c.id = Income.customer_id
																	WHERE Income.status_id = 15 and Income.data_cancel = '1901-01-01' and Income.customer_id is not null 
																	and Income.vencimento < '2017-05-31' and c.status_id not in (42)
																	group by DATE_FORMAT(`Income`.`vencimento`, '%m/%Y')
																	order by Income.vencimento desc");

		$socios = $this->Socios->find('all');

		if (isset($_GET['excel'])) {
			$berh = $this->Income->find('all', ['conditions' => ['Income.socio_id' => 1], 'order' => ['Customer.nome_primario' => 'asc']]);
			$ivan = $this->Income->find('all', ['conditions' => ['Income.socio_id' => 2], 'order' => ['Customer.nome_primario' => 'asc']]);

			$nome = 'divisao_socios';

			$dados = ['ivan' => $ivan, 'berh' => $berh];

			$this->ExcelGenerator->gerarExcelDivisao($nome, $dados, 'todos');
			$this->redirect("/files/excel/".$nome.".xlsx");
		}

		$this->set(compact('dados', 'socios'));
	}

	public function detalhes(){
		$this->Permission->check(48, "leitura")? "" : $this->redirect("/not_allowed");

		$berh = $this->Income->find('all', ['conditions' => ["DATE_FORMAT(Income.vencimento, '%m/%Y')" => $_GET['mes'], 'Income.socio_id' => 1], 'order' => ['Customer.nome_primario' => 'asc']]);

		$ivan = $this->Income->find('all', ['conditions' => ["DATE_FORMAT(Income.vencimento, '%m/%Y')" => $_GET['mes'], 'Income.socio_id' => 2], 'order' => ['Customer.nome_primario' => 'asc']]);

		if (isset($_GET['excel'])) {
			$nome = 'divisao_'.str_replace('/', '_', $_GET['mes']);

			$dados = ['ivan' => $ivan, 'berh' => $berh];

			$this->ExcelGenerator->gerarExcelDivisao($nome, $dados);
			$this->redirect("/files/excel/".$nome.".xlsx");
		}

		$this->set(compact('berh', 'ivan'));
	}

	public function divide_cobranca(){
		$this->Permission->check(48, "escrita")? "" : $this->redirect("/not_allowed");

		$this->Income->query("UPDATE incomes i
													SET i.socio_id = null
													WHERE DATE_FORMAT(`i`.`vencimento`, '%m/%Y') = '".$_GET['mes']."' AND i.socio_id IS NOT NULL");

		$dados = $this->Income->query("SELECT i.id, i.valor_total
																		FROM incomes i
																		INNER JOIN customers c on c.id = i.customer_id
																		WHERE DATE_FORMAT(`i`.`vencimento`, '%m/%Y') = '".$_GET['mes']."' and i.status_id = 15 and i.data_cancel = '1901-01-01' and i.customer_id is not null and i.vencimento < '2017-05-31' and c.status_id not in (42) 
																		order by rand()");

		$valor_dividido = 0;
		$update_income = [];
		foreach ($dados as $value) {

			if ($valor_dividido > $_GET['HiperCheck']) {
				$socio = 2;
			} else {
				$socio = 1;
			}

			$update_income[] = ['Income' => ['id' => $value['i']['id'], 'socio_id' => $socio]];

			$valor_dividido += $value['i']['valor_total'];
		}

		$this->Income->saveMany($update_income);

		$this->Session->setFlash(__('Mês '.$_GET['mes'].' dividido com sucesso!'), 'default', array('class' => "alert alert-success"));
		$this->redirect(['action' => 'index']);
		/*echo 'dividido mes '.$_GET['mes'];
		die();*/
	}
}
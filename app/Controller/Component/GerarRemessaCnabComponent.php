<?php
include '../../vendor/autoload.php';

class GerarRemessaCnabComponent extends Component {	
	public $nome_fantasia = 'BeRH';
	public $razao_social = 'BERH LTDA';
	public $cnpj = '';
	public $logradouro = '';
	public $numero = '';
	public $bairro = '';
	public $cidade = '';
	public $uf = '';
	public $cep = '';

	public function gerar($contas, $sequencia, $lote_id, $nome_arquivo){
		$Income = ClassRegistry::init('Income');

		$beneficiario = new \Eduardokum\LaravelBoleto\Pessoa([
			'documento' => $this->cnpj,
			'nome'      => $this->nome_fantasia,
			'cep'       => $this->cep,
			'endereco'  => $this->logradouro,
			'bairro' => $this->bairro,
			'uf'        => $this->uf,
			'cidade'    => $this->cidade
		]);

		$dadosEnvio = [
			'beneficiario' => $beneficiario,
			'carteira' => 109,
			'agencia' => 1111,
			'conta' => 22222,
		];
		$remessa = new Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Itau($dadosEnvio);

		// $arquivo->configure($dados_banco);
		$tipoP = [1 => 'cpf', 2 => 'cnpj'];

		// REGISTRO DETALHE
		$i = 1;
		foreach ($contas as $key => $dados) {
			$sacado_razao_social = $dados['Customer']['nome_secundario'];
			$sacado_tipo = $tipoP[$dados['Customer']['tipo_pessoa']];
			
			if ($dados['Customer']['documento'] != '') {
				$sacado_cnpj = $dados['Customer']['documento'];
			} else {
				die('Cliente '.$dados['Customer']['nome_secundario'].' sem '.$sacado_tipo.' cadastrado');
			}
			
			if ($dados['Customer']['cep'] != '') {
				$sacado_cep = $dados['Customer']['cep'];
			} else {
				die('Cliente '.$dados['Customer']['nome_secundario'].' sem cep cadastrado');
			}

			$sacado_logradouro = $dados['Customer']['endereco'];
			$sacado_bairro = $dados['Customer']['bairro'];
			$sacado_cidade = $dados['Customer']['cidade'];
			$sacado_uf = $dados['Customer']['estado'];

			$vencimento = date('Y-m-d', strtotime(str_replace('/', '-', $dados['Income']['vencimento'])));
			$data_multa = date('Y-m-d', strtotime($vencimento.' +1 day'));
			$data_cadastro = date('Y-m-d', strtotime(str_replace('/', '-', $dados['Income']['created'])));

			$pagador = new \Eduardokum\LaravelBoleto\Pessoa([
				'documento' => $sacado_cnpj,
				'nome'      => $sacado_razao_social,
				'cep'       => $sacado_cep,
				'endereco'  => $sacado_logradouro,
				'bairro' => $sacado_bairro,
				'uf'        => $sacado_uf,
				'cidade'    => $sacado_cidade,
			]);

			$boleto = new Eduardokum\LaravelBoleto\Boleto\Banco\Itau(
				[
					'logo'                   => realpath(APP . '/webroot/img/') . '341.png',
					'dataVencimento'         => new \Carbon\Carbon(),
					'valor'                  => $dados['Income']['valor_total_nao_formatado'],
					'multa'                  => false,
					'juros'                  => false,
					'numero'                 => $dados['Income']['id'],
					'numeroDocumento'        => $dados['Income']['id'],
					'pagador'                => $pagador,
					'beneficiario'           => $beneficiario,
					'diasBaixaAutomatica'    => 58,
					'carteira'               => 109,
					'agencia'                => 1111,
					'conta'                  => 99999,
					'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
					'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
					'aceite'                 => 'N',
					'especieDoc'             => 'DM',
				]
			);

			$remessa->addBoleto($boleto);
			
			$i++;

			$Income->updateAll(
				['Income.cnab_gerado' => 1, 'Income.cnab_lote_id' => $lote_id, 'Income.cnab_num_sequencial' => "'".$this->zerosEsq($key+1, 6)."'", 'Income.user_updated_id' => CakeSession::read('Auth.User.id'), 'Income.updated' => 'current_timestamp'], //set
				['Income.id' => $dados['Income']['id']] //where
			);
		}

		// para salvar
		$remessa->save(APP."Private/cnab_txt/".$nome_arquivo);

		return $nome_arquivo;
	}

	function zerosEsq($campo, $tamanho){
		$campo = substr($campo,0,$tamanho);

		$cp = str_pad($campo, $tamanho, 0, STR_PAD_LEFT);
		return $cp;
	}
}
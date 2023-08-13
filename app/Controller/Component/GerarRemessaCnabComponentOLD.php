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

		$codigo_banco = Cnab\Banco::CEF;

		$dados_banco = [
				'data_geracao'  			=> new DateTime(),
				'data_gravacao' 			=> new DateTime(), 
				'nome_fantasia' 			=> $this->nome_fantasia, // seu nome de empresa
				'razao_social'  			=> $this->razao_social,  // sua razão social
				'cnpj'          			=> $this->cnpj, // seu cnpj completo
				'logradouro'    			=> $this->logradouro,
				'numero'        			=> $this->numero,
				'bairro'        			=> $this->bairro, 
				'cidade'        			=> $this->cidade,
				'uf'            			=> $this->uf,
				'cep'           			=> $this->cep,
				'numero_sequencial_arquivo' 	=> $sequencia,
				'agencia'       			=> '4160', 
				'agencia_dv'       		=> '0', 
				'conta'         			=> '2086', // número da conta
				'operacao'     				=> '5', // digito da conta
				'banco'         			=> $codigo_banco, //código do banco
				'codigo_cedente' 			=> '382196',
		];

		$arquivo = new Cnab\Remessa\Cnab240\Arquivo($codigo_banco, 'sigcb');

		$arquivo->configure($dados_banco);
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
			
			// você pode adicionar vários boletos em uma remessa
			$arquivo->insertDetalhe(array(
			    'codigo_de_ocorrencia' => 1, // 1 = Entrada de título, futuramente poderemos ter uma constante
			    'nosso_numero'      => $dados['Income']['id'],
			    'numero_documento'  => $dados['Income']['id'],
			    'carteira'          => '109', // ?? NAO TENHO
			    'especie'           => Cnab\Especie::CEF_DUPLICATA_MERCANTIL, // Você pode consultar as especies Cnab\Especie ?? NAO TENHO
			    'valor'             => $dados['Income']['valor_total_nao_formatado'], // Valor do boleto
			    'instrucao1'        => 1, // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
			    'instrucao2'        => 0, // preenchido com zeros
			    'sacado_razao_social' => $sacado_razao_social, // O Sacado é o cliente, preste atenção nos campos abaixo
			    'sacado_tipo'       => $sacado_tipo, //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
			    'sacado_cnpj'       => $sacado_cnpj,
			    'sacado_logradouro' => $sacado_logradouro,
			    'sacado_bairro'     => $sacado_bairro,
			    'sacado_cep'        => str_replace(['-', '.'], '', $sacado_cep), // sem hífem
			    'sacado_cidade'     => $sacado_cidade,
			    'sacado_uf'         => $sacado_uf,
			    'data_vencimento'   => new DateTime($vencimento),
			    'data_cadastro'     => new DateTime($data_cadastro),
			    //'juros_de_um_dia'     => 0.33, // Valor do juros de 1 dia'
			    'juros_de_um_dia'     => 0, // Valor do juros de 1 dia'
			    'data_desconto'       => new DateTime('1901-01-01'), //
			    'valor_desconto'      => 0, // Valor do desconto
			    'prazo'               => 28, // prazo de dias para o cliente pagar após o vencimento
			    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
			    'mensagem'            => 'Descrição do boleto',
			    'data_multa'          => new DateTime($data_multa), // data da multa
			    //'valor_multa'         => 2, // valor da multa
			    'valor_multa'         => 0, // valor da multa
			    'aceite'         	  => 'N', // valor da multa
			    'registrado'          => 1, 
			    'modalidade_carteira' => 14,
			    'baixar_apos_dias'		=> 58,
			    'identificacao_distribuicao' => 0
			));
			$i++;

			$Income->updateAll(
				['Income.cnab_gerado' => 1, 'Income.cnab_lote_id' => $lote_id, 'Income.cnab_num_sequencial' => "'".$this->zerosEsq($key+1, 6)."'", 'Income.user_updated_id' => CakeSession::read('Auth.User.id'), 'Income.updated' => 'current_timestamp'], //set
				['Income.id' => $dados['Income']['id']] //where
			);
		}

		// para salvar
		$arquivo->save(APP."webroot/files/cnab_txt/".$nome_arquivo);

		return $nome_arquivo;
	}

	function zerosEsq($campo, $tamanho){
		$campo = substr($campo,0,$tamanho);

		$cp = str_pad($campo, $tamanho, 0, STR_PAD_LEFT);
		return $cp;
	}
}
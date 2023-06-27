<?php
class GetCepComponent extends Component {
	public $components = ['Session', 'Auth'];

	public function get($cep){
		$CepbrEndereco = ClassRegistry::init('CepbrEndereco');

		$endereco = $CepbrEndereco->find('first', ['conditions' => ['CepbrEndereco.cep' => $cep],
																							 'fields'     => ['CepbrEndereco.tipo_logradouro',
																																'CepbrEndereco.logradouro',
																																'CepbrBairro.bairro',
																																'CepbrCidade.cidade',
																																'CepbrCidade.uf']]);

		return $endereco;
	}
}
?>
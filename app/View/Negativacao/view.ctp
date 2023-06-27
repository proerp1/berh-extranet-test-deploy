<?php $erros = $this->data['CadastroPefinErros'] ?>
<?php $url = explode('/', $this->here);//echo $this->Html->script('validacoes'); ?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#CadastroPefinDataCompra").mask('99/99/9999');
		$('.money_exchange').maskMoney({
			decimal: ',',
			thousands: '.',
			precision: 2
		});

		<?php if ($url[3] == 'imprimir') { ?>
			window.print();
		<?php } else if($tipo != 'alterar' and count($erros) == 0) { ?>
			$('input').attr('disabled', true);
			$('select').attr('disabled', true);
		<?php } ?>
		var tipo_pessoa = $(".tipo_pessoa").val();

		if (tipo_pessoa == 2) {
			$("#CadastroPefinNome").parent().find('label').text('Nome');
			$("#CadastroPefinDocumento").parent().find('label').text('CPF');
			$("#CadastroPefinDocumento").attr('placeholder', 'CPF');
			$("#CadastroPefinDocumento").mask('999.999.999-99');
		} else {
			$("#CadastroPefinNome").parent().find('label').text('Razão Social');
			$("#CadastroPefinDocumento").parent().find('label').text('CNPJ');
			$("#CadastroPefinDocumento").attr('placeholder', 'CNPJ');
			$("#CadastroPefinDocumento").mask("99.999.999/9999-99");
		}

		var natureza = $("#CadastroPefinNaturezaOperacaoId").val();

		if (natureza == 23) {
			$(".info_banco").show();
			$(".info_banco").find('input').addAttr('required');
			$(".info_banco").find('select').addAttr('required');
		} else {
			$(".info_banco").hide();
			$(".info_banco").find('input').removeAttr('required');
			$(".info_banco").find('select').removeAttr('required');
		}

		var coobrigado = $(".temCoobrigado:checked").val();

		if (coobrigado == 1) {
			$(".div_tem_coobrigado").show();
			$(".div_tem_coobrigado").find('input').addClass('valida');
		} else {
			$(".div_tem_coobrigado").hide();
			$(".div_tem_coobrigado").find('input').removeClass('valida');
		}

		var tipo_pessoa_coobrigado = $(".tipo_pessoa_coobrigado").val();

		if (tipo_pessoa_coobrigado == 2) {
			$("#CadastroPefinCoobrigadoNome").parent().find('label').text('Nome do coobrigado');
			$("#CadastroPefinCoobrigadoDocumento").parent().find('label').text('CPF do coobrigado');
			$("#CadastroPefinCoobrigadoDocumento").mask('999.999.999-99');
		} else {
			$("#CadastroPefinCoobrigadoNome").parent().find('label').text('Razão Social do coobrigado');
			$("#CadastroPefinCoobrigadoDocumento").parent().find('label').text('CNPJ do coobrigado');
			$("#CadastroPefinCoobrigadoDocumento").mask("99.999.999/9999-99");
		}
	})
</script>

<div class="card mb-5 mb-xl-8">
	<div class="card-body pt-7 py-3">
		<?php echo $this->Form->create('CadastroPefin', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
			<?php if (count($erros) > 0): ?>
				<div class="row-fluid">
					<div class="span6">
						<div class="alert alert-danger" role="alert">Erro(s)! <br>
							<?php
								for ($i=0; $i < count($erros); $i++) { 
									echo $erros[$i]['ErrosPefin']['descricao'].'<br>';
								}
							?>
						</div>
					</div>
				</div>
			<?php endif ?>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Natureza de operação</label>
				<?php echo $this->Form->input('natureza_operacao_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Tipo de pessoa</label>
				<?php echo $this->Form->input('tipo_pessoa', ["class" => "form-select tipo_pessoa mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => [2 => "Física", 1 => "Jurídica"]]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">CPF</label>
				<?php echo $this->Form->input('documento', ["placeholder" => "CPF", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Nome</label>
				<?php echo $this->Form->input('nome', ["placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">CEP</label>
				<?php echo $this->Form->input('cep', ["placeholder" => "CEP", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Endereço</label>
				<?php echo $this->Form->input('endereco', ["placeholder" => "Endereço", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Número</label>
				<?php echo $this->Form->input('numero', ["placeholder" => "Número", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Complemento</label>
				<?php echo $this->Form->input('complemento', ["placeholder" => "Complemento", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Bairro</label>
				<?php echo $this->Form->input('bairro', ["placeholder" => "Bairro", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Cidade</label>
				<?php echo $this->Form->input('cidade', ["placeholder" => "Cidade", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Estado</label>
				<?php echo $this->Form->input('estado', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => ["AC" => "Acre", "AL" => "Alagoas", "AP" => "Amapá", "AM" => "Amazonas", "BA" => "Bahia", "CE" => "Ceará", "DF" => "Distrito Federal", "ES" => "Espirito Santo", "GO" => "Goiás", "MA" => "Maranhão", "MT" => "Mato Grosso", "MS" => "Mato Grosso do Sul", "MG" => "Minas Gerais", "PA" => "Pará", "PB" => "Paraiba", "PR" => "Paraná", "PE" => "Pernambuco", "PI" => "Piauí", "RJ" => "Rio de Janeiro", "RN" => "Rio Grande do Norte", "RS" => "Rio Grande do Sul", "RO" => "Rondônia", "RR" => "Roraima", "SC" => "Santa Catarina", "SP" => "São Paulo", "SE" => "Sergipe", "TO" => "Tocantins"]]);?>
			</div>

			<div class="info_banco">
				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Nº Banco</label>
					<?php echo $this->Form->input('num_banco', ["placeholder" => "Nº Banco", "class" => "form-control mb-3 mb-lg-0"]);?>
				</div>

				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Agência</label>
					<?php echo $this->Form->input('num_agencia', ["placeholder" => "Agência", "class" => "form-control mb-3 mb-lg-0"]);?>
				</div>

				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Conta corrente</label>
					<?php echo $this->Form->input('num_conta_corrente', ["placeholder" => "Conta corrente", "class" => "form-control mb-3 mb-lg-0"]);?>
				</div>

				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Nº cheque</label>
					<?php echo $this->Form->input('num_cheque', ["placeholder" => "Nº cheque", "class" => "form-control mb-3 mb-lg-0"]);?>
				</div>

				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Alínea</label>
					<?php echo $this->Form->input('alinea', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => [12 => 12, 13 => 13, 14 => 14]]);?>
				</div>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Data da compra</label>
				<?php echo $this->Form->input('data_compra', ["type" => "text", "placeholder" => "Data da compra", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Nosso número</label>
				<?php echo $this->Form->input('nosso_numero', ["placeholder" => "Nosso número", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Número do título</label>
				<?php echo $this->Form->input('numero_titulo', ["placeholder" => "Número do título", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Venc da dívida</label>
				<?php echo $this->Form->input('venc_divida', ["type" => "text", "placeholder" => "Venc da dívida", "class" => "form-control mb-3 mb-lg-0"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Valor</label>
				<?php echo $this->Form->input('valor', ["type" => "text", "placeholder" => "Valor", "class" => "form-control money_exchange mb-3 mb-lg-0"]);?>
			</div>

			<?php if (isset($this->request->data['CadastroPefin']['data_inclusao']) != ''): ?>
				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Data da inclusão</label>
					<p><?php echo $this->request->data['CadastroPefin']['data_inclusao'] != '' ? date('d/m/Y', strtotime($this->request->data['CadastroPefin']['data_inclusao'])) : '' ?></p>
				</div>
			<?php endif ?>

			<?php if ($this->request->data['CadastroPefin']['motivo_baixa_id'] != null): ?>
				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Motivo da baixa</label>
					<p><?php echo $this->request->data['MotivoBaixa']['nome'].' - '.$this->request->data['MotivoBaixa']['descricao'] ?></p>
				</div>

				<?php echo $this->Form->input('motivo_baixa_id', array("div" => false, "label" => false, "readonly" => true, "type" => 'hidden', "class" => "form-control", "empty" => "Selecione"));  ?>

				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Data de solicitação da baixa</label>
					<p><?php echo $this->request->data['CadastroPefin']['data_solic_baixa'] != '' ? date('d/m/Y H:i:s', strtotime($this->request->data['CadastroPefin']['data_solic_baixa'])) : ' - ' ?></p>
				</div>
			<?php endif ?>
			
			<?php if ($this->request->data['CadastroPefin']['status_id'] == 24): ?>
				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Data da baixa</label>
					<p><?php echo $this->request->data['CadastroPefin']['data_baixa'] != '' ? date('d/m/Y', strtotime($this->request->data['CadastroPefin']['data_baixa'])) : '' ?></p>
				</div>
			<?php endif ?>

			<div class="form-check form-check-custom form-check-solid mb-7">
				<div class="row">
					<div class="col-12">
						<label class="fw-semibold fs-6 mb-2">Tem coobrigado</label>
					</div>
				    <div class="col-12">
				    	<input disabled name="temCoobrigado" class="temCoobrigado form-check-input" type="radio" value="1" id="temCoobrigadoSim" <?php echo (isset($this->request->data['CadastroPefin']) != '' ? ($this->request->data['CadastroPefin']['coobrigado_nome'] != '' ? 'checked' : '') : '') ?>>
				    	<label class="form-check-label" for="temCoobrigadoSim">
				    	    Sim
				    	</label>
				    	<input disabled name="temCoobrigado" class="temCoobrigado form-check-input" type="radio" value="2" id="temCoobrigadoNao" <?php echo (isset($this->request->data['CadastroPefin']) != '' ? ($this->request->data['CadastroPefin']['coobrigado_nome'] != '' ? '' : 'checked') : 'checked') ?>>
				    	<label class="form-check-label" for="temCoobrigadoNao">
				    	    Não
				    	</label>
				    </div>
				</div>
			</div>

			<div class="div_tem_coobrigado" style="display:none">
				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Tipo de pessoa</label>
					<?php echo $this->Form->input('coobrigado_tipo_pessoa', ["required" => false, "class" => "form-select tipo_pessoa_coobrigado mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => [2 => "Física", 1 => "Jurídica"]]);?>
				</div>

				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">CPF do coobrigado</label>
					<?php echo $this->Form->input('coobrigado_documento', ["required" => false, "placeholder" => "CPF do coobrigado", "class" => "form-control mb-3 mb-lg-0"]);?>
				</div>

				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Nome do coobrigado</label>
					<?php echo $this->Form->input('coobrigado_nome', ["required" => false, "placeholder" => "Nome do coobrigado", "class" => "form-control mb-3 mb-lg-0"]);?>
				</div>
			</div>

			<div class="mb-7">
				<div class="col-sm-offset-2 col-sm-9">
					<button type="button" onclick="history.go(-1)" class="btn btn-light-dark">Voltar</button>
					<?php if ($tipo == 'alterar') { ?>
						<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Incluir</button>
					<?php } ?>
				</div>
			</div>

		</form>
	</div>
</div>
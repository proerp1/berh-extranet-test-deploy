<?php
    $status_id = '';
    if (isset($this->data['CadastroPefin']['id'])) {
        $status_id = $this->data['Status']['id'];
    }
    ?>
<?php echo $this->Html->script('validacoes', ['block' => 'script']); ?>

<script id="template-coobrigado" type="text/x-handlebars-template">
	<div class="coobrigado{{qtde}}">
        <div class="mb-7">
            <label class="fw-semibold fs-6 mb-2 coobrigado">Coobrigado {{qtde}}</label>
            <a class="btn btn-danger deleta_linha" href="" rel="tooltip" title="Excluir"> Remover</a>
        </div>

        <div class="mb-7">
            <label class="fw-semibold fs-6 mb-2">Tipo de pessoa</label>
            <select name="coobrigado_tipo_pessoa[]" class="form-select mb-3 mb-lg-0 tipo_pessoa_coobrigado" required>
                <option value="">Selecione</option>
                <option value="2">Física</option>
                <option value="1">Jurídica</option>
            </select>
        </div>

        <div class="mb-7">
            <label class="fw-semibold fs-6 mb-2">CPF do coobrigado (Fiador ou Avalista)</label>
            <input type="text" name="coobrigado_documento[]" placeholder="CPF do coobrigado" class="form-control mb-3 mb-lg-0 cpf_coobrigado" required>
        </div>

        <div class="mb-7">
            <label class="fw-semibold fs-6 mb-2">Coobrigado (Fiador ou Avalista)</label>
            <input type="text" name="coobrigado_nome[]" placeholder="Nome do coobrigado" class="form-control mb-3 mb-lg-0 nome_coobrigado" required>
        </div>
    </div>
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$(".info_banco").hide();
		<?php if ($this->request->params['action'] == 'imprimir') { ?>
			window.print();
		<?php } ?>

		<?php if ($acao == 'view' && $status_id != '23') { ?>
			$("form").find('input').prop('disabled', true);
			$("form").find('select').prop('disabled', true);
		<?php } ?>

		$('.money_exchange').maskMoney({
			decimal: ',',
			thousands: '.',
			precision: 2
		});

		$(".tipo_pessoa").on("change", function(){
			var val = $(this).val();

			tipo_pessoa(val);
		})

		tipo_pessoa($(".tipo_pessoa").val());

		$("body").delegate(".tipo_pessoa_coobrigado", "change", function(){
			tipo_pessoa_coobrigado($(this));
		})

		tipo_pessoa_coobrigado($(".tipo_pessoa_coobrigado"));

		$("#CadastroPefinValor").on("focusout", function(){
			var valor = $(this).val().replace('.', '');
			valor = valor.replace(',', '.');

			if (parseFloat(valor) < 15) {
				$(this).parent().find(".error-message").remove();
                $(this).parent().find('input').addClass("form-error");
                $(this).parent().append('<span class="error-message" style="">O valor não pode ser menor que R$ 15,00!</span>');
                $(".js-salvar").prop('disabled', true);
			} else {
                $(this).parent().find('input').removeClass("form-error");
                $(this).parent().find(".error-message").remove();
                $(".js-salvar").prop('disabled', false);
			}
		});

		$("#CadastroPefinDataCompra").on("focusout", function(){
			if (calculaMaioridade($("#CadastroPefinDataCompra").val()) >= 5 ){
				alert("Data Inválida, A data de compra não pode ser superior a 4 anos e 11 meses!");
				$("#CadastroPefinDataCompra").focus();
				$(".btn-success").hide();
			} else {
				$(".btn-success").show();
			}
		})

		$(".temCoobrigado").on("change", function(){
			var val = $(".temCoobrigado:checked").val();

			div_tem_coobrigado(val);
		})

		div_tem_coobrigado($(".temCoobrigado:checked").val());

		$("#CadastroPefinNaturezaOperacaoId").on("change", function(){
			var val = $(this).val();

			natureza(val);
		})

		natureza($("#CadastroPefinNaturezaOperacaoId").val());

		$("#CadastroPefinDocumento").on("change", function(){
			var val = $(this).val();
			var el = $(this);

			valida_doc(val, el);
		});

		$("#CadastroPefinCoobrigadoDocumento").on("change", function(){
			var val = $(this).val();
			var el = $(this);

			valida_doc(val, el);
		});

		$("body").delegate(".adiciona-coobrigado-div", "click", function(){
			var source   = $("#template-coobrigado").html();
			var template = Handlebars.compile(source);

			var qtde_coobrigado = parseInt($(".coobrigado").length+1);

			var context = {qtde: parseInt($(".coobrigado").length+1)};
			var html    = template(context);

			$(".div_tem_coobrigado").append(html);
			if (qtde_coobrigado == 3) {
				$("#botaoAdicionar").hide();
			};

			event.preventDefault();
		});

		$("body").on("click", ".deleta_linha", function(event){
			$bt = $(this);

			$bt.parent().parent().remove();
			if ($(".coobrigado").length < 3) {
				$("#botaoAdicionar").show();
			};
			event.preventDefault();
		});

		$("#CadastroPefinDocumento").mask('999.999.999-99');
		$("#CadastroPefinCoobrigadoDocumento").mask('999.999.999-99');
		$("#CadastroPefinCep").mask('99999-999');
		$("#CadastroPefinDataCompra").mask('99/99/9999');
		$("#CadastroPefinVencDivida").mask('99/99/9999');

		$(".onlyNumber").keyup(function() {
			$(this).val(this.value.match(/[0-9]*/));
		});

		$(".onlyLetter").keyup(function() {
			$(this).val(this.value.match(/[a-zA-Z\s]*/));
		});

		$(".datepicker2").datepicker({format: 'dd/mm/yyyy', weekStart: 1, autoclose: true, language: "pt-BR", todayHighlight: true, toggleActive: true, endDate: "today"});

		$("#CadastroPefinCep").change(function() {
			var $el = $(this);

			$el.parent().append("<img src='"+base_url+"/img/loading.gif' class='loading_img'>");
			$.getJSON('https://api.postmon.com.br/v1/cep/' + $(this).val())
				.success(function(data) {
					$(".loading_img").remove();
					$("#CadastroPefinEndereco").val(data["logradouro"]);
					$("#CadastroPefinBairro").val(data["bairro"]);
					$("#CadastroPefinCidade").val(data["cidade"]);
					$("#CadastroPefinEstado").val(data["estado"]);
					$("#CadastroPefinNumero").focus();
				}).error(function(data) {
					$(".loading_img").remove();
					alert('Informe um CEP válido.');
				});
		});
	});

	function natureza(val){
		if (val == 23) {
			$(".info_banco").show();
			$(".info_banco").find('input').prop('required', true);
			$(".info_banco").find('select').prop('required', true);
			$("#CadastroPefinNossoNumero").prop('required', false).parent().hide();
			$("#CadastroPefinNumeroTitulo").prop('required', false).parent().hide();
		} else {
			$(".info_banco").hide();
			$(".info_banco").find('input').prop('required', false);
			$(".info_banco").find('select').prop('required', false);
			$("#CadastroPefinNossoNumero").prop('required', true).parent().show();
			$("#CadastroPefinNumeroTitulo").prop('required', true).parent().show();
		}
	}

	function tipo_pessoa(val){
		if (val == 2) {
			$("#CadastroPefinNome").attr('placeholder', 'Nome').parent().find('label').text('Nome');
			$("#CadastroPefinDocumento").attr('placeholder', 'CPF').parent().find('label').text('CPF');
			$("#CadastroPefinDocumento").mask('999.999.999-99');
		} else {
			$("#CadastroPefinNome").attr('placeholder', 'Razão Social').parent().find('label').text('Razão Social');
			$("#CadastroPefinDocumento").attr('placeholder', 'CNPJ').parent().find('label').text('CNPJ');
			$("#CadastroPefinDocumento").mask("99.999.999/9999-99");
		}
	}

	function tipo_pessoa_coobrigado(el){
		var val = el.val();

		if (val == 2) {
			el.parent().parent().find(".nome_coobrigado").attr('placeholder', 'Nome do Coobrigado').parent().find('label').text('Nome do coobrigado');
			el.parent().parent().find(".cpf_coobrigado").attr('placeholder', 'CPF do coobrigado').parent().find('label').text('CPF do coobrigado');
			el.parent().parent().find(".cpf_coobrigado").mask('999.999.999-99');
		} else {
			el.parent().parent().find(".nome_coobrigado").attr('placeholder', 'Razão Social do coobrigado').parent().find('label').text('Razão Social do coobrigado');
			el.parent().parent().find(".cpf_coobrigado").attr('placeholder', 'CNPJ do coobrigado').parent().find('label').text('CNPJ do coobrigado');
			el.parent().parent().find(".cpf_coobrigado").mask("99.999.999/9999-99");
		}
	}

	function div_tem_coobrigado(val) {
		if (val == 1) {
			$(".div_tem_coobrigado").show();
			$("#botaoAdicionar").show();
			$(".div_tem_coobrigado").find('input').prop('required', true);
		} else {
			$(".div_tem_coobrigado").hide();
			$("#botaoAdicionar").hide();
			$(".div_tem_coobrigado").find('input').prop('required', false);
		}
	}

	function valida_doc(val, el){
		if ($(".tipo_pessoa").val() == 2) {
			var valida = validar_cpf(val);
		} else {
			var valida = valida_cnpj(val);
		}

		console.log(valida);

		if (valida) {
			el.parent().parent().removeClass("error");
			$(".error-message").remove();
			$(".js-salvar").show();
		} else {
			$(".js-salvar").hide();
			el.parent().parent().addClass("error");
			el.parent().append('<span class="error-message" style="">Documento inválido!</span>');
		}
	}

	function calculaMaioridade(nasc) {
		var hoje = new Date(), idade;
		var arrayNasc = nasc.split("/");
		
		if (arrayNasc.length == 3) {	
			var anoNasc = parseInt( arrayNasc[2] );
			var mesNasc = parseInt( arrayNasc[1] );
			var diaNasc = parseInt( arrayNasc[0] );
		} else {
			return 0;	
		}
		
		if ( arrayNasc[0] < 1 || arrayNasc[0] > 31 ) {
			 return 0;
		}

		if ( arrayNasc[1] < 1 || arrayNasc[1] > 12 ) {
			 return 0;
		}
		
		if ( arrayNasc[2] < 1900 || arrayNasc[2] > hoje.getFullYear() ) {
			 return 0;
		}
		
		idade = ( hoje.getFullYear()) - anoNasc;
		var meses = ( hoje.getMonth() + 1 ) - mesNasc;
		idade = ( meses <= 0 ) ? idade - 1 : idade;
		return idade;
	}
</script>

<?php
    echo $this->element("abas_customers", ['id' => $id]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">

        <?php echo $this->Form->create('CadastroPefin', ["id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
            <?php
                if ($acao == 'view') {
                $erros = $this->data['CadastroPefinErros']; ?>
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
            <?php } ?>

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

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Tem coobrigado</label>
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input temCoobrigado" type="radio" value="1" name="data[CadastroPefin][tem_coobrigado]" id="recorrSim" <?php echo(isset($this->request->data['CadastroPefin']['id']) ? ($this->request->data['CadastroPefin']['coobrigado_nome'] != '' ? 'checked' : '') : '') ?> />
                    <label class="form-check-label me-3" for="recorrSim">
                        Sim
                    </label>
                    <input class="form-check-input temCoobrigado" type="radio" value="2" name="data[CadastroPefin][tem_coobrigado]" id="recorrNão" <?php echo(isset($this->request->data['CadastroPefin']['id']) ? ($this->request->data['CadastroPefin']['coobrigado_nome'] != '' ? '' : 'checked') : 'checked') ?> />
                    <label class="form-check-label" for="recorrNão">
                        Não
                    </label>
                </div>
            </div>

            <div class="div_tem_coobrigado" style="display:none">
                <div class="coobrigado1">
                    <?php if (!isset($this->request->data['CadastroPefin']['id'])): ?>
                        <div class="mb-7">
                            <label class="fw-semibold fs-6 mb-2 coobrigado">Coobrigado 1</label>
                        </div>
                    <?php endif ?>

                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Tipo de pessoa</label>
                        <select name="coobrigado_tipo_pessoa[]" class="form-select tipo_pessoa_coobrigado mb-3 mb-lg-0">
                            <option value="">Selecione</option>
                            <option value="2" <?php echo(isset($this->request->data['CadastroPefin']['id']) ? ($this->request->data['CadastroPefin']['coobrigado_tipo_pessoa'] == 2 ? 'selected' : '') : '') ?>>Física</option>
                            <option value="1" <?php echo(isset($this->request->data['CadastroPefin']['id']) ? ($this->request->data['CadastroPefin']['coobrigado_tipo_pessoa'] == 1 ? 'selected' : '') : '') ?>>Jurídica</option>
                        </select>
                    </div>

                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">CPF do coobrigado</label>
                        <input type="text" name="coobrigado_documento[]" placeholder="CPF do coobrigado" class="form-control mb-3 mb-lg-0 cpf_coobrigado" value="<?php echo(isset($this->request->data['CadastroPefin']['id']) ? $this->request->data['CadastroPefin']['coobrigado_documento'] : '') ?>">
                    </div>

                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Coobrigdo (Fiador ou Avalista)</label>
                        <input type="text" name="coobrigado_nome[]" placeholder="Nome do coobrigado" class="form-control mb-3 mb-lg-0 nome_coobrigado" value="<?php echo(isset($this->request->data['CadastroPefin']['id']) ? $this->request->data['CadastroPefin']['coobrigado_nome'] : '') ?>">
                    </div>
                </div>
            </div>

            <?php if (!isset($this->request->data['CadastroPefin']['id'])): ?>
                <div class="mb-7" id="botaoAdicionar" style="display:none">
                    <label class="fw-semibold fs-6 mb-2"></label>
                    <button type="button" class="btn btn-success adiciona-coobrigado-div" rel="tooltip" title="Adicionar">Adicionar coobrigado</button>
                </div>
            <?php endif ?>

            <?php if (isset($this->request->data['CadastroPefin']['id'])): ?>
                <?php if ($this->request->data['CadastroPefin']['motivo_baixa_id'] != null): ?>
                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Motivo da baixa</label>
                        <p><?php echo $this->request->data['MotivoBaixa']['nome'].' - '.$this->request->data['MotivoBaixa']['descricao'] ?></p>
                    </div>
                    <?php echo $this->Form->input('motivo_baixa_id', ["readonly" => true, "type" => 'hidden', "class" => "form-control", "empty" => "Selecione"]);  ?>
                <?php endif ?>

                <?php if ($status_id == '24'): ?>

                    <?php if ($id == 18275): ?>
                        <div class="mb-7">
                            <label class="fw-semibold fs-6 mb-2">Data de solicitação da baixa</label>
                            <p><?php echo $this->request->data['CadastroPefin']['data_solic_baixa'] != '' ? date('d/m/Y', strtotime($this->request->data['CadastroPefin']['data_solic_baixa'])) : '' ?></p>
                        </div>
                    <?php endif ?>
                    
                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Data da baixa</label>
                        <p><?php echo $this->request->data['CadastroPefin']['data_baixa'] != '' ? date('d/m/Y', strtotime($this->request->data['CadastroPefin']['data_baixa'])) : '' ?></p>
                    </div>
                <?php endif ?>
            <?php endif ?>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customers/negativacoes/'.$id; ?>" class="btn btn-light-dark">Voltar</a>
                    <?php if ($acao != 'view' || $status_id == '23' && (CakeSession::read('Auth.CustomerUser.resale') == 0 && CakeSession::read('Auth.CustomerUser.seller') == 0)) { ?>
                    	<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Incluir</button>
                    <?php } ?>
                </div>
            </div>

        </form>
    </div>
</div>
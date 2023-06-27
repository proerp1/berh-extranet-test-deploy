<script type="text/javascript">
    $(document).ready(function() {
        $(".info_banco").hide();

        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        $("#CustomerPefinValor").on("focusout", function(){
            var valor = $(this).val().replace('.', '');
            valor = valor.replace(',', '.');

            if (parseFloat(valor) < 15) {
                alert('O valor não pode ser menor que R$ 15,00');
                $(".js-salvar").prop('disabled', true);
            } else {
                $(".js-salvar").prop('disabled', false);
            }
        });

        $("#CustomerPefinDataCompra").on("focusout", function(){
            if (calculaMaioridade($("#CustomerPefinDataCompra").val()) >= 5 ){
                alert("Data Inválida, A data de compra não pode ser superior a 4 anos e 11 meses!");
                $("#CustomerPefinDataCompra").focus();
                $(".btn-success").hide();
            } else {
                $(".btn-success").show();
            }
        })

        $("#CustomerPefinNaturezaOperacaoId").on("change", function(){
            var val = $(this).val();

            natureza(val);
        })

        natureza($("#CustomerPefinNaturezaOperacaoId").val());

        $("#CustomerPefinDataCompra").mask('99/99/9999');
        $("#CustomerPefinVencDivida").mask('99/99/9999');

        $(".datepicker2").datepicker({format: 'dd/mm/yyyy', weekStart: 1, autoclose: true, language: "pt-BR", todayHighlight: true, toggleActive: true, endDate: "today"});
    });

    function natureza(val){
        if (val == 23) {
            $(".info_banco").show();
            $(".info_banco").find('input').prop('required', true);
            $(".info_banco").find('select').prop('required', true);
            $("#CustomerPefinNossoNumero").prop('required', false).parent().parent().hide();
            $("#CustomerPefinNumeroTitulo").prop('required', false).parent().parent().hide();
        } else {
            $(".info_banco").hide();
            $(".info_banco").find('input').prop('required', false);
            $(".info_banco").find('select').prop('required', false);
            $("#CustomerPefinNossoNumero").prop('required', true).parent().parent().show();
            $("#CustomerPefinNumeroTitulo").prop('required', true).parent().parent().show();
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
    $url = $this->base.'/customers/negativacoes_cliente';
    echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerPefin', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Natureza de operação</label>
                <?php echo $this->Form->input('natureza_operacao_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="info_banco">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Nº Banco</label>
                    <?php echo $this->Form->input('num_banco', array("placeholder" => "Nº Banco", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Agência</label>
                    <?php echo $this->Form->input('num_agencia', array("placeholder" => "Agência", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Conta corrente</label>
                    <?php echo $this->Form->input('num_conta_corrente', array("placeholder" => "Conta corrente", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Nº cheque</label>
                    <?php echo $this->Form->input('num_cheque', array("placeholder" => "Nº cheque", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Alínea</label>
                    <?php echo $this->Form->input('alinea', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => [12 => 12, 13 => 13, 14 => 14]]);?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Data da compra</label>
                <?php echo $this->Form->input('data_compra', array("type" => "text", "placeholder" => "Data da compra", "class" => "form-control mb-3 mb-lg-0 datepicker2"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nosso número</label>
                <?php echo $this->Form->input('nosso_numero', array("placeholder" => "Nosso número", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Número do título</label>
                <?php echo $this->Form->input('numero_titulo', array("placeholder" => "Número do título", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Venc da dívida</label>
                <?php echo $this->Form->input('venc_divida', array("type" => "text", "placeholder" => "Venc da dívida", "class" => "form-control mb-3 mb-lg-0 datepicker2"));  ?>
            </div>

            <div class="mb-7">
                <label for="cep" class="form-label">Valor</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('valor', array("type" => "text", "placeholder" => "Data Prorede", "class" => "form-control money_exchange mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customers/negativacoes_cliente/'.$id; ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Incluir</button>
                </div>
            </div>

        </form>
    </div>
</div>
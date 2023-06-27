<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('RetornoCnab', ["id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false], 'enctype' => 'multipart/form-data']); ?>

            <div class="mb-7 col">
                <?php echo $this->Form->input('arquivo', ["div" => false, "label" => false, "required" => false, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"]);  ?>
            </div>

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Deseja registrar as contas com uma data diferente?</label>
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input data_diferente" type="radio" value="1" name="data[RetornoCnab][data_diferente]" id="sim" />
                    <label class="form-check-label me-3" for="sim">
                        Sim
                    </label>
                    <input class="form-check-input data_diferente" type="radio" value="2" name="data[RetornoCnab][data_diferente]" id="nao" checked />
                    <label class="form-check-label" for="nao">
                        Não
                    </label>
                </div>
            </div>

            <div class="mb-7 div_data_pagamento" style="display: none">
                <label class="form-label">Data de recebimento</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <?php echo $this->Form->input('data_pagamento', ["type" => "text", "placeholder" => "Data de recebimento", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/retorno_cnabs/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".data_diferente").on('change', function(event) {
            if ($(this).val() == 1) {
                $(".div_data_pagamento").show();
            } else {
                $(".div_data_pagamento").hide();
            }
        });
    });
</script>
<?php echo $this->Html->script('jquery-maskmoney'); ?>

<script>
    $(document).ready(function(){
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    });
</script>

<div class="page page-profile">

    <?php 
        echo $this->element("abas_products", array('tipo' => $product['Product']['tipo']));
    ?>

    <section class="panel panel-default">
        <div class="panel-body">
            <?php echo $this->Form->create('Feature', array("id" => "js-form-submit", "class" => "form-horizontal col-md-12", "action" => "/".$form_action."/", "method" => "post")); ?>

                <div class="form-group">
                    <label for="inputNome" class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-9">
                        <?php echo $this->Form->input('status_id', array("div" => false, "label" => false, "placeholder" => "Status", "class" => "form-control", "empty" => "Selecione"));  ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputNome" class="col-sm-2 control-label">Nome</label>
                    <div class="col-sm-9">
                        <?php echo $this->Form->input('name', array("div" => false, "label" => false, "placeholder" => "Nome", "class" => "form-control"));  ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputNome" class="col-sm-2 control-label">Data de ativação</label>
                    <div class="col-sm-9">
                        <?php echo $this->Form->input('data_ativacao', array("type" => "text", "div" => false, "label" => false, "placeholder" => "Data de ativação", "class" => "form-control datepicker"));  ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Valor da Feature</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">R$</span>
                            <?php echo $this->Form->input('valor', array("type" => "text", "div" => false, "label" => false, "placeholder" => "Valor da Feature", "class" => "form-control money_exchange"));  ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Valor mínimo</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">R$</span>
                            <?php echo $this->Form->input('valor_minimo', array("type" => "text", "div" => false, "label" => false, "placeholder" => "Valor mínimo", "class" => "form-control money_exchange"));  ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Descrição</label>
                    <div class="col-sm-9">
                        <?php echo $this->Form->input('descricao', array("div" => false, "type" => "textarea", "label" => false, "placeholder" => "Descrição", "class" => "form-control", 'style' => 'height:100px')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-9">
                        <a href="<?php echo $this->Html->url(['action' => 'features_string', $id]); ?>" class="btn btn-default">Voltar</a>
                        <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                    </div>
                </div>
            </form>
        </div> <!-- /panel-body -->
    </section>
</div> <!-- /page-profile -->
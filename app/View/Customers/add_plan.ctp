<script type="text/javascript">
    $(document).ready(function(){
        $("#PlanCustomerPlanId").change(function(){
            var id = $(this).val();
            var $el = $(this);
            
            $.ajax({
                url: "<?php echo $this->base?>/customers/get_plan_value/",
                type: "post",
                data: {id : id},
                dataType: "json",
                beforeSend: function(xhr){
                    $el.parent().find('label').append("&nbsp;<img src='"+base_url+"/img/loading.gif' class='loading_img'>");
                },
                success: function(data){
                    console.log(data);
                    $(".loading_img").remove(); 
                    $('#PlanCustomerMensalidade').val(data);
                }
            });
        })
    })
</script>

<?php
    $url = $this->base.'/customers/plans/';
    echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('PlanCustomer', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
            <?php if(isset($id)){ ?>
                <textarea name="log_old_value" style="display:none"><?php echo json_encode(array('PlanCustomer' => $this->request->data['PlanCustomer'])); ?></textarea>
            <?php } ?>
            <input type="hidden" name="data[PlanCustomer][customer_id]" value="<?php echo $id ?>">

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Plano</label>
                <select name="data[PlanCustomer][plan_id]" id="PlanCustomerPlanId" class="form-select mb-3 mb-lg-0" data-control="select2">
                    <option value="">Selecione</option>
                    <?php
                        for($a = 0; $a < count($plans); $a++){
                            $selected = "";
                            if (isset($this->request->data["PlanCustomer"])) {
                                if($plans[$a]['Plan']['id'] == $this->request->data["Plan"]["id"]){
                                    $selected = "selected";
                                }
                            }
                            echo '<option value="'.$plans[$a]['Plan']['id'].'" '.$selected.'>'.$plans[$a]['Plan']['description'].' - '.$plans[$a]['Plan']['value'].'</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="mb-7">
                <label for="cep" class="form-label">Mensalidade</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('mensalidade', array("type" => "text", "readonly" => true, "placeholder" => "Mensalidade", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Procuração?</label>
                <?php echo $this->Form->input('procuracao', array("empty" => "Selecione", "data-control" => "select2", 'options' => array('1' => 'Sim', '2' => 'Não'), "class" => "form-select mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Tabela de Preço</label>
                <?php echo $this->Form->input('price_table_id', array("data-control" => "select2", "class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione"));  ?>
            </div>

            <?php if (!isset($plan_id)) { ?>
                <div class="mb-7">
                    <label class="fw-semibold fs-6 mb-2">Status</label>
                    <?php echo $this->Form->input('status_id', array("data-control" => "select2", "class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione"));  ?>
                </div>
            <?php } else { ?>
                <div class="mb-7">
                    <label class="fw-semibold fs-6 mb-2">Status</label>
                    <p><?php echo $this->request->data['Status']['name']  ?></p>
                </div>
            <?php } ?>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customers/plans/'.$id; ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                    <?php if (isset($plan_id)) { ?>
                        <?php if ($cancelarPlano && $this->request->data['Status']['id'] == 1) { ?>
                            <a href="<?php echo $this->base.'/customers/update_status/2/'.$plan_id; ?>" class="btn btn-danger">Cancelar</a>
                        <?php } ?>
                        <?php if (!$cancelarPlano && $this->request->data['Status']['id'] != 45) { ?>
                            <a href="<?php echo $this->base.'/customers/update_status/45/'.$plan_id; ?>" class="btn btn-danger">Solicitar Cancelamento</a>
                        <?php } else if ($cancelarPlano && $this->request->data['Status']['id'] == 45) { ?>
                            <a href="<?php echo $this->base.'/customers/update_status/2/'.$plan_id; ?>" class="btn btn-danger">Aprovar Cancelamento</a>
                            <a href="<?php echo $this->base.'/customers/update_status/1/'.$plan_id; ?>" class="btn btn-danger">Reprovar Cancelamento</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>

            <?php if (isset($plan_id) and CakeSession::read("Auth.User.group_id") == 1): ?>
                <i>Alterado por <?php echo $this->request->data['UsuarioAlteracao']['name'] ?> em <?php echo date('d/m/Y H:i:s', strtotime($this->request->data['PlanCustomer']['updated'])) ?></i>
            <?php endif ?>
        </form>
    </div>
</div>
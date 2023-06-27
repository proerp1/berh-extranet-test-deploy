<?php echo $this->Html->script("html_editor/summernote", ['inline' => false]); ?>
<?php echo $this->Html->script("html_editor/summernote-pt-BR", ['inline' => false]); ?>
<?php echo $this->Html->css("html_editor/summernote", ['inline' => false]); ?>

<script>
    $(document).ready(function(){
        $('.summernote').summernote({
            lang: 'pt-BR',
            height: 300,
            toolbar : [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize', 'fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['group', ['link', 'hr' ]],
                ['misc', [ 'codeview', 'undo', 'redo' ]],
                ['help', [ 'help' ]],
            ]
        });

        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        $("#ProductTipo").on("change", function(){
            var val = $(this).val();

            showFields(val);
        });

        showFields($("#ProductTipo").val());
    });

    function showFields(val){
        $("#ProductFrequency").parent().hide();

        if (val == 3) {
            $("#ProductFrequency").parent().show();
        }
    }
</script>
<?php
    if (isset($id)) {
        echo $this->element("abas_products", ['tipo' => $this->request->data['Product']['tipo'], 'id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Product', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Tipo</label>
                <?php echo $this->Form->input('tipo', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['1' => 'SERASA', '2' => 'BeRH', '3' => 'Me Proteja', '4' => 'String']]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', ["placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7">
                <label class="form-label">Valor</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('valor', ["type" => "text", "placeholder" => "Valor", "class" => "form-control mb-3 mb-lg-0 money_exchange"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Descrição</label>
                <?php echo $this->Form->input('descricao', ["placeholder" => "Descrição", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Periodicidade</label>
                <?php echo $this->Form->input('frequency', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['30' => 'Mensal', '90' => 'Trimestral', '180' => 'Semestral', '365' => 'Anual']]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/products' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($form_action == "edit") { ?>
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-7 py-3">
            <?php echo $this->Form->create('ProductPrice', ["id" => "js-form-submit", "action" => $form_action_prices, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
                <input type="hidden" name="data[ProductPrice][product_id]" value="<?php echo $id ?>">

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Tabela de preço</label>
                    <?php echo $this->Form->input('price_table_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
                </div>

                <div class="mb-7">
                    <label class="form-label">Valor</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <?php echo $this->Form->input('value', ["type" => "text", "placeholder" => "Valor", "class" => "form-control mb-3 mb-lg-0 money_exchange"]);  ?>
                    </div>
                </div>

                <div class="mb-7">
                    <div class="col-sm-offset-2 col-sm-9">
                        <button type="submit" class="btn btn-success js-salvar">Adicionar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <?php echo $this->element("table"); ?>
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-4 rounded-start">Nome</th>
                            <th>Valor</th>
                            <th class="w-150px min-w-150px rounded-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($prices) { ?>
                            <?php for ($i=0; $i < count($prices); $i++) { ?>
                                <tr>
                                    <td class="fw-bold fs-7 ps-4"><?php echo $prices[$i]["PriceTable"]["descricao"]; ?></td>
                                    <td class="fw-bold fs-7 ps-4"><?php echo $prices[$i]["ProductPrice"]["value"]; ?></td>
                                    <td class="fw-bold fs-7 ps-4">
                                        <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/products/delete_price/'.$id.'/'.$prices[$i]["ProductPrice"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                            Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4" colspan="8">Nenhum registro encontrado</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>

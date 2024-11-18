<?php echo $this->element("aba_comunicado", array('id' => $id)); ?>

<div class="card mb-5 mb-xl-8">
    <?php echo $this->Form->create('ComunicadoCliente', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
        <div class="card-header d-block border-0 pt-6 pb-6">
            <div class="row">
                <div class="col-3 mb-10">
                    <label class="form-label fs-5 fw-bold mb-3">Cliente</label>
                    <?php echo $this->Form->input('customer_id', ["required" => true, "placeholder" => "", "class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => $customersIds]);  ?>
                </div>
                <div class="col-12 mb-7">
                    <div class="col-sm-offset-2 col-sm-9">
                        <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                        <a type="button" class="btn btn-primary" href="<?php echo $this->base.'/comunicados/add_all_clientes/'.$id; ?>">Adicionar todos os Clientes</a>
                        <a type="button" class="btn btn-primary" href="<?php echo $this->base.'/comunicados/enviar_comunicado/'.$id; ?>">Enviar Comunicado</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <div class="row">
                <div class="col-12">
                    <a href="#" id="excluir_sel" class="btn btn-danger btn-sm" style="float:right; margin-bottom: 10px">Excluir Selecionados</a>
                </div>
            </div>
            <br>

            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-80px min-w-80px rounded-start">
                            <input type="checkbox" class="check_all">
                        </th>
                        <th class="ps-4 rounded-start">Cliente</th>
                        <th class="ps-4 rounded-start">Data de Envio</th>
                        <th class="w-150px min-w-150px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="checkbox" name="del_linha" class="check_individual" id="">
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="hidden" class="item_id" value="<?php echo $data[$i]["ComunicadoCliente"]["id"]; ?>">
                                    <?php echo $data[$i]["Customer"]["nome_primario"]; ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["ComunicadoCliente"]["sent"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/comunicados/delete_cliente/'.$id.'/'.$data[$i]["ComunicadoCliente"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="10">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_excluir_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Excluir items selecionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="excluir_confirm" class="btn btn-success">Sim</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#excluir_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="del_linha"]:checked').length > 0) {
                $('#modal_excluir_sel').modal('show');
            } else {
                alert('Selecione ao menos um item a ser excluído');
            }
        });

        $('#excluir_confirm').on('click', function(e) {
            e.preventDefault();

            const comunicadoId = <?php echo $id; ?>;
            const checkboxes = $('input[name="del_linha"]:checked');
            const itemIds = [];

            checkboxes.each(function() {
                itemIds.push($(this).parent().parent().find('.item_id').val());
            });

            if (itemIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: base_url + '/comunicados/delete_all_clientes',
                    data: {
                        itemIds,
                        comunicadoId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });

        $(".check_all").on("change", function() {
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }
        });
    });
</script>

<?php $url_novo = $this->base."/negativacao/add/";  ?>
<?php
    echo $this->element("abas_customers", array('id' => $id));
?>
<div class="card mb-5 mb-xl-8">
    
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title col-6">
                <?php echo $this->Form->create('Customer', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", "class" => "w-100")); ?>
                    <div class="row">
                        <div class="col-3 me-3">
                            <?php echo $this->Form->input('status_id', array("div" => false, "label" => false, "class" => "form-select", "data-control" => "select2"));  ?>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-success js-submit-search">Alterar</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="card-toolbar">
                <form action="<?php echo $this->Html->url(array( "controller" => "customers", "action" => "log_status", $id)); ?>" role="form" id="busca" autocomplete="off">
                    <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="fas fa-filter"></i>
                            Filtro
                        </button>

                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                            <div class="px-7 py-5">
                                <div class="fs-4 text-dark fw-bolder">Opções</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            
                            <div class="px-7 py-5">
                                <div class="mb-10">
                                    <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="s" id="s">
                                        <option></option>
                                        <?php foreach ($statuses as $status_id => $status){ ?>
                                            <?php 
                                                $selected = '';
                                                if (isset($_GET['s']) && $_GET['s'] == $status_id) {
                                                    $selected = 'selected';
                                                }
                                            ?>
                                            <option value="<?php echo $status_id ?>" <?php echo $selected ?>><?php echo $status ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                
                                <div class="mb-10">
                                    <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
                                        <span class="input-group-text" style="padding: 5px;"> até </span>
                                        <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                                    <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Usuário</th>
                        <th class="w-200px min-w-200px rounded-end">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['UserCreated']['name'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]['MovimentacaoCredor']['created'])) ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#s").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });
    });
</script>
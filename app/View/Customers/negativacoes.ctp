<?php $url_novo = $this->base."/customers/add_negativacao/".$id;  ?>
<?php
    $url = $this->here;
    echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "customers", "action" => "negativacoes", $id)); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a type="button" href="javascript:" onclick="verConfirm('<?php echo $this->base."/customers/baixar_negativacoes/".$id; ?>', 'Deseja baixar as negativações inclusas por exclusão de contrato?');" class="btn btn-light-primary me-3">
                        <i class="fas fa-arrow-down"></i>
                        Baixar negativações inclusas
                    </a>

                    <div class="dropdown me-3">
                      <button class="btn btn-light-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Exportar
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf"></i> PDF</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel"></i> Excel</a></li>
                      </ul>
                    </div>

                    <a type="button" class="btn btn-primary me-3" href="<?php echo $url_novo;?>">Novo</a>
                    
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($status); $a++){
                                            $selected = "";
                                            if (isset($_GET["t"])) {
                                                if($status[$a]['Status']['id'] == $_GET["t"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$status[$a]['Status']['id'].'" '.$selected.'>'.$status[$a]['Status']['name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-10">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input type="checkbox" name="f" id="criado_cliente" class="form-check-input" value="1" <?php echo (isset($_GET['f']) && $_GET['f'] == 1 ? 'checked' : '' ) ?>>
                                    <label class="form-check-label" for="criado_cliente">
                                        Criado pelo Cliente
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                                <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("Customers/negativacoes_table"); ?>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#criado_cliente").val(null);
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>
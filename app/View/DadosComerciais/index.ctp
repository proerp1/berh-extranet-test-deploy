<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "dados_comerciais", "action" => "index")); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a href="<?php echo $this->here.'?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Vendedor:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="v" id="v">
                                    <option></option>
                                    <?php foreach ($sellers as $seller_id => $seller){ ?>
                                        <?php 
                                            $selected = '';
                                            if (isset($_GET['v']) && $_GET['v'] == $seller_id) {
                                                $selected = 'selected';
                                            }
                                        ?>
                                        <option value="<?php echo $seller_id ?>" <?php echo $selected ?>><?php echo $seller ?></option>
                                    <?php } ?>
                                </select>
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
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Nome fantasia</th>
                        <th>Razão social</th>
                        <th>Responsável</th>
                        <th class="min-w-150px">Telefone</th>
                        <th>Celular</th>
                        <th>Endereço</th>
                        <th>Cidade/Estado</th>
                        <th>Valor total</th>
                        <th class="min-w-150px">Plano</th>
                        <th class="w-100px min-w-100px rounded-end">Valor do plano</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data){ ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Status']['name'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_secundario'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['nome_primario'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['responsavel'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['telefone1'].'<br>'.$data[$i]['Customer']['telefone2'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['celular1'].'<br>'.$data[$i]['Customer']['celular2'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo trim($data[$i]['Customer']['endereco']).', '.$data[$i]['Customer']['numero'].' - '.$data[$i]['Customer']['bairro'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['cidade'].' - '.$data[$i]['Customer']['estado'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i][0]['valor_em_aberto'], 2, ',','.') ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Plan']['description'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i]['Plan']['value'], 2, ',','.') ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                                <td class="fw-bold fs-7 ps-4" colspan="13">Nenhum registro encontrado.</td>
                            </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $data ? $this->element("pagination") : ''; ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#v").val(null);

            $("#busca").submit();
        });
    });
</script>
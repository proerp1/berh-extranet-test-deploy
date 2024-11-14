<?php $url_novo = $this->base."/customer_users/add_itinerary/".$id.'/'.$user_id;  ?>
<?php
    $url = $this->base . '/customers_users/itineraries';
    echo $this->element("abas_customers", array('id' => $id));
    echo $this->element('abas_customer_users', ['id' => $id, 'url' => $url]);
?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "customer_users", "action" => "itineraries", $id, $user_id)); ?>" role="form" id="busca" autocomplete="off">
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
                    <!-- <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button> -->

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
                        <th>status</th>
                        <th>Benefício</th>
                        <th>Dias Úteis</th>
                        <th>Valor Unitário</th>
                        <th>Quantidade</th>
                        <th>Valor por dia</th>
                        <th>N° Cartão</th>
                        <th>Total</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { $total = 0; ?>
                        <?php foreach ($data as $key => $value) { 
                            $total += $value['CustomerUserItinerary']['total_not_formated'];
                            ?>
                            <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <span class='badge <?php echo isset($value['Status']['label']) ? $value['Status']['label'] : 'badge-secondary'; ?>'>
                                    <?php echo isset($value['Status']['name']) ? $value['Status']['name'] : ''; ?>
                                </span>
                            </td>
                                <td><?php echo $value['Benefit']['code'].' - '.$value['Benefit']['name']; ?></td>
                                <td><?php echo $value['CustomerUserItinerary']['working_days']; ?></td>
                                <td>R$<?php echo $value['CustomerUserItinerary']['unit_price']; ?></td>
                                <td><?php echo $value['CustomerUserItinerary']['quantity']; ?></td>
                                <td>R$<?php echo $value['CustomerUserItinerary']['price_per_day']; ?></td>
                                <td><?php echo $value['CustomerUserItinerary']['card_number']; ?></td>
                                <td>R$<?php echo $value['CustomerUserItinerary']['total']; ?></td>
                                <td>
                                    <a href="<?php echo $this->base . '/customer_users/edit_itinerary/' . $id . '/' . $user_id . '/' . $value["CustomerUserItinerary"]["id"] . '/?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                    
                                    <?php if (isset($user['Group']['id']) && $user['Group']['id'] == 1): ?>

                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base . '/customer_users/delete_itinerary/' . $id . '/' . $user_id . '/' . $value["CustomerUserItinerary"]["id"] . '/?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                <?php endif; ?>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if($total > 0) { ?>
                            <tr>
                                <td colspan="5" class="fs-7 ps-4" style="text-align:right">Total</td>
                                <td class="fw-bold fs-7 ps-4">R$<?php echo number_format($total, 2, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
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
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>
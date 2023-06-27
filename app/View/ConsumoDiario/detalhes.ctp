<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "consumo_diario", "action" => "detalhes", $id)); ?>/" role="form" id="busca" autocomplete="off">
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
                    
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Data</th>
                        <th>Cliente</th>
                        <th class="w-200px min-w-200px rounded-end">Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($data[$i]["ConsumoDiarioItem"]['data'])) ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php
                                        echo "<a target='_blank' href='".$this->Html->url(['controller' => 'customers', 'action' => 'edit', $data[$i]["Customer"]['id']])."'>".$data[$i]["Customer"]['codigo_associado'].' - '.$data[$i]["Customer"]['nome_primario']."</a>";
                                    ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['qtde'] ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>
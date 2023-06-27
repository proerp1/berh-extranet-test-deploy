<div class="page page-profile">
    <?php
        echo $this->element("abas_products", array('tipo' => $product['Product']['tipo']));
    ?>

    <section class="panel panel-default">
        <div class="panel-body">            
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Nome</th>
                        <th>Valor do Produto</th>
                        <th>Valor Mínimo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dados) { ?>
                        <?php foreach ($dados as $value) { ?>
                            <tr>
                                <td>
                                    <span class="label <?php echo $value["Status"]["label"] ?>"><?php echo $value["Status"]["name"] ?></span>
                                </td>
                                <td><?php echo $value["Feature"]["name"] ?></td>
                                <td><?php echo "R$ ".$value["Feature"]["valor"] ?></td>
                                <td><?php echo "R$ ".$value["Feature"]["valor_minimo"] ?></td>
                                <td>
                                    <a href="<?php echo $this->base.'/products/edit_feature_string/'.$id.'/'.$value["Feature"]["id"]; ?>" class="btn btn-info btn-xs">
                                        Editar
                                    </a>
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/products/delete_feature/'.$id.'/'.$value["Feature"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-xs">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div> <!-- /painel-body -->
    </section> <!-- /panel-default -->
</div> <!-- /page-profile -->
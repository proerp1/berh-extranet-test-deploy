<div class="row">
    <div class="col-md-6 col-sm-12 col-lg-4">
        <div class="panel">
            <div class="p-30">
                <div class="row">
                    <div class="col-xs-4"><img src="/img/profiles/avatar.png" alt="varun" class="img-circle img-responsive"></div>
                    <div class="col-xs-8">
                        <h2 class="m-b-0"><?php echo($buscaCliente[0]['cliente']['clienteNomeFantasia']) ?></h2>
                        <h5 class="m-t-0"><?php echo($buscaCliente[0]['cliente']['clienteEmail']) ?></h5>
                        <span class="label label-success m-r-10">Cliente</span>
                    </div>
                </div>

                <div class="user-btm-box">
                    <!-- .row -->
                    <?php 
                    if($buscaPedido[0]['pedido']['pedidoID']) {
                    ?>
                    <div class="row text-center m-t-10">
                        <div class="col-md-6 b-r"><strong>Orçamento</strong>
                            <p><?php echo($buscaPedido[0]['pedido']['pedidoID']); ?></p>
                        </div>
                        <div class="col-md-6"><strong>Pedido</strong>
                            <p><?php echo($buscaPedido[0]['pedido']['pedidoID']); ?></p>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    <!-- /.row -->
                    <hr>
                    <!-- .row -->
                    <div class="row text-center m-t-10">
                        <div class="col-md-6 b-r"><strong>Telefone</strong>
                            <p><?php echo($buscaCliente[0]['cliente']['clienteTelefone']) ?></p>
                        </div>
                        <div class="col-md-6"><strong>Celular</strong>
                            <p><?php echo($buscaCliente[0]['cliente']['clienteTelefone_2']) ?></p>
                        </div>
                    </div>
                    <!-- /.row -->
                    <hr>
                    <!-- .row -->
                    <div class="row text-center m-t-10">
                        <div class="col-md-6 b-r"><strong>Endereço Comercial</strong>
                            <p><?php echo($buscaEndereco[0]['customer_addresses']['endereco'].", ".$buscaEndereco[0]['customer_addresses']['numero']); ?>
                                <?php
                                if($buscaEndereco[0]['customer_addresses']['complemento']) {
                                    echo("<br>".$buscaEndereco[0]['customer_addresses']['complemento']);
                                }
                                ?>
                                <br> <?php echo($buscaEndereco[0]['customer_addresses']['bairro']); ?>
                                <br> <?php echo($buscaEndereco[0]['customer_addresses']['cidade']." - ".$buscaEndereco[0]['customer_addresses']['estado']); ?>
                                <br> CEP <?php echo($buscaEndereco[0]['customer_addresses']['cep']); ?></p>
                        </div>
                        <div class="col-md-6"><strong>Limite de Crédito</strong>
                                <p>Limite: R$ <?php echo(empty($buscaLimite) ? "0,00" : $buscaLimite[0][0]['limiteValor']); ?>
                                <br>Faturado: R$ <?php echo(empty($buscaLimite) ? "0,00" : $buscaLimite[0][0]['limiteFaturado']); ?>
                                <br>Saldo: R$ <?php echo(empty($buscaLimite) ? "0,00" : $buscaLimite[0][0]['limiteSaldo']); ?>
                                <br><small>Atualizado: <?php echo(empty($buscaLimite) ? "--/--/----" : $buscaLimite[0][0]['created']); ?></small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-12 col-lg-8">
        <div class="panel panel-info">
            <div class="panel-heading"> Dados orçamento</div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                <form id="js-form-submit" action="<?php echo($this->action == 'edit' ? $this->here : $this->base.'/dashboard/add/') ?>" method="post" autocomplete="off">   
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Cliente</label>
                                        <select class="form-control" name="data[Pedido][clienteID]">
                                            <option value="">Selecione...</option>
                                            <?php

                                                foreach ($customers as $clienteID => $clienteNome) {
                                                    $selected = "";
                                                    if(($clienteID == $buscaPedido[0]['pedido']['clienteID']) || ($clienteID == $buscaCliente[0]['cliente']['clienteID'])) {
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$clienteID.'" '.$selected.'>'.$clienteNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Vendedor</label>
                                        <select class="form-control" name="data[Pedido][vendedorIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php 
                                                foreach ($vendedores as $vendedorIDDatasul => $vendedorNome) {
                                                    $selected = "";   
                                                    if(($vendedorIDDatasul == $buscaPedido[0]['pedido']['vendedorIDDatasul']) || ($vendedorIDDatasul == $buscaCliente[0]['cliente']['vendedorIDDatasul'])) {
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$vendedorIDDatasul.'" '.$selected.'>'.$vendedorNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Estabelecimento</label>
                                        <select class="form-control" name="data[Pedido][estabelecimentoIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php 

                                                foreach ($estabelecimentos as $estabelecimentoIDDatasul => $estabelecimentoNome) {

                                                    $selected = "";

                                                    if($estabelecimentoIDDatasul == $buscaPedido[0]['pedido']['estabelecimentoIDDatasul']){
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$estabelecimentoIDDatasul.'" '.$selected.'>'.$estabelecimentoNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Nat Operação</label>
                                        <select class="form-control" name="data[Pedido][naturezaOperacaoIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php 

                                                $selected = "";                                                

                                                foreach ($naturezas as $naturezaOperacaoIDDatasul => $naturezaOperacaoNome) {
                                                
                                                    if($naturezaOperacaoIDDatasul == $buscaPedido[0]['pedido']['estabelecimentoIDDatasul']){
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$naturezaOperacaoIDDatasul.'" '.$selected.'>'.$naturezaOperacaoNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Condição de pagamento</label>
                                        <select class="form-control" name="data[Pedido][condicaoPagamentoIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php 
                                                foreach ($condicoesPagamento as $condicaoPagamentoIDDatasul => $condicaoPagamentoNome) {
                                                    $selected = "";
                                                    if(($condicaoPagamentoIDDatasul == $buscaPedido[0]['pedido']['condicaoPagamentoIDDatasul']) || ($condicaoPagamentoIDDatasul == $buscaCliente[0]['cliente']['condicaoPagamentoIDDatasul'])){
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$condicaoPagamentoIDDatasul.'" '.$selected.'>'.$condicaoPagamentoNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Cli Rem Tri</label>
                                        <select class="form-control" name="data[Pedido][cliRemTriIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php 
                                                foreach ($clis as $cliRemTriIDDatasul => $cliRemTriNome) {
                                                    $selected = "";
                                                    if($cliRemTriIDDatasul == $buscaPedido[0]['pedido']['cliRemTriIDDatasul']){
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$cliRemTriIDDatasul.'" '.$selected.'>'.$cliRemTriNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Portador/Mod</label>
                                        <select class="form-control" name="data[Pedido][portadorIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php  
                                                foreach ($portadores as $portadorIDDatasul => $portadorNome) {
                                                    $selected = "";
                                                    if($portadorIDDatasul == $buscaPedido[0]['pedido']['portadorIDDatasul']){
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$portadorIDDatasul.'" '.$selected.'>'.$portadorNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Faturamento Parcial</label>
                                        <div class="radio radio-danger">
                                            <?php
                                            $checked_s = "";
                                            $checked_n = "";

                                            if($buscaPedido[0]['pedido']['faturamentoParcial'] == '1') {
                                                $checked_s = "checked";
                                            } else {
                                                $checked_s = "";
                                            }

                                            if($buscaPedido[0]['pedido']['faturamentoParcial'] == '0') {
                                                $checked_n = "checked";
                                            } else {
                                                $checked_n = "";
                                            }

                                            ?>
                                            
                                            <input type="radio" <?php echo($checked_s); ?> name="data[Pedido][faturamentoParcial]" value="1">
                                            <label for="checkbox0"> Sim </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" <?php echo($checked_n); ?> name="data[Pedido][faturamentoParcial]" value="0">
                                            <label for="checkbox0"> Não </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Transportador</label>
                                        <select class="form-control" name="data[Pedido][transportadorIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php 

                                                $selected = "";

                                                foreach ($transportadores as $transportadorIDDatasul => $transportadorNome) {
                                                    
                                                    if($transportadorIDDatasul == $buscaPedido[0]['pedido']['transportadorIDDatasul']){
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$transportadorIDDatasul.'" '.$selected.'>'.$transportadorNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Transportador Redesp</label>
                                        <select class="form-control" name="data[Pedido][transportadorRedespachoIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php 

                                                $selected = "";

                                                foreach ($transportadores as $transportadorRedespachoIDDatasul => $transportadorNome) {
                                                
                                                    if($transportadorRedespachoIDDatasul == $buscaPedido[0]['pedido']['transportadorRedespachoIDDatasul']){
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$transportadorRedespachoIDDatasul.'" '.$selected.'>'.$transportadorNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Cobrança Desp</label>
                                        <select class="form-control" name="data[Pedido][cobrancaDespIDDatasul]">
                                            <option value="">Selecione...</option>
                                            <?php 

                                                $selected = "";
                                                
                                                foreach ($customersPayment as $cobrancaDespIDDatasul => $empresaNome) {
                                                    
                                                    if($cobrancaDespIDDatasul == $buscaPedido[0]['pedido']['cobrancaDespIDDatasul']){
                                                        $selected = "selected";
                                                    }
                                                    
                                                    echo '<option value="'.$cobrancaDespIDDatasul.'" '.$selected.'>'.$empresaNome.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                            </div>
                            <!--/row-->
                            <div class="form-group m-b-0">
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-success btn-block m-t-5 js-salvar" data-loading-text="Aguarde...">Salvar</button>
                                </div>
                            </div>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if($buscaPedido[0]['pedido']['pedidoID']) {
?>
<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-7">
        <div class="manage-users">
            <div class="sttabs tabs-style-iconbox">
                <nav>
                    <ul>
                        <li class="tab-current"><a href="#section-iconbox-1" class="sticon ti-shopping-cart-full"><span>Itens</span></a></li>
                        <li class=""><a href="#section-iconbox-2" class="sticon ti-medall"><span>Histórico negociação</span></a></li>
                        <li class=""><a href="#section-iconbox-3" class="sticon ti-receipt"><span>Observações</span></a></li>
                    </ul>
                </nav>
                <div class="content-wrap">
                    <section id="section-iconbox-1" class="content-current">
                        <div class="p-20 row">
                            <div class="col-sm-6">
                                <h3 class="m-t-0">Produtos</h3>
                            </div>
                            <div class="col-sm-6">
                                <ul class="side-icon-text pull-right">
                                    <li><a href="#"><span class="circle circle-sm bg-success di"><i class="ti-plus"></i></span><span>Adicionar produto</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="table-responsive manage-table">
                            <table class="table product-overview">
                            <thead>
                                <tr>
                                    
                                    <th colspan="6"></th>
                                    <th style="text-align:center" colspan="2">Estoque</th>
                                    <th style="text-align:center"></th>
                                </tr>
                                <tr>
                                    
                                    <th>Produto</th>
                                    <th>Tabela</th>
                                    <th>Preço</th>
                                    <th>Qtde</th>
                                    <th>Desconto</th>
                                    <th style="text-align:center">Total</th>
                                    <th style="text-align:center">Alocado</th>
                                    <th style="text-align:center">Disponível</th>
                                    <th style="text-align:center">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Produto">
                                    </td>
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Tabela de preço">
                                    </td>
                                    <td>R$ 450,00</td>
                                    <td width="100">
                                        <input type="text" class="form-control" placeholder="1">
                                    </td>
                                    <td width="70">
                                        <input type="text" class="form-control" placeholder="2%">
                                    </td>
                                    <td width="150" align="center" class="font-500">R$ 900,00</td>
                                    <td style="text-align:center">
                                        1.340
                                    </td>
                                    <td style="text-align:center">
                                        930
                                    </td>
                                    <td align="center">
                                        <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Produto">
                                    </td>
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Tabela de preço">
                                    </td>
                                    <td>R$ 450,00</td>
                                    <td width="100">
                                        <input type="text" class="form-control" placeholder="1">
                                    </td>
                                    <td width="70">
                                        <input type="text" class="form-control" placeholder="2%">
                                    </td>
                                    <td width="150" align="center" class="font-500">R$ 900,00</td>
                                    <td style="text-align:center">
                                        1.340
                                    </td>
                                    <td style="text-align:center">
                                        930
                                    </td>
                                    <td align="center">
                                        <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Produto">
                                    </td>
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Tabela de preço">
                                    </td>
                                    <td>R$ 450,00</td>
                                    <td width="100">
                                        <input type="text" class="form-control" placeholder="1">
                                    </td>
                                    <td width="70">
                                        <input type="text" class="form-control" placeholder="2%">
                                    </td>
                                    <td width="150" align="center" class="font-500">R$ 900,00</td>
                                    <td style="text-align:center">
                                        1.340
                                    </td>
                                    <td style="text-align:center">
                                        930
                                    </td>
                                    <td align="center">
                                        <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Produto">
                                    </td>
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Tabela de preço">
                                    </td>
                                    <td>R$ 450,00</td>
                                    <td width="100">
                                        <input type="text" class="form-control" placeholder="1">
                                    </td>
                                    <td width="70">
                                        <input type="text" class="form-control" placeholder="2%">
                                    </td>
                                    <td width="150" align="center" class="font-500">R$ 900,00</td>
                                    <td style="text-align:center">
                                        1.340
                                    </td>
                                    <td style="text-align:center">
                                        930
                                    </td>
                                    <td align="center">
                                        <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Produto">
                                    </td>
                                    <td width="200">
                                        <input type="text" class="form-control" placeholder="Tabela de preço">
                                    </td>
                                    <td>R$ 450,00</td>
                                    <td width="100">
                                        <input type="text" class="form-control" placeholder="1">
                                    </td>
                                    <td width="70">
                                        <input type="text" class="form-control" placeholder="2%">
                                    </td>
                                    <td width="150" align="center" class="font-500">R$ 900,00</td>
                                    <td style="text-align:center">
                                        1.340
                                    </td>
                                    <td style="text-align:center">
                                        930
                                    </td>
                                    <td align="center">
                                        <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                        
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                        </div>
                    </section>
                    <section id="section-iconbox-2" class="">
                        <div class="p-20 row">
                            <div class="col-sm-6">
                                <h3 class="m-t-0">Histórico</h3>
                            </div>
                            <div class="col-sm-6">
                                <ul class="side-icon-text pull-right">
                                    <li><a href="#"><span class="circle circle-sm bg-success di"><i class="ti-plus"></i></span><span>Adicionar</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="table-responsive manage-table">
                            <table class="table product-overview">
                                <thead>
                                    <tr>
                                        <th>Mensagem</th>
                                        <th>Data</th>
                                        <th>Usuário</th>
                                        <th style="text-align:center">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <h5>Lorem Ipsum available, but the majority have suffered. Lorem Ipsum available, but the majority have suffered</h5>
                                        </td>
                                        <td>28/08/2021</td>
                                        <td>Rodolfo Teles</td>
                                        <td align="center">
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Ver projeto"><i class="ti-gallery text-dark"></i></a>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5>Lorem Ipsum available, but the majority have suffered. Lorem Ipsum available, but the majority have suffered</h5>
                                        </td>
                                        <td>28/08/2021</td>
                                        <td>Rodolfo Teles</td>
                                        <td align="center">
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Ver projeto"><i class="ti-gallery text-dark"></i></a>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5>Lorem Ipsum available, but the majority have suffered. Lorem Ipsum available, but the majority have suffered</h5>
                                        </td>
                                        <td>28/08/2021</td>
                                        <td>Rodolfo Teles</td>
                                        <td align="center">
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Ver projeto"><i class="ti-gallery text-dark"></i></a>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5>Lorem Ipsum available, but the majority have suffered. Lorem Ipsum available, but the majority have suffered</h5>
                                        </td>
                                        <td>28/08/2021</td>
                                        <td>Rodolfo Teles</td>
                                        <td align="center">
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Ver projeto"><i class="ti-gallery text-dark"></i></a>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <section id="section-iconbox-3" class="">
                        <div class="p-20 row">
                            <div class="col-sm-6">
                                <h3 class="m-t-0">Histórico</h3>
                            </div>
                            <div class="col-sm-6">
                                <ul class="side-icon-text pull-right">
                                    <li><a href="#"><span class="circle circle-sm bg-success di"><i class="ti-plus"></i></span><span>Adicionar</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="table-responsive manage-table">
                            <table class="table product-overview">
                                <thead>
                                    <tr>
                                        <th>Mensagem</th>
                                        <th>Data</th>
                                        <th>Usuário</th>
                                        <th style="text-align:center">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <h5>Lorem Ipsum available, but the majority have suffered. Lorem Ipsum available, but the majority have suffered</h5>
                                        </td>
                                        <td>28/08/2021</td>
                                        <td>Rodolfo Teles</td>
                                        <td align="center">
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Ver projeto"><i class="ti-gallery text-dark"></i></a>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5>Lorem Ipsum available, but the majority have suffered. Lorem Ipsum available, but the majority have suffered</h5>
                                        </td>
                                        <td>28/08/2021</td>
                                        <td>Rodolfo Teles</td>
                                        <td align="center">
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Ver projeto"><i class="ti-gallery text-dark"></i></a>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5>Lorem Ipsum available, but the majority have suffered. Lorem Ipsum available, but the majority have suffered</h5>
                                        </td>
                                        <td>28/08/2021</td>
                                        <td>Rodolfo Teles</td>
                                        <td align="center">
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Ver projeto"><i class="ti-gallery text-dark"></i></a>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5>Lorem Ipsum available, but the majority have suffered. Lorem Ipsum available, but the majority have suffered</h5>
                                        </td>
                                        <td>28/08/2021</td>
                                        <td>Rodolfo Teles</td>
                                        <td align="center">
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash text-dark"></i></a>
                                            <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Ver projeto"><i class="ti-gallery text-dark"></i></a>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                <!-- /content -->
            </div>
            <!-- /tabs -->
        </div>
    </div>
    <div class="col-md-3 col-lg-3 col-sm-5">
        <div class="white-box">
            <h3 class="box-title">Resumo</h3>
            <hr> <small>Total produtos</small>
            <h2>R$ 1.234,56</h2>
            <hr>
            
            <!--/span-->
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Data orçamento</label>
                    <input type="date" name="" class="form-control" placeholder="dd/mm/aaaa"> </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Previsão de Fechamento</label>
                    <input type="text" id="lastName" class="form-control" placeholder="dd/mm/aaaa"> </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Vencimento</label>
                    <input type="text" id="lastName" class="form-control" placeholder="dd/mm/aaaa"> </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Valor parcela</label>
                    <input type="text" id="lastName" class="form-control" placeholder="R$ 0,00"> </div>
            </div>

            <div class="button-box">
                <div class="btn-group m-r-10 open">
                    <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Ações <span class="caret"></span></button>
                    <ul role="menu" class="dropdown-menu animated flipInX">
                        <li><a href="javascript:void(0)">Aprovado</a></li>
                        <li><a href="javascript:void(0)">Perdido</a></li>
                        <li><a href="javascript:void(0)">Cancelado</a></li>
                    </ul>
                    <button class="btn btn-success">Salvar</button>                    
                    <button class="btn btn-default btn-outline">Voltar</button>
                </div>                
            </div>            
            
        </div>
    </div>   
</div>
<?php
}
?> 

<?php
$status = 0;
if($status == 1) {
?>
<div class="row">
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="white-box">
            <div class="checkbox checkbox-danger">
                <input id="checkbox0" type="checkbox" checked="">
                <label for="checkbox0"> Cliente </label>
            </div>
            <h2>Aguardando</h2> 
            <span class="pull-right">Autorizado: Rodolfo Teles</span> 
            <span class="font-500">29/08/2021</span> 
        </div>
    </div>
    
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="white-box">
            <div class="checkbox checkbox-danger">
                <input id="checkbox0" type="checkbox" checked="">
                <label for="checkbox0"> Vendedor </label>
            </div>
            <h2>Aprovado</h2> 
            <span class="pull-right">Autorizado: Rodolfo Teles</span> 
            <span class="font-500">29/08/2021</span> 
        </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="white-box">
            <div class="checkbox checkbox-danger">
                <input id="checkbox0" type="checkbox" checked="">
                <label for="checkbox0"> Coordenação </label>
            </div>
            <h2>Aprovado</h2> 
            <span class="pull-right">Autorizado: Rodolfo Teles</span> 
            <span class="font-500">29/08/2021</span> 
        </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="white-box">
            <div class="checkbox checkbox-danger">
                <input id="checkbox0" type="checkbox" checked="">
                <label for="checkbox0"> Gerencia Comercial </label>
            </div>
            <h2>Aprovado</h2> 
            <span class="pull-right">Autorizado: Rodolfo Teles</span> 
            <span class="font-500">29/08/2021</span> 
        </div>
    </div>
    
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="white-box">
            <div class="checkbox checkbox-danger">
                <input id="checkbox0" type="checkbox" checked="">
                <label for="checkbox0"> Financeiro </label>
            </div>
            <h2>Pendente</h2> 
            <span class="pull-right">Autorizado: Rodolfo Teles</span> 
            <span class="font-500">29/08/2021</span> 
        </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="white-box">
            <div class="checkbox checkbox-danger">
                <input id="checkbox0" type="checkbox" checked="">
                <label for="checkbox0"> Expedição </label>
            </div>
            <h2>Pendente</h2> 
            <span class="pull-right">Autorizado: Rodolfo Teles</span> 
            <span class="font-500">29/08/2021</span> 
        </div>
    </div>
    
    
</div>
<?php
}
?>

<?php 
    echo $this->Html->script('cbpFWTabs.js');echo "\n\t";
?>
<script type="text/javascript">
(function() {
    [].slice.call(document.querySelectorAll('.sttabs')).forEach(function(el) {
        new CBPFWTabs(el);
    });
})();
</script>

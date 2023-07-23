<?php echo $this->Html->css('css_temp/style'); ?>
<!-- .row -->
 <style type="text/css">
 	.er-count{
 		font-size: 20px !important;
 	}
 </style>
 
 <div class="row">
    <div class="white-box">
        <div class="table-responsive">
            <table class="table product-overview" id="myTable">
                <thead>
                    <tr>
                    	<th>Número</th>
                        <th>Cliente</th>   
                        <th>Vendedor</th>
                        <th>Autor</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#85457898</td>
                        <td>Cliente Teste</td>
                        <td>Vendas 4</td>
                        <td>Vendas 4</td>
                        <td>28/08/2018</td>
                        <td>R$ 5.689,23</td>
                        <td> <span class="label label-success font-weight-100">Pendente</span> </td>
                        <td><a href="<?php echo $this->base.'/dashboard/resumo' ?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Editar"><i class="ti-marker-alt"></i></a> <a href="javascript:void(0)" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Imprimir"><i class="fa fa-print"></i></a></td>
                    </tr>                   
                </tbody>
            </table>
        </div>
    </div>
</div>
 <!-- /.row -->

 <!-- ============================================================== -->
 <!-- city-weather -->
 <!-- ============================================================== -->


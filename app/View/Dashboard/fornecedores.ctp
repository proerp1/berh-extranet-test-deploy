<?php echo $this->Html->css('css_temp/style'); ?>
<!-- Calendar CSS -->
    <link href="../plugins/bower_components/calendar/dist/fullcalendar.css" rel="stylesheet" />
 <!-- .row -->
 <style type="text/css">
 	.er-count{
 		font-size: 20px !important;
 	}
 </style>
 
 <div class="row">
 	<div class="col-md-6 col-sm-12 col-lg-4">
        <div class="col-md-6 col-sm-12 col-lg-12">
	        <div class="panel">
	            <div class="p-30">
	                <div class="row">
	                    <div class="col-xs-4"><img src="<?php echo $this->base.'/img/profiles/avatar.png'; ?>" alt="varun" class="img-circle img-responsive"></div>
	                    <div class="col-xs-8">
	                        <h2 class="m-b-0">FORNECEDOR TESTE</h2>
	                        <h4 class="m-t-0">email@email.com.br</h4>
	                        <span class="label label-success m-r-10">Fornecedor</span>
	                    </div>
	                </div>

	                <div class="user-btm-box">
                        <!-- .row -->
                        <div class="row text-center m-t-10">
                            <div class="col-md-6 b-r"><strong>Responsavel</strong>
                                <p>Luis Junior</p>
                            </div>
                            <div class="col-md-6"><strong>Departamento</strong>
                                <p>Diretor</p>
                            </div>
                        </div>
                        <!-- /.row -->
                        <hr>
                        <!-- .row -->
                        <div class="row text-center m-t-10">
	                        <div class="col-md-6 b-r"><strong>Telefone</strong>
	                            <p>(11) 2222-2222</p>
	                        </div>
	                        <div class="col-md-6"><strong>Celular</strong>
	                            <p>(11) 92222-2222</p>
	                        </div>
	                    </div>
	                    <!-- /.row -->
	                    <hr>
	                    <!-- .row -->
	                    <div class="row text-center m-t-10">
	                        <div class="col-md-6 b-r"><strong>Endereço Entrega</strong>
	                            <p>Rua teste 3250
	                                <br> São Paulo - SP
	                                <br> CEP 00000-000</p>
	                        </div>
	                        <div class="col-md-6"><strong>Pagamentos</strong>
	                            <p>Em aberto: R$ 60.000,00
	                                <br>Em atraso: R$ 40.000,00</p>
	                        </div>
	                    </div>
                        
                    </div>
	            </div>
	        </div>
	    </div>
    </div>    
   
 	<div class="col-md-6 col-lg-8 col-sm-12">

        <div class="white-box bg-theme m-b-0 p-b-0 mailbox-widget">
            <h2 class="text-white p-b-20">Acompanhamento compras</h2>
            <ul class="nav customtab nav-tabs" role="tablist">
            	<li role="presentation" class=""><a href="#pipeline" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-export"></i></span> <span class="hidden-xs">PROGRAMADO</span></a></li>
                <li role="presentation" class="active"><a href="#previsto" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-email"></i></span><span class="hidden-xs"> PAGO</span></a></li>
            </ul>
        </div>
        <div class="white-box p-0">
            <div class="tab-content m-t-0">
                <div role="tabpanel" class="tab-pane fade active in" id="previsto">
                    <div class="inbox-center table-responsive">
                        <table class="table table-hover">
                            <tbody>
                            	<tr class="unread">
									<td>DATA</td>
									<td>VENCIMENTO</td>
									<td>PAGAMENTO</td>
									<td>ORÇAMENTO</td>
									<td>CLIENTE </td>
									<td>VALOR</td>
								</tr>
                                <tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								
								
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div role="tabpanel" class="tab-pane fade in" id="pipeline">
                    <div class="inbox-center table-responsive">
                        <table class="table table-hover">
                            <tbody>
                            	<tr class="unread">
									<td>DATA</td>
									<td>VENCIMENTO</td>
									<td>PAGAMENTO</td>
									<td>ORÇAMENTO</td>
									<td>CLIENTE </td>
									<td>VALOR</td>
								</tr>
                                <tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
								<tr class="unread">
									<td>19/04/2021</td>
									<td>11/09/2021</td>
									<td>11/09/2021</td>
									<td>109532</td>
									<td>TESTE RODOLFO</td>
									<td>520,00</td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>                
            </div>
        </div>        
    </div>
 </div>

 <div class="row">
 	
    <div class="col-md-12 col-lg-6 col-sm-12">
        <div class="white-box">
            
            <h3 class="box-title">Financeiro</h3>
            <div class="row sales-report">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <h2>Pagamentos</h2>
                    <p>Aguardando pagamento/financeiro</p>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 ">
                    <h1 class="text-right text-info m-t-20">R$3.690,21</h1>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
	                    <thead>
	                        <tr>
	                           	<th>STATUS</th>
	                           	<th>PEDIDO</th>
	                            <th>VENCIMENTO</th>
	                            <th>VALOR</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <tr>
	                            <td class="txt-oflo"><span class="label label-danger label-rouded">ATRASADO</span></td>
	                            <td class="txt-oflo">20210001</td>
	                            <td class="txt-oflo">27/08/2021</td>
	                            <td><span class="text-warning">R$2.4365,32</span></td>
	                        </tr>
	                        <tr>
	                            <td class="txt-oflo"><span class="label label-danger label-rouded">ATRASADO</span></td>
	                            <td class="txt-oflo">20210001</td>
	                            <td class="txt-oflo">27/08/2021</td>
	                            <td><span class="text-warning">R$2.4365,32</span></td>
	                        </tr>
	                        <tr>
	                            <td class="txt-oflo"><span class="label label-warning label-rouded">PENDENTE</span></td>
	                            <td class="txt-oflo">20210001</td>
	                            <td class="txt-oflo">27/08/2021</td>
	                            <td><span class="text-warning">R$2.4365,32</span></td>
	                        </tr>
	                        <tr>
	                            <td class="txt-oflo"><span class="label label-warning label-rouded">PENDENTE</span></td>
	                            <td class="txt-oflo">20210001</td>
	                            <td class="txt-oflo">27/08/2021</td>
	                            <td><span class="text-warning">R$2.4365,32</span></td>
	                        </tr>
	                        <tr>
	                            <td class="txt-oflo"><span class="label label-warning label-rouded">PENDENTE</span></td>
	                            <td class="txt-oflo">20210001</td>
	                            <td class="txt-oflo">27/08/2021</td>
	                            <td><span class="text-warning">R$2.4365,32</span></td>
	                        </tr>
	                        <tr>
	                            <td class="txt-oflo"><span class="label label-warning label-rouded">PENDENTE</span></td>
	                            <td class="txt-oflo">20210001</td>
	                            <td class="txt-oflo">27/08/2021</td>
	                            <td><span class="text-warning">R$2.4365,32</span></td>
	                        </tr>
	                        <tr>
	                            <td class="txt-oflo"><span class="label label-warning label-rouded">PENDENTE</span></td>
	                            <td class="txt-oflo">20210001</td>
	                            <td class="txt-oflo">27/08/2021</td>
	                            <td><span class="text-warning">R$2.4365,32</span></td>
	                        </tr>
	                        <tr>
	                            <td class="txt-oflo"><span class="label label-warning label-rouded">PENDENTE</span></td>
	                            <td class="txt-oflo">20210001</td>
	                            <td class="txt-oflo">27/08/2021</td>
	                            <td><span class="text-warning">R$2.4365,32</span></td>
	                        </tr>
	                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="white-box">
            <div id="calendar"></div>
        </div>
    </div>
 </div>
 <!-- /.row -->

 <!-- ============================================================== -->
 <!-- city-weather -->
 <!-- ============================================================== -->

<!-- Calendar JavaScript -->
    <script src="../plugins/bower_components/calendar/jquery-ui.min.js"></script>
    <script src="../plugins/bower_components/moment/moment.js"></script>
    <script src='../plugins/bower_components/calendar/dist/fullcalendar.min.js'></script>
    <script src="../plugins/bower_components/calendar/dist/jquery.fullcalendar.js"></script>
    <script src="../plugins/bower_components/calendar/dist/cal-init.js"></script>
 <!-- ============================================================== -->
 <!-- city-weather -->
 <!-- ============================================================== -->

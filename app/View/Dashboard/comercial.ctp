<?php echo $this->Html->css('css_temp/style'); ?>
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
	                        <h2 class="m-b-0"><?php echo CakeSession::read("Auth.User.name");?></h2>
	                        <h4 class="m-t-0"><?php echo CakeSession::read("Auth.User.username");?></h4>
	                    </div>
	                </div>
	                <div class="row text-center m-t-30">
	                    <div class="col-xs-4 b-r">
	                        <h4>138.654,89</h4>
	                        <h4>VENDAS</h4>
	                    </div>
	                    <div class="col-xs-4 b-r">
	                        <h4>69%</h4>
	                        <h4>REALIZADO</h4></div>
	                    <div class="col-xs-4">
	                        <h4>200.000,00</h4>
	                        <h4>META</h4>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="col-md-6 col-sm-12 col-lg-12">
	    	<div class="white-box">
                <h3 class="box-title">Indicadores</h3>
                <ul class="country-state">
                    <li>
                        <h2>R$25.563,23</h2> <small>Meta Diária</small>
                        <div class="progress">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:48%;"> <span class="sr-only">48% Complete</span></div>
                        </div>
                    </li>
                    <li>
                        <h2>R$145.652,54</h2> <small>Vendas previsto</small>
                        <div class="progress">
                            <div class="progress-bar progress-bar-inverse" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:98%;"> <span class="sr-only">98% Complete</span></div>
                        </div>
                    </li>
                    <li>
                        <h2>R$45.123,78</h2> <small>Saldo</small>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:75%;"> <span class="sr-only">75% Complete</span></div>
                        </div>
                    </li>
                    <li>
                        <h2>R$565.434,13</h2> <small>Orçamentos</small>
                        <div class="progress">
                            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:48%;"> <span class="sr-only">48% Complete</span></div>
                        </div>
                    </li>
                </ul>
            </div>	        
	    </div>
    </div>    

 	<div class="col-md-6 col-lg-8 col-sm-12">

        <div class="white-box bg-theme m-b-0 p-b-0 mailbox-widget">
            <h2 class="text-white p-b-20">Acompanhamento vendas</h2>
            <ul class="nav customtab nav-tabs" role="tablist">
            	<li role="presentation" class=""><a href="#pipeline" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-export"></i></span> <span class="hidden-xs">LEADS</span></a></li>
                <li role="presentation" class="active"><a href="#previsto" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-email"></i></span><span class="hidden-xs"> EM NEGOCIAÇÃO</span></a></li>
                <li role="presentation" class="active"><a href="#perdidos" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-email"></i></span><span class="hidden-xs"> AGUARDANDO APROVAÇÃO</span></a></li>
                <li role="presentation" class="active"><a href="#aprovados" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-email"></i></span><span class="hidden-xs"> APROVADOS</span></a></li>
                <li role="presentation" class=""><a href="#follow" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-panel"></i></span> <span class="hidden-xs">PERDIDOS</span></a></li>
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
									<td>PREVISÃO</td>
									<td>FECHAMENTO</td>
									<td>TEMP.</td>
									<td>ORÇAMENTO</td>
									<td>CLIENTE </td>
									<td>VALOR</td>
									<td>EM ABERTO</td>
								</tr>
                                <tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
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
									<td>PREVISÃO</td>
									<td>FECHAMENTO</td>
									<td>TEMP.</td>
									<td>ORÇAMENTO</td>
									<td>CLIENTE </td>
									<td>VALOR</td>
									<td>EM ABERTO</td>
								</tr>
                                <tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr><tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
								</tr>
								<tr class="unread">
									<td>19/04/2018</td>
									<td>11/09/2018</td>
									<td>11/09/2018</td>
									<td>10%</td>
									<td>109532</td>
									<td>TESTE RODOLFO </td>
									<td>520,00</td>
									<td>7.557,04</td>
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
    <div class="col-md-12 col-lg-4 col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Forecast Diário
                  <div class="col-md-6 col-sm-6 col-xs-6 pull-right">
                    <select class="form-control pull-right row b-none">
                      <option>Janeiro 2021</option>
                      <option>Fevereiro 2021</option>
                      <option>Março 2021</option>
                      <option>Abril 2021</option>
                      <option>Maio 2021</option>
                    </select>
                  </div>
            </h3>
            <div class="row sales-report">
                
                <div class="col-md-12 col-sm-12 col-xs-12 ">
                    <h1 class="text-right text-success m-t-20">R$ 69.000,00</h1> </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>DATA</th>
                            <th>VALOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td class="txt-oflo">01/02/2021</td>
                            <td><span class="text-success">R$ 5.490,00</span></td>
                        </tr>
                        
                    </tbody>
                </table> </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-4 col-sm-12">
        <div class="white-box">
            
            <h3 class="box-title">Confirmação financeira</h3>
            <div class="row sales-report">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <h2> </h2>
                    <p>Aguardando liberação de crédito financeiro</p>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 ">
                    <h1 class="text-right text-info m-t-20">R$3.690,21</h1>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                           	<th>ORÇAMENTO</th>
                            <th>CLIENTE</th>
                            <th>DATA</th>
                            <th>VALOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">115698</td>
                            <td><span class="label label-warning label-rouded">TESTE RODOLFO</span> </td>
                            <td class="txt-oflo">27/08/2018</td>
                            <td><span class="text-warning">R$2.4365,32</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-4 col-sm-12">
        <div class="white-box">
            
            <h3 class="box-title">Ranking de produtos</h3>
            <div class="row sales-report">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <h2> </h2>
                    <p>Lista dos 10 produtos mais vendidos</p>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 ">
                    <h1 class="text-right text-info m-t-20">R$3.690,21</h1>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                        	<th>#</th>
                           	<th>PRODUTO</th>
                            <th>VALOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="txt-oflo">1</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">2</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">3</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">4</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">5</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">6</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">7</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">8</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">9</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        <tr>
                            <td class="txt-oflo">10</td>
                            <td class="txt-oflo">Produto nome</td>
                            <td><span class="text-success">R$2.4365,32</span></td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 </div>


 <!-- ============================================================== -->
 <!-- city-weather -->
 <!-- ============================================================== -->


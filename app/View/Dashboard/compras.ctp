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
 	<div class="col-md-12 col-lg-4">
        <div class="white-box bg-danger m-b-0">
            <h3 class="box-title text-white">Programação diária</h3>
        </div>
        <div class="white-box p-b-0">
            <div class="row">
                <div class="col-xs-8">
                    <h2 class="font-medium m-t-0">R$ 5.895,43</h2>
                    <h5 class="text-muted m-t-0">Saldo para o dia 17/02/2021</h5>
                </div>
                
            </div>
            <div class="row m-t-30 minus-margin">
                <div class="col-sm-12 col-sm-6 b-t b-r">
                    <ul class="expense-box">
                        <li><span><h2>R$ 85.000,00</h2><h4>Limite diário</h4></span></li>
                    </ul>
                </div>
                <div class="col-sm-12 col-sm-6  b-t">
                    <ul class="expense-box">
                        <li><span><h2>R$ 74.104,57</h2><h4>Utilizado</h4></span></li>
                    </ul>
                </div>
            </div>
            <div class="row minus-margin">
                <div class="col-sm-12 col-sm-6  b-t b-r">
                    <ul class="expense-box">
                        <li><span><h2>R$ 13.500,00</h2><h4>Reprogramar pagamento</h4></span></li>
                    </ul>
                </div>
                <div class="col-sm-12 col-sm-6  b-t">
                    <ul class="expense-box">
                        <li><span><h2>18</h2><h4>Orçamentos</h4></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

 	<div class="col-md-8">
        <div class="white-box">
            <div id="calendar"></div>
        </div>
    </div>
 </div>


<!-- Calendar JavaScript -->
    <script src="../plugins/bower_components/calendar/jquery-ui.min.js"></script>
    <script src="../plugins/bower_components/moment/moment.js"></script>
    <script src='../plugins/bower_components/calendar/dist/fullcalendar.min.js'></script>
    <script src="../plugins/bower_components/calendar/dist/jquery.fullcalendar.js"></script>
    <script src="../plugins/bower_components/calendar/dist/cal-init.js"></script>
 <!-- ============================================================== -->
 <!-- city-weather -->
 <!-- ============================================================== -->


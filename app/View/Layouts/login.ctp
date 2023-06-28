<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$cakeDescription = __d('cake_dev', 'BeRH - Extranet');
?>
<!DOCTYPE html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $cakeDescription ?>
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <?php echo $this->element("favicon"); ?>

    <?php
        echo $this->Html->css('plugins.bundle.login');echo "\n\t";
        echo $this->Html->css('style.bundle.login');echo "\n\t";

        echo $this->fetch('css');echo "\n\t";

        echo $this->Html->script('jquery.min');echo "\n\t";
    ?>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <style>
        .btn-facebook:hover {
            color: #fff !important;
        }
    </style>
</head>

<body id="kt_body" class="bg-body">

    <div class="d-flex flex-column flex-root">
        <style>body { background-image: url('<?php echo $this->base ?>/img/background_image.jpg'); background-size: cover;} [data-theme="dark"] body { background-image: url('assets/media/auth/bg4-dark.jpg'); }</style>

        <div class="d-flex flex-column flex-lg-row flex-column-fluid">

            <!--begin::Aside-->
            <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
                <!--begin::Aside-->
                <div class="d-flex flex-center flex-lg-start flex-column">
                    <!--begin::Logo-->
                    <a href="#" >
                        <img alt="Logo" src="<?php echo $this->base."/img/logo-berh-colorido.png" ?>" class="h-100px" />
                    </a>
                    <!--end::Logo-->
                </div>
                <!--begin::Aside-->
            </div>
            <!--end::Aside-->

            <div class="d-flex flex-center w-lg-50 p-10">
                <div class="w-lg-500px p-10 p-lg-15 mx-auto">
                    <?php echo $this->fetch('content'); ?>
                </div>
            </div>
        </div>

        <!-- <div class="d-flex flex-end pe-10 align-items-center" style="gap: 15rem;">
            <div>
                <p>
                    <strong>Central de atendimento</strong> <br>
                    Segunda a Sexta das 8h às 18h <br>
                    (69) 3225-0442 ou 3225-0443 <br>
                    Outras Localidades 0800 084 0996
                </p>
            </div>
            <div>
                <p>
                    www.grupoberh.com.br <br>
                    ouvidoria@grupoberh.com.br <br>
                    faleconosco@grupoberh.com.br
                </p>
            </div>
            <div class="d-flex gap-15">
                <a href="#">
                    <i class="fab fa-square-facebook" style="font-size: 4em; color: #4267B2;"></i>
                </a>
                <a href="#">
                    <i class="fab fa-square-youtube" style="font-size: 4em; color: #FF0000;"></i>
                </a>
                <a href="#">
                    <img src="<?php echo $this->base.'/img/Instagram_icon.png' ?>" style="width: 4em;">
                </a>
            </div>
        </div> -->

    </div>
    
    <?php
        echo $this->Html->script('plugins.bundle');echo "\n\t";
        echo $this->Html->script('scripts.bundle');echo "\n\t";

        echo $this->fetch('script');
    ?>

</body>

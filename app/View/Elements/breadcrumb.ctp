<?php if (isset($action) || isset($breadcrumb)) { ?>
    <!--begin::Toolbar-->
    <div class="toolbar bg-light-dark" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <?php if (isset($action)) { ?>
                    <!--begin::Title-->
                    <h1 class="d-flex text-dark fw-bolder fs-3 align-items-center my-1"><?php echo $action ?></h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-300 border-start mx-4"></span>
                    <!--end::Separator-->
                <?php } ?>
                <?php if (isset($breadcrumb)) { ?>
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                        <?php 
                            $i=0;   
                            foreach ($breadcrumb as $nome => $url) {

                                $active = 'text-muted';
                                if ($i == (count($breadcrumb) - 1)) {
                                    $active = 'text-dark';
                                }

                                if ($i > 0) {
                                    echo '<li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>';
                                }

                                echo "<li class='breadcrumb-item $active'><a href='".$this->Html->url($url)."' class='$active'>$nome</a></li>";

                                $i++;
                            }
                        ?>
                    </ul>
                    <!--end::Breadcrumb-->
                <?php } ?>
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
<?php } ?>
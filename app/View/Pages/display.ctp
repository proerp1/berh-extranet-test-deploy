<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::FAQ card-->
        <div class="card">
            <!--begin::Body-->
            <div class="card-body p-lg-15">
                <!--begin::Layout-->
                <div class="d-flex flex-column flex-lg-row">
                    
                    <!--begin::Content-->
                    <div class="flex-lg-row-fluid">
                        <!--begin::Extended content-->
                        <div class="mb-13">
                            <!--begin::Content-->
                            <div class="mb-15">
                                <!--begin::Title-->
                                <h4 class="fs-2x text-gray-800 w-bolder mb-6"><?=$page_title?></h4>
                                <!--end::Title-->
                                <!--begin::Text-->
                                <p class="fw-semibold fs-4 text-gray-600 mb-2"><?=$page_subtitle?></p>
                                <!--end::Text-->
                            </div>
                            <div id="kt_job_8_1" class="fs-6 ms-1" style="">
                                        <!--begin::Text-->
                                        <div class="table-responsive">
                                        <table class="table">
                                        <thead>
                                        <tr class="fw-bolder text-muted bg-light"> 
                                        <th>Nome</th>
                                        <th>Data</th>
                                        <th>Observação</th>
                                        <th>Download</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php if ($data) { ?>
                                            <?php for ($i=0; $i < count($data); $i++) { ?>
                                                <tr>
                                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Comunicado"]["titulo"]; ?></td>
                                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Comunicado"]["data"]; ?></td>
                                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Comunicado"]["observacao"]; ?></td>
                                                <td class="fw-bold fs-7 ps-4"><a href="<?php echo Configure::read('Extranet.link').'/files/comunicado/file/'.$data[$i]["Comunicado"]["id"].'/'.$data[$i]["Comunicado"]["file"] ?> " target='_blank'> Clique aqui e faça o download.</a></td>
                                        
                                         </tr>
                                         <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td class="fw-bold fs-7 ps-4" colspan="3">Nenhum registro encontrado.</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        </table>
                                      </div>
                <!--end::Layout-->
                                   
            </div>
            <!--end::Body-->
        </div>
        <!--end::FAQ card-->
    </div>
    <!--end::Content container-->
</div>

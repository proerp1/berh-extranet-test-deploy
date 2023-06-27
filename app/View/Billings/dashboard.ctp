<?php
    echo $this->element("abas_billings", ['id' => $id]);
?>

<div class="row gy-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-success h-75px w-75px">
                    <i class="fas fa-users fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo $total_clientes ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">CLIENTES</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_mensal[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">MENSAL R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_serasa[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">SERASA R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_pefin[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">PEFIN R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_hipercheck[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">SOLUTECH R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_desconto[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">DESCONTO R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_total-$valor_desconto[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">TOTAL R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cols-2 gy-5 g-xl-10">
    <div class="col">
        <div class="card min-h-225px">
            <div class="card-body d-flex flex-column justify-content-between">
                <h3>RANKING DOS 15+ PRODUTOS</h3>
                <div class="table-responsive">
                    <?php echo $this->element("table"); ?>
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">Produto</th>
                                <th class="rounded-end">Valor Mensal R$</th>
                            </tr>
                        </thead>
                        <tbody id="prodBody">
                            
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation" class="pull-right">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link prevProdPage" href="#" disabled="disabled"><</a></li>
                        <li class="page-item"><a class="page-link nextProdPage" href="#">></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card min-h-225px">
            <div class="card-body d-flex flex-column justify-content-between">
                <h3>RANKING DOS 15+ PARCEIROS</h3>
                <div class="table-responsive">
                    <?php echo $this->element("table"); ?>
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">Parceiro</th>
                                <th class="rounded-end">Valor Mensal R$</th>
                            </tr>
                        </thead>
                        <tbody id="partBody">
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation" class="pull-right">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link prevPartPage" href="#" disabled="disabled"><</a></li>
                        <li class="page-item"><a class="page-link nextPartPage" href="#">></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
    var billingId = <?php echo $id; ?>;
    function getProd(billingId, pageProd){
        $.ajax({
            url: base_url+"/billings/get_ranking_produtos/",
            data: {page : pageProd, billing_id: billingId},
            dataType: "json",
            beforeSend: function(){
                $('#prodBody').html("<tr id='loadProd'><td colspan='2'>Carregando...</td></tr>");
            },
            success: function(data){
                var html = '';
                $.each(data.result, function( key, value ) {
                    html = html + '<tr> <td class="fw-bold fs-7 ps-4">'+value.produto+'</td> <td>R$ '+value.valor_consumo+'</td> </tr>';
                })

                if(pageProd > 1){
                    $('.prevProdPage').removeAttr('disabled')
                } else {
                    $('.prevProdPage').attr('disabled', true)
                }

                if(data.last){
                    $('.nextProdPage').attr('disabled', true)
                } else {
                    $('.nextProdPage').removeAttr('disabled')
                }

                $('#loadProd').remove();
                
                $('#prodBody').html(html);
            }
        })
    }

    function getPart(billingId, pagePart){
        $.ajax({
            url: base_url+"/billings/get_ranking_parceiros/",
            data: {page : pagePart, billing_id: billingId},
            dataType: "json",
            beforeSend: function(){
                $('#partBody').html("<tr id='loadPart'><td colspan='2'>Carregando...</td></tr>");
            },
            success: function(data){
                var html = '';
                $.each(data.result, function( key, value ) {
                    html = html + '<tr> <td class="fw-bold fs-7 ps-4">'+value.razao_social+'</td> <td>R$ '+value.totalFaturamento+'</td> </tr>';
                })

                if(pagePart > 1){
                    $('.prevPartPage').removeAttr('disabled')
                } else {
                    $('.prevPartPage').attr('disabled', true)
                }

                if(data.last){
                    $('.nextPartPage').attr('disabled', true)
                } else {
                    $('.nextPartPage').removeAttr('disabled')
                }

                $('#loadPart').remove();
                
                $('#partBody').html(html);
            }
        })
    }

    function getCust(billingId, pageCust){
        $.ajax({
            url: base_url+"/billings/get_ranking_clientes/",
            data: {page : pageCust, billing_id: billingId},
            dataType: "json",
            beforeSend: function(){
                $('#custBody').html("<tr id='loadCust'><td colspan='2'>Carregando...</td></tr>");
            },
            success: function(data){
                var html = '';
                $.each(data.result, function( key, value ) {
                    html = html + '<tr> <td class="fw-bold fs-7 ps-4">'+value.razao_social+'</td> <td>R$ '+value.totalFaturamento+'</td> </tr>';
                })

                if(pageCust > 1){
                    $('.prevCustPage').removeAttr('disabled')
                } else {
                    $('.prevCustPage').attr('disabled', true)
                }

                if(data.last){
                    $('.nextCustPage').attr('disabled', true)
                } else {
                    $('.nextCustPage').removeAttr('disabled')
                }

                $('#loadCust').remove();
                
                $('#custBody').html(html);
            }
        })
    }


    $(document).ready(function(){
        var pageProd = 1;
        var pageCust = 1;
        var pagePart = 1;

        getProd(billingId, pageProd);
        getPart(billingId, pagePart);
        getCust(billingId, pageCust);

        $(".nextProdPage").on("click", function(e){
            $(this).blur();
            pageProd = pageProd + 1;
            getProd(billingId, pageProd);
            e.preventDefault();
        })

        $(".prevProdPage").on("click", function(e){
            $(this).blur();
            pageProd = pageProd - 1;
            getProd(billingId, pageProd);
            e.preventDefault();
        })

        $(".nextPartPage").on("click", function(e){
            $(this).blur();
            pagePart = pagePart + 1;
            getPart(billingId, pagePart);
            e.preventDefault();
        })

        $(".prevPartPage").on("click", function(e){
            $(this).blur();
            pagePart = pagePart - 1;
            getPart(billingId, pagePart);
            e.preventDefault();
        })

        $(".nextCustPage").on("click", function(e){
            $(this).blur();
            pageCust = pageCust + 1;
            getCust(billingId, pageCust);
            e.preventDefault();
        })

        $(".prevCustPage").on("click", function(e){
            $(this).blur();
            pageCust = pageCust - 1;
            getCust(billingId, pageCust);
            e.preventDefault();
        })
    })
</script>

<style>
    a[disabled="disabled"] {
        color: #999 !important;
        pointer-events: none;
        background-color: #ddd !important;
    }
    .mt-5 {
        margin-top: 30px;
    }
</style>
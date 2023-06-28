<!-- layout com logo da serasa junto -->
<style>html,body { padding: 0; margin:0; }</style>
<div style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#494c50">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 auto; padding:0; max-width:100%">
        <tbody>
            <tr>
                <td>
                    <div style="margin: 0 20px; background-color: #eee;border-top-left-radius: 6px;border-radius: 6px">
                        <a href="https://grupoberh.com.br/" rel="noopener" target="_blank" style="text-decoration: none;">
                            <img alt="Logo" src="http://berh.com.br/extranet/img/logo-berh-colorido.png" style="height: 80px;" />
                        </a>
                        <a href="https://grupoberh.com.br/" rel="noopener" target="_blank" style="text-decoration: none; float: right;">
                            <img alt="Logo" src="http://berh.com.br/cliente/img/logo-serasa-novo.png" style="height: 80px;" />
                        </a>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="left" valign="center">
                    <div style="text-align:left; margin: 0 20px; padding: 40px; background-color:#eee; border-radius: 6px">
                        <h3>Olá! Temos um comunicado importante para você.</h3>
                        <p>
                            Nome: <?php echo $pefin['CadastroPefin']['nome']; ?> <br>
                            CPF: <?php echo $pefin['CadastroPefin']['documento']; ?>
                        </p>
                        <p>
                            Atendendo à legislação vigente* viemos avisar que a empresa credora abaixo entrou em contato com a gente e solicitou a
                            abertura de cadastro negativo em seu nome:
                        </p>
                        <p>
                            Instituição Credora: <?php echo $pefin['Customer']['nome_primario']; ?> <br>
                            Nome Fantasia: <?php echo $pefin['Customer']['nome_secundario']; ?> <br>
                            CNPJ Instituição Credora: <?php echo $pefin['Customer']['documento']; ?> <br>
                            Endereço da Credora: <?php echo $pefin['Customer']['endereco'].' '.$pefin['Customer']['numero'].' - '.$pefin['Customer']['cidade'].' - '.$pefin['Customer']['estado'].' - '.$pefin['Customer']['bairro'].' CEP: '.$pefin['Customer']['cep']; ?> <br>
                            Telefone: <?php echo $pefin['Customer']['telefone1']; ?>
                        </p>
                        <table border="1" style="border-collapse: collapse; width: 100%;">
                            <tr>
                                <th style="text-align: center; padding: 10px; background-color: #ddd;">Valor da anotacao</th>
                                <th style="text-align: center; padding: 10px; background-color: #ddd;">Data de vencimento</th>
                                <th style="text-align: center; padding: 10px; background-color: #ddd;">Natureza</th>
                                <th style="text-align: center; padding: 10px; background-color: #ddd;">Contrato</th>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding: 10px;">R$ <?php echo $pefin['CadastroPefin']['valor']; ?></td>
                                <td style="text-align: center; padding: 10px;"><?php echo $pefin['CadastroPefin']['venc_divida']; ?></td>
                                <td style="text-align: center; padding: 10px;"><?php echo $pefin['NaturezaOperacao']['nome']; ?></td>
                                <td style="text-align: center; padding: 10px;"><?php echo $pefin['CadastroPefin']['numero_titulo']; ?></td>
                            </tr>
                        </table>
                        <p>
                            <strong>Contudo, ainda dá tempo de regularizar a sua situação.</strong> A partir da data de emissão deste comunicado, você possui <strong>10 dias</strong> para entrar em contato com a empresa credora e resolver o débito*. Caso você já tenha regularizado a dívida, desconsidere este comunicado.
                        </p>
                        <p><small>*Conforme previsto no art. 43, parágrafo segundo, do Código de Defesa do Consumidor.</small></p>
                        <p><strong>Algumas coisas que você precisa saber:</strong></p>
                        <p>
                            <ul>
                                <li>Se após este período você não tiver regularizado o débito com o credor, ou o credor não tiver informado a Serasa que a situação foi resolvida, <strong>as informações serão disponibilizadas no cadastro de inadimplentes para consulta.</strong> Ou seja, você será negativado(a).</li>
                                <li>
                                    <?php echo $pefin['Customer']['nome_primario']; ?> informa, última oportunidade para renegociar seus débitos <br>
                                    Confira a condição IMPERDÍVEL reservada exclusivamente para você, renegocie e remova seu cadastro da SERASA. <br>
                                    Proposta exclusiva válida somente até <?php echo date('d/m/Y', strtotime('+20 days')); ?>. <br>
                                    Para renegociar acesse nosso portal <a href="https://grupoberh.com.br/">https://grupoberh.com.br/</a> <br>
                                    Ou ligue gratuitamente para 0800 084 0996
                                </li>
                            </ul>
                        </p>
                        <!--end:Email content-->
                        <div style="padding-bottom: 10px">Atenciosamente,
                            <br>BeRH
                            <tr>
                                <td align="center" valign="center" style="font-size: 13px; text-align:center;padding: 20px; color: #fff;">
                                    <p>Atendimento 0800 084 0996 | <a href="mailto:berh@berh.com.br" style="border:none; color:#fff;">berh@berh.com.br </a></p>
                                    <p>Copyright © <a href="https://grupoberh.com.br/" rel="noopener" target="_blank" style="border:none; color:#fff;">BeRH</a>.</p>
                                </td>
                            </tr>
                            </br>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
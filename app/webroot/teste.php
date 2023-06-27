<?php
$mq_host_ip='200.245.207.146(1425)';
$queue_name = 'MEAVISE.RELATORIO008663497';
$mq_server = 'QMIFTSERASA';
$mqcno = [
    'Version' => 5,
    'Options' => MQSERIES_MQCNO_STANDARD_BINDING,
    'MQCD' => [
	'Version' => 7,
        "ChannelName" => "CREDCHECK.MEAVISE",
        'ConnectionName' => $mq_host_ip,
        'TransportType' => MQSERIES_MQXPT_TCP,
        'SSLCipherSpec' => 'TLS_RSA_WITH_AES_256_CBC_SHA256'
    ],
    'MQSCO' => [
        'KeyRepository' => '/opt/mqm/client_keys/db_certs_cred'
    ]
];
// Connect to the MQ server
mqseries_connx($mq_server, $mqcno, $conn, $comp_code, $reason);
if ($comp_code !== MQSERIES_MQCC_OK) {
     printf("Connx CompCode:%d Reason:%d Text:%s<br>\n", $comp_code, $reason, mqseries_strerror($reason));
     die('nope');
}else{
     die('yes');
}

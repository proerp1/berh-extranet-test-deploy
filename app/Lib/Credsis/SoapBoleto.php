<?php 

class SoapBoleto extends SoapClient
{

    function __construct($url)
    {
        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        parent::__construct($url, ['soap_version' => SOAP_1_2, 'trace' => 1, 'stream_context' => $context]);
    }

    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $location = str_replace('http://', 'https://', $location);
        $response = parent::__doRequest($request, $location, $action, $version, $one_way);

        return $response;
    }
}
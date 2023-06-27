<?php

class Commom {
	public function post($function, $arguments){
		ini_set("soap.wsdl_cache_enabled", "0");
		/*if(CURL_ENV == "HOMOLOG"){
			$client = new SoapClient("https://webservices.twwwireless.com.br/reluzcap/wsreluzcap.asmx?WSDL", array('proxy_host' => "192.168.0.5", 'proxy_port' => 3128));
		} else {*/
			$client = new SoapClient("https://webservices.twwwireless.com.br/reluzcap/wsreluzcap.asmx?WSDL");
		//}

		$options = array('location' => 'https://webservices.twwwireless.com.br/reluzcap/wsreluzcap.asmx');

		$result = $client->__soapCall($function, $arguments, $options);

	}
}
<?php
/*
 * AdobeConnect 8 api client
 * @see https://github.com/sc0rp10/AdobeConnect-php-api-client
 * @see http://help.adobe.com/en_US/connect/8.0/webservices/index.html
 * @version 0.1a
 *
 * Copyright 2012, sc0rp10
 * https://weblab.pro
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 *
 */


//require_once($_SERVER[DOCUMENT_ROOT].'/lib/com_func.inc.php'); // Para desarrollo usar /vanas/lib

class AdobeConnectClient {
	/**
	 * your personally root-folder id
	 * @see http://forums.adobe.com/message/2620180#2620180
	 */
	 // MDB Para el rootFolderId tomar del url, sco-id=1090773780
  
  private $userName;
  private $password;
  private $baseDomain;
  private $rootFolderId;
  

	/**
	 * @var string filepath to cookie-jar file
	 */
	private $cookie;

	/**
	 * @var resource
	 */
	private $curl;

	/**
	 * @var bool
	 */
	private $is_authorized = false;

	/**
	 *
	 */
	public function __construct (LicenciaAdobe $licenciaAC) {
    
        $this->userName = $licenciaAC->getDsUsr();
        $this->password = $licenciaAC->getDsPwd();
        $this->baseDomain = $licenciaAC->getDsBaseDomain();
        $this->rootFolderId = $licenciaAC->getDsRootFolderId();
        $this->principalId = $licenciaAC->getDsPrincipalId();    
                
		$this->cookie = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cookie_'.time().'.txt';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_REFERER, $this->baseDomain);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // MDB Evitar el problema con certificado de seguridad del sitio adobe connect
		$this->curl = $ch;
		$this->makeAuth();
	}

	/**
	 * make auth-request with stored username and password
	 *
	 * @return AdobeConnectClient
	 */
	public function makeAuth() {
		$this->makeRequest('login',
			array(
				'login'    => $this->userName,
				'password' => $this->password
			)
		);
		$this->is_authorized = true;
		return $this;
	}

	public function getCommonInfo() {
		return $this->makeRequest('common-info');
	}  
  
	// MDB Conecta a la sesion como host
  public function getSessionHost($url) {    
    $info = $this->getCommonInfo();        
    return $info['common']['cookie'];    
  }
  

	public function __destruct() {
		@curl_close($this->curl);
	}

	/**
	 * @param       $action
	 * @param array $params
	 * @return mixed
	 * @throws Exception
	 */
  private function makeRequest($action, array $params = array()) {
		$url = $this->baseDomain . "api/";
		$url .= 'xml?action='.$action;
		$url .= '&'.http_build_query($params);

		curl_setopt($this->curl, CURLOPT_URL, $url);
		$result = curl_exec($this->curl);
		
		// MDB Manejo de errores, solo para desarrollo o debug
		/*
		if (curl_errno($this->curl)) { 
            print "Error: " . curl_error($this->curl); 
        } else { 
            var_dump($data); 
            //curl_close($this->curl);  // MDB No se puede cerrar porque lo usamos para los siguientes request ya cuando estamos firmados
        } */
				
		$xml = simplexml_load_string($result);

		$json = json_encode($xml);
		$data = json_decode($json, TRUE); // nice hack!
		
		if (!isset($data['status']['@attributes']['code']) || $data['status']['@attributes']['code'] !== 'ok') {
      $data = "";
			//throw new Exception('Coulnd\'t perform the action: '.$action);
		}

		return $data;
	}
}
?>
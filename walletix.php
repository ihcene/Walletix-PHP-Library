<?php

/*
 * Cette bibliothèque permet d'utiliser les deux fonctionnes de Walletix :
 * - Générer un code de paiement à partir de l’identifiant de la commande 
 * et du montant à payer. 
 * - Vérifier un code de confirmation (vérifier si une opération de 
 * paiement a bien été effectué).
 *
 *
 * Copyright (c) 2010-2011 Youghorta BENALI
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/


define('WALLETIX_GENCODE_OK',             1);
define('WALLETIX_VERCODE_OK',             1);
define('WALLETIX_CONFCODE_OK',            1);
define('WALLETIX_GENCODE_ERROR',          0);
define('WALLETIX_GENCODE_ERROR_NaN',      -1);
define('WALLETIX_GENCODE_ERROR_ID_NaN',   -2);
define('WALLETIX_GENCODE_ERROR_AUTH',     -3);



 
class WalletixAPI
{
  var $vendorID;
  var $apiKey;
  var $apiLocation = 'http://www.walletix.com/ws/';
  
  function __construct($vendorID = null, $apiKey = null){
    if (isset($vendorID)) $this->vendorID = $vendorID;
    if (isset($apiKey)) $this->apiKey = $apiKey;
  }

  public function setVendorID($vendorID)
  {
    $this->vendorID = intval($vendorID);
  }

  public function setApiKey($apiKey)
  {
    $this->apiKey = $apiKey;
  }
  
  public function setApiLocation($apiLocation)
  {
    $this->apiLocation = $apiLocation;
  }
  
  public function verifyCode($paiementCode, $confirmationCode) {
		if ($this->hasNoEnoughInformations()) {
		  $this->trigger_data_error();
		  return false;
		}
		else{
		  	
					$params = array(
			  'vendorID'          => $this->vendorID, 
			  'apiKey'            => $this->apiKey, 
			  'paiementCode'      => $paiementCode, 
			  'confirmationCode'  => $confirmationCode
			);

			return $this->callAPI($this->apiLocation.'codeconfirmation.php', $params);
		}
	}
  
  public function generatePaiementCode($purchaseID, $amount)
  {
    if ($this->hasNoEnoughInformations()) {
      $this->trigger_data_error();
      return false;
    }
    else{
      

    	$params = array(
    	  'vendorID'    => $this->vendorID, 
    	  'apiKey'      => $this->apiKey, 
    	  'purchaseID'  => $purchaseID, 
    	  'amount'      => $amount
    	);
	  

    	return $this->callAPI($this->apiLocation.'codepaiement.php', $params);;
    }
  }
  
  
  protected function callAPI($url, $params) {
  	$ch = curl_init();
  	
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  	curl_setopt($ch, CURLOPT_POST, true);
  	
  	$result = curl_exec($ch);
  	
  	curl_close($ch);
  
  	return $this->apiResponseToXMLObject($result);
  }
  
  private function apiResponseToXMLObject($rawXMLString){
  	return $rawXMLString = 
    	new SimpleXMLElement(
    	  utf8_encode(
    	    html_entity_decode(
    	      $rawXMLString
    	    )
    	  )
    	);
  }
  
  
  
  private function hasEnoughInformations()
  {
    return !(empty($this->vendorID) || empty($this->apiKey));
  }
  
  private function hasNoEnoughInformations(){
    return !$this->hasEnoughInformations();
  }
  
  private function trigger_data_error(){
    trigger_error('Please set the $vendorID and $apiKey attributes with accessors setVendorID and setApiKey before calling this method', E_USER_WARNING);
  }
  
} // END class 

?>
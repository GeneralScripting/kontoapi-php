<?php

/**
 * API wrapper for kontoapi.de
 * 
 * requires php5-curl
 * 
 * @author manuel.andreo@crowdguru.de
 */
class KontoAPI
{
    private $apiKey = '<YOUR-API-KEY>';

    private $validityUrl = 'https://ask.kontoapi.de/for/validity.json';

    private $banknameUrl = 'https://ask.kontoapi.de/for/bankname.json';

    /**
     * validates an account-number / bank-code combination 
     * 
     * @param string $accountNumber
     * @param string $bankCode
     * @throws Exception
     * @return boolean
     */
    public function validateAccount($accountNumber, $bankCode)
    {        
        $query = http_build_query(array(
            'key' => $this->getApiKey(),
            'ktn' => $accountNumber,
            'blz' => $bankCode
        ), '', '&');

        $result = $this->request("{$this->getValidityUrl()}?{$query}");
        
        return $result['answer'] == 'yes';
    }
    
    /**
     * get bank-name for a given bank-code
     * 
     * @param string $bankCode
     * @throws Exception
     * @return string
     */
    public function getBankName($bankCode)
    {
        $query = http_build_query(array(
            'key' => $this->getApiKey(),
            'blz' => $bankCode
        ), '', '&');
    
        $result = $this->request("{$this->getBanknameUrl()}?{$query}");
    
        return $result['answer'];
    } 

    /**
     * request kontoapi.de
     * 
     * @param string $url
     * @throws Exception
     * @return array
     */
    private function request($url)
    {
        $handler = curl_init();
        curl_setopt_array($handler, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        ));
        
        if (!$result = curl_exec($handler)) {
            throw new Exception(curl_error($handler));
        }
        
        curl_close($handler);
        
        $result = json_decode($result, true);
        if (isset($result['error'])) {
            throw new Exception($result['error']);
        }
        
        return $result;
    }
    
    /*
     * getter & setter
     */

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getValidityUrl()
    {
        return $this->validityUrl;
    }

    public function setValidityUrl($validityUrl)
    {
        $this->validityUrl = $validityUrl;
    }

    public function getBanknameUrl()
    {
        return $this->banknameUrl;
    }

    public function setBanknameUrl($banknameUrl)
    {
        $this->banknameUrl = $banknameUrl;
    }
}

?>

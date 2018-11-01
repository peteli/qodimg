<?php
class QOD{
    private $qodurl = 'http://quotes.rest/qod.json';
    public $quote='';
    public $author='';
    public $background='';

    public function getQuote(){
        $return_value = $this->getRequest($this->qodurl, '', 5);
    }
    public function getRequest($url, $refer = "", $timeout = 10){
        /**
        * Curl send get request, support HTTPS protocol
        * @param string $url The request url
        * @param string $refer The request refer
        * @param int $timeout The timeout seconds
        * @return mixed
        */
        $ssl = stripos($url,'https://') === 0 ? true : false;
        $curlObj = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            #CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0',
        ];
        if ($refer) {
            $options[CURLOPT_REFERER] = $refer;
        }
        if ($ssl) {
            #$options[CURLOPT_SSL_VERIFYHOST] = false; //bad idea my firefox refuses to load page due to missing authentification
            #$options[CURLOPT_SSL_VERIFYPEER] = false; //bad idea my firefox refuses to load page due to missing authentification
        }
        curl_setopt_array($curlObj, $options);
        $returnData = curl_exec($curlObj);
        #var_dump(json_decode($returnData));
        
        // check for errors
        $return_val = true;
        if (curl_errno($curlObj)) { #NOK
            // load default json file
            $returnData = file_get_contents("qod_defaults.json");
            $jsonText = json_decode($returnData);
            //$this->quote = curl_error($curlObj);
            $return_val = false;
        } else {
            switch ($http_code = curl_getinfo($curlObj, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                  $jsonText = json_decode($returnData);
                break;
                default:
                  $returnData = file_get_contents("qod_defaults.json");
                  $jsonText = json_decode($returnData);
                  $return_val = false;
            }
        }
        curl_close($curlObj);
        $this->quote = $jsonText->contents->quotes[0]->quote;
        $this->author = $jsonText->contents->quotes[0]->author;
        $this->background = $jsonText->contents->quotes[0]->background;
        return $return_val;
    }
}
?>
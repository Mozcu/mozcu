<?php

namespace Mozcu\MozcuBundle\Service;

use Mozcu\MozcuBundle\Exception\AppException;

class RestService extends BaseService {
    
    const POST   = "POST";
    const GET    = "GET";
    const PUT    = "PUT";
    const DELETE = "DELETE";
    
    /**
     * Default options for curl.
     */
    public static $curlConfiguration = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60
    );
    
    // -------------------------------------------------------------------------
    /**
     * @param  array $options
     * @param  array $parameters
     * @return array 
     */
    private function setProperHttpMethod($options, $parameters) {
        if(!isset($parameters['method'])) {
            $parameters['method'] = self::POST;
        }
        
        switch ($parameters['method']) {
            case self::PUT:
                $options[CURLOPT_PUT] = true;
            break;

            case self::DELETE:
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            break;

            case self::GET:
                $options[CURLOPT_HTTPGET] = true;
            break;

            case self::POST:
            default:
                $options[CURLOPT_POST] = true;
        }            
            
        return $options;
    }


    // -------------------------------------------------------------------------
    /**
     * @param  string $url
     * @param  string $params
     * @return string
     * @throws NeoBizApiException 
     */
    public function makeRequest($url, $params) {
        $curlResource = curl_init();
        
        $opts = self::$curlConfiguration;
        
        $opts = $this->setProperHttpMethod($opts, $params);
        unset($params['method']);
        
        $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        $opts[CURLOPT_URL]        = $url;

        // disable the 'Expect: 100-continue' behaviour
        if(isset($opts[CURLOPT_HTTPHEADER])) {
            $existing_headers = $opts[CURLOPT_HTTPHEADER];
            $existing_headers[] = 'Expect:';
            $opts[CURLOPT_HTTPHEADER] = $existing_headers;
        } else {
            $opts[CURLOPT_HTTPHEADER] = array('Expect:');
        }

        curl_setopt_array($curlResource, $opts);
        $result = curl_exec($curlResource);

        if ($result === false) {
            $e = new AppException("CurlException: " . curl_error($curlResource) . ' [' . curl_errno($curlResource) . ']');
            
            curl_close($curlResource);
            throw $e;
        }
        
        curl_close($curlResource);
        return $result;
    }
}
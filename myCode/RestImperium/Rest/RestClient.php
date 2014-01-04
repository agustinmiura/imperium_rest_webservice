<?php
/**
 * Copyright 2013 Agustín Miura <"agustin.miura@gmail.com">
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
namespace RestImperium\Rest;

class RestClient
{
    public function __construct()
    {

    }

    public function consumeGet($url, $headerParameters, $requestParameters)
    {
        $data = $this->_performCurlGetRequest(
            $url,
            $headerParameters,
            $requestParameters
        );

        return array(
            'success'=>true,
            'data'=>$data
        );
    }

    public function consumePost($url, $headerParameters, $requestParameters)
    {
        $data = $this->_performPostRequest(
            $url,
            $headerParameters,
            $requestParameters
        );

        return array(
            'success'=>true,
            'data'=>'DATA',
            'data1'=>$data
        );
    }

    private function _createGetFields($parameters)
    {
        $answer = '';

        $index = 0;
        foreach ($parameters as $key => $value) {
            if ($index===0) {
                $answer .= '?'.$key.'='.$value;
            } else {
                $answer .= '&'.$key.'='.$value;
            }

            $index++;
        }
        return $answer;
    }

    private function _createHeaderArray($parameters)
    {

        $answer = array();
        $eachParameter;
        $parsedKey;
        $parsedValue;
        foreach ($parameters as $key => $value) {
            $parsedKey = strtolower($key);
            $parsedValue = strtolower($value);
            $eachParameter = '%s: %s';
            $eachParameter = sprintf($eachParameter, $parsedKey, $parsedValue);
            $answer[] = $eachParameter;
        }

        return $answer;
    }
    private function _performCurlGetRequest($url , $headers, $parameters)
    {
        $getFields = $this->_createGetFields($parameters);

        $url .= $getFields;

        $currentHeaders = $this->_createHeaderArray($headers);

        $options = array(
            CURLOPT_HTTPHEADER => $currentHeaders,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $curlObject = curl_init();
        curl_setopt_array($curlObject, $options);
        $answer = curl_exec($curlObject);
        curl_close($curlObject);

        return $answer;
    }

    private function _encondePostParameters($parameters)
    {
        $answer='';
        $paramTemplate = '%s=%s';
        $param;
        foreach ($parameters as $key => $value) {
            $param = sprintf($paramTemplate, $key, urlencode($value));
            $param .= '&';
            $answer .= $param;

        }
        return $answer;
    }

    private function _performPostRequest($url, $headers, $parameters)
    {
        $header = $this->_createHeaderArray($headers);

        $encodedPostParameters = $this->_encondePostParameters($parameters);

        $options = array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $encodedPostParameters
        );

        $curlObject = curl_init();
        curl_setopt_array($curlObject, $options);
        $answer = curl_exec($curlObject);
        curl_close($curlObject);

        return $answer;

    }

}

<?php

namespace App\Libraries;

use GuzzleHttp\Client as GuzzleHttp;

class Request{

    protected static $client;

    const TIMEOUT = 120;

    public static function instance($baseUri){

        if(!isset(self::$client[$baseUri])){
            self::$client[$baseUri] = new GuzzleHttp(
                [
                    'base_uri'=> $baseUri,
                    'timeout' => self::TIMEOUT,
                ]
            );
        }
        return self::$client[$baseUri];
    }

    public static function execute($method,$url,$query=[]){

        return self::{$method}($url,$query);
    }

    public static function get($url,$options=[]){

        $baseUri = pathinfo($url,PATHINFO_DIRNAME);

        $client = self::instance($baseUri);

        try {
            if($options){
                $response = $client->request('GET', $url, $options);
            }else{
                $response = $client->request('GET', $url );
            }

            if($response->getStatusCode() == 200){
                return [
                    'code' => 200,
                    'message' => 'Success',
                    'data'  => $response->getBody()->getContents(),
                ];
            }

            return [
                'code' => 1,
                'message' => 'Error',
                'data' => [],
            ];

        } catch (\Exception $e) {

            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }

    }

    public static function post($url,$query=[]){

        $baseUri = pathinfo($url,PATHINFO_DIRNAME);

        $client = self::instance($baseUri);

        $response = $client->request('POST', $url,[
            'form_params' => $query
        ]);

        if($response->getStatusCode() == 200){

            return $response->getBody()->getContents();
        }
        return false;
    }

    public static function put($url,$options=[]){

        $baseUri = pathinfo($url,PATHINFO_DIRNAME);

        $client = self::instance($baseUri);

        try {
            $response = $client->request('PUT', $url,$options);

            if($response->getStatusCode() == 200){
                return [
                    'code' => 200,
                    'message' => 'Success',
                    'data'  => $response->getBody()->getContents(),
                ];
            }

            return [
                'code' => 1,
                'message' => 'Error',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }

    }

    public static function patch($url,$options=[]){

        $baseUri = pathinfo($url,PATHINFO_DIRNAME);

        $client = self::instance($baseUri);

        try {
            $response = $client->request('PATCH', $url,$options);

            if($response->getStatusCode() == 200){
                return [
                    'code' => 200,
                    'message' => 'Success',
                    'data'  => $response->getBody()->getContents(),
                ];
            }

            return [
                'code' => 1,
                'message' => 'Error',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }

    }
}

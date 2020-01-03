<?php
/**
 * @deprecated  godaddy api 接口
 */

namespace App\Libraries\GoDaddy;
use App\Libraries\Request as RC;

class RestApi
{

    protected static $API_URL = 'https://api.godaddy.com';
    protected static $API_KEY = 'you key';
    protected static $API_SECRET = 'you secret';

    protected static $DOMAINS = 'you domain';

    protected static $PUT_RECORDS = '/v1/domains/%s/records'; # 更新域名记录，%s : domain
    protected static $GET_DOMAIN_INFO = '/v1/domains/%s';  # 获取域名购买者信息 %s : domain
    protected static $GET_DOMAIN_RECORDS = '/v1/domains/%s/records/%s/%s'; #获取域名记录 %s : domain , type , name

    public function __construct()
    {

    }

    // 更新域名 (域名所有的记录值都会更新)
    public function changeDomainRecords($domain = array())
    {
        $apiUrl = sprintf(self::$API_URL.self::$PUT_RECORDS, self::$DOMAINS );

//        $domain = [
//            [
//                "data" => 'ip',
//                "name" => "@",
//                "ttl" => 600,
//                "type" => 'A',
//            ],
//            [
//                "data" => '@',
//                "name" => "www",
//                "ttl" => 600,
//                "type" => 'CNAME',
//            ],
//            [
//                "data" => 'ns57.domaincontrol.com',
//                "name" => "@",
//                "ttl" => 3600,
//                "type" => 'NS',
//            ],
//            [
//                "data" => 'ns58.domaincontrol.com',
//                "name" => "@",
//                "ttl" => 3600,
//                "type" => 'NS',
//            ],
//        ];

        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => sprintf("sso-key %s:%s", self::$API_KEY, self::$API_SECRET),
            ],
            'json' => $domain,
        ];

        return  RC::put($apiUrl, $data) ;
    }

    // 增加指定记录
    public function addDomainRecords($type, $name, $data)
    {
        $apiUrl = sprintf(self::$API_URL.self::$PUT_RECORDS, self::$DOMAINS );

        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => sprintf("sso-key %s:%s", self::$API_KEY, self::$API_SECRET),
            ],
            'json' => [
                [
                    "data" => $data,
                    "name" => $name,
                    "ttl" => 600,
                    "type" => $type,
                ],
            ],
        ];

        return RC::patch($apiUrl, $data) ;
    }

    // 更新已经存在指定的记录 type和name相同的情况下， 如果data不相同则替换更新
    public function updateDomainRecords($type, $name, $data)
    {
        // 获取此域名所有记录
        $getDomainList = $this->getDomainRecords();
        if ($getDomainList['code'] != 200 || count($getDomainList['data']) == 0) {
            return false;
        }

        $domainList = $getDomainList['data'];

        // 更新记录处理
        $console = false;
        foreach ($domainList as $key => $row) {
            if ($row['type'] == $type && $row['name'] == $name && $row['data'] != $data) {
                $console = true;
                $domainList[$key]['data'] = $data;
            }
        }

        // 有更新
        $result = false;
        if ($console) {
            $result =  $this->changeDomainRecords($domainList);
        }

        return $result;
    }

    // 删除已经存在指定的记录, 根据type和name
    public function delDomainRecords($type, $name)
    {
        // 获取此域名所有记录
        $getDomainList = $this->getDomainRecords();
        if ($getDomainList['code'] != 200 || count($getDomainList['data']) == 0) {
            return false;
        }

        $domainList = $getDomainList['data'];

        // 删除记录处理
        $console = false;
        foreach ($domainList as $key => $row) {
            if ($row['type'] == $type && $row['name'] == $name) {
                $console = true;
                unset($domainList[$key]);
            }
        }

        // 有删除
        $result = false;
        if ($console) {
            $result =  $this->changeDomainRecords(array_values($domainList));
        }

        return $result;
    }

    // 获取域名记录信息
    public function getDomainRecords($type = '' , $name = '')
    {
        $type = '';   # 可以为空， 也可以指定type ： A CNAME ...
        $name = '';   # 可以为空， 也可以指定name ： @ www ...

        $apiUrl = sprintf(self::$API_URL.self::$GET_DOMAIN_RECORDS, self::$DOMAINS , $type, $name);

        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => sprintf("sso-key %s:%s", self::$API_KEY, self::$API_SECRET),
            ],
        ];

        $result =  RC::get($apiUrl, $data);
        if ($result['code'] == 200) {
            $result['data'] = objectToArray(json_decode($result['data']));
        }

        return $result;
    }

    // 获取域名购买者的一些信息
    public function getDomainInfo()
    {
        $apiUrl = sprintf(self::$API_URL.self::$GET_DOMAIN_INFO, self::$DOMAINS );

        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => sprintf("sso-key %s:%s", self::$API_KEY, self::$API_SECRET),
            ],
        ];

        $result =  RC::get($apiUrl, $data);
        if ($result['code'] == 200) {
            $result['data'] = objectToArray(json_decode($result['data']));
        }

        return $result;
    }

}

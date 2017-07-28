<?php
namespace PMVC\PlugIn\algolia;

use IdOfThings\GetDb;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\algolia';

\PMVC\initPlugin(['guid'=>null]);
\PMVC\l(__DIR__.'/src/BaseAlgolia.php');

/**
 * @parameters string app 
 * @parameters string key 
 */
class algolia extends GetDb
{
    public function init()
    {
        if (!isset($this['app'])) {
            return !trigger_error('Not set algolia app.');
        }
        if (!isset($this['key'])) {
            return !trigger_error('Not set algolia key.');
        }
        $this->setConnected(true);
    }

    public function getBaseDb()
    {
        return __NAMESPACE__.'\BaseAlgolia';
    }

    public function getNameSpace()
    {
        return __NAMESPACE__;
    }

    public function getBaseUrl()
    {
        return 'https://'.$this['app'].'.algolia.net/1';
    }

    public function request($url, $params=[])
    {
        $respond = null;
        $url->set($this->getBaseUrl());
        $header = [
            'X-Algolia-Application-Id: '.$this['app'],
            'X-Algolia-API-Key: '.$this['key']
        ];
        $params[CURLOPT_HTTPHEADER] = array_merge(
            \PMVC\value($params,[CURLOPT_HTTPHEADER],[]),
            $header
        );
        $curl  = \PMVC\plug('curl');
        $curl->get((string)$url,function($r) use (&$respond, $url){
            if (500 > $r->code) {
               $respond = (object)[
                'header' => $r->header,
                'body'   => \PMVC\fromJson($r->body),
                'code'   => $r->code
               ];
            } else {
                return !trigger_error('Get result error. Error Code:'.$r->code. ' url: '.$url);
            }
        })->set($params);
        $curl->process();
        return $respond;
    }
}

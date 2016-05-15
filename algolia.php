<?php
namespace PMVC\PlugIn\algolia;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\algolia';

\PMVC\initPlugin(['guid'=>null]);
\PMVC\l(__DIR__.'/src/BaseAlgolia.php');

/**
 * @parameters string ALGOLIA_APP
 * @parameters string ALGOLIA_KEY
 */
class algolia extends \IdOfThings\GetDb
{
    public function init()
    {
        if (!isset($this['ALGOLIA_APP'])) {
            $this['ALGOLIA_APP'] = \PMVC\getOption('ALGOLIA_APP');
        }
        if (!isset($this['ALGOLIA_KEY'])) {
            $this['ALGOLIA_KEY'] = \PMVC\getOption('ALGOLIA_KEY');
        }
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
        return 'https://'.$this['ALGOLIA_APP'].'.algolia.net/1';
    }

    public function request($url, $params=[])
    {
        $respond = null;
        $url->set($this->getBaseUrl());
        $header = [
            'X-Algolia-Application-Id: '.$this['ALGOLIA_APP'],
            'X-Algolia-API-Key: '.$this['ALGOLIA_KEY']
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

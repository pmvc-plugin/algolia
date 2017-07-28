<?php
namespace PMVC\PlugIn\algolia;
use PHPUnit_Framework_TestCase;

\PMVC\Load::plug();
\PMVC\addPlugInFolders(['../']);

class AlgoliaTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'algolia';

    function setup()
    {
        \PMVC\unplug($this->_plug);
        \PMVC\plug($this->_plug, ['app'=>'fakeApp', 'key'=>'fakeKey']);
    }

    function testPlugin()
    {
        ob_start();
        print_r(\PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

    function testConnect()
    {
        $algo = \PMVC\plug($this->_plug); 
        $db = $algo->getDb('parking');
        //$db[]=['objectID'=>'def'];
        //$db[]='abc';
        $db->search('d +ef');
    }

    function testIsset()
    {
        \PMVC\unplug('curl');
        $curl = \PMVC\plug('curl', [
            _CLASS => __NAMESPACE__.'\fakeCurl'
        ]);
        $curl['r'] = [
            'code'=>200
        ];
        $algo = \PMVC\plug($this->_plug); 
        $db = $algo->getDb('parking');
        $this->assertTrue(isset($db['fake']));
        $curl['r'] = [
            'code'=>404
        ];
        $this->assertFalse(isset($db['fake']));
    }
}

class fakeCurl extends \PMVC\PlugIn
{
    function get($url, $callback)
    {
        $r = array_replace(
            [
                'header'=>[],
                'body'=>[],
                'code'=>200
            ],
            $this['r']
        );
        call_user_func($callback,(object)$r);
        return $this['this'];
    }

    function set($params)
    {

    }

    function process()
    {

    }
}

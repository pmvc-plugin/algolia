<?php
namespace PMVC\PlugIn\algolia;

use PMVC\TestCase;


class AlgoliaTest extends TestCase
{
    private $_plug = 'algolia';

    function pmvc_setup()
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
        $this->haveString($this->_plug,$output);
    }

    function testConnect()
    {
        $algo = \PMVC\plug($this->_plug); 
        $db = $algo->getModel('parking');
        $res = $db->search('d +ef');
        $this->assertTrue(!empty($res));
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
        $db = $algo->getModel('parking');
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

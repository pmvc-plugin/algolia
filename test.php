<?php
PMVC\Load::plug(['dotenv'=>null]);
PMVC\addPlugInFolders(['../']);
\PMVC\plug('dotenv',[
    \PMVC\PlugIn\dotenv\EnvFile=>'./.env'
])->init();
class AlgoliaTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'algolia';
    function testPlugin()
    {
        ob_start();
        print_r(PMVC\plug($this->_plug));
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
        $algo = \PMVC\plug($this->_plug); 
        $db = $algo->getDb('parking');
        var_dump(isset($db['2016316595370088911']));
    }

}

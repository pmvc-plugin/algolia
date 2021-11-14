<?php
include_once('./vendor/autoload.php');

\PMVC\Load::plug(['dotenv'=>['./.env']]);
\PMVC\addPlugInFolders(['../']);

$algo = \PMVC\plug('algolia');
$park = $algo->getModel('parking');

$result = $park->search('"'.\PMVC\getOption('QUERY').'"',[
    'minProximity'=>1,
    'minWordSizefor1Typo'=>1,
    'advancedSyntax'=>true,
    'attributes'=>'doc.name,doc.info',
    'attributesToHighlight'=>'doc.name',
    'restrictSearchableAttributes'=>'doc.name',
    'disableTypoToleranceOnAttributes'=>'doc.name',
    'queryType'=>'prefixNone',
    'distinct'=>true,
    'typoTolerance'=>'false',
    'removeStopWords'=>false
]);

var_dump($result);



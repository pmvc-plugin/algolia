<?php
PMVC\Load::plug(['dotenv'=>null]);
PMVC\addPlugInFolders(['../']);
\PMVC\plug('dotenv',[
    \PMVC\PlugIn\dotenv\EnvFile=>'./.env'
])->init();

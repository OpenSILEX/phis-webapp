<?php

//**********************************************************************************************
//                                        config.php
//
// Author(s): Morgane VIDAL, Alexandre MAIRIN, Isabelle NEMBROT, Anne TIREAU
// PHIS-M3P version 1.0
// Copyright © - INRA - 2015
// Creation date: novembre 2014
// Contact:i.nembrot@laposte.net, anne.tireau@supagro.inra.fr, pascal.neveu@supagro.inra.fr
// Last modification date: March, 2017
// Subject: parameters for global configuration
// config :
// - Yii2 configuration file
//***********************************************************************************************
class config {
    
    public static function path() {

        $appli = 'phis-webapp';
        $hostname = 'localhost';
        
        $basePath = $appli.'/web';
        
        return [
            'appli' => $appli,
            'baseIndexURL' => 'http://'.$hostname.'/'.$basePath.'/index.php',
            'baseImageURL' => 'http://'.$hostname.'/'.$basePath.'/images/',
            'baseRoutURL' => 'http://'.$hostname.'/Routput/',
            'baseIndexPath' => '/'.$basePath.'/index.php',
            'basePath' => '/'.$basePath.'/',
            'hostnameURL' => 'http://'.$hostname.'/'.$appli,
            'documentsUrl' => '../web/documents/',
            'ocpuServer' => 'http://opensilex.org:8004/ocpu/',
            //Concepts, relations uri
            'cVariable' => 'http://www.opensilex.org/vocabulary/oeso#Variable',
            'cTrait' => 'http://www.opensilex.org/vocabulary/oeso#Trait',
            'cMethod' => 'http://www.opensilex.org/vocabulary/oeso#Method',
            'cUnit' => 'http://www.opensilex.org/vocabulary/oeso#Unit',
            'rExactMatch' => 'http://www.w3.org/2008/05/skos#exactMatch',
            'rCloseMatch' => 'http://www.w3.org/2008/05/skos#closeMatch',
            'rNarrower' => 'http://www.w3.org/2008/05/skos#narrower',
            'rBroader' => 'http://www.w3.org/2008/05/skos#broader'
        ];
    }
}

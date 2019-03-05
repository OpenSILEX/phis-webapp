<?php

//**********************************************************************************************
//                                       index.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: March 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  March, 2017
// Subject: index du site
//***********************************************************************************************
require_once('./config/config.php');

$params = config::path();
?>

<html>
    <head>
        <title>OpenSILEX</title>
        <meta http-equiv="refresh" content="0;URL=<?= $params['baseIndexURL'] ?>">
    </head>
    <body></body>
</html>
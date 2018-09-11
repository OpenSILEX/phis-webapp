<?php
//******************************************************************************
//                                       SiteMessages.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 10 Sept, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\components\helpers;

/**
 * SiteMessages a class which defines errors messages
 * @author Arnaud Charleroy<arnaud.charleroy@inra.fr>
 */
class SiteMessages {
    // Page routes
    CONST SITE_ERROR_PAGE_ROUTE = '/site/error';
    CONST SITE_WARNING_PAGE_ROUTE = '/site/warning';
    // Page variables
    CONST SITE_PAGE_NAME = "name";
    CONST SITE_PAGE_MESSAGE = "message";
    // Page messages
    CONST INTERNAL_ERROR = 'Internal Error';
    CONST CANT_FETCH_FILE = 'Can\'t fetch the file';
    CONST CANT_FETCH_FILE_AQUI_SESS = self::CANT_FETCH_FILE . '.No file of the required type is linked with your installation.'
            . ' Add a document of the type AcquisitionSessionUAVDocument or AcquisitionSessionPhenomobileDocument'
            . ' to your installation from the Infrastructures menu.';
    CONST CANT_READ_FILE = 'Can\'t read the file';
    CONST NOT_CONNECTED = 'Not connected';
    CONST ERROR_WHILE_FETCHING_DATA = "Error while fetching data";
    CONST CANT_SEND_FILE = 'Error occured when sending the file';
}

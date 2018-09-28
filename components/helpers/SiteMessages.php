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
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class SiteMessages {
    // Page routes
    const SITE_ERROR_PAGE_ROUTE = '/site/error';
    const SITE_WARNING_PAGE_ROUTE = '/site/warning';
    // Page variables
    const SITE_PAGE_NAME = "name";
    const SITE_PAGE_MESSAGE = "message";
    // Page messages
    const INTERNAL_ERROR = 'Internal Error';
    const CANT_FETCH_FILE = 'Can\'t fetch the file';
    const CANT_FETCH_FILE_AQUI_SESS = self::CANT_FETCH_FILE . '. No file of the required type is linked with your installation.'
            . ' Add a document of the type AcquisitionSessionUAVDocument or AcquisitionSessionPhenomobileDocument'
            . ' to your installation from the Infrastructures menu.';
    const CANT_READ_FILE = 'Can\'t read the file';
    const NOT_CONNECTED = 'Not connected';
    const ERROR_WHILE_FETCHING_DATA = "Error while fetching data";
    const CANT_SEND_FILE = 'Error occured when sending the file';
}

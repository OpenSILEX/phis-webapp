<?php

//******************************************************************************
//                                       WSConstants.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 16 févr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  16 févr. 2018
// Subject: constants related to the web services exchanges
//******************************************************************************

namespace app\models\wsModels;

class WSConstants {
    
    // result
    const RESULT = 'result';

    // token
    const TOKEN = 'Invalid token';
    const ACCESS_TOKEN = 'access_token';
    const TOKEN_EXPIRES_IN = 'expires_in';
    const TOKEN_COOKIE_TIMEOUT = 'tokenTimeout';
    
    // matadata
    const METADATA = 'metadata';
    const EXCEPTION = 'exception';
    const DETAILS = 'details';
    const DATA_FILES = 'datafiles';
    const LANG = "language";
    const STATUS = "status";
    
    // pagination
    const PAGINATION = 'pagination';
    const TOTAL_PAGES = 'totalPages';
    const TOTAL_COUNT = 'totalCount';
    const CURRENT_PAGE = 'currentPage';
    const PAGE_SIZE = 'pageSize';
    const PAGE = "page";
    
    // data
    const DATA = 'data';
}

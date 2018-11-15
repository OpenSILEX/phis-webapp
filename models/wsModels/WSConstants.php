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

    const ACCESS_TOKEN = 'access_token';
    const DATA = 'data';
    const METADATA = 'metadata';
    const PAGINATION = 'pagination';
    const TOTAL_PAGES = 'totalPages';
    const TOTAL_COUNT = 'totalCount';
    const CURRENT_PAGE = 'currentPage';
    const PAGE_SIZE = 'pageSize';
    const PAGE = "page";
    const TOKEN = 'Invalid token';
    const RESULT = 'result';
    const DATA_FILES = 'datafiles';
    const LANG = "language";
    const STATUS = "status";
}

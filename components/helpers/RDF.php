<?php

//******************************************************************************
//                                       RDF.php
//
// Author(s): Arnaud Charleroy <arnaud.charleroy@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 july 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 july 2018
// Subject: A helper used to format RDF
//******************************************************************************

namespace app\components\helpers;

use Yii;
use app\models\yiiModels\YiiVocabularyModel;

/**
 * A helper used to format RDF
 * @author Arnaud Charleroy<arnaud.charleroy@inra.fr>
 */
class RDF {

    const NAMESPACES_SESSION_LABEL = "namespaces_session";

    /**
     * Static public function to shorten an uri by a prefix table
     * @author Guilhem Heinrich
     * @param string $uri The uri to shorten
     *
     * @return string The shortened uri if possible
     * @see    completeUri()
     * @see    $prefix
     */
    public static function prettyUri($uri, $removePrefix = false) {
        $shortenUri = $uri;
        foreach (RDF::getNamespaces() as $pkey => $pvalue) {
            $shortenUri = str_replace($pvalue, $pkey . ':', $uri);
            // Break to assure we only replace once
            if ($shortenUri != $uri) {
                break;
            }
        }
        if ($removePrefix) {
            $shortenUri = explode(':', $shortenUri)[1];
        }
        return $shortenUri;
    }

    /**
     * Return triplestore namespace instances list.
     * Format [ prefix : namespace]
     * e.g. ["oa" : "http://www.w3.org/ns/oa#"]
     * @return list of triplestore namespaces 
     */
    public static function getNamespaces() {
        // Use session to prevent multiple triplestore calls
        if (isset(Yii::$app->session[RDF::NAMESPACES_SESSION_LABEL]) && !empty(Yii::$app->session[RDF::NAMESPACES_SESSION_LABEL])) {
            return Yii::$app->session[RDF::NAMESPACES_SESSION_LABEL];
        }

        $vocabularyModel = new YiiVocabularyModel();
        $requestRes = $vocabularyModel->getNamespaces(Yii::$app->session['access_token'], ["pageSize" => 100]);
        if ($requestRes) {
            Yii::$app->session[RDF::NAMESPACES_SESSION_LABEL] = $vocabularyModel->namespaces;
            return Yii::$app->session[RDF::NAMESPACES_SESSION_LABEL];
        } else {
            return $requestRes;
        }
    }

}

<?php

//******************************************************************************
//                                       VectorController.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 avr. 2018
// Subject:implements the CRUD actions for the Vector model
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

/**
 * CRUD actions for 
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiVectorModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class AcquisitionSessionController extends Controller {

    /**
     * define the behaviors
     * @return array
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * generated the vector creation page
     * @return mixed
     */
    public function actionGenerate() {
        $existingFilePath = Yii::getAlias('@webroot/documents/AcquisitionSessionFiles/Meta_session_UAV_1.7.xlsx');
        $newFilePath = Yii::getAlias('@webroot/documents/AcquisitionSessionFiles/Meta_session_UAV_1.7_test.xlsx');
       
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//        $reader->setLoadSheetsOnly(["HiddenPhis"]);
        $spreadsheet = $reader->load($existingFilePath);
        $spreadsheet->setActiveSheetIndexByName("HiddenPhis");
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'Hello World !');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($newFilePath);
        exit;
    }
    

}

<?php

//**********************************************************************************************
//                                       DatasetController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  January, 15 2018 - creation with multiple variables 
//                          in csv file.
// Subject: implements the CRUD actions for WSDatasetModel
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

use Exception;
use app\models\yiiModels\YiiDocumentModel;
use app\models\yiiModels\VariableSearch;
use openSILEX\handsontablePHP\adapter\HandsontableSimple;
use openSILEX\handsontablePHP\classes\ColumnConfig;

require_once '../config/config.php';

/**
 * CRUD actions for YiiDatasetModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiDatasetModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DatasetController extends Controller {
    
    //SILEX:TODO
    //create a global configuration file for the csv files
    //\SILEX:TODO
    
    const DELIM_CSV = ";";
    const AGRONOMICAL_OBJECT_URI = "ScientificObjectURI";
    const DATE = "Date";
    const VALUE = "Value";
    
    const ERRORS_MISSING_COLUMN = "Missing Columns";
    const ERRORS_ROWS = "Rows";
    const ERRORS_LINE = "Line";
    const ERRORS_COLUMN = "Column";
    const ERRORS_MESSAGE = "Message";


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
     * transform a csv in JSON
     * @param array $csvContent the csv content to transform in json
     * @return string the csv content in json. Unescape the slashes in the csv
     */
    private function csvToArray($csvContent) {
        $arrayCsvContent = [];
        foreach($csvContent as $line) {
            $arrayCsvContent[] = str_getcsv($line, DatasetController::DELIM_CSV);
        }
        return $arrayCsvContent;
    }
    
    /**
     * check by calling web service if an agronomical object exist
     * @param string $agronomicalObjectURI
     * @return boolean 
     */
    private function existAgronomicalObject($agronomicalObjectURI) {
        $agronomicalObjectModel = new \app\models\yiiModels\YiiScientificObjectModel();
        $agronomicalObject = $agronomicalObjectModel->find(Yii::$app->session['access_token'], ["uri" => $agronomicalObjectURI]);
         
        return (count($agronomicalObject) > 0);
    }
    
    /**
     * 
     * @param string $date
     * @return boolean true if date has the format Y-m-d
     */
    private function isDateOk($date) {
       $dt = \DateTime::createFromFormat("Y-m-d", $date);
       return $dt !== false && !array_sum($dt->getLastErrors());
    }
    
    /**
     * 
     * @param array $columns
     * @param array $expectedVariables string list of the variables columns 
     *                                 expected. 
     * @return array list of the missing columns with correspondances.
     *                  [0] => DatasetController::AGRONOMICAL_OBJECT_URI  
     */
    private function getMissingColumns($columns, $expectedVariables) {
        $missingColumns = null;
        
        //AGRONOMICAL_OBJECT_URI column
        if (!in_array(DatasetController::AGRONOMICAL_OBJECT_URI, $columns)) {
            $missingColumns[] = DatasetController::AGRONOMICAL_OBJECT_URI; 
        }
        
        //DATE column
        if (!in_array(DatasetController::DATE, $columns)) {
            $missingColumns[] = DatasetController::DATE;
        }
        
        //Variables columns
        foreach ($expectedVariables as $expectedVariable) {
            if (!in_array($expectedVariable, $columns)) {
                $missingColumns[] = $expectedVariable;
            }
        }
        
        return $missingColumns;
    }
    
    /**
     * 
     * @param array $csvHeader csv header (with columns names. Expected format:
     *                         [0] => AGRONOMICAL_OBJECT_URI
     * @param array $expectedVariables list of the expected variables of the file
     *                                 expected format : 
     *                                 [0] => "variable_label"
     * @return array the list of the columns to manipulate. Ignore the columns 
     *               which won't be used. Return format : 
     *               [0] => AGRONOMICAL_OBJECT_URI,
     *               [3] => "variable label"
     */
    private function getColumnsCorrespondences($csvHeader, $expectedVariables) {
        $columnsCorrepondences = [];
        for ($i = 0; $i < count($csvHeader); $i++) {
            if ($csvHeader[$i] === DatasetController::AGRONOMICAL_OBJECT_URI) {
                $columnsCorrepondences[$i] = DatasetController::AGRONOMICAL_OBJECT_URI;
            } else if ($csvHeader[$i] === DatasetController::DATE) {
                $columnsCorrepondences[$i] = DatasetController::DATE;
            } else if (in_array($csvHeader[$i], $expectedVariables)) {
                $columnsCorrepondences[$i] = $csvHeader[$i];
            }
        }
        
        return $columnsCorrepondences;
    }
    
    /**
     * 
     * @param array $csvContent content of the csv file given by the client, 
     *                          with the header row. 
     *                          Expected format : each row is a string which
     *                          values are separated by ";". 
     *                          [0] => "http://ao1;2016-06-02;0.5"
     * @return array list of the errors founded in the file 
     */
    private function getRowsErrors($csvContent, $columns) {
        $rowsErrors = null;
        $agronomicalObjects = null; //array ["agronomicalObjectURI"] => boolean.
         //true if the agronomical object exist, false if it does not exist
        for ($i = 0; $i < count($csvContent); $i++) {
            $row = str_getcsv($csvContent[$i], DatasetController::DELIM_CSV);
            
            //check row errors
            foreach ($row as $columnNumber => $value) {
                if (isset($columns[$columnNumber])) {
                $columnName = $columns[$columnNumber];
                    //if unknown agronomical object uri
                    if ($columnName === DatasetController::AGRONOMICAL_OBJECT_URI) { 
                        if (!isset($agronomicalObjects[$value])) { //If the existance of the agronomical object has never been tested
                            $agronomicalObjects[$value] = $this->existAgronomicalObject($value);
                        }
                        
                        if (!$agronomicalObjects[$value]){ //the agronomical object does not exist
                            $error = [];
                            $error[DatasetController::ERRORS_LINE] = $i + 1; //+1 because header isn't in given data
                            $error[DatasetController::ERRORS_COLUMN] = $columnNumber;
                            $error[DatasetController::ERRORS_MESSAGE] = Yii::t('app/message', 'Unknown scientific object.');
                            $rowsErrors[] = $error;
                        }
                        
                    } else if ($columnName === DatasetController::DATE) { //if bad date format or empty date
                        if (!$this->isDateOk($value)) {
                            $error = null;
                            $error[DatasetController::ERRORS_LINE] = $i + 1;
                            $error[DatasetController::ERRORS_COLUMN] = $columnNumber;
                            $error[DatasetController::ERRORS_MESSAGE] = Yii::t('app/message', 'Bad date format, expected format : YYYY-MM-DD');
                            $rowsErrors[] = $error;
                        }
                    } else { //it is a variable, is it a double ?
                        $variableValue = str_replace(",", ".", $value);
                        if (!is_numeric($variableValue)) {
                            $error = null;
                            $error[DatasetController::ERRORS_LINE] = $i + 1;
                            $error[DatasetController::ERRORS_COLUMN] = $columnNumber;
                            $error[DatasetController::ERRORS_MESSAGE] = Yii::t('app/message', 'Bad value : float expected.');
                            $rowsErrors[] = $error;
                        }
                    }  
                }
            }
        }
        
        return $rowsErrors;
    }
        
    
    /**
     * check the csv content
     * @param array $csvContent content of the csv file given by the client
     * @return array errors : ["missing columns"] ["column name"] = column number
     *                        ["rows"] [] ["line"] ["column"] ["error"]
     *               null if no errors
     */
    private function getCSVErrors($csvContent, $expectedVariables) {
        $errors = null;
        
        $csvHeader = str_getcsv($csvContent[0], DatasetController::DELIM_CSV);
        //1. check header (columns names)
        $missingColumns = $this->getMissingColumns($csvHeader, $expectedVariables);
        if ($missingColumns !== null) {
            $errors[DatasetController::ERRORS_MISSING_COLUMN] = $missingColumns;
        }
        
        //2. get columns to manipulate
        $columnsCorrespondences = $this->getColumnsCorrespondences($csvHeader, $expectedVariables);
        
        //3. check rows
        $rowsErrors = $this->getRowsErrors(array_slice($csvContent, 1), $columnsCorrespondences);
        if ($rowsErrors !== null) {
            $errors[DatasetController::ERRORS_ROWS] = $rowsErrors;
        }
        
        return $errors;
    }
    
    /**
     * get the uri of the variable label given in the parameters
     * @param string $variableLabel
     * @return string the uri of the variable label
     */
    private function getVariableUri($variableLabel) {
        $variableSearchModel = new VariableSearch();
        $variableSearchModel->label = $variableLabel;
        $searchResult = $variableSearchModel->search(Yii::$app->session['access_token'], []);
        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                 throw new Exception("user must log in");
            } else {
                throw new Exception("error getting variable uri");
            }
        } else { 
            $models = $searchResult->getModels();
            //we assume that there is only one variable with the variable label. 
            if (isset($models[0])) {
                return $models[0]->uri;
            } else {
                return null;
            }
        }
    }
    
    /**
     * 
     * @param array $fileContent csv content
     * @param array $correspondances columns numbers, corresponding to the 
     *                               wanted columns 
     * @param array $datasetModel model to whom we add data
     * @param int $variableColumnNumber variable column number to be send
     */
    private function setDataFromFileContent($fileContent, $correspondances, $datasetModel, $variableColumnNumber) {
        $cpt = 0;
        $datasetModel->data = null;
        foreach ($fileContent as $row) {
            $dataset = str_getcsv($row, DatasetController::DELIM_CSV);
            if ($cpt === 0) {
                $var = $this->getVariableUri($dataset[$variableColumnNumber]);
                if ($var !== null) { //If the column name corresponds to a 
                    //known variable, we keep it. Else, the column is ignored 
                    //(it might be for example an alias column used by the user)
                    $datasetModel->variables = $var;
                } else {
                    break;
                }
            } else if ($dataset[$variableColumnNumber] !== "") {
                $data = null;
                $data["agronomicalObject"] = $dataset[array_search(DatasetController::AGRONOMICAL_OBJECT_URI, $correspondances)];

                $data["date"] = $dataset[array_search(DatasetController::DATE, $correspondances)];
                $data["value"] = str_replace(",", ".", $dataset[$variableColumnNumber]);
                $datasetModel->data[] = $data;
            }
            $cpt++;
        }        
    }
    
    /**
     * generates provenance URI, use the timestamp
     * @param YiiDatasetModel $datasetModel
     */
    private function generateAndSetProvenanceURI($datasetModel) {
        $year = date("Y");
        $datasetModel->uriProvenance = Yii::$app->params['baseURI'] .  $year . "/pv" . substr($year, 2, 3) . time();
    }
    
    /**
     * update the added documents to say that they are linked to an object
     * (unlinked -> linked)
     * @param YiiDatasetModel $datasetModel
     * @return boolean|string
     */
    private function updateDocumentsToLinked($datasetModel) {
        //SILEX:todo
        //Handle web service errors returns
        //\SILEX:todo
        $documentModel = new YiiDocumentModel(null, null);
        
        //1. script update
        if (isset($datasetModel->wasGeneratedBy) && $datasetModel->wasGeneratedBy !== "") {
            $documentModel->findByURI(Yii::$app->session['access_token'], $datasetModel->wasGeneratedBy);
            $documentModel->status = "linked";
            $dataToSend[] = $documentModel->attributesToArray();
        }
        
        //2. associated documents update
        if ($datasetModel->documentsURIs !== null && $datasetModel->documentsURIs !== "") {
            foreach ($datasetModel->documentsURIs as $documentsURIs) {
                foreach ($documentsURIs as $documentURI) {
                    $documentModel = new YiiDocumentModel(null, null);
                    $documentModel->findByURI(Yii::$app->session['access_token'], $documentURI);
                    $documentModel->status = "linked";
                    $dataToSend[] = $documentModel->attributesToArray();
                }
            }
        }
        
        if (isset($dataToSend)) {
            $requestRes = $documentModel->update(Yii::$app->session['access_token'], $dataToSend);

            if (is_string($requestRes) && $requestRes === "token") { //L'utilisateur doit se connecter
                return "token";
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
    
    /**
     * @return array contains the variables list. The key is the variable label 
     *               and the value is the variable uri
     */
    private function getVariablesListUriLabelToShow() {
        $variableModel = new \app\models\yiiModels\YiiVariableModel();
        $variables = $variableModel->getInstancesDefinitionsUrisAndLabel(Yii::$app->session['access_token']);     
        
        if ($variables !== null) {
            return $variables;
        } else {
            return null;
        }
    }
    
    /**
     * @return array contains the variables list. The key is the variable label 
     *               and the value is the variable label
     */
    private function getVariablesListLabelToShowFromVariableList($variables) {
        if ($variables !== null) {
            $variablesToReturn = [];
            foreach ($variables as $key => $value) {
                $variablesToReturn[$value] = $value;
            }
            return $variablesToReturn;
        } else {
            return null;
        }
    }
    
    /**
     * generate the csv file for the dataset creation action. The csv file is
     * generated with a column for each variable
     * @param array variables list of the variables to add to the 
     *                                file uri => alias
     * @return mixed
     */
    public function actionGenerateAndDownloadDatasetCreationFile() {
            $fileColumns[] = DatasetController::AGRONOMICAL_OBJECT_URI;
            $fileColumns[] = DatasetController::DATE;
            $variables = Yii::$app->request->post('variables');
            foreach ($variables as $variableAlias) {
                $fileColumns[] = $variableAlias;
            }

            $file = fopen('./documents/DatasetFiles/datasetTemplate.csv', 'w');
            fputcsv($file, $fileColumns, $delimiter = ";"); 
            fclose($file);
    }
    
    /**
     * 
     * @param array $fileContent the csv file content
     * @param array $correspondancesCSV the correspondances between file columns
     *                                  and wanted columns (with row numbers)
     * @param YiiDatasetModel $datasetModel the model to save
     * @param int $variableKey the key of the variable to save
     * @return string|array "token" if the user must log in
     *                      array of the query result else
     *                      
     */
    private function saveDataset($fileContent, $correspondancesCSV, $datasetModel, $variableKey) {
        $this->setDataFromFileContent($fileContent, $correspondancesCSV, $datasetModel, $variableKey);
        if ($datasetModel->data !== null) {
            if ($datasetModel->uriProvenance === null) {
                $this->generateAndSetProvenanceURI($datasetModel);
            }  else {
                $datasetModel->creationDate = null;
                $datasetModel->wasGeneratedBy = null;
                $datasetModel->wasGeneratedByDescription = null;
                $datasetModel->documentsURIs = null;
            }
            
            $forWebService = null;
            $forWebService[] = $datasetModel->attributesToArray();
            
            $requestRes = $datasetModel->insert(Yii::$app->session['access_token'], $forWebService);
            
            return $requestRes;
        }
        return null;
    }
    
    /**
     * 
     * @param array $csvErrors the errors founded. 
     * @param array $csvHeaders the header row of the csv file
     * @param array $correspondancesCSV the files columns corresponding to the wanted data
     * @return string the JavaScript code for the cells settings
     */
    private function getHandsontableCellsSettings($csvErrors, $csvHeaders, $correspondancesCSV) {
        $updateSettings = 'hot1.updateSettings({
                    cells: function(row, col, prop){
                       var cellProperties = {};
                       var cell = hot1.getCell(row,col);
                       cell.style.color = "black";';
        
        //2. Cells Errors    
        if (isset($csvErrors[DatasetController::ERRORS_ROWS])) {
            foreach ($csvErrors[DatasetController::ERRORS_ROWS] as $dataError) {
                $updateSettings .= 'if (row === ' . ($dataError[DatasetController::ERRORS_LINE] - 1) 
                                . ' && col === ' . $dataError[DatasetController::ERRORS_COLUMN] . ') {'
                        . 'cell.style.fontWeight = "bold";'
                        . 'cell.style.color = "red";'
                            . '}';
            }
        }
        
        //3. Ignored columns
        foreach ($csvHeaders as $csvColumnNumber => $csvColumnName) {
            if (!in_array($csvColumnName, $correspondancesCSV)) {
                $updateSettings .= 'if (col === ' . $csvColumnNumber . ' ) { '
                        . 'cell.style.background = "#F2F2F2";'
                        . '}';
            }
        }
        
        $updateSettings .= 'return cellProperties;'
                . '}' ;
        
        //4. Missing Columns headers 
        if (isset($csvErrors[DatasetController::ERRORS_MISSING_COLUMN])) {
            $updateSettings .= ',afterGetColHeader: function (col, th) {';
            foreach ($csvErrors[DatasetController::ERRORS_MISSING_COLUMN] as $key => $missingColumn) { 
                //$toAdd = 1 because missing columns keys begins at 0
                //         2 if there is also the errors rows columns 
                $updateSettings .= 'if (col === ' . ((count($csvHeaders) - 1) + $key + 1) . ' ) { '
                            . 'th.style.color = "red";'
                            . '}';
            }
            $updateSettings .= '}';
        }
        
        
        
        
        $updateSettings .= '});';
        return $updateSettings;
    }
    
    /**
     * generate an handsontable with the given data, each cell is readonly
     * @param array $arrayToDisplay in this array, the first row must be the 
     *                              headers
     * @return HandsontableSimple
     */
    private function generateHandsontableDataset($arrayToDisplay) {
        $handsontable = new HandsontableSimple();
        
        //Columns headers an readonly
        $header = $arrayToDisplay[0];
        //Add bold to headers
        $headerBold = null;
        foreach ($header as $column) {
            $headerBold[] = "<b>" . $column . "</b>";
        }
        
        $handsontable->setColHeaders($headerBold);
        $columnsDefinitions = [];
        foreach ($headerBold as $column) {
            $columnsDefinitions[] = new ColumnConfig([
                'readOnly' => true,
            ]);
        }
        $handsontable->setColumns($columnsDefinitions);
        
        //Add data
        $data = array_slice($arrayToDisplay, 1);
        $handsontable->setData($data);
        
        return $handsontable;
    }
    
    /**
     * 
     * @param array $arrayData
     * @param array $arrayRowsErrors the array with the errors for each cell. 
     *                          Expected format : 
     *                           [0][ERRORS_LINE] = 0
     *                           [0][ERRORS_COLUMN] = 0
     *                           [0][ERRORS_MESSAGE] = error message
     * @return array the given $arrayData with a new column corresponding to the
     *               errors messages column
     */
    private function addColumnErrorToArray($arrayData, $arrayRowsErrors) {
        for ($i = 0; $i < count($arrayData); $i++) {
            $errorLine = "";
            if ($i === 0) {
                $errorLine = "Errors";
            } else {
                foreach ($arrayRowsErrors as $error) {
                    if ($error[DatasetController::ERRORS_LINE] === $i) {
                        $errorLine .= $error[DatasetController::ERRORS_MESSAGE] . " ";
                    }
                }
            }

            $arrayData[$i][] = $errorLine;           
        }
            
        return $arrayData;
    }
    
    /**
     * 
     * @param array $arrayData
     * @param array $arrayMissingColumns the array with the list of the missing
     *                                   columns. Expected format : 
     *                                   [0] = AGRONOMICAL_OBJECT_URI
     * @return array the given $arrayData with news columns corresponding to the
     *               missing columns with empty values
     */
    private function addMissingColumnsToArray($arrayData, $arrayMissingColumns) {
        foreach ($arrayMissingColumns as $missingColumn) {
            for ($i = 0; $i < count($arrayData); $i++) {
                $cellValue = "Missing value";
                if ($i === 0) {
                    $cellValue = $missingColumn;
                }
                $arrayData[$i][] = $cellValue;
            }
        }
        return $arrayData;
    }
    
    /**
     * 
     * @param array $arrayData
     * @param array $arrayErrors the array with the errors. Expected format : 
     *                           [ERRORS_MISSING_COLUMNS][0] = AGRONOMICAL_OBJECT_URI
     *                           [...]
     *                           [ERRORS_MISSING_COLUMNS][3] = "Variable label"
     *                           [ERRORS_ROWS][0][ERRORS_LINE] = 0
     *                           [ERRORS_ROWS][0][ERRORS_COLUMN] = 0
     *                           [ERRORS_ROWS][0][ERRORS_MESSAGE] = error message
     * @return array the $arrayData with a new column corresponding to the 
     *               errors founded in the data and new columns corresponding to
     *               the missing columns if needed
     *               e.g. AgronomicalObjectUri, variableLabel, MessageError, MissingVariable, MissingDate
     */
    private function addColumnErrorsAndMissingToArray($arrayData, $arrayErrors) {
        
        //1. Add missing columns
        if (isset($arrayErrors[DatasetController::ERRORS_MISSING_COLUMN])) {
            $arrayData = $this->addMissingColumnsToArray($arrayData, $arrayErrors[DatasetController::ERRORS_MISSING_COLUMN]);
        }
        
        //2. add columnError
        if (isset($arrayErrors[DatasetController::ERRORS_ROWS])) {
            $arrayData = $this->addColumnErrorToArray($arrayData, $arrayErrors[DatasetController::ERRORS_ROWS]);
        }
        
        return $arrayData;
    }
    
    private function getNumberInsertedData($arrayData, $variablesList, $correspondancesCSV) {
        $numberInsertedData = 0;
        foreach ($arrayData as $row) {
            foreach ($variablesList as $variable) {
                $keyVariable = array_search($variable, $correspondancesCSV);
                if ($row[$keyVariable] !== "" 
                        && $row[$keyVariable] !== null
                        && $row[$keyVariable] !== $variable) {
                    $numberInsertedData++;
                }
            }
        }
        
        return $numberInsertedData;
    }
    
    /**
     * register the dataset with the associated provenance
     * @return mixed
     */
    public function actionCreate() { 
        $datasetModel = new \app\models\yiiModels\YiiDatasetModel();
        $variablesModel = new \app\models\yiiModels\YiiVariableModel();
        
        $variables = $variablesModel->getInstancesDefinitionsUrisAndLabel(Yii::$app->session['access_token']);
        $this->view->params["variables"] = $this->getVariablesListLabelToShowFromVariableList($variables);
        
        //If the form is complete, register data
        if ($datasetModel->load(Yii::$app->request->post())) {
            //csv
            $document = UploadedFile::getInstance($datasetModel, 'file');
            $serverFilePath = \config::path()['documentsUrl'] . "DatasetFiles/" . $document->name;
            $document->saveAs($serverFilePath);
            
            //read csv file and save data
            $fileContent = str_getcsv(file_get_contents($serverFilePath), "\n");
            
            unlink($serverFilePath);       
            
            $givenVariables = $datasetModel->variables;
            $csvErrors = $this->getCSVErrors($fileContent, $givenVariables);
            $correspondancesCSV = $this->getColumnsCorrespondences(str_getcsv($fileContent[0], DatasetController::DELIM_CSV), $givenVariables);
            
            $arrayData = $this->csvToArray($fileContent);
            
            if ($csvErrors !== null) {
                $csvHeaders = str_getcsv($fileContent[0], DatasetController::DELIM_CSV);
                
                $arrayDataWithErrors = $this->addColumnErrorsAndMissingToArray($arrayData, $csvErrors);
                return $this->render('create', [
                       'model' => $datasetModel,
                       'handsontable' => $this->generateHandsontableDataset($arrayDataWithErrors),
                       'handsontableErrorsCellsSettings' => $this->getHandsontableCellsSettings($csvErrors, $csvHeaders, $correspondancesCSV)
                ]);
            } else {
                $firstInsert = true;
                foreach ($correspondancesCSV as $key => $value) {
                    if ($value !== DatasetController::AGRONOMICAL_OBJECT_URI
                        && $value !== DatasetController::DATE) { //it is a variable column
                        $requestRes = $this->saveDataset($fileContent, $correspondancesCSV, $datasetModel, $key);
                        
                        if (is_string($requestRes)) {//Request error
                            return $this->render('/site/error', [
                                'name' => Yii::t('app/messages','Internal error'),
                                'message' => $requestRes]);
                        } else if (is_array($requestRes) && isset($requestRes["token"])) { //user must log in
                            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
                        } else { //data has been saved                            
                            //say that the documents are linked (unlinked -> linked)
                            if ($firstInsert) {
                                $update = $this->updateDocumentsToLinked($datasetModel);
                                if ($update === "token") {
                                    return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
                                }
                                $firstInsert = false;
                            }
                        }
                    }
                }
                return $this->render('_form_dataset_created', [
                    'model' => $datasetModel,
                    'handsontable' => $this->generateHandsontableDataset($arrayData),
                    'intertedDataNumber' => $this->getNumberInsertedData($arrayData, $givenVariables, $correspondancesCSV)
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $datasetModel,
            ]);
        }
    }
    
    /**
     * search datasets (by variable, date start, end date). Used in the
     * experiment map visualisation (layer view)
     * @return mixed
     */
    public function actionSearchFromLayer() {
        $searchModel = new \app\models\yiiModels\DatasetSearch();
        $this->view->params["variables"] = $this->getVariablesListUriLabelToShow();
        if ($searchModel->load(Yii::$app->request->post())) {
            $searchModel->agronomicalObjects = Yii::$app->request->post()["agronomicalObjects"];
           
            $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->post());        
                  
            /* Build array for highChart
             * e.g : 
             * {
                "variable": "http:\/\/www.phenome-fppn.fr\/phenovia\/id\/variable\/v0000001",
                "agronomicalObject": [
                        "uri": "http:\/\/www.phenome-fppn.fr\/phenovia\/2017\/o1028919",
                        "data": [["1,874809","2015-02-10"],
             *                   ["2,313261","2015-03-15"]
                        ]
                }]
             * }
             */
            
            $toReturn["variable"] = $searchModel->variables;
            foreach ($searchResult->getModels()[0] as $model) {
                $agronomicalObject = null;
                $agronomicalObject["uri"] = $model->agronomicalObject;
                foreach ($model->data as $data) {
                    $dataToSave = null;
                    $dataToSave[] = (strtotime($data->date))*1000;
                    $dataToSave[] = doubleval($data->value);
                    $agronomicalObject["data"][]= $dataToSave;
                }
                $toReturn["agronomicalObject"][] = $agronomicalObject;
            }
            
            return $this->renderAjax('_form_dataset_graph', [
                        'model' => $searchModel,
                        'data' => $toReturn,
                   ]);
        } else {
            return $this->renderAjax('_form_dataset_graph', [
                        'model' => $searchModel
                   ]);
        }
    }
} 
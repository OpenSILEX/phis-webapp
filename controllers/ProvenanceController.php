<?php
namespace app\controllers;

require_once '../config/config.php';

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\wsModels\WSProvenanceModel;
use app\models\yiiModels\YiiDocumentModel;
use app\models\wsModels\WSConstants;
use app\models\yiiModels\YiiConcernedItemModel;

/**
 * CRUD actions for YiiDataModel
 * 
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiDataModel
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class ProvenanceController extends Controller {
    
    /**
     * Provenance config namespaces
     */
    const PROVENANCE_PARAMS_VALUES = "provenanceNamespaces";
    
    
    /**
     * Define the behaviors
     * 
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
     * Create a provenance from post data with documents Uri associated
     * [
     *  provenance : { label, comment, metadata:{ ... } },
     *  documents : { uri1,uri2}
     * ]
     */
    public function actionAjaxCreateProvenanceFromDataset(){

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        
        $documents = [];
        
        $provenance = $data["provenance"];
        if(isset($data["documents"])){
            $documents = $data["documents"];
        }
        $provenanceUri = $this->createProvenance(
                            $provenance['label'],
                            $provenance['comment'],
                            $provenance['sensingDevices'],
                            $provenance['agents']
                    );
        
        $this->linkDocumentsToProvenance($provenanceUri, $documents);
        
        if($provenanceUri != false){
            return $provenanceUri;
        }
        return false;
    }
    
    /**
     * Return an array with provenance list with all characteristics
     * and provenance label mapped with provenance uri
     * @return array
     */
    public function actionAjaxGetProvenancesSelectList(){
        $token = Yii::$app->session[WSConstants::ACCESS_TOKEN];

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $provenanceService = new WSProvenanceModel();

        $provenances = $this->mapProvenancesByUri($provenanceService->getAllProvenances($token));
        
        foreach ($provenances as $uri => $provenance) {
            $provenancesArray[$uri] = $provenance->label . " (" . $uri . ")";
        }
        $result['provenances'] = $provenances;
        $result['provenancesByUri'] = $provenancesArray;
        return $result;
    }
    
    
    /**
     * Create provenance from an alias and a comment
     * @param type $alias label of the provenance
     * @param type $comment comment linked to the provenance
     * @param type $sensingDevice uri of the sensor
     * @param String $agent uri of the agent
     * @return boolean
     */
    private function createProvenance($alias, $comment,$sensingDevice = null, $agent =null) {
        $provenanceService = new WSProvenanceModel();
        $date = new \DateTime();
        $createdDate = $date->format("Y-m-d\TH:i:sO");
        $metadata = [
            "namespaces" => Yii::$app->params[self::PROVENANCE_PARAMS_VALUES],
            "prov:Agent" =>[
                "oeso:SensingDevice" => [
                ],
                "oeso:Operator" => [
                ]
              ],
            ];
        if($sensingDevice != null){
            $metadata["prov:Agent"]["oeso:SensingDevice"] = $sensingDevice;
        }
        if($agent != null){
            $metadata["prov:Agent"]["oeso:Operator"] = $agent;
        }
        $provenanceUri = $provenanceService->createProvenance(
                Yii::$app->session['access_token'],
                $alias,
                $comment,
                $createdDate,
                $metadata
        );

        if (is_string($provenanceUri) && $provenanceUri != "token") {
            return $provenanceUri;
        } else {
            return false;
        }
    }
    
    /**
     * Create an associative array of the provenances objects indexed by their URI
     * @param type $provenances
     * @return array
     */
    private function mapProvenancesByUri($provenances) {
        $provenancesMap = [];
        if ($provenances !== null) {
            foreach ($provenances as $provenance) {
                $provenancesMap[$provenance->uri] = $provenance;
            }
        }

        return $provenancesMap;
    }
    
     /**
     * Link list of documents to the given provenance uri
     * (unlinked -> linked)
     * @param string $provenanceUri
     * @param array $documents
     * @return boolean
     */
    private function linkDocumentsToProvenance($provenanceUri, $documents) {
        $documentModel = new YiiDocumentModel(null, null);

        // associated documents update
        foreach ($documents as $documentURI) {
            $documentModel = new YiiDocumentModel(null, null);
            $documentModel->findByURI(Yii::$app->session['access_token'], $documentURI);
            $documentModel->status = "linked";
            $concernedItem = new YiiConcernedItemModel();
            $concernedItem->uri = $provenanceUri;
            $concernedItem->rdfType = Yii::$app->params["Provenance"];
            $documentModel->concernedItems = [$concernedItem];
            $dataToSend[] = $documentModel->attributesToArray();
        }

        if (isset($dataToSend)) {
            $requestRes = $documentModel->update(Yii::$app->session['access_token'], $dataToSend);

            if (is_string($requestRes) && $requestRes === "token") {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}
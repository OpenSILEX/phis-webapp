<?php
//******************************************************************************
//                         YiiAnnotationModel.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 9 Jul, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use app\models\wsModels\WSAnnotationModel;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSConstants;
use Yii;

/**
 * The Yii model for the Annotation. Used with web services.
 * Implements a customized Active Record (WSActiveRecord, for the web services 
 * access)
 * @see app\models\wsModels\WSAnnotationModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr> 
 * @author Arnaud Charleroy <arnaud.charleroy@.fr>
 */
class YiiAnnotationModel extends WSActiveRecord {

    /**
     * Label name used in annotation view
     */
    const LABEL = "Annotation";

    /**
     * Uri of the annotation
     * @example http://www.phenome-fppn.fr/platform/id/annotation/3ce85bf7-1d99-4831-9c13-4d7ebdafe1d6
     * @var string
     */
    public $uri;

    const URI = "uri";
    const URI_LABEL = "URI";

    /**
     * Creation date of the annotation
     * @example 2018-06-25 15:13:59+0200
     * @var string
     */
    public $creationDate;

    const CREATION_DATE = "creationDate";
    const CREATION_DATE_LABEL = "Date of Annotation";

    /**
     * Creator of the annotation
     * @example http://www.phenome-fppn.fr/diaphen/id/agent/acharleroy
     * @var string
     */
    public $creator;

    const CREATOR = "creator";
    const CREATOR_LABEL = "Creator";

    /**
     * Purpose of the annotation
     * @example http://www.w3.org/ns/oa#commenting
     * @var string
     */
    public $motivatedBy;

    const MOTIVATED_BY = "motivatedBy";
    const MOTIVATED_BY_LABEL = "Motivated by";

    /**
     * Description of the annotation
     * @example http://www.w3.org/ns/oa#commenting
     * @var string
     */
    public $comments;

    const COMMENTS = "comments";
    const COMMENTS_LABEL = "Description";

    /**
     * Target associate to this annotation 
     * @example http://www.phenome-fppn.fr/phenovia/2017/o1032481
     * @var string
     */
    public $targets;

    const TARGETS = "targets";
    const TARGETS_LABEL = "Targets";
    const TARGET_SEARCH_LABEL = "target";

    public function __construct($pageSize = null, $page = null) {
        $date = new \DateTime();
        $this->creationDate = $date->format(\DateTime::ATOM);
        $this->wsModel = new WSAnnotationModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [[self::URI, self::CREATOR, self::MOTIVATED_BY, self::COMMENTS, self::TARGETS], 'required'],
            [[self::URI, self::CREATOR, self::MOTIVATED_BY, self::COMMENTS, self::TARGETS], 'safe'],
            [[self::COMMENTS], 'string'],
            [[self::URI, self::CREATOR, self::TARGETS], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            self::URI => self::URI_LABEL,
            self::CREATOR => Yii::t('app', self::CREATOR_LABEL),
            self::MOTIVATED_BY => Yii::t('app', self::MOTIVATED_BY_LABEL),
            self::COMMENTS => Yii::t('app', self::COMMENTS_LABEL),
            self::TARGETS => Yii::t('app', self::TARGETS_LABEL)
        ];
    }

    /**
     * Permit to fill model parameters from webservice data array 
     * @param array $array key => value with annotation data value
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[self::URI];
        $this->creator = $array[self::CREATOR];
        $this->comments = $array[self::COMMENTS];
        $this->motivatedBy = $array[self::MOTIVATED_BY];
        $this->targets = $array[self::TARGETS];
    }

    /**
     * @return array used to send to the webservice in order to create a new 
     * annotation. It is a public method in case that the user want to save 
     * these annotation data in multiple instances.
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[self::CREATOR] = $this->creator;
        $elementForWebService[self::MOTIVATED_BY] = $this->motivatedBy;
        $elementForWebService[self::CREATION_DATE] = $this->creationDate;
        // For now one target can be choose
        if (isset($this->targets) && !empty($this->targets)) {
            $elementForWebService[self::TARGETS] = $this->targets;
        }
        if (isset($this->comments) && !empty($this->comments)) {
            $elementForWebService[self::COMMENTS] = $this->comments;
        }
        return $elementForWebService;
    }

    /**
     * Find an annotation from a uri
     * @param string $sessionToken
     * @param string $uri
     * @return mixed the searched object if it exists or a message if not
     */
    public function findByURI($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
            $params[WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        if ($this->page !== null) {
            $params[WSConstants::PAGE] = $this->page;
        }

        $requestRes = $this->wsModel->getAnnotationByURI($sessionToken, $uri, $params);
        if (!is_string($requestRes)) {
            if (isset($requestRes[WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                $this->arrayToAttributes($requestRes);
                return true;
            }
        } else {
            return $requestRes;
        }
    }
}

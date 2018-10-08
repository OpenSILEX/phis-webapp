<?php

namespace app\models\wsModels;

include_once '../config/web_services.php';

class WSRadiometricTargetModel extends \openSILEX\guzzleClientPHP\WSModel {

    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "radiometricTargets");
    }
    
    public function getDetails($sessionToken, $uri) {
        $subService = "/" . urlencode($uri);
        $requestRes = $this->get($sessionToken, $subService, []);

        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }

}

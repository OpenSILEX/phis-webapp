<?php

return [
    // Global
    'platform' => 'Demo',
    'isDemo' => false,
    'demoLogin' => 'guest@opensilex.org',
    'demoPassword' => 'guest',
    'opensilex-webapp-type' => 'opensilex', //'opensilex' or 'phis' (used to adapt the view)
    'dateTimeFormatPhp' => 'Y-m-d H:i:sP',
    //SILEX:info
    //this param should be removed when the new version of data will be used instead of Dataset
    'baseURI' => 'http://www.opensilex.org/demo/',
    //\SILEX:info

    // Index
    'indexPageSize' => 20,

    // Forms
    'dateTimeFormatDateTimeRangePickerStandard' => 'Y-m-dTH:i:sZ',
    'dateTimeFormatDateTimePickerUserFriendly' => 'yyyy-mm-dd HH:ii:ss',
    'dateRangeSeparator' => ' - ',
    'textAreaRowsNumber' => 6,

    // Annotation
    'annotationWidgetPageSize' => 5,

    // Event
    'eventIndexNumberOfConcernedItemsToDisplay' => 3,
    'eventAnnotationWidgetPageSize' => 5,
    'eventWidgetPageSize' => 5,

    // Web service
    'webServicePageSizeMax' => 2097152,

    // ONTOLOGY -----------------------------------------------------------------
    //-------- Concepts
    'Actuator' => 'http://www.opensilex.org/vocabulary/oeso#Actuator',
    'Lens' => 'http://www.opensilex.org/vocabulary/oeso#Lens',
    'Plot' => 'http://www.opensilex.org/vocabulary/oeso#Plot',
    'Species' => 'http://www.opensilex.org/vocabulary/oeso#Species',
    'Variety' => 'http://www.opensilex.org/vocabulary/oeso#Variety',
    'Experiment' => 'http://www.opensilex.org/vocabulary/oeso#Experiment',
    'Project' => 'http://www.opensilex.org/vocabulary/oeso#Project',
    'Camera' => 'http://www.opensilex.org/vocabulary/oeso#Camera',
    'HemisphericalCamera' => 'http://www.opensilex.org/vocabulary/oeso#HemisphericalCamera',
    'HyperspectralCamera' => 'http://www.opensilex.org/vocabulary/oeso#HyperspectralCamera',
    'MultispectralCamera' => 'http://www.opensilex.org/vocabulary/oeso#MultispectralCamera',
    'RGBCamera' => 'http://www.opensilex.org/vocabulary/oeso#RGBCamera',
    'TIRCamera' => 'http://www.opensilex.org/vocabulary/oeso#TIRCamera',
    'LiDAR' => 'http://www.opensilex.org/vocabulary/oeso#LiDAR',
    'Spectrometer' => 'http://www.opensilex.org/vocabulary/oeso#Spectrometer',
    'Installation' => 'http://www.opensilex.org/vocabulary/oeso#Installation',
    'UAV' => 'http://www.opensilex.org/vocabulary/oeso#UAV',
    'AcquisitionSessionUAVDocument' => 'http://www.opensilex.org/vocabulary/oeso#AcquisitionSessionUAVDocument',
    'FieldRobot' => 'http://www.opensilex.org/vocabulary/oeso#FieldRobot',
    'AcquisitionSessionPhenomobileDocument' => 'http://www.opensilex.org/vocabulary/oeso#AcquisitionSessionPhenomobileDocument',

    'RadiometricTarget' => 'http://www.opensilex.org/vocabulary/oeso#RadiometricTarget',
    'SpectralHemisphericDirectionalReflectanceFile' => 'http://www.opensilex.org/vocabulary/oeso#SpectralHemisphericDirectionalReflectanceFile',

    'Infrastructure' => 'http://www.opensilex.org/vocabulary/oeso#Infrastructure',
    'LocalInfrastructure' => 'http://www.opensilex.org/vocabulary/oeso#LocalInfrastructure',
    'NationalInfrastructure' => 'http://www.opensilex.org/vocabulary/oeso#NationalInfrastructure',
    'EuropeanInfrastructure' => 'http://www.opensilex.org/vocabulary/oeso#EuropeanInfrastructure',
    'Installation' => 'http://www.opensilex.org/vocabulary/oeso#Installation',

    // Event
    'event' => 'http://www.opensilex.org/vocabulary/oeev#Event',
    'moveFrom' => 'http://www.opensilex.org/vocabulary/oeev#MoveFrom',
    'moveTo' => 'http://www.opensilex.org/vocabulary/oeev#MoveTo',

    'Provenance' => 'http://www.opensilex.org/vocabulary/oeso#Provenance',

    //-------- Relations
    'aperture' => 'http://www.opensilex.org/vocabulary/oeso#aperture',
    'brdfP1' => 'http://www.opensilex.org/vocabulary/oeso#brdfP1',
    'brdfP2' => 'http://www.opensilex.org/vocabulary/oeso#brdfP2',
    'brdfP3' => 'http://www.opensilex.org/vocabulary/oeso#brdfP3',
    'brdfP4' => 'http://www.opensilex.org/vocabulary/oeso#brdfP4',
    'attenuatorFilter' => 'http://www.opensilex.org/vocabulary/oeso#attenuatorFilter',
    'dateOfLastCalibration' => 'http://www.opensilex.org/vocabulary/oeso#dateOfLastCalibration',
    'dateOfPurchase' => 'http://www.opensilex.org/vocabulary/oeso#dateOfPurchase',
    'focalLength' => 'http://www.opensilex.org/vocabulary/oeso#focalLength',
    'hasSpecies' => 'http://www.opensilex.org/vocabulary/oeso#hasSpecies',
    'hasVariety' => 'http://www.opensilex.org/vocabulary/oeso#hasVariety',
    'halfFieldOfView' => 'http://www.opensilex.org/vocabulary/oeso#halfFieldOfView',
    'hasAlias' => 'http://www.opensilex.org/vocabulary/oeso#hasAlias',
    'hasBrand' => 'http://www.opensilex.org/vocabulary/oeso#hasBrand',
    'hasExperimentModalities' => 'http://www.opensilex.org/vocabulary/oeso#hasExperimentModalities',
    'hasLens' => 'http://www.opensilex.org/vocabulary/oeso#hasLens',
    'hasPart' => 'http://www.opensilex.org/vocabulary/oeso#hasPart',
    'hasRadiometricTargetMaterial' => 'http://www.opensilex.org/vocabulary/oeso#hasRadiometricTargetMaterial',
    'hasReplication' => 'http://www.opensilex.org/vocabulary/oeso#hasReplication',
    'hasShape' => 'http://www.opensilex.org/vocabulary/oeso#hasShape',
    'hasShapeLength' => 'http://www.opensilex.org/vocabulary/oeso#hasShapeLength',
    'hasShapeWidth' => 'http://www.opensilex.org/vocabulary/oeso#hasShapeWidth',
    'hasShapeDiameter' => 'http://www.opensilex.org/vocabulary/oeso#hasShapeDiameter',
    'hasTechnicalContact' => 'http://www.opensilex.org/vocabulary/oeso#hasTechnicalContact',
    'height' => 'http://www.opensilex.org/vocabulary/oeso#height',
    'inServiceDate' => 'http://www.opensilex.org/vocabulary/oeso#inServiceDate',
    'isPartOf' => 'http://www.opensilex.org/vocabulary/oeso#isPartOf',
    'label' => 'rdfs:label',
    'maxWavelength' => 'http://www.opensilex.org/vocabulary/oeso#maxWavelength',
    'measuredVariable' => 'http://www.opensilex.org/vocabulary/oeso#measuredVariable',
    'minWavelength' => 'http://www.opensilex.org/vocabulary/oeso#minWavelength',
    'Motivation' => 'http://www.w3.org/ns/oa#Motivation',
    'personInCharge' => 'http://www.opensilex.org/vocabulary/oeso#personInCharge',
    'pixelSize' => 'http://www.opensilex.org/vocabulary/oeso#pixelSize',
    'rdfsLabel' => 'http://www.w3.org/2000/01/rdf-schema#label',
    'rdfType' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
    'scanningAngularRange' => 'http://www.opensilex.org/vocabulary/oeso#scanningAngularRange',
    'scanAngularResolution' => 'http://www.opensilex.org/vocabulary/oeso#scanAngularResolution',
    'hasSerialNumber' => 'http://www.opensilex.org/vocabulary/oeso#hasSerialNumber',
    'source' => 'http://purl.org/dc/terms/source',
    'spectralSamplingInterval' => 'http://www.opensilex.org/vocabulary/oeso#spectralSamplingInterval',
    'spotHeight' => 'http://www.opensilex.org/vocabulary/oeso#spotHeight',
    'spotWidth' => 'http://www.opensilex.org/vocabulary/oeso#spotWidth',
    'type' => 'rdf:type',
    'waveband' => 'http://www.opensilex.org/vocabulary/oeso#waveband',
    'wavelength' => 'http://www.opensilex.org/vocabulary/oeso#wavelength',
    'width' => 'http://www.opensilex.org/vocabulary/oeso#width',

    // Event
    'from' => 'http://www.opensilex.org/vocabulary/oeev#from',
    'to' => 'http://www.opensilex.org/vocabulary/oeev#to'

];

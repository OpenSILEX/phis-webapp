<?php
//******************************************************************************
//                                       app.php
// SILEX-PHIS
// Copyright © INRA 2017
// Creation date:  Mar. 2017
// Contact: morgane.vidal@inra.fr, arnaud.charleroy, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

/**
 * French translations of this application.
 * @link https://www.yiiframework.com/extension/translate
 * @update [Arnaud Charleroy] 24 August, 2018: widgets translations
 */

use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\event\EventButtonWidget;
use app\components\widgets\event\EventGridViewWidget;
use app\components\widgets\PropertyWidget;
use app\components\widgets\concernedItem\ConcernedItemGridViewWidget;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\EventAction;
use app\models\yiiModels\YiiConcernedItemModel;
use app\models\yiiModels\YiiAnnotationModel;

return [

    // translations with a plural

    '{n, plural, =1{Add an annotation} other{Add annotations}}' => '{n, plural, =1{Ajouter une annotation} other{Ajouter des annotations}}',
    '{n, plural, =1{Event} other{Events}}' => '{n, plural, =1{Evénement} other{Evénements}}',
    '{n, plural, =1{Experiment} other{Experiments}}' => '{n, plural, =1{Expérimentation} other{Expérimentations}}',
    '{n, plural, =1{Group} other{Groups}}' => '{n, plural, =1{Groupe} other{Groupes}}',
    '{n, plural, =1{Person} other{Persons}}' => '{n, plural, =1{Personne} other{Personnes}}',
    '{n, plural, =1{Project} other{Projects}}' => '{n, plural, =1{Projet} other{Projets}}',
    '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}' => '{n, plural, =1{Cible Radiométrique} other{Cibles Radiométriques}}',
    '{n, plural, =1{Scientific Object} other{Scientific Objects}}' => '{n, plural, =1{Objet Scientifique} other{Objets Scientifiques}}',
    '{n, plural, =1{Scientific frame} other{Scientific frames}}' => '{n, plural, =1{Cadre scientifique} other{Cadres scientifiques}}',
    '{n, plural, =1{Sensor} other{Sensors}}' => '{n, plural, =1{Capteur} other{Capteurs}}',
    '{n, plural, =1{Species} other{Species}}' => '{n, plural, =1{Espèce} other{Espèces}}',
    '{n, plural, =1{User} other{Users}}' => '{n, plural, =1{Utilisateur} other{Utilisateurs}}',
    '{n, plural, =1{Vector} other{Vectors}}' => '{n, plural, =1{Vecteur} other{Vecteurs}}',

    // translations in alphabetical order

    // A
    'Acquisition session template' => 'Gabarit de session d\'aquisition',
    'Add Dataset' => 'Importer un jeu de données',
    'Add Document' => 'Ajouter un document',
    'Add Document Script' => 'Ajouter un script',
    'Add row' => 'Ajouter une ligne',
    'Add Sensors' => 'Ajouter des Capteurs',
    'Add Vectors' => 'Ajouter des Vecteurs',
    'Address' => 'Adresse',
    'Admin' => 'Administrateur',
    'Administrative Contacts' => 'Contacts administratifs',
    'Affiliation' => 'Affiliation',
    'All Descendants' => 'Tous les Descendants',
    AnnotationButtonWidget::ADD_ANNOTATION_LABEL => 'Ajouter annotation', // in components/widgets/AnnotationButtonWidget.php : ADD_ANNOTATION_LABEL = 'Add an annotation'
    AnnotationGridViewWidget::LINKED_ANNOTATIONS => "Annotations liées", // in components/widgets/AnnotationGridViewWidget.php : LINKED_ANNOTATIONS = "Linked Annotation(s)"
    AnnotationGridViewWidget::NO_LINKED_ANNOTATIONS => "Aucune annotation liée", // in components/widgets/AnnotationGridViewWidget.php : NO_LINKED_ANNOTATIONS = "No linked Annotation(s)"
    'Annotations' => 'Annotations',
    'Attenuator Filter' => 'Filtre Atténuateur',
    'Available' => 'Disponible',
    'Availability' => 'Disponibilité',

    // B
    'Back to sensor view' => 'Retour à la vue du capteur',
    'Brand' => 'Marque',
    'BRDF coefficient P1' => 'Coefficient BRDF P1',
    'BRDF coefficient P2' => 'Coefficient BRDF P2',
    'BRDF coefficient P3' => 'Coefficient BRDF P3',
    'BRDF coefficient P4' => 'Coefficient BRDF P4',

    // C
    'Campaign' => 'Campagne',
    'Carpet' => 'Moquette',
    'Characterize Sensor' => 'Caractériser un Capteur',
    'Characterize' => 'Caractériser',
    'Circular' => 'Circulaire',
    'Contact' => 'Contact',
    'Contact / Help' => 'Contact / Aide',
    'Column' => 'Colonne',
    'Columns' => 'Colonnes',
    'Concerns' => 'Concerne',
    'Concerned Experimentations' => 'Expérimentations Concernées',
    'Concerned item' => 'Élément concerné',
    'Concerned item type' => 'Type de l\'élément concerné',
    'Concerned item URI' => 'URI de l\'élément concerné',
    'Concerned items URIs' => 'URIs des éléments Concernés',
    ConcernedItemGridViewWidget::CONCERNED_ITEMS_LABEL => 'Eléments concernés', // in components/widgets/concernedItem/ConcernedItemGridViewWidget.php : CONCERNED_ITEMS_LABEL = "Concerned Items"
    ConcernedItemGridViewWidget::NO_CONCERNED_ITEMS_LABEL => 'Aucun élément concerné', // in components/widgets/concernedItem/ConcernedItemGridViewWidget.php : NO_CONCERNED_ITEMS_LABEL = "No items concerned"
    'Concerned Projects' => 'Projets concernés',
    'Comment' => 'Commentaire',
    'Creation Date' => 'Date de Création',
    'Creator' => 'Auteur',
    'Creator of the annotation' => 'Auteur de l\'annotation',
    'Crop Species' => 'Espèce',

    // D
    'Data' => 'Données',
    'Data file' => 'Fichier de données',
    'Data Search'=>'Recherche de données',
    'Dataset' => 'Jeux de données',
    'Dataset Creation Date' => 'Données Insérées',
    'Date' => 'Date',
    'Date End' => 'Date de fin',
    'Date Of Last Calibration' => 'Date de Dernier Étalonnage',
    'Date Of Purchase' => 'Date d\'Achat',
    'Date Start' => 'Date de début',
    'Description' => 'Description',
    'Diameter' => 'Diamètre',
    'Diameter (m)' => 'Diamètre (m)',
    'Document Type' => 'Type du Document',
    'Download' => 'Télécharger',
    'Download Search Result' => 'Télécharger le Résultat de la Recherche',
    'Download Template' => 'Télécharger le Gabarit',
    'Download Example' => 'Télécharger un fichier d\'example',

    // E
    'Email' => 'Adresse email',
    'Entity' => 'Entité',
    'Enter date of last calibration' => 'Saisir la date de dernier étalonnage',
    'Enter date of purchase' => 'Saisir la date d\'achat',
    'Enter in service date' => 'Saisir la date de mise en service',
    'Error' => 'Erreur',
    'Errors while creating user' => 'Erreurs lors de la création de l\'utilisateur',
    'Experimental Organization' => 'Organisation expérimentale',
    'Experiment Modalities' => 'Modalités Expérimentales',
    EventAction::EVENT_UNUPDATABLE_DUE_TO_UNUPDATABLE_PROPRTY_LABEL =>
        "L'événement ne peut être mis à jour que via le web service car une de "
        . "ses propriétés spécifiques n'est actuellement pas compatible avec la webapp.",
    EventAction::PROPERTY_HAS_PEST_LABEL => "Ravageur",
    EventAction::PROPERTY_FROM_LABEL => "Depuis",
    EventAction::PROPERTY_TO_LABEL => "Jusqu'à",
    EventAction::PROPERTY_TYPE_LABEL => "Type de la propriété",
    EventButtonWidget::ADD_EVENT_LABEL => 'Ajouter événement',
    EventGridViewWidget::EVENTS_LABEL => "Événements",
    EventGridViewWidget::NO_EVENT_LABEL => "Pas d'événement",

    // F
    'Family Name' => 'Nom',
    'Field' => 'Champ',
    'File' => 'Fichier',
    'File Extension' => 'Extension du Fichier',
    'File Informations' => 'Informations sur le Fichier',
    'File Path' => 'Chemin du Fichier',
    'Financial Funding' => 'Support financier',
    'Financial Name' => 'Nom du financeur',
    'Financial Reference' => 'Référence du financeur',
    'Financial Support' => 'Support financier',
    'First Name' => 'Prénom',
    'Focal Length' => 'Distance Focale',

    // G
    'Generate Layer' => 'Générer la Couche',
    'Generate Map' => 'Générer la Carte',
    'Generated URI' => 'URI générée',
    'Geographic Location' => 'Localisation géographique',
    'Geometry' => 'Géométrie',
    'Graphic visualization' => 'Visualisation graphique',
    'Groups' => 'Groupes',
    'Guest' => 'Invité',

    // H
    'Height' => 'Hauteur',
    'Hemisphericals' => 'Hémisphériques',
    'Homepage' => 'Site web',

    // I
    
    'Image Search'=>'Recherche d\'images',
    'Image View'=>'Vue de l\'image',
    'Images Visualization' => 'Visualisation d\'Images',
    'In Service Date' => 'Date de Mise en Service',
    'Insertion status' => 'Statut d\'insertion',
    'Internal Label' => 'Label Interne',

    // J

    // K
    'Keywords' => 'Mots clés',

    // L
    'Labels' => 'Labels',
    'Laboratory Name' => 'Nom du laboratoire',
    'Language' => 'Langue',
    'Length' => 'Longueur',
    'Length (m)' => 'Longueur (m)',
    'Level' => 'Niveau',
    'Linked Agronomical Objects' => 'Objets Agronomiques Liés',
    'Linked Documents' => 'Documents Liés',
    'Line' => 'Ligne',
    'Linked Document(s)' => 'Document(s) lié(s)',
    'Login' => 'Connexion',
    'Logout' => 'Déconnexion',

    // M
    'Map Visualization' => 'Visualisation Cartographique',
    'Material' => 'Matière',
    'Members' => 'Membres',
    'Method' => 'Méthode',
    'Missing method.' => 'La méthode est vide.',
    'Missing trait.' => 'Le trait est vide.',
    'Missing unit.' => 'L\'unité est vide.',
    'Model' => 'Modèle',
    'Motivation of the annotation' => 'Motif de l\'annotation',

    // N
    'Name' => 'Nom',
    'No' => 'Non',
    'No item concerned' => 'Aucun élément concerné',
    'No Specific Property' => 'Aucune Propriété Spécifique',

    // O
    'Objective' => 'Objectif',
    'On selected plot(s)' => 'Sur les micro parcelles sélectionnées',
    'Ontologies References' => 'Références vers des Ontologies',
    'Organization' => 'Organisme',
    'ORGANIZATION' => 'ORGANISME',
    'Owner' => 'Propriétaire',

    // P
    'Password' => 'Mot de passe',
    'Painting' => 'Peinture',
    'Person In Charge' => 'Responsable',
    'Phenotype(s) Visualization' => 'Visualisation de phénotypes',
    'Phone' => 'Téléphone',
    'Pixel Size' => 'Taille de Pixel',
    'Place' => 'Lieux',
    'Private Access' => 'Accès Privé',
    'Project Coordinators' => 'Coordinateurs du projets',
    'Project Type' => 'Type du projet',
    PropertyWidget::NO_PROPERTY_LABEL => 'Aucune propriété spécifique',
    'Provenance comment' => 'Commentaire de la provenance',
    'Provenance (URI)' => 'Provenance (URI)',
    'Public Access' => 'Accès Public',

    // Q
    'Quantitative Variable' => 'Variable Quantitative',

    // R
    'Real number, String or Date' => 'Nombre réel, Chaine de caractère ou Date',
    'Rectangular' => 'Rectangulaire',
    'Reference URI' => 'URI de Référence',
    'Reflectance value' => 'Valeur de la réflectance',
    'Register an event' => 'Enregistrer un événement',
    'Related Projects' => 'Projets en lien',
    'Related References' => 'Références Externes',
    'Relation' => 'Relation',
    'Relation Type' => 'Type de Relation',
    'Relation Type Labels' => 'Labels du Type de Relation',
    'Remove last row' => 'Supprimer la dernière ligne',
    'Replication' => 'Répétition',

    // S
    'Scientific Contacts' => 'Contacts scientifiques',
    'Scientific Supervisors' => 'Superviseurs scientifiques',
    'Search Criteria' =>'Critères de recherche',
    'See' => 'Voir',
    'Select method alias...' => 'Sélectionnez l\'alias de la méthode',
    'Select trait alias...' => 'Sélectionnez l\'alias du trait',
    'Select type...' => 'Sélectionez le type',
    'Select unit alias...' => 'Sélectionnez l\'alias de l\'unité',
    'Sensor Data Visualization' => 'Visualisation des données du capteur',
    'Sensor Position' => 'Position du Capteur',
    'Sensor Profile' => 'Profil du Capteur',
    'Serial Number' => 'Numéro de Série',
    'Server File Path' => 'Lien du Fichier sur le Serveur',
    'Shape' => 'Forme',
    'Shooting Configuration' => 'Configuration de prise de vue',
    'Shortname' => 'Acronyme',
    'Show Images' => 'Afficher les Images',
    'Specific properties' => 'Propriétés spécifiques',
    'Spectral hemispheric reflectance file' => 'Fichier de réflectance hémisphérique spectrale',
    'Spectralon' => 'Spectralon',
    'Status' => 'Statut',
    'Subproject Type' => 'Type de sous projet',
    'Subproject Of' => 'Sous-projet de',

    //T
    'Team' => 'Équipe',
    'Technical Supervisors' => 'Superviseurs techniques',
    'the documentation' => 'la documentation',
    'Timezone offset' => 'Fuseau horaire',
    'Title' => 'Titre',
    'Tools' => 'Outils',
    'Trait' => 'Trait',
    'twitter' => 'twitter',
    'Type' => 'Type',
    'Type Labels' => 'Type Labels',

    // U
    'Unavailable' => 'Indisponible',
    'Unit' => 'Unité',
    'Update' => 'Modifier',
    'Update event' => 'Modifier l\'événement',
    'Update sensors' => 'Mise à jour des capteurs',
    'Update measured variables' => 'Mise à jour des variables mesurées',

    // V
    'Value' => 'Valeur',
    'Value Labels' => 'Labels de la Valeur',
    'Variable' => 'Variable',
    'Variable Label' => 'Label de la Variable',
    'Variable Definition' => 'Définition de la variable',
    'Variety' => 'Variété',
    'Verification Code' => 'Code de vérification',
    'View / Download' => 'Visualiser / Télécharger',

    // W
    'Was Generated By' => 'Généré Par',
    'Wavelength' => 'Longueur d\'onde',
    'Wavelength (nm)' => 'Longueur d\'onde (nm)',
    'Website' => 'Site web',
    'Width' => 'Largeur',
    'Width (m)' => 'Largeur (m)',
    'wikipedia page' => 'page wikipédia',

    // X

    // Y
    YiiAnnotationModel::LABEL => 'Annotation', // in models/yiiModels/YiiAnnotationModel.php : LABEL = "Annotation"
    YiiAnnotationModel::CREATION_DATE_LABEL => 'Date de l\'annotation', // in models/yiiModels/YiiAnnotationModel.php : CREATION_DATE_LABEL = "Annotation Date"
    // MISSING TRANSLATION in models/yiiModels/YiiAnnotationModel.php : CREATOR_LABEL = "Creator"
    YiiAnnotationModel::MOTIVATED_BY_LABEL  => 'Motivée par', // in models/yiiModels/YiiAnnotationModel.php : MOTIVATED_BY_LABEL = "Motivated by"
    // MISSING TRANSLATION in models/yiiModels/YiiAnnotationModel.php : BODY_VALUES_LABEL = "Description"
    YiiAnnotationModel::TARGETS_LABEL  => 'Entités ciblées', // in models/yiiModels/YiiAnnotationModel.php : TARGETS_LABEL = "Targets"
    // MISSING TRANSLATION in models/yiiModels/YiiAnnotationModel.php : TARGET_SEARCH_LABEL = "target"

    YiiConcernedItemModel::URI_LABEL => 'URI', // in models/yiiModels/YiiConcernedItemModel.php : URI_LABEL = "URI"
    YiiConcernedItemModel::RDF_TYPE_LABEL => 'Type', // in models/yiiModels/YiiConcernedItemModel.php : RDF_TYPE_LABEL = "Type"
    YiiConcernedItemModel::LABELS => 'Alias', // in models/yiiModels/YiiConcernedItemModel.php : LABELS = "labels"
    // MISSING TRANSLATION in models/yiiModels/YiiConcernedItemModel.php : LABELS_LABEL = "Labels"

    YiiEventModel::EVENT_LABEL => "Evénement", // in models/yiiModels/YiiEventModel.php : EVENT_LABEL = "Event"
    YiiEventModel::EVENTS_LABEL => "Evénements", // in models/yiiModels/YiiEventModel.php : EVENTS_LABEL = "Events"
    YiiEventModel::URI_LABEL => "URI", // in models/yiiModels/YiiEventModel.php : URI_LABEL = "URI"
    YiiEventModel::TYPE_LABEL => "Type", // in models/yiiModels/YiiEventModel.php : TYPE_LABEL = "Type"
    YiiEventModel::DATE_LABEL => "Date", // in models/yiiModels/YiiEventModel.php : DATE_LABEL = "Date"
    YiiEventModel::CONCERNED_ITEMS_LABEL => "Eléments concernés", // in models/yiiModels/YiiEventModel.php : CONCERNED_ITEMS_LABEL = "Concerned items"

    'Yes' => 'Oui',

    // Z
];

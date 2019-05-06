<?php

//**********************************************************************************************
//                                       messages.php
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: March 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  September 10th, 2018
// Subject: French translations
//***********************************************************************************************

return [
    'About' => "SILEX-PHIS est un système d'information de phénotypage haut-débit. Il est dit 'hybride' car il est basé sur
            différentes technologies de stockage de données : les bases relationnelles, les systèmes NoSQL et les outils
            du Web sémantique. SILEX-PHIS est développé pour gérer trois types de données : les mesures dites en ligne, les mesures
            hors ligne et les mesures complexes.",
    'Acquisition date of the data' => 'Date d\'acquisition de la donnée',
    'An error occurred.' => "Une erreur est apparue",
    'Bad date format.' => 'Mauvais format de date.',
    'Bad email / password' => 'Adresse email ou mot de passe erroné',
    'Bad geometry given' => 'Mauvaise géométrie donnée',
    'Bad value : float expected.' => 'Mauvaise valeur : un float est attendu.',
    'Enter in service date' => 'Renseigner la date de mise en service',
    'Errors in file' => 'Il y a des erreurs dans le fichier',
    'Expected format' => 'Format Attendu',
    'File Rules' => 'Règles concernant le contenu du fichier',
    'Internal Error' => 'Erreur interne',
    'No plot selected' => 'Pas de parcelle sélectionnée',
    'No spaces allowed. Used for the URI. Example : drops' => 'Les espaces ne sont pas autorisés. Utilisé pour créer l\'URI. Exemple: drops',
    'Please contact us if you think this is a server error. Thank you.' => 'Merci de nous contacter si pensez qu\'il s\'agit d\'une erreur de serveur.',
    'Please fill out the following fields to login:' => 'Merci de renseigner les champs suivants pour vous connecter:',
    'Required column missing (ScientificObjectURI, Date or Value)' => "Une des colonnes requises est manquante (ScientificObjectURI, Date ou Value)",
    'Required column missing (Geometry or ExperimentURI)' => 'Une des colonnes requises manque (Geometry ou ExperimentURI)',
    'See the folowing list to get all species URI' => 'Regarder dans la liste suivante pour avoir toutes les URI d\'espèces',
    'Some required fields are missings' => 'Des champs requis sont manquants.',
    'The selected sensor cannot be characterized. Please select another sensor among cameras (all camera types : RGB, multispectral, etc.), spectrometers and LiDAR.' => 'Le capteur sélectionné ne peut pas être caractérisé. Veuillez sélectionner une caméra (RGB, TIR, multispectrale, etc.), un spectromètre ou un LiDAR. ',
    'The alias of the plot (e.g. MAU17-PG_38_WW_1_19_5)' => 'L\'alias du plot (ex. MAU17-PG_38_WW_1_19_5)',
    'The experiment modalities of the plot (e.g. WW, WD)' => 'Les modalités expérimentales du plot (ex. WW, WD)',
    'The object update has failed. See insertion status column for more details.' => 'La mise à jour des objets a échoué. Voir la colonne status pour plus d\'informations',
    'The replication of the plot (e.g. 2, A)' => 'La répétition du plot (ex. 2, A)',
    'The URI of the scientific object (e.g http://www.phenome-fppn.fr/phenovia/2017/o1028649)' => 'L\'URI de l\'objet scientifique (ex. http://www.phenome-fppn.fr/phenovia/2017/o1028649)',
    'The URI of the experiment (e.g. http://www.phenome-fppn.fr/pheno3c/P3C2017-6)' => 'L\'URI de l\'experimentation (ex. http://www.phenome-fppn.fr/pheno3c/P3C2017-6)',
    'The URI of the species (e.g. http://www.phenome-fppn.fr/id/species/zeamays)' => 'L\'URI de l\'espèce (ex. http://www.phenome-fppn.fr/id/species/zeamays)',
    'The error above occurred while the Web server was processing your request.' => 'L\'erreur ci-dessus est survenue lors du traitement de votre requête par le serveur web.',
    'The spectral hemispherical reflectance file uses tabluations (\t) as field separators and dots (.) as decimal separators.' => 'le fichier de réflectance hémisphérique spectrale utilise des tabulations (\t) comme séparateurs de champ et des points (.) comme séparateur décimal',
    'The value' => 'La valeur',
    'The variety used in the plot (e.g. apache)' => 'La variété du plot (ex. apache)',
    'Be carefull to write all the new and the former properties of the updated object.' => 'Attention, veillez à écrire toutes les nouvelles et les anciennes propriétés de l\'objet mis à jour.',
    'It is not possible to change the experiment.' => 'Il n\'est pas possible de changer l\'expérimentation d\'un objet scientifique',
    'To assign scientific objects to two experiments: (1) create the scientific objects on one of the experiments and (2) update them with the uri of the second experiment.' => 'Pour affecter des objets scientifiques à deux expériences: (1) créez les objets scientifiques sur l\'une des expériences et (2) mettez-les à jour avec l\'uri de la deuxième expérience.',
    'Objects successfully updated' => 'Les objets ont bien été mis à jour',
    'Unknown scientific object.' => 'Objet scientifique inconnu.',
    'Unknown experiment' => 'Expérimentation inconnue',
    'Unknown species' => 'Espèce inconnue',
    'Unknown error' => 'Erreur inconnue',
    'Values in nanometers (450 nm, 560 nm, ...) of the wavelengths at which the radiometric target reflectance has been measured (real numbers)' => 'Valeurs en nanomètres (450 nm, 560 nm, ...) des longueurs d\'onde auxquelles la réflectance de la cible radiométrique a été mesurée (nombres réels)',
    'Values of the spectral hemispherical reflectances in wavelength of the radiometric target for each of the wavelength listed in the table\'s first row (real numbers included in a range of [0 - 1])' => 'Valeurs des réflectances hémisphériques spectrales de la cible radiométrique pour les longueurs d\'onde de la première ligne du tableau (nombres réels compris entre 0 et 1)',
    'You <b>cannot modify already existing</b> traits, methods and units.' => 'Vous ne pouvez pas modifier de traits, méthodes et unités déjà existantes.',
    'Please contact us if you think this is a server error. Thank you.' => 'Contactez l\'administrateur si vous pensez que c\'est une erreur.',
    'The above warning occurred while the Web server was processing your request.' => 'Un incident est survenu au moment du traitement de votre requête par le serveur.',
    'When you change variables measured by sensor in the list, click on the check button to update them.' => 'En cas de modification des variables mesurées par le capteur, cliquer sur le bouton de validation pour les mettre à jour.',
    'If no date are selected, visualization will render latest week of data found for sensor measured variables.' => 'Si aucune date n\'est sélectionnées, les graphiques représenteront la dernière semaine de données trouvée pour les variables mesurées par le capteur.',
    'Measures displayed are limited to the 80 000 first results.' => 'Les mesures affichées seront limitées au 80 000 premiers résultats',
    'If no date are selected, visualization will render latest week of data found for sensor measured variables.' => 'Si aucune date n\'est sélectionnée, les données affichées sont celles de la dernière semaine pour laquelle des données existent.',
    'Measures displayed are limited to the 80 000 first results.' => 'Les mesures sont affichées dans la limite de 80 000 résultats.',
    'When you change measured variables in the list, click on the check button to update them.' => 'En cas de changement dans la liste des variables mesurées, cliquer sur le bouton de validation pour les mettre à jour',
    'When you change measured variables in the list, click on the check button to update them.' => 'En cas de changement dans la liste des capteurs utilisés, cliquer sur le bouton de validation pour les mettre à jour',
    'Do you have an account or do you want to try PHIS ?' => 'Possédez-vous un compte ou souhaitez vous essayer PHIS ?',
    'Your session has expired' => 'Votre session a expirée',
    'If you already have an account' => 'Vous possédez déjà un compte',
    'If you want to try PHIS as guest' => 'Vous souhaitez essayer PHIS en tant qu\'invité',
    'You are on PHIS, the Hybrid Information System about Phenotyping !' => 'Bienvenue dans PHIS, le Système d\'Information Hybride pour le Phénotypage'
];

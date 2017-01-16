<?php

#region SQaLibur

class SQaLibur {

    private static $initialized = false;
    public static $name = 'SQaLibur';
    public static $installed = false;
    public static $page = 3;
    public static $rank = 75;
    public static $enabledShow = true;
    public static $enabledInstall = true;
    private static $langTemplate = 'SQaLibur';

    public static function getDefaults() {
        return array(
            'sqa_passwd' => array('data[SQA][sqa_passwd]', null)
        );
    }

    /**
     * initialisiert das Segment
     * @param type $console
     * @param string[][] $data die Serverdaten
     * @param bool $fail wenn ein Fehler auftritt, dann auf true setzen
     * @param string $errno im Fehlerfall kann hier eine Fehlernummer angegeben werden
     * @param string $error ein Fehlertext für den Fehlerfall
     */
    public static function init($console, &$data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__) . '/');
        Installation::log(array('text' => Installation::Get('main', 'languageInstantiated')));

        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['SQA']['sqa_passwd'], 'data[SQA][sqa_passwd]', $def['sqa_passwd'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }
    
    public static function show($console, $result, $data) {
        // das Segment soll nur gezeichnet werden, wenn der Nutzer eingeloggt ist
        if (!Einstellungen::$accessAllowed) {
            return;
        }
        
        if (!Paketverwaltung::isPackageSelected($data, 'SQaLibur')){
            return;
        }

        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $text = '';

        if (!$console) {
            $text .= Design::erstelleBeschreibung($console, Installation::Get('main', 'description', self::$langTemplate));
        }

        $text .= Design::erstelleZeile($console, Installation::Get('main', 'setPasswd', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['SQA']['sqa_passwd'], 'data[SQA][sqa_passwd]', '', true), 'v');

        echo Design::erstelleBlock($console, Installation::Get('main', 'title', self::$langTemplate), $text);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }
    
    // fügt dem Segment Paketverwaltung eine externe Komponentendefinition hinzu
    public static function getComponentFilesFromSelectedPackages($data){
        if (Paketverwaltung::isPackageSelected($data, 'SQaLibur')){
            return array(array('name'=>'SQaLibur','location'=>'external','conf'=>'http://localhost:8080/SQaLibur/info/component'));
        }
        return array();
    }
    
    // dem Segment Kmponentenzugang werden die Profile für SQaLibur hinzugefügt
    public static function getAllExternalProfiles($data){
        if (!Paketverwaltung::isPackageSelected($data, 'SQaLibur')){
            return array();
        }
        
        if (!isset($data['SQA']['sqa_passwd'])){
            $data['SQA']['sqa_passwd'] = '';
        }
        
        $myProfile = GateProfile::createGateProfile(null,
                                                    'SQaLibur');
                                                    
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'DBOOP',
                                                     'POST /insert',
                                                     null));
                                                     
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'LMarking',
                                                     'POST /marking',
                                                     null));
                                                     
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'FSPdf',
                                                     'POST /pdf',
                                                     null));
                                                     
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'LExerciseSheet',
                                                     'GET /exercisesheet/exercisesheet/:sheetid',
                                                     null));
                                                     
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'LExerciseSheet',
                                                     'GET /exercisesheet/course/:courseid',
                                                     null));
                                                     
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'DBProcessList',
                                                     'POST /process',
                                                     null));
                                                     
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'DBProcessList',
                                                     'DELETE /process/process/:processid',
                                                     null));
                                                     
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'DBProcessList',
                                                     'GET /process/course/:courseid/component/:componentid',
                                                     null));
                                                     
        $myProfile->addRule(GateRule::createGateRule(null,
                                                     'httpCall',
                                                     'FSBinder',
                                                     'GET /:folder/:a/:b/:c/:file/:filename',
                                                     null));
                                                     
        $myProfile->addAuth(GateAuth::createGateAuth(null,
                                                     'httpAuth',
                                                     null,
                                                     'SQaLibur',
                                                     $data['SQA']['sqa_passwd'],
                                                     null));

        return array($myProfile);
    }

}

#endregion SQaLibur
<?php

defined("ABSPATH") or die("");

use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use VendorDuplicator\Amk\JsonSerialize\JsonUnserializeMap;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Utils\Crypt\CryptCustom;

class DUP_PRO_Settings_U
{
    public $message;
    public $export_filepath;
    
    public function __construct()
    {
        $this->message         = '';
        $this->export_filepath = '';
    }

    /**
     *  Exports all settings an export file.
     *
     *  @return void */
    public function runExport()
    {
        $global  = DUP_PRO_Global_Entity::get_instance();
        $sglobal = DUP_PRO_Secure_Global_Entity::getInstance();
        $export_data = new StdClass();
        $export_data->templates            = DUP_PRO_Package_Template_Entity::getAllWithoutManualMode();
        $export_data->schedules            = DUP_PRO_Schedule_Entity::get_all();
        $export_data->storages             = DUP_PRO_Storage_Entity::get_all();
        $export_data->settings             = $global;
        $export_data->secure_settings      = $sglobal;
        $export_data->secure_settings->lkp = '';
        $json_file_data                    = JsonSerialize::serialize($export_data, JsonSerialize::JSON_SKIP_CLASS_NAME | JsonSerialize::JSON_SKIP_MAGIC_METHODS);
        if ($json_file_data === false) {
            //Isolate the problem area:
            $test           = SnapJson::jsonEncode($export_data->templates);
            $test_templates = ($test === null || $test === false) ? '*Fail' : 'Pass';
            $test           = SnapJson::jsonEncode($export_data->schedules);
            $test_schedules = ($test === null || $test === false) ? '*Fail' : 'Pass';
            $test           = SnapJson::jsonEncode($export_data->storages);
            $test_storages  = ($test === null || $test === false) ? '*Fail' : 'Pass';
            $test           = SnapJson::jsonEncode($export_data->settings);
            $test_settings  = ($test === null || $test === false) ? '*Fail' : 'Pass';
            $test           = SnapJson::jsonEncode($export_data->schedules);
            $test_settings  = ($test === null || $test === false) ? '*Fail' : 'Pass';

            $exc_msg = 'Isn\'t possible serialize json data';
            $div     = "******************************************";
            $err     = <<<ERR
\n{$div}\nDUPLICATOR PRO - EXPORT SETTINGS ERROR\n{$div}
Error encoding json data for export status

Templates	= {$test_templates}
Schedules	= {$test_schedules}
Storage		= {$test_storages}
Settings	= {$test_settings}
Security	= {$test_settings}

RECOMMENDATION:
Check the data in the failed areas above to make sure the data is correct.  If the data looks correct consider re-saving the data in
that respective area.  If the problem persists consider removing the items one by one to isolate the setting that is causing the issue.

ERROR DETAILS:\n$exc_msg
ERR;
            DUP_PRO_Log::traceObject('There was an error encoding json data for export', $export_data);
            throw new Exception($err);
        }

        $encrypted_data        = CryptCustom::encrypt($json_file_data, 'test');
        $this->export_filepath = DUPLICATOR_PRO_SSDIR_PATH_TMP . '/dpro-export-' . date("Ymdhs") . '.dup';
        if (file_put_contents($this->export_filepath, $encrypted_data) === false) {
            throw new Exception("Error writing export to {$this->export_filepath}");
        }

        $this->message = DUP_PRO_U::__("Export data file has been created!<br/>");
    }

    /**
     *  Creates and export file of current settings and then
     *  imports all the new settings from an existing import file
     *
     *  @param $filename The name of the import file to import
     *  @param $opts The options to import templates, schedules, storage, etc.
     *  @return void */
    public function runImport($filename, $opts)
    {
        $path_ssdir = SnapIO::safePath(DUPLICATOR_PRO_SSDIR_PATH);
        if (!file_exists($path_ssdir)) {
            DUP_PRO_U::initStorageDirectory();
        }

        //Generate backup of current settings
        $this->runExport();
        $filepath       = $_FILES['import-file']['tmp_name'];
        $encrypted_data = file_get_contents($filepath);
        if ($encrypted_data === false) {
            throw new Exception("Error reading {$filepath}");
        }

        $json_data   = CryptCustom::decrypt($encrypted_data, 'test');
        $import_data = JsonSerialize::unserializeWithMap(
            $json_data, 
            new JsonUnserializeMap([
                '' => 'object',
                'templates/*'     => 'cl:' . DUP_PRO_Package_Template_Entity::class,
                'schedules/*'     => 'object',
                'storages/*'      => 'object',
                'settings'        => 'object',
                'secure_settings' => 'object'
            ]),
            512,
            JsonSerialize::JSON_SKIP_MAGIC_METHODS
        );

        if ($import_data === null) {
            throw new Exception('Problem decoding JSON data');
        }

        $this->processImportData($import_data, $opts);
        $this->message = DUP_PRO_U::__("All data has been succesfully imported and updated! <br/>");
        $this->message .= DUP_PRO_U::__("Backup data file has been created here {$this->export_filepath} <br/>");
    }

    private function processImportData($import_data, $opts)
    {
        $storage_map  = null;
        $template_map = null;
        DUP_PRO_Log::traceObject('####opts', $opts);
        foreach ($opts as $import_type) {
            switch ($import_type) {
                case 'schedules':
                    if ($storage_map === null) {
                        $storage_map = $this->importStorages($import_data);
                    }

                    if ($template_map === null) {
                        $template_map = $this->importTemplates($import_data);
                    }
                    $this->importSchedules($import_data, $storage_map, $template_map);
                    break;
                case 'storages':
                    if ($storage_map === null) {
                        $storage_map = $this->importStorages($import_data);
                    }
                    break;
                case 'templates':
                    if ($template_map === null) {
                        $template_map = $this->importTemplates($import_data);
                    }
                    break;
                case 'settings':
                    $this->importSettings($import_data);
                    break;
                case 'secure_settings':
                    $this->importSecureSettings($import_data);
                    break;
                default:
                    throw new Exception("Unknown import type {$import_type} detected.");
            }
        }
    }

    private function importSettings($import_data)
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        $global->set_from_data($import_data->settings);
        $global->save();
    }

    private function importSecureSettings($import_data)
    {
        $sglobal = DUP_PRO_Secure_Global_Entity::getInstance();
        $sglobal->setFromData($import_data->settings);
        $sglobal->save();
    }

    private function importTemplates($import_data)
    {
        $template_map = array();
        foreach ($import_data->templates as $template_data) {
            $old_id = $template_data->getId();
            if ($template_data->is_default) {
                $template = DUP_PRO_Package_Template_Entity::get_default_template();
            } else {
                $template = DUP_PRO_Package_Template_Entity::createFromImportData($template_data);
                $template->save();
            }
            $template_map[$old_id] = $template->getId();
        }
        return $template_map;
    }

    private function importSchedules($import_data, $storage_map, $template_map)
    {
        foreach ($import_data->schedules as $schedule_data) {
            $schedule = DUP_PRO_Schedule_Entity::create_from_data($schedule_data);

            for ($i = 0; $i < count($schedule->storage_ids); $i++) {
                $old_storage_id = $schedule->storage_ids[$i];
                $schedule->storage_ids[$i] = $storage_map[$old_storage_id];
            }

            $schedule->template_id = $template_map[$schedule->template_id];
            $schedule->save();
        }
    }

    private function importStorages($import_data)
    {
        $storage_map = array();
        // Default always maps to default
        $storage_map[DUP_PRO_Virtual_Storage_IDs::Default_Local] = DUP_PRO_Virtual_Storage_IDs::Default_Local;
        // Construct associative array with index=old storage id and value=new storage index
        foreach ($import_data->storages as $storage_data) {
            // Skip default storage
            if ($storage_data->id !== DUP_PRO_Virtual_Storage_IDs::Default_Local) {
                $storage = DUP_PRO_Storage_Entity::create_from_data($storage_data, true);
                $old_id = $storage->id;
                $storage->id = -1;
                $storage->save();
                $storage_map[$old_id] = $storage->id;
            }
        }

        return $storage_map;
    }
}

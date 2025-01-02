<?php

namespace hakuryo\ConfigParser;

use hakuryo\ConfigParser\exceptions\FileNotFoundException;
use hakuryo\ConfigParser\exceptions\InvalidSectionException;
use hakuryo\ConfigParser\exceptions\MandatoryKeyException;
use hakuryo\ConfigParser\exceptions\UnsupportedFileTypeException;
use \JsonException;

class ConfigParser
{

    /**
     * @throws FileNotFoundException
     * @throws UnsupportedFileTypeException
     * @throws InvalidSectionException
     * @throws JsonException
     */
    public static function parse($path, $section = null, array $mandatoryKeys = [])
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException("File $path not found or is not readable");
        }
        if (!is_file($path)) {
            throw new FileNotFoundException("provided path $path is not a file");
        }
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $conf = null;
        switch ($ext) {
            case "json":
                if (mime_content_type($path) === "application/json") {
                    $conf = self::parseJSON($path, $section);
                } else {
                    throw new UnsupportedFileTypeException("Config file is not a json file or the JSON syntaxe is invalide");
                }
                break;
            case "ini":
                $conf = self::parseINI($path, $section);
                break;
        }
        if ($conf === null) {
            throw new UnsupportedFileTypeException("Unsupported config file type must be 'json' or 'ini'");
        }
        self::verifyKeys($conf, $mandatoryKeys);
        return $conf;
    }

    private static function sectionExist($config, $section): void
    {
        if (is_array($config)) {
            if (!array_key_exists($section, $config)) {
                throw new InvalidSectionException("The provided section '$section' does not exist");
            }
        }
        if (is_object($config)) {
            if (!property_exists($config, $section)) {
                throw new InvalidSectionException("The provided section '$section' does not exist");
            }
        }
    }

    /**
     * @throws InvalidSectionException
     */
    private static function parseINI($path, $section)
    {
        if ($section === null) {
            $rawConf = parse_ini_file($path);
        } else {
            $rawConf = parse_ini_file($path, true);
            self::sectionExist($rawConf, $section);
            $rawConf = parse_ini_file($path, true)[$section];
        }
        return json_decode(json_encode($rawConf));
    }

    /**
     * @throws InvalidSectionException
     * @throws JsonException
     */
    private static function parseJSON($path, $section): \stdClass
    {
        $rawConf = json_decode(file_get_contents($path), false, 512, JSON_THROW_ON_ERROR);
        if (!$rawConf) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }
        if ($section != null) {
            self::sectionExist($rawConf, $section);
            $rawConf = $rawConf->$section;
        }
        return $rawConf;
    }

    private static function verifyKeys(\stdClass $config, array $mandatoryKeys)
    {
        foreach ($mandatoryKeys as $key) {
            if (!property_exists($config, $key)) {
                throw new MandatoryKeyException("You must provide a file with the followings keys '" . implode("','", $mandatoryKeys) . "'");
            }
        }
    }
}

<?php

namespace modules\config\components\interfaces;

/**
 * Interface ConfigInterface
 *
 * class ConfigParams implements ConfigInterface
 * {
 *      public static function findDefaultParams()
 *      {
 *          return [
 *              [
 *                   'param' => 'SITE_NAME',
 *                   'label' => 'Site Name',
 *                   'value' => '',
 *                   'type' => 'string',
 *                   'default' => 'My Site',
 *               ],
 *              // etc.
 *          ];
 *      }
 *
 *      public static function getReplace()
 *      {
 *          return [
 *              'name' => 'SITE_NAME',
 *              // etc.
 *          ];
 *      }
 * }
 *
 * @package modules\config\components\interfaces
 */
interface ConfigInterface
{
    /**
     * Return config params array
     * @return array
     */
    public static function findParams();

    /**
     * Set associate key config app to params
     * @return array
     */
    public static function getReplace();
}

<?php

namespace Gebruederheitz\Wordpress;

use Gebruederheitz\Wordpress\Rest\RestRoute;
use Gebruederheitz\Wordpress\Rest\Traits\withREST;
use WP_REST_Request;

class ThemeModExporter
{
    use withREST;

    public const MENU_SLUG = 'ghwp-theme-mods-export';

    protected const MENU_LOCATION = 'themes.php';

    protected const MENU_TITLE = 'Customizer-Export';

    protected const MENU_TITLE_NAMESPACE = 'ghwp';

    protected $title = 'Customizer-Einstellungen Exportieren und Importieren';


    public static function init()
    {
        self::initRestApi();

        if (is_admin()) {
            add_action('admin_menu', [self::class, 'onAdminMenu']);
        }
    }

    public static function onAdminMenu()
    {
        self::registerSubmenu();
    }

    public static function renderSubmenu()
    {
        load_template(__DIR__ . '/../templates/customizer-export.php');
    }

    protected static function registerSubmenu()
    {
        add_submenu_page(
            static::MENU_LOCATION,
            __(static::MENU_TITLE, static::MENU_TITLE_NAMESPACE),
            __(static::MENU_TITLE, static::MENU_TITLE_NAMESPACE),
            'edit_posts',
            static::MENU_SLUG,
            [self::class, 'renderSubmenu']
        );
    }

    public static function exportCurrentThemeMods(WP_REST_Request $request): array
    {
        $mods = get_theme_mods();
        $string = json_encode($mods);

        return [$string];
    }

    public static function importCurrentThemeMods(WP_REST_Request $request): array
    {
        $files = $request->get_file_params();
        if (empty($files['file']['tmp_name'])) throw new \Exception('no file');

        $string = file_get_contents($files["file"]['tmp_name']);
        if (empty($string)) throw new \Exception('invalid file');

        $data = json_decode($string, true);
        if (empty($data) || !is_array($data)) throw new \Exception('invalid file contents');

        $mods = get_theme_mods();
        $hasUpdates = false;

        foreach ($data as $name => $value) {
            // Only process options with a "ghwp" prefix
            if (substr($name, 0, 4) !== 'ghwp') continue;

            $old_value = $mods[$name] ?? null;
            // Nothing to change
            if ($old_value === $value) continue;

            $hasUpdates = true;
            $mods[$name] = apply_filters( "pre_set_theme_mod_$name", $value, $old_value );
        }

        if ($hasUpdates) {
            $theme  = get_option('stylesheet');
            $result = update_option("theme_mods_$theme", $mods);

            if ( ! $result) {
                throw new \Exception('Settings could not be set');
            }
        }

        return [
            'Done.'
        ];
    }

    protected static function getRestRoutes(): array
    {
        return [
            RestRoute::create(
                'Export the current theme_mods (customizer settings)',
                '/theme-mods/export'
            )
                ->setCallback([self::class, 'exportCurrentThemeMods'])
                ->allowOnlyEditors(),
            RestRoute::create(
                'Import JSON to override the current theme mods (customizer settings)',
                '/theme-mods/import'
            )
                ->setMethods('POST')
                ->setCallback([self::class, 'importCurrentThemeMods'])
                ->setPermissionCallback(function () {
                    return current_user_can('manage_options');
                }),
        ];
    }

    protected function getInstanceRestRoutes(): array
    {
        return [];
    }
}

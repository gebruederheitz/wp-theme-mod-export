# Wordpress Theme Mod Export

_Export & Import theme_mods / customizer settings._

---

## Installation

via composer:
```shell
> composer require gebruederheitz/wp-theme-mod-export
```

Make sure you have Composer autoload or an alternative class loader present.

## Usage

```php
# functions.php (or controller class)
use Gebruederheitz\Wordpress\ThemeModExporter;

ThemeModExporter::init();
```

This will set up the utility page allowing users to export theme_mods (which
Customizer settings are usually stored under, e.g. in `gebruederheitz/wp-easy-customizer`)
to a JSON file, and to import one of those files into the currently selected
theme. 
The page will appear in the "Design" section of the Wordpress backend at 
`/wordpress/wp-admin/themes.php?page=ghwp-theme-mods-export`.



## Development

### Dependencies

- PHP >= 7.4
- [Composer 2.x](https://getcomposer.org)
- [NVM](https://github.com/nvm-sh/nvm) and nodeJS LTS (v16.x)
- Nice to have: GNU Make (or drop-in alternative)

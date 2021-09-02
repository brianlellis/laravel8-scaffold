<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Scaffold extends Command
{
  protected $signature    = 'scaffold:front-end
                            {tailwind? : Defines Tailwind as the fe css resource} 
                            {--I|install : Installs Dependencies into the system}
                            {--U|uninstall : Uninstalls Dependencies from the system}
                            ';
  protected $description  = 'Installs the front-end tools needed';

  // Execute the console command
  public function handle ( ): void
  {
    if ( $this->argument('tailwind') ) {
      if ( $this->option( 'install' ) ) {
        $this->css_tailwind_install();
      } elseif ( $this->option( 'uninstall' ) ) {
        $this->css_tailwind_uninstall(); 
      }
    }
  }

  protected function flush_node_modules ( ): void
  {
    tap( new Filesystem , function ( $files ) {
      $app_root = base_path();
      $files->deleteDirectory( $app_root . '/node_modules' );
      $files->delete( $app_root . '/yarn.lock' );
      $files->delete( $app_root . '/package-lock.json' );
    });
  }

  protected function eval_tailwind_depencies ( ): string
  {
    return 'tailwindcss@latest postcss@latest autoprefixer@latest';
  }

  protected function css_tailwind_install ( ): void
  {
    if ( file_exists( base_path() . '/tailwind.config.js' ) ) {
      $this->info( 'Tailwind seems to already be installed' );
    } else {
      $this->npm_install();
      $this->npm_install_dependencies( $this->eval_tailwind_depencies() ); 
      $this->css_tailwind_config_create();

      if ( ! $sass_install = $this->sass_install() ) {
        $this->css_add_tailwind_app();
      }

      $this->webpack_mix_create( $sass_install , $this->browsersync_config() );
    }
  }

  protected function css_tailwind_uninstall ( ): void
  {
    $this->npm_uninstall_dependencies( $this->eval_tailwind_depencies() );
    ( new Filesystem )->delete( base_path() . '/tailwind.config.js' );

    if ( file_exists( base_path() . '/resources/sass' ) ) {
      $str_confirm = 'Do you wish to remove your SASS structure';
      if ( $this->confirm( $str_confirm ) ) {
        $this->sass_files_dir_remove();
        $this->webpack_mix_original_create();
        $this->postcss_plugins_uninstall();
      }
    }
  }

  protected function css_tailwind_config_create ( ): void
  {
    $file_path  = base_path() . '/tailwind.config.js';
    $content    = <<<'EOD'
    module.exports = {
      mode: 'jit',
      purge: [
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Modules/**/Views/**/*.blade.php',
        './app/Modules/**/Views/**/*.js',
        './app/Modules/**/Views/**/*.vue',
      ],
      darkMode: false, // or 'media' or 'class'
      theme: {
        extend: {},
      },
      variants: {
        extend: {},
      },
      plugins: [],
    }
    EOD;

    file_put_contents( $file_path , $content );
  }

  protected function css_add_tailwind_app ( ): void
  {
    $content = <<<'EOD'
    @tailwind base;
    @tailwind components;
    @tailwind utilities;
    EOD;

    file_put_contents( base_path() . '/resources/css/app.css' , $content );
  }

  protected function sass_install ( ): bool
  {
    $nl = "\n";
    $str_confirm  = 'Do you wish to install SASS alongside Tailwind?'.$nl.$nl;
    $str_confirm .= 'This is generally not needed as the following post-CSS plugins'.$nl;
    $str_confirm .= 'will give you the most common SASS usages:'.$nl;
    $str_confirm .= 'https://github.com/csstools/postcss-nesting'.$nl;
    $str_confirm .= 'https://github.com/postcss/postcss-custom-selectors'.$nl;
    $str_confirm .= 'https://github.com/postcss/postcss-custom-media'.$nl;
    $str_confirm .= 'https://github.com/postcss/postcss-media-minmax'.$nl;

    if ( $this->confirm( $str_confirm ) ) {
      $this->sass_files_dir_create();
      return true;
    }
    
    $this->postcss_plugins_install();
    return false;
  }

  protected function sass_files_dir_create ( ): void
  {
    $dir_sass = base_path() . '/resources/sass';
    if ( file_exists( $dir_sass ) ) {
      $this->info( 'It seems a SASS structure already resides in your system' );
    } else {
      mkdir( $dir_sass , 0755 );
      mkdir( $dir_sass . '/abstracts' ,   0755 );
      mkdir( $dir_sass . '/vendors' ,     0755 );
      mkdir( $dir_sass . '/base' ,        0755 );
      mkdir( $dir_sass . '/layout' ,      0755 );
      mkdir( $dir_sass . '/components' ,  0755 );
      mkdir( $dir_sass . '/pages' ,       0755 );
      mkdir( $dir_sass . '/themes' ,      0755 );

      file_put_contents( $dir_sass . '/app.scss' , $this->sass_7_1_architecture() );
      file_put_contents( $dir_sass . '/abstracts/_variables.scss' , '// placehold' );
      file_put_contents( $dir_sass . '/abstracts/_mixins.scss' ,    '// placehold' );
      file_put_contents( $dir_sass . '/base/placehold.txt' ,        '' );
      file_put_contents( $dir_sass . '/base/placehold.txt' ,        '' );
      file_put_contents( $dir_sass . '/layout/placehold.txt' ,      '' );
      file_put_contents( $dir_sass . '/components/placehold.txt' ,  '' );
      file_put_contents( $dir_sass . '/pages/placehold.txt' ,       '' );
      file_put_contents( $dir_sass . '/themes/placehold.txt' ,      '' );
    }
  }

  protected function sass_files_dir_remove ( ): void
  {
    ( new Filesystem )->deleteDirectory( base_path() . '/resources/sass' );
  }

  protected function sass_7_1_architecture ( ): string
  {
    $content = <<<'EOD'
    @tailwind base;
    @tailwind components;
    @tailwind utilities;

    @import 'abstracts/variables';
    @import 'abstracts/mixins';

    // @import 'vendors/bootstrap';

    // @import 'base/reset';
    // @import 'base/typography';

    // @import 'layout/navigation';
    // @import 'layout/grid';
    // @import 'layout/header';
    // @import 'layout/footer';
    // @import 'layout/sidebar';
    // @import 'layout/forms';

    // @import 'components/buttons';

    // @import 'pages/home';

    // @import 'themes/theme';
    // @import 'themes/admin';
    EOD;

    return $content;
  }

  protected function postcss_plugins_install ( ): void
  {
    $dependencies   = 'postcss-media-minmax postcss-custom-media ';
    $dependencies  .= 'postcss-custom-selectors postcss-nesting postcss-import';
    shell_exec( 'npm install -D ' . $dependencies );
    $this->info( 'Adding postcss.config.js' );
    $this->postcss_config_create();
  }

  protected function postcss_plugins_uninstall ( ): void
  {
    if ( base_path() . '/postcss.config.js' ) {
      $dependencies   = 'postcss-media-minmax postcss-custom-media ';
      $dependencies  .= 'postcss-custom-selectors postcss-nesting postcss-import';
      shell_exec( 'npm uninstall ' . $dependencies );
    
      ( new Filesystem )->delete( base_path() . '/postcss.config.js' );
    }
  }

  protected function postcss_config_create ( ): void
  {
    $file_path  = base_path() . '/postcss.config.js';
    $content    = <<<'EOD'
    module.exports = {
      plugins: [
        require("tailwindcss"),
        require('postcss-import'),
        require('postcss-nesting'),
        require('postcss-custom-selectors'),
        require('postcss-custom-media'),
        require('postcss-media-minmax')
      ]
    }
    EOD;

    file_put_contents( $file_path , $content );
  }

  protected function browsersync_config ( ): bool
  {
    $str_confirm = 'Do you wish to configure your BrowserSync for Laravel?';
    return $this->confirm( $str_confirm );
  }

  protected function browsersync_proxy ( ): string
  {
    $cur_uri = config('app.url');
    $str_confirm = "Do you wish to use {$cur_uri} as your proxy uri?";

    if ( $this->confirm( $str_confirm ) ) {
      return $cur_uri;
    } else {
      $new_uri = $this->ask('What URI would you like to use?');
      $this->dot_env_update( 'APP_URL' , $new_uri );
      return $new_uri;
    }
  }

  protected function dot_env_update ( string $key, string $value ): void
  {
    $str_confirm = "Do you wish to update .env {$key} with {$value}?";

    if ( $str_confirm ) {
      $path       = app()->environmentFilePath();
      $env        = file_get_contents( $path );
      $old_value  = env( $key );

      if ( !str_contains( $env , $key . '=' ) ) {
        $env .= sprintf("%s=%s\n", $key, $value);
      } else {
        if ($old_value) {
          $str_search   = sprintf( '%s=%s' , $key , $old_value );
          $str_replace  = sprintf( '%s=%s' , $key , $value );
        } else {
          $str_search   = sprintf( '%s=' , $key );
          $str_replace  = sprintf( '%s=%s' , $key , $value );
        }

        $env  = str_replace( $str_search , $str_replace , $env );
      }

      file_put_contents( $path , $env );
    }
  }

  protected function npm_install ( ): void
  {
    if ( file_exists( base_path() . '/node_modules' ) ) {
      $str_confirm = 'npm install has already been ran. Do you want to want to reinstall?';
      if ( $this->confirm( $str_confirm ) ) {
        $this->flush_node_modules();
        shell_exec( 'npm install' );
      } else {
        $this->info( 'node_modules directory left untouched' );
      }
    } else {
      shell_exec( 'npm install' );
    }
  }

  protected function npm_install_dependencies ( string $dependencies ): void
  {
    shell_exec('npm install -D ' . $dependencies );
  }

  protected function npm_uninstall_dependencies ( string $dependencies ): void
  {
    $str_clean = preg_replace( '/@[^\s]+/' , '' , $dependencies );
    shell_exec( 'npm uninstall ' . $str_clean );
  }

  protected function webpack_mix_create ( bool $sass_present , bool $browser_sync ): void
  {
    $file_path  = base_path() . '/webpack.mix.js';
    $sync_proxy = $browser_sync ? $this->browsersync_proxy() : false;

    $content = <<<'EOD'
    const mix         = require('laravel-mix');
    const tailwindcss = require('tailwindcss');
    EOD;

    if ( $browser_sync && false !== stripos( $sync_proxy , 'https://' ) ) {
      $domain   = str_replace( 'https://' , '', $sync_proxy );
      $str_js   = "homedir + '/.valet/Certificates/{$domain}'";
      $str_js2  = "homedir + '/.config/valet/Certificates/{$sync_proxy}'";
      $content .= <<<EOD
      \nconst fs          = require('fs');
      const homedir     = require('os').homedir();
      const valet_cert  = 'Certificates/{$domain}';

      if ( fs.existsSync( homedir + '/.valet/' + valet_cert + '.key' ) ) {
        var ssl_cert_path = homedir + '/.valet/' + valet_cert; 
      } else if ( fs.existsSync( homedir + '/.config/valet/' + valet_cert + '.key' ) ) {
        var ssl_cert_path = homedir + '/.config/valet/' + valet_cert; 
      }

      mix.js('resources/js/app.js', 'public/js')
      EOD;
    } else {
      $content .= "\n  mix.js('resources/js/app.js', 'public/js')"; 
    }

    if ( $sass_present ) {
      $content .= <<<EOD
        \n  .sass('resources/sass/app.scss', 'public/css')
        .options({
            postCss: [ tailwindcss('./tailwind.config.js') ],
        })
        .version()
      EOD;
    } else {
      $content .= "\n  .postCss('resources/css/app.css', 'public/css')";
    }

    if ( $browser_sync ) {
      if ( false !== stripos( $sync_proxy , 'https://' ) ) {
        $str_js = "homedir + '/.valet/Certificates/' + '{$sync_proxy}'";
        $content .=<<<EOD
          \n  .browserSync({
            proxy: '{$sync_proxy}',
            host: {$domain},
            open: 'external',
            https: {
              key:  ssl_cert_path + '.key',
              cert: ssl_cert_path + '.crt',
            }
          });
        EOD;
      } else {
        $content .=<<<EOD
          \n  .browserSync({
            proxy: '{$sync_proxy}'
          });
        EOD;
      }
    } else {
      $content .= ';';
    }

    file_put_contents( $file_path , $content );
  }

  protected function webpack_mix_original_create ( ): void
  {
    $file_path  = base_path() . '/webpack.mix.js';

    $content    = <<<'EOD'
    const mix         = require('laravel-mix');

    mix.js('resources/js/app.js', 'public/js')
      .postCss('resources/css/app.css', 'public/css', [
          //
      ]);
    EOD;

    file_put_contents( $file_path , $content );
  }
}

<?php

require_once base_path() . '/Scaffold/ControllerData.php';
require_once base_path() . '/Scaffold/ControllerMapper.php';

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
eval_controller_map_existence( $controller_mapper );

aggregate_scaffold_routes();

$use_files_routing  = \DB::table( 'settings_site' )
                        ->where( 'id' , 'file_route_binding' )
                        ->first();

if ( $use_files_routing && 1 === (int) $use_files_routing->value ) {
  $is_admin   = determine_admin_route();
  $middleware = middleware_default( $is_admin );

  Route::fallback( function () use ( $is_admin ) {
    return determine_view( $is_admin );
  })->middleware( $middleware );
}

function aggregate_scaffold_routes ( ): void
{
  $app_root       = base_path();
  $module_routes  = glob( $app_root . '/app/Modules/*/web.php' );
  
  foreach ( $module_routes as $route_file ) {
    require_once $route_file;
  }
}

function determine_admin_route ( ): bool
{
  $admin_path = request()->path();

  if ( 'admin/' === substr( $admin_path , 0 , 6 ) ) {
    return true;
  }
  return false;
}

function middleware_default ( bool $is_admin = false ): array
{
  $default_middleware = [];

  if ( $is_admin ) {
    $default_middleware[]       = 'auth';
    $require_email_verification = \DB::table( 'settings_site' )
                                    ->where( 'id' , 'auth_email_verification' )
                                    ->first();

    if ( $require_email_verification ) {
      $default_middleware[] = 'verified';
    }
  }

  return $default_middleware;
}

/**
 * PUBLIC FACING PAGES
**/
function existing_redirects ( string $cur_path ): void
{
  $route = \DB::table( 'redirectors' )->where( 'entering_route' , $cur_path )->first();
  
  if ( $route ) {
    $view_header = intval( $route->action );
    Redirect::to($route->target_route, $view_header);
  }
}

function eval_db_cms_page ( string $cur_uri ): object|bool
{
  $cur_uri    = '/' . $cur_uri;
  $db_record  = \DB::table( 'cms_pages' )->where( 'url_slug' , $cur_uri )->first();

  if ( !$db_record ) {
    $db_record = \DB::table( 'cms_blog_posts' )->where( 'url_slug' , $cur_uri )->first();    
  }
  
  if ( !$db_record ) {
    return false;    
  }

  return $db_record;
}

function eval_blade_file_existence ( bool $is_admin = false ): string|bool
{
  $blade_str = str_replace( '/' , '.' , request()->path() );

  if ( $is_admin ) {
    $rm_admin_str = str_replace( 'admin.' , '' , $blade_str );
    $module_path  = 'module_admin::' . $rm_admin_str;

    if  ( View::exists( $module_path ) ) {
      return $module_path;
    }
  } else {
    $blade_path   = 'module_public::' . $blade_str;

    if ( View::exists( $blade_path ) ) {
      return $blade_path;
    } else {
      $blade_path = 'theme::' . $blade_str;

      if ( View::exists( $blade_path ) ) {
        return $blade_path;
      }
    }
  }

  return false;
}

function determine_view ( bool $is_admin = false ): object
{
  /**
   * ORDER OF OPERATIONS
   * ADMIN (AUTH) PAGES
   * 1. PRESENT BLADE FILE
   *  a. module
   * 
   * PUBLIC FACING PAGES
   * 1. Existing Redirects
   * 2. DB_CMS_PAGE
   * 3. DB_CMS_BLOGPOST
   * 4. PRESENT BLADE FILE
   *  a. resources
   *  b. module
  **/
  $blade_path   = request()->path();
  $view_header  = 200;

  if ( !$is_admin ) {
    existing_redirects( $blade_path );
  }
  if ( !$is_admin && $db_cms_page = eval_db_cms_page( $blade_path ) ) {
    // code
  }
  
  $blade_view = eval_blade_file_existence( $is_admin );

  if ( $blade_view ) {
    return view( $blade_view );
  } else {
    if ( $db_record = eval_db_cms_page( '404' ) ) {
      // code
    }
    return view( 'theme::404' );
  }
}

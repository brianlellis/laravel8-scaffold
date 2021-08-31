<?php

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
$use_files_routing  = \DB::table( 'settings_site' )
                        ->where( 'id' , 'file_route_binding' )
                        ->first();

if ( $use_files_routing && 1 === (int) $use_files_routing->value ) {
  Route::fallback( function () {
    return determine_view_public();
  });
}

function aggregate_rapyd_routes ( ): void
{
  $app_root       = base_path();
  $module_routes  = glob( $app_root . '/app/Rapyd/Modules/*/web.php' );
  $theme_routes   = glob( $app_root . '/resources/*/routes/*.{php}' , GLOB_BRACE );
  
  foreach ( [ $module_routes , $theme_routes ] as $arr_section ) {
    foreach ( $arr_section as $route_file ) {
      require_once $route_file;
    }
  }
}

function determine_admin_route_depth ( ): string // string|bool
{
  $admin_path = request()->path();

  if ( count ( $admin_path ) && 'admin' === $admin_path[0] ) {
    return '/' . implode( '/' , $admin_path );
  }
  return false;
}

function apply_middleware_admin ( string $admin_path ): void
{
  Route::group( [ 'middleware'  => 'auth , verified' ] , function ( ) use ( $admin_path ) {
    Route::get( $admin_path , function ( ) {
      return view( 'rapyd_admin::master' );
    });
  });
}

/**
 * PUBLIC FACING PAGES
**/
function existing_redirects ( string $cur_path )
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
    $db_record  = \DB::table( 'cms_blog_posts' )->where( 'url_slug' , $cur_uri )->first();    
  }
  
  if ( !$db_record ) {
    return false;    
  }

  return $db_record;
}

function eval_blade_file_existence ( string $cur_uri ): string|bool
{
  $blade_path   = str_replace( '/' , '.' , request()->path() );
  $module_path  = 'rapyd_module_public::' . $blade_path;
  $module_blade = View::exists( $module_path );

  if ( !$module_blade ) {
    $module_path = 'theme::' . $blade_path;
    
    if ( View::exists( $module_path ) ) {
      return $module_path;
    }
  } else {
    return $module_path;
  }
  return false;
}

function determine_view_public ( ): object
{
  /**
   * ORDER OF OPERATIONS
   * 1. Existing Redirects
   * 2. DB_CMS_PAGE
   * 3. DB_CMS_BLOGPOST
   * 4. PRESENT BLADE FILE
   *  a. resources
   *  b. module
  **/
  $blade_path   = request()->path();
  existing_redirects( $blade_path );
  
  $view_header  = 200;
  $blade_view   = eval_blade_file_existence( $blade_path );

  $db_cms_page  = eval_db_cms_page( $blade_path );
  if ( $db_cms_page ) {
    // code
  }

  if ( $blade_view ) {
    return view( $blade_view );
  } else {
    $db_record  = \DB::table( 'cms_pages' )->where( 'url_slug' , '404' )->first();

    if ( $db_record ) {
      // code
    }
    return view( 'theme::404' );
  }
}

function process_route_to_blade ( ): void
{
  aggregate_rapyd_routes();
  $admin_path = determine_admin_route_depth();

  if ( $admin_path ) {
    apply_middleware_admin();
  } else {
    determine_view_public();
  }
}
<?php

// If not found send back empty array to be passed to the view
function eval_controller_map_existence ( array $controller_mapper ): array
{
  return $controller_mapper[ request()->path() ] ?? [];
}

function eval_controller_map_value ( array $controller_value ): mixed
{
  $request_method = request()->method();

  if ( strtolower( $request_method ) === strtolower( $controller_value[ 'method' ] ) ) {
    $controller   = explode( '@' , $controller_value[ 'controller' ] );
    $folder_class = explode( ':' , $controller [ 0 ] );
    $folder       = $folder_class[ 0 ];
    $class_str    = $folder_class[ 1 ];

    require_once base_path() . "/app/Modules/{$folder}/Controllers/{$class_str}.php";

    $class        = new $class_str();
    $method       = $controller[ 1 ];
    $method_value = $class->$method();

    if ( !is_array( $method_value ) ) {
      return [ 'data' => $method_value ];
    }
    return $method_value;
  }
}

function eval_controller_file_existence ( ): string|bool
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
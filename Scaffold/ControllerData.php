<?php

function eval_controller_map_existence ( $controller_mapper ): string|bool
{
  dd( $controller_mapper );
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
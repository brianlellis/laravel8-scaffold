<?php

function blade_content ( object $content_blade , array $blade_mapper ): string
{
  $wrapper_data = eval_wrapper_usage( $blade_mapper );

  if ( $wrapper_data ) {
    $return_str = '';
    foreach ( $wrapper_data as $value ) {
      $return_str .= $value ? view( $value ) : $content_blade;
    }

    return compile_html_doc( $return_str );
  }
  return compile_html_doc( $content_blade , true );
}

function eval_wrapper_usage ( array $blade_mapper ): array|bool
{
  $cur_uri = request()->path();
  if ( isset( $blade_mapper[ $cur_uri ] ) ) {

  } elseif ( isset( $blade_mapper[ 'all' ] ) ) {
    return order_blade_mapper( $blade_mapper[ 'all' ] );
  }
  return false;
}

function order_blade_mapper ( array $wrapper_data ): array
{
  $return_arr = [];

  usort( $wrapper_data , function ( $a , $b ) {
    return (int) $a[ 'order' ] <=> (int) $b[ 'order' ];
  });

  foreach ( $wrapper_data as $value ) {
    $return_arr[] = $value[ 'blade' ];
  }

  return $return_arr;
}

function compile_html_doc ( string|object $html_content , bool $use_head = false ): string
{
  $str_wrap   = '<!DOCTYPE html><html lang="en">';
  $str_wrap  .= $html_content;
  $str_wrap  .= '</body></html>';

  return $str_wrap;
}

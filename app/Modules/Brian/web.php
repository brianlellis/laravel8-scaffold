<?php

Route::name('user.auth')->prefix('/auth/')->group(function () {
  Route::post('login',                '\UserAuth@login')->name('login');
  Route::get('logout',                '\UserAuth@logout')->name('logout');
  Route::post('register',             '\UserAuth@register')->name('register');
  Route::get('verify-email/{key}',    '\UserAuth@email_verify')->name('email.verify');
  Route::post('reset-pass-request',   '\UserAuth@pass_reset_request')->name('pass.reset.request');
  Route::post('reset-pass-set/set',   '\UserAuth@pass_reset_set')->name('pass.reset.set');
});
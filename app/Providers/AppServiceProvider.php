<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('min_words', 'App\Validators\MinWordsValidator@validate', 'The :attribute must have at last :min words.');
        Validator::extend('document', 'App\Validators\DocumentValidator@validate', 'The :attribute is not a valid document.');
    }
}

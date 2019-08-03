<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Session;
use URL;

class LocaleController extends Controller
{
public function setLocale($locale='en'){
    if (!in_array($locale, ['en', 'hr', 'test'])){
        $locale = 'en';
    }
    Session::put('locale', $locale);
    app()->setLocale($locale);
    // return redirect(url(URL::previous()));
    }
}
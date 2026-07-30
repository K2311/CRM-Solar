<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicPagesController extends Controller
{
    public function metaSetupGuide()
    {
        return view('public.meta_setup');
    }

    public function privacyPolicy()
    {
        return view('public.privacy_policy');
    }

    public function termsOfService()
    {
        return view('public.terms');
    }
}

<?php

namespace Phplite\Validation;

use Phplite\Http\Request;
use Phplite\Session\Session;
use Phplite\Url\Url;
use Rakit\Validation\Validator;


class Validate
{
    //Validate Request (response  bool json )
    public static function validate(array  $rules, $json)
    {
        $validator = new Validator;

        $validation = $validator->validate($_POST + $_FILES, $rules);

        $errors = $validation->errors();

        if ($validation->fails()) {
            //Two types of response
            if ($json) {

                return ['errors' => $errors->firstOfAll(),];
            } else {

                Session::set('errors', $errors);
                Session::set('oldData', Request::all());

                //back to the previous page
                return Url::redirect(Url::previous());
            }
        }
    }
}
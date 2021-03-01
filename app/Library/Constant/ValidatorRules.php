<?php


namespace App\Library\Constant;


class ValidatorRules
{
    const RULES = [
        'name'      =>  'required|string',
        'title'     =>  'required|string',
        'cover'     =>  'required|cover',

        'answers'   =>  'required|array',
        'target_url'=>  'active_url',

        'email'     =>  'email',
        'online_time'   =>  'required|date',
        'offline_time'   =>  'required|date',

        'group_number'          =>  'required|numeric|min:0',
        'valid_group_number'    =>  'required|numeric|min:0',

        'code'      =>  'required|string|min:2|max:5',
        'mobile'    =>  'required|string|min:8|max:13',
        'page_mark' =>  'required|string|min:32',
        'message_id'        =>  'required|numeric|min:1',
        'questionnaire_id'  =>  'required|numeric|min:1',
        'department_id'     =>  'required|numeric|min:1',

    ];
}
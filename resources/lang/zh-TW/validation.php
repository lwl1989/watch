<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted'             => '必須接受 :attribute',
    'active_url'           => ':attribute 必須是可使用的URL地址',
    'after'                => ':attribute 必須是在 :date 之後的日期',
    'after_or_equal'       => ':attribute 必須是在 :date 之後或者等於 :date 的日期',
    'alpha'                => ':attribute 只能包含英文字母',
    'alpha_dash'           => ':attribute 只能包含英文字母，數字和-',
    'alpha_num'            => ':attribute 只能包含英文字母和數字',
    'array'                => ':attribute 必須是陣列',
    'before'               => ':attribute 必須是在 :date. 之前的日期',
    'before_or_equal'      => ':attribute 必須是在 :date. 之前或者等於 :date 的日期',
    'between'              => [
        'numeric' => ':attribute 必須介於 :min 至 :max 之間',
        'file'    => ':attribute 大小必須介於 :min kb 至 :max kb 之間',
        'string'  => ':attribute 長度必須介於 :min 至 :max 之間',
        'array'   => ':attribute 包含的長度必須介於 :min 至 :max 個之間',
    ],
    'boolean'              => ':attribute 必須是 true 或 false',
    'confirmed'            => ':attribute 必須一致',
    'date'                 => ':attribute 不是有效的日期',
    'date_format'          => ':attribute 必須符合格式 :format',
    'different'            => ':attribute 與 :other 必須不同',
    'digits'               => ':attribute 必須是 :digits 位數',
    'digits_between'       => ':attribute 的位數必須介於 :min 與 :max 之間',
    'dimensions'           => ':attribute 圖片尺寸錯誤',
    'distinct'             => ':attribute 已存在',
    'email'                => ':attribute 必須是有效的電子郵件位址',
    'file'                 => ':attribute 必須是一個有效的文件',
    'exists'               => ':attribute 須存在',
    'filled'               => ':attribute 為必填',
    'image'                => ':attribute 必須是圖片',
    'in'                   => ':attribute 不是有效值',
    'in_array'             => ':attribute 不存在於 :other',
    'integer'              => ':attribute 必須是整數',
    'ip'                   => ':attribute 必須是有效的 IP 位址',
    'json'                 => ':attribute 必須是有效的 JSON 字串',
    'ipv4'                 => ':attribute 必須是一個有效的IPV4地址',
    'ipv6'                 => ':attribute 必須是一個有效的IPV6地址',
    'max'                  => [
        'numeric' => ':attribute 不能大於 :max',
        'file'    => ':attribute 的大小不能超過 :max kb',
        'string'  => ':attribute 不能超過 :max 個字元',
        'array'   => ':attribute 不能包含超過 :max 個',
    ],
    'mimes'                => ':attribute 必須是一個 :values 檔案',
    'min'                  => [
        'numeric' => ':attribute 不能小於 :min',
        'file'    => ':attribute 的大小不能小於 :min kb',
        'string'  => ':attribute 必須至少 :min 個字元',
        'array'   => ':attribute 必須至少有 :min 個',
    ],
    'mimetypes'            => ':attribute 必須是一個類型爲: :values 的文件',
    'not_in'               => ':attribute 是無效值',
    'numeric'              => ':attribute 必須是數字',
    'present'              => ':attribute 必須出現',
    'regex'                => ':attribute 格式無效',
    'required'             => ':attribute 為必填',
    'required_if'          => '當 :other 是 :value時，:attribute 為必填',
    'required_unless'      => '除非 :other 在 :values 之中，:attribute 為必填',
    'required_with'        => '當 :values 出現時，:attribute 為必填',
    'required_with_all'    => '當 :values 出現時，:attribute 為必填',
    'required_without'     => '當 :values 沒有出現時，:attribute 為必填',
    'required_without_all' => '當 :values 沒有出現時，:attribute 為必填',
    'same'                 => ':attribute 與 :other 須相符',
    'size'                 => [
        'numeric' => ':attribute 必須是 :size',
        'file'    => ':attribute 必須是 :size kb',
        'string'  => ':attribute 必須有 :size 個字元',
        'array'   => ':attribute 必須包含 :size 個',
    ],
    'string'               => ':attribute 必須是字串',
    'timezone'             => ':attribute 必須是有效的時區',
    'unique'               => ':attribute 已存在',
    'url'                  => ':attribute 必須是有效的url',
    'uploaded'             => ':attribute 上傳失敗',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes' => [],
];

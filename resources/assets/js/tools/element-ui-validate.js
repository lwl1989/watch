//自然數判斷 >= 0
const naturalNumberValidator = function (rule, value, callback) {
    let reg = /^[0-9]+.?[0-9]*$/;
    if (reg.test(value)) {
        let v = parseInt(value);
        if (v < 0) {
            callback('不能輸入非自然數[小於0的數字]');
        } else {
            callback();
        }
    } else {
        callback('輸入的不是合法的數字');
    }
};
//自然數判斷 >= 0 並且等於0
const naturalNumberDefaultValidator = function (rule, value, callback) {
    if (value === '') {
        value = 0;
        callback();
    }
    naturalNumberValidator(rule, value, callback)
};
//是否爲數字判斷
const doubleValidator = function (rule, value, callback) {
    let reg = /^[0-9]+.?[0-9]*$/;
    if (reg.test(value)) {
        callback();
    } else {
        callback('輸入的不是合法的數字');
    }
};
//判斷電話是否合格
const mobileValidator = function (rule, value, callback) {
    if (value === '') {
        callback();
    }
    if (/^[0-9]{8,}$/.test(value) != true) {
        callback('輸入的行動電話格式錯誤');
    } else {
        callback();
    }
};
//關鍵字判斷
const keywordValidator = function (rule, value, callback) {
    let n = (value.split(',')).length;

    if (n > 10) {
        callback(new Error("關鍵字不能超過10個"));
        return;
    }
    callback();

};


const stringNotZeroValidator = function (rule, value, callback) {
    let v = value.toString();

    if (v === "0") {
        callback(new Error("必須選擇當前欄位"));
        return;
    }
    callback();
};

const accountValidator = function (rule, value, callback) {
    let v = value.toString();

    if (!v.match(/^[0-9a-zA-Z]{6,30}$/i)) {
        callback(new Error("可輸入 6-30 個英文和數字，不可輸入符號"));
        return;
    }

    callback();
};

const passwordValidator = function (rule, value, callback) {
    let v = value.toString();

    if (!v.match(/^[0-9a-zA-Z]{8,30}$/i)) {
        callback(new Error("可輸入 8-30 個英文和數字，不可輸入符號"));
        return;
    }

    callback();
};

const adminRule = {
    account: [
        {required: true, message: '帳號為必填欄位'},
        {validator: accountValidator},
        {min: 6, max: 30, message: '可輸入 6-30 個英文和數字，不可輸入符號'}
    ],
    password: [
        {required: true, message: '密碼為必填欄位'},
        {validator: passwordValidator},
        {min: 8, max: 30, message: '可輸入 8-30 個英文和數字，不可輸入符號'}
    ],
    status: [
        {required: true, message: '狀態為必填欄位'}
    ],
    alias: [
        {required: false},
        {
            validator: function (rule, value, callback) {
                let v = value.toString();

                if (v && !v.match(/^[0-9a-zA-Z\u4E00-\u9FA5]{1,20}$/i)) {
                    callback(new Error("可輸入 1-20 個中英文和數字，不可輸入符號"));
                    return;
                }

                callback();
            }
        },
    ],
    name: [
        {required: false},
        {
            validator: function (rule, value, callback) {
                let v = value.toString();

                if (v && !v.match(/^[0-9a-zA-Z\\，,｜|=+ ~!@#$%^&*()_\/“:;.{}<>～！＠＃％︿＆＊（）＿－＝＼／＂：；。［﹀｛｝＋＜＞《》「」『』【】ÀàÂâÇçÉéÈèÊêËëÎîÏïÔôÛûÙùÜüŸÿ\-\[\]\u4E00-\u9FA5]{1,20}$/i)) {
                    callback(new Error("可輸入 1-20 個中英文、數字及符號"));
                    return;
                }

                callback();
            }
        },
    ],
    role: [
        {required: true, message: '角色為必填欄位'}
    ],
    tel: [
        {
            required: false, validator: function (rule, value, callback) {
                if (value && !value.match(/^[0-9]{1,30}$/)) {
                    return callback('可輸入 1-30 個數字');
                }

                callback();
            }
        }
    ],
    tel_ext: [
        {
            required: false, validator: function (rule, value, callback) {
                if (value && !value.match(/^[0-9]{1,4}$/)) {
                    return callback('可輸入 1-4 個數字');
                }

                callback();
            }
        }
    ],
    mobile: [
        {
            required: false,
            validator: function (rule, value, callback) {
                if (value && !value.match(/^((09[0-9]{8})|(9[0-9]{8}))$/)) {
                    return callback('可輸入 1-10 個數字');
                }

                callback();
            }
        }
    ],
    email: [
        {
            required: false,
            validator: function (rule, value, callback) {
                let val = value.toString();

                if (val &&
                    !val.match(/^((?:[a-zA-Z0-9\._-]){1,60}@(?:[a-zA-Z0-9_-]){1,20}\.(?:[a-zA-Z0-9._-]){1,20},?(?:(?:[a-zA-Z0-9\._-]){1,60}@(?:[a-zA-Z0-9_-]){1,20}\.(?:[a-zA-Z0-9._-]){1,20}){0,})+$/i)
                ) {
                    return callback('可輸入 1-100 個英文和數字，有多筆時用英文 "," 區隔');
                }

                callback();
            }
        }
    ],
    permissions: [
        {required: false, message: '權限為必填欄位'}
    ],
    department_id: [
        {required: true, message: '請選擇業務單位'}
    ],
    code: [{required: false, message: '國碼為必填欄位'}]
};

const shopRule = {
    account: [
        {
            required: true,
            validator: (rule, value, callback)=>{
                let val = value.toString();

                if (!val.match(/^[0-9]{8}$/i)) {
                    return callback('僅能輸入8位數字，必須剛好8位數，不多不少')
                }
                callback();
            }
        },
    ],
    password: [
        {required: true, message: '密碼為必填欄位'}
    ],
    status: [
        {required: true, message: '狀態為必填欄位'}
    ],
    type: [
        {required: true, message: '角色為必選字段'}
    ],
    name: [
        {
            required: true,
            validator: (rule, value, callback)=>
            {
                let val = value.toString();

                if (!val) {
                    return callback('商店名稱為必填欄位');
                }


                if (val && !val.match(/^[0-9a-zA-Z\\，,｜|=+ ~!@#$%^&*()_\/“:;.{}<>～！＠＃％︿＆＊（）＿－＝＼／＂：；。［﹀｛｝＋＜＞《》「」『』【】ÀàÂâÇçÉéÈèÊêËëÎîÏïÔôÛûÙùÜüŸÿ\-\[\]\u4E00-\u9FA5]{1,20}$/i)) {
                    return callback(new Error("可輸入 1-20 個中英文、數字及符號"));
                }

                callback();
            }
        }
    ],
    tel: [
        {
            required: false,
            validator: (rule, value, callback)=>{
                if (value && !value.match(/^[0-9]{1,30}$/)) {
                    return callback('可輸入 1-30 個數字');
                }

                callback();
            }
        }
    ],
    tel_ext: [
        {
            required: false,
            validator:(rule, value, callback)=>{
                if (value && !value.match(/^[0-9]{1,3}$/)) {
                    return callback('可輸入 1-3 個數字');
                }

                callback();
            }
        }
    ],
    mobile: [
        {
            required: true,
            validator: (rule, value, callback)=>{
                let val = value.toString();
                
                if (!val) {
                    return callback('手機號碼必填欄位');
                }
                
                if (val && !val.match(/^[0-9]{1,20}$/)) {
                    return callback('可輸入 1-20 個數字');
                }

                callback();
            }
        }
    ],
    exchange_code: [
        {required: true, message: "兌換驗證碼必填欄位"},
        {
            validator: (rule, value, callback) => {
                let val = value.toString();

                if (val && !val.match(/^[0-9]{4,8}$/i)) {
                    return callback('兌換驗證碼只能輸入4-8碼純數字');
                }

                callback();
            }
        }
    ],
    email:[
        {
            required: false,
            validator: function (rule, value, callback) {
                let val = value.toString();

                if (val &&
                    !val.match(/^((?:[a-zA-Z0-9\._-]){1,60}@(?:[a-zA-Z0-9_-]){1,20}\.(?:[a-zA-Z0-9._-]){1,20},?(?:(?:[a-zA-Z0-9\._-]){1,60}@(?:[a-zA-Z0-9_-]){1,20}\.(?:[a-zA-Z0-9._-]){1,20}){0,})+$/i)
                ) {
                    return callback('可輸入 1-100 個英文和數字，有多筆時用英文 "," 區隔');
                }

                callback();
            }
        }
    ],
    permissions: [{required: false}],
    address: [{required: true,message:"地址為必填欄位"}, {message: '長度限制200個字數', min: 0, max: 200}],
    self_url: [
        {
            required: false,
            validator: (rule, value, callback) => {
                let val = value.toString();

                if (val && !val.match(/^https?:\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/i)) {
                    return callback('官網網址格式有誤');
                }

                callback();
            }
        }
    ],
    facebook_url: [
        {
            required: false,
            validator: (rule, value, callback) => {
                let val = value.toString();

                if (val && !val.match(/^https?:\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/i)) {
                    return callback('粉絲專頁網址格式有誤');
                }

                callback();
            }
        }
    ],
    google_search: [{required: false}, {message: '長度限制200個字數', min: 0, max: 200}],
    cover_url: [{required: true, message: '商店頭像為必填欄位'}],
    is_accept_gold: [{required: true, message: '角色爲必填'}],
    code: [{required: true, message: '國碼為必填欄位'}]
};

const adminSearchRule = {
    name: [{required: false}, {min: 0, max: 20, message: '姓名長度不能超過 20 個字符', trigger: 'blur'}],
    account: [{required: false}, {min: 6, max: 30, message: '長度需要在 6 到 30 個字符', trigger: 'blur'}],
    mobile: [{required: false}, {min: 0, max: 20, message: '行動電話長度不能超過 20 個字符', trigger: 'blur'}],
    email: [{required: false}, {type: 'email', message: '電子郵件格式有誤', trigger: 'blur'}],
};

const goodsRule = {
    shop_id: [{required: true, message: '供應商為必填欄位'}],
    goods_num: [
        {required: false, message: '商品編號為必填欄位'},
        {
            validator: function (rule, value, callback) {
                let v = value.toString();

                if (v && !v.match(/^[0-9a-z]{1,50}$/i)) {
                    callback(new Error("必須是正確的 1-50 個英文和數字"));
                    return;
                }

                callback();
            }
        },{min: 1, max: 50, message: '長度不能超過 50 個字符'}],
    category_id: [{required: true, message: '商品類別為必填欄位'}],
    goods_name: [{required: true, message: '商品名稱為必填欄位'}, {min: 1, max: 50, message: '可輸入1-50個字數'}],
    goods_alias_name: [{required: true, message: '商品簡稱為必填欄位'}, {min: 1, max: 50, message: '長度不能超過 50 個字符'}],
    goods_price: [{required: true, validator: naturalNumberValidator, message: '必須是正確的數字'}],
    price_type: [{required: true, message: '貨幣類別為必填欄位'}],
    exchange_gold: [{required: true, type: 'number', message: '兌換金幣必須填寫,必須是正確的數字'}],
    goods_stock: [{required: true, type: 'number', message: '庫存必須填寫,必須是正確的數字'}],

    goods_intro: [{required: true, message: '詳細規格描述為必填欄位'}, {min: 1, max: 4000, message: '長度不能超過 4000 個字符'}],
    goods_remark: [{required: true, message: '注意事項為必填欄位'}, {min: 1, max: 4000, message: '長度不能超過 4000 個字符'}],
    goods_cover: [{required: true, message: '商品圖片為必填欄位'}],
    goods_unit: [{required: true, message: '包裝單位為必填欄位'}],
    keyword: [{required: false}, {validator: keywordValidator, trigger: 'blur'}],

    goods_length: [{required: false, validator: naturalNumberDefaultValidator, trigger: 'change'}],
    goods_width: [{required: false, validator: naturalNumberDefaultValidator, trigger: 'change'}],
    goods_high: [{required: false, validator: naturalNumberDefaultValidator, trigger: 'change'}],
};

const departmentSearchRule = {
    name: [
        {required: true, message: '業務單位為必填欄位'},
        {min: 0, max: 30, message: '業務單位長度不能超過 30 個字符', trigger: 'blur'}
    ]
};

const departmentRule = {
    unit_id: [{required: true, message: '隸屬單位為必填欄位'}],
    name: [
        {required: true, message: '業務單位為必填欄位'},
        {
            validator: function (rule, value, callback) {
                let val = value.toString();

                if (val && !val.match(/^[0-9a-zA-Z\\，,｜|=+ ~!@#$%^&*()_\/“:;.{}<>～！＠＃％︿＆＊（）＿－＝＼／＂：；。［﹀｛｝＋＜＞《》「」『』【】ÀàÂâÇçÉéÈèÊêËëÎîÏïÔôÛûÙùÜüŸÿ\-\[\]\\\u4E00-\u9FA5]{1,30}$/i)) {
                    callback(new Error("可輸入 1-30 個中英文、數字及符號，單位名稱不可重複"));
                    return;
                }

                callback();
            }
        }
    ],
    e_name: [
        {required: true, message: '英文單位為必填欄位'},

    ],
    mail_code: [{
        required: false, validator: function (rule, value, callback) {
            let val = value.toString();

            if (val && !val.match(/^[0-9]{1,5}$/)) {
                return callback('可輸入 1-5 個數字,不可輸入特殊元字符');
            }

            callback();
        }
    }],
    address: [{required: false}, {min: 0, max: 200, message: '地址長度不能超過 200 個字數'}],
    phone: [{
        required: false, validator: function (rule, value, callback) {
            let val = value.toString();

            if (val && !val.match(/^[0-9\\-]{1,10}$/)) {
                return callback('可輸入 1-10 個數字');
            }

            callback();
        }
    }],
    phone_ext: [
        {
            required: false, validator: function (rule, value, callback) {
                let val = value.toString();

                if (val && !val.match(/^[0-9-]{1,10}$/)) {
                    return callback('可輸入 1-10 個數字');
                }

                callback();
            }
        }
    ],
    facsimile: [{
        required: false, validator: function (rule, value, callback) {
            if (value && !value.match(/^[0-9-]{1,10}$/)) {
                return callback('可輸入 1-10 個數字');
            }

            callback();
        }
    }]
};

const departmentGroupSearchRule = {};

const departmentGroupRule = {
    department_id: [{required: true, message: '業務單位為必填欄位'}],
    fileList: [{required: true, message: '檔案為必選欄位'}],
    name: [{required: true, message: '群組名稱為必填欄位'},{min:0, max:30,message: '群組名稱長度不能超過 30 個字數', trigger: 'blur'}],
    mail_code: [{required: false}, {min: 0, max: 5, message: '郵遞區號長度不能超過 5 個字符,不可輸入特殊元字符', trigger: 'blur'}],
    address: [{required: false}, {min: 0, max: 200, message: '地址長度不能超過 200 個字數', trigger: 'blur'}],
    phone: [{
        required: false, validator: function (rule, value, callback) {
            if (value && !value.match(/^[0-9]{1,30}$/)) {
                return callback('可輸入 1-30 個數字');
            }

            callback();
        }, trigger: 'blur'
    }
    ],
    phone_ext: [
        {
            required: false, validator: function (rule, value, callback) {
                if (value && !value.match(/^[0-9]{1,20}$/)) {
                    return callback('可輸入 1-20 個數字');
                }

                callback();
            }
        }
    ],
    facsimile: [
        {
            required: false, validator: function (rule, value, callback) {
                if (value && !value.match(/^[0-9]{1,20}$/)) {
                    return callback('可輸入 1-20 個數字');
                }

                callback();
            }
        }
    ]
};

const messageSettingRule = {
    department_id: [{required: true, message: '業務單位為必填欄位'}],
    name: [{required: true, message: '業務名稱為必填欄位'}, {min: 1, max: 50, message: '業務名稱至多50碼', trigger: 'blur'}],
    send_object: [{required: true, message: '推播對象為必填欄位'}],
    content: [{required: true, message: '推播內容為必填欄位'}],
    send_time_type: [{required: true, message: '推播時間為必填欄位'}],
    send_count: [{required: true, message: '推播次數為必填欄位'}],
    space_time: [{required: false}],
    valid_time_type: [{required: true, message: '有效時間為必填欄位'}],
    end_time: [{required: true, message: '停止時間為必填欄位'}]
};

const messageSettingSearchRule = {
    symbol_gold_key: [ {
        required: false, validator: function (rule, value, callback) {
            if (value && !value.match(/^[0-9]{1,10}$/)) {
                return callback('可輸入 1-20 個數字');
            }

            callback();
        }
    }
    ]
};

const usersSearchRule = {};

const usersRule = {};

const adRule = {
    title: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 30,
        message: '名稱長度限制 25 個字數',
        trigger: 'blur'
    }],
    cover: [{required: true, message: '圖片為必填欄位'}],
    select_time: [{required: true, message: '上架時間為必填欄位'}],
    target_url: [{
        required: false, validator: function (rule, value, callback) {
            let val = value.toString();

            if (val && !val.match(/^https?:\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/i)) {
                return callback('連結網址格式有誤');
            }

            callback();
        }, trigger: 'blur'
    }],
};

const appRule = {
    title: [{required: true, message: 'APP 名稱為必填欄位'}, {
        min: 1,
        max: 30,
        message: 'APP名稱長度限制 25 個字數'
    }],
    cover: [{required: true, message: 'APP icon 為必填欄位'}],
    target_url: [{required: true, message: 'APP ID 為必填欄位'}, {
        min: 1,
        max: 100,
        message: 'APP ID長度限制 400 個字數'
    }],
};

const loadingRule = {
    title: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 30,
        message: '名稱長度限制 25 個字數',
        trigger: 'blur'
    }]
};

const questionRule = {
    title: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 255,
        message: '名稱長度不能超過 255 個字符',
        trigger: 'blur'
    }],
    department_id: [{required: true, message: '業務為必填欄位'}, {
        validator: stringNotZeroValidator, trigger: 'change'
    }],
    stage_id: [{required: true, message: '金幣為必填欄位'}, {
        validator: stringNotZeroValidator, trigger: 'change'
    }],
};

const questionRadioRule = {
    title: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 100,
        message: '名稱長度不能超過 100 個字符',
        trigger: 'blur'
    }],
    option: [{required: true, message: '選項為必填欄位'}, {
        min: 1,
        max: 255,
        message: '選項長度不能超過 255 個字符',
        trigger: 'blur'
    }],
    'options[0].option': [{required: true, message: '選項為必填欄位'}, {
        min: 1,
        max: 255,
        message: '選項長度不能超過 255 個字符',
        trigger: 'blur'
    }],
};

const questionTextRule = {
    title: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 100,
        message: '名稱長度不能超過 100 個字符',
        trigger: 'blur'
    }],
    answer: [{required: true, message: '答案為必填欄位'}, {
        min: 1,
        max: 255,
        message: '答案長度不能超過 255 個字符',
        trigger: 'blur'
    }],
};

const questionProfileRule = {
    title: [{required: true, message: '問題為必填欄位'}, {
        min: 1,
        max: 100,
        message: '問題長度不能超過 100 個字符',
        trigger: 'blur'
    }],
};

const questionGoldsRule = {
    // person_gold: [{required: true, message: '名稱為必填欄位'}, {
    //     validator: naturalNumberDefaultValidator, trigger: 'change'
    // }],
};

const activitySearchRule = {};

const activityRule = {
    name: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 255,
        message: '名稱長度不能超過 255 個字符',
        trigger: 'blur'
    }],
    department_id: [{required: true, message: '業務為必填欄位'}, {
        validator: stringNotZeroValidator, trigger: 'change'
    }],
    stage_id: [{required: true, message: '金幣為必填欄位'}, {
        validator: stringNotZeroValidator, trigger: 'change'
    }],
};

const activityRadioRule = {
    title: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 100,
        message: '名稱長度不能超過 100 個字符',
        trigger: 'blur'
    }],
    option: [{required: true, message: '選項為必填欄位'}, {
        min: 1,
        max: 255,
        message: '選項長度不能超過 255 個字符',
        trigger: 'blur'
    }],
    'options[0].option': [{required: true, message: '選項為必填欄位'}, {
        min: 1,
        max: 255,
        message: '選項長度不能超過 255 個字符',
        trigger: 'blur'
    }],
};

const activityTextRule = {
    title: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 100,
        message: '名稱長度不能超過 100 個字符',
        trigger: 'blur'
    }],
    answer: [{required: true, message: '答案為必填欄位'}, {
        min: 1,
        max: 255,
        message: '答案長度不能超過 255 個字符',
        trigger: 'blur'
    }],
};

const activityProfileRule = {
    title: [{required: true, message: '名稱為必填欄位'}, {
        min: 1,
        max: 100,
        message: '名稱長度不能超過 100 個字符',
        trigger: 'blur'
    }],
};

const activityGoldsRule = {
    // person_gold: [{required: true, message: '名稱為必填欄位'}, {
    //     validator: naturalNumberDefaultValidator, trigger: 'change'
    // }],
};
const preferentialRule = {
    title: [{required:true, message: '內容為必填欄位'}, {
        min: 1,
        max: 30,
        message: '名稱長度不能超過 30 個字符',
        trigger: 'blur'
    }],
    desc: [{
        min: 0,
        max: 10000,
        message: '備註長度不能超過 10000 個字符',
        trigger: 'blur'
    }],
    cover: [{required: true, message: '景點圖片為必填欄位'}],
}
const versionsRule = {
    vernumber: [{required: true, message: '版本號為必填欄位'}, {
        validator: function (rule, value, callback) {
            let v = value.toString();

            if (!v.match(/^[0-9.]{1,30}$/i)) {
                callback(new Error("請輸入合法版本號"));
                return;
            }

            callback();
        }, trigger: 'change'
    }],
    vermobile: [{required: true, message: '版本號為必填欄位'}, {
        validator: function (rule, value, callback) {
            let v = value.toString();

            if (!v.match(/^[0-9]{1,30}$/i)) {
                callback(new Error("請輸入純數字版本號"));
                return;
            }

            callback();
        }, trigger: 'change'
    }],
    content: [{required: true, message: '更新內容為必填欄位'},
        {
            min: 1,
            max: 255,
            message: '答案長度不能超過 255 個字符',
            trigger: 'blur'
        }
    ],
    up_time: [{required: true, message: '上架時間為必填欄位'}],
};

const goldRecyclePersonSearchRule = {

};

const goldRecyclePersonRule = {

};

export let AdminRule = adminRule;
export let AdminSearchRule = adminSearchRule;
export let ShopRule = shopRule;
export let GoodsRule = goodsRule;
export let DepartmentRule = departmentRule;
export let DepartmentSearchRule = departmentSearchRule;
export let DepartmentGroupRule = departmentGroupRule;
export let DepartmentGroupSearchRule = departmentGroupSearchRule;
export let UsersSearchRule = usersSearchRule;
export let UsersRule = usersRule;
export let MessageSettingRule = messageSettingRule;
export let MessageSettingSearchRule = messageSettingSearchRule;
export let AdRule = adRule;
export let AppRule = appRule;
export let WelComeRule = loadingRule;
export let QuestionRule = questionRule;
export let QuestionRadioRule = questionRadioRule;
export let QuestionTextRule = questionTextRule;
export let QuestionProfileRule = questionProfileRule;
export let QuestionGoldsRule = questionGoldsRule;
export let ActivitySearchRule = activitySearchRule;
export let ActivityRule = activityRule;
export let ActivityRadioRule = activityRadioRule;
export let ActivityTextRule = activityTextRule;
export let ActivityProfileRule = activityProfileRule;
export let ActivityGoldsRule = activityGoldsRule;
export let VersionsRule = versionsRule;
export let GoldRecyclePersonSearchRule = goldRecyclePersonSearchRule;
export let GoldRecyclePersonRule = goldRecyclePersonRule;
export let PerferentialRule = preferentialRule;
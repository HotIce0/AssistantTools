<?php

return [
    //微信临时登录凭证校验接口(如果接口地址没有变更不需要修改)
    'WX_AUTH_INTERFACE_HOST' => 'https://api.weixin.qq.com',
    'WX_AUTH_INTERFACE_PATH' => '/sns/jscode2session',

    'APP_ID' => 'wx2f7988a6f230a9ad',
    'APP_SECRET' => '559f5b1a3e688cfe2886a22fecfebbbf',

    //微信登陆有效期 7200 s = 120 minute
    'WX_LOGIN_EXPIRES' => '7200',
];
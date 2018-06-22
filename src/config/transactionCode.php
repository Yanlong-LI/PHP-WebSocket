<?php
/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/4/4 0004
 * Time: 下午 1:49
 * APPLICATION:交易处理码
 */

return array(
    'messageType' => '0700',
    'getListScavengingChannelTypes' => '100006',//获取扫码通道类型列表
    'getListScavengingChannels' => '100007',//获取扫码通道列表
    'alipayM2S' => '100008',//支付宝正扫
//    'alipayM2S' => '190111',//支付宝正扫
    'alipayS2M' => '100002',//支付宝反扫
    'wechatM2S' => '100009',//微信正扫
//    'wechatM2S' => '190100',//微信正扫
    'wechatS2M' => '100003',//微信反扫
    'UnionPayS2M' => '100004',//银联反扫
    'getListShortCutChannels' => '190936',//获取快捷通道列表
    'getListShortCutBindCards' => '190926',//获取快捷绑卡列表
    'synchroinformationSettlementShortcut' => '190937',//同步快捷结算信息（通道A10000000000004，A10000000000005）
    'synchroinformationSettlementShortcut2' => '100011',//同步快捷结算信息（通道A10000000000013\190926接口返回XX16时调用)
    'shortCutBindCard' => '190927',//快捷绑卡
    'shortCutDelCard' => '190908',//快捷删卡
    'shortCutCardSMS' => '190931',//快捷开卡短信
    'shortCutCard' => '190932',//快捷开卡
    'shortCutPaySMS' => '190933',//快捷支付短信
    'shortCutPay' => '190934',//快捷支付
    'payForAnother' => '800001',//代付
    'initialization' => '100001',//微信小程序初始化下单
    'gateWay' => '800003',//网关
    'WG001BankList' => '800002',//WG001支持银行列表
    'WG002shortCutRegisterSMS' => '800004',//网关002获取快捷注册验证码
    'WG002shortCutRegister' => '800005',//网关002快捷注册
    'WG002shortCutBindBankCardSMS' => '800006',//获取WG002快捷绑定银行卡验证码
    'WG002shortCutBindBankCard' => '800007',//wg002快捷绑卡
    'orderStatusQuery' => '190935',//订单状态查询
    'refund'=>'100005',//退款

);


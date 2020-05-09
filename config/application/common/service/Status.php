<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/2
 * Time: 10:44
 */

namespace app\common\service;


class Status
{
   //案件状态
   const CASE_TO_PROCESSED  = 1;   //案件待处理
   const CASE_PROCESSING    = 2;   //案件处理中
   const CASE_COMPLETION    = 3;   //案件完成

   const IS_VALID           = 1;   //有效
   const NOT_VALID          = 0;   //无效
   const USER_IS_VALID      = 0;   //有效
   const IS_INLINE          = 1;   //行级元素
   const IS_BLOCK           = 0;   //块级元素

   //状态
   const TRANSACTION_STATE  = 1;   //交易状态
   const AUCTION_STATE      = 2;   //拍卖状态

   //分页
   const PAGE               = 15;  //分页页数
   const PAGINATION         = 10;  //分页页数 默认10

   //公司类型
   const AGENT              = 1;  //中介公司
   const COURT              = 2;  //法院
   const JUDGE              = 2;  //承办法官

    const INVOICE_BUSINESS     = 1; //发票业务
    const COMMISSION_BUSINESS  = 2; //提成业务

    //流程限制
    const CREATE            = 1; //创建流程
    const HANDLE            = 2; //提交流程
    const PERSONAL          = 1; //个人
    const TEAM              = 2; //团队
    const GROUP             = 2; //组
    const NORMAL            = 1; //正常
    const COMPLETE          = 2; //完成
    const RETURN_BACK       = 3; //否决
    const REVOCATION        = 4; //撤销
    const IS_BACK           = 1; //驳回
    const NOT_BACK          = 0; //未驳回

    const MANAGE_COMMISSION = 1; //管理提成
    const TEAM_COMMISSION   = 1; //团队提成

    const LEVEL              = 2; //二级城市
    const NAN_JING           = '南京市'; //二级城市

    //业务类型
    const SETTLEMENT         = 1; //结算管理
    const COMMISSION         = 2; //提成管理

    //提成类型
    const IS_LEADER          = 1; //管理提成
    const IS_TEAM            = 2; //团队提成

    const IS_SURE            = 1; //确认
    const NOT_SURE           = 0; //未确认
    const IS_DISTRIBUTION    = 1; //已分配
    const NOT_DISTRIBUTION   = 0; //未分配
    const TO_BE_DISTRIBUTED  = 1; //待分配
    const TO_BE_CONFIRMED    = 2; //待确认
    const APPLYING           = 3; //申请中
    const THE_ARCHIVE        = 8; //归档
    const THE_REVIEW_REJECTED = 7; //复审驳回
    const THE_REVIEW_PASS    = 6; //复审通过
    const THE_TRIAL_PASS     = 4; //初审通过
    const THE_TRIAL_REJECTED = 5; //初审驳回

    const LEADER             = 1; //领导
    const NOT_LEADER         = 0; //非领导
    const MANAGER            = 1; //经理
    const SUPERVISOR         = 2; //主管
    const SECRETARY          = 3; //内勤
    const LEGWORK            = 4; //外勤

    const IS_DEL             = 1; //已删除
    const NOT_DEL            = 0; //未删除

    const CLOSING_THE_DEAL   = 6; //已成交

    const SAVING             = 0; //保存中

    public static $business_type = [
        [
            'id' => 1,
            'name' => '结算管理'
        ],
        [
            'id' => 2,
            'name' => '业务提成'
        ],
    ];

    //数据范围
    public static $data_range = [
        [
            'id' => 2,
            'name' => '我的团队'
        ],
        [
            'id' => 1,
            'name' => '我的案件'
        ],
    ];

    //数据范围
    public static $customer_data_range = [
        [
            'id' => 2,
            'name' => '全部'
        ],
        [
            'id' => 1,
            'name' => '我的客户'
        ],
    ];

    //案件状态
    public static $case_state = [
        [
            'id' => 0,
            'name' => '全部'
        ],
        [
            'id' => 1,
            'name' => '待处理'
        ],
        [
            'id' => 2,
            'name' => '处理中'
        ],
        [
            'id' => 3,
            'name' => '已完成'
        ],
    ];

    //对接类型
    public static $docking_type = [
        [
            'id' => 1,
            'name' => '个人'
        ],
        [
            'id' => 2,
            'name' => '组'
        ],
    ];
    
    //申请状态
    public static $apply_state = [
        [
            'id' => 1,
            'name' => '未申请'
        ],
        [
            'id' => 2,
            'name' => '已申请'
        ],
    ];

    //流程状态
    public static $state = [
        [
            'id' => -1,
            'name' => '回退修改'
        ],
        [
            'id' => 0,
            'name' => '保存中'
        ],
        [
            'id' => 1,
            'name' => '流程中'
        ],
        [
            'id' => 2,
            'name' => '审核通过'
        ],
    ];

    //收费方式
    public static $charge_type = [
        [
            'id' => 1,
            'name' => '按件'
        ],
        [
            'id' => 2,
            'name' => '按比例'
        ],
    ];
    
    //抬头类型
    public static $rise_type = [
        [
            'id' => 1,
            'name' => '企业'
        ],
        [
            'id' => 2,
            'name' => '个人/非企业'
        ],
    ];
    
    //发票类型
    public static $invoice_type = [
        [
            'id' => 1,
            'name' => '增值税普通发票 '
        ],
        [
            'id' => 2,
            'name' => '增值税专业发票'
        ],
    ];

    //提成分配状态
    public static $performance_type = [
        [
            'id' => 1,
            'name' => '待分配'
        ],
        [
            'id' => 2,
            'name' => '待确认'
        ],
        [
            'id' => 3,
            'name' => '申请中'
        ],
        [
            'id' => 4,
            'name' => '初审通过'
        ],
        [
            'id' => 5,
            'name' => '初审驳回'
        ],
        [
            'id' => 6,
            'name' => '复审通过'
        ],
        [
            'id' => 7,
            'name' => '复审驳回'
        ],
        [
            'id' => 8,
            'name' => '已归档'
        ],
    ];

    //业绩提成驳回节点
    public static $return_back = [
        [
            'id' => 1,
            'name' => '初审驳回'
        ],
        [
            'id' => 2,
            'name' => '复审驳回'
        ],
    ];
}
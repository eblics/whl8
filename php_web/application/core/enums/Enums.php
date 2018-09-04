<?php
/**
 * 
 * @author shizq
 *
 */
class BoolEnum {

	const Yes = 1;
	const No = 0;
	
	static $EnumValues = [
		self::Yes => '是',
		self::No => '否'	
	];
}

// shop wait for fix
class BoolEnums {

	const Yes = 1;
	const No = 0;
	
	static $Values = [
		self::Yes => '是',
		self::No => '否'	
	];
}

class MerchantAccountEnum {

	const Admin = 0;
	
	static $EnumValues = [
		self::Admin => '管理员',	
	];
}

class MerchantStatusEnum {

	const Create = 0;
	const Reviewed = 1;
	const Refused = 2;
	const Freezed = 3;
	const Review = 4;
	const PreReview = 5;
	
	static $EnumValues = [
		self::Create => '新建',
		self::Reviewed => '已审核',
		self::Refused => '已拒绝',
		self::Freezed => '冻结',
		self::Review => '待审核',
		self::PreReview => '预审核'
	];
}

class ActivityTypeEnum {

	const Redpacket = 0;
	const HappyCoin = 1; // deprecated
	const Card = 2;
	const Mix = 3;
	const Point = 4;
	const Multi = 5;
	const Accum = 6;
	
	static $EnumValues = [
		self::Redpacket => '红包策略',
		self::HappyCoin => '欢乐币策略',
		self::Card => '乐券策略',
		self::Mix => '组合策略',
		self::Point => '积分策略',
		self::Multi => '叠加策略',
		self::Accum => '累计策略'
	];
}

class AdminRoleEnum {

	const Master = 0;
	const Admin = 1;
	const Normal = 2;
	
	static $EnumValues = [
		self::Master => '超级管理员',
		self::Admin => '超级管理员',
		self::Normal => '超级管理员',
	];
}

class AdminStatusEnum {

	const Disable = 0;
	const Enable = 1;
	const Locked = 2;
	const Del = 3;
	
	static $EnumValues = [
		self::Disable => '未启用',
		self::Enable => '启用',
		self::Locked => '禁用',
		self::Del => '已删除'
	];
}

class DynamicTypeEnum {

	const Admin = 0;
	const Merchant = 1;
	const MchAccount = 2;
	
	static $EnumValues = [
		self::Admin => 'opp_accounts',
		self::Merchant => 'merchants',
		self::MchAccount => 'mch_accounts'
	];

	static $EnumField = [
		self::Admin => 'userName',
		self::Merchant => 'name',
		self::MchAccount => 'userName'
	];
}

class AccountTypeEnum {

	const Normal = 0;

	const Merchant = 1;

	const Isnone = 2;

	static $EnumValues = array(
		self::Normal => '普通号',
		self::Merchant => '企业号',
		self::Isnone => '账号不存在'
	);
}

class WxCompanyVipLevel{
	const Basic = 0;
	const Standard = 1;
	const Premium = 2;
	const Ultimate = 3;

	static $EnumValues = array(
		self::Basic => '基础版',
		self::Standard => '标准版',
		self::Premium => '高级版',
		self::Ultimate => '旗舰版'
	);
}

class RoleEnum {

	const Comsumer = 0;

	const Waiter = 1;

	const Salesman = 2;

	static $EnumValues = array(
		self::Comsumer => '消费者',
		self::Waiter => '服务员',
		self::Salesman => '业务员',
	);
}

class ScanActionEnum {

	const Scan = 0;

	const Transfer = 1;

	static $EnumValues = array(
		self::Scan => '扫码',
		self::Transfer => '转移',
	);
}

class MchWxEnum {

	const Mobile = 1;

	const Shop = 2;

	static $EnumValues = array(
		self::Mobile => '消费者',
		self::Shop => '供应链',
	);
}
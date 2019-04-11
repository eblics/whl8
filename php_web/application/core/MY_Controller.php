<?php
require 'enums/Enums.php';

/**
 * 基础控制器
 *
 * @author shizq
 */
class MY_Controller extends CI_Controller {

	const MANUAL_MCH_AUTH_IDS = [173];

	public function __construct() {
		parent::__construct();
		set_error_handler(function($errno, $errstr, $errfile, $errline) {
			if ($this->isAjax()) {
				error("custom-error: [$errno] $errstr Error on line $errline in $errfile");
				$this->ajaxResponseOver($errstr, 500, 500);
			} else {
				error("custom-error: [$errno] $errstr Error on line $errline in $errfile");
			}
		});
		set_exception_handler(function($e) {
			if ($this->isAjax()) {
				if ($e->getCode() === 0) {
					error('custom-error: '. $e->getMessage());
					$this->ajaxResponseOver($e->getMessage(), 1);
				}
				$this->ajaxResponseOver($e->getMessage(), $e->getCode());
			} else {
				$this->showErrPage($e->getMessage());
			}
		});
		$this->load->library('session');
		if (! is_cli()) {
			if ($this->isAjax()) {
				debug('ajax-request: '. $this->getCurrentUrl());
			} else {
				debug('page-request: '. $this->getCurrentUrl());
			}
		}
	}

	protected function showErrPage($errmsg) {
		$this->load->view('page_error', ['errmsg' => $errmsg, 'title' => '消息']);
	}

	protected function getCurrentUrl() {
		return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
	}

	protected function ajaxResponse($data = NULL, $msg = NULL , $errcode = 0) {
		$resp_data = ['data' => $data, 'errmsg' => $msg, 'errcode' => $errcode];
		$this->output->set_content_type('application/json')
			->set_output(json_encode($resp_data));
	}

	protected function ajaxResponseSuccess($data = NULL, $msg = NULL , $errcode = 0) {
		$this->ajaxResponse($data, $msg, $errcode);
	}

	protected function ajaxResponseFail($msg = '发生未知错误', $errcode = 1, $statusCode = 200) {
		$this->output->set_status_header($statusCode);
		$this->ajaxResponse(NULL, $msg, $errcode);
	}

	protected function ajaxResponseOver($msg = '请求失败', $errcode = 1, $statusCode = 200) {
		$this->output->set_status_header($statusCode);
		header('Content-Type: application/json');
		exit(json_encode(['data' => NULL, 'errmsg' => $msg, 'errcode' => $errcode]));
	}

	protected function isAjax() {
		return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) &&
		strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest";
	}

	protected function getCurrentWxUser($mchId = NULL) {
		if (! isset($mchId)) {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('没有提供商户编号');
			} else {
				$this->showErrPage('没有提供商户编号');
			}
		}
		$merchant = $this->db->where('id', $mchId)->get('merchants')->row();
		if (! isset($merchant)) {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('商户不存在');
			} else {
				$this->showErrPage('商户不存在');
			}
		}
		if ($this->session->has_userdata('current_wx_user')) {
			$currentWxUser = $this->session->userdata('current_wx_user');
			return $currentWxUser;
		}
		if ($this->isAjax()) {
			$this->ajaxResponseOver('登录超时，请刷新页面重试');
		}
		$this->load->model('Weixin_model', 'weixin');
		$redirectUri = $this->getCurrentUrl();
		$wxUser = $this->weixin->getWxUser($redirectUri, $merchant);
		if (isset($wxUser->errcode)) {
			$this->showErrPage('获取微信用户信息失败：'. $wxUser->errmsg);
		}
		$dbWxUser = $this->weixin->getWxUserByOpenid($wxUser->openid);
		if (isset($dbWxUser)) {
			$wxUserId = $dbWxUser->id;
			$this->weixin->updateWxUser($dbWxUser->openid, $wxUser);
		} else {
			$wxUser->mchId = $mchId;
			$wxUserId = $this->weixin->addWxUser($wxUser);
		}

		$wxUser = $this->weixin->getWxUserById($wxUserId);
		$this->session->set_userdata('current_wx_user', $wxUser);
		$state = $this->input->get('state');
		if (isset($state)) {
			redirect(base64_decode($state));
		} else {
			exit('no state');
		}
	}

}

/**
 * 企业端基础控制器
 *
 * @author shizq
 */
class MerchantController extends MY_Controller {

	protected function getCurrentMerchant() {
		if ($this->session->has_userdata('current_merchant')) {
			return $this->session->userdata('current_merchant');
		} else {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('会话超时，请刷新页面重试');
			} else {
				redirect('/login');
				exit();
			}
		}
	}

	protected function getCurrentManager() {
		if ($this->session->has_userdata('current_manager')) {
			return $this->session->userdata('current_manager');
		} else {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('会话超时，请刷新页面重试');
			} else {
				redirect('/login');
				exit();
			}
		}
	}
}


/**
 * 移动端基础控制器
 *
 * @author shizq
 */
class Mobile_Controller extends MY_Controller {

	protected function getCurrentMchId() {
		if ($this->session->has_userdata('current_mch_id')) {
			return $this->session->userdata('current_mch_id');
		} else {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('会话超时，请刷新页面或重新扫码');
			} else {
				$this->showErrorPage('会话超时，请刷新页面或重新扫码');
			}
		}
	}

	protected function getCurrentUser($mchId = NULL, $checkForbidden = TRUE, $isGetCommonUser = FALSE) {
		//debug('hls-getCurrentUser-'.$mchId);
		if (! isset($mchId)) {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('没有提供商户编号');
			} else {
				$this->showErrPage('没有提供商户编号');
			}
		}
		//error('get_current_user:'.$mchId);
		$merchant = $this->db->where('id', $mchId)->get('merchants')->row();
		if (! isset($merchant)) {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('商户不存在');
			} else {
				$this->showErrPage('商户不存在');
			}
		}
		if ($mchId != -1) {
			$this->session->set_userdata('current_mch_id', $mchId);
		}
		return $this->getCurrentMember($merchant, $checkForbidden, $isGetCommonUser);
		// if (is_null($mchId)) {
		// 	if ($this->isAjax()) {
		// 		$this->ajaxResponseOver('没有这个商户');
		// 	} else {
		// 		$this->showErrorPage('没有这个商户');
		// 	}
		// }
		// if ($mchId != -1) {
		// 	$this->session->set_userdata('current_mch_id', $mchId);
		// }
		// if ($this->session->has_userdata('current_user_' . $mchId)) {
		// 	if (! $isGetCommonUser && $mchId == -1) {
		// 		if ($this->isAjax()) {
		// 			$this->ajaxResponseOver('没有这个商户');
		// 		} else {
		// 			$this->showErrorPage('没有这个商户');
		// 		}
		// 	}
		// 	$currentUser = $this->session->userdata('current_user_' . $mchId);
		// 	if (! $checkForbidden) {
		// 		return $currentUser;
		// 	}
		// 	if ($mchId != -1) {
		// 		$userSub = $this->db->where('userId', $currentUser->id)->get('users_common_sub')->row();
		// 		if (isset($userSub) && 1 == $userSub->status) {
		// 			error('show-forbidden-mch: '. json_encode($currentUser));
		// 			if ($this->isAjax()) {
		// 				$this->ajaxResponseOver('您的账号已被封禁，请先申请解封');
		// 			} else {
		// 				$this->showForbiddenPage();
		// 			}
		// 		}
		// 	}
		// 	if ($currentUser->nickName === '未知昵称') {
		// 		$currentUser = $this->db->where('id', $currentUser->id)->get('users')->row();
		// 		$this->session->set_userdata('current_user_' . $mchId, $currentUser);
		// 	}
		// 	return $currentUser;
		// } else {
		// 	if ($this->isAjax()) {
		// 		$this->ajaxResponseOver('登录超时，请刷新页面重试，或重新扫码');
		// 	} else {
		// 		$loginUrl = '/login?mch_id=' . $mchId . '&from=' .  urlencode($this->getCurrentUrl());
		// 		redirect($loginUrl);
		// 		exit();
		// 	}
		// }
	}

	protected function getCommonUser($checkForbidden = TRUE) {
		$merchant = $this->db->where('id', -1)->get('merchants')->row();
		$commonUser = $this->getCurrentMember($merchant, FALSE, TRUE);
		if (! $checkForbidden) {
			return $commonUser;
		}
		$commonUser = $this->db->where('id', $commonUser->id)->get('users_common')->row();
		if (isset($commonUser) && $commonUser->commonStatus == BoolEnum::Yes) {
			error('show-forbidden-sys: '. json_encode($commonUser));
			if ($this->isAjax()) {
				$this->ajaxResponseOver('您的账号已被封禁，请先申请解封');
			} else {
				$this->showForbiddenPage();
			}
		}
		if (! isset($commonUser)) {
			session_destroy();
			if ($this->isAjax()) {
				$this->ajaxResponseOver('CommonUser is missing');
			} else {
				$this->showErrPage('CommonUser is missing');
			}
		}
		return $commonUser;
	}

	protected function getCurrentMember($merchant, $checkForbidden, $isGetCommonUser) {
		//debug('getCurrentMember-'.var_export($merchant,True));
		if ($this->session->has_userdata('current_member_'. $merchant->id)) {
			if (! $isGetCommonUser && $merchant->id == -1) {
				if ($this->isAjax()) {
					$this->ajaxResponseOver('没有这个商户');
				} else {
					$this->showErrorPage('没有这个商户');
				}
			}
			$currentUser = $this->session->userdata('current_member_'. $merchant->id);
			if (! $checkForbidden) {
				return $currentUser;
			}
			if ($merchant->id != -1) {
				$userSub = $this->db->where('userId', $currentUser->id)->get('users_common_sub')->row();
				if (isset($userSub) && BoolEnum::Yes == $userSub->status) {
					error('show-forbidden-mch: '. json_encode($currentUser));
					if ($this->isAjax()) {
						$this->ajaxResponseOver('您的账号已被封禁，请先申请解封');
					} else {
						$this->showForbiddenPage();
					}
				}
			}
			if ($currentUser->subscribe == BoolEnum::No && 
				! in_array($merchant->id, self::MANUAL_MCH_AUTH_IDS)) {
				$dbUser = $this->db->where('id', $currentUser->id)->get('users')->row();
				if (! isset($dbUser)) {
					session_destroy();
					if ($this->isAjax()) {
						$this->ajaxResponseOver('会话超时，请刷新页面或重新扫码');
					} else {
						$this->showErrorPage('会话超时，请刷新页面或重新扫码');
					}
				} else {
					$currentUser = $dbUser;
					$this->session->set_userdata('current_member_'. $merchant->id, $currentUser);
				}
			}
			return $currentUser;
		}
		if ($this->isAjax()) {
			$this->ajaxResponseOver('登录超时，请刷新页面重试', 10002);
		}
		$this->load->model('Weixin_model', 'weixin');
		$redirectUri = $this->getCurrentUrl();
		if (in_array($merchant->id, self::MANUAL_MCH_AUTH_IDS)) {
			$merchant->manualAuth = BoolEnum::Yes;
		} else {
			$merchant->manualAuth = BoolEnum::No;
		}
		// 第三个参数表示获取消费者微信信息
		$wxUser = $this->weixin->getWxUser($redirectUri, $merchant, BoolEnum::Yes); 
		if (isset($wxUser->errcode)) {
			$this->showErrPage('获取微信用户信息失败：'. $wxUser->errmsg);
		}
		// 保存用户信息到数据库
		$this->load->model('User_model', 'user');
		if ($merchant->id != -1) {
			$user = $this->user->get_by_openid($wxUser->openid);
			if (isset($user)) {
				$wxUser->id = $user->id;
				$wxUser->updateTime = time();
				$this->user->save($wxUser);
			} else {
				$wxUser->mchId = $merchant->id;
				$wxUser->createTime = time();
				$wxUser->updateTime = time();
				$this->user->save($wxUser);
			}
			$user = $this->user->get_by_openid($wxUser->openid);
		} else {
			$user = $this->user->get_common_by_openid($wxUser->openid);
			if (isset($user)) {
				$wxUser->id = $user->id;
				$wxUser->updateTime = time();
				$this->user->save_common($wxUser);
			} else {
				$wxUser->mchId = $merchant->id;
				$wxUser->createTime = time();
				$wxUser->updateTime = time();
				$this->user->save_common($wxUser);
			}
			$user = $this->user->get_common_by_openid($wxUser->openid);
		}
		
		$this->session->set_userdata('current_member_'. $merchant->id, $user);
		$state = $this->input->get('state');
		if (isset($state)) {
			redirect(base64_decode($state));
		} else {
			exit('no state');
		}
	}

    protected function getCurrentScanUser($mchId, $loadTestOpenid = NULL) {
        $openid_key = 'openid_' . $mchId;
        if (isset($loadTestOpenid)) {
            if (config_item('loadtest')) {
                return $this->user->get_by_openid($loadTestOpenid);
            }
        }
        $currentUser = $this->getCurrentUser($mchId);
        if ($currentUser->id === 0 || $currentUser->id === '0') {
            session_destroy();
            exit('get-current-scan-user fail');
        }
        return $currentUser;
    }

	protected function getCurrentLecode() {
		if ($this->session->has_userdata('last_scan_code')) {
            return $this->session->userdata('last_scan_code');
        } else {
            return NULL;
        }
	}

	protected function getCurrentScanCode() {
        return $this->getCurrentLecode();
    }
    protected function getCurrentEvilLevel() {
        if ($this->session->has_userdata('last_evil_level')) {
            return $this->session->userdata('last_evil_level');
        } else {
            return NULL;
        }
    }
    
    protected function showForbiddenPage() {
    	$msg = '您的微信帐号已被锁定<BR>
    		如有疑问，长按识别二维码，申请解锁<BR>
    		<font style="font-size:1rem;color:gray;">您本次扫描的商品包装内的二维码<BR>
    		是重要的申诉凭据，请妥善保存</font>';
    	$this->load->view('error_forbidden', ['errmsg' => $msg]);
    }

	protected function showErrorPage($message) {
		$this->load->view('error', ['errmsg' => $message]);
	}

}


/**
 * 门店端基础控制器
 *
 * @author shizq
 */
class Shop_Controller extends MY_Controller {

	protected function getCurrentMchId() {
		if ($this->session->has_userdata('current_mch_id')) {
			return $this->session->userdata('current_mch_id');
		} else {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('会话超时，请从微信菜单重新进入');
			} else {
				$this->showErrPage('会话超时，请从微信菜单重新进入');
			}
		}
	}

	protected function getCurrentSalesman($mchId, $isGetCommonUser = FALSE) {
		if (is_null($mchId)) {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('没有这个商户');
			} else {
				$this->showErrorPage('没有这个商户');
			}
		}
		$this->session->set_userdata('current_mch_id', $mchId);
		if ($this->session->has_userdata('current_salesman_' . $mchId)) {
			if (! $isGetCommonUser && $mchId == -1) {
				if ($this->isAjax()) {
					$this->ajaxResponseOver('没有这个商户');
				} else {
					$this->showErrorPage('没有这个商户');
				}
			}
			return $this->session->userdata('current_salesman_' . $mchId);
		} else {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('登录超时，请重新刷新页面');
			} else {
				$wxUser = $this->getCurrentWxUser($mchId);
				$currentSalesman = $this->db->where('openid', $wxUser->openid)->get('salesman')->row();
				$this->session->set_userdata('current_salesman_' . $mchId, $currentSalesman);
				return $currentSalesman;
			}
		}
	}

	protected function getCurrentWaiter($mchId, $isGetCommonUser = FALSE) {
		if (is_null($mchId)) {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('没有这个商户');
			} else {
				$this->showErrorPage('没有这个商户');
			}
		}
		if ($mchId != -1) {
			$this->session->set_userdata('current_mch_id', $mchId);
		}
		if ($this->session->has_userdata('current_waiter_' . $mchId)) {
			if (! $isGetCommonUser && $mchId == -1) {
				if ($this->isAjax()) {
					$this->ajaxResponseOver('没有这个商户');
				} else {
					$this->showErrorPage('没有这个商户');
				}
			}
			return $this->session->userdata('current_waiter_' . $mchId);
		} else {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('登录超时，请重新刷新页面');
			} else {
				$wxUser = $this->getCurrentWxUser($mchId);
				$currentWaiter = $this->db->where('openid', $wxUser->openid)->get('waiters')->row();
				$this->session->set_userdata('current_waiter_' . $mchId, $currentWaiter);
				return $currentWaiter;
			}
		}
	}

	protected function getCommonUser($checkForbidden = TRUE) {
		$commonUser = $this->getCurrentWaiter(-1, TRUE);
		if (! $checkForbidden) {
			return $commonUser;
		}
		$commonUser = $this->db->where('id', $commonUser->id)->get('users_common')->row();
		if ($commonUser->commonStatus == 1) {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('您的账号已被封禁，请先申请解封');
			} else {
				$this->showForbiddenPage();
			}
		}
		return $commonUser;
	}

	protected function getJssdkParams($mchId = NULL) {
		$merchant = $this->db->where('id', $mchId)->get('merchants')->row();
		if (! isset($merchant)) {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('商户不存在');
			} else {
				$this->showErrPage('商户不存在');
			}
		}
		$currentUrl = $this->getCurrentUrl();
		$this->load->model('Weixin_model', 'weixin');
		$params = $this->weixin->getJssdkParams($merchant, $currentUrl);
		return $params;
	}

	protected function getCurrentLecode() {
		if ($this->session->has_userdata('last_scan_code')) {
            return $this->session->userdata('last_scan_code');
        } else {
            return NULL;
        }
	}

	protected function getJsApiSigPackage($mchId) {
		$this->load->model('Tools_model', 'tools');
		return $this->tools->getSignature($mchId);
	}

	protected function showView($page, $data = []) {
		$params = ['v' => time()];
		$params = array_merge($params, $data);
		$this->load->view($page, $params);
	}
}


/**
 * 运营端基础控制器
 *
 * @author shizq
 */
class OppController extends MY_Controller {

	private $openUris = ['/login', '/login/verify_img', '/api/login'];

	public function __construct() {
		parent::__construct();
		if (! in_array(explode('?', $_SERVER["REQUEST_URI"])[0], $this->openUris)) {
			$this->getCurrentUser();
		}
	}

	protected function getCurrentUser() {
		if ($this->session->has_userdata('admin')) {
			return $this->session->userdata('admin');
		} else {
			if ($this->isAjax()) {
				$this->ajaxResponseOver('会话超时，请重新登录');
			} else {
				// exit(json_encode($this->session->userdata('admin')));
				redirect('/login');
				exit();
			}
		}
	}

	protected function saveDynamic($action, $target_id, $target_type) {
		$target = $this->db->select(DynamicTypeEnum::$EnumField[$target_type])
			->where('id', $target_id)
			->get(DynamicTypeEnum::$EnumValues[$target_type])->row_array();
		if (is_null($target[DynamicTypeEnum::$EnumField[$target_type]])) {
			$target[DynamicTypeEnum::$EnumField[$target_type]] = '未知名称';
		}
		$dynamic = [
			'adminId' => $this->getCurrentUser()->id,
			'action' => $action,
			'occTime' => time(),
			'targetId' => $target_id,
			'targetTable' => DynamicTypeEnum::$EnumValues[$target_type],
			'target' => $target[DynamicTypeEnum::$EnumField[$target_type]]
		];
		$this->db->insert('opp_dynamic', $dynamic);
	}
}


/**
 *
 * Api控制器
 *
 * @author shizq
 *
 */
class Api_Controller extends OppController {

	public function __construct() {
		parent::__construct();

		if (! $this->isAjax()) {
        	$this->ajaxResponseOver('拒绝访问', 403, 403);
        }
	}

}

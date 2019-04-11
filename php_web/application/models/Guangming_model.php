<?php
/**
 * 光明定制开发
 *
 * @author shizq
 */
class Guangming_model extends MY_Model {

    /**
     * 更新用户积分数据
     * @param  $updateParams
     * @return void        
     */
    public function updateBackpoint($updateParams) {
    	info('guangming-model-update-backpoint - begin');
    	info('guangming-model-update-backpoint - params: '. json_encode($updateParams));
    	$mchId = $this->getEnvGmMchId();
    	try {
    		$scanLog = $this->db->where('code', $updateParams['code'])->get('scan_log')->row();
    		if (! isset($scanLog)) {
    			throw new Exception("在红码平台找不到该乐码", 1);
    		}
            if ($scanLog->over === '1') {
                throw new Exception("此乐码已被扫描", 1);
            }
            if ($scanLog->openId !== $updateParams['openid']) {
                throw new Exception("乐码不属于该微信用户", 1);
            }
    		if (intval($scanLog->mchId) !== $mchId) {
    			throw new Exception("此乐码不属于光明乳业", 1);
    		}
            $locationArr = explode(',', $updateParams['location']);
            $updateData['lat'] = $locationArr[0];
            $updateData['lng'] = $locationArr[1];
            $updateData['over'] = 1;

            info('guangming-model-update-backpoint - update scan_log old: '. json_encode($scanLog));
            info('guangming-model-update-backpoint - update scan_log new: '. json_encode($updateData));

            $this->beginTransition();
            $this->db->where('code', $updateParams['code'])->where('openId', $updateParams['openid'])->update('scan_log', $updateData);
            if ($this->db->affected_rows() !== 1) {
                throw new Exception("更新扫码信息失败", 1);
            }
            $this->addUserPoints($scanLog, $updateParams);
            if (! $this->checkTransitionSuccess()) {
                throw new Exception("发生未知错误", 1);
            }
            $this->commitTransition();
    	} catch (Exception $e) {
    		error('guangming-model-update-backpoint - fail: '. $e->getMessage());
            $this->rollbackTransition();
    		throw $e;
    	} finally {
    		info('guangming-model-update-backpoint - end');
    	}
        
    }

    /**
     * 更新用户信息
     * @param  $updateParams
     * @return void        
     */
    public function updateBackuser($updateParams) {
        // 扫码流程会写入用户信息，该处仅需处理更新信息，无该openid返回错误结果
        info('guangming-model-update-backuser - begin');
        info('guangming-model-update-backuser - params: '. json_encode($updateParams));
        $mchId = $this->getEnvGmMchId();
        $updateParams['mchId'] = $mchId;
        try {
            $userInfo = $this->db->where('openid', $updateParams['openid'])->get('users')->row();
            if (! isset($userInfo)) {
                throw new Exception("在红码平台找不到该用户", 1);
            }

            info('guangming-model-update-backuser - update scan_log old: '. json_encode($userInfo));
            info('guangming-model-update-backuser - update scan_log new: '. json_encode($updateParams));
            $openid = $updateParams['openid'];
            unset($updateParams['openid']);
            $this->db->where('openid', $openid)->update('users', $updateParams);
            if ($this->db->affected_rows() !== 1) {
                throw new Exception("用户信息没有发生变化", 2);
            }
        } catch (Exception $e) {
            error('guangming-model-update-backuser - fail: '. $e->getMessage());
            throw $e;
        } finally {
            info('guangming-model-update-backuser - end');
        }
    }

    /**
     * 检查乐码是否属于光明企业
     * @return void
     */
    public function checkCodeOwner($mchId) {
        $gmId = $this->getEnvGmMchId();
        if (intval($mchId) !== intval($gmId)) {
            throw new Exception("此乐码不属于光明乳业", 1);
        }
    }

    /**
     * 获取光明扫码用户信息
     * @param $openid
     * @return object       
     */
    public function getGuangmingMember($openid) {
        $this->db->db_select('hls_guangming');
        $gmMember = $this->db->where('openid', $openid)->get('gm_member')->row();
        return $gmMember;
    }

    /**
     * 保存光明扫码用户信息
     * @return void
     */
    public function saveGuangmingMember($addParams) {
        info('guangming-model-save-guangming-member - begin');
        info('guangming-model-save-guangming-member - params: '. json_encode($addParams));
        $this->db->db_select('hls_guangming');
        try {
            $gmMember = $addParams;
            $this->db->insert('gm_member', $gmMember);
            if ($this->db->affected_rows() !== 1) {
                throw new Exception("添加用户信息失败", 1);
            }
        } catch (Exception $e) {
            error('guangming-model-save-guangming-member - fail: '. $e->getMessage());
            throw $e;
        } finally {
            info('guangming-model-save-guangming-member - end');
        }
    }

    /**
     * 更新用户的积分账户
     * @param $scanLog     
     * @param $updateParams
     */
    private function addUserPoints($scanLog, $updateParams) {
        $userPoints = new stdClass();
        $userPoints->mchId = $scanLog->mchId;
        $userPoints->userId = $scanLog->userId;
        $userPoints->pointsId = -2;
        $userPoints->amount = $updateParams['point'];
        $userPoints->getTime = $updateParams['getTime'];
        $userPoints->code = $scanLog->code;
        $userPoints->scanId = $scanLog->id;
        $userPoints->instId = -1;
        $userPoints->role = 0;
        info('guangming-model-add-user-points - insert user_points: '. json_encode($userPoints));
        $this->db->insert('user_points', $userPoints);
        if ($this->db->affected_rows() !== 1) {
            throw new Exception("添加用户积分失败", 1);
        }
        $userPointsId = $this->db->insert_id();
        $updateData['rewardTable'] = 'user_points';
        $updateData['rewardId'] = $userPointsId;
        $updateData['over'] = 1;
        info('guangming-model-add-user-points - update scan_log old: '. json_encode($scanLog));
        info('guangming-model-add-user-points - update scan_log new: '. json_encode($updateData));
        $this->db->where('id', $scanLog->id)->update('scan_log', $updateData);
        if ($this->db->affected_rows() !== 1) {
            throw new Exception("更新中奖信息失败", 1);
        }
        $addData = [$scanLog->userId, $scanLog->mchId, $userPoints->amount, $userPoints->amount];
        $sql = "INSERT INTO user_points_accounts(userId, mchId, amount, role)
            VALUES (?, ?, IFNULL(amount, 0) + ?, 0)
            ON DUPLICATE KEY UPDATE amount = IFNULL(amount, 0) + ?";
        info('guangming-model-add-user-points - insert user_points_accounts: '. json_encode($addData));
        $this->db->query($sql, $addData);
    }

}

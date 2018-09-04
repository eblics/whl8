<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends Mobile_Controller {

    public function match_activity() {
        $commonUser = $this->getCommonUser();
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        $lecode = $this->getCurrentLecode();
        $position = $this->input->get('pos');

        // users_common_sub add
        $sql = "select * from users_common_sub where parentId = ? and mchId = ?";
        $subUser = $this->db->query($sql, [$commonUser->id, $currentUser->mchId])->row();
        if (! isset($subUser)) {
            $sql = "insert into users_common_sub (parentId, userId, openid, mchId, status) values (?, ?, ?, ?, 0)";
            $this->db->query($sql, [$commonUser->id, $currentUser->id, $currentUser->openid, $currentUser->mchId]);
        }

        if (! isset($lecode)) {
            error("activity/match_activity: 没有乐码，currentUser: ". json_encode($currentUser));
            $this->load->library('common/common_login');
            $this->load->library('common/ipwall');
            $this->common_login->save_user_log(1, '无乐码匹配活动', 'null', $currentUser->mchId, $currentUser->id);
            $this->ipwall->error_process();
            $this->ajaxResponseFail('找不到您扫描的乐码');
            return;
        }

        $this->load->model('scan_log_model');
        $scanLog = $this->scan_log_model->get_by_code($lecode);

        if (! isset($scanLog)) {
            error("activity/match_activity: 没有扫码纪录，currentUser: ". json_encode($currentUser));
            $this->load->library('common/common_login');
            $this->load->library('common/ipwall');
            $this->common_login->save_user_log(1, '无扫码记录匹配活动', $lecode, $currentUser->mchId, $currentUser->id);
            $this->ipwall->error_process();
            $this->ajaxResponseFail('找不到您的扫码记录');
            return;
        }
        
        try {
            $this->load->model('Scan_log_model', 'scan_log');
            $scanLog = $this->scan_log->updateScanLogPosition($scanLog, $position);
            if ($scanLog->mchId === 315 || $scanLog->mchId === '315') {
                $this->load->model('Zhengdao_model', 'zhengdao');
                $this->zhengdao->isAreaForbidden($scanLog->areaCode);
            }
            $evilLevel = $this->getCurrentEvilLevel();
            $scanLog->evilLevel = $evilLevel;
            $scanLog = $this->scan_log->matchSubActivity($scanLog);
            if(isset($scanLog->evilLevel)){
                unset($scanLog->evilLevel);
            }
            $this->load->model('sub_activity_model', 'sub_activity');
            $subActivity = $this->sub_activity->get($scanLog->activityId);

            $this->load->model('webapp_model');
            $webapp = $this->webapp_model->getWebappPathForSubActivity($subActivity->webAppId);

            if (strpos($webapp->appPath, 'http://') === 0) {
                $webappUrl = $webapp->appPath . '?v=' . date('YmdHi');
            } else {
                $webappUrl = config_item('mobile_url') . $webapp->appPath . '?v=' . date('YmdHi');
            }
            // $random = mt_rand(1000, 9999);
            // $this->session->set_userdata('current_h5_path_'. $random, $webappUrl);
            // $webappUrl = '/h5/html/' . $random . '.html';
            if (! isProd()) {
                if (! isDev()) {
                    $webappUrl .= '&env=test';
                } else {
                    $webappUrl .= '&env=dev';
                }
            }

            $this->load->library('common/ipwall');
            $this->ipwall->correct_process();
            $this->ajaxResponseSuccess(['url' => $webappUrl]);
        } catch (Exception $e) {
            $this->load->library('common/common_login');
            $this->common_login->save_user_log(1, $e->getMessage(), $scanLog->code, $currentUser->mchId, $currentUser->id);
            $this->ajaxResponseFail($e->getMessage());
        }

    }

    public function get_best_match($type='json') {
        $this->match_activity();
    }

    public function take_activity() {
        $commonUser = $this->getCommonUser();
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        $lecode = $this->getCurrentLecode();

        $this->load->library('common/common_login');
        if (! isset($lecode)) {
            error("activity/take_activity: 没有乐码，currentUser: ". json_encode($currentUser));
            $this->load->library('common/ipwall');
            $this->common_login->save_user_log(1, '无乐码参与活动', 'null', $currentUser->mchId, $currentUser->id);
            $this->ipwall->error_process();
            $this->ajaxResponseFail('找不到您扫描的乐码');
            return;
        }

        $this->load->model('scan_log_model');
        $scanLog = $this->scan_log_model->get_by_code($lecode);

        if (! isset($scanLog)) {
            error("activity/take_activity: 没有扫码纪录，currentUser: ". json_encode($currentUser));
            $this->load->library('common/ipwall');
            $this->common_login->save_user_log(1, '无扫码记录参与活动', $lecode, $currentUser->mchId, $currentUser->id);
            $this->ipwall->error_process();
            $this->ajaxResponseFail('找不到您的扫码记录');
            return;
        }

        try {
            $this->checkScanUser($scanLog, $currentUser);
            $this->load->model('sub_activity_model', 'sub_activity');
            $subActivity = $this->sub_activity->get($scanLog->activityId);
            $this->checkActivity($subActivity);
            $this->load->model('red_packet_model', 'redpacket');
            //活动是红包类型
            if ($subActivity->activityType == ActivityTypeEnum::Redpacket) {
                $result = $this->redpacket->try_red_packet($subActivity->detailId, $subActivity, $scanLog);
            }
            //活动是欢乐币类型
            if ($subActivity->activityType == ActivityTypeEnum::HappyCoin) {
            }
            //活动是卡券类型
            if ($subActivity->activityType == ActivityTypeEnum::Card) {
                $result = $this->redpacket->try_card($subActivity->detailId, $subActivity, $scanLog);
            }
            //活动是组合类型
            if ($subActivity->activityType == ActivityTypeEnum::Mix) {
                $result = $this->redpacket->try_mixstrategy($subActivity->detailId, $subActivity, $scanLog);
            }
            //活动是积分类型
            if ($subActivity->activityType == ActivityTypeEnum::Point) {
                $result = $this->redpacket->try_point($subActivity->detailId, $subActivity, $scanLog);
            }
            //活动是叠加类型
            if ($subActivity->activityType == ActivityTypeEnum::Multi) {
                $result = $this->redpacket->try_multistrategy($subActivity->detailId, $subActivity, $scanLog);
            }
            //活动是累计类型
            if ($subActivity->activityType == ActivityTypeEnum::Accum) {
                $result = $this->redpacket->try_accumstrategy($subActivity->detailId, $subActivity, $scanLog);
            }

            $this->load->model('jokes_model');
            $joke=$this->jokes_model->get_joke();
            if ($joke) {
                $result->alt_text = $joke->text;
            }
            $this->load->model('Merchant_model', 'merchant');
            $merchant = $this->merchant->get($currentUser->mchId);
            $result->qrcode_url = '/h5/get_qrcode/'. urlencode($merchant->wxQrcodeUrl);
            $scanLog->over = 1;
            $this->scan_log_model->update($scanLog);
            $this->trigger_model->trigger_scan_log_update($scanLog);
            $this->common_login->save_user_log(5, '正常扫码', $scanLog->code, $currentUser->mchId, $currentUser->id);
            //正确扫码处理结果
            $this->load->library('common/ipwall');
            $this->ipwall->correct_process();
            $this->output->set_content_type('application/json')->set_output(json_encode($result));
        } catch (Exception $e) {
            $this->common_login->save_user_log(1, $e->getMessage(), $scanLog->code, $currentUser->mchId, $currentUser->id);
            $this->load->model('jokes_model');
            $joke = $this->jokes_model->get_joke();
            $result = [
                'data' => ['alt_text' => $joke->text],
                'alt_text' => $joke->text,
                'errcode' => $e->getCode(),
                'errmsg' => $e->getMessage()
            ];
            if ($result['errcode'] == 3) {
                // 获取奖品信息，以便再次展示
                if (isset($scanLog->rewardId)) {
                    $sql = "SELECT * FROM $scanLog->rewardTable WHERE id = ?";
                    $reward = $this->db->query($sql, [$scanLog->rewardId])->row();
                    if (isset($reward->amount)) {
                        $result['data']['amount'] = $reward->amount;
                    }
                }
            }
            $this->load->model('Merchant_model', 'merchant');
            $merchant = $this->merchant->get($currentUser->mchId);
            $result['qrcode_url'] = '/h5/get_qrcode/'. urlencode($merchant->wxQrcodeUrl);
            $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }
    }

    private function checkScanUser($scanLog, $currentUser) {
        if ($scanLog->openId != $currentUser->openid) {
            throw new Exception("此码已被他人扫过", 2);
        }
        if ($scanLog->over == BoolEnum::Yes) {
            throw new Exception("您已扫过此码", 3);
        }
    }

    private function checkActivity($subActivity) {
        if (! isset($subActivity)) {
            throw new Exception('没有适合你的活动', 6);
        }
        if ($subActivity->role != 0) {
            throw new Exception('没有适合你的活动', 5);
        }
        if ($subActivity->mainState == 0 || $subActivity->state == 0) {
            throw new Exception('活动还未启动，敬请期待', 6);
        }
        if ($subActivity->mainState == 2 || $subActivity->state == 2) {
            throw new Exception('活动已经停止', 7);
        }
        if ($subActivity->startTime > time()) {
            throw new Exception('活动还未开始', 8);
        }
        if ($subActivity->endTime < time()) {
            throw new Exception('活动已过期', 9);
        }
    }


    // -------------------------------------
    // 获取省市区数据（Added by shizq）
    public function app_areas() {
        try {
            $this->load->model('Ranking_model', 'ranking');
            $provinces = $this->ranking->areas();
            echo json_encode(['data' => $provinces, 'errmsg' => 'success', 'errcode' => 0]);
        } catch (Exception $e) {
            echo json_encode(['data' => [], 'errmsg' => $e->getMessage(), 'errcode' => $e->getCode()]);
        }
    }

    // -------------------------------------
    // 初始化大转盘项（Added by shizq）
    public function init_table_item() {
        $lecode = $this->getCurrentLecode();
        try {
            $this->load->model('activity_model', 'activity');
            $items = $this->activity->getMixstrategy($lecode);
            $this->load->model('merchant_model', 'merchant');
            $merchant = $this->merchant->getFormScanHistory($lecode);
            $data = ['items' => $items, 'merchant' => $merchant];
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['data' => $data, 'errmsg' => 'success', 'errcode' => 0]));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['data' => [], 'errmsg' => $e->getMessage(), 'errcode' => $e->getCode()]));
        }
    }

    /**
     * 抽奖业务
     *
     * @param $role 用户角色
     * @param $code 乐码
     * @return json
     */
    public function take($type='json'){
        $this->take_activity();
    }

}
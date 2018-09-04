<?php
class Activity extends Shop_Controller {

    public function api($apiName = NULL) {
        if (! isset($apiName)) {
            $this->ajaxResponseOver('403 Forbbiden.', 403, 403);
        } else if ($apiName == 'activity.match') {
            $this->matchActivity();
        } else if ($apiName == 'activity.take') {
            $this->takeActivity();
        } else {
            $this->ajaxResponseOver('404 Not Found.', 404, 404);
        }
    }

    public function matchActivity() {
        $currentWaiter = $this->getCurrentWaiter($this->getCurrentMchId());
        $lecode = $this->getCurrentLecode();
        $position = $this->input->get('pos');

        if (! isset($lecode)) {
            $this->ajaxResponseOver('找不到您扫描的乐码');
        }

        $this->load->model('Scan_log_model', 'scan_log');
        $scanLog = $this->scan_log->getWaiterScanLogByLecode($lecode);
        if (! isset($scanLog)) {
            $this->ajaxResponseOver('找不到您的扫码记录');
        }
        $scanLog = $this->scan_log->updateScanLogPosition($scanLog, $position, TRUE);
        $scanLog = $this->scan_log->matchSubActivity($scanLog, TRUE);

        $this->load->model('sub_activity_model', 'sub_activity');
        $subActivity = $this->sub_activity->get($scanLog->activityId);
        $this->ajaxResponseSuccess();
    }

    private function takeActivity() {
        $currentWaiter = $this->getCurrentWaiter($this->getCurrentMchId());
        $lecode = $this->getCurrentLecode();
        if (! isset($lecode)) {
            $this->ajaxResponseOver('找不到您扫描的乐码');
        }

        $this->load->model('Scan_log_model', 'scan_log');
        $scanLog = $this->scan_log->getWaiterScanLogByLecode($lecode);
        if (! isset($scanLog)) {
            $this->ajaxResponseOver('找不到您的扫码记录', 1);
        }
        if ($scanLog->userId != $currentWaiter->id) {
            $this->ajaxResponseOver('此码已被他人扫过', 2);
        }
        if ($scanLog->over == BoolEnum::Yes) {
            $this->ajaxResponseOver('您已扫过此码', 3);
        }

        $this->load->model('sub_activity_model', 'sub_activity');
        $subActivity = $this->sub_activity->get($scanLog->activityId);
        if (! isset($subActivity)) {
            $this->ajaxResponseOver('没有匹配到活动', 6);
        }
        if ($subActivity->role != RoleEnum::Waiter) {
            $this->ajaxResponseOver('此乐码没有针对服务员的活动', 5);
        }
        if ($subActivity->mainState == 0 || $subActivity->state == 0) {
            $this->ajaxResponseOver('活动还未启动，敬请期待', 6);
        }
        if ($subActivity->mainState == 2 || $subActivity->state == 2) {
            $this->ajaxResponseOver('活动已经停止', 7);
        }
        if ($subActivity->startTime > time()) {
            $this->ajaxResponseOver('活动还未开始', 8);
        }
        if ($subActivity->endTime < time()) {
            $this->ajaxResponseOver('活动已结束', 9);
        }

        $this->load->model('red_packet_model', 'redpacket');
        //活动是红包类型
        if ($subActivity->activityType == ActivityTypeEnum::Redpacket) {
            $result = $this->redpacket->try_red_packet($subActivity->detailId, $subActivity, $scanLog);
        }
        //活动是欢乐币类型
        if ($subActivity->activityType == ActivityTypeEnum::HappyCoin) {}

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
        $joke = $this->jokes_model->get_joke();
        if ($joke) {
            $result->alt_text = $joke->text;
        }
        $scanLog->over = BoolEnum::Yes;
        $this->scan_log->updateWaiterScanLogByLecode($lecode, $scanLog);
        //正确扫码处理结果
        $this->load->library('common/ipwall');
        $this->ipwall->correct_process();
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

}
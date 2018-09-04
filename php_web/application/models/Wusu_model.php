<?php
class Wusu_model extends MY_Model {

    const DB_NAME = 'hls_wusu';

    const PRIZES = ['-', '飞虎全队签名篮球', '飞虎男篮冠军T恤', '球员语音公仔'];
    static $PRIZES_NUM = [];

    public function __construct() {
        $this->db->db_select(self::DB_NAME);
    }

    public function getConfig() {
        return $this->db->get('wusu_zhuwei_config')->row();
    }

    /**
     *@deprecated 无需关注用户是否抽过奖 
     */
    public function checkTryedPrize($openid) {
        // 无论抽奖还是没抽奖
        return FALSE;
        $row = $this->db->where('openid', $openid)->get('wusu_zhuwei_openid')->row();
        if (isset($row) && $row->try_prize === '1' && $row->mobile !== NULL) {
            return TRUE;
        }
        if (isset($row) && $row->try_prize === '1' && $row->prize_type === '0') {
            return TRUE;
        }
        return FALSE;
    }

    public function getRanking($openid) {
        $row = $this->db->where('openid', $openid)->get('wusu_zhuwei_openid')->row();
        if (isset($row)) {
            return $row->ranking;
        }
        $this->db->insert('wusu_zhuwei_openid', ['openid' => $openid]);
        return $this->db->insert_id();
    }

    public function tryPrize($openid) {
        error('wusu-model-try-prize - params: '. $openid);
        $row = $this->db->where('openid', $openid)->get('wusu_zhuwei_openid')->row();
        if ($row->try_prize === '1') {
            return 0; // intval($row->prize_type);
        }
        $config = $this->getConfig();
        if (! isset($config)) {
            $randMax = 1000;
            self::$PRIZES_NUM = [0, 1, 1, 1];
        } else {
            $randMax = $config->max_num;
            self::$PRIZES_NUM = explode('|', $config->prize_num);
        }
        error('wusu-model-try-prize - prize_num: '. json_encode(self::$PRIZES_NUM));
        $prizeIndex = mt_rand(1, $randMax);
        if ($prizeIndex < count(self::PRIZES)) {
            // 中奖了
            $updateParams = [
                'try_prize' => 1,
                'try_time' => date('Y-m-d H:i:s'),
                'prize_name' => self::PRIZES[$prizeIndex],
                'prize_type' => $prizeIndex,
            ];
            $sql = "select count(1) num from wusu_zhuwei_openid where prize_type = ?";
            $countRow = $this->db->query($sql, [$prizeIndex])->row();
            $total = $countRow->num;
            if ($row->try_prize === '0' && $total < self::$PRIZES_NUM[$prizeIndex]) {
                $this->db->where('openid', $openid)->update('wusu_zhuwei_openid', $updateParams);
                if ($this->db->affected_rows() !== 1) {
                    throw new Exception("Update wusu_zhuwei_openid Error", 1);
                }
                return $prizeIndex;
            } else {
                $updateParams = [
                    'try_prize' => 1,
                    'try_time' => date('Y-m-d H:i:s'),
                    'prize_type' => 0,
                ];
                $this->db->where('openid', $openid)->update('wusu_zhuwei_openid', $updateParams);
                if ($this->db->affected_rows() !== 1) {
                    throw new Exception("Update wusu_zhuwei_openid Error", 1);
                }
                return FALSE;
            }
        } else {
            $updateParams = [
                'try_prize' => 1,
                'try_time' => date('Y-m-d H:i:s'),
                'prize_type' => 0,
            ];
            $this->db->where('openid', $openid)->update('wusu_zhuwei_openid', $updateParams);
            if ($this->db->affected_rows() !== 1) {
                throw new Exception("Update wusu_zhuwei_openid Error", 1);
            }
            return FALSE;
        }
    }

    public function saveUser($openid, $saveParams) {
        $updateParams = [
            'realname' => $saveParams['realname'],
            'mobile' => $saveParams['mobile'],
            'address' => $saveParams['address'],
        ];
        $this->db->where('openid', $openid)->update('wusu_zhuwei_openid', $updateParams);
        if ($this->db->affected_rows() !== 1) {
            throw new Exception("Update wusu_zhuwei_openid Error", 1);
        }
    }

}

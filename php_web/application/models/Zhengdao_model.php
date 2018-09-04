<?php
/**
 * 正道定制功能
 *
 * @author shizq
 */
class Zhengdao_model extends MY_Model {

    const FORBIDDEN_CODE = [370700, 320200]; // 潍坊市，无锡市

    public function isAreaForbidden($areaCode) {
        $areasRow = $this->db->where('code', $areaCode)->select('parentCode')->get('areas')->row();
        if (in_array($areasRow->parentCode, self::FORBIDDEN_CODE)) {
            throw new Exception("您所在的地区暂时不支持此活动", 1);
        }
    }

}

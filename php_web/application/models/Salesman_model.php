<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 *
 */
class Salesman_model extends CI_Model {

    /**
     * 获得一个业务员信息
     *
     * @param $salesmanId 商户ID
     * @param $mchId 商户ID
     * @return object 管理员对象
     */
    function getSalesman($salesmanId, $mchId) {
        $result = $this->db->where('id', $salesmanId)
            ->where('mchId', $mchId)
            ->get('mch_salesman')->result();
        if (count($result) == 1) {
            return $result[0];
        } else {
            return NULL;
        }
    }

    /**
     * 获取企业所有的业务员
     * 
     * @param $mchId 商户ID
     * @return array 业务员对象数组
     */
    function listSalesman($mchId) {
        $salesmans = $this->db->select('t1.id, t1.realName, ifnull(t2.openid, "未绑定") as openid, t1.mobile, t1.status, t1.idCardNo')
            ->from('mch_salesman as t1')
            ->join('salesman as t2', 't2.mchSalesmanId = t1.id', 'left')
            ->where('t1.mchId', $mchId)->where('t1.rowStatus', 0)->get()->result();
        return $salesmans;
    }

    /**
     * 保存一个业务员的信息到数据库
     * 
     * @param $salesman 管理员信息关联数组
     * @param $salesmanId 是否是编辑操作
     * @return boolean
     */
    function saveSalesman($salesman, $salesmanId = NULL) {
        info("save salesman - begin");
        
        if (isset($salesmanId)) {
            $row = $this->db->where('id', $salesmanId)->where('rowStatus', 0)
            ->get('mch_salesman')->row();
            if (! isset($row)) {
                throw new Exception("业务员不存在", 1);
            }
            $this->db->trans_start();
            $updated = $this->db->set('realName', $salesman['realName'])
            ->set('mobile', $salesman['mobile'])
            ->set('idCardNo', $salesman['idCardNo'])
            ->where('id', $salesmanId)->update('mch_salesman');
            if (! $updated) {
                $this->db->trans_rollback();
                throw new Exception("发生未知错误", 1);
            }
            $affected_rows = $this->db->affected_rows();
            if ($affected_rows === 1) {
                $updated = $this->db->set('mchSalesmanId', NULL)->where('mchSalesmanId', $salesmanId)->update('salesman');
                if (! $updated) {
                    $this->db->trans_rollback();
                    throw new Exception("发生未知错误", 1);
                }
            }
            $this->db->trans_complete();
            return;
        }
        $row = $this->db->where('mobile', $salesman['mobile'])
            ->where('rowStatus', 0)
            ->get('mch_salesman')->row();
        if (isset($row)) {
            throw new Exception("当前手机号已存在", 1);
        }
        $row = $this->db->where('realName', $salesman['realName'])
            ->where('rowStatus', 0)
            ->get('mch_salesman')->row();
        if (isset($row)) {
            throw new Exception("当前业务员姓名已存在", 1);
        }
        $success = $this->db->insert('mch_salesman', $salesman);
        if (! $success) {
            throw new Exception("发生未知错误", 1);
        }
        info("save salesman - end");
    }

    /**
     * 删除一个业务员账户
     * 
     * @param $salesmanId 业务员编号
     * @return boolean
     */
    function delSalesman($salesmanId) {
        info('delete salesman - begin');
        info('salesman id is: ' . $salesmanId);
        $result = $this->db->set('rowStatus', 1)
            ->where('id', $salesmanId)->update('mch_salesman');
        if ($result) {
            info('Delete admin success');
        } else {
            error('Delete admin faild');
            throw new Exception("发生未知错误", 1);
        }
        info('delete salesman - end');
        $salesman = $this->db->where('id', $salesmanId)->get('mch_salesman')->row();
        return $salesman->realName;
        return $result;
    }

    /**
     * 锁定或解锁一个业务员
     * 
     * @param $salesmanId 业务员编号
     * @param $lock 2 锁定，0 解锁
     * @return boolean
     */
    function freezeSalesman($salesmanId, $lock) {
        info('freeze salesman - begin');
        info('salesman id is: ' . $salesmanId . ' lock type is: ' . $lock);

        /**
         * 只有两种操作合法
         */
        if ($lock !== '2' && $lock !== '0') {
            error('Unknow lock type: ' . $lock);
            throw new Exception("未知的请求参数");
        }
        $result = $this->db->set('status', $lock)->where('id', $salesmanId)->update('mch_salesman');
        if (! $result) {
            error('update salesman status faild');
            throw new Exception("发生未知错误", 1);
        }
        info('freeze salesman - end');
        $salesman = $this->db->where('id', $salesmanId)->get('mch_salesman')->row();
        return $salesman->realName;
    }
}
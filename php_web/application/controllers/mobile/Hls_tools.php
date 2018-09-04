<?php
/**
 * 欢乐扫工具控制器
 *
 * @author shizq <shizhiqiang@acctrue.com>
 */
class Hls_tools extends Mobile_Controller {

	const OUTTER_INNER = 2;
	const INNER_OUTTER = 1;

	public function trans() {
		$this->load->model('Merchant_model', 'merchant');
        $merchant = $this->merchant->get(0);
		$params = ['appId' => $merchant->wxAppId, 'appSecret' => $merchant->wxAppSecret];
        $this->load->library('weixin_jssdk', $params);
        $signPackage = $this->weixin_jssdk->GetSignPackage();
        $signPackage['appId'] = $merchant->wxAppId;
        $signPackage['title'] = '乐码查询工具';
        $this->load->view('tools/trans', $signPackage);
	}

	public function api($apiName = NULL) {
		if ($apiName === 'lecode.toggle') {
			$this->toggleLecode();
		} else {
			$this->ajaxResponseFail('404 Not Found', 404, 404);
		}
	}

	private function toggleLecode() {
		$lecode = $this->input->get('code');
		$way = $this->input->get('way');
		if (! $lecode) {
			echo json_encode(['errcode'=>1, 'message'=>'请输入正确的乐码！']);
		} else {
			try {
				$result = $this->decode_lecode($lecode, $way);
				echo json_encode(['errcode'=>0, 'content'=> $result]);
			} catch (Exception $e) {
				echo json_encode(['errcode'=>1, 'message'=>$e->getMessage()]);
			}
		}
	}

	private function decode_lecode($lecode, $way) {
		$this->load->library('common/code_encoder');

		// 获取明码
		if ($way == SELF::OUTTER_INNER) {
			$decode_result = $this->code_encoder->decode_pub($lecode);
			if ($decode_result->errcode !== 0) {
				throw new Exception($decode_result->errmsg . '！');
			}

			$decode_result = $decode_result->result;
		}

		// 获取暗码
		if ($way == SELF::INNER_OUTTER) {
			$decode_result = $this->code_encoder->decode($lecode);
			if(empty($decode_result)){
				throw new Exception('此乐码无法解析');
			}
			if (!isset($decode_result) || $decode_result->errcode !== 0) {
				throw new Exception($decode_result->errmsg);
			}

			$decode_result = $decode_result->result;
			$merchant = $this->merchant_model->get_by_code($decode_result->mch_code);
			if (! isset($merchant)) {
				throw new Exception('商户不存在', 1);
			}
			$sql = "SELECT batchNo
					FROM batchs
					WHERE ? >= start
					AND ? <= end
					AND mchId = ?";
			$batch_no_result = $this->db->query($sql, [$decode_result->value, $decode_result->value, $merchant->id])->result();

			if (count($batch_no_result) == 0) {
				throw new Exception('找不到乐码批次'. count($batch_no_result), 1);
			}

			/**
			 * 当查询结果为一条数据的时候是正常业务，否则为异常状态
			 */
			if (count($batch_no_result) != 1) {
				throw new Exception('数据异常' . count($batch_no_result));
			}

			return [
				'pub_code' => hls_encode_pub($decode_result->value),
				'mch_name' => $merchant->name,
				'batch_no' => $batch_no_result[0]->batchNo
			];
		}
	}

}
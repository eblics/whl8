<?php
class Account extends MerchantController {

    // public function update_token() {
    //     $this->load->model('Merchant_model', 'merchant');
    //     $merchants = $this->merchant->get_all();
    //     foreach ($merchants as $merchant) {
    //         if ($merchant->wxAuthStatus == BoolEnum::No) {
    //             $accessToken = $this->weixin_rest_api->get_access_token(
    //                 $merchant->wxAppId, $merchant->wxAppSecret);
    //             $this->merchant->update_base_token(
    //                 $merchant->wxAppId, $accessToken, time() + 7000);
    //         }
    //         // if ($merchant->wxAuthStatus_shop == BoolEnum::No) {
    //         //     $accessToken = $this->weixin_rest_api->get_access_token(
    //         //         $merchant->wxAppId_shop, $merchant->wxAppSecret_shop);
    //         //     $this->merchant->update_base_token_shop(
    //         //         $merchant->wxAppId_shop, $accessToken, time() + 7000);
    //         // }
    //     }
    //     print 'update base token ok.'.PHP_EOL;
    // }
}
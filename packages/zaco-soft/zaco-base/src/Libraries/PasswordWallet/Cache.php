<?php
namespace ZacoSoft\ZacoBase\Libraries\PasswordWallet;

use DB;

class Cache
{
    public function getCacheCategoryCommon()
    {
        $cacheKey = 'pw.cache.category.common.2';
        $cacheData = getCacheData($cacheKey);
        if (isEmpty($cacheData)) {
            $data = DB::table('default_password_wallet_categories')
                ->whereNull('pwct_user_id')
                ->get();
            foreach ($data as $item) {
                $cacheData[$item->pwct_id] = $item->pwct_name;
            }

            setCacheData($cacheKey, $cacheData, 3600 * 12);
        }
        return $cacheData;
    }
}

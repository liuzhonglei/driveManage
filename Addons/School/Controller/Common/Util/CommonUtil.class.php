<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/11/10
 * Time: 10:46
 */

namespace Addons\School\Controller\Common\Util;


class CommonUtil {
    /**
     * convert  the records to condition str
     * @param $array
     * @return string
     */
    public static function  getArrayStr($array)
    {
        $return = "";
        foreach ($array as $record) {
            if (!empty($return)) {
                $return .= ",";
            }
            foreach ($record as $key => $value) {
                $return .= "'" . $value . "'";
                break;
            }

        }
        return $return;
    }
}
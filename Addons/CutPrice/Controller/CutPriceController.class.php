<?php

namespace Addons\CutPrice\Controller;

/**
 * Class CutPriceController
 * @package Addons\CutPrice\Controller
 */
class CutPriceController extends BaseController {
    /**
     * list the data
     */
    function lists(){
        $this->model = $this->getModel ( 'cut_price' );
        $this->listsTable = "cut_price_all";
        $this->assign('add_button',0);
        $this->assign('del_button',0);
        parent::lists();
    }

    /**
     * list the data count
     */
    function listsCount(){
        $this->model = $this->getModel ( 'cut_price_count' );
        $this->listsTable = "cut_price_count";
        $_POST['token'] = get_token();
        $this->assign('add_button',0);
        $this->assign('del_button',0);
        parent::common_lists($this->model,0,"lists" );
    }
}

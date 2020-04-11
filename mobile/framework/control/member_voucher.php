<?php
/**
 * 我的代金券
 *
 * @好商城V4 (c) 2015-2016 33hao Inc.
 * @license    http://www.haoid.cn
 * @link       交流群号：216611541
 * @since      好商城提供技术支持 授权请购买shopnc授权
 */

defined('InShopNC') or exit('Access Invalid!');

class member_voucherControl extends mobileMemberControl {

	public function __construct() {
		parent::__construct();
	}

    /**
     * 地址列表
     */
    public function voucher_listOp() {
		$model_voucher = Model('voucher');
        $voucher_list = $model_voucher->getMemberVoucherList($this->member_info['member_id'], $_POST['voucher_state'], $this->page);
        $page_count = $model_voucher->gettotalpage();

        output_data(array('voucher_list' => $voucher_list), mobile_page($page_count));
    }

    public function voucher_freeexOp(){
        $voucher_t_id = $_REQUEST['tid'];
        $username = $this->member_info['member_name'];
        if($username == ""){
            output_data("请登录");
            exit();
        }else{
            $t_voucher = Model()->table("voucher_template")->where("voucher_t_id=$voucher_t_id")->find();
            $num = $t_voucher['voucher_t_giveout'];
            $total = $t_voucher['voucher_t_total'];
            if($num >= $total){
                output_data("代金券已领取空！");
                exit();
            }else{
                $map['voucher_t_id'] = $voucher_t_id;
                $map['voucher_title'] = $t_voucher['voucher_t_title'];
                $map['voucher_desc'] = $t_voucher['voucher_t_desc'];
                $map['voucher_start_date'] = $t_voucher['voucher_t_start_date'];
                $map['voucher_end_date'] = $t_voucher['voucher_t_end_date'];
                $map['voucher_price'] = $t_voucher['voucher_t_price'];
                $map['voucher_store_id'] = $t_voucher['voucher_t_store_id'];
                $map['voucher_limit'] = $t_voucher['voucher_t_limit'];
                $map['voucher_state'] = 1;
                $map['voucher_active_date'] = time();
                $map['voucher_owner_id'] = $this->member_info['member_id'];
                $map['voucher_owner_name'] = $username;
                $rs = Model()->table("voucher")->insert($map);
                if($rs){
                    $nums['voucher_t_giveout'] = $num+1;
                    $r = Model()->table("voucher_template")->where("voucher_t_id=$voucher_t_id")->update($nums);
                    output_data("领取成功!");
                    exit();
                }else{
                    output_data("领取失败!");
                    exit();
                }
            }
            
        }
    }
}

<?php
/**
 * 会员管理
 *
 *
 *
 **by 好商城V3 www.haoid.cn 运营版*/

defined('InShopNC') or exit('Access Invalid!');

class member_shenfenControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('sns_member');
	}
	/**
	 * 身份管理
	 */
	public function indexOp(){
		$this->member_shenfenOp();
	}

	/**
	 * 身份列表
	 */
	public function member_shenfenOp(){
		// 实例化模型
		$model = Model();
		if(chksubmit()){
			switch ($_POST['submit_type']){
				case 'del':
					$condition['mshenfen_id'] = array('in',implode(',', $_POST['id']));
					$result = $model->table('mshenfen')->where($condition)->delete();
					if ($result){
						showMessage(Language::get('nc_common_op_succ'));
					}else {
						showMessage(Language::get('nc_common_op_fail'));
					}
					break;
			}
		}
		$shenfen_list = $model->table('mshenfen')->order('mshenfen_sort asc')->page(10)->select();
		Tpl::output('showpage', $model->showpage(2));
		Tpl::output('shenfen_list', $shenfen_list);
		Tpl::showpage('sns_membershenfen.index');
	}
	/**
	 * 添加标签
	 */
	public function shenfen_addOp(){
		
		if(chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
					array("input"=>$_POST["membershenfen_name"], "require"=>"true", "message"=>'会员身份名称不能为空！'),
					array("input"=>$_POST["membershenfen_sort"], "require"=>"true", 'validator'=>'Number', "message"=>'会员身份排序只能为数字'),
			);
			$error = $obj_validate->validate();
			if($error != ''){
				showMessage($error);
			}else{
				$insert = array(
						'mshenfen_name'=>$_POST['membershenfen_name'],
						'mshenfen_sort'=>intval($_POST['membershenfen_sort']),
						'is_show'=>intval($_POST['is_show']),
						'mshenfen_time'=>time()
					);
				$model = Model();
				$result = $model->table('mshenfen')->insert($insert);
				if ($result){
					$url = array(
							array(
									'url'=>'index.php?act=member_shenfen&op=shenfen_add',
									'msg'=>Language::get('sns_member_add_once_more'),
							),
							array(
									'url'=>'index.php?act=member_shenfen&op=member_shenfen',
									'msg'=>Language::get('sns_memner_return_list'),
							)
					);
					$this->log(L('nc_add,sns_member_shenfen').'['.$_POST['membertag_name'].']',1);
					showMessage(Language::get('nc_common_op_succ'),$url);
				}else {
					showMessage(Language::get('nc_common_op_fail'));
				}
			}
		}

		Tpl::showpage('sns_membershenfen.add');
	}
	/**
	 * 编辑身份
	 */
	public function shenfen_editOp(){
		// 实例化模型
		$model = Model();

		if(chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
					array("input"=>$_POST["membershenfen_name"], "require"=>"true", "message"=>'会员身份名称不能为空！'),
					array("input"=>$_POST["membershenfen_sort"], "require"=>"true", 'validator'=>'Number', "message"=>'会员身份排序只能为数字'),
			);
			$error = $obj_validate->validate();
			if($error != ''){
				showMessage($error);
			}else{
				$update = array();
				$update['mshenfen_name']		= trim($_POST['membershenfen_name']);
				$update['mshenfen_sort']		= intval($_POST['membershenfen_sort']);
				$update['is_show']	= intval($_POST['is_show']);
				

				$result = $model->table('mshenfen')->where(array('mshenfen_id'=>$_POST['mshenfen_id']))->update($update);
				if ($result){
					$this->log(L('nc_edit,sns_member_shenfen').'['.$_POST['membershenfen_name'].$_POST['mshenfen_id'].']',1);
					showMessage(Language::get('nc_common_op_succ'),'index.php?act=member_shenfen&op=member_shenfen');
				}else {
					showMessage(Language::get('nc_common_op_fail'));
				}
			}
		}
		// 验证
		$mshenfen_id = intval($_GET['id']);
		if($mshenfen_id <= 0){
			showMessage(Language::get('param_error'),'','','error');
		}
		
		$mshenfen_info = $model->table('mshenfen')->where(array('mshenfen_id'=>$mshenfen_id))->find();
		if(empty($mshenfen_info)){
			showMessage(Language::get('param_error'),'','','error');
		}
		Tpl::output('mshenfen_info', $mshenfen_info);
		Tpl::showpage('sns_membershenfen.edit');
	}
	/**
	 * 删除身份
	 */
	public function shenfen_delOp(){
		// 验证
		$mshenfen_id = intval($_GET['id']);
		
		if($mshenfen_id <= 0){
			showMessage(Language::get('param_error'),'','','error');
		}
		$model = Model();
		$result = $model->table('mshenfen')->where(array('mshenfen_id'=>$mshenfen_id))->delete();
		if ($result){
			$this->log(L('nc_del,sns_member_shenfen').'[ID:'.$mshenfen_id.']',1);
			showMessage(Language::get('nc_common_del_succ'));
		}else {
			showMessage(Language::get('nc_common_del_fail'));
		}
	}
	
	/**
	 * ajax修改
	 */
	public function ajaxOp(){
		// 实例化模型
		$model = Model();
		switch ($_GET['branch']){
			/**
			 * 更新名称、排序、推荐
			 */
			case 'mshenfen_name':
			case 'mshenfen_sort':
			case 'is_show':
				$update = array(
					$_GET['column']=>$_GET['value']
				);
				$model->table('mshenfen')->where(array('mshenfen_id'=>intval($_GET['id'])))->update($update);
				echo 'true';
				break;
		}
	}
	

}
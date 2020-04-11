<?php
/*
 * 论坛接口
 * author:lee
 * 2016.7.18
 */

defined('InShopNC') or exit('Access Invalid!');

class circleControl extends BaseCircleControl{
	public function __construct(){
		Language::read('circle');
		parent::__construct();
	}

	public function themelistOp(){
		$where = array();
		$where['circle_id']	= $_POST['c_id'];
		$theme_list = Model()->table('circle_theme')->where($where)->order('is_stick desc,lastspeak_time desc')->select();
		$c_id  = $_POST['c_id'];
		$circle_info = Model()->table('circle')->where("circle_id=$c_id")->find();
		$arr['list'] = $theme_list;
		$arr['circle_name'] = $circle_info['circle_name'];
		$arr['circle_mastername'] = $circle_info['circle_mastername'];
		$arr['circle_img'] = 'http://www.nrwspt.com/data/upload/circle/group/'.$circle_info['circle_img'];

		$res = getarrres('200','查询成功',$arr);
		exit(json_encode($res));
	}

	/**
	 * 发帖
	 * (post)key:令牌  thclass_name：类型 name：标题 themecontent：内容 c_id:讨论区id
	 */
	public function savethemeOp(){
		$c_id  = $_POST['c_id'];
		$circle_info = Model()->table('circle')->where("circle_id=$c_id")->find();
		$key  = $_POST['key'];
		$token = Model('mb_user_token');
	    $data['token'] = $key;
	    $tokens = $token->where($data)->find();
	    $member_id = $tokens['member_id'];
	    $member_name = $tokens['member_name'];
		// Reply function does close,throw error.
		if(!intval(C('circle_istalk'))){
			$res = getarrres('-401',L('circle_theme_cannot_be_published'),0);
        	exit(json_encode($res));
		}
		// checked cookie of SEC
		if(cookie(circle_intervaltime)){
			// showDialog(L('circle_operation_too_frequent'));
			$res = getarrres('-401',L('circle_operation_too_frequent'),1);
        	exit(json_encode($res));
		}

		// 不是圈子成员不能发帖
		// if(!in_array($this->identity, array(1,2,3))){
		// 	// showDialog(L('circle_no_join_ban_release'));
		// 	$res = getarrres('-401',L('circle_no_join_ban_release'),2);
		//  exit(json_encode($res));
		// }

		$model = Model();

		/**
		 * 验证 //标题
		 */
		$obj_validate = new Validate();
		$validate_arr[] = array("input"=>$_POST["name"], "require"=>"true","message"=>Language::get('nc_name_not_null'));
		$validate_arr[] = array("input"=>$_POST["name"], "validator"=>'Length',"min"=>4,"max"=>30,"message"=>Language::get('nc_name_min_max_length'));
		//内容
		$validate_arr[] = array("input"=>$_POST["themecontent"], "require"=>"true","message"=>Language::get('nc_content_not_null'));
		if(intval(C('circle_contentleast')) > 0) $validate_arr[] = array("input"=>$_POST["themecontent"],"validator"=>'Length',"min"=>intval(C('circle_contentleast')),"message"=>Language::get('circle_contentleast'));
		$obj_validate -> validateparam = $validate_arr;
		$error = $obj_validate->validate();
		if ($error != ''){
			$res = getarrres('-401',$error,0);
        	exit(json_encode($res));
		}

		$insert = array();
		$insert['theme_name']	= $_POST['name'];
		$insert['theme_content']= $_POST['themecontent'];
		$insert['circle_id']	= $c_id;
		$insert['circle_name']	= $circle_info['circle_name'];
		// $insert['thclass_id']	= $thclass_id;
		// $insert['thclass_name'] = $thclass_name;
		$insert['member_id']	= $member_id;
		$insert['member_name']	= $member_name;
		$insert['is_identity']	= 1;
		$insert['theme_addtime']= time();
		$insert['lastspeak_time']= time();
		$insert['lastspeak_name']= $member_name;
		$insert['theme_special']= intval($_GET['sp']);
		$themeid = $model->table('circle_theme')->insert($insert);
		if($themeid){
			$has_goods = 0;	// 存在商品标记
			$has_affix = 0;// 存在附件标记
			// 更新话题附件
			$model->table('circle_affix')->where(array('affix_type'=>1, 'member_id'=>$member_id, 'theme_id'=>0))->update(array('theme_id'=>$themeid, 'circle_id'=>$c_id));

			// 更新话题信息
			$affixe_count = $model->table('circle_affix')->where(array('affix_type'=>1, 'member_id'=>$member_id, 'theme_id'=>$themeid))->count();
			if($affixe_count > 0){
				$has_affix = 1;
			}
			if($has_goods || $has_affix){
				$update = array();
				$update['theme_id']		= $themeid;
				$update['has_goods']	= $has_goods;
				$update['has_affix']	= $has_affix;
				$model->table('circle_theme')->update($update);
			}

			// 更新圈子表话题数
			$update = array(
						'circle_id'=>$c_id,
						'circle_thcount'=>array('exp', 'circle_thcount+1')
					);
			$model->table('circle')->update($update);

			// 更新用户相关信息
			$update = array(
						'cm_thcount'=>array('exp', 'cm_thcount+1'),
						'cm_lastspeaktime'=>time()
					);
			$model->table('circle_member')->where(array('member_id'=>$member_id, 'circle_id'=>$c_id))->update($update);

			// set cookie of SEC
			if(intval(C('circle_intervaltime')) > 0){
				setNcCookie('circle_intervaltime', true, intval(C('circle_intervaltime')));
			}

			// Experience
			$param = array();
			$param['member_id']		= $member_id;
			$param['member_name']	= $member_name;
			$param['circle_id']		= $c_id;
			$param['type']			= 'release';
			$param['itemid']		= $themeid;
			Model('circle_exp')->saveExp($param);
			$res = getarrres('200',L('nc_release_op_succ'),'succ');
    		exit(json_encode($res));
		}else{
			// var_dump(L('nc_release_op_fail'));
			$res = getarrres('-401',L('nc_release_op_fail'),0);
        	exit(json_encode($res));
		}
	}

	//回复
	public function replyOp(){
		$map['theme_id']  = $_POST['theme_id'];
		$map['circle_id']  = $_POST['circle_id'];
		$map['reply_title']  = $_POST['reply_title'];
		$map['reply_content']  = $_POST['reply_content'];
		$key  = $_POST['key'];
		$token = Model('mb_user_token');
	    $data['token'] = $key;
	    $tokens = $token->where($data)->find();
	    $map['member_id'] = $tokens['member_id'];
	    $map['member_name'] = $tokens['member_name'];
	    $map['reply_addtime'] = time();
	    $rs = Model()->table("circle_threply")->insert($map);
	    if($rs){
	    	$maps['theme_id'] = $_POST['theme_id'];
			$fields = "theme_commentcount";
			$theme = Model()->table("circle_theme")->field($fields)->where($maps)->find();
			$umap['theme_commentcount'] = intval($theme['theme_commentcount'])+1;
			Model()->table("circle_theme")->where($maps)->update($umap);
	    	$res = getarrres('200','已回复',1);
	    }else{
	    	$res = getarrres('-401','回复失败',0);
	    }
	    exit(json_encode($res));
	}

	//帖子详情
	public function themeinfoOp(){
		$theme_id = isset($_POST['theme_id'])?$_POST['theme_id']:'';
		$map['theme_id'] = $theme_id;
		$field = "member_name,theme_name,theme_content,theme_likecount,theme_commentcount";
		$theme = Model()->table("circle_theme")->field($field)->where($map)->find();
		$res = getarrres('200','帖子详情',$theme);
		exit(json_encode($res));
	}

	//回复详情
	public function replyinfoOp(){
		$theme_id = isset($_POST['theme_id'])?$_POST['theme_id']:'';
		$map['theme_id'] = $theme_id;
		$field = "member_name,reply_content,reply_title,reply_addtime";
		$theme = Model()->table("circle_threply")->field($field)->where($map)->select();
		$res = getarrres('200','回复详情',$theme);
		exit(json_encode($res));
	}

	//搜索 
	public function sousuoOp(){
		$content = $_POST['content'];
		$map['circle_name'] = array('like','%'.$content.'%');
		$circle = Model()->table("circle")->where($map)->select();
		foreach ($circle as $key => $value) {
			$circle[$key]['circle_img'] = 'http://www.nrwspt.com/data/upload/circle/group/'.$value['circle_img'];
		}
		$res = getarrres('200','帖子列表',$circle);
		exit(json_encode($res));
	}


	/**
	 * 个人信息
	 * (post)key:令牌
	 */
	public function membcircleOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','未找到令牌',0);
        	exit(json_encode($res));
		}
		$field = "member_id,cm_thcount,cm_comcount";
		$cm_infos = Model()->table('circle_member')->field($field)->where(array('member_id'=>$memberid))->find();
		if(empty($cm_infos)){
			$bb['el_itemid'] = array('like','%3%');
			$bb['member_id'] = $memberid;
			$aaa = Model()->table('circle_theme')->where(array('member_id'=>$memberid))->select();
			$bbb = Model()->table('circle_theme')->where($bb)->select();
			$cm_infos['cm_thcount'] = count($aaa);
			$cm_infos['cm_comcount'] = count($bbb);
		}
		$fields = "member_name,member_truename,member_avatar";
		$member = Model()->table("member")->field($fields)->where(array('member_id'=>$memberid))->find();
		$member['avatar'] = getMemberAvatar($member['member_avatar']);
		$cm_info = array_merge($cm_infos,$member);
		$res = getarrres('200','成功',$cm_info);
    	exit(json_encode($res));
	}

	/**
	 * 个人中心:发表的帖子
	 * (post)key:令牌
	 */
	public function makecircleOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','未找到令牌',0);
        	exit(json_encode($res));
		}
		$model = Model();
		$theme_list = $model->table('circle_theme')->where(array('member_id'=>$memberid))->order('theme_id desc')->select();
		if(!empty($theme_list)){
			$theme_list = array_under_reset($theme_list, 'theme_id');
			$themeid_array = array(); $circleid_array = array();
			foreach ($theme_list as $val){
				$themeid_array[]	= $val['theme_id'];
				$circleid_array[]	= $val['circle_id'];
			}
			$themeid_array = array_unique($themeid_array);
			$circleid_array = array_unique($circleid_array);

			// affix
			$affix_list = $model->table('circle_affix')->where(array('affix_type'=>1, 'member_id'=>$memberid, 
				'theme_id'=>array('in', $themeid_array)))->select();
			$affix_list = array_under_reset($affix_list, 'theme_id', 2);

			// like
			$like_list = $model->table('circle_like')->where(array('theme_id'=>array('in', $themeid_array)))->select();
			$like_list = array_under_reset($like_list, 'theme_id');

		}
		$arr['affix_list'] = $affix_list;
		$arr['like_list'] = $like_list;
		$res = getarrres('200','成功',$theme_list);
    	exit(json_encode($res));
	}

	/**
	 * 个人中心:赞过的帖子
	 * (post)key:令牌
	 */
	public function likecircleOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','未找到令牌',0);
        	exit(json_encode($res));
		}
		$model = Model();
		$like_array = $model->table('circle_like')->field('circle_id,theme_id')->where(array('member_id'=>$memberid))->order('theme_id desc')->select();
		if(!empty($like_array)){
			$theme_list = array_under_reset($like_array, 'theme_id');
			$themeid_array = array(); $circleid_array = array();
			foreach ($theme_list as $val){
				$themeid_array[]	= $val['theme_id'];
				$circleid_array[]	= $val['circle_id'];
			}
			$themeid_array = array_unique($themeid_array);
			$circleid_array = array_unique($circleid_array);
			// theme
			$theme_list = $model->table('circle_theme')->where(array('theme_id'=>array('in', $themeid_array)))->select();
			// affix
			$affix_list = $model->table('circle_affix')->where(array('affix_type'=>1, 'theme_id'=>array('in', $themeid_array)))->select();
			$affix_list = array_under_reset($affix_list, 'theme_id', 2);

		}
		$arr['affix_list'] = $affix_list;
		$arr['theme_list'] = $theme_list;
		$res = getarrres('200','成功',$theme_list);
    	exit(json_encode($res));
	}

	/**
	 * 维修专区
	 * (post)class_id:分类 class_name:分类名称
	 */
	public function groupOp(){
		$model = Model();
		$where = array();
		$where['circle_status'] = 1;
		$class_name = $_GET['class_name'];
		if($_GET['keyword'] != ''){
			$where['circle_name|circle_tag'] = array('like', '%'.$_GET['keyword'].'%');
		}
		if(intval($_GET['class_id']) > 0){
			$where['class_id'] = intval($_GET['class_id']);
		}
		$circle_list = $model->table('circle')->where($where)->select();
		foreach($circle_list as $k => $v) {
			$circle_list[$k]['image'] = circleLogo($v['circle_id']);
		}
		$circle_listarr['class_name'] = $class_name;
		$circle_listarr['list'] = $circle_list;
		$res = getarrres('200','成功',$circle_listarr);
    	exit(json_encode($res));
	}

	//最新帖子
	public function newcircleOp(){
		$model = Model();

		// 最新      **显示3个圈子，按推荐随机排列，推荐不够按成员数主题数降序排列**
		$circle_list = $model->table('circle')->field('*')->where(array('circle_status'=>1))->order('circle_addtime desc')->limit(3)->select();
		if(!empty($circle_list)){
			$circle_list = array_under_reset($circle_list, 'circle_id');$circleid_array = array_keys($circle_list);
			// 查询圈子最新主题
			foreach($circle_list as $key=>$val){
				// 最新的两条数据
				$theme_list = $model->table('circle_theme')->where(array('circle_id'=>$val['circle_id'], 'is_closed'=>0))->order('theme_id desc')->limit(1)->select();
				$circle_list[$key]['theme_list'] = $theme_list;
			}
		}
		$res = getarrres('200','成功',$circle_list);
    	exit(json_encode($res));
	}

	//推荐圈子
	public function recircleOp(){
		$model = Model();

		$rcircle_list = $model->table('circle')->field('*, is_recommend*rand() as rand')->where(array('circle_status'=>1, 'is_recommend'=>1))->order('rand desc')->limit('20')->select();
		foreach ($rcircle_list as $key => $val) {
			$rcircle_list[$key]['image'] = circleLogo($val['circle_id']);
		}
		$res = getarrres('200','成功',$rcircle_list);
    	exit(json_encode($res));
	}

	/**
	 * 首页幻灯片
	 */
	public function loginpicOp(){
		$loginpic = unserialize(C('circle_loginpic'));
		foreach ($loginpic as $k => $v) {
			$loginpic[$k]['pic'] = 'http://www.nrwspt.com/data/upload/circle/'.$v['pic'];
		}
		$res = getarrres('200','成功',$loginpic);
    	exit(json_encode($res));
	}


	/**
	 * 最热帖子
	 */
	public function hotcircleOp(){
		$model = Model();

		// 热门圈子      **显示3个圈子，按推荐随机排列，推荐不够按成员数主题数降序排列**
		$circle_list = $model->table('circle')->field('*, is_hot*rand() as rand')->where(array('circle_status'=>1, 'is_hot'=>1))->order('rand desc')->limit(3)->select();
		if(!empty($circle_list)){
			$circle_list = array_under_reset($circle_list, 'circle_id');$circleid_array = array_keys($circle_list);
			// 查询圈子最新主题
			foreach($circle_list as $key=>$val){
				// 最新的两条数据
				$theme_list = $model->table('circle_theme')->where(array('circle_id'=>$val['circle_id'], 'is_closed'=>0))->order('theme_id desc')->limit(1)->select();
				$circle_list[$key]['theme_list'] = $theme_list;
			}
		}
		$res = getarrres('200','成功',$circle_list);
    	exit(json_encode($res));
	}

	/**
	 * 创建圈子
	 */
	public function add_groupOp(){
		if($_SESSION['is_login'] != 1){
			@header('location: '.SHOP_SITE_URL.'/index.php?act=login&ref_url='.getRefUrl());
		}
		if(!intval(C('circle_iscreate'))){
			showMessage(L('circle_grooup_not_create'), '', '', 'error');
		}
		$model = Model();
		// 在验证
		// 允许创建圈子验证
		$where = array();
		$where['circle_masterid'] = $_SESSION['member_id'];
		$create_count = $model->table('circle')->where($where)->count();
		if(intval($create_count) >= C('circle_createsum')) showDialog(L('circle_create_max_error'));

		// 允许加入圈子验证
		$where = array();
		$where['member_id']	= $_SESSION['member_id'];
		$join_count = $model->table('circle_member')->where($where)->count();
		if(intval($join_count) >= C('circle_joinsum')) showDialog(L('circle_join_max_error'));

		if(chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
					array("input"=>$_POST["c_name"], "require"=>"true", "message"=>L('circle_name_not_null'))
			);
			$error = $obj_validate->validate();
			if($error != ''){
				showDialog($error);
			}else{
				$insert = array();
				$insert['circle_name']			= $_POST['c_name'];
				$insert['circle_masterid']		= $_SESSION['member_id'];
				$insert['circle_mastername']	= $_SESSION['member_name'];
				$insert['circle_desc']			= $_POST['c_desc'];
				$insert['circle_tag']			= $_POST['c_tag'];
				$insert['circle_pursuereason']	= $_POST['c_pursuereason'];
				$insert['circle_status']		= 2;
				$insert['is_recommend']			= 0;
				$insert['class_id']				= intval($_POST['class_id']);
				$insert['circle_addtime']		= time();
				$insert['circle_mcount']		= 1;
				$result = $model->table('circle')->insert($insert);
				if($result){
					// Membership level information
					$data = rkcache('circle_level') ? rkcache('circle_level') : rkcache('circle_level', true);

					// 把圈主信息加入圈子会员表
					$insert = array();
					$insert['member_id']	= $_SESSION['member_id'];
					$insert['circle_id']	= $result;
					$insert['circle_name']	= $_POST['c_name'];
					$insert['member_name']	= $_SESSION['member_name'];
					$insert['cm_applytime']	= $insert['cm_jointime'] = time();
					$insert['cm_state']		= 1;
					$insert['cm_level']		= $data[1]['mld_id'];
					$insert['cm_levelname']	= $data[1]['mld_name'];
					$insert['cm_exp']		= 1;
					$insert['cm_nextexp']	= $data[2]['mld_exp'];
					$insert['is_identity']	= 1;
					$insert['cm_lastspeaktime'] = '';
					$model->table('circle_member')->insert($insert);

					showDialog(L('nc_common_op_succ'),'index.php?act=group&c_id='.$result, 'succ');
				}else{
					showDialog(L('nc_common_op_fail'));
				}
			}
		}
		Tpl::output('create_count', $create_count);
		Tpl::output('join_count', $join_count);

		// 圈子分类
		$class_list = $model->table('circle_class')->where(array('class_status'=>1))->order('class_sort asc')->select();
		Tpl::output('class_list', $class_list);

		$this->circleSEO(L('circle_create'));
		Tpl::showpage('group_add');
	}
	/**
	 * 我加入的讨论区
	 */
	public function myjoinedcircleOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','未找到令牌',0);
        	exit(json_encode($res));
		}
		$model = Model('circle_member');

		$cm_list = $model->getCircleMemberList(array('member_id'=>$memberid, 'circle_id' => array('neq', 0)),'circle_id,circle_name,is_identity', 0, 'is_identity asc');
		if (empty($cm_list)) {
			$res = getarrres('-401','无记录',0);
        	exit(json_encode($res));
		}
		if (strtoupper(CHARSET) == 'GBK'){
		    $cm_list = Language::getUTF8($cm_list);
		}
		$res = getarrres('200','成功',$cm_list);
    	exit(json_encode($res));
	}
	/**
	 * 圈子名称验证
	 */
	public function check_circle_nameOp(){
		$name = $_GET['name'];
		if (strtoupper(CHARSET) == 'GBK'){
			$name = Language::getGBK($name);
		}
		$rs = Model()->table('circle')->where(array('circle_name'=>$name))->find();
		if (!empty($rs)){
			$res = getarrres('200','验证失败',0);
		}else{
			$res = getarrres('200','验证成功',1);
		}
    	exit(json_encode($res));
	}
}

<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="wrap">
    <div class="tabmenu">
        <?php include template('layout/submenu'); ?>
    </div>
    <div class="ncm-default-form">
        <form method="post" id="cash_form" action="index.php?act=predeposit&op=pd_cash_add">
            <input type="hidden" name="form_submit" value="ok" />
            <dl>
                <dt><i class="required">*</i>提现金额：</dt>
                <dd><input name="pdc_amount" type="text" class="text w50" id="pdc_amount" maxlength="10" ><em class="add-on">
                        <i class="icon-renminbi"></i></em> （当前可用金额：<strong class="orange"><?php echo floatval($output['member_info']['available_predeposit']); ?></strong>&nbsp;&nbsp;元）<span></span>
                    <p class="hint mt5"></p>
                </dd>
            </dl>
            <!--      <dl>
                    <dt><i class="required">*</i>收款银行：</dt>
                    <dd><input name="pdc_bank_name" type="text" class="text w200" id="pdc_bank_name" maxlength="40"/><span></span>
                      <p class="hint">强烈建议优先填写国有4大银行(中国银行、中国建设银行、中国工商银行和中国农业银行)
            请填写详细的开户银行分行名称，虚拟账户如支付宝、财付通填写“支付宝”、“财付通”即可。</p>
                    </dd>
                  </dl>-->
            <dl>
                <dt><i class="required">*</i>收款方式：</dt>
                <dd>
                    <select name="pdc_bank_name" class="text w200" id="pdc_bank_name" maxlength="40"/>
                <option value="0">微信</option>
                </select>
                <script type="text/javascript">
                    $(function () {
                        $("dl.wrp_code").hide();
                        $("#checked").on('click', function () {
                            window.location = "http://www.nrwspt.com/shop/index.php?act=member_connect&op=weixinbind";
                        });
                        $("#checked").on('click', function () {
                            //console.log( $(this).parents("dl").find("p.hint").html());
                            $.ajax({
                                //几个参数需要注意一下
                                type: "GET", //方法类型
                                dataType: "json", //预期服务器返回的数据类型
                                url: "index.php?act=member_security&op=checked_weixin", //url
                                data: "",
                                success: function (_rs) {
                                    //console.log(_rs);//打印服务端返回的数据(调试用)
                                    if (_rs.code == 1) {
                                        $("#checked").parents("dl").find("p.hint").html(_rs.msg);
                                    } else if (_rs.code == 0) { //没有绑定微信信息
                                        $("#checked").parents("dl").find("p.hint").html(_rs.msg);
                                        $("dl.wrp_code").show();
                                    } else if (_rs.code == 2) { //已经绑定了微信信息
                                        $("#checked").parents("dl").find("p.hint").html(_rs.msg);
                                    }
                                }
                            });
                        })
                    })
                </script>
                <span style="border:1px solid #CCC;cursor: pointer" id="checked">点击确认</span>
                <p class="hint">提示:用户账号信息必须和微信账户相关联,且只能提现到相关联的微信账户</p>
                </dd>
            </dl>
            <dl class="wrp_code">
                <span style="border:1px solid #CCC;cursor: pointer" id="touch">点击关联微信账号</span> 
            </dl>
            <!--            
                                    <dl>
                                        <dt><i class="required">*</i>收款账号：</dt>
                                        <dd><input name="pdc_bank_no" type="text" class="text w200" id="pdc_bank_no" maxlength="30"/><span></span>
                                            <p class="hint">银行账号或虚拟账号(支付宝、财付通等账号)</p>
                                        </dd>
                                    </dl>
            -->   
            <dl>
                <dt><i class="required">*</i>开户人姓名：</dt>
                <dd><input name="pdc_bank_user" type="text" class="text w100" id="pdc_bank_user" maxlength="10"/><span></span>
                    <p class="hint">收款账号的开户人姓名</p>
                </dd>
            </dl>
            <dl>
                <dt><i class="required">*</i>支付密码：</dt>
                <dd><input name="password" type="password" class="text w100" id="password" maxlength="20"/><span></span>
                    <p class="hint">
                        <?php if (!$output['member_info']['member_paypwd']) { ?>
                            <strong class="red">还未设置支付密码</strong><a href="<?php echo SHOP_SITE_URL; ?>/index.php?act=member_security&op=auth&type=modify_paypwd" class="ncm-btn-mini ncm-btn-acidblue vm ml10" target="_blank">马上设置</a>
                        <?php } ?>
                    </p>
                </dd>
            </dl>
            <dl class="bottom"><dt>&nbsp;</dt>
                <dd><label class="submit-border"><input type="submit"  class="submit" value="确认提现" /></label><a class="ncm-btn ml10" href="javascript:history.go(-1);">取消并返回</a></dd>
            </dl>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#cash_form').validate({
            submitHandler: function (form) {
                ajaxpost('cash_form', '', '', 'onerror')
            },
            errorPlacement: function (error, element) {
                var error_td = element.parent('dd').children('span');
                error_td.append(error);
            },
            rules: {
                pdc_amount: {
                    required: true,
                    number: true,
                    min: 0.01,
                    max: <?php echo floatval($output['member_info']['available_predeposit']); ?>
                },
                pdc_bank_name: {
                    required: true
                },
                pdc_bank_no: {
                    required: true
                },
                pdc_bank_user: {
                    required: true
                },
                password: {
                    required: true
                }
            },
            messages: {
                pdc_amount: {
                    required: '<i class="icon-exclamation-sign"></i>请正确输入提现金额',
                    number: '<i class="icon-exclamation-sign"></i>请正确输入提现金额',
                    min: '<i class="icon-exclamation-sign"></i>请正确输入提现金额',
                    max: '<i class="icon-exclamation-sign"></i>请正确输入提现金额'
                },
                pdc_bank_name: {
                    required: '<i class="icon-exclamation-sign"></i>请输入收款银行'
                },
                pdc_bank_no: {
                    required: '<i class="icon-exclamation-sign"></i>请输入收款账号'
                },
                pdc_bank_user: {
                    required: '<i class="icon-exclamation-sign"></i>请输入开户人姓名'
                },
                password: {
                    required: '<i class="icon-exclamation-sign"></i>请输入支付密码'
                }
            }
        });
    });
</script>
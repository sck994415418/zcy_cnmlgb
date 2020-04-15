<head>
    <style>
        .tabmenu .tab {
            height: 36px;
            border-bottom: none;
        }
        .tabmenu .tab li {
            float: left;
            margin-right: 3px;
        }
        .tabmenu .tab .normal a {
            font: lighter 12px/33px "microsoft yahei";
            color: #FFF;
            background-color: #AAA;
            display: inline-block;
            height: 33px;
            padding: 0 5px;
            margin-top: 15px;
        }
        .tabmenu .tab .active a {
            font: 18px/35px "microsoft yahei";
            color: #FFF;
            background-color: #27A9E3;
            display: inline-block;
            height: 33px;
            padding: 0 5px;
            cursor: default;
            margin-top: 15px;
        }
        .sticky .tabmenu {
            width: 960px;
            padding-top: 10px;
            padding-bottom: 10px;
            position: fixed;
            top: 0;
        }

        .tabmenu {
            background-color: #FFF;
            width: 100%;
            height: 120px;
            display: block;
            position: relative;
            z-index: 99;
        }
        .tabmenu a.ncsc-btn {
            position: absolute;
            z-index: 1;
            top: 60px;
            right: 0px;
        }
    </style>
</head>
<ul class="tab pngFix">
    <?php if(is_array($output['member_menu']) and !empty($output['member_menu'])) {
        foreach ($output['member_menu'] as $key => $val) {
//            var_dump($output['menu_key']);
//            var_dump($_GET['status']);
            if($val['menu_key'] == $output['menu_key']) {
                echo '<li class="active"><a '.(isset($val['target'])?"target=".$val['target']:"").' href="'.$val['menu_url'].'">'.$val['menu_name'].'</a></li>';
            } else {
                echo '<li class="normal"><a '.(isset($val['target'])?"target=".$val['target']:"").' href="'.$val['menu_url'].'">'.$val['menu_name'].'</a></li>';
            }
        }
    }
    ?>
</ul>
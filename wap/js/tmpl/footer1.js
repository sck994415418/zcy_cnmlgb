$(function() {
    var a = getCookie("key");
    var e = '<div class="footer_nav">' + '<div class="footer_dw">' + '<ul>';
    e += '<a href="' + WapSiteUrl + '/index.html"><li class="footer_b1"><dl><dd><img src="' + WapSiteUrl + '/img/home.png" /></dd><dd>首页</dd></dl></li></a>' + '<a href="' + WapSiteUrl + '/tmpl/product_first_categroy.html"><li class="footer_b1"><dl><dd><img src="' + WapSiteUrl + '/img/search.png" /></dd><dd>分类</dd></dl></li></a>' + '<a href="' + WapSiteUrl + '/tmpl/cart_list.html"><li class="footer_b1"><dl><dd><img src="' + WapSiteUrl + '/img/gw1.png" /></dd><dd>购物车</dd></dl></li></a>' + '<a href="' + WapSiteUrl + '/tmpl/member/signin.html"><li class="footer_b1"><dl><dd><img src="' + WapSiteUrl + '/img/jiaoliu.png" /></dd><dd>签到</dd></dl></li></a>' + '<a href="' + WapSiteUrl + '/tmpl/member/member.html"><li class="footer_b1"><dl><dd><img src="' + WapSiteUrl + '/img/self.png" /></dd><dd>我的</dd></dl></li></a>';
    e += '</ul>' + "</div>" + "</div>";
    $("#footer").html(e);
    var a = getCookie("key");
    $("#logoutbtn").click(function() {
        var a = getCookie("username");
        var e = getCookie("key");
        var i = "wap";
        $.ajax({
            type: "get",
            url: ApiUrl + "/index.php?act=logout",
            data: {
                username: a,
                key: e,
                client: i
            },
            success: function(a) {
                if (a) {
                    delCookie("username");
                    delCookie("key");
                    location.href = WapSiteUrl
                }
            }
        })
    })
});
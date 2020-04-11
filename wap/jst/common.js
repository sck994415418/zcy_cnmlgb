$(function(){
	/*控制设备的大小*/
	document.documentElement.style.fontSize = document.documentElement.clientWidth / 6.4 + 'px';
});
function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    }
    return null;
}
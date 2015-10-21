var Const = {};
Const.BANNERS_PATH = 'images/banners/';

(function start() {
    //var topBannerSpot = document.getElementById('topBannerSpot');
    var bottomBannerSpot = document.getElementById('bottomBannerSpot');

    //var topBannerRotation = new BannerRotation(topBannerSpot, BannerConfig.TOP_BANNERS);
    var bottomBannerRotation = new BannerRotation(bottomBannerSpot, BannerConfig.BOTTOM_BANNERS);

    //topBannerRotation.start();
    bottomBannerRotation.start();
})();

(function(nodeId) {
    var iframe = document.createElement('iframe');
    iframe.frameBorder = 0;
    iframe.width = 990;
    iframe.height = 270;
    iframe.src = '//topface.com/reg-banner/';
    iframe.onload = function() {
        console.log('iframe load!');
    };
    document.getElementById(nodeId).appendChild(iframe);
})('topBannerSpot');

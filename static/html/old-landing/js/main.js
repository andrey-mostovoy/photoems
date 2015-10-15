var Const = {};
Const.BANNERS_PATH = 'images/banners/';

(function start() {
    var topBannerSpot = document.getElementById('topBannerSpot');
    var bottomBannerSpot = document.getElementById('bottomBannerSpot');

    var topBannerRotation = new BannerRotation(topBannerSpot, BannerConfig.TOP_BANNERS);
    var bottomBannerRotation = new BannerRotation(bottomBannerSpot, BannerConfig.BOTTOM_BANNERS);

    topBannerRotation.start();
    bottomBannerRotation.start();
})();

/**
 * @param {Element} element
 * @param {string[]} bannerNames
 * @constructor
 */
var BannerRotation = function(element, bannerNames) {
    /** @type Element */
    this.element = element;

    /** @type string[] */
    this.bannerUrls = _.map(bannerNames, function(bannerName) {
        return Const.BANNERS_PATH + bannerName;
    });
    this.bannerUrls = _.shuffle(this.bannerUrls);

    /** @type int */
    this.currentBannerIdx = null;

    this.rotationInterval = null;
};

BannerRotation.ROTATION_INTERVAL = BannerConfig.BANNERS_ROTATION_INTERVAL;

BannerRotation.prototype.start = function() {
    this.nextBanner();

    var self = this;
    this.rotationInterval = setInterval(function() {
        self.nextBanner();
    }, BannerRotation.ROTATION_INTERVAL);
};

BannerRotation.prototype.nextBanner = function() {
    if (this.currentBannerIdx === null) {
        this.currentBannerIdx = 0;
    } else {
        this.currentBannerIdx++;
    }
    this.currentBannerIdx %= this.bannerUrls.length;

    this.element.style.backgroundImage = 'url(' + this.bannerUrls[this.currentBannerIdx] + ')';
};

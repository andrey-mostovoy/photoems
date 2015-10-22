/** @namespace App.Sbn */
Core.createNamespace('App.Sbn');

jQuery(document).ready(function() {
    var $body = jQuery('body'),
        Sbn = new App.Sbn.Sbn();
});

/**
 * Конструктор.
 * @constructor
 */
App.Sbn.Sbn = function() {
    this.IMAGE_PATH = '/static/img/sbn/';
    this.FLASH_PATH = '/static/swf/sbn/';
    this.collection = App.Sbn.BannerConfig.collection;
    this.places = App.Sbn.BannerConfig.places;

    for (var i in this.places) {
        if (!this.places.hasOwnProperty(i)) {
            continue;
        }

        var size = this.places[i],
            chosen = this.choose(this.collection[size]);

        this.collection[size].splice(chosen.index, 1);
        var $sbn = this.makeSbnHtmlItem(chosen.item);

        jQuery('.js-sbn-' + i).html($sbn);
    }
};

App.Sbn.BannerTypes = {
    IMG: 'img',
    FLASH: 'swf',
    IFRAME: 'iframe'
};

App.Sbn.BannerConfig = {
    collection: { },
    places: { }

    /*
    Пример конфига

    collection: {
        s990_270: [
            { type: this.TYPE_IFRAME, url: '//topface.com/reg-banner/' }
        ],
        s728_90: [
            {type: this.TYPE_IMG, file: '2-1.jpg', click: '//topface.com'},
            {type: this.TYPE_IMG, file: '2-2.jpg', click: '//topface.com'},
            {type: this.TYPE_IMG, file: '2-3.jpg', click: '//topface.com'},
            {type: this.TYPE_IMG, file: '3-1.png', click: '//topface.com/landingtf/'},
            {type: this.TYPE_IMG, file: '3-2.png', click: '//topface.com/landingtf/'},
            {type: this.TYPE_IMG, file: '3-3.png', click: '//topface.com/landingtf/'},
            {type: this.TYPE_IMG, file: '3-4.png', click: '//topface.com/landingtf/'},
            {type: this.TYPE_IMG, file: '4-1.jpg', click: '//topface.com'},
            {type: this.TYPE_IMG, file: '4-2.gif', click: '//topface.com'}
        ],
        s240_400: [
            {type: this.TYPE_IMG, file: '1-1.png', click: '//topface.com/landingtf/'},
            {type: this.TYPE_IMG, file: '1-2.png', click: '//topface.com/landingtf/'},
            {type: this.TYPE_IMG, file: '1-3.png', click: '//topface.com/landingtf/'},
            {type: this.TYPE_IMG, file: '4-3.jpg', click: '//topface.com'}
        ]
    };
    places: {
        top: 's990_270',
        //bottom: 's728_90',
        left: 's240_400',
        right: 's240_400'
    };
    */
};

/**
 * Устанавливаем конфиг баннеров
 * @param config
 * @see App.Sbn.BannerConfig
 */
App.Sbn.setBannerConfig = function(config) {
    App.Sbn.BannerConfig = config;
};

/**
 * Выбирает из коллекций рандомный элемент.
 * @param {Array} collection
 * @returns {*}
 */
App.Sbn.Sbn.prototype.choose = function(collection) {
    var min = 0,
        max = collection.length - 1,
        rand = min + Math.floor(Math.random() * (max + 1 - min));

    if (collection.length == 1) {
        rand = 0;
    }

    return {index: rand, item: collection[rand]};
};

/**
 * Создает и возвращает элемент дома для вставки в плейс.
 * @param {Object} SbnItem
 * @returns {*}
 */
App.Sbn.Sbn.prototype.makeSbnHtmlItem = function(SbnItem) {
    if (SbnItem.type == App.Sbn.BannerTypes.IMG) {
        var $img = jQuery('<img/>').attr('src', this.IMAGE_PATH + SbnItem.file),
            $a = jQuery('<a/>').attr('href', SbnItem.click).attr('target', '_blank');

        $a.append($img);

        return $a;
    }

    if (SbnItem.type == App.Sbn.BannerTypes.FLASH) {

    }

    if (SbnItem.type == App.Sbn.BannerTypes.IFRAME) {
        var iframe = document.createElement('iframe');
        iframe.frameBorder = 0;
        iframe.width = 990;
        iframe.height = 270;
        iframe.src = SbnItem.url;
        return iframe;
    }
};

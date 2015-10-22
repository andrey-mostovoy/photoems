(function() {
    var collection = {
        s990_270: [
            { type: App.Sbn.BannerTypes.IFRAME, url: '//topface.com/reg-banner/' }
        ],
        s728_90: [
            {type: App.Sbn.BannerTypes.IMG, file: '2-1.jpg', click: '//topface.com'},
            {type: App.Sbn.BannerTypes.IMG, file: '2-2.jpg', click: '//topface.com'},
            {type: App.Sbn.BannerTypes.IMG, file: '2-3.jpg', click: '//topface.com'},
            {type: App.Sbn.BannerTypes.IMG, file: '3-1.png', click: '//topface.com/landingtf/'},
            {type: App.Sbn.BannerTypes.IMG, file: '3-2.png', click: '//topface.com/landingtf/'},
            {type: App.Sbn.BannerTypes.IMG, file: '3-3.png', click: '//topface.com/landingtf/'},
            {type: App.Sbn.BannerTypes.IMG, file: '3-4.png', click: '//topface.com/landingtf/'},
            {type: App.Sbn.BannerTypes.IMG, file: '4-1.jpg', click: '//topface.com'},
            {type: App.Sbn.BannerTypes.IMG, file: '4-2.gif', click: '//topface.com'}
        ],
        s240_400: [
            {type: App.Sbn.BannerTypes.IMG, file: '1-1.png', click: '//topface.com/landingtf/'},
            {type: App.Sbn.BannerTypes.IMG, file: '1-2.png', click: '//topface.com/landingtf/'},
            {type: App.Sbn.BannerTypes.IMG, file: '1-3.png', click: '//topface.com/landingtf/'},
            {type: App.Sbn.BannerTypes.IMG, file: '4-3.jpg', click: '//topface.com'}
        ]
    };
    var places = {
        //top: 's990_270'
        //bottom: 's728_90',
    };
    if (!Core.Data.get('image')) {
        places.left = 's240_400';
        places.right = 's240_400';
    }

    var oldNewLandingBannerConfig = {
        collection: collection,
        places: places
    };

    App.Sbn.setBannerConfig(oldNewLandingBannerConfig);
})();

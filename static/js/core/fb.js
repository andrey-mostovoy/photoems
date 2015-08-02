/** @param {object} Фб окружение **/
Core.Fb = {
    /**
     * Колбеки по событию лайка.
     */
    likeCallbacks: [],

    /**
     * Вставляет на страницу fb api скрипт
     */
    init: function() {
        this._listenLikeEvent();

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/ru_RU/all.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    },

    /**
     * Слушает события по действиям с фб.
     * @private
     */
    _listenLikeEvent: function() {
        var t = this;
        window.fbAsyncInit = function() {
            window.FB.Event.subscribe('edge.create', function (response) {
                for (var i in t.likeCallbacks) {
                    if (t.likeCallbacks.hasOwnProperty(i)) {
                        t.likeCallbacks[i](response);
                    }
                }
            });
        };
    },

    /**
     * Добавляет колбек для события лайка.
     * @param {function} callback
     */
    addLikeCallback: function(callback) {
        this.likeCallbacks.push(callback);
    }
};

/**
 * @namespace Core неймспейс ядра приложения.
 */
Core = {
    /**
     * Создает указанный неймспейс, если его еще нет.
     * @param {string} namespace
     */
    createNamespace: function(namespace) {
        var namespaceParts = namespace.split('.'),
            parent = window;

        for (var i = 0; i < namespaceParts.length; i++) {
            var namespacePartName = namespaceParts[i];
            // check if the current parent already has the namespace declared
            // if it isn't, then create it
            if (typeof parent[namespacePartName] === 'undefined') {
                parent[namespacePartName] = {};
            }
            // get a reference to the deepest element in the hierarchy so far
            parent = parent[namespacePartName];
        }
    },

    /**
     * @param {object} данные от сервера
     */
    Data: {
        /**
         * @param {object} распарсенные данные от сервера
         */
        _data: {},

        /**
         * Возвращает значение из указанного ключа или дефолтное
         * @param {string} key
         * @param {string|int|object|Array} [def]
         * @returns {*}
         */
        get: function(key, def) {
            if (!this._data.hasOwnProperty(key)) {
                return def ? def : null;
            }
            return this._data[key];
        }
    },

    /**
     * @param {object} Сервисер для аякс запросов.
     */
    Service: {
        /**
         * Делает запрос на сервер
         * @param {string} serviceName имя сервисера полностью с неймспейсом через .
         * @param {string} commandName имя команды (метода сервисера)
         * @param {object} [data] объект с данными
         * @param {function} [successCallback] функция выполняемая по успешному завершению запроса
         */
        call: function(serviceName, commandName, data, successCallback) {
            data['_service'] = serviceName;
            data['_command'] = commandName;
            jQuery.ajax({
                url: '/service/',
                type: 'post',
                data: data,
                success: successCallback
            });
        }
    }
};

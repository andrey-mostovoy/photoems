/** @namespace App.MainLandingPage */
Core.createNamespace('App.MainLandingPage');

jQuery(document).ready(function () {
    var $body = jQuery('body'),
        Page = new App.MainLandingPage.MainLandingPage();

    if (Core.Data.get('image', false)) {
        $body.delegate('.js-save-button', 'click', jQuery.proxy(Page.onSave, Page));
        $body.delegate('select[name=effect]', 'change', jQuery.proxy(Page.onEffectSelectChange, Page));
        $body.delegate('.js-set-image-effect', 'click', jQuery.proxy(Page.onSetImageEffect, Page));
        $body.delegate('.js-change-image', 'click', jQuery.proxy(Page.onChangeImage, Page));
    } else {
        bindUploadWidget();
    }
});

/**
 * Конструктор страницы.
 * @constructor
 */
App.MainLandingPage.MainLandingPage = function () {
    this.effect = '';
    this.additional = '';
};

/**
 * Обработчик события сохранения результата.
 * @param {Object} event
 */
App.MainLandingPage.MainLandingPage.prototype.onSave = function (event) {
    if (!this.setParams()) {
        return;
    }

    var urlParts = [
        'download=' + Core.Data.get('image'),
        'effect=' + this.effect,
        'additional=' + this.additional
    ];

    window.location.href = window.location.origin + '?' + urlParts.join('&');
};

/**
 * Обработчик события применения эффекта на изображение.
 * @param {Object} event
 */
App.MainLandingPage.MainLandingPage.prototype.onSetImageEffect = function (event) {
    if (!this.setParams()) {
        return;
    }

    // ставим лоадер
    var $imageWrapper = jQuery('.imageWrapper'),
        $loader = jQuery('<div/>').addClass('loader').css({height: $imageWrapper.height()});
    $imageWrapper.append($loader);

    jQuery.ajax({
        type: 'post',
        data: {
            apply: true,
            effect: this.effect,
            additional: this.additional,
            img: Core.Data.get('image')
        },
        success: function (response) {
            if (response.error) {
                alert(response.error);
            } else {
                jQuery('img', $imageWrapper).attr('src', response.url);
            }
            jQuery('.loader').remove();
        }
    });
};

/**
 * Устанавливает параметры для фильтра изображения.
 * @returns {boolean}
 */
App.MainLandingPage.MainLandingPage.prototype.setParams = function () {
    var $form = jQuery(event.target).parents('.control').eq(0),
        $effect = $form.find('.js-set-image-effect:checked'),
        effect = $effect.val(),
        $color = $form.find('input[name=colorize]'),
        $gamma = $form.find('select[name=gamma]'),
        $blur = $form.find('select[name=blur]');

    if (!effect) {
        alert('effect has not chosen');
        return false;
    }

    this.effect = effect;

    switch (effect) {
        case 'colorize':
            var color = $color.val();
            if (!color) {
                alert('Set image color first');
                return false;
            }
            // хеш цвета
            this.additional = color;
            break;
        case 'blur':
            var blur = parseInt($blur.val());
            if (!blur) {
                alert('Set image blur coefficient first');
                return false;
            }
            if (blur < 1) {
                alert('Set image blur coefficient more than 0');
                return false;
            }
            // целое число
            this.additional = blur;
            break;
        case 'gamma':
            var gamma = parseFloat($gamma.val());
            if (!gamma) {
                alert('Set image gamma coefficient first');
                return false;
            }
            if (gamma === Number(gamma) && gamma % 1 !== 0) {
                // число с точкой
                this.additional = gamma;
            } else {
                alert('Image gamma correction must be float');
                return false;
            }
            break;
    }
    return true;
};

/**
 * Обработчик события замены изображения на другое.
 * @param {Object} event
 */
App.MainLandingPage.MainLandingPage.prototype.onChangeImage = function (event) {
    var char = '?';
    if (window.location.search.length != 0) {
        char = '&';
    }
    window.location.href += char + 'change=1';
};

/**
 * Обработчик события выбора эффекта наложения на изображение.
 * @param {Object} event
 */
App.MainLandingPage.MainLandingPage.prototype.onEffectSelectChange = function (event) {
    var $effect = jQuery(event.target),
        effect = $effect.val();

    jQuery('.additionalWrapper').hide();

    switch (effect) {
        case 'colorize':
        case 'blur':
        case 'gamma':
            jQuery('.' + effect + 'Wrapper').show();
            break;
    }
};

/**
 * Биндит виджет загрузки изображений.
 */
function bindUploadWidget() {
    var ul = jQuery('#upload ul');

    jQuery('#drop a').click(function () {
        // Simulate a click on the file input button
        // to show the file browser dialog
        jQuery(this).parent().find('input').click();
    });

    // Initialize the jQuery File Upload plugin
    jQuery('#upload').fileupload({

        // This element will accept file drag/drop uploading
        dropZone: jQuery('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {

            var tpl = jQuery('<li class="working"><input type="text" value="0" data-width="48" data-height="48"' +
                ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

            // Append the file name and file size
            tpl.find('p').text(data.files[0].name)
                .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);

            // Initialize the knob plugin
            tpl.find('input').knob();

            // Listen for clicks on the cancel icon
            tpl.find('span').click(function () {

                if (tpl.hasClass('working')) {
                    jqXHR.abort();
                }

                tpl.fadeOut(function () {
                    tpl.remove();
                });

            });

            // Automatically upload the file once it is added to the queue
            var jqXHR = data.submit();
        },

        progress: function (e, data) {

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            if (progress == 100) {
                data.context.removeClass('working');
            }
        },

        done: function (e, data) {
            var response = data.result;
            if (response.status == 'error') {
                alert(response.message);
            } else {
                window.location.href = window.location.origin + '?id=' + response.id;
            }
        },

        fail: function (e, data) {
            // Something has gone wrong!
            data.context.addClass('error');
        }

    });


    // Prevent the default action when a file is dropped on the window
    jQuery(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    // Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }
}

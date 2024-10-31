(function ($) {
    $.fn.PayamitoSpinner = function (method) {
        return this.each(function () {

            var elem = $(this),
                elemClass = 'edd_payamito',
                edd_payamito_text,
                effectObj,
                effectElemCount,
                createSubElem = false,
                specificAttr = 'background-color',
                addStyle = '',
                effectElemHTML = '',
                edd_payamitoObj,
                _options,
                currentID;
            var methods = {
                init: function () {
                    var _defaults = {
                        effect: 'win8',
                        text: '',
                        bg: 'rgba(255,255,255,0.7)',
                        color: "red",
                        maxSize: '500',
                        waitTime: -1,
                        textPos: 'vertical',
                        fontSize: '',
                        source: '',
                        zindex: 99999999,
                        onClose: function () {
                        }
                    };
                    _options = $.extend(_defaults, method);

                    currentID = new Date().getMilliseconds();
                    edd_payamitoObj = $('<div class="' + elemClass + '" data-edd_payamito_id="' + currentID + '"></div>');

                    switch (_options.effect) {

                        case 'win8':
                            effectElemCount = 5;
                            createSubElem = true;

                    }

                    if (addStyle !== '') {
                        addStyle += ';';
                    }

                    if (effectElemCount > 0) {
                        if (_options.effect === 'img') {
                            effectElemHTML = '<img src="' + _options.source + '">';
                        } else {
                            for (var i = 1; i <= effectElemCount; ++i) {
                                if ($.isArray(_options.color)) {
                                    var color = _options.color[i];
                                    if (color == undefined) {
                                        color = '#000';
                                    }
                                } else {
                                    var color = _options.color;
                                }
                                if (createSubElem) {
                                    effectElemHTML += '<div class="' + elemClass + '_progress_elem' + i + '"><div style="' + specificAttr + ':' + color + '"></div></div>';
                                } else {
                                    effectElemHTML += '<div class="' + elemClass + '_progress_elem' + i + '" style="' + specificAttr + ':' + color + '"></div>';
                                }
                            }
                        }
                        effectObj = $('<div class="' + elemClass + '_progress ' + _options.effect + '" style="' + addStyle + '">' + effectElemHTML + '</div>');
                    }

                    if (_options.text) {
                        if ($.isArray(_options.color)) {
                            var color = _options.color[0];
                        } else {
                            var color = _options.color;
                        }
                        if (_options.fontSize != '') {
                            var size = 'font-size:' + _options.fontSize;
                        } else {
                            var size = '';
                        }
                        edd_payamito_text = $('<div class="' + elemClass + '_text" style="color:' + color + ';' + size + '">' + _options.text + '</div>');
                    }
                    var elemObj = elem.find('> .' + elemClass);

                    if (elemObj) {
                        elemObj.remove();
                    }
                    var edd_payamitoDivObj = $('<div class="' + elemClass + '_content ' + _options.textPos + '"></div>');
                    edd_payamitoDivObj.append(effectObj, edd_payamito_text);
                    edd_payamitoObj.append(edd_payamitoDivObj);
                    if (elem[0].tagName == 'HTML') {
                        elem = $('body');
                    }
                    elem.addClass(elemClass + '_container').attr('data-edd_payamito_id', currentID).append(edd_payamitoObj);
                    elemObj = elem.find('> .' + elemClass);
                    var elemContentObj = elem.find('.' + elemClass + '_content');
                    elemObj.css({background: _options.bg});

                    if (_options.maxSize !== '' && _options.effect != 'none') {
                        var elemH = effectObj.outerHeight();
                        var elemW = effectObj.outerWidth();
                        var elemMax = elemH;
                        if (_options.effect === 'img') {
                            effectObj.css({height: _options.maxSize + 'px'});
                            effectObj.find('>img').css({maxHeight: '100%'});
                            elemContentObj.css({marginTop: -elemContentObj.outerHeight() / 2 + 'px'});
                        } else {
                            if (_options.maxSize < elemMax) {
                                if (_options.effect == 'stretch') {
                                    effectObj.css({height: _options.maxSize + 'px', width: _options.maxSize + 'px'});
                                    effectObj.find('> div').css({margin: '0 5%'});
                                } else {
                                    var zoom = _options.maxSize / elemMax - 0.2;
                                    var offset = '-50%';
                                    effectObj.css({
                                        transform: 'scale(' + zoom + ') translateX(' + offset + ')',
                                        whiteSpace: 'nowrap'
                                    });
                                }

                            }
                        }
                    }
                    elemContentObj.css({marginTop: -elemContentObj.outerHeight() / 2 + 'px'});

                    function setElTop(getTop) {
                        elemContentObj.css({top: 'auto', transform: 'translateY(' + getTop + 'px) translateZ(0)'});
                    }

                    if (elem.outerHeight() > $(window).height()) {
                        var sTop = $(window).scrollTop(),
                            elH = elemContentObj.outerHeight(),
                            elTop = elem.offset().top,
                            cH = elem.outerHeight(),
                            getTop = sTop - elTop + $(window).height() / 2;
                        if (getTop < 0) {
                            getTop = Math.abs(getTop);
                        }
                        if (getTop - elH >= 0 && getTop + elH <= cH) {
                            if (elTop - sTop > $(window).height() / 2) {
                                getTop = elH;
                            }
                            setElTop(getTop);
                        } else {
                            if (sTop > elTop + cH - elH) {
                                getTop = sTop - elTop - elH;
                            } else {
                                getTop = sTop - elTop + elH;
                            }
                            setElTop(getTop);
                        }
                        $(document).scroll(function () {
                            var sTop = $(window).scrollTop(),
                                getTop = sTop - elTop + $(window).height() / 2;
                            if (getTop - elH >= 0 && getTop + elH <= cH) {
                                setElTop(getTop);
                            }
                        });
                    }

                    if (_options.waitTime > 0) {
                        setTimeout(function () {
                            edd_payamitoClose();
                        }, _options.waitTime);
                    }

                    elemObj.on('destroyed', function () {
                        if (_options.onClose && $.isFunction(_options.onClose)) {
                            _options.onClose(elem);
                        }
                        elemObj.trigger('close', {el: elem});
                    });

                    $.event.special.destroyed = {
                        remove: function (o) {
                            if (o.handler) {
                                o.handler();
                            }
                        }
                    };

                    return elemObj;
                },
                hide: function () {
                    PayamitoSpinnerClose();
                }
            };

            function PayamitoSpinnerClose() {
                var currentID = elem.attr('data-edd_payamito_id');
                elem.removeClass(elemClass + '_container').removeAttr('data-edd_payamito_id');
                elem.find('.' + elemClass + '[data-edd_payamito_id="' + currentID + '"]').remove();
            }

            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, arguments);
            }

        });

    };

    $(window).on('load', function () {

        $('body.edd_payamito_body').addClass('hideMe');
        setTimeout(function () {
            $('body.edd_payamito_body').find('.edd_payamito_container:not([data-edd_payamito_id])').remove();
            $('body.edd_payamito_body').removeClass('edd_payamito_body hideMe');
        }, 200);
    });
})(jQuery);
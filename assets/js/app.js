jQuery(document).ready(function ($) {

    constMODE = "edd";

    const PayamitoOTPTIME = parseInt($("#edd_payamito_otp_time").val());
    const PayamitoOSENDOTP = $("#payamito_send_otp")[0];
    const PayamitoNOUNCE = $("#edd_payamito_nonce").val();
    const PayamitoOTP = $("#payamito_otp_field");
    if (PayamitoOTPTIME !== undefined && PayamitoOSENDOTP !== undefined && PayamitoNOUNCE !== undefined && PayamitoOTP !== undefined) {
        var FIELD;

        FIELD = $("#payamito_mobile_field");

        if (FIELD !== undefined) {
            $(PayamitoOSENDOTP).click(function () {
                if (validate_field(FIELD)) {
                    let data = {"mobile": FIELD.val()};
                    Payamito_Spinner(type = "start");
                    $.ajax({

                        url: PayamitoGeneral.ajaxurl,
                        type: 'POST',
                        data: {
                            'action': "Payamito_request",
                            'nonce': PayamitoGeneral.nonce,
                            "mobile": FIELD.val(),
                        }
                    }).done(function (r, s) {
                        if (s == 'success' && r != '0' && r != "" && typeof r === 'object') {
                            Payamito_notification(r.e, r.message)
                            if (r.e == 1) {
                                Payamito_timer();
                            }
                        }
                    }).fail(function () {

                    })
                        .always(function (r, s) {
                            Payamito_Spinner(type = "close");
                        });
                }

            });

            function validate_field(field) {

                $([document.documentElement, document.body]).animate({
                    scrollTop: field.offset().top - 35
                }, 1000);
                field.addClass("Payamito-error-field")
                if (field.val() === null || !field.val().trim().length) {
                    Payamito_notification(0, PayamitoGeneral.invalid)
                    return false;
                }
                return true;
            }

            function Payamito_notification(ty = -1, m) {
                switch (ty) {
                    case ty = -1:
                        iziToast.error({
                            timeout: 10000,
                            title: PayamitoGeneral.error,
                            message: m,
                            displayMode: 2
                        });
                        break;
                    case ty = 0:
                        iziToast.warning({
                            timeout: 10000,
                            title: PayamitoGeneral.warning,
                            message: m,
                            displayMode: 2
                        });
                        break;
                    case ty = 1:
                        iziToast.success({
                            timeout: 10000,
                            title: PayamitoGeneral.success,
                            message: m,
                            displayMode: 2
                        });
                }
            }

            function Payamito_Spinner(type = "start") {
                let spinner = $("body");
                if (type == "start") {
                    spinner.PayamitoSpinner();
                } else {
                    spinner.PayamitoSpinner('hide');
                }
            }

            function Payamito_timer() {
                var timer = PayamitoOTPTIME;
                var innerhtml = PayamitoOSENDOTP.value;
                $("#payamito_send_otp").prop('disabled', true);
                $("#payamito_send_otp").css('cursor', 'wait');
                var Interval = setInterval(function () {
                    seconds = parseInt(timer);
                    seconds = seconds < 10 ? "0" + seconds : seconds;
                    PayamitoOSENDOTP.value = seconds + ":" +
                        PayamitoGeneral.second;
                    if (--timer <= 0) {
                        timer = 0;
                        $("#payamito_send_otp").removeAttr('disabled');
                        $("#payamito_send_otp").css('cursor', 'grab');
                        PayamitoOSENDOTP.value = innerhtml;
                        clearInterval(Interval);
                    }
                }, 1000);
            }
        }
    }
});
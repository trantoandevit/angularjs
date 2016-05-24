(function () {
    var msgs = {
        'email': 'Hãy nhập E-mail hợp lệ',
        'url': 'Hãy nhập URL hợp lệ',
        'tooLong': 'Hãy nhập từ {1} ký tự trở xuống',
        'tooShort': 'Hãy nhập từ {1} ký tự trở lên',
        'rangeUnderflow': 'Hãy nhập số từ {1} trở lên',
        'rangeOverflow': 'Hãy nhập số từ {1} trở xuống',
        'valueMissing': 'Hãy nhập trường này'
    };

    $.validate = {
        'setLanguage': function (lang) {
            msgs = lang;
        }
    };

    $.fn.validate = function () {
        var form = this;

        $('input,select,textarea', form).each(function () {
            var control = this;
            control.oninvalid = function () {
                var validity = control.validity;
                control.setCustomValidity("");

                if (control.valid)
                    return true;

                if (validity.typeMismatch) {
                    switch (control.type.toLowerCase()) {
                        case 'email':
                            control.setCustomValidity(msgs.email);
                            break;
                        case 'url':
                            control.setCustomValidity(msgs.url);
                            break;
                    }
                } else if (validity.tooLong) {
                    control.setCustomValidity(msgs.tooLong.replace('{1}', control.maxLength));
                } else if (validity.tooShort) {
                    control.setCustomValidity(msgs.tooShort.replace('{1}', control.minLength));
                } else if (validity.rangeUnderflow) {
                    control.setCustomValidity(msgs.rangeUnderflow.replace('{1}', control.min));
                } else if (validity.rangeOverflow) {
                    control.setCustomValidity(msgs.rangeOverflow.replace('{1}', control.max));
                } else if (validity.valueMissing) {
                    control.setCustomValidity(msgs.valueMissing);
                }
            };
        });
    };
})();

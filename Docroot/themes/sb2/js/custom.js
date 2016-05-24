$(function () {
    $('[data-toggle="tooltip"]').tooltip({
        'container': 'body'
    });
});

function setCookie(cname, cvalue, exdays, path) {
    exdays = exdays || 365 * 20;
    path = path || window.location.pathname;
    //xu ly Angular $$hashKey 
    function removeHash(obj) {
        delete obj['$$hashKey'];
        for (var i in obj)
            if (typeof obj[i] == 'object')
                removeHash(obj[i]);
    }
    if (typeof cvalue == "object")
        removeHash(cvalue);

    cvalue = JSON.stringify(cvalue);
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=" + path;
}

function getCookie(cname, defVal) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return $.parseJSON(c.substring(name.length, c.length));
    }
    return defVal;
}

function compareObj(a, b) {
    a = $.extend({}, a);
    b = $.extend({}, b);
    a['$$hashKey'] = 1;
    b['$$hashKey'] = 1;
    return JSON.stringify(a) === JSON.stringify(b);
}

$(function () {
    //check/uncheck all
    $('.table thead').on('change', '[type=checkbox]', function () {
        var checked = $(this).prop('checked');
        $(this).parents('table:first').find('tbody [type=checkbox]').each(function () {
            if (this.disabled == false && this.readOnly == false)
                $(this).prop('checked', checked).trigger('change').triggerHandler('click');
        });
    });
    //check dòng thường trong table
    $('.table-record tbody').on('change', '[type=checkbox]', function () {
        var checked = $(this).prop('checked');
        $(this).parents('tr:first').toggleClass('warning', checked);
    });
});

var sb2 = angular.module('sb2', []);
sb2.factory('$apply', ['$rootScope', function ($rootScope) {
        return function (fn) {
            setTimeout(function () {
                $rootScope.$apply(fn);
            });
        };
    }]);

sb2.directive('ngDom', function ($apply) {
    return {
        scope: {'ngDom': '='},
        link: function (scope, elem) {
            $apply(function () {
                scope.ngDom = elem[0];
            });
        }
    };
});

$('.form-validate').each(function () {
    $(this).validate();
});
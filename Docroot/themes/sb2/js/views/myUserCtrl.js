sb2.controller('userCtrl', function ($scope, $apply, $timeout, $http) {
    $scope.showFilter = getCookie('showFilter', false);
    $scope.depPk;
    $scope.department = null;
    $scope.ajax = {};
    $scope.editingDep;
    $scope.editingUser;
    $scope.modalDep;
    $scope.checkedUsers = {};
    $scope.checkedDeps = {};
    $scope.departmentPicker;
    $scope.groups;
    $scope.permissions;
    $scope.ajax = {};
    $scope.searchResult;

    $(window).on('hashchange', function () {
        $apply(function () {
            $scope.filter = {'search': ''};
            $scope.depPk = window.location.hash.replace('#', '').replace('/', '') || 0;
            $scope.getDep($scope.depPk);
        });
    }).trigger('hashchange');

    $scope.getDep = function (depPk) {
        if ($scope.ajax.load)
            $scope.ajax.load.abort();

        $scope.ajax.load = $.ajax({
            'url': CONFIG.siteUrl + '/rest/department/' + depPk,
            'cache': false,
            'data': {'users': 1, 'departments': 1, 'ancestors': 1},
            'dataType': 'json'
        }).done(function (res) {
            $apply(function () {
                $scope.department = res;
            });
        }).always(function () {
            $scope.ajax.load = false;
        });
    };

    $scope.deps = function () {
        var dep = $scope.department || {};
        return $scope.searchResult ? $scope.searchResult.departments : dep.deps;
    };

    $scope.users = function () {
        var dep = $scope.department || {};
        return $scope.searchResult ? $scope.searchResult.users : dep.users;
    };

    $scope.toggleFilter = function () {
        $scope.showFilter = !$scope.showFilter;
    };

    $scope.$watch('showFilter', function (newVal) {
        if (typeof newVal === 'undefined')
            return;

        setCookie('showFilter', newVal);
    });

    $scope.$watchCollection('filter', function (newVal) {
        if ($scope.ajax.search)
            $scope.ajax.search.abort();
        if (!newVal)
            return;
        if (newVal.search) {
            $scope.ajax.search = $.ajax({
                'url': CONFIG.siteUrl + '/rest/user/search',
                'data': $scope.filter
            }).done(function (resp) {
                $apply(function () {
                    $scope.searchResult = resp;
                });
            });
        } else {
            $scope.searchResult = null;
        }
    });

    $scope.$watch('depPk', function (newVal) {
        if (typeof newVal === 'undefined')
            return;
    });

    $scope.goUp = function () {
        if (!$scope.department.ancestors.length) {
            window.location.hash = '#/';
            return;
        }
        var parentDep = $scope.department.ancestors[$scope.department.ancestors.length - 1];
        window.location.hash = '#/' + parentDep.pk;
    };


    $scope.editDep = function (dep, insertAndOpen) {
        dep = dep || {'stt': true};
        dep.insertAndOpen = insertAndOpen || false;
        $scope.editingDep = $.extend({}, dep);
        $scope.editingDep.parentDep = $scope.department;
        $($scope.modalDep).modal('show');
    };

    $scope.anythingChecked = function () {
        for (var i in $scope.checkedDeps)
            if ($scope.checkedDeps[i])
                return true;
        for (var i in $scope.checkedUsers)
            if ($scope.checkedUsers[i])
                return true;
        return false;
    };

    $scope.pickEditDep = function () {
        $('[ng-department-picker]')[0].openModal({
            'selected': $scope.editingDep.parentDep ? $scope.editingDep.parentDep.pk : null,
            'not': $scope.editingDep.pk,
            'submit': function (dep) {
                $apply(function () {
                    $scope.editingDep.parentDep = dep;
                });
            }
        });
    };

    $scope.clearParentDep = function () {
        $scope.editingDep.parentDep = null;
        $scope.editingDep.depFk = null;
    };

    $scope.$watchCollection('editingDep', function (newVal) {
        if (!newVal)
            return;
        if (newVal.parentDep)
            newVal.depFk = newVal.parentDep.pk;
        newVal.pk = newVal.pk || 0;
    });

    $scope.submitDep = function ($event) {
        if (!$event.target.checkValidity()) {
            return;
        }
        var url = CONFIG.siteUrl + '/rest/department/' + $scope.editingDep.pk;
        $scope.ajax.submit = true;
        $http.put(url, $scope.editingDep).then(function () {
            $scope.getDep($scope.depPk);
            $($scope.modalDep).modal('hide');
            $scope.ajax.submit = false;

            if ($scope.editingDep.insertAndOpen)
                $timeout(function () {
                    $scope.editDep(null, true);
                }, 500);
        });
    };

    $scope.editUser = function (user, insertAndOpen) {
        user = user ? $.extend({}, user) : {
            'stt': true,
            'changePass': true,
            'groups': [],
            'permissions': []
        };
        user.insertAndOpen = insertAndOpen || false;
        user.parentDep = $scope.department;
        $scope.editingUser = user;
        $($scope.modalUser).modal('show');

        $http.get(CONFIG.siteUrl + '/rest/group?status=1').then(function (resp) {
            $scope.groups = resp.data;
        });
        $http.get(CONFIG.siteUrl + '/rest/basePermission').then(function (resp) {
            $scope.permissions = resp.data;
        });
    };

    $scope.togglePassword = function () {
        if ($scope.editingUser.changePass) {
            $scope.editingUser.changePass = false;
            $scope.editingUser.newPass = null;
            $scope.editingUser.rePass = null;
        } else {
            $scope.editingUser.changePass = true;
        }
        $timeout(function () {
            $(window).trigger('resize');
        });
    };

    $scope.pickUserDep = function () {
        $('[ng-department-picker]')[0].openModal({
            'selected': $scope.editingUser.parentDep ? $scope.editingUser.parentDep.pk : null,
            'submit': function (dep) {
                $apply(function () {
                    $scope.editingUser.parentDep = dep;
                });
            }
        });
    };
    $scope.$watchCollection('editingUser', function (newVal) {
        if (!newVal)
            return;
        newVal.depFk = newVal.parentDep ? newVal.parentDep.pk : 0;
        newVal.pk = newVal.pk || 0;
    });

    $scope.clearUserDep = function () {
        $scope.editingUser.parentDep = null;
    };

    $scope.submitUser = function ($event) {
        if (!$event.target.checkValidity()) {
            return;
        }
        if ($scope.editingUser.errcheckUniqueAccount) {
            $scope.userAccDom.focus();
            return;
        }

        var user = $scope.editingUser;
        user.passError = null;
        //check pass
        if (user.changePass && user.newPass && user.newPass.length < 6) {
            user.passError = 'Hãy nhập mật khẩu tối thiểu 6 ký tự.';
            $scope.newPassDom.focus();
            return;
        }
        if (user.changePass && user.newPass != user.rePass) {
            user.passError = 'Mật khẩu nhập lại không khớp.';
            $scope.rePassDom.focus();
            return;
        }

        var url = CONFIG.siteUrl + '/rest/user/' + (user.pk ? user.pk : 0);
        $scope.ajax.submit = true;
        $http.put(url, user).then(function (resp) {
            $($scope.modalUser).modal('hide');
            $scope.getDep($scope.depPk);
            $scope.ajax.submit = false;

            if ($scope.editingUser.insertAndOpen) {
                $timeout(function () {
                    $scope.editUser(null, true);
                }, 500);
            }
            $scope.editingUser = null;
        });
    };

    $scope.toggleGroup = function ($event) {
        var target = $event.target;
        if (target.checked && $scope.editingUser.groups.indexOf(target.value) == -1)
            $scope.editingUser.groups.push(target.value);
        else if (!target.checked) {
            var idx = $scope.editingUser.groups.indexOf(target.value);
            if (idx != -1)
                $scope.editingUser.groups.splice(idx, 1);
        }
    };

    $scope.togglePermission = function ($event) {
        var target = $event.target;
        if (target.checked && $scope.editingUser.permissions.indexOf(target.value) == -1)
            $scope.editingUser.permissions.push(target.value);
        else if (!target.checked) {
            var idx = $scope.editingUser.permissions.indexOf(target.value);
            if (idx != -1)
                $scope.editingUser.permissions.splice(idx, 1);
        }
    };

    $scope.$watch('editingUser.account', function (newVal, oldVal) {
        if (!newVal || !oldVal)
            return;

        var url = CONFIG.siteUrl + '/rest/user/checkUniqueAccount';
        var data = {
            'pk': $scope.editingUser.pk,
            'account': $scope.editingUser.account
        };

        $scope.ajax.checkUniqueAccount = true;
        $scope.editingUser.errcheckUniqueAccount = false;

        $timeout(function () {
            $http.post(url, data).then(function (res) {
                $scope.ajax.checkUniqueAccount = false;
                $scope.editingUser.errcheckUniqueAccount = !res.data;
            });
        }, 500);

    });

    $scope.getCheckedUsers = function () {
        var users = [];
        for (var id in $scope.checkedUsers)
            if ($scope.checkedUsers[id])
                users.push(id);

        return users;
    };

    $scope.getCheckedDeps = function () {
        var deps = [];
        for (var id in $scope.checkedDeps)
            if ($scope.checkedDeps[id])
                deps.push(id);

        return deps;
    };

    $scope.delete = function () {
        var msg = 'Những phòng ban, tài khoản trong đối tượng bị xóa sẽ chuyển về thư mục gốc, thực hiện?';

        if (confirm(msg)) {
            $http.delete(CONFIG.siteUrl + '/rest/user', {'data': $scope.getCheckedUsers()}).then(function () {
                return $http.delete(CONFIG.siteUrl + '/rest/department', {'data': $scope.getCheckedDeps()});
            }).then(function () {
                $scope.getDep($scope.depPk);
            });
        }
    };

    $scope.move = function () {
        $('[ng-department-picker]')[0].openModal({
            'not': $scope.getCheckedDeps(),
            'selected': $scope.depPk,
            'submit': function (dep) {
                if (dep.pk == $scope.depPk)
                    return;
                $http.put(CONFIG.siteUrl + '/rest/department/move', {'pks': $scope.getCheckedDeps(), 'dest': dep.pk})
                        .then(function () {
                            return $http.put(CONFIG.siteUrl + '/rest/user/move', {'pks': $scope.getCheckedUsers(), 'dest': dep.pk});
                        })
                        .then(function () {
                            $scope.getDep($scope.depPk);
                            $scope.checkedDeps = {};
                            $scope.checkedUsers = {};
                        });
            }
        });
    };

    $scope.deleteUser = function (user) {
        $scope.checkedUsers = {};
        $scope.checkedDeps = {};
        $scope.checkedUsers[user.pk] = true;

        $timeout(function () {
            $scope.delete();
        });

    };

    $scope.deleteDep = function (dep) {
        $scope.checkedUsers = {};
        $scope.checkedDeps = {};
        $scope.checkedDeps[dep.pk] = true;

        $timeout(function () {
            $scope.delete();
        });
    };


    $scope.copyUser = function (user) {
        var u = $.extend({}, user);
        $.extend(u, {
            'pk': 0,
            'isAdmin': 0,
            'account': '',
            'fullName': '',
            'changePass': true
        });
        $scope.editUser(u);
    };
});


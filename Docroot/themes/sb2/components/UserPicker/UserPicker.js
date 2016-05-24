sb2.directive('ngUserPicker', function ($apply) {
    function link(scope, elem) {
        scope.currentDep = 0;
        scope.ajax = {};
        scope.checked = {};
        scope.depInstance;
        scope.notUser = [];
        scope.notGroup = [];
        scope.search;
        scope.searchResult = {};
        scope.tab = 0;
        scope.groups = [];
        scope.selectedGroup;

        scope.setTab = function (idx) {
            scope.tab = idx;
            if (idx == 1) {
                if (scope.ajax.group)
                    scope.ajax.group.abort();
                scope.ajax.group = $.ajax({
                    'url': CONFIG.siteUrl + '/rest/group',
                    'dataType': 'json'
                }).done(function (resp) {
                    $apply(function () {
                        for (var i in resp) {
                            var group = resp[i];
                            for (var j in scope.notGroup) {
                                if (group.pk == scope.notGroup[j]) {
                                    resp.splice(i, 1);
                                    break;
                                }
                            }
                        }
                        scope.groups = resp;
                    });
                });
            }
        };

        elem[0].openModal = function (config) {
            config = config || {};
            scope.notUser = config.notUser || [];
            scope.notGroup = config.notGroup || [];
            scope.currentDep = config.department || 0;
            scope.onSubmit = config.submit;
            scope.onCancel = config.cancel;
            scope.activeOnly = config.activeOnly || true;
            scope.tab = 0;

            scope.depInstance = null;
            scope.setDep(scope.currentDep);

            $('.modal', elem).modal('show');
        };

        scope.deps = function () {
            var depInstance = scope.depInstance || {};
            var searchResult = scope.searchResult || {};
            return scope.search ? searchResult.deps : depInstance.deps;
        };

        scope.users = function () {
            var depInstance = scope.depInstance || {};
            var searchResult = scope.searchResult || {};
            return scope.search ? searchResult.users : depInstance.users;
        };

        scope.$watchCollection('depInstance', function () {
            if (!scope.depInstance || !scope.depInstance.users)
                return;
            //xóa user trong mảng not
            for (var i in scope.depInstance.users) {
                var user = scope.depInstance.users[i];
                for (var j in scope.notUser) {
                    var pk = scope.notUser[j];
                    if (user.pk == pk) {
                        scope.depInstance.users.splice(i, 1);
                        break;
                    }
                }
            }
        });

        scope.$watch('search', function (search) {
            if (!search)
                return;

            if (scope.ajax.search)
                scope.ajax.search.abort();

            scope.searchResult = null;
            scope.ajax.search = $.ajax({
                'url': CONFIG.siteUrl + '/rest/user/search',
                'data': {'search': scope.search, 'status': 1},
                'dataType': 'json'
            }).done(function (resp) {
                $apply(function () {
                    scope.checked = {};
                    scope.searchResult = {
                        'users': resp.users,
                        'deps': resp.departments
                    };
                });
            });
        });

        scope.setDep = function (pk) {
            scope.currentDep = pk;
            scope.search = null;

            if (scope.ajax.loadDep)
                scope.ajax.loadDep.abort();

            scope.checked = {};

            scope.ajax.loadDep = $.ajax({
                'url': CONFIG.siteUrl + '/rest/department/' + scope.currentDep,
                'data': {'users': 1, 'departments': 1, 'ancestors': 1},
                'dataType': 'json'
            }).done(function (resp) {
                scope.ajax.loadDep = null;
                $apply(function () {
                    scope.depInstance = resp;
                });
            });
        };

        scope.isChecked = function () {
            for (var i in scope.checked)
                if (scope.checked[i])
                    return true;
            return false;
        };

        scope.submit = function () {
            if (!scope.isChecked())
                return;

            var users = [];
            if (scope.tab == 0)
                for (var i in scope.users()) {
                    var user = $.extend({}, scope.users()[i]);
                    if (scope.checked[user.pk])
                        users.push(user);
                }
            else if (scope.selectedGroup && scope.selectedGroup.users)
                for (var i in scope.selectedGroup.users) {
                    var user = $.extend({}, scope.selectedGroup.users[i]);
                    if (scope.checked[user.pk])
                        users.push(user);
                }

            scope.onSubmit(users);
            $('.modal', elem).modal('hide');
        };

        scope.setSelectedGroup = function (group) {
            scope.selectedGroup = group;
            scope.checked = {};

            if (group)
                $.getJSON(CONFIG.siteUrl + '/rest/group/' + group.pk + '/user', function (resp) {
                    $apply(function () {
                        //xóa not
                        for (var i in resp) {
                            var user = resp[i];
                            for (var j in scope.notUser) {
                                if (user.pk == scope.notUser[j]) {
                                    resp.splice(i, 1);
                                    break;
                                }
                            }
                        }
                        scope.selectedGroup.users = resp;
                    });
                });
        };
    }

    return {
        'scope': {},
        'link': link,
        'templateUrl': CONFIG.siteUrl + '/themes/sb2/components/UserPicker/UserPicker.php'
    };
});
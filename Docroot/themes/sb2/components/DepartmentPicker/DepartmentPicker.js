sb2.directive('ngDepartmentPicker', function ($apply, $timeout) {
    function link(scope, elem) {
        elem = elem[0];
        scope.onSubmit;
        scope.onCancel;
        scope.tree;
        scope.selected;
        scope.root = {
            pk: 0,
            depName: '[Thư mục gốc]'
        };

        elem.openModal = function (options) {
            scope.onSubmit = options.submit || new Function();
            scope.onCancel = options.cancel || new Function();
            scope.not = typeof options.not === 'object' ? options.not : [options.not];

            var params = {
                'not': scope.not,
                'departments': 1,
                'rescusively': 1
            };

            scope.selected = null;
            $('.modal-department-picker:first', elem).modal('show');

            var url = CONFIG.siteUrl + '/rest/department/0?' + $.param(params);
            $.getJSON(url, function (res) {
                function findSelected(dep) {
                    var ret = false;
                    for (var i in dep.deps)
                        if (findSelected(dep.deps[i])) {
                            dep.expand = true;
                            ret = true;
                        }

                    if (dep.pk == options.selected) {
                        scope.selected = dep;
                        ret = true;
                    }
                    return ret;
                }

                $apply(function () {
                    findSelected(res);
                    scope.tree = res;
                });
            });
        };

        $('.modal-department-picker:first', elem).unbind('hide.bs.modal').on('hide.bs.modal', scope.onCancel);

        scope.toggleExpand = function (dep) {
            dep.expand = !dep.expand;
        };

        scope.setSelected = function (dep, $event) {
            $event.stopPropagation();
            var target = $($event.target);
            if (target.hasClass('fa-caret-down') || target.hasClass('fa-caret-right'))
                return;

            scope.selected = dep;
        };

        scope.submit = function () {
            $('.modal-department-picker:first', elem).modal('hide');
            $timeout(function () {
                scope.onSubmit(scope.selected);
                scope.onSubmit = new Function();
                scope.onCancel = new Function();
                delete scope.selected;
            });
        };
    }

    return {
        'templateUrl': CONFIG.siteUrl + '/themes/sb2/components/DepartmentPicker/DepartmentPicker.php',
        'scope': {},
        'link': link
    };
});
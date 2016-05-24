var fieldModule = angular.module('fieldModule', ['sb2','bw.paging']);

fieldModule.controller("fieldCtrl", function ($scope, $apply, $timeout) {
    // ham render du lieu
    $scope.data = {};
    $scope.filter = {
        page: 1,
        name: '',
        row_per_page: 20,
        total: 0
    };
    
    $scope.render = function () {
        $.ajax({
            method: "POST",
            dataType: "json",
            url: SITEROOT + "admin/field/getData",
            data: $scope.filter
        }).done(function (data) {
            $apply(function () {
                $scope.data = data.data;
                $scope.filter.total = data.total;
            });
        });
    };
    
    $scope.pagingaction = function(){
        $timeout(function () {
            $scope.render();
        });
    };
    
    $scope.edit = function (item) {
        $scope.modal = $.extend({}, item);
        $('#addBtn').css('display','none');
        $('#editBtn').css('display','');
    };


    $scope.showNewModal = function () {
        $scope.modal = "";
        $('#addBtn').css('display','');
        $('#editBtn').css('display','none');
    };
    // luu member
    $scope.save = function () {
        var json = {'pk': $scope.modal.pk, 'c_name': $scope.modal.c_name, 'c_code': $scope.modal.c_code,
            'c_order': $scope.modal.c_order, 'c_status': $scope.modal.c_status};
        if (checkRequire())
        {
            $.ajax({
                method: "POST",
                url: SITEROOT + "admin/field/update",
                data: json
            }).done(function () {
                $scope.render();
            });
            $("#myModal").modal("hide");
        }
    };

    $scope.clear = function () {
        $scope.editing = "";
    };

    $scope.searchByType = function () {
        $.ajax({
            type: "GET",
            url: SITEROOT + "admin/field/searchType",
            data: {"type": $scope.searchType}
        }).done(function (data) {
            console.log(data);
            $apply(function () {
                $scope.data = data;
            });
            $scope.searchType = "";
        });
    };
    // them member
    $scope.add = function () {

        var json = {'c_name': $scope.modal.c_name, 'c_code': $scope.modal.c_code,
            'c_order': $scope.modal.c_order, 'c_status': $scope.modal.c_status};
        if (checkRequire())
        {
            $.ajax({
                method: "POST",
                url: SITEROOT + "admin/field/add",
                data: json
            }).done(function () {
                $scope.render();
                $("#myModal").modal("hide");
            });
        }
    };

    $scope.searchByName = function () {
        $scope.render();
    };
    // xoa member
    $scope.delete = function (item) {
        $.ajax({
            type: "POST",
            url: SITEROOT + "admin/field/delete",
            data: {"pk": item.pk}
        }).done(function () {
            $scope.render();
            $scope.editing = "";
            alert("Xóa thành công");
        });
        $("#myModal").modal("hide");
    };

    // xoa nhieu member
    $scope.deletes = function () {
        var arr_id = [];
        $("input:checkbox:checked").each(function () {
            arr_id.push($(this).attr('id'));
        });
        $.ajax({
            type: "POST",
            url: SITEROOT + "admin/field/delete",
            data: {"pk": arr_id}
        }).done(function () {
            $scope.render();
            alert("Xóa thành công");
        });
        $("#myModal").modal("hide");
    };

    /**
     * Comment
     */
    function checkRequire()
    {
        var checkContinue = true;
        var allInput = $('input[required]');
        for (var i = 0; i < allInput.length; i++)
        {
            var input = allInput.eq(i);
            if (!input.val())
            {
                checkContinue = false;
                var nullInput = input.parent().prev().text();
                var mess = nullInput.substring(0,nullInput.length-5);
                alert("Bạn chưa nhập " + mess);
                input.focus();
                break;
            }
        }
        return checkContinue;
    }
    
    //thuc hien cac hanh dong
    $scope.render();
});

/**
 * Comment
 */

/**
 * Comment
 */




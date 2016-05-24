sb2.controller("memberCtrl", function ($scope, $apply)
{
    $scope.dataScope = [
        {id: 0
            , label: 'Cấp sở'
        },
        {id: 1
            , label: 'Cấp quận/huyện'
        },
        {id: 2
            , label: 'Cấp phường/xã'
        }
    ];

    // ham render du lieu
    $scope.render = function () {
        $.ajax({
            method: "GET",
            dataType: "json",
            url: SITEROOT + "admin/member/getData"
        }).done(function (data)
        {
            console.log(data);
            $apply(function ()
            {
                $scope.data = data;
            });
        });
    };
    $scope.render();

    $scope.edit = function (item)
    {
        $scope.modal = {};
        $scope.modal = $.extend({}, item);
        $scope.dataScopeSelected = $scope.dataScope[0];
        $($scope.dataScope).each(function (id, val) {

            if (val.id == $scope.modal.c_scope)
            {
                return $scope.dataScopeSelected = $scope.dataScope[id];
            }
        });
        $('#addBtn').css('display', 'none');
        $('#editBtn').css('display', '');
    };


    $scope.showNewModal = function ()
    {
        $scope.dataScopeSelected = $scope.dataScope[0];
        $scope.modal = "";
        $('#addBtn').css('display', '');
        $('#editBtn').css('display', 'none');
    };

    // luu member
    $scope.save = function ()
    {
        var json = {
            'pk': $scope.modal.pk
            , 'c_name': $scope.modal.c_name
            , 'c_code': $scope.modal.c_code
            , 'c_link_service': $scope.modal.c_link_service
            , 'c_account': $scope.modal.c_account,
            'c_password': $scope.modal.c_password
            , 'c_type': $scope.modal.c_type
            , 'c_order': $scope.modal.c_order
            , 'c_status': $scope.modal.c_status
            , c_url_login: $scope.modal.c_url_login
            , c_scope: $scope.dataScopeSelected.id
        };
        if (checkRequire())
        {
            $.ajax({
                type: "POST",
                url: SITEROOT + "admin/member/update",
                data: json
                , dataType: 'json'
            }).done(function (data)
            {
                if (typeof data.resp && data.resp == 'fail')
                {
                    alert(data.msg);
                    return false;
                }

                $scope.render();
                $("#myModal").modal("hide");
            });
        }
    };

    $scope.clear = function () {
        $scope.editing = "";
    };

    $scope.searchByType = function () {
        $.ajax({
            type: "GET",
            url: SITEROOT + "admin/member/searchType",
            data: {"type": $scope.searchType}
        }).done(function (data)
        {
            $apply(function () {
                $scope.data = data;
            });
            $scope.searchType = "";
        });
    };
    // them member
    $scope.add = function ()
    {
        var json = {
            'c_name': $scope.modal.c_name
            , 'c_code': $scope.modal.c_code
            , 'c_link_service': $scope.modal.c_link_service
            , 'c_account': $scope.modal.c_account
            , 'c_password': $scope.modal.c_password
            , 'c_type': $scope.modal.c_type
            , 'c_order': $scope.modal.c_order
            , 'c_status': $scope.modal.c_status
            , c_url_login: $scope.modal.c_url_login
            , c_scope: $scope.dataScopeSelected.id
        };

        if (checkRequire())
        {
            $.ajax({
                type: "POST",
                url: SITEROOT + "admin/member/add",
                data: json
                , dataType: 'json'
                , success: function (data)
                {
                    if (typeof data.resp && data.resp == 'fail')
                    {
                        alert(data.msg);
                        return false;
                    }
                    $scope.render();
                    $("#myModal").modal("hide");
                }
            });
        }
    };

    $scope.searchByName = function () {
        $.ajax({
            type: "GET",
            url: SITEROOT + "admin/member/searchName",
            data: {"keyword": $scope.searchKeyWord}
        }).done(function (data)
        {
            $apply(function () {
                $scope.data = data;
            });
        });
    };
    // xoa member
    $scope.delete = function (item)
    {
        if (confirm('Bạn chắc chắn xóa các đối tượng đã chọn?'))
        {
            $.ajax({
                type: "POST",
                url: SITEROOT + "admin/member/delete",
                data: {"pk": item.pk}
            }).done(function ()
            {
                $scope.render();
                $scope.editing = "";
            });
            $("#myModal").modal("hide");
        }
    };

    // xoa nhieu member
    $scope.deletes = function ()
    {
        if (confirm('Bạn chắc chắn xóa các đối tượng đã chọn?'))
        {
            var arr_id = [];
            $("input:checkbox:checked").each(function () {
                arr_id.push($(this).attr('id'));
            });
            $.ajax({
                type: "POST",
                url: SITEROOT + "admin/member/delete",
                data: {"pk": arr_id}
            }).done(function () {
                $scope.render();
            });
        }
    };

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
                var mess = nullInput.substring(0, nullInput.length - 5);
                alert("Bạn chưa nhập " + mess);
                input.focus();
                break;
            }
        }
        return checkContinue;
    }
});



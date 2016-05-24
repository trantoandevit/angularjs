var recordModule = angular.module('recordModule', ['sb2','bw.paging','summernote']);
recordModule.controller("recordCtrl", function ($scope, $apply, $timeout) {
    // số bản ghi 1 trang
    var record_per_page = 20;
    $scope.memberList = '';
    $scope.recordList = '';
    $scope.allRecordList = '';
    $scope.fieldList = '';
    $scope.attachment_deleted_list_id_tmp = [];
    // điều kiện lọc
    $scope.filter = {
        keyword: "",
        member: "",
        status: "",
        page: 1,
        limit: record_per_page
    };
    // directive phân trang
    $scope.pagging = {
        page: 1,
        pagesize: record_per_page,
        total: 24
    };

    // truyen tham so cho pagging
    $scope.pagging_init = function ()
    {
//        alert(typeof $scope.allRecordList);
        var total = Object.keys($scope.allRecordList).length;
        $scope.pagging.total = total;
    };
    //Load danh sách đơn vị
    $scope.loadData_init = function ()
    {
        $.ajax({
            method: "GET",
            dataType: "json",
            url: SITEROOT + "admin/field/getAllField"
        }).done(function (data) {
            $apply(function () {
                $scope.fieldList = data;
            });
        });
        $.ajax({
            method: "GET",
            dataType: "json",
            url: SITEROOT + "admin/member/getData"
        }).done(function (data) {
            $apply(function () {
                $scope.memberList = data;
            });
        });

    };
    $scope.loadData_init();


    // hàm chạy khi ấn pagging
    $scope.pagging.pagingaction = function () {
        $timeout(function () {
            $scope.filter.page = $scope.pagging.page;
            $.ajax({
                type: "GET",
                url: SITEROOT + "admin/record/search",
                data: $scope.filter,
                dataType: 'json'
            }).done(function (data) {
                $apply(function () {
                    $scope.recordList = data;
                });
            });
        });

    };

    //Hàm xóa bộ lọc

    $scope.refresh = function () {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: SITEROOT + "admin/record/getData"
        }).done(function (data)
        {
            $apply(function ()
            {
                $scope.allRecordList = data;
                $scope.pagging_init();
            });
        });
        $scope.filter.keyword = "";
        $scope.filter.member = "";
        $scope.filter.status = "";
        $scope.filter.page = 1;
        $scope.pagging.page = 1;
        $scope.filter.limit = record_per_page;
        $.ajax({
            type: "GET",
            dataType: "json",
            data: $scope.filter,
            url: SITEROOT + "admin/record/search"
        }).done(function (data)
        {
            $apply(function ()
            {
                $scope.recordList = data;
            });
        });
    };

    // hàm chạy lần đầu tiên
    $scope.refreshData = function () {
        $.ajax({
            type: "GET",
            dataType: "json",
            data: $scope.filter,
            url: SITEROOT + "admin/record/getData"
        }).done(function (data)
        {
            $apply(function ()
            {
                $scope.allRecordList = data;
                $scope.pagging_init();
            });
        });
        $.ajax({
            type: "GET",
            dataType: "json",
            data: $scope.filter,
            url: SITEROOT + "admin/record/search"
        }).done(function (data)
        {
            console.log(data);
            $apply(function ()
            {
                $scope.recordList = data;
            });
        });
        $scope.attachment_deleted_list_id_tmp = [];
    };
    $scope.refreshData();

    $scope.edit = function (item)
    {
        $scope.attachment_deleted_list_id_tmp = [];
        $scope.modal = $.extend({}, item);
        console.log($scope.modal);
        $scope.modal.memberChecked = {};

        for (var i in item.fk_member)
        {
            var memberId = item.fk_member[i];
            $scope.modal.memberChecked[memberId] = true;
        }
        $('#frmUpload #listFiles').html('');
        $('#frmUpload #file').val('');

    };

    $scope.deletedAttachment = function (attachment)
    {
        $scope.attachment_deleted_list_id_tmp.push(attachment.c_file_name);
        var id = '#listFile' + attachment.pk;
        $(id).remove();
    };

    $scope.showNewModal = function ()
    {
        $scope.modal = {};
        $scope.modal.c_scope = 0;
        $scope.modal.fk_linh_vuc = '';
        $('#file').val("");
        $('#listFiles').html("");
    };


    $scope.clear = function () {
        $scope.editing = "";
    };

    // tìm kiếm
    $scope.search = function () {
        $scope.filter.page = 1;
        $scope.filter.limit = "";
        $.ajax({
            type: "GET",
            url: SITEROOT + "admin/record/search",
            data: $scope.filter,
            dataType: 'json'
        }).done(function (data) {
            $apply(function () {
                console.log(data);
                $scope.allRecordList = data;
                $scope.pagging_init();
            });
        });
        $scope.filter.page = 1;
        $scope.filter.limit = record_per_page;
        $.ajax({
            type: "GET",
            url: SITEROOT + "admin/record/search",
            data: $scope.filter,
            dataType: 'json'
        }).done(function (data) {
            $apply(function () {
                $scope.recordList = data;
            });
        });
    };
    // them member
    $scope.Update = function ()
    {

        var recordTypeId = $('#frmUpload #pk').val() || 0;
        var url = SITEROOT + "admin/record/update";
        if (recordTypeId == 0)
        {
            url = SITEROOT + "admin/record/add";
        }
        $('#frmUpload #hdn_list_file_delete').val($scope.attachment_deleted_list_id_tmp.toString());

        if (checkRequire())
        {
            var formData = new FormData($('#frmUpload')[0]);
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                cache: false,
                //dataType: 'json',
                contentType: false,
                processData: false
            }).done(function (data)
            {
                console.log(data);
                if (typeof data.resp != 'undefined')
                {
                    alert(data.msg);
                    return false;
                }
                $scope.refreshData();
                $("#myModal").modal("hide");
            });
        }
    };

    // xoa member
    $scope.delete = function (item)
    {
        if (!confirm('Bạn chắc chắn xóa các đối tượng đã chọn?'))
        {
            return false;
        }

        $.ajax({
            type: "POST",
            url: SITEROOT + "admin/record/delete",
            data: {"pk": item.pk}
        }).done(function () {
            $scope.refreshData();
            $scope.editing = "";
        });
        $("#myModal").modal("hide");
    };

    // xoa nhieu member
    $scope.deletes = function () {
        if (!confirm('Bạn chắc chắn xóa các đối tượng đã chọn?'))
        {
            return false;
        }
        var arr_id = [];
        $("input:checkbox:checked").each(function () {
            arr_id.push($(this).attr('id'));
        });
        var list_id = arr_id.toString();
        $.ajax({
            type: "POST",
            url: SITEROOT + "admin/record/delete",
            data: {"pk": list_id}
        }).done(function () {
            $scope.refreshData();
        });
        $("#myModal").modal("hide");
    };

    /**
     * Comment
     */
    function checkRequire()
    {
        if ($('select[name="sel_fk_linh_vuc"]').val() == '')
        {
            alert('Bạn chưa chọn lĩnh vực');
            return false;
        }
        var checkContinue = true;
        var allInput = $('input[required]');
        for (var i = 0; i < allInput.length; i++)
        {
            var input = allInput.eq(i);
            if (!input.val())
            {
                checkContinue = false;
                var nullInput = input.parent().prev().text();
                var mess = nullInput.substring(0, nullInput.length - 3);
                alert("Bạn chưa nhập " + mess);
                input.focus();
                break;
            }
        }
        return checkContinue;
    }
});

function addAttachment() {
    var element = "<div class='col-xs-10 row'>";
    element += "<input class='form-control' id='code'>";
    element += "</div>";
    element += "<div class='col-xs-2'>";
    element += "<button class='btn btn-default' onclick='deleteAttachment(this)'>Xóa</button>";
    element += "</div>";
    $('#attachment-area').append(element);
}
;


function deleteAttachment(element)
{
    var thisElement = $(element);
    ($(thisElement).parent().prev()).remove();
    ($(thisElement).parent()).remove();

}


$(function ()
{
    $('#file').change(function () {
        $('#listFiles').html('');
        var input = document.getElementById('file');

        for (i = 0; i < input.files.length; i++)
        {
            var li = document.createElement('li');
            li.innerHTML = input.files[i].name;
            $('#listFiles').append(li);
        }
    });
});

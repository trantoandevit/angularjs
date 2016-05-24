<fieldset>
    <legend>Chọn nhóm</legend>

    <table class='table-bordered table table-striped table-hover'>
        <tr ng-repeat="group in groups">
            <td>
                <label class="check">
                    <input type="checkbox" value="{{group.pk}}" ng-checked="editingUser.groups.indexOf(group.pk) != -1" ng-click="toggleGroup($event)"/>
                    <before></before>
                    <after></after>&nbsp;
                    {{group.groupName}}
                </label>
            </td>
        </tr>
    </table>
</fieldset>
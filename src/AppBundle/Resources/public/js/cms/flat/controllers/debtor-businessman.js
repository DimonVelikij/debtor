(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorBusinessmanCtrl', DebtorBusinessmanCtrl);

    DebtorBusinessmanCtrl.$inject= [
        '$scope',
        'FormHelper'
    ];

    function DebtorBusinessmanCtrl (
        $scope,
        FormHelper
    ) {
        $scope.submit = function ($event, form) {
            $event.preventDefault();

            FormHelper.forceDirty(form);

            if (form.$invalid) {
                return;
            }


        };
    }

})(angular);
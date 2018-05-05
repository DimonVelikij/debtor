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

            var submitData = $scope.getBaseSubmitData();
            submitData.ogrnip = $scope.currentDebtor.ogrnip;
            submitData.inn = $scope.currentDebtor.inn;

            $scope.baseSubmit(form, submitData);
        };
    }

})(angular);
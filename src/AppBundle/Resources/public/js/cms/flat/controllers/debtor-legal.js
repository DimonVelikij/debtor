(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorLegalCtrl', DebtorLegalCtrl);

    DebtorLegalCtrl.$inject= [
        '$scope',
        'FormHelper'
    ];

    function DebtorLegalCtrl (
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
            submitData.ogrn = $scope.currentDebtor.ogrn;
            submitData.inn = $scope.currentDebtor.inn;
            submitData.bossName = $scope.currentDebtor.bossName;
            submitData.bossPosition = $scope.currentDebtor.bossPosition;

            $scope.baseSubmit(form, submitData);
        };
    }

})(angular);
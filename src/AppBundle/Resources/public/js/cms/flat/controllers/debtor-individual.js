(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorIndividualCtrl', DebtorIndividualCtrl);

    DebtorIndividualCtrl.$inject= [
        '$scope',
        'FormHelper'
    ];

    function DebtorIndividualCtrl (
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
            submitData.dateOfBirth = $scope.currentDebtor.dateOfBirth ? $scope.currentDebtor.dateOfBirth.replace(/\./g, '') : null;
            submitData.placeOfBirth = $scope.currentDebtor.placeOfBirth;

            $scope.baseSubmit(form, submitData);
        };
    }

})(angular);
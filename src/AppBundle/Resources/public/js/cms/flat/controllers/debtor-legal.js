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


        };
    }

})(angular);
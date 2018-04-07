(function (angular) {
    "use strict";

    angular
        .module('cms.debtor')
        .controller('DebtorCreateCtrl', DebtorCreateController);

    DebtorCreateController.$inject = [
        '$scope',
        '$http',
        'Initializer',
        'FormHelper'
    ];

    function DebtorCreateController (
        $scope,
        $http,
        Initializer,
        FormHelper
    ) {
        $scope.formData = {
            debtorType: null,

            startDebtPeriod: null,
            endDebtPeriod: null,

            dateFillDebt: null,
            sumDebt: null,
            periodAccruedDebt: null,
            periodPayDebt: null,

            dateFillFine: null,
            sumFine: null,
            periodAccruedFine: null,
            periodPayFine: null
        };

        /**
         * подгружаем типы должников
         */
        $http.get(Initializer.Path.AdminDebtorTypes)
            .then(function (response) {
                $scope.debtorTypes = _.merge(response.data);
            });

        $scope.submit = function ($event, form) {
            $event.preventDefault();
console.log($scope.formData);
            FormHelper.forceDirty(form);

            if (form.$invalid) {
                return;
            }

            /*form['type'].errorMessages = {
                backend: 'test'
            };
            form['type'].$setValidity('backend', false);
            вынести в FormHelper.showBackendErrors
            */
        };
    }

})(angular);